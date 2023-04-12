<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
 

class AssetCategory extends Model
{
 
    protected $table = "wh_asset_category";
    protected $fillable =  ["name", "description"];


}
