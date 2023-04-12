<?php

namespace App\Repositories;
use Illuminate\Support\Facades\DB;
use App\Models\SpotType;

class SpotTypeRepository
{
    protected $config;

    public function __construct()
    {
        $this->config = new ConfigRepository;
    }

    public function getAll()
    {
        return SpotType::whereNull('deleted_at')
                        ->orderBy('name', 'ASC')
                        ->get(['id', 'name', 'description', 'islodging', 'code']);
    }

    public function getList()
    {
        return DB::table('wh_spot_type')
                    ->orderBy('name', 'ASC')
                    ->get(['id as value', 'name as text','islodging as islodging']);
    }
  
    public function getListLodging() {
        return SpotType::whereNull('deleted_at')->where('islodging','=',1)->get(['id as value', 'name as text']);
    }

    public function create($request)
    {
        $spotType = SpotType::create($request->all());

        return response()->json(['success' => true, 'model' => $spotType]);
    }

    public function update($request)
    {       
        $spotType = SpotType::find($request->id);

        $spotType->fill($request->all())->save();

        return response()->json(['success' => true, 'model' => $spotType]);
    }

    public function delete($request)
    {
        $spotType = SpotType::findOrFail($request->id);

        $hasRelations = $this->config->checkRelations($spotType, ['spots']);

        if(!$hasRelations)
        {
            $spotType->delete();
            return response()->json(['success' => true, 'model' => $spotType]);
        }
        else
        {
            return response()->json(['success' => false, 'model' => $spotType, 'relations' => $hasRelations]);
        }
    }

    public function restore($request)
    {
        $spotType = SpotType::withTrashed()->findOrFail($request->id);

        $spotType->restore();

        return response()->json(['success' => true, 'model' => $spotType]);
    }
}