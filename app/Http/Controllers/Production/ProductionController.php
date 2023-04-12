<?php

namespace App\Http\Controllers\Production;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\Production\ProductionRepository;
use App\Repositories\Production\ProductionDetailRepository;
use App\Repositories\Production\ProductionStatusRepository;
use App\Repositories\Production\ProductionScheduleRepository;

use App\Repositories\Production\ProductRepository;
use App\Repositories\Production\EquipmentRepository;
use App\Repositories\Production\ProductPresentationRepository;
use App\Repositories\Production\ProductDestinationRepository;


class ProductionController extends Controller
{
    protected $production;
    protected $productiondetail;
    protected $product;
    protected $equipment;
    protected $schedule;    
    protected $productDestination;
    protected $productionStatus;

    public function __construct(ProductionRepository $production, ProductionDetailRepository $productiondetail,
                            ProductRepository $product, 
                            EquipmentRepository $equipment,
                            ProductionScheduleRepository $schedule,
                            ProductDestinationRepository $productDestination,
                            ProductionStatusRepository $productionStatus
                            )
    {
        $this->production = $production;
        $this->productiondetail = $productiondetail;
        $this->product = $product;
        $this->equipment = $equipment;
        $this->schedule = $schedule;
        $this->productDestination = $productDestination;
        $this->productionStatus = $productionStatus;
         
    }

    public function index()
    {               
        $breadcrumbs = [
            ['link'=>"/",'name'=> 'Home'],['link'=>"/config-dashboard",'name'=>"Configuration"], ['name'=>"Production"],['link'=>"/dashboard-production",'name'=>"Dashboard"]
        ];
           
        $pageConfigs = [
            'pageHeader' => true,            
        ];

        return view('/pages/production/production', [
            'pageConfigs' => $pageConfigs,
            'breadcrumbs' => $breadcrumbs,
            'equipments' => $this->equipment->getList(),
            'products' => $this->product->getList(),
            'schedules'   => $this->schedule->getList(),
            'productionstatuses' => $this->productionStatus->getList()
            
        ]);
    }

    public function dashboard()
    {               
        $breadcrumbs = [
            ['link'=>"/",'name'=> 'Home'],['link'=>"/config-dashboard",'name'=>"Configuration"], ['name'=>"Production Dashboard"]
        ];
           
        $pageConfigs = [
            'verticalMenuNavbarType' => 'sticky',
            'pageHeader' => true,            
        ];

        return view('/pages/production/dashboard-production', [
            'schedules'   => $this->schedule->getList(),
            'pageConfigs' => $pageConfigs,
            'breadcrumbs' => $breadcrumbs
        ]);
    }



    public function getCurrentProduction(Request $request) {
        return $this->production->getCurrentProduction($request);
    }

    public function getProductions(Request $request)
    {
        return $this->production->getAll($request);
    }

    public function startProduction(Request $request)
    {
        return $this->production->startProduction($request);
    }

    public function finishProduction(Request $request)
    {
        return $this->production->finishProduction($request);
    }

    // Sets idoperator or production and idproduction of equipment
    public function updateEquipmentProduction(Request $request)
    {
        return $this->production->updateEquipmentProduction($request);
    }
    
    public function initializeProduction(Request $request)
    {
        return $this->production->initializeProduction($request);
    }

    public function create(Request $request)
    {
        return $this->production->create($request);
    }

    public function createFromApp(Request $request)
    {
        
        return $this->production->createFromApp($request);
    }

    public function update(Request $request)
    {
        return $this->production->update($request);
    }

    public function delete(Request $request)
    {
        return $this->production->delete($request);
    }

    public function getLast()
    {
        return $this->production->getLast();
    }

    //////////////////////// PRODUCTION DETAIL /////////////////////////////////

    public function getProductionDetails(Request $request)
    {
        return $this->productiondetail->getProductionDetails($request->idproduction);
    }

    public function createProductionDetail(Request $request)
    {
        return $this->productiondetail->create($request);
    }

    public function updateProductionDetail(Request $request)
    {
        return $this->productiondetail->update($request);
    }

    public function deleteProductionDetail(Request $request)
    {
        return $this->productiondetail->delete($request);
    }

 


    /// STOPS

    public function reportStop(Request $request) 
    {
        return $this->production->reportStop($request);
    }

    public function updateReportedStop(Request $request)
     {
        return $this->production->updateReportedStop($request);
    }

    public function discardStop(Request $request) 
    {
        return $this->production->discardStop($request);
    }

    public function startStop(Request $request)
     {
        return $this->production->startStop($request);
    }

    public function finishStop(Request $request) 
    {
        return $this->production->finishStop($request);
    }

    public function pauseStop(Request $request) 
    {
        return $this->production->pauseStop($request);
    }

    public function resumeStop(Request $request) 
    {
        return $this->production->resumeStop($request);
    }




}