<?php

namespace App\Http\Controllers\Config;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\MenuRepository;

class MenuController extends Controller
{
    protected $menuRepository;

    public function __construct()
    {
        $this->middleware('auth', ['only' => 'index']);
        $this->menuRepository = new MenuRepository;
    }

    public function index()
    {
        $breadcrumbs = [ ['link'=>"/",'name'=> 'Home'],['link'=>"/config-dashboard",'name'=>"Configuration"], ['name'=>"Menu"] ];
           
        $pageConfigs = [ 'pageHeader' => true ];

        return view('pages.config.menu.index', [
            'pageConfigs' => $pageConfigs,
            'breadcrumbs' => $breadcrumbs
        ]);
    }

    public function getAll()
    {
        return $this->menuRepository->getAll();
    }

    public function enable(Request $request)
    {
        return $this->menuRepository->enable($request);
    }
}
