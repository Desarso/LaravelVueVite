<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Models\Warehouse\WarehouseItem;
use App\Models\Warehouse\WarehouseStatus;
use App\Repositories\ItemRepository;
use App\Repositories\SpotRepository;
use App\Repositories\TicketPriorityRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use App\Repositories\Warehouse;
use App\Repositories\Warehouse\WarehouseRepository;

use App\Repositories\Warehouse\WarehouseStatusRepository;
use App\Repositories\Warehouse\WarehouseItemRepository;
use App\Repositories\Warehouse\WarehouseSupplierRepository;

class WarehouseController extends Controller
{
    protected $warehouse;
    protected $warehouseStatus;
    protected $WarehouseSupplier;
    protected $item;
    protected $spot;
    protected $user;
    protected $priority;

    public function __construct(WarehouseRepository $warehouse,
                                WarehouseStatusRepository $warehouseStatus,
                                SpotRepository $spot, 
                                WarehouseItemRepository $item, 
                                UserRepository $user,
                                TicketPriorityRepository $priority )
    {
        $this->middleware('auth', ['only' => 'index']);
        
        $this->warehouse = $warehouse;
        $this->warehouseStatus = $warehouseStatus;
        $this->warehouseSupplier = new WarehouseSupplierRepository;
        $this->item = $item;
        $this->priority = $priority;
        $this->spot = $spot;
        $this->user = $user;

    }


    public function index()
    {               
        $breadcrumbs = [
            ['link'=>"/",'name'=> 'Home'],['link'=>"/config-dashboard",'name'=>"Configuration"], ['name'=>"Warehouse"]
        ];
           
        $pageConfigs = [
            'pageHeader' => true,            
        ];

        return view('/pages/warehouse/warehouse', [
            'pageConfigs'         => $pageConfigs,
            'breadcrumbs'         => $breadcrumbs,     
            'warehouse_settings'  => json_encode($this->warehouse->getSettings()),
            'warehouse_statuses'  => $this->warehouseStatus->getList(),
            'warehouse_suppliers' => $this->warehouseSupplier->getList(),
            'warehouse_items'     => $this->item->getList(),
        ]);
    }

    public function getWarehouses(Request $request)
    {
        return $this->warehouse->getAll($request);
    }

    public function nextStatus(Request $request)
    {
        $statuses = $this->warehouse->nextStatus($request);

        return view('pages.warehouse.next-status', ["statuses" => $statuses]);
    }

    public function changeStatus(Request $request)
    {
        return $this->warehouse->changeStatus($request);
    }
    
    public function getLast()
    {
        return $this->warehouse->getLast();
    }

    public function create(Request $request)
    {
        return $this->warehouse->create($request);
    }

    public function update(Request $request)
    {
        return $this->warehouse->update($request);
    }

    public function delete(Request $request)
    {
        return $this->warehouse->delete($request);
    }

    /********** APP`s functions **************/

    public function getWarehouseRequestAPP(Request $request)
    {
        return $this->warehouse->getWarehouseRequestAPP($request);
    }

    public function saveWarehouseRequestAPP(Request $request)
    {
        return $this->warehouse->saveWarehouseRequestAPP($request);
    }

}//END
