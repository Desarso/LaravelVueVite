<?php

namespace App\Models\Production;
use Illuminate\Database\Eloquent\Model;

class EquipmentStatus extends Model
{
    protected $table = "wh_equipment_status";
    protected $fillable =  ["name", "description", "icon", "color"];
   

}
