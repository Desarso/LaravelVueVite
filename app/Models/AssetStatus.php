<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
 

class AssetStatus extends Model
{
 
    protected $table = "wh_asset_status";
    protected $fillable =  ["name", "description", "color", "icon"];


}
