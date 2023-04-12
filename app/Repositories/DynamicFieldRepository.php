<?php

namespace App\Repositories;
use Illuminate\Support\Facades\DB;
use App\Models\DynamicField;

class DynamicFieldRepository
{
    public function getAll()
    {
        return DynamicField::whereNull('deleted_at')->get(['id', 'name', 'type', 'values']);
    }

    public function getList()
    {
        return DB::table('wh_dynamic_field')->get(['id as value', 'name as text','type']);
    }
  

    public function create($request)
    {
        return DynamicField::create($request->all());
    }

    public function update($request)
    {       
      $model = DynamicField::find($request->id);
      $model->fill($request->all())->save();
      return $model;

    }

    public function delete($request)
    {
        $model = DynamicField::findOrFail($request->id);
        $model->delete();
    }
}