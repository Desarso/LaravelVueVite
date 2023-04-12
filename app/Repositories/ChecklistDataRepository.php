<?php

namespace App\Repositories;
use Illuminate\Support\Facades\DB;
use App\Models\ChecklistData;

class ChecklistDataRepository
{
    public function getAll()
    {
        return ChecklistData::get(['id', 'name', 'data']);        
    }

    public function getList()
    {        
        return ChecklistData::get(['id as value', 'name as text', 'data as items']);
    }

    public function create($request)
    {
        return ChecklistData::create($request->all());
    }

    public function update($request)
    {
        $model = ChecklistData::find($request->id);
        $model->fill($request->all())->save();
        return $model;
    }

    public function delete($request)
    {
        $model = ChecklistData::findOrFail($request->id);
        $model->delete();
    }

  
}