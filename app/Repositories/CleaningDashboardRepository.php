<?php

namespace App\Repositories\Cleaning;
use App\Repositories\SpotRepository;
use App\Repositories\UserRepository;
use App\Repositories\ChecklistOptionRepository;
use App\Repositories\Reports\ReportChecklistRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Spot;
use App\Models\User;
use App\Models\Cleaning\CleaningPlan;
use App\Models\Cleaning\CleaningNote;
use App\Models\Cleaning\CleaningChecklist;
use App\Enums\CleaningStatus;
use Carbon\Carbon;
use App\Events\CleaningStatusChange;
use App\Models\Cleaning\CleaningStatus as CleanStatus;

class CleaningDashboardRepository
{
    protected $cleaningPlanRepository;
    protected $spotRepository;
    protected $checklistOptionRepository;
    protected $userRepository;

    public function __construct()
    {
        $this->cleaningPlanRepository    = new CleaningPlanRepository;
        $this->spotRepository            = new SpotRepository;
        $this->reportChecklistRepository = new ReportChecklistRepository;
        $this->checklistOptionRepository = new ChecklistOptionRepository;
        $this->userRepository            = new UserRepository;
    }

    public function getCleaningSpots($request)
    {
        $spots = $this->userRepository->getUserSpots(Auth::id());

        $data = Spot::select(['id', 'idparent', 'name', 'shortname', 'idcleaningstatus', 'idcleaningplan'])
                    ->with("parent:id,name")
                    ->with("cleaningStatus:id,background,icon")
                    ->with(["currentCleaning" => function ($q) {
                        $q->where('idcleaningstatus', '!=', CleaningStatus::Clean);
                     }])
                    ->with(["cleaningPlans" => function ($q) {
                        $q->where('date', '=', Carbon::today())
                          ->where('idcleaningstatus', '!=', CleaningStatus::Clean)
                          ->orderByRaw('-cleanat DESC')
                          ->orderBy('created_at', 'asc');
                     }])
                    ->withCount(["cleaningPlans" => function ($q) {
                        $q->where('date', Carbon::today())
                          ->where('idcleaningstatus', CleaningStatus::Dirty);
                     }])
                    ->where('cleanable', true)
                    ->whereIn('id', $spots)
                    ->when(!is_null($request->idbranch), function ($query) use ($request) {
                        $spots = $this->spotRepository->getChildren($request->idbranch); 
                        return $query->whereIn('id', $spots);
                    })
                    ->when(!is_null($request->idspot), function ($query) use ($request) {
                        return $query->where('id', $request->idspot);
                    })
                    ->when(!is_null($request->idcleaningstatus), function ($query) use ($request) {
                        return $query->where('idcleaningstatus', $request->idcleaningstatus);
                    })
                    ->when(!is_null($request->search), function ($query) use ($request) {
                        return $query->where('name', 'LIKE', '%'. $request->search .'%');  
                    })  
                    ->get();

        $data->map(function ($item){
            $item['parent_name'] = $item->parent->name;
            $item['user'] = $this->getUserCleaning($item);
            return $item;
        });

        return $data;
    }

    public function getCleaningPlans($request)
    {
        $data = CleaningPlan::where('date', '=', Carbon::today())
                            ->where('idspot', $request->idspot)                
                            ->get();
        return $data;
    }

    public function getCleaningChecklist($request)
    {
        $cleaningChecklist = CleaningChecklist::where('idplaner', $request->idplan)->first();

        if(is_null($cleaningChecklist)) return [];

        $checklistData = DB::table('wh_checklist_data')->get(['id', 'data']);

        $collection = collect(json_decode($cleaningChecklist->options));

        foreach($collection as $option)
        {
            $this->reportChecklistRepository->formatData($option, $checklistData);
        }
        
        return $collection->sortBy('position')->values();
    }

    public function getCleaningNotes($request)
    {
        return CleaningNote::where('idplaner', $request->idplan)->get();
    }

    public function changeCleaningStatus($request)
    {
        $spot = Spot::find($request->id);
        $spot->idcleaningstatus = $request->idcleaningstatus;
        $spot->save();

        $cleaningStatus = CleanStatus::find($request->idcleaningstatus);

        if(!is_null($cleaningStatus) && $cleaningStatus->notify == true)
        {
            event(new CleaningStatusChange($spot, $cleaningStatus));
        }

        if(!is_null($spot->idcleaningplan))
        {
            $this->calculateDuration($spot, $request->idcleaningstatus);
        }
        
        return response()->json(['success' => true]);
    }

    public function createCleaningPlan($request) 
    {
        $request['date']       = Carbon::today();
        $request['created_by'] = Auth::id();

        $cleaningPlan = CleaningPlan::create($request->all());

        $this->createCleaningChecklist($cleaningPlan);
        
        return response()->json(['success' => true]);
    }

    private function createCleaningChecklist($cleaningPlan)
    {
        if(!is_null($cleaningPlan->item->idchecklist))
        {
            $checklist = $this->checklistOptionRepository->getChecklistCopy($cleaningPlan->item->idchecklist, $cleaningPlan->id);
            $cleaningPlan->checklists()->create($checklist);
        }
    }

    public function getLastCleaningChange()
    {
        $lastSpot = Spot::orderBy('updated_at', 'desc')->first();

        $lastPlan = CleaningPlan::orderBy('updated_at', 'desc')->first();

        $date = ($lastSpot->updated_at->gt($lastPlan->updated_at) == true ? $lastSpot->updated_at : $lastPlan->updated_at);

        return (is_null($date) ? "null" : $date);
    }

    public function getUserCleaning($item)
    {
        $iduser = null;

        if(!is_null($item->currentCleaning))
        {
            $iduser = (!is_null($item->currentCleaning->iduser) ? $item->currentCleaning->iduser : null);
        }
        else if(!is_null($item->cleaningPlans) && $item->cleaningPlans->count() > 0)
        {
            $iduser = (!is_null($item->cleaningPlans[0]->iduser) ? $item->cleaningPlans[0]->iduser : null);
        }

        return User::select('id', 'firstname', 'lastname', 'urlpicture')->find($iduser);
    }

    public function deleteCleaningPlan($request)
    {
        $cleaningPlan = CleaningPlan::findOrFail($request->id);
        
        $cleaningPlan->delete();

        return response()->json(['success' => true, 'message' => 'Acción completada con éxito']);
    }

    public function initializeCleaningDashboard()
    {
        DB::table('wh_spot')->where('cleanable', 1)->update(['idcleaningstatus' => 1, 'idcleaningplan' => null]);

        return response()->json(['success' => true, 'message' => 'Acción completada con éxito']);
    }

    function calculateDuration($spot, $idcleaningstatus)
    {
        $cleaningPlan = CleaningPlan::find($spot->idcleaningplan);

        switch($idcleaningstatus)
        {
            case CleaningStatus::Cleaning:

                is_null($cleaningPlan->startdate) == true ? $cleaningPlan->startdate = Carbon::now() : $cleaningPlan->resumedate = Carbon::now();
                break;

            case CleaningStatus::Paused:

                $now = Carbon::now();

                if(!is_null($cleaningPlan->resumedate))
                {
                    $cleaningPlan->duration = $now->diffInSeconds($cleaningPlan->resumedate) + $cleaningPlan->duration;
                }
                else
                {
                    $cleaningPlan->duration = $now->diffInSeconds($cleaningPlan->startdate);
                }

                break;

            case CleaningStatus::Clean:

                $now = Carbon::now();

                if($cleaningPlan->idcleaningstatus == CleaningStatus::Cleaning && is_null($cleaningPlan->resumedate))
                {
                    $cleaningPlan->duration = $now->diffInSeconds($cleaningPlan->startdate);
                }
                else if($cleaningPlan->idcleaningstatus == CleaningStatus::Cleaning && !is_null($cleaningPlan->resumedate))
                {
                    $cleaningPlan->duration = $now->diffInSeconds($cleaningPlan->resumedate) + $cleaningPlan->duration;
                }

                $cleaningPlan->finishdate = Carbon::now();

                $spot->idcleaningplan = null;

                break;

            default:

                $now = Carbon::now();

                if(is_null($cleaningPlan->resumedate))
                {
                    $cleaningPlan->duration = $now->diffInSeconds($cleaningPlan->startdate);
                }
                else if(!is_null($cleaningPlan->resumedate))
                {
                    $cleaningPlan->duration = $now->diffInSeconds($cleaningPlan->resumedate) + $cleaningPlan->duration;
                }

                $cleaningPlan->finishdate = Carbon::now();

        }
        
        $cleaningPlan->idcleaningstatus = $idcleaningstatus;
        $cleaningPlan->save();
        $spot->save();
    }
}