<?php

namespace App\Http\Controllers\Config;

use Illuminate\Http\Request;
use App\Repositories\AppReminderRepository;
use App\Http\Controllers\Controller;

class AppReminderController extends Controller
{
    protected $appReminderRepository;

    public function __construct()
    {
        $this->middleware('auth', ['only' => 'index']);
        $this->appReminderRepository = new AppReminderRepository;
    }

    public function index(Request $request)
    {        

        $breadcrumbs = [
            ['link'=>"/",'name'=> 'Home'],['link'=>"/config-dashboard",'name'=>"Configuration"], ['name'=>"Reminders"]
        ];
           
        $pageConfigs = [
            'pageHeader' => true,
        ];

        return view('/pages/config/reminders/index', [
            'pageConfigs'  => $pageConfigs,
            'breadcrumbs'  => $breadcrumbs,
        ]);
    }

    public function getAll()
    {
        return $this->appReminderRepository->getAll();
    }

    public function create(Request $request)
    {
        return $this->appReminderRepository->create($request);
    }

    public function update(Request $request)
    {
        return $this->appReminderRepository->update($request);
    }

    public function delete(Request $request)
    {
        return $this->appReminderRepository->delete($request);
    }

    public function change(Request $request)
    {
        return $this->appReminderRepository->change($request);
    }
}
