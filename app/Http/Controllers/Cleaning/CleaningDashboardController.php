<?php

namespace App\Http\Controllers\Cleaning;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\Cleaning\CleaningDashboardRepository;
use App\Repositories\Cleaning\CleaningPlanRepository;
use App\Repositories\Cleaning\CleaningStatusRepository;

class CleaningDashboardController extends Controller
{
    protected $cleaningDashboardRepository;
    protected $cleaningPlanRepository;
    protected $cleaningStatusRepository;

    public function __construct()
    {
        $this->cleaningDashboardRepository = new CleaningDashboardRepository;
        $this->cleaningPlanRepository      = new CleaningPlanRepository;
        $this->cleaningStatusRepository    = new CleaningStatusRepository;
    }

    public function index()
    {
        $breadcrumbs = [
            ['link' => "/config-cleaningschedule", 'name' => "Cleaning Schedule"], ['link' => "/cleaning-assign", 'name' => "Cleaning Assign"], ['name' => "Dashboard"], 
        ];

        $pageConfigs = [
            'verticalMenuNavbarType' => 'sticky',
            'pageHeader' => true,
        ];

        return view('pages.cleaning.dashboard-cleaning', [
            'pageConfigs'    => $pageConfigs,
            'breadcrumbs'    => $breadcrumbs,
            'cleaningStatus' => $this->cleaningStatusRepository->getList(),
            'cleaningSpots'  => $this->cleaningPlanRepository->getCleaningSpots(),
            'cleaningStaff'  => $this->cleaningPlanRepository->getCleaningStaff(),
            'cleaningItems'  => $this->cleaningPlanRepository->getCleaningItems()
        ]);
    }

    public function getCleaningSpots(Request $request)
    {
        return $this->cleaningDashboardRepository->getCleaningSpots($request);
    }   

    public function getCleaningPlans(Request $request)
    {
        return $this->cleaningDashboardRepository->getCleaningPlans($request);
    }   

    public function getCleaningChecklist(Request $request)
    {
        return $this->cleaningDashboardRepository->getCleaningChecklist($request);
    }   

    public function getCleaningNotes(Request $request)
    {
        return $this->cleaningDashboardRepository->getCleaningNotes($request);
    }   

    public function changeCleaningStatus(Request $request)
    {
        return $this->cleaningDashboardRepository->changeCleaningStatus($request);
    }   

    public function createCleaningPlan(Request $request)
    {
        return $this->cleaningDashboardRepository->createCleaningPlan($request);
    }  

    public function deleteCleaningPlan(Request $request)
    {
        return $this->cleaningDashboardRepository->deleteCleaningPlan($request);
    }  

    public function getLastCleaningChange(Request $request)
    {
        return $this->cleaningDashboardRepository->getLastCleaningChange($request);
    }  

    public function initializeCleaningDashboard()
    {
        return $this->cleaningDashboardRepository->initializeCleaningDashboard();
    }  
}
