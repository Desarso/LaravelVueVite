<?php

namespace App\Http\Controllers\Config;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\AppRepository;


class AppController extends Controller
{
    public $app;

    public function __construct(AppRepository $app)
    {
        // $this->middleware('auth', ['only' => 'index']);
        $this->app = $app;
    }

    public function index()
    {
        $breadcrumbs = [
            ['link' => "/", 'name' => 'Home'], ['name' => "Apps"],
        ];

        $pageConfigs = [
            'pageHeader' => true,
        ];

        return view('/pages/config/apps/index', [
            'apps'        => $this->app->getAll(),  
            'pageConfigs' => $pageConfigs,
            'breadcrumbs' => $breadcrumbs,
        ]);
    }

    public function getAllApps()
    {
        return $this->app->getAllAPP();
    }

}
