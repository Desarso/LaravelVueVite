<?php

namespace App\Http\Controllers\Production;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\Production\ProductionScheduleRepository;
use App\Repositories\Production\ProductionBreakRepository;


class ProductionScheduleController extends Controller
{
    protected $schedule;
    protected $break;
    
    public function __construct(ProductionScheduleRepository $schedule, ProductionBreakRepository $break)
    {
        $this->schedule = $schedule;
        $this->break = $break;
        
    }

    public function index()
    {               
        $breadcrumbs = [
            ['link'=>"/",'name'=> 'Home'],['link'=>"/config-dashboard",'name'=>"Configuration"], ['name'=>"Production Schedules"]
        ];
           
        $pageConfigs = [
            'pageHeader' => true,            
        ];

        return view('/pages/production/production-schedule', [
            'pageConfigs' => $pageConfigs,
            'breadcrumbs' => $breadcrumbs,            
            'breaks'      => $this->break->getList(),
            
        ]);
    }

    public function getAll()
    {
        return $this->schedule->getAll();
    }

    public function getList()
    {
        return $this->schedule->getList();
    }

    public function create(Request $request)
    {
        return $this->schedule->create($request);
    }

    public function update(Request $request)
    {
        return $this->schedule->update($request);
    }

    public function delete(Request $request)
    {
        return $this->schedule->delete($request);
    }


    
}
