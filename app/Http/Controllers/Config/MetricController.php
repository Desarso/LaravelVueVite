<?php

namespace App\Http\Controllers\Config;

use App\Http\Controllers\Controller;
use App\Models\App;
use Illuminate\Http\Request;
use App\Repositories\MetricRepository;


class MetricController extends Controller
{
    protected $metric;

    public function __construct(MetricRepository $metric)
    {
        $this->middleware('auth', ['only' => 'index']);
        $this->metric = $metric;
    }

    public function index()
    {        
       
        $breadcrumbs = [
            ['link'=>"/",'name'=> 'Home'],['link'=>"/config-dashboard",'name'=>"Configuration"], ['link'=>"/config-checklists",'name'=>"Checklists"],['link'=>"/config-checklistoptions",'name'=>"Checklist Options"] ,['name'=>"Metrics"]
        ];
           
        $pageConfigs = [
            'pageHeader' => true,            
        ];

        return view('/pages/config/checklist/metrics', [
            'pageConfigs' => $pageConfigs,
            'breadcrumbs' => $breadcrumbs
        ]);
    }

    
    public function getAll()
    {
        return $this->metric->getAll();
    }

    public function getList()
    {
        return $this->metric->getList();
    }

    public function create(Request $request)
    {
        return $this->metric->create($request);
    }

    public function update(Request $request)
    {
        return $this->metric->update($request);
    }

    public function delete(Request $request)
    {
        return $this->metric->delete($request);
    }


}
