<?php

namespace App\Models\Warehouse;

use Illuminate\Database\Eloquent\Model;

class WarehouseNote extends Model
{
    protected $table = "wh_warehouse_note";

    protected $fillable = ["idwarehouse", "note", "created_by"];

    //Relationships
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'idwarehouse', 'id');
    }
}
