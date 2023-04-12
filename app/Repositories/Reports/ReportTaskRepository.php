<?php

namespace App\Repositories\Reports;
use Illuminate\Support\Facades\DB;
use App\Models\Ticket;
use App\Models\Spot;
use App\Models\Item;
use Carbon\Carbon;
use App\Helpers\Helper;
use App\Enums\TicketStatus;
use App\Repositories\SpotRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use Session;

class ReportTaskRepository
{
    protected $spotRepository;
    protected $userRepository;

    public function __construct()
    {
        $this->spotRepository = new SpotRepository;
        $this->userRepository = new UserRepository;
    }


      public function getDataSpotTickets($request)
      {
            $start = Carbon::parse($request->start)->startOfDay();
            $end   = Carbon::parse($request->end)->endOfDay();

            $userSpots = $this->userRepository->getUserSpots(Auth::id());
    
            $data = Spot::select('id')
                        ->with(["tickets" => function ($query) use($request, $start, $end){
                            $query->select('idspot', 'idstatus', 'created_by', 'wh_ticket.created_at')
                                  ->whereBetween('wh_ticket.created_at', [$start, $end])
                                  ->when(!is_null($request->idstatus), function ($query) use($request){
                                    return $query->where('idstatus', $request->idstatus);
                                  })
                                  ->when(!is_null($request->iditem), function ($query) use($request){
                                    return $query->where('iditem', $request->iditem);
                                  })
                                  ->when(!is_null($request->idteam), function ($query) use($request){
                                    return $query->where('idteam', $request->idteam);
                                  });
                        }])
                        ->whereIn('id', $userSpots)
                        ->when(!is_null($request->idspot), function ($query) use($request) {
                              $spots = $this->spotRepository->getChildren($request->idspot);
                              return $query->whereIn('id', $spots);
                        })
                        ->get();

            $collection = collect();

            foreach($data as $item)
            {
              $item->total    = $item->tickets->count();

              if ($item->total > 0) {
                $item->pendint  = $item->tickets->where("idstatus", TicketStatus::Pending)->count();
                $item->progress = $item->tickets->where("idstatus", TicketStatus::Progress)->count();
                $item->paused   = $item->tickets->where("idstatus", TicketStatus::Paused)->count();
                $item->finished = $item->tickets->where("idstatus", TicketStatus::Finished)->count();
                
                $collection->push($item);
              }
            }

            return $collection->sortByDesc('total')->values();
      }

      public function getSpotTickets($request)
      {
            $start = Carbon::parse($request->start)->startOfDay();
            $end   = Carbon::parse($request->end)->endOfDay();

            $data = Ticket::select('id', 'idstatus', 'idteam', 'iditem', 'name', 'created_by', 'created_at')
                          ->whereBetween('wh_ticket.created_at', [$start, $end])
                          ->when(!is_null($request->idparentspot), function ($query) use($request){
                              return $query->where('idspot', $request->idparentspot);
                          })
                          ->when(!is_null($request->iditem), function ($query) use($request){
                              return $query->where('iditem', $request->iditem);
                          })
                          ->when(!is_null($request->idstatus), function ($query) use($request){
                              return $query->where('idstatus', $request->idstatus);
                          })
                          ->when(!is_null($request->idteam), function ($query) use($request){
                              return $query->where('idteam', $request->idteam);
                          })
                          ->latest()
                          ->get();
            
            return $data;
      }

      public function getDataItemTickets($request)
      {
            $start = Carbon::parse($request->start)->startOfDay();
            $end   = Carbon::parse($request->end)->endOfDay();

            $userSpots = $this->userRepository->getUserSpots(Auth::id());
    
            $data = Item::select('id')
                        ->with(["tickets" => function ($query) use($request, $start, $end, $userSpots){
                            $query->select('iditem', 'idstatus', 'created_by', 'wh_ticket.created_at')
                                  ->whereIn('idspot', $userSpots)
                                  ->whereBetween('wh_ticket.created_at', [$start, $end])
                                  ->when(!is_null($request->idstatus), function ($query) use($request){
                                    return $query->where('idstatus', $request->idstatus);
                                  })
                                  ->when(!is_null($request->idspot), function ($query) use($request){
                                    return $query->where('idspot', $request->idspot);
                                  })
                                  ->when(!is_null($request->idteam), function ($query) use($request){
                                    return $query->where('idteam', $request->idteam);
                                  });
                        }])
                        ->when(!is_null($request->iditem), function ($query) use($request){
                              return $query->where('id', $request->iditem);
                        })
                        ->get();


            $collection = collect();

            foreach($data as $item)
            {
              $item->total    = $item->tickets->count();

              if ($item->total > 0) {
                  $item->total    = $item->tickets->count();
                  $item->pendint  = $item->tickets->where("idstatus", TicketStatus::Pending)->count();
                  $item->progress = $item->tickets->where("idstatus", TicketStatus::Progress)->count();
                  $item->paused   = $item->tickets->where("idstatus", TicketStatus::Paused)->count();
                  $item->finished = $item->tickets->where("idstatus", TicketStatus::Finished)->count();
                
                $collection->push($item);
              }
            }

            return $collection->sortByDesc('total')->values();
      }

      public function getItemTickets($request)
      {
            $start = Carbon::parse($request->start)->startOfDay();
            $end   = Carbon::parse($request->end)->endOfDay();

            $data = Ticket::select('id', 'idstatus', 'idteam', 'idspot', 'name', 'created_by', 'created_at')
                          ->whereBetween('wh_ticket.created_at', [$start, $end])
                          ->when(!is_null($request->idparentitem), function ($query) use($request){
                              return $query->where('iditem', $request->idparentitem);
                          })
                          ->when(!is_null($request->iditem), function ($query) use($request){
                              return $query->where('iditem', $request->iditem);
                          })
                          ->when(!is_null($request->idstatus), function ($query) use($request){
                              return $query->where('idstatus', $request->idstatus);
                          })
                          ->when(!is_null($request->idteam), function ($query) use($request){
                              return $query->where('idteam', $request->idteam);
                          })
                          ->latest()
                          ->get();
            
            return $data;
      }
}