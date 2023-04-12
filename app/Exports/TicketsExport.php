<?php

namespace App\Exports;

use App\Models\Ticket;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Session;
use App\Repositories\TicketRepository;
use App\Repositories\UserRepository;

class TicketsExport implements FromCollection, WithHeadings, WithEvents
{
    protected $request;
    protected $ticketRepository;
    protected $userRepository;

    function __construct($request)
    {
        $this->request          = $request;
        $this->ticketRepository = new TicketRepository;
        $this->userRepository   = new UserRepository;
    }
   
    public function collection()
    {
        $hasRangeDate = $this->request->hasRangeDate === "false" ? false : true;

        $teams = $this->userRepository->getTeams(Auth::id());

        $spots = json_decode(Auth::user()->spots);

        $rows = Ticket::whereHas('item.tickettype', function ($query) {
                        $query->where('showingrid', 1);
                    })
                    ->where(function ($query) use ($teams, $spots) {
                        $query->whereIn('idspot', $spots)
                            ->whereIn('idteam', $teams)
                            ->orWhere('created_by', Auth::id());
                    })
                    ->when($hasRangeDate, function ($query){
                        $start = Carbon::parse($this->request->start)->startOfDay();
                        $end   = Carbon::parse($this->request->end)->endOfDay();

                        $query->whereBetween('created_at', [$start, $end]);
                    })
                    ->when($this->request->has('filter'), function ($query){
                        $query->where(function ($q) {
                            return $this->ticketRepository->applyFilters($q, $this->request);
                        });
                    })
                    ->when(!is_null($this->request->search), function ($query) {
                        //Buscador
                        $query->where(function ($q){
                            return $this->ticketRepository->applySearch($q, $this->request);
                        });
                     })
                    ->get(["id", "code", "idstatus", "idpriority", "idspot", "name", "description", "created_by", "created_at", "idteam", "startdate" ,"finishdate", "duration", "duedate"]);

        $collection = collect([]);

        foreach($rows as $row)
        {
            $item = (object) array(
                "code"        => $row->code,
                "idstatus"    => $row->status->name,
                "idpriority"  => $row->priority->name,
                "branch"      => is_null($row->spot->parent) ? '' : $row->spot->parent->name,
                "idspot"      => $row->spot->name,
                "idspottype"  => $row->spot->spottype->name,
                "name"        => $row->name,
                "description" => $row->description,
                "created_by"  => $row->createdby->fullname,
                "idteam"      => $row->team->name,
                "users"       => $this->formatUsers($row),
                "tags"        => $this->formatTags($row),
                "date"        => $this->getFormatDate($row->created_at),
                "duedate"     => $this->getFormatDate($row->duedate),
                "startdate"   => $this->getFormatDate($row->startdate),
                "finishdate"  => $this->getFormatDate($row->finishdate),
                "duration"    => gmdate("H:i:s", $row->duration)
            );

            $collection->push($item);
        }

        return $collection;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $cellRange = 'A1:W1'; // All headers
                $columns = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q'];
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(14);

                foreach ($columns as $column)
                {
                    $event->sheet->getDelegate()->getColumnDimension($column)->setAutoSize(true);
                }
            },
        ];
    }

    public function headings(): array
    {
        return ["CODIGO", "ESTADO", "PRIORIDAD", "SEDE", "LUGAR", "TIPO LUGAR", "TAREA", "DESCRIPCION", "CREADO POR", "EQUIPO", "RESPONSABLES", "ETIQUETAS", "FECHA", "FECHA VENCIMIENTO", "FECHA INICIO", "FECHA FIN", "DURACION"];
    }

    private function formatUsers($row)
    {
        if($row->users->count() > 0)
        {
            $plucked = $row->users->pluck('fullname');
            return join(", ", $plucked->all());
        }

        return "";
    }

    private function formatTags($row)
    {
        if($row->tags->count() > 0)
        {
            $plucked = $row->tags->pluck('name');
            return join(", ", $plucked->all());
        }

        return "";
    }

    private function getFormatDate($date)
    {
        return !is_null($date) ? Carbon::parse($date)->setTimezone(Session::get('local_timezone'))->toDateTimeString() : "------------";
    }
}
