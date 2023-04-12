<?php

namespace App\Repositories;
use Illuminate\Support\Facades\DB;
use App\Models\Ticket;
use App\Models\TicketNote;
use App\Models\TicketChecklist;
use App\Models\User;
use Carbon\Carbon;
use App\Enums\TicketStatus;
use App\Enums\LogAction;
use App\Enums\TicketNoteTypes;
use Illuminate\Support\Facades\Log as LavavelLog;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use App\Events\TicketCreated;
use App\Events\TicketApproved;
use App\Events\NoteCreated;
use App\Events\TicketAssigned;
use App\Events\TicketFinished;
use App\Helpers\Helper;
use App\Enums\App;



class TicketAppRepository
{
    protected $userRepository;
    protected $checklistRepository;
    protected $checklistOptionRepository;
    protected $logRepository;

    public function __construct(UserRepository $userRepository, TicketChecklistRepository $checklistRepository, ChecklistOptionRepository $checklistOptionRepository, LogRepository $logRepository)
    {
        $this->userRepository        = $userRepository;
        $this->checklistRepository = $checklistRepository;
        $this->checklistOptionRepository = $checklistOptionRepository;
        $this->logRepository = $logRepository;
    }

    public function getAllTicket($request)
    {
        $idUser = $request->iduser;
        $teams = $this->userRepository->getTeams($idUser);
        $spots = $this->userRepository->getUserSpots($idUser);
        $version = isset($request->version) ? intval($request->version) : 400;

        $colums = ['id', 'uuid', 'code', 'name', 'duration', 'iditem', 'idspot', 'idstatus', 'idpriority', 'idspot', 'idteam', 'description', 'created_by', 'files', 'approved', 'signature', 'duedate', 'byclient', 'created_at', 'updated_at', 'deleted_at'];

        if ($version >= 405) {
            $newColumns = ['idplanner'];
            $colums = array_merge($colums, $newColumns);
        }

        return Ticket::with('usersAll:id,firstname,lastname,urlpicture,wh_ticket_user.copy')
        // return Ticket::with('usersAll')
                        ->when($version >= 405, function ($query) {
                            $query->with(['spot:id,name,shortname,idparent','spot.parent:id,name']);
                        }, function ($query) {
                            $query->with('spot:id,name,shortname');
                        })
                        ->with('tags:id as idtag')
                        ->with('item:id,idtype')
                        ->with('team:id,name')
                        ->where(function ($query) use ($idUser, $teams, $spots) {
                            //Filtro de equipos
                            $query->whereIn('idspot', $spots)
                                  ->where(function ($query) use ($idUser, $teams) {
                                    //Filtro de equipos
                                    $query->whereIn('idteam', $teams)
                                          ->orWhere('created_by', $idUser)
                                          ->orWhereHas('users', function ($q) use ($idUser)  {
                                            $q->where('iduser', $idUser);
                                          });
                                  });
                         })
                        ->whereHas('item.tickettype', function ($query) {
                            $query->where('showingrid', 1);
                         })
                        ->orderBy('created_at', 'desc')
                        ->select($colums);
    }

    public function getAllApp($request)
    {
        $tickets = $this->getAllTicket($request);

        $tickets->when($request->has('filter'), function ($query) use ($request) {
            $query->where(function ($q) use ($request) {
                return $this->applyFilters($q, $request);
            });
        });

        if($request->lastidtask != NULL) {
            $tickets->where('id','<' ,$request->lastidtask);
        }

        return $tickets->take($request->take)->latest()->get();
    }

    public function getNewTicketsApp($request)
    {
        $updatedAt = Carbon::parse($request->updatedAt)->setTimezone('UTC');

        $tickets = $this->getAllTicket($request);

        return  $tickets
                    ->with('notes.createdBy')
                    ->with('checklists')
                    ->withTrashed()
                    ->where('updated_at', '>',$updatedAt)
                    ->get();
    }

    public function syncToServer($request)
    {
        $version = isset($request->version) ? $request->version : 306;
        $tasks = json_decode($request->tasks, true);

        if ($request->iduser == 0) {
        
            DB::table('log_sync')->insert([
                'idticket' => 0,
                'error' => 405,
                'iduser' => $request->iduser,
                'data' => json_encode($request->all())
            ]);
        }

        Session::put('iduser', $request->iduser);
        Auth::loginUsingId($request->iduser);

        if (is_array($tasks) || is_object($tasks)) {
        }
        else {
            DB::table('log_sync')->insert([
                'idticket' => 0,
                'error' => 404,
                'iduser' => $request->iduser,
                'data' => json_encode($request->all())
            ]);

            return response()->json([
                "result" => false,
                "ticket" => []
            ]);
        }

        foreach($tasks as &$taskRequest)
        {
            try {
                $touch = isset($taskRequest["touch"]) ? $taskRequest["touch"] : true; 

                if($touch) {

                    $taskRequest["files"] = is_null($taskRequest["idserver"]) ? helper::UploadImageApp(json_decode($taskRequest["files"])) : $taskRequest["files"];
                    
                    if(is_null($taskRequest['code'])) {
                        unset($taskRequest['code']);
                    }

                    $task = Ticket::where(['uuid' => $taskRequest["uuid"]])->first();
                    if ($task) {

                        if ($task->idstatus == TicketStatus::Finished) { 
                            $taskRequest["idstatus"] = TicketStatus::Finished;
                        } else if ($task->idstatus > 1 && $taskRequest["idstatus"] == 1) {
                            $taskRequest["idstatus"] = $task->idstatus;
                        }
                    } 

                    $taskModel = new Ticket();
                    $taskModel->unsetEventDispatcher();
                    $task = $taskModel->updateOrCreate(
                        ['uuid' => $taskRequest["uuid"]],
                        $taskRequest
                    );
                } else {
                    $task = Ticket::withTrashed()->find($taskRequest["idserver"]);
                    // if (is_null($task)) continue;
                    $task->unsetEventDispatcher();
                }

                $this->syncTaskLog($task, $taskRequest["logs"]);
                $this->syncTaskUsers($task, $taskRequest);
                
                $this->syncTaskNotes($taskRequest["notes"], $task->id);
                $this->syncTaskChecklist($taskRequest, $task->id, $version);
                $task->touch();

                if($task->wasRecentlyCreated) {
                    
                    if (is_null($task->code)) {
                        $task->code = $task->id;
                        $task->save();
                    }
                    
                    event(new TicketCreated($task));
                }

                unset($taskRequest["checklist"]);
                unset($taskRequest["notes"]);
                DB::table('log_sync')->insert([
                    'idticket' => $task->id,
                    'error' => 200,
                    'iduser' => $request->iduser,
                    'data' => json_encode($taskRequest)
                ]);

            } catch (\Exception $e) {

                unset($taskRequest["checklist"]);
                unset($taskRequest["notes"]);
                DB::table('log_sync')->insert([
                    'idticket' => 0,
                    'iduser' => $request->iduser,
                    'error' => 500,
                    'message' => '',
                    'message' => substr($e->getMessage(), 0, 1000),
                    'data' => json_encode($taskRequest)
                ]);

                LavavelLog::debug('APP Bug: ' . $e->getMessage());
            }

        }

        $getNewTask = isset($request->getNewTask) ? $request->getNewTask : true; 
        $newTasks = ($getNewTask) ? $this->getNewTicketsApp($request) : [];

        return response()->json([
            "result" => true,
            "ticket" => $newTasks
        ]);
    }
    

    function syncTaskNotes($notesRequest, $idTicket)
    {
        if (is_null($notesRequest)) return null;

        $collection = collect((object) $notesRequest);

        foreach ($collection as $note) {

            $note["idticket"] = $idTicket;
            $ticketNote = TicketNote::where('uuid', $note["uuid"])->where('idticket', $note["idticket"])->first();

            if (!$ticketNote) {

                if ($this->isNoteImg($note)) {
                    $note["note"] = helper::UploadImageApp([$note["note"]]);
                }

                $ticketNote = TicketNote::create($note);
                $this->logRepository->register(LogAction::CreateNote, null, Session::get('iduser'), $ticketNote);
                event(new NoteCreated($ticketNote));
            }
        }

    }

    private function isNoteImg($note)
    {
        $result = false;

        $is_base64 = strlen($note["note"]) > 5000;
        
        if ($note["type"] == TicketNoteTypes::images || $is_base64) {
            $result = true;
        }

        return $result;
    }

  
    private function syncChecklistTable($newData, $oldData)
    {
        $rowsDeleted = $newData->where('deleted', 1)->pluck('row')->toArray();

        $result = collect();
        $newData = $newData->whereNotNull('idparent')->where('deleted', 0);
        $tables = $oldData->where('optiontype', 16)->pluck('idchecklistoption')->toArray();

        foreach ($tables as $key) {
            
            $rowsId = $newData->where('idparent', $key)->pluck('row')->toArray();

            $oldRows = $oldData->where('idparent', $key)->whereNotIn('row', $rowsId);
            $newRows = $newData->where('idparent', $key);
            $options = $oldRows->merge($newRows);

            $result = $result->merge($options);
        }

        $result = $result->whereNotIn('row', $rowsDeleted);

        return $result;
    }
  
    private function syncChecklistRegular($newData, $oldData)
    {
        $newData = $newData->whereNull('idparent');
        $oldData = $oldData->whereNull('idparent');

        $keys = $newData->pluck('idchecklistoption')->toArray();
        $oldData = $oldData->whereNotIn('idchecklistoption', $keys);

        $options = $oldData->merge($newData);
 
        return $options;
    }
  
    function syncTaskChecklist($taskRequest, $idTicket, $version)
    {
        $checklist = $taskRequest["checklist"];
        $idchecklist = $taskRequest["idchecklist"];
        if (is_null($checklist)) return null;

        $ticketChecklist = TicketChecklist::where('idticket', $idTicket)->first();
    
        if ($ticketChecklist) { 
            $newData = collect($checklist);
            $oldData = collect(json_decode($ticketChecklist->options, true));

            $tableData = $this->syncChecklistTable($newData, $oldData);
            $regularData = $this->syncChecklistRegular($newData, $oldData);


            $options = $tableData->merge($regularData);
            $options = json_encode($options);
        } else {
            $options = json_encode($checklist);
        }

        $data = [
            'idticket'  => $idTicket,
            'options'   => $options,
            'results'   => null,
        ];

        if(!is_null($idchecklist)) {
            $data['idchecklist'] = $idchecklist;
        }

        TicketChecklist::updateOrCreate(
            ['idticket' => $idTicket],
            $data
        );
    }

    public function deleteTaskApp($request)
    {
        $ticket = Ticket::findOrFail($request->idtask);
        $ticket->updated_by = $request->iduser;
        $ticket->delete();

        return response()->json(['success' => true, 'message' => 'Acción completada con éxito']);
    }

    public function approvedTaskApp($request)
    {
        $ticket = Ticket::findOrFail($request->idtask);
        $ticket->updated_by = $request->iduser;
        $ticket->approved = $request->approved === 'true' ? true : false;
        
        if(!$ticket->approved)
        {
            $ticket->idstatus   = TicketStatus::Pending;
            $ticket->finishdate = null;
        }

        $ticket->save();

        event(new TicketApproved($ticket));

        if($request->note != ''){
            $ticketNote = new TicketNote();
            $ticketNote->unsetEventDispatcher();
            $ticketNote::create([
                'idticket' => $request->idtask,
                'note' => $request->note,
                'created_by' => $request->iduser,
                'type' => 1,
                'uuid' => uniqid()
            ]);
        }

        return response()->json(['success' => true]);
    }  

    public function updateTaskApp($request) {

        $item = DB::table('wh_item')->where('id', $request->iditem)->first(['name', 'idteam', 'idchecklist']);

        $request['name']       = $item->name;
        $request['idteam']     = $item->idteam;
        $request['updated_by'] = $request->iduser;
        $users                 = json_decode($request->users);
        $tags                  = json_decode($request->tags);
        $code                  = $request->code;
        
        $inputs = $request->except(['users', 'tags', 'code']);
        $ticket = Ticket::find($request->idticket);
        $ticket->fill($inputs);
        if ($code != 'NULL') $ticket->code = $code;
        $ticket->save();

        $ticket->users()->wherePivot('copy', 0)->sync($users);
        $ticket->tags()->sync($this->getFormatTags($tags, $request->iduser));
        $data = $this->getTaskbyId($request->idticket);

        return response()->json([
            'success' => true, 
            'message' => 'Acción completada con éxito',
            'ticket' => $data
        ]);
    }

    private function getTaskbyId($idticket) {

        return Ticket::where('id', $idticket)
                    ->with('spot:id,name')
                    ->first(['code','name', 'iditem', 'idspot', 'idteam', 'idpriority', 'description', 'duedate']);
    }

    public function signTaskAPP($request) {

        $url = helper::UploadImageApp([$request->signature]);

        $ticket = Ticket::find($request->idticket);
        $ticket->signature = $url;
        $ticket->updated_by = $request->iduser;
        $ticket->save();

        return response()->json([
            'success' => true, 
            'signature' => $url
        ]);
    }

    private function syncTaskUsers($ticket, $taskRequest) {

        $users = json_decode($taskRequest["users"], true);

        if (!is_null($users)){
            $attachedIds = $ticket->users()->pluck('iduser')->toArray();
            $newIds = array_diff($users, $attachedIds);
            $ticket->users()->wherePivot('copy', 0)->attach($newIds);
        }

        if (!array_key_exists('copied', $taskRequest)) return;

        $copied = json_decode($taskRequest["copied"], true);

        if (!is_null($copied)){
            $ticket->usersCopy()->wherePivot('copy', 1)->sync($this->getFormatUsersCopy((array)$copied));
        }
    }

    private function getFormatTags($tags, $iduser)
    {
        $result = array();

        foreach($tags as $value)
        {
            $result[$value] = ['iduser' => $iduser];
        }

        return $result;
    }

    public function createTaskAPP($request) {

        $item = DB::table('wh_item')->where('id', $request->iditem)->first(['name', 'idchecklist']);
        $request["files"] = $request->img != 'null' ? helper::UploadImageApp(json_decode($request->img)) : null;

        session(["iduser" => $request->created_by]);
        $task = Ticket::create($request->all());

        $users = json_decode($request->users);
        $task->users()->wherePivot('copy', 0)->attach($users);

        if(!is_null($item->idchecklist)) {

            $checklist_copy = $this->checklistOptionRepository->getChecklistCopy($item->idchecklist, $task->id);
            $task->checklists()->create($checklist_copy);
        }

        return response()->json(['success' => true]);
    }

    /**************** CLEANING FUNCTION ***********************/

    public function getTaskByIdspot($request) {

        $items = DB::table('wh_ticket as t')
                    ->join('wh_item as i', 't.iditem', '=', 'i.id')
                    ->join('wh_ticket_type as tt', 'i.idtype', '=', 'tt.id')
                    ->join('wh_spot as s', 't.idspot', '=', 's.id')
                    ->select('t.id', 't.code', 't.name', 't.quantity', 't.idspot', 's.name AS spot_name', 'tt.icon', 'tt.color', 't.idstatus', 't.created_by', 't.created_at')
                    ->where('t.idstatus', "!=", TicketStatus::Finished)
                    ->where('t.idspot', $request->idspot)
                    ->whereNull('t.deleted_at')
                    ->orderBy('t.id', 'desc')
                    ->get();

        $items->map(function ($item) {
            $item->icon = helper::formatIcon($item->icon);
        });

        return $items;
    }

    
    public function getTaskCleaningProduct($request) {

        $settings = helper::getCleaningSettings();
        $idtypes = $settings->cleaning_products;

        $items = DB::table('wh_ticket as t')
                    ->join('wh_item as i', 't.iditem', '=', 'i.id')
                    ->join('wh_ticket_type as tt', 'i.idtype', '=', 'tt.id')
                    ->join('wh_spot as s', 't.idspot', '=', 's.id')
                    ->select('t.id', 't.code', 't.name', 't.quantity', 't.idspot', 's.name AS spot_name', 'tt.icon', 'tt.color', 't.idstatus', 't.created_by', 't.created_at')
                    ->where(function ($query) {
                        $query->where('t.idstatus', "!=", TicketStatus::Finished)
                              ->orwhereDate('t.created_at', "=", Carbon::today());
                    })
                    ->whereIn('tt.id', $idtypes)
                    ->whereNull('t.deleted_at')
                    ->orderBy('t.id', 'desc')
                    ->get();

        $items->map(function ($item) {
            $item->icon = helper::formatIcon($item->icon);
        });

        return $items;
    }

    public function changeStatusApp($request)
    {

        $ticket = Ticket::find($request->idticket);
        $ticket->unsetEventDispatcher();
        $ticket->users()->attach($request->idUser);
        $ticket->updated_by = $request->idUser;
        $ticket->idstatus = $request->idstatus;
        $ticket->save();

        return response()->json(['success' => true]);
    }

    private function getFormatUsersCopy($users)
    {
        $result = array();

        foreach($users as $value)
        {
            $result[$value] = ['copy' => 1];
        }

        return $result;
    }

    public function searchTicket($request)
    {
        $idUser = $request->iduser;
        $spots = $this->userRepository->getUserSpots($idUser);
        $teams = $this->userRepository->getTeams($idUser);

        $result = DB::table('wh_ticket as task')
                    ->select('task.id', 'task.code', 'task.name', 'task.idstatus', 'wh_spot.id AS idspot', 'wh_spot.name AS spot', 'wh_spot.shortname', 'task.description', 'task.iditem', 'task.idpriority', 'task.approved', DB::raw('IF(files IS NULL, "null", "1") AS files'), 'task.created_at', 'task.deleted_at')
                    ->leftJoin('wh_ticket_user as task_user', 'task.id', '=', 'task_user.idticket')
                    ->leftJoin('wh_user', 'wh_user.id', '=', 'task_user.iduser')
                    ->leftJoin('wh_spot', 'wh_spot.id', '=', 'task.idspot')
                    ->leftJoin('wh_priority', 'wh_priority.id', '=', 'task.idpriority')
                    ->leftJoin('wh_ticket_tag AS task_tag', 'task_tag.idticket', '=', 'task.id')
                    ->leftJoin('wh_tag', 'wh_tag.id', '=', 'task_tag.idtag')
                    ->leftJoin('wh_item', 'wh_item.id', '=', 'task.iditem')
                    ->leftJoin('wh_ticket_type', 'wh_ticket_type.id', '=', 'wh_item.idtype')
                    ->when($request->has('where'), function ($query) use ($request) {
                        $query->whereRaw($request->where);
                    })
                    ->where('wh_ticket_type.showingrid', 1)
                    ->where(function ($query) use ($idUser, $teams, $spots) {
                        $query->whereIn('task.idspot', $spots)
                              ->whereIn('task.idteam', $teams)
                              ->orWhere('task.created_by', $idUser);
                    })
                    ->skip($request->skip)
                    ->take(100)
                    ->orderBy('id', 'desc')
                    ->distinct()
                    ->get();
        
        $tasks = $result->whereNull('deleted_at')->values();

        return $tasks;
    }

    public function findTicketsApp($request)
    {
        $tickets = $this->getAllTicket($request);

        return  $tickets->where('id', $request->idtask)
                        ->get();
    }

    function syncTaskLog($task, $logs)
    {
        if (count($logs) == 0) return null;

        if (!array_key_exists('uuid', $logs[0])) {
            $task->logs()->createMany($logs);
        } else {

            foreach ($logs as $log) {

                $newLog = $task->logs()->firstOrCreate(
                    ['uuid' => $log["uuid"]],
                    $log
                );

                if ($newLog->wasRecentlyCreated) {
                    $this->sendFinishNotification($task, $log);
                }
            }
        }

    }

    function sendFinishNotification($task, $log)
    {
        $settings = json_decode(DB::table('wh_organization')->first()->settings);
        $notify = property_exists($settings, 'notify_finished_task') ? $settings->notify_finished_task : false;

        if (!$notify) return false;

        if ($log["action"] == 'EDIT_TICKET') {

            if ($log["idstatus"] == TicketStatus::Finished) {
                event(new TicketFinished($task));
            }
        }
    }

    public function uploadBase64($request) {
 
        $url = helper::UploadImageApp([$request->base64]);

        return response()->json([
            'success' => true, 
            'url' => $url
        ]);
    }

    public function deleteImage($request) {
 
        $success = helper::deleteUrl($request->url);

        return response()->json(['success' => $success]);
    }

    public function checkTaskExistsAPP($request) {
 
        $tickets = Ticket::select('id', 'created_by', 'created_at')
                        ->with('createdby:id,firstname,lastname')
                        ->where('idspot', $request->idspot)
                        ->where('iditem', $request->iditem)
                        ->where('idstatus', '!=', TicketStatus::Finished)
                        ->get();

        return response()->json([
            'success' => true, 
            'exists' => ($tickets->count() > 0),
            'task' => $tickets->first(),
        ]);
    }

    
    private function applyFilters($model, $request)
    {
        $filters = json_decode($request->filter);

        foreach($filters as $filter)
        {
            switch ($filter->field)
            {
                case "iduser":

                    if ($filter->value == "NULL") {
                        $model->doesnthave('users');
                    } else {

                        $value = ($filter->operator) == 'eq' ? $filter->value : explode(",", $filter->value);

                        $model->whereHas('users', function ($query) use ($value) {
                            $query->where('iduser', $value);
                        });
                    }
                    break;
                    
                case "idtag":

                    $value = ($filter->operator) == 'eq' ? $filter->value : explode(",", $filter->value);

                    $model->whereHas('tags', function ($query) use ($value) {
                        $query->where('idtag', $value);
                    });
                    break;

                case 'dateRange':
                    $start = Carbon::parse($filter->start)->startOfDay();
                    $end   = Carbon::parse($filter->end)->endOfDay();

                    $model->whereBetween('created_at', [$start, $end]);
                    break;

                case 'task_overdue':

                    $model->whereDate('duedate', '<', Carbon::now())
                            ->where('idstatus', '!=', TicketStatus::Finished);
                    break;
                
                default:
                    
                    switch ($filter->operator) {
                        case 'eq':
                            $model->where($filter->field, $filter->value);
                            break;
                        case 'ne':
                            $model->where($filter->field, '!=' , $filter->value);
                            break;
                        case 'in':
                            $dataToFind = explode(",", $filter->value);
                            $model->whereIn($filter->field, $dataToFind);
                            break;
                    }
                    break;
            }
        }

        return $model;
    }

    public function assignTaskApp($request) {

        $users  = json_decode($request->users);
        $ticket = Ticket::find($request->idticket);
        $ticket->updated_by = $request->iduser;
        $ticket->save();

        $ticket->users()->wherePivot('copy', 0)->sync($users);

        return response()->json([
            'success' => true, 
            'message' => 'Acción completada con éxito'
        ]);
    }

    public function showMsFilesApp($request)
    {
        return view('/pages/appPages/showMSfile', ['url' => $request->url ]);
    }

}