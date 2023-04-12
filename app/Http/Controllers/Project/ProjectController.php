<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\Project\ProjectRepository;
use App\Repositories\TicketStatusRepository; 


class ProjectController extends Controller
{
    protected $project;
    protected $status;

    public function __construct(ProjectRepository $project, TicketStatusRepository $status)
    {
        $this->middleware('auth', ['only' => 'index']);
        $this->project = $project;
        $this->status = $status;
    }

    public function dashboard()
    {        
       
        $breadcrumbs = [
            ['link'=>"/",'name'=> 'Home'], ['name'=>"Projects"]
        ];
           
        $pageConfigs = [
            'pageHeader' => true,    
            'verticalMenuNavbarType' => 'sticky',
                              
        ];

        return view('/pages/project/index', [
            'pageConfigs' => $pageConfigs,
            'breadcrumbs' => $breadcrumbs,
            'statuses'    => $this->status->getList()  

        ]);
    }

       

        public function dashboardGantt()
        {        
           
            $breadcrumbs = [
                ['link'=>"/",'name'=> 'Home'], ['name'=>"Projects"]
            ];
               
            $pageConfigs = [
                'pageHeader' => true,    
                'verticalMenuNavbarType' => 'sticky',
                
            ];
    
            return view('/pages//project/dashboard-gantt', [
                'pageConfigs' => $pageConfigs,
                'breadcrumbs' => $breadcrumbs
            ]);
        }
    
    

 
    public function getAll()
    {
        return $this->project->getAll();
    }

    public function getList()
    {
        return $this->project->getList();
    }

   

    public function create(Request $request)
    {
        return $this->project->create($request);
    }

    public function update(Request $request)
    {
        return $this->project->update($request);
    }

    public function delete(Request $request)
    {
        return $this->project->delete($request);
    }




  
    
}
