<?php

namespace App\Http\Controllers\Production;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\Production\EquipmentRepository;
use App\Repositories\Production\EquipmentTypeRepository;
use App\Repositories\Production\EquipmentStatusRepository;

use App\Repositories\Production\ProductCategoryRepository;


class EquipmentController extends Controller
{
    protected $equipment;
    protected $types;
    protected $status;
    
    protected $productCategory;

    public function __construct(EquipmentRepository $equipment, EquipmentTypeRepository $type,
        EquipmentStatusRepository $status,
        ProductCategoryRepository $productCategory
        )
    {
        $this->equipment = $equipment;
        $this->type = $type;
        $this->status = $status;        
        $this->productCategory = $productCategory;
    }

    public function index()
    {               
        $breadcrumbs = [
            ['link'=>"/",'name'=> 'Home'],['link'=>"/config-dashboard",'name'=>"Configuration"], ['name'=>"Equipments"]
        ];
           
        $pageConfigs = [
            'pageHeader' => true,            
        ];

        return view('/pages/production/equipment', [
            'pageConfigs' => $pageConfigs,
            'breadcrumbs' => $breadcrumbs,
            'statuses'    => $this->status->getList(),
            'types'       => $this->type->getList(),            
            'productcategories' => $this->productCategory->getList()
        ]);
    }

    public function getAll()
    {
        return $this->equipment->getAll();
    }

    public function getList()
    {
        return $this->equipment->getList();
    }

    public function create(Request $request)
    {
        return $this->equipment->create($request);
    }

    public function update(Request $request)
    {
        return $this->equipment->update($request);
    }

    public function delete(Request $request)
    {
        return $this->equipment->delete($request);
    }

 
}
