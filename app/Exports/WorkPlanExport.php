<?php

namespace App\Exports;


use App\Models\Planner;
use App\Models\WorkPlan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithTitle;
use App\Repositories\WorkPlanRepository;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Doctrine\Common\Collections\Collection;
use \stdClass;

class WorkPlanExport implements FromCollection, WithHeadings, ShouldAutoSize, WithEvents, WithTitle
{
    protected $request;
    protected $plannerRepository;
    protected $cells;

    function __construct($request)
    {
        $this->request            = $request;
        $this->workPlanRepository = new WorkPlanRepository;
        $this->cells              = collect();
    }
    
    public function title(): string
    {
        return WorkPlan::find($this->request->idworkplan)->name;
    }

    public function collection()
    {
        $data = $this->workPlanRepository->getData($this->request);

        $collection = collect();

        $i = 2;

        foreach ($data->groupBy("idplanner") as $group)
        {
            $item = ['title' => $group[0]['title'], '01' => '', '02' => '', '03' => '', '04' => '', '05' => '', '06' => '', '07' => '', '08' => '', '09' => '', '10' => '', '11' => '', '12' => '', '13' => '', '14' => '', '15' => '', '16' => '', '17' => '', '18' => '', '19' => '', '20' => '', '21' => '', '22' => '', '23' => '', '24' => '', '25' => '', '26' => '', '27' => '', '28' => '', '29' => '', '30' => '', '31' => ''];

            foreach ($group as $row)
            {
                $day = $row['start']->format('d');

                $this->cells->push(["cell" => ($this->getCell($day) . $i), "color" => $this->getColor($row['idstatus'])]);
            }

            $collection->push($item);
            $i++;
        }

        return $collection;
    }

    public function headings(): array
    {
        $start = Carbon::parse($this->request->startDate)->startOfMonth();
        $end   = Carbon::parse($this->request->startDate)->endOfMonth();

        $period = CarbonPeriod::create($start, $end);

        $dates = ["TAREA"];

        foreach ($period as $date)
        {
            array_push($dates, $date->format('M d'));
        }

        return $dates;
    }

    public function registerEvents(): array
    {
        $daysInMonth = Carbon::parse($this->request->startDate)->daysInMonth;

        $finishCell = $this->getCell($daysInMonth) . "1";

        return [
            AfterSheet::class => function(AfterSheet $event) use($finishCell) {

                $event->sheet->freezePane('A2'); // freezing here

                $cellRange = 'A1:'. $finishCell; // All headers

                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(13)->setBold(true);
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE);

                foreach ($this->cells as $cell)
                {
                    $styleArray = [
                        'fill' => [
                            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                            'startColor' => [
                                'argb' => $cell['color'],
                            ],
                        ],
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                                'color' => ['argb' => 'd5e1df'],
                            ],
                        ],
                    ];


                    $event->sheet->getStyle($cell['cell'])->applyFromArray($styleArray);
                }

                $styleArray = [
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
                            'color' => ['argb' => 'FFFFFF'],
                        ],
                    ],
                ];
                
                $event->sheet->getStyle($cellRange)->applyFromArray($styleArray);
                $event->sheet->getDefaultRowDimension()->setRowHeight(30);
            },
        ];
    }

    private function getCell($day)
    {
        $cells = collect([
            ['position' => '01', 'value' => 'B'],
            ['position' => '02', 'value' => 'C'],
            ['position' => '03', 'value' => 'D'],
            ['position' => '04', 'value' => 'E'],
            ['position' => '05', 'value' => 'F'],
            ['position' => '06', 'value' => 'G'],
            ['position' => '07', 'value' => 'H'],
            ['position' => '08', 'value' => 'I'],
            ['position' => '09', 'value' => 'J'],
            ['position' => '10', 'value' => 'K'],
            ['position' => '11', 'value' => 'L'],
            ['position' => '12', 'value' => 'M'],
            ['position' => '13', 'value' => 'N'],
            ['position' => '14', 'value' => 'O'],
            ['position' => '15', 'value' => 'P'],
            ['position' => '16', 'value' => 'Q'],
            ['position' => '17', 'value' => 'R'],
            ['position' => '18', 'value' => 'S'],
            ['position' => '19', 'value' => 'T'],
            ['position' => '20', 'value' => 'U'],
            ['position' => '21', 'value' => 'V'],
            ['position' => '22', 'value' => 'W'],
            ['position' => '23', 'value' => 'X'],
            ['position' => '24', 'value' => 'Y'],
            ['position' => '25', 'value' => 'Z'],
            ['position' => '26', 'value' => 'AA'],
            ['position' => '27', 'value' => 'AB'],
            ['position' => '28', 'value' => 'AC'],
            ['position' => '29', 'value' => 'AD'],
            ['position' => '30', 'value' => 'AE'],
            ['position' => '31', 'value' => 'AF'],
        ]);

        return $cells->firstWhere('position', $day)['value']; 
    }

    private function getColor($idstatus)
    {
        $color = "F4516C";

        switch($idstatus)
        {
            case 1:
                $color = "F4516C";
                break;

            case 2:
                $color = "12c684";
                break;

            case 3:
                $color = "f6a213";
                break;
    
            case 4:
                $color = "C4C5D6";
                break;              
        }

        return $color;
    }
}
