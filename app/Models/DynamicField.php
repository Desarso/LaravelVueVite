<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DynamicField extends Model
{
    use SoftDeletes;
    protected $table = "wh_dynamic_field";
    protected $fillable =  ["name", "description", "type", "values"];
    
   
 
}
