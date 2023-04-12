<?php

namespace App\Models\Warehouse;

use Illuminate\Database\Eloquent\Model;

class WarehouseSupplier extends Model
{
    protected $table = 'wh_warehouse_supplier';
    protected $fillable = ['id', 'name', 'description'];
}
