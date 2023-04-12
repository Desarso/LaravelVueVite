<?php

namespace App\Repositories\Warehouse;

use Illuminate\Support\Facades\DB;
use App\Models\Warehouse\WarehouseSupplier;

class WarehouseSupplierRepository
{
    public function getAll()
    {
        return WarehouseSupplier::get(["id", "name", "description"]);
    }

    public function getList()
    {
        return WarehouseSupplier::get(['id as value', 'name as text']);
    }         
}