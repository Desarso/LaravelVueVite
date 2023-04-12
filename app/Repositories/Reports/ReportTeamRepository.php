<?php

namespace App\Repositories\Reports;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Ticket;
use App\Models\User;
use Carbon\Carbon;
use App\Helpers\Helper;
use App\Enums\TicketStatus;
use Session;
use App\Repositories\SpotRepository;

class ReportTeamRepository
{
      protected $spotRepository;

      public function __construct()
      {
          $this->spotRepository = new SpotRepository;
      }

      public function getData($request)
      {
            $start = Carbon::parse($request->start)->startOfDay();
            $end   = Carbon::parse($request->end)->endOfDay();

            $spots = json_decode(Auth::user()->spots);

            $data = Ticket::when(!is_null($request->idspot), function ($query) use($request){

                              $spots = $this->spotRepository->getChildren($request->idspot);

                              return $query->whereIn('idspot', $spots);
                          })
                          ->when(!is_null($request->idstatus), function ($query) use($request){
                              return $query->where('idstatus', $request->idstatus);
                          })
                          ->when(!is_null($request->idteam), function ($query) use($request){
                              return $query->where('idteam', $request->idteam);
                          })
                          ->when(!is_null($request->iduser), function ($query) use($request){
                              return $query->whereHas('users', function ($q) use ($request) {
                                    $q->where('iduser', $request->iduser);
                              });
                          })
                          ->whereIn('idspot', $spots)
                          ->whereBetween('created_at', [$start, $end])
                          ->get(['id', 'code', 'name', 'iditem', 'idteam', 'idstatus', 'idspot', 'idpriority', 'duedate', 'finishdate', 'justification', 'created_at']);

            return $data;
      }

      public function getDataByTeam($request)
      {
            $data = $this->getData($request);

            $labels = DB::table('wh_team')->wherein('id', $data->pluck("idteam")->toArray())->pluck("name")->toArray();

            $grouped = $data->groupBy('idteam');

            $data_pending  = [];
            $data_finished = [];
            $data_paused   = [];
            $data_progress   = [];
            $data_total      = [];

            foreach ($grouped as $group)
            {
                $pending  = $group->where("idstatus", 1)->count();
                $finished = $group->where("idstatus", 4)->count();
                $paused   = $group->where("idstatus", 3)->count();
                $progress = $group->where("idstatus", 2)->count();
                $total    = $pending + $finished + $paused + $progress; 

                array_push($data_pending, $pending);
                array_push($data_finished, $finished);
                array_push($data_paused, $paused);
                array_push($data_progress, $progress);
                array_push($data_total, $total);
            }

            $series = [
                  ["name" => "Pendientes", "data" => $data_pending],
                  ["name" => "En Progreso", "data" => $data_progress],
                  ["name" => "Pausadas", "data" => $data_paused], 
                  ["name" => "Finalizadas", "data" => $data_finished]
            ];

            return ["series" => $series, "series_total" => $data_total,/*"series_dates" => $days,*/ "labels" => $labels];
      }
}