<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Metric extends Model
{
    use SoftDeletes;
    protected $table = "wh_metric";
    protected $fillable =  ["name", "description", "symbol", "enabled"];
    
     // Accesors
     public function getEnabledAttribute($value)
     {
         $value = $value == 1 ? $value = true : $value = false;
         return $value;
     }
    
 
     // Mutators
     public function setEnabledAttribute($value)
     {         
         $value == "true" ? $this->attributes['enabled'] = 1 : $this->attributes['enabled'] = 0;   
     }
 
}
