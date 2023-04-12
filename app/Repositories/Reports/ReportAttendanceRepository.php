<?php

namespace App\Repositories\Reports;
use Illuminate\Support\Facades\DB;
use App\Models\UserAttendance;
use Carbon\Carbon;
use App\Helpers\Helper;
use Session;

class ReportAttendanceRepository
{
      public function getData($request)
      {
            $start = Carbon::parse($request->start)->startOfDay();
            $end   = Carbon::parse($request->end)->endOfDay();

            $data = UserAttendance::when(!is_null($request->iduser), function ($query) use ($request) {
                                    return $query->where('iduser', $request->iduser);
                                  })
                                  ->when(!is_null($request->idteam), function ($query) use($request){
                                    return $query->whereHas('user.teams', function ($q) use ($request) {
                                        $q->where('idteam', $request->idteam);
                                    });
                                  })
                                  ->when(!is_null($request->idstatus), function ($query) use ($request) {
                                    return $query->where('status', $request->idstatus);
                                  })
                                  ->whereBetween('punch_in', [$start, $end])
                                  ->get();

            $data->each(function ($item, $key) {
                $item->start_coordinates = $item->start_location;
                $item->end_coordinates   = $item->end_location;
                $item->iduser            = $item->user->fullname;
                $item->start_location    = $this->getLocation($item->start_location);
                $item->end_location      = $this->getLocation($item->end_location);
                $item->duration          = $this->getDuration($item);
            });

            return $data;
      }

      private function getLocation($json)
      {
            if(is_null($json)) return null;
            $data = json_decode($json);
            return property_exists($data, 'location') ? $data->location : null;
      }

      private function getDuration($item)
      {
            if(is_null($item->punch_out)) return 0;

            return $item->punch_in->diffInSeconds($item->punch_out);
      }
}