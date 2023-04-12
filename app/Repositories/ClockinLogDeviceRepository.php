<?php

namespace App\Repositories;


use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\ClockinLog;
use App\Models\ClockinLogSummary;
use App\Models\Shift;
use App\Models\Holiday;
use App\Models\UserSchedule;
use App\Models\User;
use App\Enums\ShiftType;
use Illuminate\Support\Facades\Auth;
use Session;

class ClockinLogDeviceRepository
{
    protected $bufferClockin;

    public function __construct()
    {
        $this->bufferClockin = 5;
    }

    public function registerAttendanceDevice($request)
    { 
        $data = json_decode($request->request_data);
        $data = $data->ApiRequestInfo;

        if($data->Operation == 'RealTimePunchLog') 
        {
            $user = User::find($data->UserId);
            
            if (is_null($user)) {
                return response()->json(["Status" => "done"]);
            }

            $action = $this->getAttendanceAction($data);

            if($action == 'Clockin') 
            {
                $this->registerAttendanceClockin($data);
            } 
            else if($action == 'Clockout') 
            {
                $this->registerAttendanceClockout($data);
            }
        }

        return response()->json(["Status" => "done"]);
    }

    private function registerAttendanceClockin($data)
    { 
        $date = str_replace(" GMT -0600","", $data->OperationTimeEx);
        $clockin = Carbon::createFromFormat('Y-m-d H:i:s', $date, 'America/Costa_Rica');

        $shift = $this->getShift($data, $clockin);

        $lateTime = 0;
        $outOfTime = 0;

        if($shift && $shift->type != ShiftType::DayOff)
        {
            $clockin = Carbon::createFromFormat('Y-m-d H:i:s', $date, 'America/Costa_Rica');
            $start = $shift->start->day($clockin->day)->addMinutes($this->bufferClockin);

            if($clockin->greaterThan($start))
            {
                $lateTime = $start->diffInSeconds($clockin); 
            }

            $diff = $start->diffInMinutes($clockin);

            if($diff > 120)
            {
                $outOfTime = 1;
            }
        }

        
        ClockinLogSummary::firstOrCreate(
            [ 
                'iduser' => $data->UserId, 
                'date' => $clockin->format('Y-m-d')
            ], 
            [ 
                'late_time' => $lateTime, 
                'isholiday' => $this->checkHoliday() 
            ]
        );
        
        $clockin2 = $clockin->setTimezone('UTC');

        ClockinLog::create(['iduser' => $data->UserId, 'clockin' => $clockin2, 'out_of_time' => $outOfTime]);
    }

    private function checkActiveClockin($iduser, $yesterday)
    {
        $date =  $yesterday->setTimezone('UTC');
        return ClockinLog::where('iduser', $iduser)
                         ->whereNull('clockout')
                         ->whereDate('clockin', '>=', $date)
                         ->latest()
                         ->first();
    }

    private function checkLastClockinLog($iduser, $currentTime)
    {
        $clockinLog = ClockinLog::where('iduser', $iduser)
                                ->latest()
                                ->first();

        if (is_null($clockinLog)) return false;

        $date = (!is_null($clockinLog->clockout)) ? $clockinLog->clockout : $clockinLog->clockin;
        $result = $date->addMinutes(5)->greaterThan($currentTime);

        return $result;
    }

    private function lastClockOut($iduser)
    {
        return ClockinLog::where('iduser', $iduser)
                         ->whereNotNull('clockout')
                         ->latest()
                         ->first();
    }

    private function checkHoliday()
    {
        return Holiday::whereDay('date', '=', date('d'))
                      ->whereMonth('date', '=', date('m'))
                      ->exists();
    }

    private function getHour($data)
    { 
        $date = str_replace(" GMT -0600","", $data->OperationTimeEx);
        return Carbon::createFromFormat('Y-m-d H:i:s', $date, 'America/Costa_Rica');
    }

    private function registerAttendanceClockout($data)
    { 
        $yesterday = $this->getHour($data)->subDay(1);

        $clockinLog = $this->checkActiveClockin($data->UserId, $yesterday);

        if(is_null($clockinLog)) return 0;

        $shift = $this->getShift($data, $clockinLog->clockin);

        $clockout  = $this->getHour($data);

        if(($shift && $shift->type != ShiftType::DayOff) && (!$clockinLog->out_of_time))
        {
            $endTime   = $shift->end;

            $clockout2 = $clockout->setTimezone('UTC');
            $clockinLog->clockout = $clockout2;

            $start = $shift->start->day($clockinLog->clockin->day)->setTimezone('UTC');

            $startTime = ($start > $clockinLog->clockin) ? $start: $clockinLog->clockin;

            $duration = $startTime->diffInSeconds($clockout2);

            $clockinLog->duration = $duration;

            $endTime->day($clockinLog->clockout->day);

            $durationShift = $this->getExpectedDuration($shift);
            // [7, 1]

            if($duration > $durationShift)
            {
                // contar minutos despues de horario regular 8H
                $over_time = 0;
                $extra_hours = 0;
                $extra = 0;

                if ((8 *3600) < $duration) {
                    $extra_hours = $duration - (8 *3600);
                    $over_time = $this->getOverTime($extra_hours);
                }

                $extra = ($duration - $durationShift) - $extra_hours;

                $over_time += $this->getOverTime($extra, true);

                $clockinLogSummary = ClockinLogSummary::where('iduser', $clockinLog->iduser)
                                                      ->whereDate('date', $clockinLog->clockin->format('Y-m-d'))
                                                      ->first();

                switch ($shift->type)
                {
                    case 'DAY':
                        $clockinLogSummary->regular_time = $over_time;
                        break;

                    case 'NIGHT':
                        $clockinLogSummary->double_time = $over_time;
                        break;

                    case 'MIX':
                        $clockinLogSummary->overtime = $over_time;
                        break;
                }

                $clockinLogSummary->save();
            }

            $clockinLog->action = 'CLOCK-OUT';
            $clockinLog->save();
        } 
        else
        {
            $duration = $clockinLog->clockin->diffInSeconds($clockout);

            $clockinLog->action   = 'CLOCK-OUT';
            $clockinLog->clockout = $clockout->setTimezone('UTC');
            $clockinLog->duration = $duration;
            $clockinLog->save();
        }
    }

    private function getExpectedDuration($shift)
    {
        $expectedDuration = 0;

        switch ($shift->type)
        {
            case 'DAY':
                $expectedDuration = 8 * 3600;
                break;

            case 'NIGHT':
                $expectedDuration = 6 * 3600;
                break;

            case 'MIX':
                $expectedDuration = 7 * 3600;
                break;
        }

        $startTime = Carbon::parse($shift->start);
        $endTime   = Carbon::parse($shift->end);

        $shiftDuration = $startTime->diffInSeconds($endTime);

        return ($shiftDuration < $expectedDuration) ? $shiftDuration : $expectedDuration;
    }

    private function getShift($request, $date)
    {
        $userSchedule = UserSchedule::with('shift')
                                    ->where('iduser', $request->UserId)
                                    ->whereDate('date', '=', $date)
                                    ->first();

        return (!is_null($userSchedule) ? $userSchedule->shift : false);
    }

    private function getOverTime($secOver, $useElse = false)
    {
        $over_time = 0;
        $minsOver = $secOver / 60;

        if($minsOver <= 65 && $useElse == false) {

            if ($minsOver == 65) {
                $over_time = 1.0;
            } else if ($minsOver >= 35) {
                $over_time = 0.5;
            }
        }
        else
        {
            $over_time = bcdiv($minsOver / 60, 1, 1);
            $decimals = explode('.', $over_time);

            if(count($decimals) > 1)
            {
                $minHalf = $minsOver - ($decimals[0] * 60);

                if ($minHalf >= 30) {
                    $over_time = $decimals[0] + 0.5;
                } else {
                    $over_time = $decimals[0] + 0.0;
                }
            }
        }

        return ($over_time * 3600);
    }

    private function getAttendanceAction($data)
    { 
        $currentTime = $this->getHour($data);
        $yesterday = $this->getHour($data)->subDay(1);

        //Validamos que el clockin no sea muy seguido de parte del usario (presionan varias veces la máquina)
        $lastLog = $this->checkLastClockinLog($data->UserId, $currentTime);
        if($lastLog) return false;

        //Verifica si hay clockin activo de ayer o del día de la acción (clockin o clockout)
        $clockinLog = $this->checkActiveClockin($data->UserId, $yesterday);
        
        
        if($clockinLog)
        {

            $shift = $this->getShift($data, $clockinLog->clockin);

            if($shift)
            {
                $end = Carbon::parse($shift->end)->day($clockinLog->clockin->day);
                $start = Carbon::parse($shift->start)->day($clockinLog->clockin->day);

                if($end < $start) $end->add(1, 'day');

                $nextDay = $clockinLog->clockin->add(1, 'day');
                
                $shift = $this->getShift($data, $nextDay);

                if(!$shift || $shift->type == ShiftType::DayOff) return 'Clockout';

                $startTime = Carbon::parse($shift->start);
                $startTime->day($nextDay->day);

                $diffEnd = $currentTime->diffInSeconds($end);
                $diffStart = $currentTime->diffInSeconds($startTime);

                return ($diffEnd < $diffStart) ? 'Clockout' : 'Clockin';
            } else {

                if ($currentTime->isSameDay($clockinLog->clockin)) {
                    return 'Clockout';
                } else {  

                    $diff = $currentTime->diffInHours($clockinLog->clockin);
                    return ($diff > 18) ? 'Clockin' : 'Clockout';
                }

            }
        }

        $lastClockinLog = $this->lastClockOut($data->UserId);
        
        if ($lastClockinLog) {
            if($lastClockinLog->clockout->addMinutes(5)->greaterThan($currentTime)) return false;
        }

        return 'Clockin';
    }

    public function calculateDurationFromSchedule($shift, $clockin, $clockout)
    {
        $startTime = ($shift->start > $clockin) ? $shift->start : $clockin;
        $duration = $startTime->diff($clockout);


        return $duration;
        
    }
}