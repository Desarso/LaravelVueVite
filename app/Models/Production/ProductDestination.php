<?php

namespace App\Models\Production;
use Illuminate\Database\Eloquent\Model;

class ProductDestination extends Model
{  
    protected $table = "wh_product_destination";
    protected $fillable =  ["name", "description","enabled"];   
}
