<?php

namespace App\Repositories\Reports;
use App\Repositories\Cleaning\CleaningPlanRepository;
use Illuminate\Support\Facades\DB;
use App\Models\Ticket;
use App\Models\Item;
use App\Models\User;
use App\Models\Cleaning\CleaningPlan;
use Carbon\Carbon;
use App\Helpers\Helper;
use App\Enums\TicketStatus;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use Session;

class ReportCleaningRepository
{
    protected $cleaningPlanRepository;
    protected $userRepository;

    public function __construct()
    {
        $this->cleaningPlanRepository = new CleaningPlanRepository;
        $this->userRepository = new UserRepository;
    }

    public function getData($request)
    {
        $start = Carbon::parse($request->start)->startOfDay();
        $end   = Carbon::parse($request->end)->endOfDay();

        $cleaning_products = $this->cleaningPlanRepository->getCleaningSettings()->cleaning_products;

        $data = Ticket::with('item:id,idtype')
                      ->when(!is_null($request->iditem), function ($query) use($request){
                        return $query->where('iditem', $request->iditem);
                      })
                      ->when(!is_null($request->idspot), function ($query) use($request){
                        return $query->where('idspot', $request->idspot);
                      })
                      ->when(!is_null($request->idtype), function ($query) use($request){
                        return $query->whereHas('item.tickettype', function ($q) use($request){
                            $q->where('id', $request->idtype);
                         });
                      })
                      ->whereHas('item', function ($query) use ($cleaning_products) {
                        $query->whereIn('idtype', $cleaning_products);
                      })
                      ->whereBetween('created_at', [$start, $end])
                      ->get(['id', 'name', 'iditem', 'quantity']);

        return $data;
    }

    public function getDataCleaningRequest($request)
    {
        $collection = collect([]);

        $data = $this->getData($request);

        $total = $data->sum('quantity');

        $grouped = $data->groupBy('iditem');

        foreach($grouped as $iditem => $tickets)
        {
            $item = ['name' => $tickets[0]->name, 'total_quantity' => $tickets->sum('quantity'), 'total_tickets' => $tickets->count(), 'average' => $this->getAverage($total, $tickets->sum('quantity'))];

            $collection->push($item);
        }

        return $collection->sortByDesc('total_quantity')->values()->all();
    }

    public function getDataCleaningTicketType($request)
    {
        $collection = collect([]);

        $data = $this->getData($request);

        $total = $data->count();

        foreach($data as $ticket)
        {
            $ticket->tickeType = $ticket->item->tickettype->name;
        }

        $grouped = $data->groupBy('tickeType');

        foreach($grouped as $ticketType => $tickets)
        {
            $item = ['name' => $ticketType, 'percent' => $this->getAverage($total, $tickets->count())];

            $collection->push($item);
        }

        return $collection;
    }

    public function getCleaningRequestItems($request)
    {
        $cleaning_products = $this->cleaningPlanRepository->getCleaningSettings()->cleaning_products;
        
        return DB::table('wh_item')  
                 ->whereIn('idtype', $cleaning_products)
                 ->get(['id as value', 'name as text']);
    }

    public function getCleaningRequestTicketTypes($request)
    {
        $cleaning_products = $this->cleaningPlanRepository->getCleaningSettings()->cleaning_products;
        
        return DB::table('wh_ticket_type')  
                 ->whereIn('id', $cleaning_products)
                 ->get(['id as value', 'name as text']);
    }

    private function getAverage($total, $tickets)
    {
        if($tickets == 0) return 0;

        return $total == 0 ? 100 : round( ( $tickets / $total ) * 100 );
    }

    public function getDataCleaning($request)
    {
        $start = Carbon::parse($request->start)->startOfDay();
        $end   = Carbon::parse($request->end)->endOfDay();
        $userSpots = $this->userRepository->getUserSpots(Auth::id());

        $data =  CleaningPlan::when(!is_null($request->iditem), function ($query) use($request){
                                return $query->where('iditem', $request->iditem);
                             })
                             ->when(!is_null($request->idspot), function ($query) use($request){
                                return $query->where('idspot', $request->idspot);
                             })
                             ->when(!is_null($request->idstatus), function ($query) use($request){
                                return $query->where('idcleaningstatus', $request->idstatus);
                             })
                             ->when(!is_null($request->iduser), function ($query) use($request){
                                return $query->where('iduser', $request->iduser);
                             })
                             ->whereBetween('created_at', [$start, $end])
                             ->whereIn('idspot', $userSpots)
                             ->get();
        return $data;
    }
}