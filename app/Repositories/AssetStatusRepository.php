<?php

namespace App\Repositories;
use Illuminate\Support\Facades\DB;
use App\Models\AssetStatus;

class AssetStatusRepository
{
    public function getAll()
    {
        return AssetStatus::get(["id","name", "description", "color","icon"]);
    }

    public function getList()
    {
        return AssetStatus::get(['id as value', 'name as text', 'color', 'icon']);
    }

    public function create($request)
    {
        return AssetStatus::create($request->all());
    }

    public function update($request)
    {       
      $model = AssetStatus::find($request->id);
      $model->fill($request->all())->save();
      return $model;

    }

    public function delete($request)
    {
        $model = AssetStatus::findOrFail($request->id);
        $model->delete();
    }    

}