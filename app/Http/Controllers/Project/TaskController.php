<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\Project\TaskRepository;
use App\Repositories\Project\ProjectRepository;
 
 

class TaskController extends Controller
{
    protected $task;
    protected $project;

    public function __construct(TaskRepository $task, ProjectRepository $project)
    {
        $this->middleware('auth', ['only' => 'index']);
        $this->task = $task;
        $this->project = $project;
    }

    public function gantt(Request $request)
    {               

        $projectname = $this->project->getProjectName($request->id);
        
        $breadcrumbs = [            
            ['link'=>"/",'name'=> 'Home'],['link'=>"/projects",'name'=>"Projects"],['name'=> $projectname]
        ];
           
        $pageConfigs = [
            'pageHeader' => true,    
            'verticalMenuNavbarType' => 'sticky',
                              
        ];

        return view('/pages/project/gantt', [
            'pageConfigs' => $pageConfigs,
            'breadcrumbs' => $breadcrumbs
        ]);
    }


 
    public function getGanttData(Request $request)
    {
        return $this->task->getGanttData($request);
    }

    public function store(Request $request)
    {
        return $this->task->store($request);        
    }
 
    public function update($id, Request $request)
    {
        return $this->task->update($id, $request);     
    }
 
    public function destroy($id)
    {
        return $this->task->destroy($id);        
    }
   
   
 


 
    
}
