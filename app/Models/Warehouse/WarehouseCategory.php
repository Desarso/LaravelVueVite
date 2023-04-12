<?php

namespace App\Models\Warehouse;

use Illuminate\Database\Eloquent\Model;

class WarehouseCategory extends Model
{
    protected $table = "wh_warehouse_category";
    protected $fillable = ['name', 'description'];
}
