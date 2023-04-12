<?php

namespace App\Repositories\Cleaning;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Ticket;
use App\Models\Spot;
use App\Models\Cleaning\CleaningPlan;
use App\Enums\TicketStatus;
use App\Enums\CleaningStatus;
use Helper;
use Carbon\Carbon;

class CleaningDashboardRepository
{
    protected $cleaningPlanRepository;

    public function __construct()
    {
        $this->cleaningPlanRepository = new CleaningPlanRepository;
    }

    public function getEssentialProducts()
    {
        $cleaning_products = $this->cleaningPlanRepository->getCleaningSettings()->cleaning_products;

        return Ticket::select(['id','name', 'description', 'iditem', 'idspot', 'idstatus', 'quantity', 'created_by', 'created_at'])
                     ->with('spot:id,name,shortname')
                     ->with('item:id,idtype')
                     ->where('idstatus', '!=', TicketStatus::Finished)
                     ->whereHas('spot', function ($query){
                        $query->where('cleanable', true);
                     })
                     ->whereHas('item', function ($query) use ($cleaning_products) {
                        $query->where('idtype', $cleaning_products);
                     })
                     ->latest()
                     ->get();
    }

    public function getCleaningData()
    {
        $data = DB::table('wh_cleaning_status as cs')
                  ->leftJoin('wh_cleaning_plan as cp', function ($join) {
                        $join->on('cp.idcleaningstatus', '=', 'cs.id')
                             ->where('cp.date', Carbon::today());
                  })                                  
                  ->select('cs.id', 'cs.name', 'cs.background', 'cs.name', DB::raw('count(cp.idcleaningstatus) as cleaning_count'))
                  ->groupBy('cs.id')
                  ->orderBy('cleaning_count', 'Desc')
                  ->get();

        return $data;
    }

    public function getCleaningPlans($request)
    {
        if(is_null($request->idspot)) return [];

        return CleaningPlan::where('date', Carbon::today())
                           ->where('idspot', $request->idspot)
                           ->get();
    }

    public function createOrUpdateCleaningPlan($request) 
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
        
        return $cleaningPlan->fresh();
    }

    public function changeCleaningStatus($request) 
    {
        $spot = Spot::find($request->id);
        $spot->idcleaningstatus = $request->idcleaningstatus;
        $spot->save();

        /*
        if(!is_null($spot->idcleaningplan))
        {
            $spot->currentCleaning()->update(["idcleaningstatus" => $request->idcleaningstatus]);
        }
        else if($request->idcleaningstatus == CleaningStatus::Clean || $request->idcleaningstatus == CleaningStatus::Inspected)
        {
            $iditem = $this->cleaningPlanRepository->getCleaningSettings()->default_cleaning_item;

            $plan = ["idspot" => $spot->id, "date" => Carbon::today(), "idcleaningstatus" => CleaningStatus::Clean, "iditem" => $iditem, "iduser" => Auth::id()];

            $cleaningPlan = CleaningPlan::create($plan);
            
            $spot->idcleaningplan = $cleaningPlan->id;
            $spot->save();
        }
        */

        return response()->json(['success' => true]);
    }

    public function getLast()
    {
        $last = CleaningPlan::where('date', Carbon::today())->orderBy('updated_at', 'desc')->first();

        return (is_null($last) ? "null" : $last->updated_at);
    }

    public function getLastCleaningTicket()
    {
        $cleaning_products = $this->cleaningPlanRepository->getCleaningSettings()->cleaning_products;

        $last = Ticket::select(['updated_at'])
                     ->whereHas('spot', function ($query){
                        $query->where('cleanable', true);
                     })
                     ->whereHas('item', function ($query) use ($cleaning_products) {
                        $query->where('idtype', $cleaning_products);
                     })
                     ->orderBy('updated_at', 'desc')
                     ->first();
                     
        return (is_null($last) ? "null" : $last->updated_at);
    }

    public function getLastCleaningSpot()
    {
        $last = Spot::orderBy('updated_at', 'desc')->first();

        return (is_null($last) ? "null" : $last->updated_at);
    }
    
}