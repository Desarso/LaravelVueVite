<?php

namespace App\Http\Controllers\Config;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\TeamRepository;
use App\Repositories\RoleRepository;

class TeamController extends Controller
{
    protected $team;
    protected $role;

    public function __construct()
    {
        $this->middleware('auth', ['only' => 'index']);
        $this->team = new TeamRepository;
        $this->role = new RoleRepository;
    }

    public function index()
    {               
        $breadcrumbs = [
            ['link'=>"/",'name'=> 'Home'],['link'=>"/config-dashboard",'name'=>"Configuration"], ['name'=>"Teams"], ['link'=>"/config-users",'name'=>"Users"]
        ];
           
        $pageConfigs = [
            'pageHeader' => true,            
        ];

        return view('/pages/config/teams/index', [
            'pageConfigs' => $pageConfigs,
            'breadcrumbs' => $breadcrumbs
        ]);
    }

    public function getAll()
    {
        return $this->team->getAll();
    }

    public function getList()
    {
        return $this->team->getList();
    }

    public function create(Request $request)
    {
        return $this->team->create($request);
    }

    public function update(Request $request)
    {
        return $this->team->update($request);
    }

    public function delete(Request $request)
    {
        return $this->team->delete($request);
    }

    public function restore(Request $request)
    {
        return $this->team->restore($request);
    }

    public function getListApp(Request $request)
    {
        return $this->team->getListApp();
    }
}
