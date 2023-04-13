<?php

namespace App\Http\Controllers\User;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Exports\UserScheduleExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Repositories\UserScheduleRepository;


class UserScheduleController extends Controller
{

    protected $userScheduleRepository;

    public function __construct()
    {
        $this->userScheduleRepository = new UserScheduleRepository;
    }

    public function export(Request $request) 
    {
        $myFile = Excel::raw(new UserScheduleExport($request), \Maatwebsite\Excel\Excel::XLSX);

        $response = array(
            'name' => "Schedules", //no extention needed
            'file' => "data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,".base64_encode($myFile) //mime type of used format
         );
         
        return response()->json($response);
    }

    public function viewUserSchedule()
    {
        $breadcrumbs = [ ['link' => "/", 'name' => 'Home'], ['link' => "/config-dashboard", 'name' => "Configuration"], ['name' => "Calendario"] ];

        $pageConfigs = [ 'pageHeader' => true ];

        return view('/pages/config/schedule/user-schedule', [         
            'pageConfigs' => $pageConfigs,
            'breadcrumbs' => $breadcrumbs,
            'schedules'   => $this->userScheduleRepository->getListShift()
        ]);
    }

    public function getUserSchedule(Request $request)
    {
        return $this->userScheduleRepository->getUserSchedule($request);
    }

    public function updateUserSchedule(Request $request)
    {
        return $this->userScheduleRepository->updateUserSchedule($request);
    }
}
