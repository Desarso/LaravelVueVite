<?php

namespace App\Repositories\Reports;

use Illuminate\Support\Facades\DB;
use App\Models\ClockinLogSummary;
use App\Models\ClockinLog;
use Carbon\Carbon;
use App\Helpers\Helper;
use App\Repositories\UserRepository;
use Session;

class ReportClockinTimeRepository
{
    protected $userRepository;

    public function __construct()
    {
        $this->userRepository = new UserRepository;
    }

    public function getData($request)
    {
        $start = Carbon::parse($request->start)->startOfDay();
        $end   = Carbon::parse($request->end)->endOfDay();

        $userBranches = $this->userRepository->getUserBranch();

        $data = ClockinLogSummary::whereBetween('created_at', [$start, $end])
            ->when(!is_null($request->iduser), function ($query) use ($request) {
                return $query->where('iduser', $request->iduser);
            })
            ->when(!is_null($request->idstatus), function ($query) use ($request) {
                return $query->where('status', $request->idstatus);
            })
            ->when(!is_null($request->idbranch), function ($query) use ($request) {
                $query->whereHas('user', function ($query) use ($request) {
                    $query->whereJsonContains('spots', (int) $request->idbranch);
                });
            })
            ->whereHas('user', function ($q) use ($userBranches) {

                $q->where(function ($query) use ($userBranches) {
                    $query->whereJsonContains('spots', $userBranches[0]);

                    foreach ($userBranches as $branch) {
                        $query->orWhereJsonContains('spots', $branch);
                    }
                });
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return $data;
    }

    public function getClockinDetails($request)
    {
        if (is_null($request->date)) return [];

        $date = Carbon::parse($request->date, Session::get('local_timezone'))->startOfDay();

        $start = Carbon::parse($request->date, Session::get('local_timezone'))->startOfDay()->setTimezone('UTC');
        $end   = Carbon::parse($request->date, Session::get('local_timezone'))->endOfDay()->setTimezone('UTC');


        $data = ClockinLog::select('idactivity', 'clockin', 'clockout', 'duration')
            ->with('activity:id,name,color')
            ->where('iduser', $request->iduser)
            ->where(function ($query) use ($start, $end) {
                $query->whereBetween('clockin', [$start, $end])
                    ->orWhereBetween('clockout', [$start, $end]);
            })
            ->orderBy('clockin', 'desc')
            ->get();

        $data->each(function ($log) use ($date) {

            if (!is_null($log->clockout)) {
                $clockin = $log->clockin->format('Y-m-d');
                $clockout = $log->clockout->format('Y-m-d');

                if ($date->format('Y-m-d') != $clockin) {
                    $log->clockin = $date->startOfDay();
                    $log->duration = $log->clockin->diffInSeconds($log->clockout);
                } else if ($date->format('Y-m-d') != $clockout) {
                    $log->clockout = $date->endOfDay();
                    $log->duration = $log->clockin->diffInSeconds($log->clockout);
                }
            }
        });

        return $data;
    }
}
