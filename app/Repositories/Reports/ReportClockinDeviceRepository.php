<?php

namespace App\Repositories\Reports;
use Illuminate\Support\Facades\DB;
use App\Models\ClockinLogSummary;
use App\Models\ClockinLog;
use Carbon\Carbon;
use App\Helpers\Helper;
use Session;

class ReportClockinDeviceRepository
{
    public function getData($request)
    {
        $start = Carbon::parse($request->start)->startOfDay();
        $end   = Carbon::parse($request->end)->endOfDay();

        $data = ClockinLog::whereBetween('created_at', [$start, $end])
                          ->when(!is_null($request->iduser), function ($query) use ($request) {
                                return $query->where('iduser', $request->iduser);
                          })
                          ->when(!is_null($request->idteam), function ($query) use($request){
                            return $query->whereHas('user.teams', function ($q) use ($request) {
                                $q->where('idteam', $request->idteam);
                            });
                          })
                          ->orderBy('created_at', 'desc')
                          ->get();

        // dd($data);

        $data->each(function ($item, $key) {

            $summary = DB::table('wh_clockin_log_summary')->where('iduser', $item->iduser)->whereDate('date', $item->clockin)->first();

            $item->iduser   = $item->user->fullname;
            $item->avatar   = $item->user->urlpicture;
            $item->teamName = ($item->user->coreTeam->count() > 0) ? $item->user->coreTeam[0]->name : "";

            $item["late_time"]    = $summary->late_time;
           
            $item["regular_time"] = $summary->regular_time / 3600;
            $item["overtime"]     = $summary->overtime / 3600;
            $item["double_time"]  = $summary->double_time / 3600;
            $item["isholiday"]    = $summary->isholiday;
            $item["clockin"]      = $item->clockin->format('Y-m-d H:i:s');
            $item["clockout"]      = is_null($item->clockout) ? null : $item->clockout->format('Y-m-d H:i:s');
            // dd($item->clockin);
        });

        return $data;
    }
}