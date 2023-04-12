<?php

namespace App\Repositories\Production;
use Illuminate\Support\Facades\DB;
use App\Models\Production\Presentation;

class PresentationRepository
{
    public function getAll()
    {
        return Presentation::get( ["id","name", "description", "units", "idequipmenttype", "isendproduct"]);
    }

    public function getList()
    {
        return Presentation::get(['id as value', 'name as text']);
    }
    
    public function create($request)
    {
        return Presentation::create($request->all());
    }

    public function update($request)
    {       
      $model = Presentation::find($request->id);
      $model->fill($request->all())->save();
      return $model;

    }

    public function delete($request)
    {
        $model = Presentation::findOrFail($request->id);
        $model->delete();
    }    

}