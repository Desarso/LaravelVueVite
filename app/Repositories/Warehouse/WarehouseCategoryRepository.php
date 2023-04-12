<?php

namespace App\Repositories\Warehouse;

use Illuminate\Support\Facades\DB;
use App\Models\Warehouse\WarehouseCategory;

class WarehouseCategoryRepository
{
    public function getAll()
    {
        return WarehouseCategory::get();
    }

    public function getList()
    {
        return WarehouseCategory::get(['id as value', 'name as text']);
    } 
    
    public function create($request)
    {
        return WarehouseCategory::create($request->all());
    }

    public function update($request)
    {       
        $model = WarehouseCategory::find($request->id);
        $model->fill($request->all())->save();
        return $model;
    }

    public function delete($request)
    {
        $model = WarehouseCategory::findOrFail($request->id);
        $model->delete();
    }
}