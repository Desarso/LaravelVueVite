<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\Warehouse\WarehouseItemRepository;
use App\Repositories\Warehouse\WarehouseCategoryRepository;

class WarehouseItemController extends Controller
{
    protected $warehouseItem;
    protected $warehouseCategory;

    public function __construct()
    {
        $this->warehouseItem     = new WarehouseItemRepository;
        $this->warehouseCategory = new WarehouseCategoryRepository;
    }

    public function index()
    {               
        $breadcrumbs = [ ['link' => "/",'name'=> 'Home'], ['link' => "/config-dashboard",'name' => "Configuration"], ['name' => "Warehouse Items"] ];
           
        $pageConfigs = [ 'pageHeader' => true ];

        return view('/pages/warehouse/warehouse-item', [
            'pageConfigs' => $pageConfigs,
            'breadcrumbs' => $breadcrumbs,      
            'categories'  => $this->warehouseCategory->getList()     
        ]);
    }


    public function getWarehouseItems(Request $request)
    {
        return $this->warehouseItem->getAll($request);
    }

    public function getLast()
    {
        return $this->warehouseItem->getLast();
    }

    public function create(Request $request)
    {
        return $this->warehouseItem->create($request);
    }

    public function update(Request $request)
    {
        return $this->warehouseItem->update($request);
    }

    public function delete(Request $request)
    {
        return $this->warehouseItem->delete($request);
    }

    public function getValueMapper(Request $request)
    {
        return $this->warehouseItem->getValueMapper($request);
    }

    public function getAllWarehouseItems(Request $request)
    {
        return $this->warehouseItem->getAllWarehouseItems($request);
    }
    

    /********** APP`s functions **************/

    public function searchItemsWarehouseAPP(Request $request)
    {
        return $this->warehouseItem->searchItemsWarehouseAPP($request);
    }

}//FINAL