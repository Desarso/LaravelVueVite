<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SpotType extends Model
{
    use SoftDeletes;
    protected $table = "wh_spot_type";
    protected $fillable =  ["name", "description", "code", "islodging", "idexternal"];

    // Accesors
    public function getIslodgingAttribute($value)
    {
        $value = $value == 1 ? $value = true : $value = false;
        return $value;
    }
   

    // Mutators
    public function setIslodgingAttribute($value)
    {         
        $value == "true" ? $this->attributes['islodging'] = 1 : $this->attributes['islodging'] = 0;   
    }
   
    //Relationships
    public function spots()
    {
        return $this->hasMany(Spot::class, 'idtype', 'id');
    }
}



