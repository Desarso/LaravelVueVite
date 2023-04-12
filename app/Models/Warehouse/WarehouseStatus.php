<?php

namespace App\Models\Warehouse;

use Illuminate\Database\Eloquent\Model;

class WarehouseStatus extends Model
{
    protected $table = 'wh_warehouse_status';
    protected $fillable = ['id','name','description','icon','color','created_at'];
}
