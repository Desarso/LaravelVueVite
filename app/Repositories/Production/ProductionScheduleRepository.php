<?php

namespace App\Repositories\Production;
use Illuminate\Support\Facades\DB;
use App\Models\Production\ProductionSchedule;

class ProductionScheduleRepository
{
    public function getAll()
    {
        $schedules = ProductionSchedule::get(["id", "name", "description", "duration", "dow", "breaks" , "enabled"]);
        $schedules->map(function ($schedule){
            $schedule->dow = $schedule->dow == null ? null :  array_map(array($this, 'formatDow'), json_decode($schedule->dow));
            $schedule->breaks = $schedule->breaks == null ? null :  array_map(array($this, 'formatBreaks'), json_decode($schedule->breaks));
            return $schedule;
        });


        return $schedules;
    }

    public function getList()
    {
        return ProductionSchedule::where('enabled', true)->get(['id as value', 'name as text']);
    }         


    public function create($request)
    {
        $dows = $this->pluckDows($request->dow);
        $breaks = $this->pluckBreaks($request->breaks);

        $request->merge(['dow' => json_encode($dows)]);        
        $request->merge(['breaks' => json_encode($breaks)]);       

        $model = ProductionSchedule::create($request->all());
        $model->dow = array_map(array($this, 'formatDow'), $dows);
        $model->breaks = array_map(array($this, 'formatBreaks'), $breaks);
        return $model;
    }

    public function update($request)
    {       
        $model = ProductionSchedule::find($request->id);
        
        $dows = $this->pluckDows($request->dow);
        $breaks = $this->pluckBreaks($request->breaks);

        $request->merge(['dow' => json_encode($dows)]);
        $request->merge(['breaks' => json_encode($breaks)]);       

        $model->fill($request->all())->save();
        $model->dow = array_map(array($this, 'formatDow'), $dows);
        $model->breaks = array_map(array($this, 'formatBreaks'), $breaks);
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

    
    private function formatBreaks($idbreak)
    {
        $break = new \stdClass;
        $break->value = $idbreak;
        return $break;
    }

    private function pluckBreaks($breaks)
    {
        $breaks = collect($breaks)->pluck('value')->toArray();
        $breaks = array_map('intval', $breaks);
        return $breaks;
    }



    public function delete($request)
    {
        $model = ProductionSchedule::findOrFail($request->id);
        $model->delete();
    }    

}