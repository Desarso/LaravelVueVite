<?php

namespace App\Repositories\Reports;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Ticket;
use App\Models\User;
use Carbon\Carbon;
use App\Helpers\Helper;
use App\Enums\TicketStatus;
use App\Repositories\UserRepository;
use Session;

class ReportUserRepository
{
      protected $userRepository;

      public function __construct()
      {
          $this->userRepository = new UserRepository;
      }

      public function getDataUserTickets($request)
      {
            $start = Carbon::parse($request->start)->startOfDay();
            $end   = Carbon::parse($request->end)->endOfDay();

            $spots = json_decode(Auth::user()->spots);

            $userBranches = $this->userRepository->getUserBranch();

            if(count($userBranches) == 0) return [];
    
            $data = User::select('id')
                        ->with(["tickets" => function ($query) use($request, $start, $end, $spots){
                            $query->select('idticket', 'idstatus', 'created_by', 'wh_ticket.created_at')
                                  ->whereBetween('wh_ticket.created_at', [$start, $end])
                                  ->whereIn('idspot', $spots)
                                  ->when(!is_null($request->idspot), function ($query) use($request){
                                    return $query->where('idspot', $request->idspot);
                                  })
                                  ->when(!is_null($request->idstatus), function ($query) use($request){
                                    return $query->where('idstatus', $request->idstatus);
                                  })
                                  ->when(!is_null($request->idteam), function ($query) use($request){
                                    return $query->where('idteam', $request->idteam);
                                  });
                        }])
                        ->when(!is_null($request->iduser), function ($query) use($request){
                              return $query->where('id', $request->iduser);
                        })
                        ->where(function($query) use ($userBranches) {
                              $query->whereJsonContains('spots', $userBranches[0]);
          
                              foreach ($userBranches as $branch)
                              {
                                  $query->orWhereJsonContains('spots', $branch);      
                              }
                        })
                        ->get();

            foreach($data as $user)
            {
                  $user->total    = $user->tickets->count();
                  $user->pendint  = $user->tickets->where("idstatus", TicketStatus::Pending)->count();
                  $user->progress = $user->tickets->where("idstatus", TicketStatus::Progress)->count();
                  $user->paused   = $user->tickets->where("idstatus", TicketStatus::Paused)->count();
                  $user->finished = $user->tickets->where("idstatus", TicketStatus::Finished)->count();
            }

            return $data->sortByDesc('total')->values();
      }

      public function getUserTicketsDetails($request)
      {
            $start = Carbon::parse($request->start)->startOfDay();
            $end   = Carbon::parse($request->end)->endOfDay();

            $spots = json_decode(Auth::user()->spots);

            $data = Ticket::select('id', 'idstatus', 'idspot', 'idteam', 'iditem', 'name', 'created_by', 'created_at')
                          ->whereBetween('wh_ticket.created_at', [$start, $end])
                          ->whereIn('idspot', $spots)
                          ->whereHas('users', function ($q) use ($request) {
                              $q->where('iduser', $request->idparentuser);
                          })
                          ->when(!is_null($request->idspot), function ($query) use($request){
                              return $query->where('idspot', $request->idspot);
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
      
      public function getDataUserReports($request)
      {
            $start = Carbon::parse($request->start)->startOfDay();
            $end   = Carbon::parse($request->end)->endOfDay();

            $spots = json_decode(Auth::user()->spots);

            $userBranches = $this->userRepository->getUserBranch();

            if(count($userBranches) == 0) return [];
    
            $data = User::select('id')
                        ->withCount(["reports" => function ($query) use($request, $start, $end, $spots){
                            $query->whereBetween('wh_ticket.created_at', [$start, $end])
                                  ->whereIn('idspot', $spots)
                                  ->when(!is_null($request->idspot), function ($query) use($request){
                                    return $query->where('idspot', $request->idspot);
                                  })
                                  ->when(!is_null($request->idstatus), function ($query) use($request){
                                    return $query->where('idstatus', $request->idstatus);
                                  })
                                  ->when(!is_null($request->idteam), function ($query) use($request){
                                    return $query->where('idteam', $request->idteam);
                                  });
                        }])
                        ->when(!is_null($request->iduser), function ($query) use($request){
                              return $query->where('id', $request->iduser);
                        })
                        ->where(function($query) use ($userBranches) {
                              $query->whereJsonContains('spots', $userBranches[0]);
          
                              foreach ($userBranches as $branch)
                              {
                                  $query->orWhereJsonContains('spots', $branch);      
                              }
                        })
                        ->orderBy('reports_count', 'desc')
                        ->get();

            return $data;
      }
}