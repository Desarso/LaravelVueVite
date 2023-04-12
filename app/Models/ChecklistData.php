<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChecklistData extends Model
{   
    protected $table = "wh_checklist_data";
    protected $fillable =  ["name", "data"];
 

}
 