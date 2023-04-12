<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CouponSheetDetail extends Model
{
    protected $table = "wh_coupon_sheet_detail";

    protected $fillable =  ['idsheet', 'barcode', 'position', 'description'];

    public function sheet()
    {
        return $this->hasOne(CouponSheet::class, 'id','idsheet');
    }
    
}
