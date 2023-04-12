<?php

namespace App\Models\Production;

use App\Models\Production\Production;
use App\Models\Production\EquipmentStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Equipment extends Model
{
    use SoftDeletes;
    protected $table = "wh_equipment";
    protected $fillable =  ["name", "description", "idtype", "idproductcategory", "idstatus", "velocity", "warmup_duration","cleaning_duration","enabled"];


    public function productions()
    {
        return $this->hasMany(Production::class, 'idequipment', 'id');
    }

    public function status()
    {
        return $this->hasOne(EquipmentStatus::class, 'id', 'idstatus');
    }

    // Accesors
    public function getEnabledAttribute($value)
    {
        $value = $value == 1 ? $value = true : $value = false;
        return $value;
    }

    //Mutators
    public function setEnabledAttribute($value)
    {         
        $value == "true" ? $this->attributes['enabled'] = 1 : $this->attributes['enabled'] = 0;   
    }

}
