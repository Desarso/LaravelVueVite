<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Protocol extends Model
{
    use SoftDeletes;
    protected $table = "wh_protocol";
    protected $fillable = ["name", "version", "code", "idtype", "smallimage","image", "html", "isemergency", "activated", "reference", "qrcode", "lan", "enabled"];


    // Accesors
    public function getEnabledAttribute($value)
    {
        $value = $value == 1 ? $value = true : $value = false;
        return $value;
    }

    public function getIsemergencyAttribute($value)
    {
        $value = $value == 1 ? $value = true : $value = false;
        return $value;
    }

    public function getActivatedAttribute($value)
    {
        $value = $value == 1 ? $value = true : $value = false;
        return $value;
    }

    //Mutators
    public function setEnabledAttribute($value)
    {
        $value == "true" ? $this->attributes['enabled'] = 1 : $this->attributes['enabled'] = 0;
    }

    public function setIsemergencyAttribute($value)
    {
        $value == "true" ? $this->attributes['isemergency'] = 1 : $this->attributes['isemergency'] = 0;
    }

    public function setActivatedAttribute($value)
    {
        $value == "true" ? $this->attributes['activated'] = 1 : $this->attributes['activated'] = 0;
    }
}
