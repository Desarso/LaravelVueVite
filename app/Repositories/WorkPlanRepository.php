<?php

namespace App\Repositories;
use Illuminate\Support\Facades\DB;
use App\Models\Planner;
use App\Models\WorkPlan;
use App\Models\Ticket;
use Carbon\Carbon;
use Session;
use Recurr\Rule;  
use Recurr\Transformer\ArrayTransformer;
use Recurr\Transformer\Constraint\AfterConstraint;
use Recurr\Transformer\Constraint\BetweenConstraint;
use App\Enums\App;
use Illuminate\Support\Facades\Auth;

class WorkPlanRepository
{
    protected $checklistOptionRepository;
    protected $ticketRepository;
    protected $userRepository;
    protected $timezone;
    protected $config;

    public function __construct()
    {
        $this->checklistOptionRepository = new ChecklistOptionRepository;
        $this->ticketRepository          = new TicketRepository;
        $this->userRepository            = new UserRepository;
        $this->timezone = 'America/Costa_Rica'; //$this->getPlannerSettings()->timezone;
        $this->config = new ConfigRepository;
    }

    public function getAll()
    {
        $userBranch = $this->userRepository->getUserBranch();

        return WorkPlan::whereIn('idspot',$userBranch)->get();      
    }

    public function getData($request)
    {
        //if(is_null($request->idworkplan)) return [];

        $workPlan = WorkPlan::whereId($request->idworkplan)->first();

        $startOfMonth = Carbon::parse($request->startDate, $this->timezone)->startOfDay()->setTimezone('UTC');
        $endOfMonth   = Carbon::parse($request->endDate, $this->timezone)->endOfDay()->setTimezone('UTC');

        $planners = Planner::with('item:id,name')
                           ->with('spot:id,name')
                           ->where('idworkplan', $request->idworkplan)
                           ->when(!is_null($request->iduser), function ($query) use ($request) {
                                return $query->whereJsonContains('users', $request->iduser);
                           })
                           ->where(function ($query) use ($startOfMonth, $endOfMonth) {
                                $query->where('frequency', '!=', 'NEVER')
                                      ->orWhere(function ($query) use ($startOfMonth, $endOfMonth) {
                                            $query->whereBetween('start', [$startOfMonth, $endOfMonth]);
                                        });
                           })
                           ->get();

        $result = collect();

        foreach($planners as $planner)
        {
            if($planner->frequency != "NEVER")
            {
                $rule        = new Rule();
                $transformer = new ArrayTransformer(); 
                
                $rule->setStartDate($planner->start)
                    ->setEndDate($planner->end)
                    ->setFreq($planner->frequency)
                    ->setInterval($planner->interval);
                    //->setUntil($until);

                switch($planner->frequency)
                {
                    case 'DAILY':
                        break;
                    
                    case 'WEEKLY':
                        $days = explode(',', $planner->by_day); 
                        $rule->setByDay($days);
                        break;

                    case 'MONTHLY':
                        break;
                }

                $betweenConstraint = new BetweenConstraint($startOfMonth, $endOfMonth);

                $collection = $transformer->transform($rule, $betweenConstraint);

                foreach($collection as $item)
                {
                    $ticket = $this->getTicket($planner->id, $item->getStart());

                    $item = [
                        'id'       => random_int(1, 5000),
                        'idplanner'=> $planner->id,
                        'title'    => $planner->item->name,
                        'item'     => $planner->item,
                        'spot'     => $planner->spot,
                        'start'    => Carbon::parse($item->getStart()),
                        'end'      => Carbon::parse($item->getEnd()),
                        'ticket'   => $ticket,
                        'idstatus' => is_null($ticket) ? null : $ticket->idstatus,
                        'type'     => $workPlan->type,
                        'approved' => is_null($ticket) ? null : $ticket->approved,
                        'note'     => is_null($ticket) ? null : $this->getNoteFromChecklist($ticket),
                        'all_day'  => 1
                    ];

                    $result->push($item);
                }
            }
            else
            {
                $ticket = $this->getTicket($planner->id, $planner->start);

                $item = [
                    'id'       => random_int(1, 5000),
                    'idplanner'=> $planner->id,
                    'title'    => $planner->item->name,
                    'item'     => $planner->item,
                    'spot'     => $planner->spot,
                    'start'    => $planner->start,
                    'end'      => $planner->end,
                    'ticket'   => $ticket,
                    'idstatus' => is_null($ticket) ? null : $ticket->idstatus,
                    'type'     => $workPlan->type,
                    'approved' => is_null($ticket) ? null : $ticket->approved,
                    'note'     => is_null($ticket) ? null : $this->getNoteFromChecklist($ticket),
                    'all_day'  => 1
                ];

                $result->push($item);
            }
        }

        return $result;
    }

    public function getWorkPlanAPP($request)
    {
        $timeZone = 'America/Costa_Rica';
        $startOfMonth = Carbon::parse($request->start, $timeZone)->startOfDay()->setTimezone('UTC');
        $endOfMonth   = Carbon::parse($request->end, $timeZone)->endOfDay()->setTimezone('UTC');

        $teams = $this->userRepository->getTeams($request->iduser);
        $spots = $this->userRepository->getUserSpots($request->iduser);

        Auth::loginUsingId($request->iduser);


        $planners = Planner::with('item:id,name')
                            ->with('workplan:id,name,type')
                            ->with('spot:id,name')
                            ->where(function ($query) use ($request, $teams) {
                                $query->whereJsonContains('users', $request->iduser)
                                        ->orWhere(function ($q) use ($teams) {
                                            $q->whereHas('item', function ($q)  use ($teams) {
                                                $q->whereIn('idteam', $teams);
                                            })
                                            ->whereNull('users');
                                         });
                            })
                            ->where(function ($query) use ($startOfMonth, $endOfMonth) {
                                $query->where('frequency', '!=', 'NEVER')
                                      ->orWhere(function ($query) use ($startOfMonth, $endOfMonth) {
                                        $query->whereBetween('start', [$startOfMonth, $endOfMonth]);
                                      });
                            })
                            ->whereIn('idspot', $spots)
                            ->get();

        $result = $this->getFormatPlanner($planners, $startOfMonth, $endOfMonth);

        return $result;
    }


    public function getTicket($idplanner, $stardate)
    {
        return Ticket::select('id', 'idstatus', 'startdate', 'approved')
                    ->with('users:id as iduser,firstname,lastname')
                    ->with("checklists:id,idticket,options")
                    ->whereDate('created_at', '=', $stardate)
                    ->where('idplanner', $idplanner)
                    ->first();
    }

    public function getNoteFromChecklist($ticket)
    {
        if($ticket->checklists->count() == 0) return null;

        $collection = collect(json_decode($ticket->checklists[0]->options));

        $option = $collection->firstWhere('optiontype', 5);

        return (is_null($option) ? null : $option->value);
    }

    public function getWorkPlanList()
    {
        return DB::table('wh_work_plan')->get(['id as value', 'name as text', 'type', 'idspot']);
    }

    public function getPlannerList($request)
    {
        //if(is_null($request->idworkplan)) return [];

        $users = DB::table('wh_user')->get(['id', 'firstname']);

        $workplan = DB::table('wh_work_plan')->first();

        $idworkplan = !is_null($workplan) ? $workplan->id : null;

        $planners = Planner::with('item:id,name')
                           ->when(is_null($request->idworkplan), function ($query) use ($request, $idworkplan) {
                                return $query->where('idworkplan', $idworkplan);
                           }, function ($query) use ($request) {
                                return $query->where('idworkplan', $request->idworkplan);
                           })
                           ->when(!is_null($request->iduser), function ($query) use ($request) {
                                return $query->whereJsonContains('users', $request->iduser);
                           })
                           ->get();

        $planners->map(function ($planner) use ($users) {
            $planner['value'] = $planner->id;
            $planner['text']  = $planner->item->name;
            $planner['users_text'] = $this->getUsers($users, $planner->users);
            return $planner;
        });
    
        return $planners;
    }

    private function getUsers($data, $users)
    {
        $plucked = $data->whereIn('id', json_decode($users))->pluck('firstname');

        return join(", ", $plucked->all());
    }

    public function create($request)
    {
        $workPlan = WorkPlan::create($request->all());

        return response()->json(['success' => true, 'model' => $workPlan]);
    }

    public function update($request)
    {
        $workPlan = WorkPlan::find($request->id);

        $workPlan->fill($request->all())->save();

        return response()->json(['success' => true, 'model' => $workPlan]);
    }

    public function delete($request)
    {
        $workPlan = WorkPlan::findOrFail($request->id);

        $hasRelations = $this->config->checkRelations($workPlan, ['planners']);

        if(!$hasRelations)
        {
            $workPlan->delete();
            return response()->json(['success' => true, 'model' => $workPlan]);
        }
        else
        {
            return response()->json(['success' => false, 'model' => $workPlan, 'relations' => $hasRelations]);
        }
    }

    public function restore($request)
    {
        $workPlan = WorkPlan::withTrashed()->findOrFail($request->id);

        $workPlan->restore();

        return response()->json(['success' => true, 'model' => $workPlan]);
    }

    public function getPlannerToEvaluateAPP($request)
    {
        $startOfMonth = Carbon::today()->startOfDay();
        $endOfMonth = Carbon::today()->endOfDay();
                    
        $planners = Planner::with('item:id,name')
                            ->with('workplan:id,name,type')
                            ->with('spot:id,name')
                            ->where('idworkplan', $request->idevaluate)
                            ->where(function ($query) use ($startOfMonth, $endOfMonth) {
                                $query->where('frequency', '!=', 'NEVER')
                                      ->orWhere(function ($query) use ($startOfMonth, $endOfMonth) {
                                        $query->whereBetween('start', [$startOfMonth, $endOfMonth]);
                                      });
                            })
                            ->get();

        $data = $this->getFormatPlanner($planners, $startOfMonth, $endOfMonth);

        return response()->json(['success' => true, 'data' => $data]);
    }

    private function getFormatPlanner($planners, $startOfMonth, $endOfMonth)
    {
        $result = collect();
        $users = DB::table('wh_user')->get(['id', 'firstname']);
            

        foreach($planners as $planner)
        {
            if($planner->frequency != "NEVER")
            {
                $rule        = new Rule();
                $transformer = new ArrayTransformer(); 


                $rule->setStartDate($planner->start)
                    ->setEndDate($planner->end)
                    ->setFreq($planner->frequency)
                    ->setInterval($planner->interval);

                switch($planner->frequency)
                {
                    case 'DAILY':
                        break;
                    
                    case 'WEEKLY':
                        $days = explode(',', $planner->by_day); 
                        $rule->setByDay($days);
                        break;
                }

                $betweenConstraint = new BetweenConstraint($startOfMonth, $endOfMonth);

                $collection = $transformer->transform($rule, $betweenConstraint);

                foreach($collection as $item)
                {
                    $item = [
                        'id'            => $planner->id,
                        'item'          => $planner->item,
                        'spot'          => $planner->spot,
                        'workplan'      => $planner->workplan,
                        'description'   => $planner->description,
                        'stardate'      => $item->getStart(),
                        'ticket'        => $this->getTicket($planner->id, $item->getStart()),
                        'users_text'    => $this->getUsers($users, $planner->users),
                        'idevaluate'    => $planner->idworkplan_evaluate
                    ];

                    $result->push($item);
                }
            }
            else
            {
                $item = [
                    'id'            => $planner->id,
                    'item'          => $planner->item,
                    'spot'          => $planner->spot,
                    'workplan'      => $planner->workplan,
                    'description'   => $planner->description,
                    'stardate'      => Array("date" => $planner->start),
                    'ticket'        => $this->getTicket($planner->id, $planner->start),
                    'users_text'    => $this->getUsers($users, $planner->users),
                    'idevaluate'    => $planner->idworkplan_evaluate 
                ];

                $result->push($item);
            }
        }

        $result = $result->sortByDesc(function($plan) {
            return $plan['stardate'];
        })->values()->all();

        return $result;
    }

    public function getPlannerToNotify() {

        $startOfDay = Carbon::today()->startOfDay();
        $endOfDay = Carbon::today()->endOfDay();
                    
        $planners = Planner::where(function ($query) use ($startOfDay, $endOfDay) {
                                $query->where('frequency', '!=', 'NEVER')
                                      ->orWhere(function ($query) use ($startOfDay, $endOfDay) {
                                        $query->whereBetween('start', [$startOfDay, $endOfDay]);
                                      });
                            })
                            ->where(function ($query) use ( $endOfDay) {
                                $query->where('until','>', $endOfDay)
                                      ->orWhereNull('until');
                            })
                            ->get();

        $data = $this->getFormatPlannerNotify($planners, $startOfDay, $endOfDay);
        return $data;
    }

    private function getFormatPlannerNotify($planners, $startOfMonth, $endOfMonth)
    {
        $resultUsers = collect();

        foreach($planners as $planner)
        {
            $users = array();

            if($planner->frequency != "NEVER")
            {
                $rule        = new Rule();
                $transformer = new ArrayTransformer(); 


                $rule->setStartDate($planner->start)
                    ->setEndDate($planner->end)
                    ->setFreq($planner->frequency)
                    ->setInterval($planner->interval);

                switch($planner->frequency)
                {
                    case 'DAILY':

                        break;
                    
                    case 'WEEKLY':
                        $days = explode(',', $planner->by_day); 
                        $rule->setByDay($days);
                        break;
                }

                $betweenConstraint = new BetweenConstraint($startOfMonth, $endOfMonth);

                $collection = $transformer->transform($rule, $betweenConstraint);

                foreach($collection as $item)
                {
                    $ticket = $this->getTicket($planner->id, $item->getStart());
                    
                    if (is_null($ticket)) {
                        $users = json_decode($planner->users);
                    }
                }
            }
            else
            {

                $ticket = $this->getTicket($planner->id, $planner->start);
                    
                if (is_null($ticket)) {
                    $users = json_decode($planner->users);
                }
            }

            foreach ((array)$users as $user)
            {
                $result = $resultUsers->firstWhere('iduser', $user);

                if(is_null($result))
                {
                    $item = (object) ["iduser" => $user, "tickets" => 1];
                    $resultUsers->push($item);
                }
                else
                {
                    $result->tickets += 1;
                }
            }
        }

        return $resultUsers;
    }

    public function checkPendingPlanner($request)
    {
    
        $startOfDay = Carbon::today()->startOfDay()->format('Y-m-d H:00');
        $endOfDay = Carbon::today()->endOfDay()->format('Y-m-d H:00');
        $request->request->add(['start' => $startOfDay]);
        $request->request->add(['end' => $endOfDay]);

        $data =  collect($this->getWorkPlanAPP($request));
        $result =  $data->whereNull('ticket')->count();

        return response()->json(['success' => true, 'result' => $result]);
    }

    public function copyWorkPlan($request)
    {
        $planner = WorkPlan::create($request->all());

        $this->replicateWorkPlanTasks($planner, $request->idworkplan);

        return response()->json(["success" => true, "model" => $planner]);
    }
    
    public function replicateWorkPlanTasks($newPlanner, $idcopyPlanner)
    {
        $planners = Planner::where('idworkplan', $idcopyPlanner)->get();

        foreach($planners as $plan)
        {
            $newPlan = $plan->replicate();
            $newPlan->fill([
                'idworkplan' => $newPlanner->id,
                'idspot' => $newPlanner->idspot,
                'users' => null
            ]);
            $newPlan->save();
        }
    }
}
