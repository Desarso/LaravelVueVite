<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;


class App extends Model
{   
    protected $table = "wh_app";
    protected $fillable =  ["name", "description", "url","icon","color","position","enabled"];

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
