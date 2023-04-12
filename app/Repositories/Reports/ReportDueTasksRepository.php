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
use App\Enums\TicketStatus;

class ReportDueTasksRepository
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

            $data  = Ticket::select('id', 'name', 'idspot', 'idteam', 'iditem', 'idstatus', 'created_by', 'duedate', 'finishdate', 'created_at')
                           ->whereIn('idspot', $spots)
                           ->when(!is_null($request->idteam), function ($query) use($request){
                              return $query->where('idteam', $request->idteam);
                           })
                           ->when(!is_null($request->iditem), function ($query) use($request){
                              return $query->where('iditem', $request->iditem);
                           })
                           ->when(!is_null($request->iduser), function ($query) use($request){
                              return $query->whereHas('users', function ($q) use ($request) {
                                  $q->where('iduser', $request->iduser);
                              });
                           })
                           ->whereBetween('created_at', [$start, $end])
                           ->whereNotNull('duedate')
                           ->get();
            
            foreach ($data as $ticket)
            {
                  $ticket['isOverdue'] = $this->checkIfTaskIsOverdue($ticket);
            }

            return $data;
      }

      public function getDataBySpot($request)
      {
            $dataGroups = $this->getData($request);

            return $this->getDataGroupedByKey($dataGroups, 'idspot');
      }

      public function getDataByTeam($request)
      {
            $dataGroups = $this->getData($request);

            return $this->getDataGroupedByKey($dataGroups, 'idteam');
      }

      public function getDataByItem($request)
      {
            $dataGroups = $this->getData($request);

            return $this->getDataGroupedByKey($dataGroups, 'iditem');
      }

      public function checkIfTaskIsOverdue($ticket)
      {
            $limitDate = ($ticket->idstatus == TicketStatus::Finished) ? $ticket->finishdate : Carbon::now(Session::get('local_timezone'));
    
            return ($limitDate->greaterThan($ticket->duedate) ? true : false);
      }

      public function getDataGroupedByKey($dataGroups, $key)
      {
            $dataGroups = $dataGroups->groupBy($key);

            $result = collect();

            foreach ($dataGroups as $key => $dataGroup)
            {
                  $object = [
                        'id'         => $key,
                        'total'      => $dataGroup->count(),
                        'unfinished' => $dataGroup->where('idstatus', '!=', TicketStatus::Finished)->count(),
                        'overdue'    => $dataGroup->where('isOverdue', true)->count(),
                        'average'    => $this->getAverageTaskCompletion($dataGroup->count(), $dataGroup->where('isOverdue', false)->count())
                  ];
        
                  $result->push($object);
            }

            return $result;
      }

      public function getAverageTaskCompletion($total, $ontime)
      {
            return $total == 0 ? 0 : round(($ontime / $total) * 100);
      }
}