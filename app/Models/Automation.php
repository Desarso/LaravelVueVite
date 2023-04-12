<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Automation extends Model
{
    use SoftDeletes;
    protected $table = "wh_automation";
    protected $fillable =  ["name", "description", "conditions", "actions", "enabled", "created_by", "updated_by"];

 
     


}
