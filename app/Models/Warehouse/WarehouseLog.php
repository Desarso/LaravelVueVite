<?php

namespace App\Models\Warehouse;

use Illuminate\Database\Eloquent\Model;

class WarehouseLog extends Model
{
    protected $table = "wh_warehouse_log";
    protected $fillable =  ["action", "data", "idstatus", "idwarehouse", "iduser"];

    public function warehouse()
    {
        return $this->hasOne(Warehouse::class, 'id', 'idwarehouse');
    }
}
