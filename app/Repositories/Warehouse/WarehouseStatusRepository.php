<?php

namespace App\Repositories\Warehouse;

use Illuminate\Support\Facades\DB;
use App\Models\Warehouse\WarehouseStatus;

class WarehouseStatusRepository
{
    
    public function getAll()
    {
        return WarehouseStatus::get( ["id","name", "description", "icons", "color"]);
    }

    public function getList()
    {
        return WarehouseStatus::get(['id as value', 'name as text', 'color']);
    }         

}