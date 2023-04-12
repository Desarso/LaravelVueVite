<?php

namespace App\Repositories\Project;

use Illuminate\Support\Facades\DB;
use App\Models\Project;
use Session;
use Carbon\Carbon;

class ProjectRepository
{
    public function getAll()
    {
        $projects =  Project::whereNull('deleted_at')->orderBy('order')->get(['id', 'name',  'description', 'idstatus', 'progress', 'start','end','users', 'isprivate']);

        $projects->map(function ($project){
            $project->users = array_map(array($this, 'formatUsers'), json_decode($project->users));
            return $project;
        });

        return $projects;
    
    }

    private function formatUsers($iduser)
    {
        $user = new \stdClass;
        $user->value = $iduser;
        return $user;
    }

    public function getList()
    {
        return DB::table('wh_project')->get(['id as value', 'name as text', 'isprivate']);
    }

    public function getProjectName($id) 
    {
        $p = Project::find($id);
        return $p->name;
    }

 
    public function create($request)
    {
        //return Project::create($request->all());
        $users = $this->pluckUsers($request->users);
        $request->merge(['users' => json_encode($users)]);
        $project = Project::create($request->all());
        $project->users = array_map(array($this, 'formatUsers'), $users);
        return $project;
    }

    private function pluckUsers($users)
    {
        $users = collect($users)->pluck('value')->toArray();
        $users = array_map('intval', $users);
        return $users;
    }

    public function update($request)
    {
        $users = $this->pluckUsers($request->users);
        $request->merge(['users' => json_encode($users)]);

        $model = Project::find($request->id);
        $model->fill($request->all())->save();
        $model->users = array_map(array($this, 'formatUsers'), $users);
        return $model;
    }

    public function delete($request)
    {
        $model = Project::findOrFail($request->id);
        $model->delete();
    }
}
