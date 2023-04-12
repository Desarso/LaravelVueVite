<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends Model
{
    use SoftDeletes;
    protected $table = "wh_item";
    protected $fillable =  ["name", "description","code", "idteam", "users", "idtype", "isprivate", "sla", "idpriority", "isglitch" , "enabled", "idchecklist", "idprotocol"];
     
    //Relationships
    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'iditem', 'id');
    }

    public function checklistOptions()
    {
        return $this->hasMany(ChecklistOption::class, 'iditem', 'id');
    }

    public function tickettype()
    {
        return $this->hasOne(TicketType::class, 'id','idtype');
    }

    public function warehouses()
    {
        return $this->hasMany(Warehouse::class,'iditem', 'id');
    }

    //Accesors
    public function getIsprivateAttribute($value)
    {
        $value = $value == 1 ? $value = true : $value = false;
        return $value;
    }

    public function getEnabledAttribute($value)
    {
        $value = $value == 1 ? $value = true : $value = false;
        return $value;
    }

    public function getIsglitchAttribute($value)
    {
        $value = $value == 1 ? $value = true : $value = false;
        return $value;
    }

    //Mutators
    public function setIsprivateAttribute($value)
    {         
        $value == "true" ? $this->attributes['isprivate'] = 1 : $this->attributes['isprivate'] = 0;   
    }

    public function setIsglitchAttribute($value)
    {         
        $value == "true" ? $this->attributes['isglitch'] = 1 : $this->attributes['isglitch'] = 0;   
    }
    
    public function setEnabledAttribute($value)
    {         
        $value == "true" ? $this->attributes['enabled'] = 1 : $this->attributes['enabled'] = 0;   
    }
}
 