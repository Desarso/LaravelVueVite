<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChecklistOption extends Model
{
    protected $table = "wh_checklist_option";

    protected $fillable =  ['name', 'idchecklist', 'optiontype', 'idmetric', 'instructions', 'starttime', 'position', 'group', 'isgroup', 'iddata', 'iditem', 'idspot', 'departments', 'properties', 'enabled', 'showinreport', 'idparent'];

    public static function boot()
    {
        parent::boot();

        self::deleting(function ($checklistOption) { // before delete() method call this
            $checklistOption->children()->each(function ($child) {
                $child->delete(); // <-- direct deletion
            });
        });
    }

    // Accesors
    public function getEnabledAttribute($value)
    {
        $value = $value == 1 ? $value = true : $value = false;
        return $value;
    }

    public function getIsgroupAttribute($value)
    {
        $value = $value == 1 ? $value = true : $value = false;
        return $value;
    }

    //Mutators
    public function setEnabledAttribute($value)
    {
        $value == "true" ? $this->attributes['enabled'] = 1 : $this->attributes['enabled'] = 0;
    }

    public function setIsgroupAttribute($value)
    {
        $value == "true" ? $this->attributes['isgroup'] = 1 : $this->attributes['isgroup'] = 0;
    }

    //Relationships
    public function checklist()
    {
        return $this->hasOne(Checklist::class, 'id', 'idchecklist');
    }

    public function children()
    {
        return $this->hasMany(ChecklistOption::class, 'idparent', 'id')->orderBy('position', 'ASC');
    }
}
