<?php

namespace App\Models\Production;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Presentation extends Model
{
    use SoftDeletes;
    protected $table = "wh_presentation";
    protected $fillable =  ["name", "description", "units", "idequipmenttype", "isendproduct"];


     // Accesors
     public function getIsendproductAttribute($value)
     {
         $value = $value == 1 ? $value = true : $value = false;
         return $value;
     }
 
     //Mutators
     public function setIsendproductAttribute($value)
     {         
         $value == "true" ? $this->attributes['isendproduct'] = 1 : $this->attributes['isendproduct'] = 0;   
     }


}
