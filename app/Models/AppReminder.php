<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Session;

class AppReminder extends Model
{
    protected $table = "wh_app_reminder";
    
    protected $fillable =  ["message", "time", "dow" ,"teams", "users_exception", "last_send", "enabled"];

    // Accesors
    public function getEnabledAttribute($value)
    {
        $value = $value == 1 ? $value = true : $value = false;
        return $value;
    }

    public function getTimeAttribute($value) 
    {     
        if(!is_null($value))
        {
            return Carbon::parse($value)->setTimezone('America/Costa_Rica');
        } 
    }

    //Muttators
    public function setTimeAttribute($value)
    {
        if(!is_null($value))
        {
            $this->attributes['time'] = Carbon::parse($value, 'America/Costa_Rica')->setTimezone(config('app.timezone'));
        }
    }

    public function setEnabledAttribute($value)
    {         
        $value == "true" ? $this->attributes['enabled'] = 1 : $this->attributes['enabled'] = 0;   
    }
}
