<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\ClockinLogRepository;
use App\Repositories\ClockinLogDeviceRepository;

class ClockinLogController extends Controller
{
    protected $clockinLogRepository;
    protected $clockinLogDeviceRepository;

    public function __construct()
    {
        $this->clockinLogRepository = new ClockinLogRepository;
        $this->clockinLogDeviceRepository = new ClockinLogDeviceRepository;
    }

    public function registerClockinAPP(Request $request)
    {
        return $this->clockinLogRepository->registerClockinAPP($request);
    }

    public function registerClockoutAPP(Request $request)
    {
        return $this->clockinLogRepository->registerClockoutAPP($request);
    }

    public function getUserClockinLogAPP(Request $request)
    {
        return $this->clockinLogRepository->getUserClockinLogAPP($request);
    }

    public function getUserClockinSummaryAPP(Request $request)
    {
        return $this->clockinLogRepository->getUserClockinSummaryAPP($request);
    }

    public function getUserClockLogHistoryAPP(Request $request)
    {
        return $this->clockinLogRepository->getUserClockLogHistoryAPP($request);
    }
    
    public function approveClockinTime(Request $request)
    {
        return $this->clockinLogRepository->approveClockinTime($request);
    }

    //Funciones para CAM
    public function registerAttendanceDevice(Request $request)
    {
        return $this->clockinLogDeviceRepository->registerAttendanceDevice($request);
    }

    public function verifyClockInCodeAPP(Request $request)
    {
        return $this->clockinLogRepository->verifyClockInCodeAPP($request);
    }

    public function pauseClockinAPP(Request $request)
    {
        return $this->clockinLogRepository->registerClockinPause($request);
    }
}
