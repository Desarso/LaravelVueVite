<?php

namespace App\Repositories\Production;
use Illuminate\Support\Facades\DB;
use App\Models\Production\ProductionInput;


class ProductionInputRepository
{
    public function getAll()
    {
        return ProductionInput::get( ["id", "name", "description", "idproductcategory", "formula", "measure", "pack_size", "pack_placing_duration",  "buffer","idstop"]);
    }

    public function getList()
    {
        return ProductionInput::get(['id as value', 'name as text']);
    }
    
    public function create($request)
    {
        return ProductionInput::create($request->all());
    }

    public function update($request)
    {       
      $model = ProductionInput::find($request->id);
      $model->fill($request->all())->save();
      return $model;

    }

    public function delete($request)
    {
        $model = ProductionInput::findOrFail($request->id);
        $model->delete();
    }    



   


}