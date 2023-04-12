<?php

namespace App\Repositories\Project;

use Illuminate\Support\Facades\DB; 
use App\Models\Task;
use App\Models\Link;
use Session;
use Carbon\Carbon;

class TaskRepository
{
    
    
 
    public function getGanttData($request){

        $tasks = new Task();
        $links = new Link();

        return response()->json([
            "data" => $tasks->orderBy('sortorder')->where('idproject',$request->id)->get(
                [
                'id', 
                'name as text', 
                'duration',
                'progress',
                'start as start_date',
                'parent',
                'tasktype as type'
                ]),

            "links" => $links->all()
        ]);

    }


    public function store($request){
 
        $task = new Task();
 
        $task->name = $request->text;
        $task->start = $request->start_date;
        $task->duration = $request->duration;
        $task->progress = $request->has("progress") ? $request->progress : 0;
        $task->parent = $request->parent;
        $task->tasktype = $request->type;
        $task->sortorder = Task::max("sortorder") + 1;

          // need to send the project
          $task->idproject = 1;

          // defaults
          $task->idteam = 1;
          $task->iditem = 999999;
          $task->idspot = 999999;
          $task->created_by = 1800;  // current user
 
        $task->save();
 
        return response()->json([
            "action"=> "inserted",
            "tid" => $task->id
        ]);
    }
 
    public function update($id, $request){
        $task = Task::find($id);
 
        $task->name = $request->text;
        $task->start = $request->start_date;
        $task->duration = $request->duration;
        $task->progress = $request->has("progress") ? $request->progress : 0;
        $task->parent = $request->parent;
        $task->tasktype = $request->type;
        if($request->has("target")){
            $this->updateOrder($id, $request->target);
        }

      
 
        $task->save();
 
        return response()->json([
            "action"=> "updated"
        ]);
    }

    private function updateOrder($taskId, $target){
        $nextTask = false;
        $targetId = $target;
     
        if(strpos($target, "next:") === 0){
            $targetId = substr($target, strlen("next:"));
            $nextTask = true;
        }
     
        if($targetId == "null")
            return;
     
        $targetOrder = Task::find($targetId)->sortorder;
        if($nextTask)
            $targetOrder++;
     
        Task::where("sortorder", ">=", $targetOrder)->increment("sortorder");
     
        $updatedTask = Task::find($taskId);
        $updatedTask->sortorder = $targetOrder;
        $updatedTask->save();
    }
    
 
    public function destroy($id){
        $task = Task::find($id);
        $task->delete();
 
        return response()->json([
            "action"=> "deleted"
        ]);
    }

    
}
