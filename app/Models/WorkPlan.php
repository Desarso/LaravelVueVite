<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class WorkPlan extends Model
{
    protected $table = "wh_work_plan";

    protected $fillable =  ["name", "type", "idspot"];

    //Relationships
    public function planners()
    {
        return $this->hasMany(Planner::class, 'idworkplan', 'id');
    }
}
