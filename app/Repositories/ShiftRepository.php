<?php

namespace App\Repositories;
use Illuminate\Support\Facades\DB;
use App\Models\Shift;

class ShiftRepository
{
    public function getAll()
    {
        return Shift::get();  
    }

    public function getList()
    {
        return DB::table('wh_shift')->get(['id as value', 'name as text']);
    }

    public function create($request)
    {
        return Shift::create($request->all());
    }

    public function update($request)
    {
        $shift = Shift::find($request->id);
        $shift->fill($request->all())->save();

        return $shift;
    }

    public function delete($request)
    {
        $shift = Shift::findOrFail($request->id);
        $shift->delete();
    }
}