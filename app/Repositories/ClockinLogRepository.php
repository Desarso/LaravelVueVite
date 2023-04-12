<?php

namespace App\Repositories;


use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\User;
use App\Models\ClockinLog;
use App\Models\ClockinLogSummary;
use App\Models\Shift;
use App\Models\ClockinLogDetail;
use Illuminate\Support\Facades\Auth;
use Session;


class ClockinLogRepository
{

    public function registerClockinAPP($request)
    {
        $hasClockin = ClockinLog::select('id', 'iduser', 'action', 'clockin', 'clockout', 'duration', 'idactivity', 'start_location', 'created_at')
                                ->where('iduser', $request->iduser)
                                ->where('action', 'CLOCK-IN')
                                ->first();

        if ($hasClockin) return response()->json(["result" => true]);

        ClockinLog::create($request->all());

        ClockinLogSummary::firstOrCreate(
            [
                'iduser' => $request->iduser,
                'date' => Carbon::now()->format('Y-m-d')
            ],
            [
                'isholiday' => 1,
            ]
        );

        return response()->json([
            "result" => true
        ]);
    }

    public function registerClockoutAPP($request)
    {
        Auth::loginUsingId($request->iduser);

        Session::put('local_timezone', 'America/Costa_Rica');

        $clockinLog = ClockinLog::find($request->id);
        $clockin = $clockinLog->clockin->second(0)->setTimezone(config('app.timezone'));
        $clockinLog->clockin = $clockin;
        $clockinLog->fill($request->all())->save();
        $clockinLog->fresh();

        $idschedule = Auth::user()->idschedule;
        $clockin = $clockinLog->clockin;
        $clockout = $clockinLog->clockout;

        $shift = $this->getShift($idschedule, $clockin);

        if (!$clockin->isSameDay($clockout)) {
            $dayOne = $this->getSlots($shift->idovertime, $clockin, Carbon::parse($clockin)->endOfDay());
            $this->getOvertimeDetail($dayOne, $clockinLog);
           
            $clockinLog->clockin = Carbon::parse($clockout)->startOfDay();
            $dayTwo = $this->getSlots($shift->idovertime, Carbon::parse($clockout)->startOfDay(), $clockout);
            $this->getOvertimeDetail($dayTwo, $clockinLog);
        } else {
            $slots = $this->getSlots($shift->idovertime, $clockin, $clockout);
            $this->getOvertimeDetail($slots, $clockinLog);
        }

        return response()->json([
            "result" => true
        ]);
    }

    public function getUserClockinLogAPP($request)
    {
        $autoClockout = $this->checkAutoClockout($request);
        $result = '';
        // $autoClockout = false;

        $data = ClockinLog::select('id', 'iduser', 'action', 'clockin', 'clockout', 'duration', 'idactivity', 'start_location', 'created_at')
                    ->with('activity:id,name,color')
                    ->where('iduser', $request->iduser)
                    ->where(function ($query) {
                        $query->whereDate('created_at', Carbon::today())
                              ->orWhere('action', 'CLOCK-IN');
                    })
                    ->orderBy('created_at', 'DESC')
                    ->get();

        $result = ['data' => $data, 'autoclockout' => $autoClockout];

        return response()->json($result);
    }

    private function getShift($idschedule, $clockin)
    {
        return DB::table('wh_shift')
                    ->where('idschedule', $idschedule)
                    ->whereJsonContains('dow', $clockin->shortEnglishDayOfWeek)
                    ->first();
    }

    private function getSlots($idovertime, $clockin, $clockout)
    {

        return DB::table('wh_overtime_slot')
                    ->where('idovertime', $idovertime)
                    ->where(function ($query) use ($clockin, $clockout) {
                        $query->where(function ($query) use ($clockin, $clockout) {
                            $query->whereBetween('start', [$clockin, $clockout])
                                ->orWhereBetween('end', [$clockin, $clockout]);
                        })
                        ->orWhere(function ($query) use ($clockin, $clockout) {
                            $query->whereTime('start', '<', $clockin)
                                ->whereTime('end', '>', $clockout);
                        });
                    })
                   ->get();
    }

    private function getOvertimeDetail($slots, $clockinLog)
    {
        $rates = collect();
        $clockin = $clockinLog->clockin;
        $clockout = $clockinLog->clockout;

        foreach ($slots as $slot) {

            $start = Carbon::parse($slot->start, 'America/Costa_Rica')->day($clockin->format('d'))->month($clockin->format('m'));
            $end = Carbon::parse($slot->end, 'America/Costa_Rica')->day($clockin->format('d'))->month($clockin->format('m'));
        
            $startRate = ($clockin > $start) ? $clockin : $start;
            $endRate = ($clockout > $end) ? $end : $clockout;
        
            $diff = $startRate->diffInSeconds($endRate);

            if ($endRate->second == 59) {
                $diff += 1;
            }

            $rates->push([
                'time' => $diff,
                'rate' => $slot->rate,
                'idclockin' => $clockinLog->id,
                'iduser' => Auth::id(),
                'start' => $startRate->toTimeString(),
                'end' => $endRate->toTimeString(),
                'date' => $clockinLog->clockin->format('Y-m-d'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }

        ClockinLogDetail::insert($rates->toArray());
        $this->getClockinSummary($rates, $clockinLog);
    }

    private function getClockinSummary($rates, $clockinLog)
    {
        $rates = $rates->map(function ($item, $key) {
            $item['rate'] = strval( $item['rate'] ) ;
            return $item;
        });

        $groups = $rates->groupBy('rate');

        $resume_item = ['iduser' => Auth::id(), 'regular_time' => 0, 'overtime' => 0, 'double_time' => 0];

        foreach ($groups as $key => $group)
        {
            $sum = $group->sum('time');

            switch($key)
            {
                case '1':
                    $resume_item['regular_time'] = $sum;
                    break;

                case '1.5':
                    $resume_item['overtime'] = $sum;
                    break;

                case '2':
                    $resume_item['double_time'] = $sum;
                    break;
            }
        }

        ClockinLogSummary::updateOrCreate([
                        'date' => $clockinLog->clockin->format('Y-m-d'),
                        'iduser' => Auth::id()
                    ],[
                        'regular_time' => DB::raw( 'regular_time + ' . $this->roundSecondsInMinutes($resume_item['regular_time']) ),
                        'overtime'     => DB::raw( 'overtime + ' . $this->roundSecondsInMinutes($resume_item['overtime']) ),
                        'double_time'  => DB::raw( 'double_time + ' . $this->roundSecondsInMinutes($resume_item['double_time']) ),
                    ]);
    }
    
    public function getUserClockinSummaryAPP($request)
    {
        $logSummary = ClockinLogSummary::select('id', 'regular_time', 'overtime', 'double_time', 'date', "status")
                                ->whereBetween('date', [$request->startDate, $request->endDate])
                                ->where('iduser', $request->iduser)
                                ->orderBy('date', 'DESC')
                                ->get();


        $summary = [
            'regular_time' => $logSummary->sum('regular_time'),
            'overtime' => $logSummary->sum('overtime'),
            'double_time' => $logSummary->sum('double_time'),
        ];

        $logApproved = ClockinLogSummary::select('id', 'regular_time_approved AS regular_time', 'overtime_approved AS overtime', 'double_time_approved AS double_time', 'date', 'date_approved', 'note_approved', 'status', 'fully_approved', 'idapprover')
                                ->with('approver:id,firstname,lastname')
                                ->whereBetween('date', [$request->startDate, $request->endDate])
                                ->where('iduser', $request->iduser)
                                ->where('status', 'VERIFIED')
                                ->orderBy('date', 'DESC')
                                ->get();

        $summaryApproved = [
            'regular_time' => $logApproved->sum('regular_time'),
            'overtime' => $logApproved->sum('overtime'),
            'double_time' => $logApproved->sum('double_time'),
        ];

        return response()->json([
            "logs" => $logSummary,
            "summary" => $summary,
            "summary_approved" => $summaryApproved,
            "logs_approved" => $logApproved,
        ]);
    }
    
    public function getUserClockLogHistoryAPP($request)
    {
        $timeZone = 'America/Costa_Rica';
        $start = Carbon::parse($request->date, $timeZone)->startOfDay()->setTimezone('UTC');
        $end   = Carbon::parse($request->date, $timeZone)->endOfDay()->setTimezone('UTC');

        $logs = ClockinLog::select('id', 'iduser', 'action', 'clockin', 'clockout', 'duration', 'idactivity', 'created_at')
                        ->with('activity:id,name,color')
                        ->where('iduser', $request->iduser)
                        ->where(function ($query) use ($start, $end) {
                            $query->whereBetween('clockin', [$start, $end])
                                  ->orWhereBetween('clockout', [$start, $end]);
                        })
                        ->orderBy('clockin', 'DESC')
                        ->get();

        $date = Carbon::parse($request->date, $timeZone);
        
        $logs->each(function($log) use ($date) {

            $clockin = $log->clockin->format('Y-m-d');
            $clockout = ($log->clockout != null) ? $log->clockout->format('Y-m-d') : null;

            $log->duration = $log->clockin->diffInSeconds($log->clockout);

            // if ($date->format('Y-m-d') != $clockin) {
            //     $log->clockin = $date->startOfDay();
            //     $log->duration = $log->clockin->diffInSeconds($log->clockout);
            // } else if($clockout != null && $date->format('Y-m-d') != $clockout) {
            //     $log->clockout = $date->endOfDay();
            //     $log->duration = $log->clockin->diffInSeconds($log->clockout);
            // }
        });  
        
        return $logs;
    }
    
    public function approveClockinTime($request)
    {
        $request['idapprover']    = Auth::id();
        $request['status']        = "VERIFIED";
        $request['date_approved'] = Carbon::now()->setTimezone(config('app.timezone'));
        $request['fully_approved'] = 

        $model = ClockinLogSummary::find($request->id);
        $model->fill($request->all())->save();

        $model->fully_approved = $this->checkFullyApproved($model->refresh());
        $model->save();

        return response()->json([ 'success' => true ]);
    }

    private function checkFullyApproved($model)
    {
        $result = $model->only(["regular_time", "overtime", "double_time", "regular_time_approved", "overtime_approved", "double_time_approved"]);

        if($result["regular_time"] != $result["regular_time_approved"] || $result["overtime"] != $result["overtime_approved"] || $result["double_time"] != $result["double_time_approved"]) return false;

        return true;
    }

    private function roundSecondsInMinutes($seconds)
    {
        return floor($seconds / 60) * 60;
    }

    public function checkAutoClockout($request)
    {
        $limitTime = $this->getClockinLimit();
        $autoClockout = false;
        $limitHour = Carbon::now()->subHours($limitTime);

        $currentClockin = ClockinLog::select('id', 'iduser', 'action', 'clockin', 'created_at')
                                    ->where('iduser', $request->iduser)
                                    ->where('action', 'CLOCK-IN')
                                    ->where('clockin', '<', $limitHour)
                                    ->first();

        if (!is_null($currentClockin)) {
            $clockout = $currentClockin->clockin->setTimezone('UTC')->addHours($limitTime)->second(0);
            $duration = $currentClockin->clockin->diffInSeconds($clockout);
            
            $request->request->add(['id' => $currentClockin->id]);
            $request->request->add(['clockout' => $clockout]);
            $request->request->add(['action' => 'CLOCK-OUT']);
            $request->request->add(['duration' => $duration]);
            $request->request->add(['auto_clockout' => true]);

            $this->registerClockoutAPP($request);
            $autoClockout = true;
        }

        return  $autoClockout;
    }

    private function getClockinLimit()
    {
        $organization = DB::table('wh_organization')->select('settings')->first();
        $settings = json_decode($organization->settings);

        if (property_exists($settings, 'clockin_limit')) {
            return $settings->clockin_limit;
        } else {
            return 12;
        }
    }

    public function verifyClockInCodeAPP($request)
    {
        $result = false;
        $data = null;
        $user = User::where('clockin_code', $request->code)
                    // ->with('clockin:id,iduser,action,clockin')
                    ->get(['id','firstname','lastname','urlpicture'])
                    ->first();

        if($user) {
            $result = true;

            $clockin =  $this->getCurrentClockin($user->id);
            $pause =  $this->getClockinPause($user);

            $user = $user->toArray();
            $user['clockin'] = $clockin;
            $user['pause'] = $pause;
        }

        return response()->json([
            "result" => $result,
            "user" => $user
        ]);
    }
    
    public function getCurrentClockin($iduser)
    {
        return ClockinLog::select('id', 'iduser', 'action', 'clockin')
                        ->whereHas('activity', function ($query) {
                            $query->where('timesensitive', 1);
                        })
                        ->where('action', 'CLOCK-IN')
                        ->where('iduser', $iduser)
                        ->first();
    }
    
    public function getClockinPause($user)
    {
        return ClockinLog::select('id', 'iduser', 'action', 'clockin')
                            ->whereHas('activity', function ($query) {
                                $query->where('timesensitive', 0);
                            })
                            ->where('action', 'CLOCK-IN')
                            ->where('iduser', $user->id)
                            ->first();
    }
    
    public function registerClockinPause($request)
    {
        if ($request->id == 0) 
        {   
            $clockin =  $this->getCurrentClockin($request->iduser);

            if (!is_null($clockin)) {
                ClockinLog::create($request->all());
            }
        } else {
            ClockinLog::where('id', $request->id)
                        ->update($request->all());
        }

        return response()->json([
            "result" => true
        ]);
    }
}