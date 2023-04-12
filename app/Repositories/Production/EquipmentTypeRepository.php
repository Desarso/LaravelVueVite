<?php

namespace App\Repositories\Production;
use Illuminate\Support\Facades\DB;
use App\Models\Production\EquipmentType;

class EquipmentTypeRepository
{
    public function getAll()
    {
        return EquipmentType::get( ["name", "description", "destinations"]);
    }

    public function getList()
    {
        return EquipmentType::get(['id as value', 'name as text']);
    }         

}