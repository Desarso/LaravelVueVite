<?php

namespace App\Http\Controllers;
use App\Repositories\WorkPlanRepository;
use App\Repositories\SpotRepository;
use Illuminate\Http\Request;
use App\Models\WorkPlan;
use App\Exports\MainWorkPlanExport;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class WorkPlanController extends Controller
{
    protected $workPlanRepository;
    protected $spotRepository;

    public function __construct()
    {
        $this->workPlanRepository = new WorkPlanRepository;
        $this->spotRepository = new SpotRepository;
    }

    public function index()
    {
        $breadcrumbs = [
            ['link'=>"/",'name'=> 'Home'], ['link'=>"/config-dashboard",'name'=> "Configuration"], ['name'=>"Work Plans"]
        ];

        $pageConfigs = [
            'pageHeader' => true,
        ];

        $user_branches = $this->spotRepository->getUserBranch();

        return view('/pages/config/workPlan/index', [       
            'pageConfigs' => $pageConfigs,
            'breadcrumbs' => $breadcrumbs,
            'user_branches' => $user_branches,
        ]);
    }

    public function viewWorkPlan(Request $request)
    {
        $breadcrumbs = [
            ['link' => "/", 'name' => 'Home'], ['link'=>"/config-work-plans",'name'=> "Work Plans"]
        ];

        $pageConfigs = [
            'pageHeader' => true,
        ];

        return view('/pages/workPlan/index', [       
            'pageConfigs' => $pageConfigs,
            'breadcrumbs' => $breadcrumbs,
            'workPlans'   => $this->workPlanRepository->getWorkPlanList(),
            'planners'    => $this->workPlanRepository->getPlannerList($request),
            'workPlanSelected'  => ($request->has('idworkplan') ? $request->idworkplan : 'null'),
        ]);
    }

    public function create(Request $request)
    {
        return $this->workPlanRepository->create($request);
    }

    public function update(Request $request)
    {
        return $this->workPlanRepository->update($request);
    }

    public function delete(Request $request)
    {
        return $this->workPlanRepository->delete($request);
    }

    public function restore(Request $request)
    {
        return $this->workPlanRepository->restore($request);
    }

    public function getAll(Request $request)
    {
        return $this->workPlanRepository->getAll($request);
    }

    public function getData(Request $request)
    {
        return $this->workPlanRepository->getData($request);
    }

    public function getWorkPlanAPP(Request $request)
    {
        return $this->workPlanRepository->getWorkPlanAPP($request);
    }

    public function getPlannerList(Request $request)
    {
        return $this->workPlanRepository->getPlannerList($request);
    }

    public function getPlannerToEvaluateAPP(Request $request)
    {
        return $this->workPlanRepository->getPlannerToEvaluateAPP($request);
    }

    public function checkPendingPlansApp(Request $request)
    {
        return $this->workPlanRepository->checkPendingPlanner($request);
    }

    public function exportToExcel(Request $request)
    {
        $myFile = Excel::raw(new MainWorkPlanExport($request), \Maatwebsite\Excel\Excel::XLSX);

        $response = array(
            'name' => (WorkPlan::find($request->idworkplan)->name . " - " . Carbon::parse($request->startDate)->format('F Y')), //no extention needed
            'file' => "data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,".base64_encode($myFile) //mime type of used format
         );
         
        return response()->json($response);
    }
    
    public function copyWorkPlan(Request $request)
    {
        return $this->workPlanRepository->copyWorkPlan($request);
    }
}
