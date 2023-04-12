<?php

namespace App\Http\Controllers\Config;

use App\Http\Controllers\Controller;
use App\Repositories\PlannerRepository;


use App\Repositories\ItemRepository;
use App\Repositories\SpotRepository;
use Illuminate\Http\Request;

class PlannerController extends Controller
{
    protected $planner;    
    protected $item;
    protected $spot;

    public function __construct(PlannerRepository $planner, ItemRepository $item)
    {        
        $this->planner = $planner;
        $this->item    = $item;
    }

    public function index()
    {
        $breadcrumbs = [
            ['link' => "/", 'name' => 'Home'], ['link' => "/config-dashboard", 'name' => "Configuration"], ['name' => "Planner"],
        ];

        $pageConfigs = [
            'pageHeader' => true,
        ];

        return view('/pages/config/planner/index', [
            'items' => $this->item->getList(),           
            'pageConfigs' => $pageConfigs,
            'breadcrumbs' => $breadcrumbs,
        ]);
    }

    public function getAll()
    {
        return $this->planner->getAll();
    }

    public function getAllScheduler()
    {
        return $this->planner->getAllScheduler();
    }

    public function create(Request $request)
    {
        return $this->planner->create($request);
    }

    public function update(Request $request)
    {
        return $this->planner->update($request);
    }

    public function delete(Request $request)
    {
        return $this->planner->delete($request);
    }

    public function enabledPlanner(Request $request)
    {
        return $this->planner->enabledPlanner($request);
    }

    public function generateRecurringTickets(Request $request)
    {
        return $this->planner->generateRecurringTickets($request);
    }

    public function createPlannerTask(Request $request)
    {
        return $this->planner->createPlannerTask($request);
    }

    public function updatePlannerTask(Request $request)
    {
        return $this->planner->updatePlannerTask($request);
    }
}
