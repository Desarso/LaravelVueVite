<?php

namespace App\Models\Production;
use Illuminate\Database\Eloquent\Model;

class ProductionStatus extends Model
{
    protected $table = "wh_production_status";
    protected $fillable =  ["name", "description", "icon", "color"];
   

}
