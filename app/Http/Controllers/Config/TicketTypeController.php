<?php

namespace App\Http\Controllers\Config;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\TicketTypeRepository;
use App\Repositories\TeamRepository;

class TicketTypeController extends Controller
{
    protected $ticketType;

    public function __construct(TicketTypeRepository $ticketType)
    {
        $this->middleware('auth', ['only' => 'index']);
        $this->ticketType = $ticketType;
    }

    public function index()
    {        
        $teams = new TeamRepository;
        $breadcrumbs = [
            ['link'=>"/",'name'=> 'Home'],['link'=>"/config-dashboard",'name'=>"Configuration"], ['name'=>"Task Types"],['link'=>"/config-items",'name'=>"Items"]
        ];
           
        $pageConfigs = [
            'pageHeader' => true,            
        ];

        return view('/pages/config/tickettypes/index', [
            'pageConfigs' => $pageConfigs,
            'breadcrumbs' => $breadcrumbs,
            'teams'       => $teams->getList()
        ]);
    }

    public function getAll()
    {
        return $this->ticketType->getAll();
    }

    public function getList()
    {
        return $this->ticketType->getList();
    }

    public function create(Request $request)
    {
        return $this->ticketType->create($request);
    }

    public function createOnFly(Request $request)
    {
        return $this->ticketType->createOnFly($request);
    }

    public function update(Request $request)
    {
        return $this->ticketType->update($request);
    }

    public function delete(Request $request)
    {
        return $this->ticketType->delete($request);
    }

    public function restore(Request $request)
    {
        return $this->ticketType->restore($request);
    }
}
