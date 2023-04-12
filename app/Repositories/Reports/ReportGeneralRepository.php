<?php

namespace App\Repositories\Reports;
use Illuminate\Support\Facades\DB;
use App\Models\Ticket;
use App\Models\User;
use Carbon\Carbon;
use App\Helpers\Helper;
use App\Enums\TicketStatus;
use Session;

class ReportGeneralRepository
{
      protected $localTimezone;

      public function __construct()
      {
          $this->localTimezone = env('LOCAL_TIMEZONE', 'America/Costa_Rica');
      }

      public function getData($request)
      {
            $start = Carbon::parse($request->start, $this->localTimezone)->startOfDay()->setTimezone('UTC');
            $end   = Carbon::parse($request->end, $this->localTimezone)->endOfDay()->setTimezone('UTC');

            $data = Ticket::when(!is_null($request->idspot), function ($query) use($request){
                              return $query->where('idspot', $request->idspot);
                          })
                          ->when(!is_null($request->idstatus), function ($query) use($request){
                              return $query->where('idstatus', $request->idstatus);
                          })
                          ->when(!is_null($request->idteam), function ($query) use($request){
                              return $query->where('idteam', $request->idteam);
                          })
                          ->when(!is_null($request->iditem), function ($query) use($request){
                              return $query->where('iditem', $request->iditem);
                          })
                          ->whereBetween('created_at', [$start, $end])
                          ->get(['id', 'name', 'iditem', 'idteam', 'idstatus', 'idspot', 'created_at', DB::raw('Date(created_at) as date')]);
            return $data;
      }

      public function getDataEfficacy($request)
      {
            $data = $this->getData($request);

            $total = $data->count();

            $finished = $data->where("idstatus", 4)->count();

            $efficacy = ($total == 0 ? 100 : (round(( $finished / $total) * 100)));

            return ["efficacy" => $efficacy, "total" => $total, "finished" => $finished];
      }

      public function getDataActivity($request)
      {
            $data = $this->getData($request);

            $grouped = $data->groupBy('date');

            $data  = [];
            $categories  = [];

            foreach ($grouped as $key => $group) {

                  array_push($data, $group->count());
                  array_push($categories, $key);
            }

            $series = [
                  ["name" => "tareas", "data" => $data],
            ];

            return ["series" => $series, "categories" => $categories];
      }

      public function getDataActivityBySpot($request)
      {
            $data = $this->getData($request);

            $limit = $data->countBy('idspot')->sortDesc()->take(10)->keys()->toArray();

            $data = $data->whereIn('idspot', $limit);

            $spots = DB::table('wh_spot')->whereIn('id', $data->pluck("idspot")->toArray())->get();

            $grouped = $data->groupBy('idspot');

            $data_total = [];
            $labels     = [];

            $series = [
                  ["name" => "Pendientes",  "data" => []],
                  ["name" => "En Progreso", "data" => []],
                  ["name" => "Pausadas",    "data" => []], 
                  ["name" => "Finalizadas", "data" => []]
            ];

            foreach ($limit as $key)
            {
                  array_push($series[0]["data"], $grouped[$key]->where("idstatus", 1)->count());
                  array_push($series[1]["data"], $grouped[$key]->where("idstatus", 2)->count());
                  array_push($series[2]["data"], $grouped[$key]->where("idstatus", 3)->count());
                  array_push($series[3]["data"], $grouped[$key]->where("idstatus", 4)->count());
                  array_push($data_total, $grouped[$key]->count());
                  array_push($labels, $spots->firstWhere('id', $key)->name);
            }

            return ["series" => $series, "series_total" => $data_total, "labels" => $labels];
      }
      
      public function getDataActivityByItem($request)
      {
            $data = $this->getData($request);

            $limit = $data->countBy('iditem')->sortDesc()->take(10)->keys()->toArray();

            $data = $data->whereIn('iditem', $limit);

            $items = DB::table('wh_item')->whereIn('id', $data->pluck("iditem")->toArray())->get();

            $grouped = $data->groupBy('iditem');

            $data_total = [];
            $labels     = [];

            $series = [
                  ["name" => "Pendientes",  "data" => []],
                  ["name" => "En Progreso", "data" => []],
                  ["name" => "Pausadas",    "data" => []], 
                  ["name" => "Finalizadas", "data" => []]
            ];

            foreach ($limit as $key)
            {
                  array_push($series[0]["data"], $grouped[$key]->where("idstatus", 1)->count());
                  array_push($series[1]["data"], $grouped[$key]->where("idstatus", 2)->count());
                  array_push($series[2]["data"], $grouped[$key]->where("idstatus", 3)->count());
                  array_push($series[3]["data"], $grouped[$key]->where("idstatus", 4)->count());
                  array_push($data_total, $grouped[$key]->count());
                  array_push($labels, $items->firstWhere('id', $key)->name);
            }

            return ["series" => $series, "series_total" => $data_total, "labels" => $labels];
      }
}