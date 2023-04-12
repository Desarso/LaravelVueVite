<?php

namespace App\Repositories;
use Illuminate\Support\Facades\DB;
use App\Models\Holiday;

class HolidayRepository
{
    public function getAll()
    {
        return Holiday::get();
    }

    public function getList()
    {
        return DB::table('wh_holiday')->get(['id as value', 'name as text']);
    }

    public function create($request)
    {
        return Holiday::create($request->all());
    }

    public function update($request)
    {       
        $model = Holiday::find($request->id);
        $model->fill($request->all())->save();
        return $model;
    }

    public function delete($request)
    {
        $model = Holiday::findOrFail($request->id);
        $model->delete();
    }
}