<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Models\Spot;
use Illuminate\Http\Request;
use App\Repositories\ItemRepository;
use App\Repositories\SpotRepository;
use App\Repositories\TicketPriorityRepository;
use App\Repositories\Warehouse\WarehouseReportRepository;

class WarehouseReportController extends Controller
{

    protected $warehouseReport;
    protected $item;
    protected $spot;
    protected $priority;

    public function __construct(WarehouseReportRepository $warehouseReport,
                                SpotRepository $spot, 
                                ItemRepository $item, 
                                TicketPriorityRepository $priority )
    {
        $this->warehouseReport = $warehouseReport;
        $this->item = $item;
        $this->priority = $priority;
        $this->spot = $spot;
    }

    public function index()
    {               
        $breadcrumbs = [
            ['link'=>"/",'name'=> 'Home'],['link'=>"/warehouse-report",'name'=>"Reports"], ['name'=>"Warehouse Report"]
        ];
          
        $pageConfigs = ['pageHeader' => true,];
        
        return view('/pages/warehouse/warehouseReport', [
            'pageConfigs' => $pageConfigs,
            'breadcrumbs' => $breadcrumbs,        
        ]);
    }

    public function getWarehouseReport(Request $request)
    {
        return $this->warehouseReport->getAll($request);
    }

    public function getGeneralAverage(Request $request)
    {
        return $this->warehouseReport->getGeneralAverage($request);
    }

    public function getLast()
    {
        return $this->warehouseReport->getLast();
    }
}
