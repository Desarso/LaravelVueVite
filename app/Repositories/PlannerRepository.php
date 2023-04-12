<?php

namespace App\Repositories;
use Illuminate\Support\Facades\DB;
use App\Models\Planner;
use App\Models\Ticket;
use Carbon\Carbon;
use Session;
use Recurr\Rule;  
use Recurr\Transformer\ArrayTransformer;
use Recurr\Transformer\Constraint\AfterConstraint;
use App\Enums\App;

class PlannerRepository
{
    protected $checklistOptionRepository;
    protected $ticketRepository;
    protected $timezone;

    public function __construct()
    {
        $this->checklistOptionRepository = new ChecklistOptionRepository;
        $this->ticketRepository          = new TicketRepository;
        $this->config = new ConfigRepository;
        $this->timezone = 'America/Costa_Rica'; //$this->getPlannerSettings()->timezone;
    }

    public function getAll()
    {
        return Planner::withCount('tickets')->get();
    }

    public function getAllScheduler()
    {
        $planners = Planner::with("item:id,name")->where("enabled", true)->get();

        $planners->map(function ($planner){
            $planner['title']          = $planner->item->name;
            $planner['recurrenceRule'] = $this->getRecurrenceFormat($planner);
            return $planner;
        });
    
        return $planners;
    }

    public function getList()
    {
        return Planner::get(['id as value', 'name as text']);
    }

    public function create($request)
    {
        $this->formatJson($request, "users");
        $this->formatJson($request, "copies");
        $this->formatJson($request, "tags");
        $this->formatDays($request);

        foreach((array)$request->spots as $spot)
        {
            $request->merge(['idspot' => $spot]);
            $planner = Planner::create($request->all());
        }
    }

    public function update($request)
    {
        $planner = Planner::find($request->id);

        $hasRelations = $this->config->checkRelations($planner, ['tickets']);

        if(!$hasRelations)
        {
            $this->formatJson($request, "users");
            $this->formatJson($request, "copies");
            $this->formatJson($request, "tags");
            $this->formatDays($request);
    
            $planner = Planner::find($request->id);
            $planner->fill($request->all())->save(); 
               
            return response()->json(['success' => true, 'model' => $planner]);
        }
        else
        {
            return response()->json(['success' => false, 'model' => $planner, 'relations' => $hasRelations]);
        }
    }

    public function delete($request)
    {
        $planner = Planner::findOrFail($request->id);

        $planner->delete();

        return response()->json(['success' => true, 'model' => $planner]);
    }

    public function enabledPlanner($request)
    {
        return Planner::where('id', $request->id)->update(['enabled' => $request->enabled]);
    }

    //Funcion para el formato de recurrencia del calendario
    private function getRecurrenceFormat($planner)
    {
        $expression = sprintf('FREQ=%s;', $planner->frequency);

        if($planner->interval) {
            $expression .= sprintf('INTERVAL=%s;', $planner->interval);
        }

        if($planner->by_day){
            $expression .= sprintf('BYDAY=%s;', $planner->by_day);
        }

        if($planner->until){
            $expression .= sprintf('UNTIL=%s;', $planner->until);
        }

        return $expression;
    }

    //Función que obtiene los planners activos y verifica si es necesario crear una tarea para el día actual según la regla del planner.
    public function generateRecurringTickets()
    {
        $rule        = new Rule();
        $transformer = new ArrayTransformer(); 

        $until = Carbon::today($this->timezone)->endOfDay();
        $today = Carbon::today($this->timezone);

        $planners = Planner::where(["enabled" => 1, "isfinished" => 0])->get();

        //dd($planners);

        foreach($planners as $planner)
        {
            $rule->setStartDate($planner->start)
                 ->setEndDate($planner->end)
                 ->setFreq($planner->frequency)
                 ->setInterval($planner->interval)
                 ->setUntil($until);

            switch($planner->frequency)
            {
                case 'DAILY':

                    break;
                
                case 'WEEKLY':
                    $days = explode(',', $planner->by_day); 
                    $rule->setByDay($days);
                    break;
            }

            $afterConstraint = new AfterConstraint($today);

            $collection = $transformer->transform($rule, $afterConstraint);
   
            if(count($collection) > 0)
            {
                $start = Carbon::instance($collection[0]->getStart());
                $end   = Carbon::instance($collection[0]->getEnd());

                $this->createTicketFromPlanner($planner, $start, $end);
            }

            if(!is_null($planner->until) && $today->greaterThanOrEqualTo($planner->until))
            {
                $planner->isfinished = true;
                $planner->save();
            }
        }
    }

    //Creamos el ticket a partir del planner
    private function createTicketFromPlanner($planner, $start, $end)
    {
        if($this->checkRecurringTicketsCreatedForToday($planner->id, $start, $end)) return;

        $idsuperadmin = DB::table('wh_user')->where('issuperadmin', true)->first()->id;

        $data = $planner->only("iditem", "idspot", "description");

        $data["idplanner"]  = $planner->id;
        $data['uuid']       = uniqid();
        $data["name"]       = $planner->item->name;
        $data["idteam"]     = $planner->item->idteam;
        $data["idasset"]    = $planner->idasset;
        $data['start']      = $start;
        $data["end"]        = $end;
        $data["created_by"] = $idsuperadmin;
        $data["updated_by"] = $idsuperadmin;

        if($planner->business_days != 0)
        {
            $duedate = $start;
            $data['duedate'] = $duedate->addDays($planner->business_days);
        }

        $ticket = Ticket::create($data);
        $ticket->unsetEventDispatcher();

        $ticket->users()->wherePivot('copy', 0)->attach(json_decode($planner->users));
        $ticket->usersCopy()->wherePivot('copy', 1)->attach($this->ticketRepository->getFormatUsersCopy((array) json_decode($planner->copies)));
        $ticket->tags()->attach($this->ticketRepository->getFormatTags((array) json_decode($planner->tags)));

        if(!is_null($planner->item->idchecklist))
        {
            $checklistCopy = $this->checklistOptionRepository->getChecklistCopy($planner->item->idchecklist, $ticket->id);
            $ticket->checklists()->create($checklistCopy);
        }
    }

    //Función que verifica si ya existe un ticket según el planner y las fechas de inicio y fin.
    private function checkRecurringTicketsCreatedForToday($idplanner, $start, $end)
    {
        $first = Ticket::where("idplanner", $idplanner)
                        ->where('start', $this->getDateInUTC($start))
                        ->where('end', $this->getDateInUTC($end))
                        ->first();

        return is_null($first) ? false : true;
    }

    private function getDateInUTC($date)
    {
        return Carbon::parse($date, $this->timezone)->setTimezone(config('app.timezone'));
    }

    //Funcion para formatear en json
    private function formatJson($request, $key)
    {
        if($request->has($key))
        {
            $data = (array) $request->$key;
            $request->merge([$key => json_encode($data)]);
        }
        else
        {
            $request->merge([$key => null]);
        }

        return $request;
    }

    //Funcion para formatear los días en un string separado por ","
    private function formatDays($request)
    {
        if($request->has("days"))
        {
            $days = implode(",", $request->days);
            $request->merge(['by_day' => $days]);
        }
        else
        {
            $request->merge(['by_day' => null ]);
        }

        return $request;
    }

    public function getPlannerSettings() 
    {
        $settings = DB::table('wh_app')->where('id', App::Planner)->first()->settings;
        return json_decode($settings);
    }

    public function createPlannerTask($request)
    {
        if($request->repeat == "false")
        {
            $this->setNeverFrecuency($request);
        }

        $request->merge(['start' => ($request->date . ' ' . $request->start)]);
        $request->merge(['end' => ($request->date . ' ' . $request->end)]);

        $this->formatJson($request, "users");
        $this->formatDays($request);

        $planner = Planner::create($request->all());

        return response()->json(['success' => true, 'model' => $planner]);
    }

    public function updatePlannerTask($request)
    {
        if($request->repeat == "false")
        {
            $this->setNeverFrecuency($request);
        }

        $request->merge(['start' => ($request->date . ' ' . $request->start)]);
        $request->merge(['end' => ($request->date . ' ' . $request->end)]);
        
        $this->formatJson($request, "users");
        $this->formatDays($request);

        $planner = Planner::find($request->id);
        $planner->fill($request->all())->save();  
        
        return response()->json(['success' => true, 'model' => $planner]);
    }

    public function setNeverFrecuency($request)
    {
        $request->merge(['frequency' => 'NEVER']);
        $request->merge(['interval' => 0]);
        $request->merge(['until' => null]);
        $request->merge(['by_day' => null]);
        $request->merge(['by_month_day' => null]);

        if(!$request->has('by_day'))
        {
            $request->add(['by_day' => null]);
        }

        return $request;
    }
}
