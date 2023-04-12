<?php

namespace App\Repositories\Cleaning;
use Illuminate\Support\Facades\DB;
use App\Models\Cleaning\CleaningLog;
use App\Enums\CleaningLog as CleaningLogAction;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Session;

class CleaningLogRepository
{
    public function getAll($request)
    {
        $logs = CleaningLog::select(['id', 'action', 'idspot', 'iduser', 'data', 'created_at'])
                           ->when($request->has('filter') && !is_null($request->filter), function ($query) use ($request) {
                                //Filtros del buscador
                               $query->where(function ($q) use ($request) {
                                    return $this->applyFilters($q, $request);
                               });
                           });

        $total = $logs->count('id');
        $logs  = $logs->skip($request->skip)->take($request->take)->latest()->get();


        $logs->map(function ($log){
            $log['description'] = $this->getMessage($log);
            return $log;
        });

        return array("total" => $total, "data" => $logs);
    }

    private function applyFilters($model, $request)
    {
        $filters = $request->filter['filters'];

        foreach($filters as $filter)
        {
            if(property_exists($filter, 'filters'))
            {
                foreach($filter['filters'] as $f)
                {
                    $model->orWhere($f['field'], $f['value']);
                }
            }
            else
            {
                $model->orWhere($filter['field'], $filter['value']);
            }
        }

        return $model;
    }

    private function getMessage($log)
    {
        $message = "";

        switch($log->action)
        {
            case "CREATE_PLAN":
                $message = $this->getCreateFormat($log);
                break;
            
            case "EDIT_PLAN":
                $message = $this->getEditFormat($log);
                break;

            case "DELETE_PLAN":
                $message = $this->getDeleteFormat($log);
                break;
        }

        return $message;
    }

    private function getCreateFormat($log)
    {
        $data = json_decode($log->data);
        return "<i class='fal fa-broom'></i> Nueva limpieza <ul> <li> <b>Tipo de limpieza</b> " . $this->getConfigName('wh_item', $data->iditem) .  "</li> <li> <b>Responsable</b> " . $this->getUserName($data->iduser) . "</li>  <li> <b>Fecha de limpieza</b> " . $this->formatDate($data->date) . "</li> </ul>";
    }

    private function getDeleteFormat($log)
    {
        $data = json_decode($log->data);
        return "<i class='fal fa-trash-alt'></i> Limpieza eliminada <ul> <li> <b>Tipo de limpieza</b> " . $this->getConfigName('wh_item', $data->iditem) . "</li> <li> <b>Responsable</b> " . $this->getUserName($data->iduser) . "</li> </ul>";
    }

    private function getEditFormat($log)
    {
        $changes = json_decode($log->data);

        $description = "<i class='fal fa-edit'></i> Se editó plan de limpieza <ul>";

        foreach ($changes as $key => $value)
        {
            switch($key)
            {
                case "iduser":
                    $description .= "<li> <b>Responsable</b> cambió a " . $this->getUserName($value) . "</li>";
                    break;

                case "idspot":
                    $description .= "<li> <b>Spot</b> cambió a " . $this->getConfigName("wh_spot", $value) . "</li>";
                    break;
    
                case "iditem":
                    $description .= "<li> <b>Tipo de limpieza</b> cambió a " . $this->getConfigName("wh_item", $value) . "</li>";
                    break; 
                    
                case "cleanat":
                    $description .= "<li> <b>Hora de limpieza</b> cambió a " . $this->formatHour($value) . "</li>";
                    break; 
                
                case "startdate":
                    $description .= "<li> <b>Fecha de inicio</b> cambió a " . $this->formatDate($value) . "</li>";
                    break; 

                case "finishdate":
                    $description .= "<li> <b>Fecha de finalización</b> cambió a " . $this->formatDate($value) . "</li>";
                    break; 

                case "duration":
                    $description .= "<li> <b>Duración</b> cambió a " . $value . "</li>";
                    break;        

                case "idcleaningstatus":
                    $description .= "<li> <b>Estado</b> cambió a " . $this->getConfigStatus($value) . "</li>";
                    break;   
            }
        }

        return $description . "</ul>";
    }

    private function getConfigStatus($id)
    {
        $status = DB::table("wh_cleaning_status")->where('id', $id)->first();

        return "<div class='badge badge-pill badge-danger' style='background-color: " . $status->background . ";'>" . $status->name . "</div>";
    }

    private function getConfigName($table, $id)
    {
        return DB::table($table)->where('id', $id)->first()->name;
    }

    private function getUserName($id)
    {
        $user = DB::table('wh_user')->select(DB::raw('CONCAT(firstname," ",lastname) AS fullname'))->where('id', $id)->first();
        return is_null($user) ? "----" : $user->fullname;
    }

    private function formatDate($date)
    {
        if(is_null($date)) return "_______?";

        $date = Carbon::createFromFormat('Y-m-d\TH:i:s.uZ', $date, 'UTC');
        $date->setTimezone(Session::get('local_timezone'));
        return $date->toDateTimeString();
    }

    private function formatHour($date)
    {
        if(is_null($date)) return "_______?";

        $date = Carbon::createFromFormat('Y-m-d\TH:i:s.uZ', $date, 'UTC');
        $date->setTimezone(Session::get('local_timezone'));
        return $date->toTimeString();
    }

    public function register($action, $model)
    {
        $model->makeHidden(['updated_at', 'created_at']);

        $iduser = (Auth::check()) ?  Auth::id() : session('iduser');

        switch($action)
        {
            case CleaningLogAction::CreatePlan:

                $log = ["action" => $action, "data" => $model->toJson(), "idspot" => $model->idspot, "iduser" => $iduser];
                break;

            case CleaningLogAction::EditPlan:
                
                $changes = $model->getChanges();
                unset($changes['updated_at']);
                $log = ["action" => $action, "data" => json_encode($changes), "idspot" => $model->idspot, "iduser" => $iduser];
                break;

            case CleaningLogAction::DeletePlan:

                $log = ["action" => $action, "data" => $model->toJson(), "idspot" => $model->idspot, "iduser" => $iduser];
                break;
        }

        CleaningLog::create($log);
    }
}