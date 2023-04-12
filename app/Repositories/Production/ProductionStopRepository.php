<?php

namespace App\Repositories\Production;
use Illuminate\Support\Facades\DB;
use App\Models\Production\ProductionStop;


class ProductionStopRepository
{
    public function getAll()
    {
        return ProductionStop::get( ["id","name", "description", "idtype", "idteam", "expectedduration", "enabled"]);
    }

    public function getList()
    {
        return ProductionStop::where('enabled', true)->get(['id as value', 'name as text']);
    }
    
    public function create($request)
    {
        return ProductionStop::create($request->all());
    }

    public function update($request)
    {       
      $model = ProductionStop::find($request->id);
      $model->fill($request->all())->save();
      return $model;

    }

    public function delete($request)
    {
        $model = ProductionStop::findOrFail($request->id);
        $model->delete();
    }    



   


}