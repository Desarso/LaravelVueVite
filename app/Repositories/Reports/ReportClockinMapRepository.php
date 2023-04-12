<?php

namespace App\Repositories\Reports;
use Illuminate\Support\Facades\DB;
use App\Models\ClockinLog;
use Carbon\Carbon;
use App\Helpers\Helper;
use Session;

class ReportClockinMapRepository
{
      public function getClockinData($request)
      {
            $start = Carbon::parse($request->start)->startOfDay();
            $end   = Carbon::parse($request->end)->endOfDay();

            $data = ClockinLog::with('activity:id,name')
                              ->when(!is_null($request->iduser), function ($query) use ($request) {
                                    return $query->where('iduser', $request->iduser);
                              })
                              ->when(!is_null($request->idteam), function ($query) use($request){
                                    return $query->whereHas('user.teams', function ($q) use ($request) {
                                          $q->where('idteam', $request->idteam);
                                    });
                              })
                              ->whereBetween('created_at', [$start, $end])
                              ->whereNotNull('start_location')
                              ->orderBy('updated_at', 'DESC')
                              ->get();

            return $data;
      }

      public function getClockinDataByUser($request)
      {
            $start = Carbon::parse($request->start)->startOfDay();
            $end   = Carbon::parse($request->end)->endOfDay();

            $data = ClockinLog::with('activity:id,name')
                              ->where('iduser', $request->iduser)
                              ->whereBetween('created_at', [$start, $end])
                              ->get();

            return response()->json(["success" => true, "data" => $data]);
      }

      public function getLastClockinChange()
      {
            $clockinLog = ClockinLog::orderBy('updated_at', 'desc')->first();
    
            return (is_null($clockinLog) ? "null" : $clockinLog->updated_at);
      }
}