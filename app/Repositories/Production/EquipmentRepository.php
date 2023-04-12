<?php

namespace App\Repositories\Production;
use Illuminate\Support\Facades\DB;
use App\Models\Production\Equipment;

class EquipmentRepository
{
    public function getAll()
    {
        return Equipment::get( ["id","name", "description", "idtype", "idproductcategory", "idstatus","velocity", "warmup_duration","cleaning_duration","enabled"]);
    }

    public function getList()
    {
        return Equipment::where('enabled', true)->get(['id as value', 'name as text']);
    }
    
    public function create($request)
    {
        return Equipment::create($request->all());
    }

    public function update($request)
    {       
      $model = Equipment::find($request->id);
      $model->fill($request->all())->save();
      return $model;

    }

    public function delete($request)
    {
        $model = Equipment::findOrFail($request->id);
        $model->delete();
    }    

}