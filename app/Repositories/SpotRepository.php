<?php

namespace App\Repositories;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Spot;

class SpotRepository
{
    protected $userRepository;
    protected $config;

    public function __construct()
    {
        $this->userRepository   = new UserRepository;
        $this->config = new ConfigRepository;
    }

    public function getAll()
    {
        return Spot::orderBy('name', 'ASC')
                    ->get(['id', 'name', 'shortname', 'idtype', 'idparent','isbranch', "cleanable", "floor", "enabled"]);      
    }

    public function getList()
    {
        return DB::table('wh_spot as s')
                 ->select('s.id as value', 's.name as text', DB::raw('if(s.idparent = -1,"",(select name from wh_spot where id = s.idparent)) as spotparent'), 's.idparent', 's.shortname', 's.isbranch', 's.enabled', 's.deleted_at')
                 ->orderBy('name', 'ASC')
                 ->get();
    }

    public function getGlobal()
    {
        return DB::table('wh_spot')->get(['id as value', 'name as text', 'isbranch']);
    }
    
    public function getRequireCleaningSpots()
    {
        return DB::table('wh_spot')->where('enabled', true)
        ->where('cleanable', true)->get(['id as value', 'name as text']);


    }

    public function create($request)
    {
        $spot = Spot::create($request->all());

        return response()->json(['success' => true, 'model' => $spot]);
    }

    public function createOnFly($request)
    {
        $spot = Spot::create($request->all());

        return response()->json(['success' => true, 'message' => 'Acción completada con éxito', 'model' => $spot->refresh()]);
    }

    public function update($request)
    {
        $spot = Spot::find($request->id);

        $spot->fill($request->all())->save();

        return response()->json(['success' => true, 'model' => $spot]);
    }

    public function delete($request)
    {
        $spot = Spot::findOrFail($request->id);

        $hasRelations = $this->config->checkRelations($spot, ['tickets', 'checklistOptions']);

        if(!$hasRelations)
        {
            $spot->delete();
            return response()->json(['success' => true, 'model' => $spot]);
        }
        else
        {
            return response()->json(['success' => false, 'model' => $spot, 'relations' => $hasRelations]);
        }
    }

    public function getHierarchy()
    {
        $parents = $this->getParents()->pluck('value')->toArray();

        $spots = Spot::whereIn('id', $parents)->orderBy("idparent")->orderBy("name")->get(['id', 'name as text', 'idparent']);

        if($spots->count() == 0) return [];

        $new = array();

        foreach ($spots as $spot) {
            $new[$spot['idparent']][] = $spot;
        }

        $tree = $this->buildTree($new, array($spots[0]));
        return $tree;
    }

    private function buildTree(&$list, $parent)
    {
        $tree = array();
        foreach ($parent as $k=>$l)
        {
            if(isset($list[$l['id']]))
            {
                $l['items'] = $this->buildTree($list, $list[$l['id']]);
            }
            $tree[] = $l;
        }
        return $tree;
    }

    public function getListApp($idUser, $updated_at = null, $request)
    {
        $version = isset($request->version) ? intval($request->version) : 400;
        $spots = $this->userRepository->getUserSpots($idUser);

        $colums = ['S1.id', 'S1.name', 'S1.cleanable', 'S1.deleted_at'];

        if ($version >= 405) {
            $newColumns = ['S2.name AS parent_name'];
            $colums = array_merge($colums, $newColumns);
        }

        return DB::table('wh_spot AS S1')
                ->leftJoin('wh_spot AS S2', 'S1.idparent', '=', 'S2.id')
                ->whereIn('S1.id', $spots)
                ->when(!is_null($updated_at), function ($query) use ($updated_at){
                    return $query->where('S1.updated_at', '>', $updated_at)
                                 ->orWhere('S1.deleted_at', '>', $updated_at);
                }, function ($query) {
                    return $query->whereNull('S1.deleted_at');
                })
                ->select($colums)
                ->get();
    }

    public function getChildren($idspot)
    {
        if(is_null($idspot)) return;

        $result = array();

        $parents = DB::table('wh_spot')->select('idparent')->distinct()->get()->pluck('idparent')->toArray();

        $models = DB::table('wh_spot')->select('id', 'idparent')->get();

        $this->getAllChildren($idspot, $models, $parents, $result);

        return $result;
    }

    private function getAllChildren($idspot, $models, $parents, &$result = array())
    {
        array_push($result, $idspot);
        
        $data = $models->where('idparent', $idspot)->where('id', '!=', $idspot)->whereNotIn('id', $result);

        $withoutChildren = $data->whereNotIn('id', $parents)->pluck('id')->toArray();

        $result = array_merge($result, $withoutChildren);

        $withChildren = $data->whereIn('id', $parents);

        foreach($withChildren as $children)
        {
            $this->getAllChildren($children->id, $models, $result);
        }
    }

    public function getCleningSpotsAPP($request)
    {
        $spots = $this->userRepository->getUserSpots($request->idUser);

        return DB::table('wh_spot')
                ->select('id', 'name')
                ->whereIn('id', $spots)
                ->where('cleanable', 1)
                ->whereNull('deleted_at')
                ->get();
    }

    public function getSpots($spot)
    {
        return DB::table('wh_spot')
                 ->where('idspot', $spot)
                 ->pluck('id')
                 ->toArray();
    }

    public function searchSpotBranchAPP($request)
    {
        $spots = $this->userRepository->getUserSpots($request->idUser);

        return DB::table('wh_spot')
                ->select('id', 'name', 'cleanable', 'deleted_at')
                ->whereIn('id', $spots)
                ->whereNull('deleted_at')
                ->where('isbranch', 1)
                ->when(isset($request->name), function ($query) use($request) {
                    $query->Where('name', 'LIKE', "%$request->name%");
                })
                ->get();
    }

    public function restore($request)
    {
        $spot = Spot::withTrashed()->findOrFail($request->id);

        $spot->restore();

        return response()->json(['success' => true, 'model' => $spot]);
    }

    public function getDataTreeList()
    {
        return DB::table('wh_spot')->select('id', 'name', 'idparent as parentId')->get();
    }

    public function getParents()
    {
        $parents = DB::table('wh_spot')->select('idparent')->distinct()->get()->pluck('idparent')->toArray();

        return DB::table('wh_spot')->select('id as value', 'name as text', 'idparent')->whereIn('id', $parents)->orWhere('isbranch', true)->get();
    }

    public function getUserBranch()
    {
        $userSpots = $this->userRepository->getUserSpots(Auth::id());
        return DB::table('wh_spot')
                    ->whereIn('id', $userSpots)
                    ->whereNull('deleted_at')
                    ->where('isbranch', 1)
                    ->get(['id as value', 'name as text']);
    }
}