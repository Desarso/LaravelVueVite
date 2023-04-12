<?php

namespace App\Exports;

use App\Models\ClockinLog;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Session;
use App\Repositories\Reports\ReportClockinMapRepository;

class ClockinMapExport implements FromCollection, WithHeadings, WithEvents
{
    protected $request;
    protected $clockinMapRepository;

    function __construct($request)
    {
        $this->request          = $request;
        $this->clockinMapRepository = new ReportClockinMapRepository;
    }
   
    public function collection()
    {
        $start = Carbon::parse($this->request->start)->startOfDay();
        $end   = Carbon::parse($this->request->end)->endOfDay();

        $request = $this->request;

        $rows = ClockinLog::with('user:id,firstname,lastname')
                            ->whereBetween('clockin', [$start, $end])
                            ->whereNotNull('start_location')
                            ->when(!is_null($request->iduser), function ($query) use ($request) {
                                return $query->where('iduser', $request->iduser);
                            })
                            ->when(!is_null($request->idteam), function ($query) use($request){
                                return $query->whereHas('user.teams', function ($q) use ($request) {
                                    $q->where('idteam', $request->idteam);
                                });
                            })
                            ->orderBy('iduser')
                            ->orderBy('clockin', 'DESC')
                            ->get();

        $collection = collect([]);
        // $rows = $rows->sortByDesc('iduser')->values();

        foreach($rows as $row)
        {
            $item = (object) array(
                "name"        => $row->user->fullname,
                "clockin"     => $this->getFormatDate($row->clockin),
                "clockout"    => $this->getFormatDate($row->clockout),
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
                $columns = ['A', 'B', 'C', 'D'];
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
        return ["Nombre", "Entrada", "Salida", "DURACION"];
    }


    private function getFormatDate($date)
    {
        return !is_null($date) ? Carbon::parse($date)->setTimezone('America/Costa_Rica')->toDateTimeString() : "------------";
    }
}
