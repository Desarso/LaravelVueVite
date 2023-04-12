<?php

namespace App\Http\Controllers\Config;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\SpotTypeRepository;

class SpotTypeController extends Controller
{
    protected $spotType;

    public function __construct(SpotTypeRepository $spotType)
    {
        $this->middleware('auth', ['only' => 'index']);
        $this->spotType = $spotType;
    }

    public function index()
    {
        
        $breadcrumbs = [
            ['link'=>"/",'name'=> 'Home'],['link'=>"/config-dashboard",'name'=>"Configuration"], ['name'=>"Spot Types"],['link'=>"/config-spots",'name'=>"Spots"]
        ];
           
        $pageConfigs = [
            'pageHeader' => true,
            
        ];

        return view('/pages/config/spottypes/index', [
            'pageConfigs' => $pageConfigs,
            'breadcrumbs' => $breadcrumbs
        ]);
    }

    public function getAll()
    {
        return $this->spotType->getAll();
    }

    public function getList()
    {
        return $this->spotType->getList();
    }

    public function create(Request $request)
    {
        return $this->spotType->create($request);
    }

    public function update(Request $request)
    {
        return $this->spotType->update($request);
    }

    public function delete(Request $request)
    {
        return $this->spotType->delete($request);
    }

    public function restore(Request $request)
    {
        return $this->spotType->restore($request);
    }
}
