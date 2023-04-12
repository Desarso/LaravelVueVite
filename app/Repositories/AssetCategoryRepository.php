<?php

namespace App\Repositories;
use Illuminate\Support\Facades\DB;
use App\Models\AssetCategory;

class AssetCategoryRepository
{
    public function getAll()
    {
        return AssetCategory::get(["id","name", "description"]);
    }

    public function getList()
    {
        return AssetCategory::get(['id as value', 'name as text']);
    }
    

    public function create($request)
    {
        return AssetCategory::create($request->all());
    }

    public function update($request)
    {       
      $model = AssetCategory::find($request->id);
      $model->fill($request->all())->save();
      return $model;

    }

    public function delete($request)
    {
        $model = AssetCategory::findOrFail($request->id);
        $model->delete();
    }    

}