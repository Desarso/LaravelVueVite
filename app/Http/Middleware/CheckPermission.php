<?php

namespace App\Http\Middleware;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Ticket;
use App\Models\TicketUser;
use App\Models\Item;
use Closure;
use App\Enums\TicketStatus;
use App\Enums\App;

class CheckPermission
{
    public function handle($request, Closure $next)
    {
        $permission = $this->checkPermission($request);

        if(!$permission["success"])
        {
            return response()->json($permission);
        }

        return $next($request);
    }

    private function checkPermission($request)
    {
        $resutl = [];

        switch($request->action)
        {
            case 'delete':

                if($this->getPermission($request) == true || $this->checkIfUserCreateTicket($request) == true)
                {
                    $result = ['success' => true];
                }
                else
                {
                    $result = ['success' => false, 'message' => 'Permiso Denegado'];
                }

                break;

            case 'create':

                $item = DB::table('wh_item')->where('id', $request->iditem)->first(['id', 'idteam', 'isprivate']);

                if($item->isprivate == true)
                {
                    $teams = DB::table('wh_user_team')->where('iduser',  Auth::id())->pluck('idteam')->toArray();

                    if(in_array($item->idteam, $teams))
                    {
                        $result = ['success' => true];
                    }
                    else
                    {
                        $result = ['success' => false, 'message' => 'El ítem es privado'];
                    }
                }
                else
                {
                    $result = ['success' => true];
                }

                break;

            case 'verify':

                if($this->getPermission($request) == true)
                {
                    $ticket = Ticket::find($request->idticket, ['idstatus']);

                    if($ticket->idstatus == TicketStatus::Finished)
                    {
                        $result = ['success' => true];
                    }
                    else
                    {
                        $result = ['success' => false, 'message' => 'La tarea no está finalizada'];
                    }
                }
                else
                {
                    $result = ['success' => false, 'message' => 'Permiso Denegado'];
                }

                break;

            case 'editchecklist':

                $ticket = Ticket::find($request->idticket);

                switch ($ticket->idstatus)
                {
                    case TicketStatus::Progress:
                        //$result = ['success' => true, 'message' => 'Puede editar checklist'];
                        $result = $ticket->users->where('id',Auth::id())->isEmpty() ? ['success' => false, 'message' => 'No eres usuario de la tarea'] : ['success' => true];
                        break;

                    case TicketStatus::Finished:
                        $request->merge(['action' => 'editfinished']);
                        $result = ['success' => $this->getPermission($request), 'message' => 'Permiso Denegado'];
                        break;
                    
                    default:
                        $result = ['success' => false, 'message' => 'La tarea no está en progreso'];
                        break;
                }

                break;
            
            case 'changestatus':

                if($this->getPermission($request) == true || $this->checkIfUserExistInTicket($request) == true)
                {
                    if($request->idstatus != TicketStatus::Progress || $request->has("confirmPause"))
                    {
                        $result = ['success' => true];
                        break;
                    }

                    $request->merge(['action' => 'multitask']);
                    $isMultitask = $this->getPermission($request);

                    if($isMultitask)
                    {
                        $result = ['success' => true];
                    }
                    else
                    {
                        $inProgress = DB::table('wh_ticket as t')
                                        ->join('wh_ticket_user as tu', 'tu.idticket', '=', 't.id')
                                        ->where('tu.iduser', Auth::id())
                                        ->where('t.idstatus', TicketStatus::Progress)
                                        ->exists();

                        if($inProgress)
                        {
                            $result = ['success' => false, 'message' => 'Ya tienes una tarea en progreso', 'confirm' => true];
                        }
                        else
                        {
                            $result = ['success' => true];
                        }
                    }
                }
                else
                {
                    $result = ['success' => false, 'message' => 'Permiso Denegado'];
                }
                
                break;

            case 'changecleaningstatus':

                $settings = DB::table('wh_app')->where('id', App::Cleaning)->first()->settings;
                $settings = json_decode($settings);
                $teams = DB::table('wh_user_team')->where('iduser',  Auth::id())->pluck('idteam')->toArray();
                $result = array_intersect($teams, $settings->cleaning_teams);

                if(count($result) > 0)
                {
                    $idteam = $settings->cleaning_teams[0];
                    $permission = DB::table('wh_user_team as rt')
                        ->join('wh_role as r', 'r.id', '=', 'rt.idrole')
                        ->where('rt.iduser', Auth::id())
                        ->where('rt.idteam', $idteam)
                        ->where('r.permissions->' . $request->action, 'true')
                        ->exists();

                    if($permission)
                    {
                        $result = ['success' => true];
                    }
                    else
                    {
                        $result = ['success' => false, 'message' => 'Permiso Denegado'];
                    }
                }
                else
                {
                    $result = ['success' => false, 'message' => 'Permiso Denegado'];
                }

                return $result;
                break;

                case 'createconfig':

                    $model = DB::table('wh_user_team')->where('iduser', Auth::id())->where('core_team', 1)->first();

                    if(!is_null($model))
                    {
                        $request->merge(['core_team' => $model->idteam]);

                        if($this->getPermission($request) == true)
                        {
                            $result = ['success' => true];
                        }
                        else
                        {
                            $result = ['success' => false, 'message' => 'Permiso Denegado'];
                        }
                    }
                    else
                    {
                        $result = ['success' => false, 'message' => 'Permiso Denegado'];
                    }
    
                    break;

                case 'approveovertime':

                        $model = DB::table('wh_user_team')->where('iduser', Auth::id())->where('core_team', 1)->first();
    
                        if(!is_null($model))
                        {
                            $request->merge(['core_team' => $model->idteam]);
    
                            if($this->getPermission($request) == true)
                            {
                                $result = ['success' => true];
                            }
                            else
                            {
                                $result = ['success' => false, 'message' => 'Permiso Denegado'];
                            }
                        }
                        else
                        {
                            $result = ['success' => false, 'message' => 'Permiso Denegado'];
                        }
        
                        break;
            default:
                $result = ['success' => $this->getPermission($request), 'message' => 'Permiso Denegado'];
                break;
        }

        return $result;
    }

    private function getPermission($request)
    {
        $idteam = null;

        if(isset($request->idticket))
        {
            $idteam = Ticket::find($request->idticket)->idteam;
        }
        else if($request->has("core_team"))
        {
            $idteam = $request->core_team;
        }
        else
        {
            $idteam = Item::find($request->iditem)->idteam;
        }

        $permission = DB::table('wh_user_team as rt')
                        ->join('wh_role as r', 'r.id', '=', 'rt.idrole')
                        ->where('rt.iduser', Auth::id())
                        ->where('rt.idteam', $idteam)
                        ->where('r.permissions->' . $request->action, 'true')
                        ->exists();

        return ($permission ? true : false);
    }

    private function checkIfUserExistInTicket($request)
    {
        $exist = TicketUser::where('idticket', $request->idticket)->where('iduser', Auth::id())->first();

        return (is_null($exist) ? false : true);
    }

    private function checkIfUserCreateTicket($request)
    {
        $exist = Ticket::where('id', $request->idticket)->where('created_by', Auth::id())->first();

        return (is_null($exist) ? false : true);
    }
}
