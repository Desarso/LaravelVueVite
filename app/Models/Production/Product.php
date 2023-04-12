<?php

namespace App\Models\Production;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;
    protected $table = "wh_product";
    protected $fillable =  ["name", "description", "code", "idequipmenttype", "idproductcategory", "idformula", "idpresentation", "iddestination","enabled"];


    public function presentation()
    {
        return $this->hasOne(Presentation::class, 'id', 'idpresentation');
    }

    /*public function presentations()
    {
        return $this->belongsToMany(Presentation::class, 'wh_product_presentation', 'idproduct', 'idpresentation');
    }
    */
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
