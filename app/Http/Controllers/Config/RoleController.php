<?php

namespace App\Http\Controllers\Config;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\RoleRepository;

class RoleController extends Controller
{
    protected $role;

    public function __construct(RoleRepository $role)
    {
        $this->middleware('auth', ['only' => 'index']);
        $this->role = $role;
    }

    public function index()
    {
        $breadcrumbs = [ ['link'=>"/",'name'=> 'Home'],['link'=>"/config-dashboard",'name'=>"Configuration"], ['name'=>"Roles"] ];
           
        $pageConfigs = [ 'pageHeader' => true ];

        return view('pages.config.roles.index', [
            'pageConfigs' => $pageConfigs,
            'breadcrumbs' => $breadcrumbs
        ]);
    }

    public function getAll()
    {
        return $this->role->getAll();
    }

    public function getList()
    {
        return $this->role->getList();
    }

    public function create(Request $request)
    {
        return $this->role->create($request);
    }

    public function update(Request $request)
    {
        return $this->role->update($request);
    }

    public function delete(Request $request)
    {
        return $this->role->delete($request);
    }

    public function restore(Request $request)
    {
        return $this->role->restore($request);
    }

    public function change(Request $request)
    {
        return $this->role->change($request);
    }
}
