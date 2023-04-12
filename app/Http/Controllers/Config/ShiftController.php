<?php

namespace App\Http\Controllers\Config;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\ShiftRepository;
use App\Repositories\ScheduleRepository;
use App\Repositories\OvertimeRepository;

class ShiftController extends Controller
{
    protected $shiftRepository;
    protected $scheduleRepository;
    protected $overtimeRepository;

    public function __construct()
    {
        $this->middleware('auth', ['only' => 'index']);

        $this->shiftRepository    = new ShiftRepository;
        $this->scheduleRepository = new ScheduleRepository;
        $this->overtimeRepository = new OvertimeRepository;
    }

    public function index()
    {
        $breadcrumbs = [ ['link'=>"/",'name'=> 'Home'],['link'=>"/config-dashboard",'name'=>"Configuration"], ['name'=> "Shift"] ];
           
        $pageConfigs = [ 'pageHeader' => true ];

        return view('pages.config.shift.index', [
            'pageConfigs' => $pageConfigs,
            'breadcrumbs' => $breadcrumbs,
            'schedules'   => $this->scheduleRepository->getList(),
            'overtimes'   => $this->overtimeRepository->getList()
        ]);
    }

    public function getAll()
    {
        return $this->shiftRepository->getAll();
    }

    public function getList()
    {
        return $this->shiftRepository->getList();
    }

    public function create(Request $request)
    {
        return $this->shiftRepository->create($request);
    }

    public function update(Request $request)
    {
        return $this->shiftRepository->update($request);
    }

    public function delete(Request $request)
    {
        return $this->shiftRepository->delete($request);
    }
}
