<?php

namespace App\Models\Production;
use Illuminate\Database\Eloquent\Model;

class ProductCategory extends Model
{  
    protected $table = "wh_product_category";
    protected $fillable =  ["name", "description"];   
}
