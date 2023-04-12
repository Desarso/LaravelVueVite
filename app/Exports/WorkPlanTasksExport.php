<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use App\Models\Ticket;
use Carbon\Carbon;
use Session;

class WorkPlanTasksExport implements FromCollection, WithTitle, ShouldAutoSize, WithHeadings, WithStyles
{
    protected $request;
    protected $plannerRepository;

    function __construct($request)
    {
        $this->request = $request;
    }
    
    public function title(): string
    {
        return "Tareas";
    }

    public function collection()
    {
        $start = Carbon::parse($this->request->startDate)->startOfMonth();
        $end   = Carbon::parse($this->request->startDate)->endOfMonth();

        $rows = Ticket::whereNotNull("idplanner")
                      ->whereHas('planner', function ($query) {
                            $query->where('idworkplan', $this->request->idworkplan);
                      })
                      ->whereBetween('created_at', [$start, $end])
                      ->get(["id", "code", "idstatus", "idspot", "name", "description", "created_at", "startdate" ,"finishdate", "duration"]);

        $collection = collect([]);

        foreach($rows as $row)
        {
            $item = (object) array(
                "code"        => $row->code,
                "idstatus"    => $row->status->name,
                "idspot"      => $row->spot->name,
                "name"        => $row->name,
                "description" => $row->description,
                "users"       => $this->formatUsers($row),
                "date"        => $this->getFormatDate($row->created_at),
                "startdate"   => $this->getFormatDate($row->startdate),
                "finishdate"  => $this->getFormatDate($row->finishdate),
                "duration"    => gmdate("H:i:s", $row->duration)
            );

            $collection->push($item);
        }

        return $collection;
    }

    public function headings(): array
    {
        return ["CODIGO", "ESTADO" , "LUGAR", "TAREA", "DESCRIPCION", "RESPONSABLES", "FECHA", "FECHA INICIO", "FECHA FIN", "DURACION"];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getDefaultRowDimension()->setRowHeight(25);
        $sheet->freezePane('A2'); // freezing here

        return [
            // Style the first row as bold text.
            1 => [
                    'font' => [
                        'bold' => true,
                        'size' => 16 ,
                        'color' => [
                            'argb' => \PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE
                        ]
                    ],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    ],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => [
                            'argb' => '034f84',
                        ]
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => [
                                'argb' => 'FFFFFF'
                            ],
                        ],
                    ],
            ],
        ];
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

    private function getFormatDate($date)
    {
        return !is_null($date) ? Carbon::parse($date)->setTimezone(Session::get('local_timezone'))->toDateTimeString() : "";
    }
}
