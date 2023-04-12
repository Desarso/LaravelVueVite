<?php

namespace App\Http\Controllers\Production;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\Production\ProductRepository;
use App\Repositories\Production\EquipmentTypeRepository;
use App\Repositories\Production\ProductCategoryRepository;
use App\Repositories\Production\ProductDestinationRepository;
use App\Repositories\Production\PresentationRepository;
use App\Repositories\Production\ProductionFormulaRepository;


class ProductController extends Controller
{
    protected $equipmentType;
    protected $productCategory;
    protected $productDestination;
    protected $presentation;
    protected $formula;

    public function __construct(ProductRepository $product, EquipmentTypeRepository $equipmentType, 
                            ProductCategoryRepository $productCategory,
                            ProductDestinationRepository $productDestination,
                            PresentationRepository $presentation,
                            ProductionFormulaRepository $formula )
    {
        $this->product = $product;
        $this->equipmentType = $equipmentType;
        $this->productCategory = $productCategory;
        $this->productDestination = $productDestination;
        $this->presentation = $presentation;
        $this->formula = $formula;
    }

    public function index()
    {               
        $breadcrumbs = [
            ['link'=>"/",'name'=> 'Home'],['link'=>"/config-dashboard",'name'=>"Configuration"], ['name'=>"Products"]
        ];
           
        $pageConfigs = [
            'pageHeader' => true,            
        ];

        return view('/pages/production/products', [
            'pageConfigs' => $pageConfigs,
            'breadcrumbs' => $breadcrumbs,
            'equipmenttypes' => $this->equipmentType->getList(),
            'productcategories' => $this->productCategory->getList(),
            'productdestinations' => $this->productDestination->getList(),
            'presentations' => $this->presentation->getList(),
            'formulas' => $this->formula->getList()
        ]);
    }

    public function getAll()
    {
        return $this->product->getAll();
    }

    public function getList()
    {
        return $this->product->getList();
    }

    public function create(Request $request)
    {
        return $this->product->create($request);
    }

    public function update(Request $request)
    {
        return $this->product->update($request);
    }

    public function delete(Request $request)
    {
        return $this->product->delete($request);
    }
}
