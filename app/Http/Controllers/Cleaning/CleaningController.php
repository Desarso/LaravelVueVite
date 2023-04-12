<?php

namespace App\Http\Controllers\Cleaning;

use App\Http\Controllers\Controller;
use App\Repositories\Cleaning\CleaningPlanRepository;
use App\Repositories\Cleaning\CleaningScheduleRepository;
use App\Repositories\Cleaning\CleaningStatusRepository;
use App\Repositories\Cleaning\CleaningNoteRepository;
use App\Repositories\Cleaning\CleaningChecklistRepository;
use App\Repositories\Cleaning\CleaningLogRepository;
use App\Repositories\ItemRepository;
use App\Repositories\SpotRepository;
use Illuminate\Http\Request;

class CleaningController extends Controller
{
    
    
    
    protected $cleaningschedule;
    protected $cleaningplan;
    protected $cleaningNote;
    protected $cleaningChecklist;
    protected $cleaningstatus;
    protected $item;
    protected $spot;
    protected $cleaningLogRepository;

    public function __construct(CleaningScheduleRepository $cleaningschedule,
        CleaningStatusRepository $cleaningstatus, 
        CleaningPlanRepository $cleaningplan, 
        CleaningNoteRepository $cleaningNote, 
        CleaningChecklistRepository $cleaningChecklist, 
        ItemRepository $item,
        SpotRepository $spot,
        CleaningLogRepository $cleaningLogRepository
    ) {
        
        
        $this->middleware('auth', ['only' => ['dashboard','cleaningSchedule','cleaningAssign']]);


        $this->cleaningschedule = $cleaningschedule;
        $this->cleaningplan = $cleaningplan;
        $this->cleaningNote = $cleaningNote;
        $this->cleaningChecklist = $cleaningChecklist;
        $this->cleaningstatus = $cleaningstatus;
        $this->item = $item;
        $this->spot = $spot;
        $this->cleaningLogRepository = $cleaningLogRepository;
    }

    public function cleaningSchedule()
    {
        $breadcrumbs = [
            ['link' => "/", 'name' => 'Home'], ['link' => "/config-dashboard", 'name' => "Configuration"], ['name' => "Cleaning Schedule"], ['link' => "/dashboard-cleaning", 'name' => "Dashboard"],
        ];

        $pageConfigs = [
            'pageHeader' => true,
        ];

        return view('/pages/cleaning/cleaning-schedule', [
            'items' => $this->item->getCleaningItems(),
            'spots' => $this->spot->getRequireCleaningSpots(),
            'pageConfigs' => $pageConfigs,
            'breadcrumbs' => $breadcrumbs
        ]);
    }

    public function cleaningAssign()
    {
        $breadcrumbs = [
            ['link' => "/", 'name' => 'Home'], ['link' => "/config-dashboard", 'name' => "Configuration"], ['name' => "Cleaning Schedule"], ['link' => "/dashboard-cleaning", 'name' => "Dashboard"],
        ];

        $pageConfigs = [
            'pageHeader' => true,
        ];

        return view('/pages/cleaning/cleaning-assign', [
            'pageConfigs'      => $pageConfigs,
            'breadcrumbs'      => $breadcrumbs,
            'cleaningStatus'   => $this->cleaningstatus->getAll(),
            'cleaningSpots'    => $this->cleaningplan->getCleaningSpots(),
            'cleaningStaff'    => $this->cleaningplan->getCleaningStaff(),
            'cleaningSettings' => json_encode($this->cleaningplan->getCleaningSettings())
        ]);
    }

    public function dashboard()
    {
        $breadcrumbs = [
            ['link' => "/", 'name' => 'Home'], ['link' => "/config-cleaningschedule", 'name' => "Cleaning Schedule"], ['link' => "/cleaning-assign", 'name' => "Cleaning Assign"], ['name' => "Dashboard"], 
        ];

        $pageConfigs = [
            'verticalMenuNavbarType' => 'sticky',
            'pageHeader' => true,
        ];

        return view('/pages/cleaning/dashboard-cleaning', [
            'cleaningtasktypes' => $this->item->getCleaningItems(),
            'cleaningstatuses'  => $this->cleaningstatus->getList(),
            'pageConfigs'       => $pageConfigs,
            'breadcrumbs'       => $breadcrumbs,
            'cleaningStaff'     => $this->cleaningplan->getCleaningStaff(),
            'cleaningSpots'     => $this->cleaningplan->getCleaningSpots(),
            'cleaningItems'     => $this->item->getCleaningItems(),
            'cleaningSettings'  => json_encode($this->cleaningplan->getCleaningSettings())
        ]);
    }

    public function getCleaningPlan(Request $request)
    {
        return $this->cleaningplan->getCleaningPlan($request);
    }

    public function getSpotCleaningPlan(Request $request)
    {
        return $this->cleaningplan->getSpotCleaningPlan($request);
    }

    public function generateCleaningPlan()
    {
        return $this->cleaningplan->createCleaningPlan();
    }

    public function initializeCleaning()
    {
        return $this->cleaningplan->initializeCleaning();
    }

    public function updateSpotCleaningInfo(Request $request)
    {
        return $this->cleaningplan->updateSpotCleaningInfo($request);
    }
 
    public function saveCleaningPlanSequence(Request $request)
    {
        return $this->cleaningplan->saveCleaningPlanSequence($request);
    }

    public function getCleaningSchedules()
    {
        return $this->cleaningschedule->getAll();
    }

    public function getCleaningSchedulesList()
    {
        return $this->cleaningschedule->getList();
    }

    public function createCleaningSchedule(Request $request)
    {
        return $this->cleaningschedule->create($request);
    }

    public function updateCleaningSchedule(Request $request)
    {
        return $this->cleaningschedule->update($request);
    }

    public function deleteCleaningSchedule(Request $request)
    {
        return $this->cleaningschedule->delete($request);
    }

    // APP`s functions

    public function searchRoomsAPP(Request $request)
    {
        return $this->cleaningschedule->searchRoomsAPP($request);
    }

    public function getRoomsAPP(Request $request)
    {
        return $this->cleaningschedule->getRoomsAPP($request);
    }

    public function getRoomByIdAPP(Request $request)
    {
        return $this->cleaningschedule->getRoomByIdAPP($request);
    }

    public function getCleaningStatusAPP(Request $request)
    {
        return $this->cleaningstatus->getAllAPP();
    }

    public function chageRoomStatusAPP(Request $request)
    {
        return $this->cleaningplan->chageRoomStatusAPP($request);
    }

    public function chageCleaningPlanStatusAPP(Request $request)
    {
        return $this->cleaningplan->chageCleaningPlanStatusAPP($request);
    }

    public function getMyCleaningPlanAPP(Request $request)
    {
        return $this->cleaningplan->getMyCleaningPlanAPP($request);
    }

    public function getCleaningNotesAPP(Request $request)
    {
        return  $this->cleaningNote->getCleaningNotesAPP($request);
    }

    public function CreateCleaningNotesAPP(Request $request)
    {
        return  $this->cleaningNote->CreateCleaningNotesAPP($request);
    }

    public function getCleaningChecklistAPP(Request $request)
    {
        return  $this->cleaningChecklist->getCleaningChecklistAPP($request);
    }

    public function syncCleaningChecklistAPP(Request $request)
    {
        return  $this->cleaningChecklist->syncCleaningChecklistAPP($request);
    }

    public function createCleaningPlanAPP(Request $request)
    {
        return  $this->cleaningplan->createCleaningPlanAPP($request);
    }

    public function createPlanFromSliderPlanAPP(Request $request)
    {
        return  $this->cleaningplan->createPlanFromSliderPlanAPP($request);
    }

    public function getCleaningPlanAPP(Request $request)
    {
        return  $this->cleaningplan->getCleaningPlanAPP($request);
    }

    public function getCleaningPlanBySpotAPP(Request $request)
    {
        return  $this->cleaningplan->getCleaningPlanBySpotAPP($request);
    }

    public function deleteCleaningPlanAPP(Request $request)
    {
        return  $this->cleaningplan->deleteCleaningPlanAPP($request);
    }

    public function assingCleaningPlanAPP(Request $request)
    {
        return  $this->cleaningplan->assingCleaningPlanAPP($request);
    }
    // APP`s functions


    // Cleaning-assign 
    public function getCleaningStaffWithPlans(Request $request)
    {
        return $this->cleaningplan->getCleaningStaffWithPlans($request);
    }

    public function getCleaningStaff(Request $request)
    {
        return $this->cleaningplan->getCleaningStaff($request);
    }

    public function getAvailableSpots(Request $request)
    {
        return $this->cleaningplan->getAvailableSpots($request);
    }

    public function assignCleaning(Request $request)
    {
        return $this->cleaningplan->assignCleaning($request);
    }

    public function findCleaningPlan(Request $request)
    {
        return $this->cleaningplan->findCleaningPlan($request);
    }

    public function editCleaningPlan(Request $request)
    {
        return $this->cleaningplan->editCleaningPlan($request);
    }

    public function moveCleaningPlan(Request $request)
    {
        return $this->cleaningplan->moveCleaningPlan($request);
    }

    public function deleteCleaningPlan(Request $request)
    {
        return $this->cleaningplan->deleteCleaningPlan($request);
    }

    public function getCleaningSpots()
    {
        return $this->cleaningplan->getCleaningSpots();
    }

    public function getCleaningItems()
    {
        return $this->cleaningplan->getCleaningItems();
    }
    // Cleaning-assign

    // Cleaning-log
    public function getAllCleaningLog(Request $request)
    {
        return $this->cleaningLogRepository->getAll($request);
    }
    // Cleaning-log
}
