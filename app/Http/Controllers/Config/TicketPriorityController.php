<?php

namespace App\Http\Controllers\Config;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\TicketPriorityRepository;

class TicketPriorityController extends Controller
{
    protected $ticketPriorityRepository;

    public function __construct()
    {
        $this->middleware('auth', ['only' => 'index']);
        $this->ticketPriorityRepository = new TicketPriorityRepository;
    }

    public function index()
    {
        $breadcrumbs = [ ['link'=>"/",'name'=> 'Home'],['link'=>"/config-dashboard",'name'=>"Configuration"], ['name'=>"Prioridades"] ];
           
        $pageConfigs = [ 'pageHeader' => true ];

        return view('pages.config.ticketPriority.index', [
            'pageConfigs' => $pageConfigs,
            'breadcrumbs' => $breadcrumbs
        ]);
    }

    public function getAll()
    {
        return $this->ticketPriorityRepository->getAll();
    }

    public function update(Request $request)
    {
        return $this->ticketPriorityRepository->update($request);
    }
}
