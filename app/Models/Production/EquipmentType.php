<?php

namespace App\Models\Production;
use Illuminate\Database\Eloquent\Model;

class EquipmentType extends Model
{
    protected $table = "wh_equipment_type";
    protected $fillable =  ["name", "description", "destinations"];
   

}
