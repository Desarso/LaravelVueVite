<?php

namespace App\Models;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    protected $table = "wh_schedule";

    protected $fillable =  ["name", "idtype"];

    //Relationships
	public function type()
    {
        return $this->hasOne(ScheduleType::class, 'id', 'idtype');
    }
}
