<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;
use Session;

class Planner extends Model
{
    use SoftDeletes;
    protected $table = "wh_planner";
    protected $fillable = ["iditem", "idspot", "idasset", "users", "copies", "tags", "description", "start", "end", "all_day", "by_day", "by_month_day", "frequency", "interval", "until", "isfinished", "business_days", "enabled", "idworkplan", "idworkplan_evaluate"];

    // Relationships
    public function workplan()
    {
        return $this->hasOne(WorkPlan::class,'id','idworkplan');
    }

    public function item()
    {
        return $this->hasOne(Item::class,'id','iditem');
    }

    public function spot()
    {
        return $this->hasOne(Spot::class,'id','idspot');
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'idplanner','id');
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
            return Carbon::parse($value)->setTimezone('America/Costa_Rica');
        } 
    }

    public function getEndAttribute($value) 
    {     
        if(!is_null($value))
        {
            return Carbon::parse($value)->setTimezone('America/Costa_Rica');
        } 
    }

    public function getUntilAttribute($value) 
    {     
        if(!is_null($value))
        {
            return Carbon::parse($value)->setTimezone('America/Costa_Rica');
        } 
    }

    //Muttators
    public function setStartAttribute($value)
    {
        if(!is_null($value))
        {
            $this->attributes['start'] = Carbon::parse($value, 'America/Costa_Rica')->setTimezone(config('app.timezone'));
        }
    }

    public function setEndAttribute($value)
    {
        if(!is_null($value))
        {
            $this->attributes['end'] = Carbon::parse($value, 'America/Costa_Rica')->setTimezone(config('app.timezone'));
        }
    }

    public function setUntilAttribute($value)
    {
        if(!is_null($value))
        {
            $this->attributes['until'] = Carbon::parse($value, 'America/Costa_Rica')->setTimezone(config('app.timezone'));
        }
        else
        {
            $this->attributes['until'] = null;
        }
    }

    public function setFrequencyAttribute($value)
    {
        switch ($value)
        {
            case 'DAILY':
                $this->attributes['frequency'] = "1";
                break;
            
            case 'WEEKLY':
                $this->attributes['frequency'] = "2";
                break;

            case 'MONTHLY':
                $this->attributes['frequency'] = "3";
                break;

            case 'NEVER':
                $this->attributes['frequency'] = "4";
                break;
        }
    }
}
