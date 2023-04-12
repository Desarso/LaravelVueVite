<?php

namespace App\Http\Controllers\Production;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\Production\ProductionInputRepository;
use App\Repositories\Production\ProductCategoryRepository;
use App\Repositories\Production\ProductionStopRepository;


class ProductionInputController extends Controller
{
    protected $input;
    protected $productcategory;
    protected $productionstop;
    
    public function __construct(
        ProductionInputRepository $input,
        ProductCategoryRepository $productcategory,
        ProductionStopRepository $productionstop
        )
    {
        $this->input = $input;        
        $this->productcategory = $productcategory;
        $this->productionstop = $productionstop;
    }

    public function index()
    {               
        $breadcrumbs = [
            ['link'=>"/",'name'=> 'Home'],['link'=>"/config-dashboard",'name'=>"Configuration"], ['name'=>"Production Inputs"]
        ];
           
        $pageConfigs = [
            'pageHeader' => true,            
        ];

        return view('/pages/production/production-input', [
            'pageConfigs' => $pageConfigs,
            'breadcrumbs' => $breadcrumbs,
            'categories'  => $this->productcategory->getList(),
            'stops'       => $this->productionstop->getList()  
            //'productcategories' => $this->productCategory->getList(),
            
        ]);
    }

    public function getAll()
    {
        return $this->input->getAll();
    }

    public function getList()
    {
        return $this->input->getList();
    }

    public function create(Request $request)
    {
        return $this->input->create($request);
    }

    public function update(Request $request)
    {
        return $this->input->update($request);
    }

    public function delete(Request $request)
    {
        return $this->input->delete($request);
    }


 
}
