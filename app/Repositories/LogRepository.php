<?php

namespace App\Repositories;
use Illuminate\Support\Facades\DB;
use App\Models\Log;
use App\Enums\LogAction;
use App\Enums\TicketStatus;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class LogRepository
{
    public function getAll($request)
    {
        //$teams = $this->userRepository->getTeams(Auth::id());

        $logs = Log::select(['id', 'action', 'data', 'idticket', 'idstatus', 'iduser', 'created_at'])
                   ->with('ticket:id,code')
                   ->with('user')
                   ->when($request->has('filter'), function ($query) use ($request) {
                        //Filtros del buscador
                        $query->where(function ($q) use ($request) {
                            return $this->applyFilters($q, $request);
                        });
                    });

        $total = $logs->count('id');
        $logs  = $logs->skip($request->skip)->take($request->take)->latest()->get();

        $logs->map(function ($log) use($request){
            $log['description'] = $this->getMessage($log, $request->timezone);
            return $log;
        });

        return array("total" => $total, "data" => $logs);
    }

    private function getMessage($log, $timezone)
    {
        $message = "";

        switch($log->action)
        {
            case "CREATE_TICKET":
                $message = $this->getCreateFormat($log);
                break;

            case "EDIT_TICKET":
                $message = $this->getEditFormat($log, $timezone);
                break;

            case "DELETE_TICKET":
                $message = $this->getDeleteFormat($log);
                break;

            case "CREATE_NOTE":
                $message = $this->getNoteCreateFormat($log);
                break;  

            case "DELETE_NOTE":
                $message = $this->getNoteDeleteFormat($log);
                break; 

            case "USER":
                $message = $this->getUserFormat($log);
                break; 

            case "COPY":
                $message = $this->getCopyFormat($log);
                break; 

            case "TAG":
                $message = $this->getTagFormat($log);
                break; 
            
            case "APPROVER":
                $message = $this->getApproverFormat($log);
                break; 
        }

        return $message;
    }

    private function getUserFormat($log)
    {
        $data = json_decode($log->data);
        $action = $data->action == "attached" ? "añadido" : "removido";
        $icon   = $data->action == "attached" ? "fal fa-user-plus" : "fal fa-user-minus";

        return "<i class='" . $icon . "'></i> Usuario " . $action . "<ul> <li>" . $this->getUserName($data->id) . "</li></ul>";
    }

    private function getApproverFormat($log)
    {
        $data = json_decode($log->data);
        $action = $data->action == "attached" ? "añadido" : "removido";
        $icon   = $data->action == "attached" ? "fal fa-user-plus" : "fal fa-user-minus";

        return "<i class='" . $icon . "'></i> Aprobador " . $action . "<ul> <li>" . $this->getUserName($data->id) . "</li></ul>";
    }

    private function getCopyFormat($log)
    {
        $data = json_decode($log->data);
        $action = $data->action == "attached" ? "añadida" : "removida";
        $icon   = $data->action == "attached" ? "fal fa-copy" : "fal fa-copy";

        return "<i class='" . $icon . "'></i> Copia " . $action . "<ul> <li>" . $this->getUserName($data->id) . "</li></ul>";
    }

    private function getTagFormat($log)
    {
        $data = json_decode($log->data);
        $action = $data->action == "attached" ? "añadida" : "removida";

        return "<i class='fal fa-tag'></i> Etiqueta " . $action . "<ul> <li>" . $this->getConfigName('wh_tag', $data->id) . "</li></ul>";
    }

    private function getCreateFormat($log)
    {
        $data = json_decode($log->data);
        return "<i class='fal fa-clipboard-check'></i> Nueva tarea <ul> <li> <b>Item</b> " . $data->name . "</li> <li> <b>Lugar</b> " . $this->getConfigName('wh_spot', $data->idspot) . "</li> </ul>";
    }

    private function getDeleteFormat($log)
    {
        $data = json_decode($log->data);
        return "<i class='fal fa-trash-alt'></i> Tarea eliminada <ul> <li> <b>Item</b> " . $data->name . "</li> <li> <b>Lugar</b> " . $this->getConfigName('wh_spot', $data->idspot) . "</li> </ul>";
    }
    
    private function getEditFormat($log, $timezone)
    {
        $changes = json_decode($log->data);

        $description = "<i class='fal fa-edit'></i> Se editó tarea <ul>";

        foreach ($changes as $key => $value)
        {
            switch($key)
            {
                case "iditem":
                    $description .= "<li> <b>Item</b> cambió a " . $this->getConfigName("wh_item", $value) . "</li>";
                    break;

                case "idspot":
                    $description .= "<li> <b>Spot</b> cambió a " . $this->getConfigName("wh_spot", $value) . "</li>";
                    break;

                case "idteam":
                    $description .= "<li> <b>Equipo</b> cambió a " . $this->getConfigName("wh_team", $value) . "</li>";
                    break;

                case "idpriority":
                    $description .= "<li> <b>Prioridad</b> cambió a " . $this->getConfigName("wh_ticket_priority", $value) . "</li>";
                    break; 
                    
                case "description":
                    $description .= "<li> <b>Description</b> cambió a " . $value . "</li>";
                    break; 

                case "justification":
                    $description .= "<li> <b>Justificación</b> cambió a " . $value . "</li>";
                    break; 

                case "byclient":
                    $description .= "<li> <b>Reportado por cliente</b> cambió a " . ($value ? "SI" : "NO") . "</li>";
                    break; 
                
                case "files":
                    $description .= "<li>" . $value . "</li>";
                    break; 

                case "approved":
                    $description .= "<li> <b>Aprobado</b> cambió a " . ($value ? "SI" : "NO") . "</li>";
                    break; 

                case "idstatus":
                    $description .= "<li> <b>Estado</b> cambió a " . $this->getConfigName("wh_ticket_status", $value) . "</li>";
                    break;

                case "startdate":
                    $description .= "<li> <b>Fecha de inicio</b> cambió a " . $this->formatDate($value, $timezone) . "</li>";
                    break;

                case "finishdate":
                    $description .= "<li> <b>Fecha de fin</b> cambió a " . $this->formatDate($value, $timezone) . "</li>";
                    break;

                case "duration":
                    $description .= "<li> <b>Duración</b> cambió a " . $value . "</li>";
                    break;

                case "duedate":
                    $description .= "<li> <b>Fecha de vencimiento</b> cambió a " . $this->formatDate($value, $timezone) . "</li>";
                    break;

                case "signature":
                    $description .= "<li> La tarea fue firmada</li>";
                    break;
            }
        }

        return $description . "</ul>";
    }

    private function getNoteCreateFormat($log)
    {
        $data = json_decode($log->data);
        return "<i class='fal fa-comment-alt'></i> Nueva nota <ul> <li>" . $data->note . "</li> </ul>";
    }

    private function getNoteDeleteFormat($log)
    {
        $data = json_decode($log->data);
        return "<i class='fal fa-comment-alt-slash'></i> Nota eliminada<ul> <li>" . $data->note . "</li> </ul>";
    }
    
    private function getConfigName($table, $id)
    {
        return DB::table($table)->where('id', $id)->first()->name;
    }

    private function getUserName($id)
    {
        $user = DB::table('wh_user')->select(DB::raw('CONCAT(firstname," ",lastname) AS fullname'))->where('id', $id)->first();
        return $user->fullname;
    }

    private function formatDate($date, $timezone)
    {
        if(is_null($date)) return "_______?";

        $date = Carbon::createFromFormat('Y-m-d\TH:i:s.uZ', $date, 'UTC');
        $date->setTimezone($timezone);
        return $date->toDateTimeString();
    }

    private function applyFilters($model, $request)
    {
        $logic   = $request->filter['logic'];
        $filters = $request->filter['filters'];

        foreach($filters as $filter)
        {
            switch ($filter['operator'])
            {
                case "eq":

                    $model->when($logic == "and", function ($query) use ($filter) {
                        return $query->where($filter['field'], $filter['value']);
                    }, function ($query) use ($filter) {
                        return $query->orWhere($filter['field'], $filter['value']);
                    });
                    break;

                case "contains":

                    $model->when($logic == "and", function ($query) use ($filter) {
                        return $query->where($filter['field'], 'like', '%' . $filter['value'] . '%');
                    }, function ($query) use ($filter) {
                        return $query->orWhere($filter['field'], 'like', '%' . $filter['value'] . '%');
                    });
                    break;
            }
        }

        return $model;
    }
    
    public function register($action, $ticket, $iduser, $data = null)
    {
        switch($action)
        {
            case LogAction::Login:

                $log =["action" => $action, "data" => json_encode($data), "idticket" => null, "iduser" => $iduser];
                break;

            case LogAction::CreateTicket:

                $data = $ticket->only("id", "code", "name", "iditem", "idteam", "idstatus", "idpriority", "idspot", "description", "byclient");
                $log =["action" => $action, "data" => json_encode($data), "idticket" => $ticket->id, "idstatus" => TicketStatus::Pending, "iduser" => $iduser];
                break;

            case LogAction::EditTicket:

                $data = collect($data)->except('updated_at');
                $idstatus = $data->has('idstatus') ? $data['idstatus'] : null;

                $log = ["action" => $action, "data" => json_encode($data), "idticket" => $ticket->id, "iduser" => $iduser, 'idstatus' => $idstatus];
                break;

            case LogAction::DeleteTicket:

                $data = $ticket->only("id","name", "iditem", "idteam", "idstatus", "idpriority", "idspot", "description", "byclient");
                $log =["action" => $action, "data" => json_encode($data), "idticket" => $ticket->id, "idstatus" => $ticket->idstatus, "iduser" => $iduser];
                break;

            case LogAction::CreateNote:

                $result = $data->only("note", "type", "idticket");
                $log =["action" => $action, "data" => json_encode($result), "idticket" => $data->idticket, "iduser" => $iduser];
                break;
            
            case LogAction::DeleteNote:

                $result = $data->only("note", "type", "idticket");
                $log = ["action" => $action, "data" => json_encode($result), "idticket" => $data->idticket, "iduser" => $iduser];
                break;

            default:
                $log = ["action" => $action, "data" => json_encode($data), "idticket" => $ticket->id, "iduser" => $iduser];
                break;
        }

        Log::create($log);
    }

    public function getTaskLogsApp($request)
    {
        $logs = Log::select(['id', 'action', 'data', 'idticket', 'idstatus', 'iduser', 'created_at'])
                    ->with('user:id,firstname,lastname,urlpicture')
                    ->where('idticket', $request->idtask)
                    ->get();

        $logs->map(function ($log) use($request) {

            $description = $this->getMessage($log, $request->timezone);
            $description = str_replace("<li>","\n",$description);
            $log['description'] = strip_tags($description);

            return $log;
        });

        return $logs;
    }
}