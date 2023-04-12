<?php

namespace App\Models\Production;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Session;

class ProductionDetail extends Model
{
    protected $table = "wh_production_detail";
    
    protected $fillable =  [ "idproduction", "time", "quantity", "idoperator"];


    //Muttators
    public function setTimeAttribute($value)
    {
        if(!is_null($value))
        {
            $this->attributes['time'] = Carbon::parse($value, Session::get('local_timezone'))->setTimezone(config('app.timezone'));
        }
    }

    //Accesors
    public function getTimeAttribute($value) 
    {      
        return Carbon::parse($value)->setTimezone(Session::get('local_timezone'));
    }
    
}
