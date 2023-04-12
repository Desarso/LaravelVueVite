<?php

namespace App\Repositories\Reports;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\AttendanceOvertime;
use App\Models\AttendanceResumeOvertime;
use Carbon\Carbon;
use App\Helpers\Helper;
use Session;


class ReportAttendanceOvertimeRepository
{
      protected $localTimezone;

      public function __construct()
      {
          $this->localTimezone = env('LOCAL_TIMEZONE', 'America/Costa_Rica');
      }
      
      public function getData($request)
      {
            $start = Carbon::parse($request->start, $this->localTimezone)->startOfDay();
            $end   = Carbon::parse($request->end, $this->localTimezone)->endOfDay();

            $slots = AttendanceOvertime::select('iduser', 'rate', DB::raw('SUM(time) as time'), DB::raw('DATE(created_at) as date'))
                                      ->when(!is_null($request->iduser), function ($query) use ($request) {
                                          return $query->where('iduser', $request->iduser);
                                      })
                                      ->with('user:id,firstname,lastname')
                                      ->whereBetween('created_at', [$start, $end])
                                      ->groupBy('iduser', 'rate', 'date')
                                      ->get();
            
            $result = array();
            $slots = $slots->groupBy('iduser'); 

            foreach ($slots as $value) {
                $collection = collect($value)->groupBy('date');
                
                foreach ($collection as $key => $item) {

                    array_push($result, array(
                        'iduser' => $item[0]->iduser,
                        'date' => $key,
                        'simple' => ($item->firstWhere('rate', 1)) ? $item->firstWhere('rate', 1)->time : 0,
                        'half' => ($item->firstWhere('rate', 1.5)) ? $item->firstWhere('rate', 1.5)->time : 0,
                        'double' => ($item->firstWhere('rate', 2)) ? $item->firstWhere('rate', 2)->time : 0,
                        'user' => $item[0]->user,
                    ));
                }
            }
            
            return $result;
      }


      public function getDataResume($request)
      {
            $start = Carbon::parse($request->start, $this->localTimezone)->startOfDay();
            $end   = Carbon::parse($request->end, $this->localTimezone)->endOfDay();

            $users = $this->getUsersFromMySpots();

            $data = AttendanceResumeOvertime::when(!is_null($request->iduser), function ($query) use ($request) {
                                                 return $query->where('iduser', $request->iduser);
                                             })
                                             ->when(!is_null($request->idstatus), function ($query) use ($request) {
                                                return $query->where('status', $request->idstatus);
                                             })
                                             ->whereBetween('created_at', [$start, $end])
                                             ->whereIn('iduser', $users)
                                             ->orderBy('created_at', 'asc')
                                             ->get();
        
            
            return $data;
      }

      private function getUsersFromMySpots()
      {
        $user_spots = json_decode(Auth::user()->spots);
        $spots = DB::table('wh_spot')->where('isbranch', 1)->whereIn('id', $user_spots)->select('id')->pluck('id')->toArray();
        
        return DB::table('wh_user')
                    ->where(function($query) use($spots) {
                        $query->whereJsonContains('wh_user.spots', $spots[0]);

                        for($i = 1; $i < count($spots); $i++) {
                            $query->orWhereJsonContains('wh_user.spots', $spots[$i]);      
                        }
                    })->select('id')->pluck('id')->toArray();
      }

      public function getOvertimeDetails($request)
      {
            $start = Carbon::parse($request->date, $this->localTimezone)->startOfDay();
            $end   = Carbon::parse($request->date, $this->localTimezone)->endOfDay();

            $slots = AttendanceOvertime::select('iduser', DB::raw('1 AS rate'), 'idticket', 'start', 'end', 'time',  DB::raw('Date(CONVERT_TZ(created_at, "+00:00", "-06:00")) as date'))
                                        ->where('iduser', $request->iduser)
                                        ->with('ticket:id,name,code')
                                        ->whereBetween('created_at', [$start, $end])
                                        ->orderBy('idticket')
                                        ->orderBy('idlog')
                                        ->orderBy('rate')
                                        ->get();
            
            return $slots;
      }

      public function getDataResumeAPP($request)
      {
            $start = Carbon::parse($request->start, $this->localTimezone)->startOfDay()->setTimezone('UTC');
            $end   = Carbon::parse($request->end, $this->localTimezone)->endOfDay()->setTimezone('UTC');

            return AttendanceResumeOvertime::select('id', 'normal_time', 'half_time', 'double_time', 'status',  DB::raw('Date(CONVERT_TZ(created_at, "+00:00", "-06:00")) as date'),  DB::raw('created_at as date3'))
                                            ->where('iduser', $request->iduser)
                                            ->whereBetween('created_at', [$start, $end])
                                            ->orderBy('created_at', 'asc')
                                            ->get();
      }
}