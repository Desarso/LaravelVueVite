<?php

namespace App\Http\Controllers\Config;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\SpotRepository;
use App\Repositories\SpotTypeRepository;

class SpotController extends Controller
{
    protected $spot;

    public function __construct(SpotRepository $spot)
    {
        $this->middleware('auth', ['only' => 'index']);
        $this->spot   = $spot;
    }

    public function index(Request $request)
    {        
        $st = new SpotTypeRepository;
        $breadcrumbs = [
            ['link'=>"/",'name'=> 'Home'],['link'=>"/config-dashboard",'name'=>"Configuration"], ['name'=>"Spots"]
        ];
           
        $pageConfigs = [
            'pageHeader' => true,
        ];

        return view('/pages/config/spots/index', [
            'pageConfigs'  => $pageConfigs,
            'breadcrumbs'  => $breadcrumbs,
            "spotTypes"    => $st->getList(), 
            "spots"        => $this->spot->getList(),
            "open"         => ($request->has('open') ? 'true' : 'false'),
            "spotsParents" => $this->spot->getParents()
        ]);
    }


    // TESTING FOR gabriel

    public function test() {
        return view('/pages/config/spots/test', [
            "spots" => $this->spot->getAll()
        ]);
    }


    /* 

    
    public function test_inertia() {
        return inertia('/pages/config/spots/testinertia', [
            "spots" => $this->spot->getAll()
        ]);
    }
    */ 

    public function getAll()
    {
        return $this->spot->getAll();
    }

    public function getSpots()
    {
        return $this->spot->getSpots();
    }

    public function getList()
    {
        return $this->spot->getList();
    }

    // Require Cleaning
    public function getRequireCleaningSpots()
    {
        return $this->spot->getRequireCleaningSpots();
    }
   
    public function create(Request $request)
    {
        return $this->spot->create($request);
    }

    public function createOnFly(Request $request)
    {
        return $this->spot->createOnFly($request);
    }

    public function update(Request $request)
    {
        return $this->spot->update($request);
    }

    public function delete(Request $request)
    {
        return $this->spot->delete($request);
    }

    public function restore(Request $request)
    {
        return $this->spot->restore($request);
    }

    public function getHierarchy()
    {
        return $this->spot->getHierarchy();
    }

    public function getListApp(Request $request)
    {
        return $this->spot->getListApp($request->iduser, null, $request);
    }

    public function getChildren($idspot = 0)
    {
        return $this->spot->getChildren($idspot);
    }

    public function getCleningSpotsAPP(Request $request)
    {
        return $this->spot->getCleningSpotsAPP($request);
    }

    public function searchSpotBranchAPP(Request $request)
    {
        return $this->spot->searchSpotBranchAPP($request);
    }

    public function getAllSpotsTreeList(Request $request)
    {
        return [];//$this->spot->getDataTreeList($request);
    }

    
}
