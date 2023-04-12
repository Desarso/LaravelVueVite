<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\Warehouse\WarehouseCategoryRepository;

class WarehouseCategoryController extends Controller
{
    protected $warehouseCategory;

    public function __construct()
    {
        $this->warehouseCategory = new WarehouseCategoryRepository;
    }

    public function index()
    {               
        $breadcrumbs = [
            ['link' => "/",'name'=> 'Home'], ['link' => "/config-dashboard",'name' => "Configuration"], ['name' => "Warehouse Category"]
        ];
           
        $pageConfigs = [
            'pageHeader' => true,            
        ];

        return view('/pages/warehouse/warehouse-category', [
            'pageConfigs' => $pageConfigs,
            'breadcrumbs' => $breadcrumbs,           
        ]);
    }

    public function getAll()
    {
        return $this->warehouseCategory->getAll();
    }

    public function create(Request $request)
    {
        return $this->warehouseCategory->create($request);
    }

    public function update(Request $request)
    {
        return $this->warehouseCategory->update($request);
    }

    public function delete(Request $request)
    {
        return $this->warehouseCategory->delete($request);
    }
}
