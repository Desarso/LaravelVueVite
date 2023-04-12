<?php

namespace App\Models\Production;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductionStop extends Model
{
    use SoftDeletes;
    protected $table = "wh_production_stop";
    protected $fillable =  ["name", "description", "idtype", "idteam","expectedduration", "enabled"];

     // Accesors
     public function getEnabledAttribute($value)
     {
         $value = $value == 1 ? $value = true : $value = false;
         return $value;
     }

     public function getNotifyAttribute($value)
     {
         $value = $value == 1 ? $value = true : $value = false;
         return $value;
     }
 
     //Mutators
     public function setEnabledAttribute($value)
     {         
         $value == "true" ? $this->attributes['enabled'] = 1 : $this->attributes['enabled'] = 0;   
     }

     public function setNotifyAttribute($value)
     {         
         $value == "true" ? $this->attributes['notify'] = 1 : $this->attributes['notify'] = 0;   
     }
   

}
