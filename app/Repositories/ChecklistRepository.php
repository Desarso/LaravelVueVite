<?php

namespace App\Repositories;
use Illuminate\Support\Facades\DB;
use App\Models\Checklist;

class ChecklistRepository
{
    public function getAll()
    {
        return Checklist::get(['id', 'name', 'description', 'created_by', 'send_by_email', 'enabled']);        
    }

    public function getList()
    {        
        return Checklist::where('enabled', true)->get(['id as value', 'name as text', 'type']);
    }

    public function create($request)
    {
        return Checklist::create($request->all());
    }

    public function update($request)
    {
        $model = Checklist::find($request->id);
        $model->fill($request->all())->save();
        return $model;
    }

    public function delete($request)
    {
        $model = Checklist::findOrFail($request->id);
        $model->delete();
    }

  
}