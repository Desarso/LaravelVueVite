<?php

namespace App\Repositories\Production;
use Illuminate\Support\Facades\DB;
use App\Models\Production\ProductionBreak;


class ProductionBreakRepository
{
    public function getAll()
    {
        $breaks = ProductionBreak::get( ["id","name", "description", "duration", "dow",  "enabled"]);

        $breaks->map(function ($break){
            $break->dow = $break->dow == null ? '' :  array_map(array($this, 'formatDow'), json_decode($break->dow));
            return $break;
        });
     return $breaks;        
    }

    public function getList()
    {
        return ProductionBreak::where('enabled', true)->get(['id as value', 'name as text']);
    }
    
    public function create($request)
    {
        $dows = $this->pluckDows($request->dow);
        $request->merge(['dow' => json_encode($dows)]);        
        $model = ProductionBreak::create($request->all());
        $model->dow = array_map(array($this, 'formatDow'), $dows);
        return $model;
    }

    private function pluckDows($dows)
    {
        $dows = collect($dows)->pluck('value')->toArray();
        $dows = array_map('intval', $dows);
        return $dows;
    }

    private function formatDow($iddow)
    {
        $dow = new \stdClass;
        $dow->value = $iddow;
        return $dow;
    }


    public function update($request)
    {       
        $model = ProductionBreak::find($request->id);
        $dows = $this->pluckDows($request->dow);
        $request->merge(['dow' => json_encode($dows)]);
        $model->fill($request->all())->save();
        $model->dow = array_map(array($this, 'formatDow'), $dows);
        return $model;

    }

    public function delete($request)
    {
        $model = ProductionBreak::findOrFail($request->id);
        $model->delete();
    }    



   


}