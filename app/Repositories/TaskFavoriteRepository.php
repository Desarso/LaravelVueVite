<?php

namespace App\Repositories;
use Illuminate\Support\Facades\DB;
use App\Models\TaskFavorite;

class TaskFavoriteRepository
{
    public function saveFavorite($request)
    {
        $result = false;
        $taskFavorite = TaskFavorite::create($request->all());
        if($taskFavorite) $result = true;

        return response()->json([
            "result" => $result,
        ]);
    }


    public function getFavoritesByIduser($request)
    {
        return TaskFavorite::where('iduser', $request->idUser)
                        ->with('spot:id,name')
                        ->with([
                            'item:id,name,idtype,idchecklist,idteam',
                            'item.tickettype:id,name,icon,color'
                        ])
                        ->select('id', 'name', 'iduser', 'idspot', 'iditem')
                        ->get();
    }


    public function deleteFavorite($request)
    {
        $result = false;
        $taskFavorite = TaskFavorite::find($request->idfavorite);
        if($taskFavorite) {
            $taskFavorite->delete();
            $result = true;
        }

        return response()->json([
            "result" => $result,
        ]);
    }
}