<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TicketType extends Model
{
    use SoftDeletes;
    protected $table = "wh_ticket_type";
    protected $fillable =  ["name", "description", "idteam", "icon", "color", "iscleaningtask", "template", "hassla", "showingrid"];

    //Accesors
    public function getIsCleaningTaskAttribute($value)
    {
        $value = $value == 1 ? $value = true : $value = false;
        return $value;
    }

    public function getHasslaAttribute($value)
    {
        $value = $value == 1 ? $value = true : $value = false;
        return $value;
    }

    public function getShowingridAttribute($value)
    {
        $value = $value == 1 ? $value = true : $value = false;
        return $value;
    }
   
    //Mutators
    public function setIsCleaningTaskAttribute($value)
    {         
        $value == "true" ? $this->attributes['iscleaningtask'] = 1 : $this->attributes['iscleaningtask'] = 0;   
    }

    public function setHasslaAttribute($value)
    {         
        $value == "true" ? $this->attributes['hassla'] = 1 : $this->attributes['hassla'] = 0;   
    }

    public function setShowingridAttribute($value)
    {         
        $value == "true" ? $this->attributes['showingrid'] = 1 : $this->attributes['showingrid'] = 0;   
    }

    //Relationships
    public function items()
    {
        return $this->hasMany(Item::class, 'idtype', 'id');
    }
}
