<?php

namespace App\Http\Controllers\Production;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\Production\ProductionLogRepository;


class ProductionLogController extends Controller
{
    protected $log;
    
    public function __construct(ProductionLogRepository $log)
    {
        $this->log = $log;
        
    }

    public function index()
    {               
        $breadcrumbs = [
            ['link'=>"/",'name'=> 'Home'],['link'=>"/config-dashboard",'name'=>"Configuration"], ['name'=>"Production Stops"]
        ];
           
        $pageConfigs = [
            'pageHeader' => true,            
        ];

        return view('/pages/production/production-stop', [
            'pageConfigs' => $pageConfigs,
            'breadcrumbs' => $breadcrumbs,
            
            //'productcategories' => $this->productCategory->getList(),
            
        ]);
    }

    public function getAll()
    {
        return $this->log->getAll();
    }

    public function getList()
    {
        return $this->log->getList();
    }

    public function getProductionLog(Request $request) 
    {
        return $this->log->getProductionLog($request);
    }

    public function getAllProductionsLog(Request $request) 
    {
        return $this->log->getAllProductionsLog($request);
    }

    /*
    public function create(Request $request)
    {
        return $this->log->create($request);
    }

    public function update(Request $request)
    {
        return $this->log->update($request);
    }

    public function delete(Request $request)
    {
        return $this->log->delete($request);
    }


    public function startProductionLog(Request $request) {        
        return $this->log->startProductionLog($request);

    }

    public function finishProductionLog(Request $request) {
        return $this->log->finishProductionLog($request);
    }

    public function pauseProductionLog(Request $request) {
        return $this->log->pauseProductionLog($request);
    }

    public function resumeProductionLog(Request $request) {
        return $this->log->resumeProductionLog($request);
    }
   */
    

}
