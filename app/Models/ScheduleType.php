<?php

namespace App\Models;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class ScheduleType extends Model
{
    protected $table = "wh_schedule_type";

    protected $fillable =  ["name"];
}
