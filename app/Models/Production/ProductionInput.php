<?php

namespace App\Models\Production;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductionInput extends Model
{  
    use SoftDeletes;
    protected $table = "wh_production_input";
    protected $fillable =  ["name", "description", "idproductcategory", "formula", "measure", "pack_size", "pack_placing_duration", "buffer", "idstop"];   


     

     
}
