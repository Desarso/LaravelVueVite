<?php

namespace App\Repositories;
use Illuminate\Support\Facades\DB;
use App\Models\ChecklistOption;
use Carbon\Carbon;
use App\Models\Checklist;
use App\Models\ChecklistData;
use Illuminate\Http\Request;

class ChecklistOptionRepository
{
    public function getChecklistCopy($idchecklist, $idticket)
    {
        $options = DB::table('wh_checklist_option')
                     ->where('idchecklist', $idchecklist)
                     ->where('enabled', 1)
                     ->whereNull('idparent')
                     ->get(['id as idchecklistoption', 'name', 'optiontype', 'group', 'position', 'iddata', 'isgroup', 'iditem', 'idspot', 'departments', 'idasset', 'properties', 'showinreport']);

        foreach ($options as $option)
        {
            // $option->idticket  = $idticket;
            $option->value     = $this->getDefaultValue($option);
            $option->approved  = "null";
        }

        $results = json_encode(["total" => $options->where('optiontype', '!=', 6)->count(), "si" => 0, "no" => 0]);

        return ["idchecklist" => $idchecklist, 'options' => $options->toJson(), 'results' => $results];
    }

    public function getDefaultValue($option)
    {
        $default = "";

        switch($option->optiontype)
        {
            case 1:
                //CHECK
                $default = "0";
                break;

            case 3:
                //TEXT
                $default = "";
                break;

            case 11:
                //IMAGE
                $properties = json_decode($option->properties);
                $default = $properties->value;
                break;

            case 14:
                $default = 1;
                break;

            default:
                $default = null;
        }

        return $default;
    }
    
    public function getAll($idchecklist = null)
    {
        $data = ChecklistOption::when($idchecklist, function ($query, $idchecklist) {
                                    return $query->where('idchecklist', $idchecklist);
                                })->orderBy('position', 'ASC')->get();  
        
        $data->map(function ($item){
            $item->departments = (is_null($item->departments) ? [] : array_map(array($this, 'formatTeams'), json_decode($item->departments)));
            return $item;
        });

        return $data;
    }     

    public function getList()
    {        
        return ChecklistOption::where('enabled', true)->get(['id as value', 'name as text']);
    }

    public function create($request)
    {     
        $departments = $this->pluckTeams($request->departments);
        $request->merge(['departments' => json_encode($departments)]);

        //$this->applyReordering($request);
         $result = ChecklistOption::create($request->all());
         $result->departments = array_map(array($this, 'formatTeams'), $departments);
         $this->_reorderOptions($request->idchecklist);
         return $result;
    }

    // reorder after creating new option
    private function applyReordering($request) 
    {    
        if ($request->reorder == 'true') {
            CheckListOption::where('position', '>=', $request->position)
                ->where('idchecklist','=', $request->idchecklist)
                ->increment('position', 1);              
                }
    }

    // reorder after grid drag and drop of rows
    public function reorderOptions($request) 
    {        
        $rows = json_decode($request->data);
        $position = 1;
        $group = null;
        foreach ($rows as $row) {
            if ($row->isgroup == true) $group = ($group == null ? 1 : $group+ 1);            
            $opt = ChecklistOption::find($row->id);
            $opt->position = $position;
            $opt->group = $group;            
            $opt->save();
            $position++;
        } 
    }

    private function _reorderOptions($idchecklist) {
        $rows = DB::table('wh_checklist_option')->where('idchecklist',$idchecklist)->orderBy('position','ASC')->select('id','isgroup')->get();
        $this->reorderOptions(new Request([
            'data'   => json_encode($rows)            
        ]));
    }

    public function update($request)
    {
        $departments = $this->pluckTeams($request->departments);
        $request->merge(['departments' => json_encode($departments)]);
        $model = ChecklistOption::find($request->id);
        $model->fill($request->all())->save();
        $model->departments = array_map(array($this, 'formatTeams'), $departments);
        $this->_reorderOptions($model->idchecklist);
        
        return $model;
    }

    private function pluckTeams($teams)
    {
        $teams = collect($teams)->pluck('value')->toArray();
        $teams = array_map('intval', $teams);
        return $teams;
    }

    private function formatTeams($idteam)
    {
        $team = new \stdClass;
        $team->value = $idteam;
        return $team;
    }

    public function delete($request)
    {        
        $model = ChecklistOption::findOrFail($request->id);
        $model->delete();
        $this->_reorderOptions($model->idchecklist);
        
    }

    public function getListApp($updated_at = null, $request = null)
    {
        $columns_with = 'checklistoptions:id,name,optiontype,group,position,iddata,isgroup,iditem,idspot,departments,idasset,idchecklist,properties,showinreport,idparent';
        $columns = ['id', 'name', 'deleted_at'];


        if(isset($request->version)) {
            if ($request->version >= 411) { 
                $newColumns = ['collapse'];
                $columns = array_merge($columns, $newColumns);
            }
        }

        return Checklist::with($columns_with)
                        ->select($columns)
                        ->when(!is_null($updated_at), function ($query) use ($updated_at){
                            return $query->withTrashed()
                                         ->where('updated_at', '>', $updated_at);
                        })
                        ->get();
    }

    public function getChecklistDataApp($updated_at = null)
    {
        return DB::table('wh_checklist_data')
                ->select('id', 'name', 'data')
                ->when(!is_null($updated_at), function ($query) use ($updated_at){
                    return $query->where('updated_at', '>', $updated_at);
                })
                ->get();
    }
}