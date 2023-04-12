<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class CouponSheetHeader extends Model
{
    protected $table = "wh_coupon_sheet_header";

    protected $fillable =  ['initial_code', 'status', 'startdate', 'finishdate', 'created_by', 'closed_by'];

    public function sheets()
    {
        return $this->hasMany(CouponSheet::class, 'idheader', 'id');
    }

    public function getMessageAttribute()
    {
        $user = User::find($this->created_by);

        $date = Carbon::parse($this->created_at)->format("Y-m-d");

        return "Código inicial N° <strong>{$this->initial_code}</strong>, creado por <strong>{$user->fullname}</strong> el <strong>{$date}</strong> <strong id='sheet-count' class='sheet-count'>Total hojas: 0</strong>";
    }
}
