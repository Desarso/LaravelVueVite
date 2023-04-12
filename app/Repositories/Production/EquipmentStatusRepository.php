<?php

namespace App\Repositories\Production;
use Illuminate\Support\Facades\DB;
use App\Models\Production\EquipmentStatus;

class EquipmentStatusRepository
{
    public function getAll()
    {
        return EquipmentStatus::get( ["name", "description", "icons", "color"]);
    }

    public function getList()
    {
        return EquipmentStatus::get(['id as value', 'name as text', 'icon', 'color']);
    }         

}