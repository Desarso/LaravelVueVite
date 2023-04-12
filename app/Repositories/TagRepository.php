<?php

namespace App\Repositories;
use Illuminate\Support\Facades\DB;
use App\Models\Tag;

class TagRepository
{
    public function getAll()
    {
        return DB::table('wh_tag')->select('id', 'name', 'color')->get();
    }

    public function getList()
    {
        return DB::table('wh_tag')->get(['id as value', 'name as text', 'color']);
    }

    public function create($request)
    {
        $tag = Tag::create($request->all());
    }

    public function createOnFly($request)
    {
        $tag = Tag::create($request->all());

        return response()->json(['success' => true, 'message' => 'Acción completada con éxito', "model" => $tag->refresh()]);
    }

    public function getListApp($updated_at = null)
    {
        $items = DB::table('wh_tag')
                    ->select('id', 'name', 'color', 'deleted_at')
                    ->when(!is_null($updated_at), function ($query) use ($updated_at){
                        return $query->where('updated_at', '>', $updated_at);
                    }, function ($query) {
                        return $query->whereNull('deleted_at');
                    })
                    ->get();

        return $items;
    }
}