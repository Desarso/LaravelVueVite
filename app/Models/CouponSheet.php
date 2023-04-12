<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CouponSheet extends Model
{
    protected $table = "wh_coupon_sheet";

    protected $fillable =  ['idheader', 'barcode', 'created_by'];

    public static function boot()
    {
        parent::boot();

        self::deleting(function ($couponSheetDetail) { // before delete() method call this
            $couponSheetDetail->coupons()->each(function ($child) {
                $child->delete(); // <-- direct deletion
            });
        });
    }

    public function coupons()
    {
        return $this->hasMany(CouponSheetDetail::class, 'idsheet', 'id');
    }

    public function escannedCoupons()
    {
        return $this->hasMany(CouponSheetDetail::class, 'idsheet', 'id')->whereNotNull('scandate');
    }
}
