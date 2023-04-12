<?php

namespace App\Repositories\Reports;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Ticket;
use App\Models\TicketUser;
use App\Models\TicketType;
use App\Models\Team;
use Carbon\Carbon;
use App\Helpers\Helper;
use App\Repositories\SpotRepository;
use Session;

class ReportTeamsSummaryRepository
{
      protected $spotRepository;
  
      public function __construct()
      {
          $this->spotRepository   = new SpotRepository;
      }

      public function getData($request)
      {
            $start = Carbon::parse($request->start)->startOfDay();
            $end   = Carbon::parse($request->end)->endOfDay();

            $spots = is_null($request->idspot) ? json_decode(Auth::user()->spots) : $this->spotRepository->getChildren($request->idspot);

            $query = Ticket::select('id', 'iditem', 'idteam', 'idstatus', 'startdate', 'created_at')
                           ->with('team:id,name')
                           ->with('users:id as iduser,firstname')
                           ->whereIn('idspot', $spots)
                           ->when(!is_null($request->idteam), function ($query) use($request){
                              return $query->where('idteam', $request->idteam);
                           })
                           ->when(!is_null($request->iduser), function ($query) use($request){
                              return $query->whereHas('users', function ($q) use ($request) {
                                  $q->where('iduser', $request->iduser);
                              });
                           })
                           ->when(!is_null($request->idtype), function ($query) use($request){
                              return $query->whereHas('item', function ($q) use ($request) {
                                  $q->where('idtype', $request->idtype);
                              });
                           })
                           ->when(!is_null($request->iditem), function ($query) use($request){
                              return $query->where('iditem', $request->iditem);
                           })
                           ->whereBetween('created_at', [$start, $end]);

            return $query;
      }

      public function getDataTeamUserSummary($request)
      {
            $result = collect();

            $data = $this->getData($request)->get();
            $tickets =  $data->pluck('id');
            
            $ticketUser = TicketUser::with('ticket:id,idstatus')
                              ->whereIn('idticket',$tickets)
                              ->where('copy', 0)
                              ->get();

            $grouped = $ticketUser->groupBy('iduser');

            foreach ($grouped as $key => $user)
            {
                  $tickets = $user->pluck('ticket');
                  $object = ['iduser' => $key, 'quantity' => $user->count(), 'summary' => $this->getSummary($tickets)];
        
                  $result->push($object);
            }

            return $result->sortByDesc('quantity')->values();
      }

      public function getDataTeamsSummary($request)
      {
            $result = collect();

            $data = $this->getData($request)->withCount('users')->get();
            $grouped = $data->groupBy('idteam');


            foreach ($grouped as $id => $team)
            {
                  $noAssign = $team->where('users_count', 0)->count();
                  $object = ['idteam' => $id, 'quantity' => $team->count(), 'summary' => $this->getSummary($team), 'noAssign' => $noAssign];
        
                  $result->push($object);
            }

            return $result->sortByDesc('quantity')->values();
      }

      public function getTeamSummaryByStatus($request)
      {
            $query = $this->getData($request);

            $data = $query->get();
            $grouped = $data->groupBy('team.name');

            $labels = $grouped->keys();

            $data_pending  = collect();
            $data_finished = collect();
            $series_total  = collect();

            foreach ($grouped as $group)
            {
                $pending  = $group->where("idstatus", '!=', 4)->count();
                $finished = $group->where("idstatus", 4)->count();

                $data_pending->push($pending);
                $data_finished->push($finished);
                $series_total->push($group->count());
            }

            $series = [
                  ["name" => "Pendientes", "data" => $data_pending],
                  ["name" => "Finalizadas", "data" => $data_finished]
            ];

            return ["labels" => $labels, "series" => $series, "series_total" => $series_total];
      }

      public function getTeamSummary($request)
      {
            $query = $this->getData($request);

            $data = $query->get();
            $total = $data->count();
            $data = $data->groupBy('idteam');

            $teams = Team::whereIn('id', $data->keys())
                              ->orderBy('id')
                              ->get(['id','name', 'color']);

            $series = collect();
            $labels = collect();
            $colors = collect();

            foreach ($data as $id => $teamData) {
                  $series->push($teamData->count());
                  
                  $team = $teams->firstWhere('id', $id);
                  $labels->push($team->name);

                  if (is_null($team->color)) {
                        $colors->push($this->random_color());
                  } else {
                        $colors->push($team->color);
                  }
            }

            return ["series" => $series, "labels" => $labels, "colors" => $colors, "total" => $total];
      }

      private function getSummary($data)
      {
            $finished = $data->where('idstatus', 4)->count();

            if($finished == 0) return 0;

            $summary = ($finished / $data->count()) * 100;

            return round($summary);
      }

      function random_color_part() {
            return str_pad( dechex( mt_rand( 0, 255 ) ), 2, '0', STR_PAD_LEFT);
      }
      
      function random_color() {
            return '#'.$this->random_color_part() . $this->random_color_part() . $this->random_color_part();
      }
}