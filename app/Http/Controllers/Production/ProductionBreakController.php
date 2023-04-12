<?php

namespace App\Http\Controllers\Production;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Repositories\Production\ProductionBreakRepository;


class ProductionBreakController extends Controller
{
    
    protected $break;

    public function __construct(ProductionBreakRepository $break)
    {
        
        $this->break = $break;
    }

    public function index()
    {               
        $breadcrumbs = [
            ['link'=>"/",'name'=> 'Home'],['link'=>"/config-dashboard",'name'=>"Configuration"], ['name'=>"Production Breaks"]
        ];
           
        $pageConfigs = [
            'pageHeader' => true,            
        ];

        return view('/pages/production/production-break', [
            'pageConfigs' => $pageConfigs,
            'breadcrumbs' => $breadcrumbs,            
        ]);
    }

    public function getAll()
    {
        return $this->break->getAll();
    }

    public function getList()
    {
        return $this->break->getList();
    }

    public function create(Request $request)
    {
        return $this->break->create($request);
    }

    public function update(Request $request)
    {
        return $this->break->update($request);
    }

    public function delete(Request $request)
    {
        return $this->break->delete($request);
    }

 
}
