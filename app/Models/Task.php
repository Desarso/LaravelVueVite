<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Session;
 


class Task extends Model
{   
    protected $table = "wh_ticket";
    protected $fillable = [  'idproject', 'parent', 'progress', 'goal',  "name",  "start", "duration", "tasktype", "idspot", "idteam", "iditem", "created_by"];

    protected $appends = ["open"];
 
    public function getOpenAttribute(){
        return true;
    }


    // Accesors
    public function getEnabledAttribute($value)
    {
        $value = $value == 1 ? $value = true : $value = false;
        return $value;
    }

    public function getStartAttribute($value) 
    {     
        if(!is_null($value))
        {
            return Carbon::parse($value);
        }
    }

    public function getEndAttribute($value) 
    {     
        if(!is_null($value))
        {
            return Carbon::parse($value);
        }
    }

    //Mutators
    public function setEnabledAttribute($value)
    {         
        $value == "true" ? $this->attributes['enabled'] = 1 : $this->attributes['enabled'] = 0;   
    }

    public function setStartAttribute($value)
    {
        if(!is_null($value))
        {
            $this->attributes['start'] = Carbon::parse($value, Session::get('local_timezone'))->setTimezone(config('app.timezone'));
        }
    }

    public function setEndAttribute($value)
    {
        if(!is_null($value))
        {
            $this->attributes['end'] = Carbon::parse($value, Session::get('local_timezone'))->setTimezone(config('app.timezone'));
        }
    }

}
