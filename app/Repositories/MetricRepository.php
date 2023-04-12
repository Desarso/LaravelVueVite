<?php

namespace App\Repositories;
use Illuminate\Support\Facades\DB;
use App\Models\Metric;

class MetricRepository
{
    public function getAll()
    {
        return Metric::get(['id', 'name', 'description','symbol', 'enabled']);        
    }

    public function getList()
    {
        return Metric::where('enabled', true)->get(['id as value', 'name as text']);
    }

    public function create($request)
    {
        return Metric::create($request->all());
    }

    public function update($request)
    {
        $model = Metric::find($request->id);
        $model->fill($request->all())->save();
        return $model;
    }

    public function delete($request)
    {
        $model = Metric::findOrFail($request->id);
        $model->delete();
    }
}