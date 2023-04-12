<?php

namespace App\Http\Controllers\Production;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\Production\ProductionFormulaRepository;
use App\Repositories\Production\ProductionInputRepository;


class ProductionFormulaController extends Controller
{
    protected $formula;
    protected $input;
    
    public function __construct(
        ProductionFormulaRepository $formula,
        ProductionInputRepository $input
        )
    {
        $this->formula = $formula;
        $this->input = $input;
        
    }

    public function index()
    {               
        $breadcrumbs = [
            ['link'=>"/",'name'=> 'Home'],['link'=>"/config-dashboard",'name'=>"Configuration"], ['name'=>"Production Formulas"]
        ];
           
        $pageConfigs = [
            'pageHeader' => true,            
        ];

        return view('/pages/production/production-formula', [
            'pageConfigs' => $pageConfigs,
            'breadcrumbs' => $breadcrumbs,
            'inputs'       => $this->input->getList()                        
        ]);
    }

    public function getAll()
    {
        return $this->formula->getAll();
    }

    public function getList()
    {
        return $this->formula->getList();
    }

    public function create(Request $request)
    {
        return $this->formula->create($request);
    }

    public function update(Request $request)
    {
        return $this->formula->update($request);
    }

    public function delete(Request $request)
    {
        return $this->formula->delete($request);
    }
 

}
