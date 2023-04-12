<?php

namespace App\Repositories\Production;
use Illuminate\Support\Facades\DB;
use App\Models\Production\ProductionFormula;


class ProductionFormulaRepository
{
    public function getAll()
    {
        $formulas = ProductionFormula::get( ["id","name", "description", "inputs"]);
        $formulas->map(function ($formula){
            $formula->inputs = $formula->inputs == null ? null :  array_map(array($this, 'formatInput'), json_decode($formula->inputs));            
            return $formula;
        });        

        return $formulas;
    }

    public function getList()
    {
        return ProductionFormula::get(['id as value', 'name as text']);
    }
    
    public function create($request)
    {
        $inputs = $this->pluckInputs($request->inputs);
        $request->merge(['inputs' => json_encode($inputs)]);        
        $model = ProductionFormula::create($request->all());
        $model->inputs = array_map(array($this, 'formatInput'), $inputs);
        return $model;
    }

    public function update($request)
    {       
      $model = ProductionFormula::find($request->id);
      $inputs = $this->pluckInputs($request->inputs);
      $request->merge(['inputs' => json_encode($inputs)]);        

      $model->fill($request->all())->save();
      $model->inputs = array_map(array($this, 'formatInput'), $inputs);
      return $model;

    }

    public function delete($request)
    {
        $model = ProductionFormula::findOrFail($request->id);
        $model->delete();
    }    


    private function pluckInputs($inputs)
    {
        $inputs = collect($inputs)->pluck('value')->toArray();
        $inputs = array_map('intval', $inputs);
        return $inputs;
    }

    private function formatInput($id)
    {
        $input = new \stdClass;
        $input->value = $id;
        return $input;
    }


   


}