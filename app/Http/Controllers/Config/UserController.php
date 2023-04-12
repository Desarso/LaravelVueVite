<?php

namespace App\Http\Controllers\Config;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\UserRepository;
use App\Repositories\SpotRepository;
use App\Repositories\ShiftRepository;
use App\Repositories\ScheduleRepository;

class UserController extends Controller
{
    protected $userRepository;
    protected $shiftRepository;
    protected $scheduleRepository;

    public function __construct()
    {
        $this->middleware('auth', ['only' => 'index']);

        $this->userRepository = new UserRepository;
        $this->shiftRepository = new ShiftRepository;
        $this->scheduleRepository = new ScheduleRepository;
    }

    public function index(Request $request)
    {               
        $sr = new SpotRepository;
        $spots = json_encode($sr->getHierarchy());   

        $breadcrumbs = [
            ['link'=>"/",'name'=> 'Home'],['link'=>"/config-dashboard",'name'=>"Configuration"], ['name'=>"Users"]
        ];
           
        $pageConfigs = [
            'pageHeader' => true,            
        ];

        return view('/pages/config/users/index', [
            'pageConfigs' => $pageConfigs,
            'breadcrumbs' => $breadcrumbs,
            'spots' => $spots,
            'schedules' => $this->scheduleRepository->getList(),
            'attendance' => '[]',
            "open"        => ($request->has('open') ? 'true' : 'false')
        ]);
    }

    public function profile()
    {
        $breadcrumbs = [ ['link' => "/", 'name' => "Home"]];

        return view('user/profile', [
            'breadcrumbs' => $breadcrumbs
        ]);
    }

    public function getAll()
    {
        return $this->userRepository->getAll();
    }

    public function getList()
    {
        return $this->userRepository->getList();
    }

    public function create(Request $request)
    {
        return $this->userRepository->create($request);
    }

    public function update(Request $request)
    {
        return $this->userRepository->update($request);
    }

    public function delete(Request $request)
    {
        return $this->userRepository->delete($request);
    }

    public function getTeamUsers(Request $request)
    {
        return $this->userRepository->getTeamUsers($request);
    }

    public function saveSpots(Request $request) 
    {
        return $this->userRepository->saveSpots($request);
    }
    public function changeDarkMode(Request $request)
    {
        return $this->userRepository->changeDarkMode($request);
    }
    public function getListApp(Request $request)
    {
        return $this->userRepository->getListApp(null, $request);
    }

    public function updateAvatarApp(Request $request)
    {
        return $this->userRepository->updateAvatarApp($request);
    }

    public function updateUserApp(Request $request)
    {
        return $this->userRepository->updateUserApp($request);
    }

    public function saveProfile(Request $request)
    {
        return $this->userRepository->saveProfile($request);
    }

    public function resetPassword(Request $request)
    {
        return $this->userRepository->resetPassword($request);
    }

    public function changePassword(Request $request)
    {
        return $this->userRepository->changePassword($request);
    }

    public function saveShortcut(Request $request)
    {
        return $this->userRepository->saveShortcut($request);
    }

    public function savePreferences(Request $request)
    {
        return $this->userRepository->savePreferences($request);
    }

    public function getCleaningUsersAPP(Request $request)
    {
        return $this->userRepository->getCleaningUsersAPP($request);
    }

    public function checkForceloginAPP(Request $request)
    {
        return $this->userRepository->checkForceloginAPP($request);
    }

    public function changePhoto(Request $request)
    {
        return $this->userRepository->changePhoto($request);
    }

    public function disable(Request $request)
    {
        return $this->userRepository->disable($request);
    }

    public function restore(Request $request)
    {
        return $this->userRepository->restore($request);
    }

    public function changeAvailableApp(Request $request)
    {
        return $this->userRepository->changeAvailable($request);
    }

    public function getUserCount(Request $request)
    {
        return $this->userRepository->getUserCount($request);
    }

    public function setUserLimit(Request $request)
    {
        return $this->userRepository->setUserLimit($request);
    }
}
