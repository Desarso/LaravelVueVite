<?php

namespace App\Repositories\Cleaning;
use Illuminate\Support\Facades\Auth;
use App\Repositories\SpotRepository;
use App\Models\Cleaning\CleaningPlan;
use App\Models\Cleaning\CleaningStatus;
use App\Models\Spot;
use App\Models\User;
use App\Enums\TicketStatus;
use App\Enums\CleaningStatus as StatusEnum;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Enums\App;
use App\Events\CleaningStatusChange;
use Session;
use Helper;


class CleaningPlanRepository
{
    protected $spotRepository;

    public function __construct()
    {
        $this->spotRepository = new SpotRepository;
    }

    public function getCleaningProducts() 
    {
        $cleaning_products = $this->getCleaningSettings()->cleaning_products;
        
        return DB::table('wh_item')  
                 ->whereIn('idtype', $cleaning_products)
                 ->pluck('id')
                 ->toArray();
    }

    public function getCleaningPlan($request)
    {
        $cleaning_products = $this->getCleaningProducts();

        return Spot::select(['id', 'name', 'shortname', 'idcleaningstatus', 'idcleaningplan'])
                   ->with("cleaningStatus:id,background,icon")
                   ->with("currentCleaning")
                   ->with(["cleaningPlans" => function($q) {                       
                        $q->select('id', 'idspot', 'idcleaningstatus')
                          ->where('date', Carbon::today());         
                   }])
                   ->withCount(["tickets" => function($q) use ($cleaning_products) {
                        $q->whereIn('iditem', $cleaning_products)
                          ->where('idstatus', '!=', TicketStatus::Finished);
                   }])  
                   ->where('cleanable', true)    
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
                   ->when(!is_null($request->iduser), function ($query) use ($request) {
                        return $query->whereHas('currentCleaning', function ($q) use($request) {
                                    $q->where('iduser', $request->iduser); 
                                });
                   })  
                   ->get();
    }

    public function getSpotCleaningPlan($request) 
    {        
        return CleaningPlan::with("cleaningticket.status", "cleaningticket.item")
            ->where('date', '=', Carbon::today())
            ->where('idspot',$request->idspot)                
            ->orderBy('sequence')
         ->get();
    }

    public function createCleaningPlan()
    {
        $dow = Carbon::now()->setTimezone(Session::get('local_timezone'))->dayOfWeek;

        $schedules = DB::table('wh_cleaning_schedule as s')->where('enabled', true)
                        ->where('deleted_at', null)
                        ->whereJsonContains('dow', $dow)
                        ->select('s.iduser', 's.idspot', 's.iditem', 's.time', 's.sequence')
                        ->get();

        $today = Carbon::today();

        $sequence = 1;

        foreach($schedules as $schedule)
        {
            $time = (is_null($schedule->time) ? null : Carbon::parse($schedule->time)->setTimezone('America/Costa_Rica'));

            if(!CleaningPlan::where('date', $today)->where('idspot', $schedule->idspot)->where('cleanat', $time)->exists())
            {
                $plan = new CleaningPlan();
                $plan->idspot  = $schedule->idspot;
                $plan->iduser  = $schedule->iduser;
                $plan->date    = $today;
                $plan->cleanat = $time;
                $plan->iditem  = $schedule->iditem;

                if ($schedule->sequence == 0)
                {
                    $plan->sequence = $sequence++;
                }
                else
                {
                    $plan->sequence = $schedule->sequence;
                }

                $plan->save();
            }
        }
    }

    public function initializeCleaning()
    {
        return DB::table('wh_spot')
            ->where('cleanable', 1)
            ->update(['idcleaningstatus' => 1]);
    }

    public function updateSpotCleaningInfo($request)
    {
        $model = Spot::find($request->idspot);
        if ($model != null) {
            $model->idcleaningstatus = $request->idcleaningstatus;                        
            return $model->save();
        }
        return "Model not found";
    }


    public function saveCleaningPlanSequence($request)
    {
        $i = 1 ;
        foreach( $request->data as $d) {
            if ($d == null) continue;
            $model = CleaningPlan::find((int)$d);            
            $model->sequence = $i++;             
            $model->save();
        }
    }

    // Cleaning-assign 
    public function getCleaningStaffWithPlans($request) 
    {
        $settings = $this->getCleaningSettings();

        $cleaning_teams = $settings->cleaning_teams;

        $data = User::select('id')
                    ->with(['cleaningPlans' => function($query){
                        $query->with('spot:id,idcleaningstatus')->select('id', 'idspot', 'iduser', 'idcleaningstatus')->where('date', '=', Carbon::today());
                    }])
                    ->whereHas('teams', function ($query) use($cleaning_teams) {
                        $query->whereIn('idteam', $cleaning_teams); 
                    })
                    ->get();

        $data->map(function ($item) {

            $total = $item->cleaningPlans->count();
            $clean = $item->cleaningPlans->whereIn('idcleaningstatus', [StatusEnum::Clean, StatusEnum::Inspected])->count();
            $dirty = $item->cleaningPlans->where('idcleaningstatus', StatusEnum::Dirty)->count();
            
            $item->total      = $total;
            $item->dirty_plan = $dirty;
            $item->clean      = $clean;
            $item->average    = ($total == 0 ? 0 : round(( $clean / $total) * 100));

            return $item;
        });

        return $data;
    }

    public function getAvailableSpots($request) 
    {
        return Spot::select('id','name')
                   ->withCount(["cleaningPlans" => function ($query) {
                        $query->where('date', '=', Carbon::today());
                   }])
                   ->where('cleanable', true)
                   ->orderBy('cleaning_plans_count')
                   ->get();
    }

    
    public function assignCleaning($request) 
    {
        $settings = $this->getCleaningSettings();

        $cleaningPlan = CleaningPlan::create(
            [
                'iduser'     => $request->iduser,
                'idspot'     => $request->idspot,
                'iditem'     => $settings->default_cleaning_item,
                'date'       => Carbon::today(),
                'created_by' => Auth::id()
            ]);

        return response()->json(['success' => true]);
    }

    public function moveCleaningPlan($request) 
    {
        $cleaningPlan = CleaningPlan::find($request->id);
        $cleaningPlan->iduser = $request->iduser;
        $cleaningPlan->save();
        
        return response()->json(['success' => true]);
    }
    
    public function findCleaningPlan($request) 
    {
        return CleaningPlan::find($request->id);
    }

    public function editCleaningPlan($request) 
    {
        if(is_null($request->id))
        {
            $request['date'] = Carbon::today();
            $cleaningPlan = CleaningPlan::create($request->all());
        }
        else
        {
            $cleaningPlan = CleaningPlan::find($request->id);
            $cleaningPlan->fill($request->all());
            $cleaningPlan->save();
        }
        
        return response()->json(['success' => true]);
    }

    public function getCleaningSettings() 
    {
        $settings = DB::table('wh_app')->where('id', App::Cleaning)->first()->settings;
        return json_decode($settings);
    }

    public function deleteCleaningPlan($request)
    {
        $cleaningPlan = CleaningPlan::findOrFail($request->id);
        $cleaningPlan->delete();

        return response()->json(['success' => true, 'message' => 'Acción completada con éxito']);
    }

    public function getCleaningItems() 
    {
        return DB::table('wh_item as i')
                 ->join('wh_ticket_type as t', 't.id', '=', 'i.idtype')
                 ->where('i.isglitch', false)
                 ->where('t.iscleaningtask', true)
                 ->whereNull('i.deleted_at')
                 ->select('i.id as value', 'i.name as text')
                 ->get();
    }

    public function getCleaningSpots() 
    {
        return DB::table('wh_spot')
                 ->where('cleanable', true)
                 ->whereNull('deleted_at')
                 ->select('id as value', 'name as text')
                 ->get();
    }

    public function getCleaningStaff() 
    {
        $settings = $this->getCleaningSettings();

        $cleaning_teams = $settings->cleaning_teams;

        return User::select('id as value', 'urlpicture', DB::raw('CONCAT(firstname, " ", lastname) AS text'))
                    ->whereHas('teams', function ($query) use($cleaning_teams) {
                        $query->whereIn('idteam', $cleaning_teams); 
                    })
                    ->get();
    }
    // Cleaning-assign 

    public function chageCleaningPlanStatusAPP($request)
    {
        $result = false;
        session(['iduser' => $request->authuser]);

        if (!is_null($request->idplan)) {
            $plan = CleaningPlan::find($request->idplan);
            $this->calculateDuration($plan, $request->idcleaningstatus);
            $plan->idcleaningstatus = $request->idcleaningstatus;    
            
            if ($plan->iduser == 0) {
                $plan->iduser = $request->authuser;
            }
            
            $plan->save();
        }

        $spot = Spot::find($request->idspot);
        $spot->idcleaningstatus = $request->idcleaningstatus;                        
        if (!is_null($request->idplan)) {
            $spot->idcleaningplan = ($plan->idcleaningstatus != StatusEnum::Clean) ? $plan->id : null;
        }                        
        $spot->save();

        $cleaningStatus = CleaningStatus::find($request->idcleaningstatus);

        if(!is_null($cleaningStatus) && $cleaningStatus->notify == true)
        {
            event(new CleaningStatusChange($spot, $cleaningStatus));
        }

        $plan = $this->findCleaningPlanById($request);

        return response()->json([
            "result" => $result,
            "plan" => $plan,
        ]);
    }

    public function chageRoomStatusAPP($request)
    {
        $result = false;
        session(['iduser' => $request->authuser]);

        $spot = Spot::find($request->idspot);
        $spot->idcleaningstatus = $request->idcleaningstatus;                        
        $spot->save();

        $cleaningStatus = CleaningStatus::find($request->idcleaningstatus);

        if(!is_null($cleaningStatus) && $cleaningStatus->notify == true)
        {
            event(new CleaningStatusChange($spot, $cleaningStatus));
        }

        if (!is_null($spot->idcleaningplan)) {

            session(['iduser' => $request->authuser]);

            $plan = CleaningPlan::find($spot->idcleaningplan);
            
            if ($plan) {
                
                $this->calculateDuration($plan, $request->idcleaningstatus);
                $plan->idcleaningstatus = $request->idcleaningstatus;    
                
                if ($plan->iduser == 0) {
                    $plan->iduser = $request->authuser;
                }
                
                $plan->save();
            }
        }

        return response()->json([
            "result" => $result,
        ]);
    }

    public function getMyCleaningPlanAPP($request)
    {
        $items = CleaningPlan::select('id','iditem','idspot','cleanat','idcleaningstatus', 'iduser', 'created_by')
                    ->with('spot:id,name,idcleaningstatus')
                    ->with('item:id,name')
                    ->with('cleaningStatus:id,name,background,icon')
                    ->where(function ($query) use ($request) {
                        $query->where('iduser', '=', $request->iduser)
                              ->orWhereNull('iduser');
                     })
                    ->where('date', Carbon::today())
                    ->orderByRaw('FIELD(idcleaningstatus, 7,2,4,1,5,6,3)')
                    ->get();

        $items->map(function ($item) {
            $iconOriginal = $item->cleaningStatus->getOriginal('icon');
            $item->cleaningStatus->icon = helper::formatIcon($iconOriginal);
            return $item;
        });

        return $items;
    }

    function calculateDuration($cleaningPlan, $idstatus)
    {
        switch ($idstatus)
        {
            case StatusEnum::Cleaning:

                is_null($cleaningPlan->startdate) == true ? $cleaningPlan->startdate = Carbon::now() : $cleaningPlan->resumedate = Carbon::now();
                break;

            case StatusEnum::Paused:

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

            default:

                $now = Carbon::now();
                
                if($cleaningPlan->idcleaningstatus == StatusEnum::Cleaning && is_null($cleaningPlan->resumedate))
                {
                    
                    if(!is_null($cleaningPlan->startdate))
                    {
                        $cleaningPlan->duration = $now->diffInSeconds($cleaningPlan->startdate);
                    }
                }
                else if($cleaningPlan->idcleaningstatus == StatusEnum::Cleaning && !is_null($cleaningPlan->resumedate))
                {
                    $cleaningPlan->duration = $now->diffInSeconds($cleaningPlan->resumedate) + $cleaningPlan->duration;
                }

                $cleaningPlan->finishdate = Carbon::now();

            break;
        }
    }

    private function findCleaningPlanById($request)
    {
        $query = CleaningPlan::select('id','iditem','idspot','iduser','cleanat','idcleaningstatus', 'created_by')
                            ->with('item:id,name')
                            ->with('spot:id,name,idcleaningstatus')
                            ->with('user:id,firstname,lastname,urlpicture')
                            ->with('cleaningStatus:id,name,background,icon')
                            ->with('createdBy:id,firstname,lastname,urlpicture');

        if ($request->has('idplan')) {
            $query->where('id', '=', $request->idplan);
        } else {
            $query->where('idcleaningstatus', '!=', StatusEnum::Clean)
                  ->where('date', '=', Carbon::today())->where('idspot', '=', $request->idspot)
                  ->where(function ($query) use ($request) {
                    $query->where('iduser', '=', $request->iduser)
                          ->orWhereNull('iduser');
                  });
        }
        
        return $query->first();
    }

    public function createPlanFromSliderPlanAPP($request)
    {
        $plan = $this->findCleaningPlanById($request);

        session(['iduser' => $request->authuser]);
        
        if (is_null($plan)) {
        
            $plan = new CleaningPlan();
            $plan->idspot = $request->idspot;
            $plan->iditem = $request->iditem; 
            $plan->iduser = $request->iduser;
            $plan->idcleaningstatus = StatusEnum::Dirty;
            $plan->created_by = $request->authuser;
            $plan->date = Carbon::today();
            $plan->sequence = 1;
            $plan->save();
            $plan->refresh();
        } else {
            if ($plan->iduser == 0) {
                $plan->iduser = $request->authuser;
            }
            
            $plan->save();
            $plan->refresh();
        }

        if (StatusEnum::Dirty == $plan->idcleaningstatus) {

            $request->merge(['idplan' => $plan->id]);
            $request->merge(['idcleaningstatus' => StatusEnum::Cleaning]);
            $this->chageCleaningPlanStatusAPP($request);
            $plan = $this->findCleaningPlanById($request);
        }
        
        
        return response()->json([
            "sucess" => true,
            "plan" => $plan 
        ]);
    }

    public function createCleaningPlanAPP($request)
    {
        session(['iduser' => $request->authuser]);
        
        $plan = new CleaningPlan();
        $plan->idspot = $request->idspot;
        $plan->iditem = $request->iditem; 
        $plan->iduser = ($request->iduser != 'null') ? $request->iduser : null;
        if($request->cleanat != 'null') $plan->cleanat = $request->cleanat;
        $plan->idcleaningstatus = ($request->isRush == 'true') ? StatusEnum::Rush : StatusEnum::Dirty;
        $plan->created_by = $request->authuser;
        $plan->date = Carbon::today();
        $plan->sequence = 1;
        $plan->save();

        if($request->isRush == 'true') { 
            $spot = Spot::find($request->idspot);
            $spot->idcleaningstatus = StatusEnum::Rush;                      
            $spot->save();
        }


        return response()->json([
            "sucess" => true,
            "plan" => $plan 
        ]);
    }

    public function getCleaningPlanAPP($request)
    {
        $plan = CleaningPlan::select('id','iditem','idspot','cleanat','idcleaningstatus', 'created_by')
                            ->with('spot:id,name,idcleaningstatus')
                            ->with('cleaningStatus:id,name,background,icon')
                            ->where('id', '=', $request->idplan)
                            ->get();

        $plan->map(function ($item) {
            $iconOriginal = $item->cleaningStatus->getOriginal('icon');
            $item->cleaningStatus->icon = helper::formatIcon($iconOriginal);
            return $item;
        });

        return response()->json([
            "sucess" => true,
            "plan" => $plan 
        ]);
    }

    public function getCleaningPlanBySpotAPP($request)
    {
        $statusOrder = [StatusEnum::Cleaning, StatusEnum::Paused, StatusEnum::Dirty];
        $cliningStatus = CleaningStatus::whereNotIn('id', $statusOrder)->get();
        $cliningStatus = $cliningStatus->pluck('id')->toArray();
        $statusOrder = array_merge($statusOrder, $cliningStatus);

        $plan = CleaningPlan::select('id','iditem','idspot', 'iduser' ,'cleanat','idcleaningstatus', 'created_by')
                            ->with('item:id,name')
                            ->with('spot:id,name,idcleaningstatus')
                            ->with('user:id,firstname,lastname,urlpicture')
                            ->with('createdBy:id,firstname,lastname,urlpicture')
                            ->with('cleaningStatus:id,name,background,icon')
                            ->where('idspot', '=', $request->idspot)
                            ->where('date', Carbon::today())
                            ->orderByRaw('FIELD(idcleaningstatus,'. implode(",", $statusOrder) .')')
                            ->get();

        $plan->map(function ($item) {
            $iconOriginal = $item->cleaningStatus->getOriginal('icon');
            $item->cleaningStatus->icon = helper::formatIcon($iconOriginal);
            return $item;
        });

        return response()->json([
            "sucess" => true,
            "plan" => $plan 
        ]);
    }

    public function deleteCleaningPlanAPP($request)
    {
        $success = false;
        $cleaningPlan = CleaningPlan::find($request->idplan);
        Auth::loginUsingId($request->authuser);
        
        if ($cleaningPlan) {
            $success = true;
            $cleaningPlan->delete();
        }
        
        return response()->json(['success' => $success]);
    }

    public function assingCleaningPlanAPP($request)
    {
        $success = false;
        $cleaningPlan = CleaningPlan::find($request->idplan);
        Auth::loginUsingId($request->authuser);
        
        if (!is_null($cleaningPlan)) {
            $cleaningPlan->iduser = $request->newUser;
            $cleaningPlan->save();
            $success = true;
        }
        
        return response()->json(['success' => $success]);
    }
}
