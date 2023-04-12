<?php

namespace App\Repositories\Production;
use Illuminate\Support\Facades\DB;
use App\Models\Production\ProductionStatus;

class ProductionStatusRepository
{
    public function getAll()
    {
        return ProductionStatus::get( ["name", "description", "icons", "color"]);
    }

    public function getList()
    {
        return ProductionStatus::get(['id as value', 'name as text', 'icon', 'color']);
    }         

}