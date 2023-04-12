<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use App\Models\CouponSheetDetail;
use App\Models\CouponSheet;
use Illuminate\Support\Facades\Auth;

class CouponSheetImport implements ToCollection
{
    public $barcode;
    public $idheader;

    public function  __construct($barcode, $idheader)
    {
        $this->barcode  = $barcode;
        $this->idheader = $idheader;
    }

    public function collection(Collection $collection)
    {
        $collection = $collection->where("0", ">", 0)->where("0", "<=", 24);

        $couponSheet = CouponSheet::create(['idheader' => $this->idheader, 'barcode' => $this->barcode, 'created_by' => Auth::id()]);

        $position = 0;

        foreach ($collection as $item)
        {
            $position ++;

            $quantity = $item[3];

            $data = ['idsheet' => $couponSheet->id, 'barcode' => $item[1], 'position' => $position, 'description' => $item[2]];

            if($quantity == 1)
            {
                CouponSheetDetail::create($data);
            }
            else
            {
                CouponSheetDetail::create($data);

                for ($x = 0; $x < ($quantity - 1); $x++)
                {
                    $position ++;

                    $data["position"] = $position;

                    CouponSheetDetail::create($data);
                }
            }
        }
    }
}
