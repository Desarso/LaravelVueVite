<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChecklistGroupWeight extends Model
{
    protected $table = "wh_checklist_group_weight";

    protected $fillable =  ["idchecklist", "group", "idparent", "weight"];

    public function subgroups()
    {
        return $this->hasMany(ChecklistGroupWeight::class, 'idparent', 'id');
    }
 
}
