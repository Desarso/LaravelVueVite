<?php

namespace App\Repositories;
use Illuminate\Support\Facades\DB;
use App\Models\Team;

class TeamRepository
{
    protected $config;

    public function __construct()
    {
        $this->config = new ConfigRepository;
    }

    public function getAll()
    {     
        return Team::with('users:id as iduser')->orderBy('name', 'asc')->get();
    }

    private function formatBosses($idboss)
    {
        $boss = new \stdClass;
        $boss->value = $idboss;
        return $boss;
    }

    private function pluckBosses($bosses)
    {
        $bosses = collect($bosses)->pluck('value')->toArray();
        $bosses = array_map('intval', $bosses);
        return $bosses;
    }

    public function getList()
    {
        return DB::table('wh_team')->orderBy('name', 'ASC')->get(['id as value', 'name as text', 'color']);
    }

    public function create($request)
    {
        $request->merge(['bosses' => json_encode($request->bosses)]);
       
        $team = Team::create($request->all());   

        $team->users()->attach($this->getFormatUsers($request->users));
 
        return response()->json(['success' => true, 'model' => $team]);
    }

    public function update($request)
    {
        $request->merge(['bosses' => json_encode($request->bosses)]);

        $team = Team::find($request->id);
        
        $team->fill($request->all())->save();

        $team->users()->sync($this->getFormatUsers($request->users));

        return response()->json(['success' => true, 'model' => $team]);
    }

    public function delete($request)
    {
        $team = Team::findOrFail($request->id);

        $hasRelations = $this->config->checkRelations($team, ['tickets', 'users']);

        if(!$hasRelations)
        {
            $team->delete();
            return response()->json(['success' => true, 'model' => $team]);
        }
        else
        {
            return response()->json(['success' => false, 'model' => $team, 'relations' => $hasRelations]);
        }
    }

    public function getListApp()
    {
        return DB::table('wh_team')
                ->select('id', 'name', 'color')
                ->whereNull('deleted_at')
                ->get();
    }

    private function getFormatUsers($users)
    {
        $result = array();
        
        if(is_null($users)) return [];

        foreach((array)$users as $user)
        {
            $result[$user] = ['idrole' => 1];
        }

        return $result;
    }

    public function restore($request)
    {
        $team = Team::withTrashed()->findOrFail($request->id);
        
        $team->restore();

        return response()->json(['success' => true, 'model' => $team]);
    }
}