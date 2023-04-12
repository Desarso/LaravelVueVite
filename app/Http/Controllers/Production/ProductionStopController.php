<?php

namespace App\Http\Controllers\Production;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\Production\ProductionStopRepository;


class ProductionStopController extends Controller
{
    protected $productionStop;
    
    public function __construct(ProductionStopRepository $productionStop)
    {
        $this->productionStop = $productionStop;
        
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
        return $this->productionStop->getAll();
    }

    public function getList()
    {
        return $this->productionStop->getList();
    }

    public function create(Request $request)
    {
        return $this->productionStop->create($request);
    }

    public function update(Request $request)
    {
        return $this->productionStop->update($request);
    }

    public function delete(Request $request)
    {
        return $this->productionStop->delete($request);
    }


    // lOG
    public function reportProductionStop(Request $request)
    {
        return $this->productionStop->reportProductionStop($request);
    }

    public function getProductionLog(Request $request)
    {
        return $this->productionStop->getProductionLog($request);
    }

}
