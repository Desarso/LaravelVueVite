<?php

namespace App\Exports;

use App\Models\UserSchedule;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Carbon\Carbon;
use Session;

class UserScheduleExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected $request;

    function __construct($request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $start = Carbon::parse($this->request->start);
        $end   = Carbon::parse($this->request->end);

        $rows = UserSchedule::with(['user','shift'])
                        ->whereBetween('date', [$start, $end])
                        ->whereHas('user.teams', function ($q) {
                            $q->where('core_team', 1)
                              ->where('idteam', $this->request->idteam);
                         })
                        ->get();


        $collection = collect([]);

        $grouped = $rows->groupBy('iduser');

        foreach($grouped as $group)
        {
            $data["iduser"] = $this->formatUser($group[0]);

            foreach($group as $key => $value)
            {
                $data[$value->date] = $this->formatSchedule($value);
            }

            $collection->push((object) $data);
        }

        return $collection;
    }

    public function headings(): array
    {
        Carbon::setLocale('es');
        
        $headings = array();

        $start = Carbon::parse($this->request->start);
        $end   = Carbon::parse($this->request->end);

        $diff = $start->diffInDays($end);

        array_push($headings, "USUARIO", $start->toDateString());

        for($i = 1; $i <= $diff; $i++)
        {
            array_push($headings, $start->addDays(1)->toDateString());
        }

        return $headings;
    }

    private function formatUser($row)
    {
        if($row->user->count() > 0)
        {
            return $row->user->fullname;
        }

        return "";
    }

    private function formatSchedule($row)
    {
        if($row->shift->count() > 0)
        {
            return $row->shift->name;
        }

        return "";
    }
}


