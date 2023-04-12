<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Checklist extends Model
{
    use SoftDeletes;
    protected $table = "wh_checklist";
    protected $fillable = ["name", "description", "created_by", "send_by_email", "type", "enabled", "collapse"];

    //Relationships
    function checklistoptions()
    {
        return $this->hasMany(ChecklistOption::class, 'idchecklist', 'id');
    }

    public function items()
    {
        return $this->hasMany(Item::class, 'idchecklist', 'id');
    }
 
    // Accesors
    public function getEnabledAttribute($value)
    {
        return ($value == 1 ? true : false);
    }

    public function getSendByEmailAttribute($value)
    {
        return ($value == 1 ? true : false);
    }

    //Mutators
    public function setEnabledAttribute($value)
    {         
        $value == "true" ? $this->attributes['enabled'] = 1 : $this->attributes['enabled'] = 0;   
    }

    public function setSendByEmailAttribute($value)
    {         
        $value == "true" ? $this->attributes['send_by_email'] = 1 : $this->attributes['send_by_email'] = 0;   
    }

    public function setCollapseAttribute($value)
    {          
        $this->attributes['collapse'] = $value == "true" ?  1 : 0;         
    }
}
