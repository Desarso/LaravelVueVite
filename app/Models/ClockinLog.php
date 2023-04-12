<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Session;


class ClockinLog extends Model
{
    protected $table = "wh_clockin_log";
    protected $fillable =  ['action', 'clockin', 'clockout', 'duration', 'iduser', 'idactivity', 'start_location', 'end_location', 'auto_clockout', 'out_of_time'];

    public function user()
    {
        return $this->hasOne(User::class, 'id','iduser');
    }

    public function activity()
    {
        return $this->hasOne(ClockinActivity::class, 'id','idactivity');
    }

    //Accesors
    public function getActionAttribute($value)
    {     
        return $value;
    }

    public function getClockinAttribute($value) 
    {     
        // dd(Carbon::parse($value)->setTimezone('America/Costa_Rica'));
        if(!is_null($value))
        {
            $date = Carbon::parse($value)->setTimezone(Session::get('local_timezone'));
            return $date;
        } 
    }

    public function getClockoutAttribute($value)
    {     
        if(!is_null($value))
        {
            $date = Carbon::parse($value)->setTimezone(Session::get('local_timezone'));
            return $date;
        } 
    }
}
