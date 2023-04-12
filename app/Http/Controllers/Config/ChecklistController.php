<?php

namespace App\Http\Controllers\Config;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\ChecklistOptionRepository;
use App\Repositories\ChecklistRepository;
use App\Repositories\ItemRepository;
//use App\Repositories\MetricRepository;
use App\Repositories\ChecklistDataRepository;

class ChecklistController extends Controller
{
    protected $checklistoption;
    protected $checklist;
    //protected $metric;
    protected $checklistData;


    public function __construct(ChecklistOptionRepository $checklistoption, CheckListRepository $checklist,  ChecklistDataRepository $checklistData) 
    {
        $this->middleware('auth', ['only' => 'index']);
        
        $this->checklistoption = $checklistoption;
        $this->checklist       = $checklist;
        //$this->metric          = $metric;
        $this->checklistData   = $checklistData;
    }

    public function index()
    {
        $breadcrumbs = [
            ['link'=>"/",'name'=> 'Home'],['link'=>"/config-dashboard",'name'=>"Configuration"], ['name'=>"Checklists"], ['link'=>"/config-checklistoptions",'name'=>"Checklist Options"]
        ];
           
        $pageConfigs = [
            'pageHeader' => true,            
        ];

        return view('/pages/config/checklist/index', [
            'pageConfigs' => $pageConfigs,
            'breadcrumbs' => $breadcrumbs,                     
        ]);
    }

    public function options(Request $request)
    {
        $i = new ItemRepository;
        
        $breadcrumbs = [
            ['link'=>"/",'name'=> 'Home'],['link'=>"/config-dashboard",'name'=>"Configuration"], ['link'=>"/config-checklists",'name'=>"Checklists"],  ['name'=>"Checklist Options"]
        ];
           
        $pageConfigs = [
            'pageHeader' => true,            
        ];

        return view('/pages/config/checklist/options', [
            'pageConfigs'    => $pageConfigs,
            'breadcrumbs'    => $breadcrumbs,
            'checklists'     => $this->checklist->getList(),
            'items'          => $i->getList(),
            'checklistData'  => $this->checklistData->getList(),
            'checklistSelected'  => ($request->has('id') ? $request->id : "null")
        ]);
    }

    /*
    public function metrics()
    {
        $breadcrumbs = [
            ['link'=>"/",'name'=> 'Home'],['link'=>"/config-dashboard",'name'=>"Configuration"], ['link'=>"/config-checklists",'name'=>"Checklists"],['link'=>"/config-checklistoptions",'name'=>"Checklist Options"] ,['name'=>"Metrics"]
        ];
           
        $pageConfigs = [
            'pageHeader' => true,            
        ];

        return view('/pages/config/checklist/metrics', [
            'pageConfigs' => $pageConfigs,
            'breadcrumbs' => $breadcrumbs,                     
        ]);
    }*/

    public function data()
    {
        $breadcrumbs = [
            ['link'=>"/",'name'=> 'Home'],['link'=>"/config-dashboard",'name'=>"Configuration"], ['link'=>"/config-checklists",'name'=>"Checklists"],['link'=>"/config-checklistoptions",'name'=>"Checklist Options"], ['name'=>"Checklist Data"]
        ];
           
        $pageConfigs = [
            'pageHeader' => true,            
        ];

        return view('/pages/config/checklist/data', [
            'pageConfigs' => $pageConfigs,
            'breadcrumbs' => $breadcrumbs,                     
        ]);
    }

    
    public function getAll()
    {
        return $this->checklist->getAll();
    }

    public function getList()
    {
        return $this->checklist->getList();
    }

    public function create(Request $request)
    {
        return $this->checklist->create($request);
    }

    public function update(Request $request)
    {
        return $this->checklist->update($request);
    }

    public function delete(Request $request)
    {
        return $this->checklist->delete($request);
    }

    //////////////////////////////////////////////////////////////////////////
    // CheckList Options
    //////////////////////////////////////////////////////////////////////////

    public function getAllOptions(Request $request)
    {
        return $this->checklistoption->getAll($request->idchecklist);
    }

    public function getListOptions()
    {
        return $this->checklistoption->getList();
    }

    public function createOption(Request $request)
    {        
        return $this->checklistoption->create($request);
    }

    public function updateOption(Request $request)
    {
        return $this->checklistoption->update($request);
    }

    public function deleteOption(Request $request)
    {
        return $this->checklistoption->delete($request);
    }

    // Reorder Options (from grid drag and drop)
    public function reorderOptions(Request $request) 
    {
        return $this->checklistoption->reorderOptions($request);
    }


    // Metrics
/*
    public function getAllMetrics(Request $request)
    {
        return $this->metric->getAll();
    }

    public function getListMetrics()
    {
        return $this->metric->getList();
    }

    public function createMetric(Request $request)
    {        
        return $this->metric->create($request);
    }

    public function updateMetric(Request $request)
    {
        return $this->metric->update($request);
    }

    public function deleteMetric(Request $request)
    {
        return $this->metric->delete($request);
    }*/


    // Checklist Data

    public function getAllData(Request $request)
    {
        return $this->data->getAll();
    }

    public function getListData()
    {
        return $this->data->getList();
    }

    public function createData(Request $request)
    {        
        return $this->data->create($request);
    }

    public function updateData(Request $request)
    {
        return $this->data->update($request);
    }

    public function deleteData(Request $request)
    {
        return $this->data->delete($request);
    }




    // For App

    public function getListApp(Request $request)
    {
        return $this->checklistoption->getListApp(null, $request);
    }

    public function getChecklistDataApp()
    {
        return $this->checklistoption->getChecklistDataApp();
    }
}
