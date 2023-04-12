<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Holiday extends Model
{
    protected $table = "wh_holiday";

    protected $fillable =  ["name", "date"];

    public function setDateAttribute($value)
    {
        $this->attributes['date'] = Carbon::parse($value);
    }
}