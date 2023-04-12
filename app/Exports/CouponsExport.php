<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class CouponsExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    public function collection()
    {
        $active = DB::table('wh_coupon_sheet_header')->where('status', 'OPEN')->first();

        $data = DB::table('wh_coupon_sheet as cs')
                  ->join('wh_coupon_sheet_detail as csd', 'csd.idsheet', '=', 'cs.id')
                  ->where('cs.idheader', $active->id)
                  ->whereNotNull('csd.scandate')
                  ->select('idsheet', 'scandate', DB::raw('DATE(csd.scandate) as scandate'))
                  ->orderBy('scandate', 'asc')
                  ->get();

        $groups = $data->groupBy('scandate');

        $collection = collect([]);

        foreach($groups as $group)
        {
            $item = [ "date" => $group[0]->scandate, "number" => $group->count() ];

            $collection->push($item);
        }

        return $collection;
    }

    public function headings(): array
    {
        return ["FECHA", "CUPONES"];
    }
}
