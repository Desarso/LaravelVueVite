<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use SoftDeletes;
    protected $table = "wh_project";
    protected $fillable =  ["name", "description", "start","end","progress","created_by","updated_by", "archived", "users", "code", "idstatus", "idteam","isprivate", ""];
    
     // Accesors
     public function getIsprivateAttribute($value)
     {
         $value = $value == 1 ? $value = true : $value = false;
         return $value;
     }

     public function getArchivedAttribute($value)
     {
         $value = $value == 1 ? $value = true : $value = false;
         return $value;
     }
    
 
     // Mutators
     public function setIsprivateAttribute($value)
     {          
         $value == "true" ? $this->attributes['isprivate'] = 1 : $this->attributes['isprivate'] = 0;         
     }
     public function setArchivedAttribute($value)
     {          
         $value == "true" ? $this->attributes['archived'] = 1 : $this->attributes['archived'] = 0;         
     }
 
}
