<?php

namespace App\Repositories\Reports;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Spot;
use Carbon\Carbon;
use App\Helpers\Helper;
use App\Repositories\UserRepository;
use App\Repositories\SpotRepository;
use Illuminate\Support\Facades\Auth;
use Session;

class ReportDurationRepository
{
      protected $userRepository;
      protected $spot;

      public function __construct()
      {
          $this->userRepository = new UserRepository;
          $this->spot = new Spot;
      }

      public function getDataUserDuration($request)
      {
            $start = Carbon::parse($request->start)->startOfDay();
            $end   = Carbon::parse($request->end)->endOfDay();

            $userSpots = $this->userRepository->getUserSpots(Auth::id());
    
            $data = User::select('id', 'firstname', 'lastname')
                        ->has('tickets')
                        ->with(["tickets" => function ($query) use($request, $start, $end, $userSpots){
                            $query->select('idticket', 'name', 'iditem', 'idspot', 'created_by', 'startdate', 'finishdate', 'duration', 'wh_ticket.created_at')
                                  ->whereBetween('wh_ticket.created_at', [$start, $end])
                                  ->whereIn('idspot', $userSpots)
                                  ->when(!is_null($request->idspot), function ($query) use($request){
                                    $spots = $this->spot->getSpotWithChidrens((array) $request->idspot, true);
                                    return $query->whereIn('idspot', $spots);
                                  })
                                  ->when(!is_null($request->iditem), function ($query) use($request){
                                    return $query->where('iditem', $request->iditem);
                                  })
                                  ->when(!is_null($request->idteam), function ($query) use($request){
                                    return $query->where('idteam', $request->idteam);
                                  });
                        }])
                        ->when(!is_null($request->iduser), function ($query) use($request){
                              return $query->where('id', $request->iduser);
                        })
                        ->get();

            $collection = collect();

            foreach($data as $user)
            {
                  foreach($user->tickets as $ticket)
                  {
                      $item = (object) array(
                          "id"         => $ticket->idticket,
                          "iduser"     => $user->fullname,
                          "name"       => $ticket->name,
                          "idspot"     => $ticket->idspot,
                          "duration"   => $ticket->duration,
                          "response"   => $this->getResponseTime($ticket),
                          "startdate"  => $ticket->startdate,
                          "finishdate" => $ticket->finishdate,
                          "created_at" => $ticket->created_at,
                      );
          
                      $collection->push($item);
                  }
            }

            return $collection;
      }

      public function getDataSpotDuration($request)
      {
            $start = Carbon::parse($request->start)->startOfDay();
            $end   = Carbon::parse($request->end)->endOfDay();
            $userSpots = $this->userRepository->getUserSpots(Auth::id());
    
            $data = Spot::select('id', 'name')
                        ->whereIn('id', $userSpots)
                        ->has('tickets')
                        ->with(["tickets" => function ($query) use($request, $start, $end){
                            $query->select('id', 'name', 'iditem', 'idspot', 'created_by', 'startdate', 'finishdate', 'duration', 'wh_ticket.created_at')
                                  ->whereBetween('wh_ticket.created_at', [$start, $end])
                                  ->when(!is_null($request->idspot), function ($query) use($request){
                                    $spots = $this->spot->getSpotWithChidrens((array) $request->idspot, true);
                                    return $query->whereIn('idspot', $spots);
                                  })
                                  ->when(!is_null($request->iditem), function ($query) use($request){
                                    return $query->where('iditem', $request->iditem);
                                  })
                                  ->when(!is_null($request->idteam), function ($query) use($request){
                                    return $query->where('idteam', $request->idteam);
                                  })
                                  ->when(!is_null($request->iduser), function ($query) use($request){
                                    return $query->whereHas('users', function ($q) use ($request){
                                                $q->where('iduser', $request->iduser);
                                          });
                                  });
                        }])
                        ->get();

            $collection = collect([]);

            foreach($data as $spot)
            {
                  foreach($spot->tickets as $ticket)
                  {
                      $item = (object) array(
                          "id"         => $ticket->id,
                          "idspot"     => $spot->name,
                          "name"       => $ticket->name,
                          "users"      => $ticket->users->pluck('fullname')->join(', '),
                          "duration"   => $ticket->duration,
                          "response"   => $this->getResponseTime($ticket),
                          "startdate"  => $ticket->startdate,
                          "finishdate" => $ticket->finishdate,
                          "created_at" => $ticket->created_at,
                      );
          
                      $collection->push($item);
                  }
            }

            return $collection;
      }

      private function getResponseTime($ticket)
      {
            if(is_null($ticket->finishdate)) return "---------------------";

            $minutes = $ticket->created_at->diffInMinutes($ticket->finishdate);

            if($minutes == 0)
            {
                  $seconds = $ticket->created_at->diffInSeconds($ticket->finishdate);
                  return $seconds . " secs"; 
            }
            else if($minutes <= 60)
            {
                  return $minutes . " mins";
            }
            else if($minutes <= 1440)
            {
                  return round(($minutes / 60)) . " hrs";
            }
            else if($minutes >= 1440)
            {
                  return round(($minutes / 1440)) . " días";
            }
      }
}