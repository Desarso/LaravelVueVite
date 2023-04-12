<?php

namespace App\Models\Production;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductionBreak extends Model
{  
    use SoftDeletes;
    protected $table = "wh_production_break";
    protected $fillable =  ["name", "description", "duration", "dow", "enabled"];   


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
