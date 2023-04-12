<?php

namespace App\Http\Controllers\Production;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Repositories\Production\EquipmentTypeRepository;
use App\Repositories\Production\PresentationRepository;


class PresentationController extends Controller
{
    protected $presentation;
    protected $equipmenttype;

    public function __construct(PresentationRepository $presentation, EquipmentTypeRepository $equipmenttype)
    {
        $this->presentation = $presentation;      
        $this->equipmenttype = $equipmenttype;
    }

    public function index()
    {               
        $breadcrumbs = [
            ['link'=>"/",'name'=> 'Home'],['link'=>"/config-dashboard",'name'=>"Configuration"], ['name'=>"Presentations"]
        ];
           
        $pageConfigs = [
            'pageHeader' => true,            
        ];

        return view('/pages/production/presentation', [
            'pageConfigs' => $pageConfigs,
            'breadcrumbs' => $breadcrumbs,
            'equipmenttypes' => $this->equipmenttype->getList(),
             
        ]);
    }

    public function getAll()
    {
        return $this->presentation->getAll();
    }

    public function getList()
    {
        return $this->presentation->getList();
    }

    public function create(Request $request)
    {
        return $this->presentation->create($request);
    }

    public function update(Request $request)
    {
        return $this->presentation->update($request);
    }

    public function delete(Request $request)
    {
        return $this->presentation->delete($request);
    }

 
}
