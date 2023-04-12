<?php

namespace App\Models\Production;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductionSchedule extends Model
{
    use SoftDeletes;
    protected $table = "wh_production_schedule";
    protected $fillable =  ["name", "description", "duration", "dow", "breaks", "enabled"];

    
    // Accesors
    public function getEnabledAttribute($value)
    {
        $value = $value == 1 ? $value = true : $value = false;
        return $value;
    }

    //Mutators
    public function setEnabledAttribute($value)
    {         
        $value == "true" ? $this->attributes['enabled'] = 1 : $this->attributes['enabled'] = 0;   
    }
    

}









