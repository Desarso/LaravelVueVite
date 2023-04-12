<?php

namespace App\Repositories;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Ticket;
use App\Models\TicketNote;
use App\Models\TicketUser;
use Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use App\Enums\TicketStatus;
use App\Enums\LogAction;
use App\Enums\ReminderType;
use App\Events\TicketCreated;
use App\Events\TicketApproved;
use Config;
use Session;

class TicketRepository
{
    protected $userRepository;
    protected $checklistOptionRepository;
    protected $ticketNoteRepository;
    protected $reminderRepository;
    protected $spotRepository;

    public function __construct()
    {
        $this->userRepository            = new UserRepository;
        $this->checklistOptionRepository = new ChecklistOptionRepository;
        $this->ticketNoteRepository      = new TicketNoteRepository;
        $this->reminderRepository        = new ReminderRepository;
        $this->spotRepository            = new SpotRepository;
    }

    public function getAll($request)
    {
        $hasRangeDate = $request->hasRangeDate === "false" ? false : true;

        $teams = $this->userRepository->getTeams(Auth::id());

        $spots = json_decode(Auth::user()->spots);

        $settings = json_decode(DB::table('wh_organization')->first()->settings);

        $tickets = Ticket::query()->select(['id', 'name', 'idstatus', 'iditem', 'idteam', 'idpriority', 'code', 'quantity', 'idspot', 'description', 'approved', 'files', 'justification', 'created_at', 'duration', 'duedate', 'signature', 'created_by', 'quantity'])
                         ->with('spot:id,name')
                         ->with('item:id,idtype')
                         ->with('users:id as iduser')
                         ->with('usersCopy:id as iduser')
                         ->with('tags')
                         ->withCount(["notes" => function ($q) {
                            $q->whereNull('idchecklistoption');
                         }])
                         ->with(["checklists" => function ($q) {
                            $q->select('idticket', 'results');
                         }])
                         ->whereHas('item.tickettype', function ($query) {
                            $query->where('showingrid', 1);
                         })
                         ->where(function ($query) use ($teams, $spots) {
                            //Filtro de equipos
                            $query->whereIn('idspot', $spots)
                                  ->where(function ($query) use ($teams) {
                                        $query->whereIn('idteam', $teams)
                                              ->orWhere('created_by', Auth::id())
                                              ->orWhereHas('usersAll', function ($q) {
                                                $q->where('iduser', Auth::id());
                                              });
                                  });
                         })
                         ->when($hasRangeDate, function ($query) use($request){
                            $start = Carbon::parse($request->start)->startOfDay();
                            $end   = Carbon::parse($request->end)->endOfDay();

                            $query->whereBetween('created_at', [$start, $end]);
                         })
                         ->when($request->has('filter'), function ($query) use ($request) {
                            //Filtros del buscador
                            $query->where(function ($q) use ($request) {
                                return $this->applyFilters($q, $request);
                            });
                         })
                         ->when(!is_null($request->search), function ($query) use ($request) {
                            //Buscador
                            $query->where(function ($q) use ($request) {
                                return $this->applySearch($q, $request);
                            });
                         })
                         ->when($request->has('sort'), function ($query) use($request, $settings){
                            return $this->sort($query, $request->sort);
                         }, function ($query) use($settings){
                            return (property_exists($settings, 'default_order') ? $query->orderByRaw('FIELD(idstatus,'. $settings->default_order .')') : $query->orderBy('created_at', 'DESC'));
                         });

        $total = $tickets->count('id');

        $tickets2 = clone $tickets;

        $overdueTasks = $tickets2->where('idstatus', '!=', 4)->whereDate('duedate', '<=', Carbon::now())->count();

        return array("overdueTasks" => $overdueTasks, "total" => $total, "data" => $tickets->skip($request->skip)->take($request->take)->get());
    }

    public function getStats($request)
    {
        $hasRangeDate = $request->hasRangeDate === "false" ? false : true;

        $teams = $this->userRepository->getTeams(Auth::id());

        $spots = json_decode(Auth::user()->spots);

        $settings = json_decode(DB::table('wh_organization')->first()->settings);

        $tickets = Ticket::where(function ($query) use ($teams, $spots) {
                            //Filtro de equipos
                            $query->whereIn('idspot', $spots)
                                  ->where(function ($query) use ($teams) {
                                        $query->whereIn('idteam', $teams)
                                              ->orWhere('created_by', Auth::id())
                                              ->orWhereHas('usersAll', function ($q) {
                                                $q->where('iduser', Auth::id());
                                              });
                                  });
                         })
                         ->whereHas('item.tickettype', function ($query) {
                            $query->where('showingrid', 1);
                         })
                         ->when($hasRangeDate, function ($query) use($request){
                            $start = Carbon::parse($request->start)->startOfDay();
                            $end   = Carbon::parse($request->end)->endOfDay();

                            $query->whereBetween('created_at', [$start, $end]);
                         })
                         ->when($request->has('filter'), function ($query) use ($request) {
                            //Filtros del buscador
                            $query->where(function ($q) use ($request) {
                                return $this->applyFilters($q, $request);
                            });
                         })
                         ->when(!is_null($request->search), function ($query) use ($request) {
                            //Buscador
                            $query->where(function ($q) use ($request) {
                                return $this->applySearch($q, $request);
                            });
                         })
                         ->select(['id', 'idstatus', 'duration'])
                         ->when($request->has('sort'), function ($query) use($request, $settings){
                            return $this->sort($query, $request->sort);
                         }, function ($query) use($settings){
                            return (property_exists($settings, 'default_order') ? $query->orderByRaw('FIELD(idstatus,'. $settings->default_order .')') : $query->orderBy('created_at', 'DESC'));
                         })
                         ->get();

        $finished = $tickets->where('idstatus', 4)->count();
        $total    = $tickets->count();

        return[
            'status'           => $tickets->countBy('idstatus'),
            'average_duration' => $finished == 0 ? 0 : round((($tickets->where('idstatus', 4)->sum('duration') / $finished) / 60)),
            'efficacy'         => $total    == 0 ? 100 : round(( $finished / $total) * 100),
            'total'            => $total
        ];
    }

    public function getMyStats($request)
    {
        $hasRangeDate = $request->hasRangeDate === "false" ? false : true;

        if($request->has('iduser')) Auth::loginUsingId($request->iduser);

        $settings = json_decode(DB::table('wh_organization')->first()->settings);
        
        $teams = $this->userRepository->getTeams(Auth::id());

        $spots = json_decode(Auth::user()->spots);

        $tickets = Ticket::where(function ($query) use ($teams, $spots) {
                            //Filtro de equipos
                            $query->whereIn('idspot', $spots)
                                  ->where(function ($query) use ($teams) {
                                        $query->whereIn('idteam', $teams)
                                              ->orWhere('created_by', Auth::id());
                                  });
                                  
                        })->whereHas('users', function ($query) {
                            $query->where('iduser', Auth::id());
                         })
                         ->whereHas('item.tickettype', function ($query) {
                            $query->where('showingrid', 1);
                         })
                         ->when($hasRangeDate, function ($query) use($request){
                            $start = Carbon::parse($request->start)->startOfDay();
                            $end   = Carbon::parse($request->end)->endOfDay();

                            $query->whereBetween('created_at', [$start, $end]);
                         })
                         ->when($request->has('filter'), function ($query) use ($request) {
                            //Filtros del buscador
                            $query->where(function ($q) use ($request) {
                                return $this->applyFilters($q, $request);
                            });
                         })
                         ->when(!is_null($request->search), function ($query) use ($request) {
                            //Buscador
                            $query->where(function ($q) use ($request) {
                                return $this->applySearch($q, $request);
                            });
                         })
                         ->select(['id', 'idstatus', 'duration', 'approved', 'duedate', 'finishdate'])
                         ->when($request->has('sort'), function ($query) use($request, $settings){
                            return $this->sort($query, $request->sort);
                         }, function ($query) use($settings){
                            return (property_exists($settings, 'default_order') ? $query->orderByRaw('FIELD(idstatus,'. $settings->default_order .')') : $query->orderBy('created_at', 'DESC'));
                         })
                         ->get();

        $finished = $tickets->where('idstatus', TicketStatus::Finished)->count();
        $pending = $tickets->where('idstatus', TicketStatus::Pending)->count();
        $paused = $tickets->where('idstatus', TicketStatus::Paused)->count();
        $total    = $tickets->count();
        $efficacy = $this->getDataEfficacy($tickets);

        return[
            'pending'    => $pending,
            'paused'   => $paused,
            'finished'   => $finished,
            'reproved'   => $tickets->whereNotNull('approved')->where('approved', 0)->count(),
            'efficiency' => $total == 0 ? 100 : round( ( $finished / $total ) * 100 ),
            'efficacy'   => $efficacy['efficacy'],
            'expired'    => $efficacy['expired'],
            'total'      => $total
        ];
    }

    private function getDataEfficacy($tickets)
    {
        $with_expiration  = $tickets->whereNotNull('duedate');
        $total_expiration = $with_expiration->count();
        $expired = 0;
        $ontime  = 0;

        foreach($with_expiration as $item)
        {
            $limit_date = is_null($item->finishdate) ? Carbon::now()->setTimezone(Session::get('local_timezone')) : Carbon::parse($item->finishdate)->setTimezone(Session::get('local_timezone'));
            $limit_date->greaterThan($item->duedate) ? $expired++ : $ontime++;
        }

        if($total_expiration == 0) return ["efficacy" => 100, "expired" => $expired];
        if($expired == 0) return ["efficacy" => 100, "expired" => $expired];

        $efficacy = round(($ontime / $total_expiration) * 100);

        return ["efficacy" => $efficacy, "expired" => $expired];
    }

    public function applyFilters($model, $request)
    {
        $logic   = $request->filter['logic'];
        $filters = $request->filter['filters'];

        foreach($filters as $filter)
        {
            switch ($filter['operator'])
            {
                case "eq":
                    $this->getFilterEq($model, $filter, $logic);
                    break;

                case "contains":
                    $this->getFilterContains($model, $filter, $logic);
                    break;
            }
        }

        return $model;
    }

    public function applySearch($query, $request)
    {
        $query->where('name', 'LIKE', '%'. $request->search .'%')
              ->orWhere('description', 'LIKE', '%'. $request->search .'%')
              ->orWhere('code', 'LIKE', '%'. $request->search .'%')
              ->orWhereHas('priority', function ($query) use ($request){
                    $query->where('name', 'LIKE', '%'. $request->search .'%');
              })
              ->orWhereHas('spot', function ($query) use ($request){
                    $query->where('name', 'LIKE', '%'. $request->search .'%');
              })
              ->orWhereHas('status', function ($query) use ($request){
                    $query->where('name', 'LIKE', '%'. $request->search .'%');
              })
              ->orWhereHas('users', function ($query) use ($request){
                    $query->where('firstname', 'LIKE', '%'. $request->search .'%')
                          ->orWhere('lastname', 'LIKE', '%'. $request->search .'%')
                          ->orWhere('username', 'LIKE', '%'. $request->search .'%');
              })
              ->orWhereHas('tags', function ($query) use ($request){
                $query->where('name', 'LIKE', '%'. $request->search .'%');
              });

        return $query;
    }

    private function getFilterContains($model, $filter, $logic)
    {
        switch ($filter['field'])
        {
            case "note":

                $model->when($logic == "and", function ($query) use ($filter) {
                    return $query->whereHas('notes', function ($query) use ($filter) {
                        $query->where('note', 'like', '%' . $filter['value'] . '%');
                    });
                }, function ($query) use ($filter) {
                    return $query->orWhereHas('notes', function ($query) use ($filter) {
                        $query->orWhere('note', 'like', '%' . $filter['value'] . '%');
                    });
                });

                break;
            
            default:

                $model->when($logic == "and", function ($query) use ($filter) {
                    return $query->where($filter['field'], 'like', '%' . $filter['value'] . '%');
                }, function ($query) use ($filter) {
                    return $query->orWhere($filter['field'], 'like', '%' . $filter['value'] . '%');
                });
        }

        return $model;
    }

    private function getFilterEq($model, $filter, $logic)
    {
        switch ($filter['field'])
        {
            case "iduser":

                $model->when($logic == "and", function ($query) use ($filter) {
                    return $query->whereHas('users', function ($query) use ($filter) {
                        $query->where('iduser', $filter['value']);
                    });
                }, function ($query) use ($filter) {
                    return $query->orWhereHas('users', function ($query) use ($filter) {
                        $query->where('iduser', $filter['value']);
                    });
                });

                break;

            case "idtag":

                $model->when($logic == "and", function ($query) use ($filter) {
                    return $query->whereHas('tags', function ($query) use ($filter) {
                        $query->where('idtag', $filter['value']);
                    });
                }, function ($query) use ($filter) {
                    return $query->orWhereHas('tags', function ($query) use ($filter) {
                        $query->where('idtag', $filter['value']);
                    });
                });

                break;
            
            case "idtype":

                $model->when($logic == "and", function ($query) use ($filter) {
                    return $query->whereHas('item', function ($query) use ($filter) {
                        $query->where('idtype', $filter['value']);
                    });
                }, function ($query) use ($filter) {
                    return $query->orWhereHas('item', function ($query) use ($filter) {
                        $query->where('idtype', $filter['value']);
                    });
                });

                break;

            case "idbranch":

                $spots = $this->spotRepository->getChildren($filter['value']);

                $model->when($logic == "and", function ($query) use ($filter, $spots) {
                    return $query->whereIn('idspot', $spots);
                }, function ($query) use ($filter, $spots) {
                    return $query->orWhereIn('idspot', $spots);
                });

                break;
                
            case "assign":

                $model->when($logic == "and", function ($query) use ($filter) {
                    return $query->when($filter['value'] == 1, function ($q) {
                        return $q->has('users', '==', 0);
                    }, function ($q) {
                        return $q->has('users', '>', 0);
                    });

                }, function ($query) use ($filter) {
                    return $query->when($filter['value'] == 1, function ($q) {
                        return $q->orHas('users', '==', 0);
                    }, function ($q) {
                        return $q->orHas('users', '>', 0);
                    });
                });
    
                break;

            case "idstatus":

                $operator = "=";

                if($filter['value'] == 5)
                {
                    $operator = "!=";
                    $filter['value'] = 4;
                }

                $model->when($logic == "and", function ($query) use ($filter, $operator) {
                    return $query->where($filter['field'], $operator, $filter['value']);
                }, function ($query) use ($filter, $operator) {
                    return $query->orWhere($filter['field'], $operator, $filter['value']);
                });
    
                break;

            case "duedate":

                $now = Carbon::now();

                $model->when($logic == "and", function ($query) use ($now) {
                    return $query->where('idstatus', '!=', 4)->where('duedate', '<=', $now);
                }, function ($query) use ($now) {
                    return $query->orWhere('idstatus', '!=', 4)->where('duedate', '<=', $now);
                });

                break;
            
            default:

                $model->when($logic == "and", function ($query) use ($filter) {
                    return $query->where($filter['field'], $filter['value']);
                }, function ($query) use ($filter) {
                    return $query->orWhere($filter['field'], $filter['value']);
                });
        }

        return $model;
    }

    private function sort($query, $sorts)
    {
        foreach($sorts as $sort) {
            $query->orderBy($sort["field"], $sort["dir"]);
        }
        return $query;
    }

    public function create($request)
    {
        $idtickets = [];

        $item = DB::table('wh_item')->where('id', $request->iditem)->first(['name', 'idteam', 'idchecklist', 'idpriority']);

        $this->getDuedate($request, $item);

        $request['name']       = $item->name;
        $request['idteam']     = $item->idteam;
        $request['created_by'] = Auth::id();
        $request['updated_by'] = Auth::id();
        $users                 = (array)$request->users;
        $tags                  = (array)$request->tags;
        $approvers             = (array)$request->approvers;
        
        foreach ((array) $request->spots as $idspot)
        {
            $request['idspot'] = $idspot;
            if (count($approvers) > 0) $request['approved'] = 0;

            if($request->byresource && count($users) > 1)
            {
                foreach ($users as $user)
                {
                    $request['uuid'] = uniqid();
                    $ticket = $this->saveTicket($request, (array)$user, $tags, $approvers, $item);
                    array_push($idtickets, $ticket->id);
                }
            }
            else
            {
                $request['uuid'] = uniqid();
                $ticket = $this->saveTicket($request, $users, $tags, $approvers, $item);
                array_push($idtickets, $ticket->id);
            }
        }

        return response()->json(['success' => true, 'message' => 'Acción completada con éxito', 'tickets' => $idtickets]);
    }

    private function getDuedate($request, $item)
    {
        $priority = DB::table('wh_ticket_priority')->where('id', $item->idpriority)->first(['sla']);

        if(is_null($request->duedate) && !is_null($priority) && ($priority->sla > 0))
        {
            $request->merge(['duedate' => Carbon::now()->addMinutes($priority->sla)]);
        }

        return $request;
    }

    private function saveTicket($request, $users, $tags, $approvers, $item)
    {
        $ticket = Ticket::create($request->all());

        $ticket->users()->wherePivot('copy', 0)->attach($users);
        $ticket->usersCopy()->wherePivot('copy', 1)->attach($this->getFormatUsersCopy((array)$request->copies));
        $ticket->tags()->attach($this->getFormatTags($tags));
        $ticket->approvers()->attach($this->getFormatApprovers($approvers));

        if(!is_null($item->idchecklist))
        {
            $checklist_copy = $this->checklistOptionRepository->getChecklistCopy($item->idchecklist, $ticket->id);
            $ticket->checklists()->create($checklist_copy);
        }

        return $ticket;
    }

    private function getFormatApprovers($approvers)
    {
        $result = array();
        for($i = 0; $i < count($approvers); $i++)
        {
            $result[$approvers[$i]] = ['sequence' => $i+1];
        }
        return $result;   
    }
    

    public function getFormatTags($tags)
    {
        $result = array();

        foreach($tags as $value)
        {
            $result[$value] = ['iduser' => Auth::id()];
        }

        return $result;
    }

    public function getFormatUsersCopy($users)
    {
        $result = array();

        foreach($users as $value)
        {
            $result[$value] = ['copy' => 1];
        }
        
        return $result;
    }

    public function delete($request)
    {
        $ticket = Ticket::findOrFail($request->idticket);
        $ticket->updated_by = Auth::id();
        $ticket->delete();

        return response()->json(['success' => true, 'message' => 'Acción completada con éxito']);
    }

    public function verify($request)
    {
        $ticket = Ticket::findOrFail($request->idticket);

        $ticket->updated_by = Auth::id();
        $ticket->approved   = $request->approved;

        if($request->approved == 0)
        {
            $ticket->idstatus   = TicketStatus::Pending;
            $ticket->finishdate = null;
        }

        $ticket->save();

        if(!is_null($request->note))
        {
            $request->approved ? $request->merge(['note' => ("Tarea aprobada: " . $request->note)]) : $request->merge(['note' => ("Tarea reprobada: " . $request->note)]);

            $this->ticketNoteRepository->create($request);
        }

        event(new TicketApproved($ticket));

        return response()->json(['success' => true, 'message' => 'Acción completada con éxito']);
    }

    public function getStatus($request)
    {
        $status = DB::table('wh_ticket as t')
                    ->join('wh_ticket_status as ts', 'ts.id', '=', 't.idstatus')
                    ->where('t.id', $request->idticket)
                    ->select('ts.nextstatus')
                    ->first();

        $next_status = DB::table('wh_ticket_status')
                         ->whereIn('id', json_decode($status->nextstatus))
                         ->select('id', 'name', 'color')
                         ->get();

        return view("task.next-status", ["next_status" => $next_status]);
    }
    
    public function changeStatus($request)
    {
        if($request->has("confirmPause")) $this->pauseTicket();

        $ticket = Ticket::find($request->idticket);

        $dataLog = ["old_status" => $ticket->idstatus, "new_status" => $request->idstatus];

        if($ticket->users->count() == 0)
        {
            $ticket->users()->attach(Auth::id());
        }

        $this->calculateDuration($ticket, $request->idstatus);

        $ticket->updated_by = Auth::id();
        $ticket->idstatus = $request->idstatus;
        $ticket->save();

        return response()->json(['success' => true, 'message' => 'Acción completada con éxito']);
    }

    public function setDuration($request)
    {
        $ticket = Ticket::find($request->idticket);
        $ticket->duration = ($request->duration * 60);
        $ticket->save();

        return response()->json(['success' => true, 'message' => 'Acción completada con éxito']);
    }

    private function pauseTicket()
    {
        $tickets = Ticket::whereHas('users', function ($query) {
                            $query->where('iduser', Auth::id());
                         })
                         ->where('idstatus', TicketStatus::Progress)
                         ->get();

        $tickets->each(function ($ticket, $key){
            $this->calculateDuration($ticket, TicketStatus::Paused);

            $ticket->updated_by = Auth::id();
            $ticket->idstatus = TicketStatus::Paused;
            $ticket->save();
        });
    }

    function calculateDuration($ticket, $idstatus)
    {
        switch ($idstatus)
        {
            case TicketStatus::Progress:

                is_null($ticket->startdate) == true ? $ticket->startdate = Carbon::now() : $ticket->resumedate = Carbon::now();
                break;

            case TicketStatus::Paused:

                $now = Carbon::now();

                if(!is_null($ticket->resumedate))
                {
                    $ticket->duration = $now->diffInSeconds($ticket->resumedate) + $ticket->duration;
                }
                else
                {
                    $ticket->duration = $now->diffInSeconds($ticket->startdate);
                }

                break;

            case TicketStatus::Finished:

                $now = Carbon::now();

                if($ticket->idstatus == TicketStatus::Progress && is_null($ticket->resumedate))
                {
                    $ticket->duration = $now->diffInSeconds($ticket->startdate);
                }
                else if($ticket->idstatus == TicketStatus::Progress && !is_null($ticket->resumedate))
                {
                    $ticket->duration = $now->diffInSeconds($ticket->resumedate) + $ticket->duration;
                }

                $ticket->finishdate = Carbon::now();

                break;
        }
    }

    public function update($request)
    {
        $item = DB::table('wh_item')->where('id', $request->iditem)->first(['name', 'idteam', 'idchecklist', 'idpriority']);

        $this->getDuedate($request, $item);

        $request['name']       = $item->name;
        $request['idteam']     = $item->idteam;
        $request['updated_by'] = Auth::id();
        
        $inputs = $request->except(['users']);
        $ticket = Ticket::find($request->idticket);
        $ticket->updated_by = Auth::id();
        $ticket->fill($inputs);
        $ticket->save();

        $users     = $ticket->users()->wherePivot('copy', 0)->sync($request->users);
        $usersCopy = $ticket->usersCopy()->wherePivot('copy', 1)->sync($this->getFormatUsersCopy((array)$request->copies));
        $tags      = $ticket->tags()->sync($this->getFormatTags((array)$request->tags));
        $approvers = $ticket->approvers()->sync($this->getFormatApprovers((array)$request->approvers));

        $ticket->touch();

        return response()->json(['success' => true, 'message' => 'Acción completada con éxito']);
    }

    public function get($request)
    {
        return Ticket::with('users:id as iduser')->with('usersCopy:id as iduser')->with('tags:id as idtag')->with('approvers:id as iduser')->find($request->idticket);
    }

    public function uploadFile($request)
    {
        if(!$request->hasFile('files1')) return response()->json(['success' => false]);
        
        $saved_tickets = explode(",", $request->idtickets);

        $client = env('DO_SPACES_HOTEL', 'prueba');
        $path   = env('DO_SPACES_ROUTE', 'https://dingdonecdn.nyc3.digitaloceanspaces.com/');
        
        $plain_urls = null;
        
        foreach($saved_tickets as $idticket)
        {
            foreach ($request->file('files1') as $file)
            {
                $file_name = uniqid() . "." . $file->getClientOriginalExtension();
    
                $url = Storage::disk('spaces')->putFileAs($client, $file, $file_name, 'public');
                $full_url = $path . $url;

                is_null($plain_urls) ? ($plain_urls = $full_url) : ($plain_urls .= ',' . $full_url);
            }

            $ticket = Ticket::find($idticket);

            $ticket->files = is_null($ticket->files) ? $plain_urls : ($ticket->files . ',' . $plain_urls);
            $ticket->save();
            
            $plain_urls = null;
        }

        return response()->json(['success' => true]);
    }

    public function deleteFile($request)
    {
        $full_url = explode('/', $request->key);
        $deleted = Storage::disk('spaces')->delete($full_url[3] . '/' . $full_url[4]);

        if($deleted)
        {
            $ticket = Ticket::find($request->idticket);

            $array_files_urls = explode(",", $ticket->files);

            $new_files_urls = array_diff($array_files_urls, [$request->key]);

            $ticket->files = count($new_files_urls) == 0 ? null : implode(",", $new_files_urls);
            $ticket->save();
        }

        return 1;
    }

    public function escalate($request)
    {
        if($request->has('iduser')) {
            Auth::loginUsingId($request->iduser);
            $request->users = json_decode($request->users);
        }

        $ticket = Ticket::findOrFail($request->idticket);
        $ticket->updated_by = Auth::id();
        $ticket->idteam = $request->idteam;
        $ticket->save();

        $oldUsers     = $ticket->users->pluck('id')->toArray();
        $oldCopyUsers = $ticket->usersCopy->pluck('id')->toArray();

        if(!is_null($request->users) && $oldUsers != $request->users)
        {
            $ticket->users()->wherePivot('copy', 0)->sync([]);

            $copies = array_merge($oldUsers, $oldCopyUsers);

            foreach ($request->users as $user)
            {
                if (($key = array_search($user, $copies)) !== false)
                {
                    unset($copies[$key]);
                }
            }

            $ticket->users()->wherePivot('copy', 0)->sync($request->users);

            $ticket->usersCopy()->wherePivot('copy', 1)->sync($this->getFormatUsersCopy((array)$copies));
        }

        $users = $ticket->usersAll()->select('iduser AS id','firstname','lastname','urlpicture','copy')->get();
        $team = $ticket->load('team:id,name')->team;

        return response()->json([
            'success' => true, 
            'message' => 'Acción completada con éxito', 
            'users' => $users,
            'team' => $team,
        ]);
    }

    public function getLast()
    {
        $teams = $this->userRepository->getTeams(Auth::id());
        $spots = json_decode(Auth::user()->spots);

        $last = Ticket::where(function ($query) use ($teams, $spots) {
                            $query->whereIn('idspot', $spots)
                                  ->whereIn('idteam', $teams)
                                  ->orWhere('created_by', Auth::id());
                      })
                      ->orderBy('updated_at', 'desc')
                      ->first();

        return (is_null($last) ? "null" : $last->updated_at);
    }
    
    public function getgeneralStats($request)
    {
        $hasRangeDate = $request->hasRangeDate === "false" ? false : true;
        $tickets = Ticket::when($hasRangeDate, function ($query) use($request){
                            $start = Carbon::parse($request->start)->startOfDay();
                            $end   = Carbon::parse($request->end)->endOfDay();

                            $query->whereBetween('created_at', [$start, $end]);
                         })
                         ->select(['id', 'idstatus'])
                         ->get();

        $users = TicketUser::whereIn('idticket', $tickets->pluck('id')->toArray())
                         ->distinct('iduser')
                         ->count();

        $pending = $tickets->where('idstatus', TicketStatus::Pending)->count();
        $finished = $tickets->where('idstatus', TicketStatus::Finished)->count();
        $progress = $tickets->where('idstatus', TicketStatus::Progress)->count();
        $total    = $tickets->count();

        $totalUser = DB::table('wh_user')->whereNull('deleted_at')->count();
        $lastTicket = Ticket::latest()->first();

        return[
            'pending'       => $pending,
            'finished'      => $finished,
            'progress'      => $progress,
            'users'         => $users,
            'total_user'    => $totalUser,
            'last_ticket'   => !is_null($lastTicket) ?  $lastTicket->created_at : '', 
            'total'         => $total
        ];
    }

    public function getTrendStats($request)
    {
        $start = Carbon::parse($request->start)->startOfDay();
        $end   = Carbon::parse($request->end)->endOfDay();

        $diffIndays = $start->diff($end)->days;

        DB::statement("SET SQL_MODE=''");
        $data = DB::table('wh_ticket as t')
                    ->when($diffIndays > 30, function ($query) {
                        
                        $query->select(DB::raw('count(id) AS `task`'), DB::raw("DATE_FORMAT(created_at, '%m %Y') AS `date`"));
                    }, function ($query) {
                        $query->select(DB::raw('count(id) AS `task`'), DB::raw("DATE_FORMAT(created_at, '%d %m %Y') AS `date`"));
                    })
                    ->groupBy("date")
                    ->orderBy("created_at", 'ASC')
                    ->whereNull('deleted_at')
                    ->whereBetween('created_at', [$start, $end])
                    ->get();

        return $data;

    }

    public function syncFromExcel($request)
    {
        $tasks = json_decode($request->tasks, true);

        foreach($tasks as &$task)
        {
            Ticket::where('id', $task["id"])
                    ->update([
                        'description' =>  $task["descripcion"],
                        'justification' =>  $task["justificacion"]
                    ]);
        }

        return response()->json(['success' => true, 'message' => 'Acción completada con éxito']);
    } 

    public function getDataToPowerBI($request)
    {
        $hasRangeDate = $request->has("start");

        $tickets = Ticket::select(['id', 'name', 'idstatus', 'iditem', 'idteam', 'idpriority', 'code', 'idspot', 'description', 'created_by', 'created_at'])
                        ->with('status:id,name')
                        ->with('priority:id,name')
                        ->with(['spot:id,name,idparent','spot.parent:id,name'])
                        ->with(['item:id,name,idtype','item.tickettype:id,name'])
                        ->with('team:id,name')
                        ->with('createdby:id,firstname,lastname')
                        ->with('users:iduser,firstname,lastname')
                        ->orderBy('id', 'desc')
                        ->when($hasRangeDate, function ($query) use($request) {
                            $start = Carbon::parse($request->start)->startOfDay();
                            $end   = Carbon::parse($request->end)->endOfDay();

                            $query->whereBetween('created_at', [$start, $end]);
                        }, function ($query) {
                            $query->take(1000);
                        })
                        ->get();
        
        
        $data = $tickets->map(function ($task) {

            $newTask = collect($task);

            if(count($newTask['users']) > 0)
            {
                $plucked = collect($newTask['users'])->pluck('fullname');
                $newTask['users'] = join(", ", $plucked->all());
            }

            return $newTask;
        });
        
        return $data;
    }

    public function getTicketResume($request)
    {
        return Ticket::with("spot:id,name")
                     ->with('status:id,name,color')
                     ->with('priority:id,name,color')
                     ->with(['item:id,idtype','item.tickettype:id,name,icon,color'])
                     ->with('team:id,name')
                     ->with('createdby:id,firstname,lastname')
                     ->with('notes')
                     ->with(['notes','notes.createdBy'])
                     ->find($request->idticket);
    }

    public function getTrends($request)
    {
        $start = Carbon::parse($request->start)->startOfDay();
        $end   = Carbon::parse($request->end)->endOfDay();

        $diffIndays = $start->diff($end)->days;

        DB::statement("SET SQL_MODE=''");
        $data = DB::table('wh_ticket as t')
                    ->when($diffIndays > 30, function ($query) {
                        
                        $query->select(DB::raw('count(id) AS `task`'), DB::raw("DATE_FORMAT(created_at, '%m %Y') AS `date`"));
                    }, function ($query) {
                        $query->select(DB::raw('count(id) AS `task`'), DB::raw("DATE_FORMAT(created_at, '%d %m %Y') AS `date`"));
                    })
                    ->groupBy("date")
                    ->orderBy("created_at", 'ASC')
                    ->whereNull('deleted_at')
                    ->whereBetween('created_at', [$start, $end])
                    ->get();

        return $data;

    }
}