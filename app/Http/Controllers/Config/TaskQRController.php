<?php

namespace App\Http\Controllers\Config;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\TicketQrRepository;

class TaskQRController extends Controller
{
    protected $team;
    protected $role;

    public function __construct()
    {
        $this->middleware('auth', ['only' => 'index']);
        $this->ticketQr = new TicketQrRepository;
    }

    public function index()
    {               
        $breadcrumbs = [
            ['link'=>"/",'name'=> 'Home'],['link'=>"/config-dashboard",'name'=>"Configuration"], ['name'=>"qr"]
        ];
           
        $pageConfigs = [
            'pageHeader' => true,            
        ];

        return view('/pages/config/taskQR/index', [
            'pageConfigs' => $pageConfigs,
            'breadcrumbs' => $breadcrumbs
        ]);
    }

    public function getAll(Request $request)
    {
        return $this->ticketQr->getAll($request);
    }

    public function create(Request $request)
    {
        return $this->ticketQr->create($request);
    }

    public function delete(Request $request)
    {
        return $this->ticketQr->delete($request);
    }
}
