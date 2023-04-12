<?php

namespace App\Repositories\Reports;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Ticket;
use App\Models\TicketType;
use App\Models\Spot;
use Carbon\Carbon;
use App\Helpers\Helper;
use App\Repositories\SpotRepository;
use Session;

class ReportTasksSummaryRepository
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

            $query = Ticket::select('id', 'name', 'idspot', 'iditem', 'idstatus', 'duration', 'startdate', 'created_at')
                           ->whereIn('idspot', $spots)
                           ->when(!is_null($request->idteam), function ($query) use($request){
                              return $query->where('idteam', $request->idteam);
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

      public function getDataItemSummary($request)
      {
            $result = collect();

            $data = $this->getData($request)->get()->groupBy('iditem');

            foreach ($data as $key => $item)
            {
                  $object = ['iditem' => $key, 'quantity' => $item->count(), 'summary' => $this->getSummary($item)];
        
                  $result->push($object);
            }

            return $result->sortByDesc('quantity')->values();
      }

      public function getDataTicketTypeSummary($request)
      {
            $result = collect();

            $query = $this->getData($request);

            $data = $query->with('item:id,idtype')->get()->groupBy('item.idtype');

            foreach ($data as $key => $item)
            {
                  $object = ['idtype' => $key, 'quantity' => $item->count(), 'summary' => $this->getSummary($item)];
        
                  $result->push($object);
            }

            return $result;
      }

      public function getDataSpotSummary($request)
      {
            $result = collect();

            $spots = json_decode(Auth::user()->spots);

            $branches = DB::table('wh_spot')->select('id')
                          ->when(!is_null($request->idspot), function ($query) use($request, $spots){
                              $query->where('id', $request->idspot);
                          }, function ($query) use ($spots) {
                              $query->whereIn('id', $spots);
                          })
                          ->where('isbranch', 1)
                          ->where('id', '!=', 0)
                          ->get();

            $data = $this->getData($request)->get();

            foreach ($branches as $branch)
            {
                  $children = $this->spotRepository->getChildren($branch->id);

                  $tasks = $data->whereIn('idspot', $children);

                  $object = ['idspot' => $branch->id, 'quantity' => $tasks->count(), 'summary' => $this->getSummary($tasks)];
        
                  $result->push($object);

                  //$data = $data->whereNotIn('idspot', $children);
            }

            return $result->sortByDesc('quantity')->values();
      }

      public function getTicketSummaryByMonth($request)
      {
            $query = $this->getData($request);

            $data = $query->select("id", "idstatus", DB::raw("DATE_FORMAT(created_at, '%b %Y') AS `date`"))
                          ->get();
            
            $grouped = $data->groupBy('date');
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

      public function getTicketSummary($request)
      {
            $query = $this->getData($request);

            $data = $query->get();

            $series  = collect();

            $pending  = $data->where("idstatus", '!=', 4)->count();
            $finished = $data->where("idstatus", 4)->count();

            $series->push($pending);
            $series->push($finished);

            return ["series" => $series, "total" => $data->count()];
      }

      private function getSummary($data)
      {
            $finished = $data->where('idstatus', 4)->count();

            if($finished == 0) return 0;

            $summary = ($finished / $data->count()) * 100;

            return round($summary);
      }
}