<?php

namespace App\Repositories;
use Illuminate\Support\Facades\DB;
use App\Models\Filter;
use Illuminate\Support\Facades\Auth;

class FilterRepository
{
    public function getUserFilters()
    {        
        $data = DB::table('wh_filter')->where('iduser', Auth::id())->orWhereNull('iduser')->get(['id', 'name', 'data', 'iduser']);

        $data->whereNull('iduser')->each(function ($item, $key) {

            if(strpos($item->data, "?") !== false){
                $result = str_replace("?", Auth::id(), $item->data);
                $item->data = $result;
            }
        });

        return $data;
    }

    public function create($request)
    {
        $request->merge(['iduser' => Auth::id()]);
        return Filter::create($request->all());
    }

    public function update($request)
    {
        $model = Filter::find($request->id);
        $model->update($request->all());
        return $model;
    }
    public function delete($request)
    {
        $model = Filter::findOrFail($request->id);
        $model->delete();
        return $model;
    }
    
}