<?php

namespace App\Repositories\Cleaning;
use Illuminate\Support\Facades\DB;
use App\Repositories\ChecklistOptionRepository;
use App\Models\Cleaning\CleaningChecklist;
use App\Models\Cleaning\CleaningPlan;
use Carbon\Carbon;
use App\Helpers\Helper;

class CleaningChecklistRepository
{
    protected $checklistOptionRepository;

    public function __construct()
    {
        $this->checklistOptionRepository = new ChecklistOptionRepository;
    }

    public function getCleaningChecklistAPP($request)
    {
        $checklist = $this->getCleaningChecklist($request);

        if(is_null($checklist)) 
        {
            $this->createCleaningChecklist($request);
            $checklist = $this->getCleaningChecklist($request);
        }

        if (is_null($checklist)) {
            return response()->json(['success' => false]);
        } else {
            return response()->json(['success' => true, 'options' => $checklist->options]);
        }
    }

    private function createCleaningChecklist($request)
    {
        $plan = CleaningPlan::find($request->id);

        if(!is_null($plan->item->idchecklist))
        {
            $checklist_copy = $this->checklistOptionRepository->getChecklistCopy($plan->item->idchecklist, $plan->id);
            $plan->checklists()->create($checklist_copy);
        }
    }

    private function getCleaningChecklist($request)
    {
        return CleaningChecklist::select("idplaner", "options", "results", "created_at")
                                    ->where('idplaner', $request->id)    
                                    ->first();

    }

    function syncCleaningChecklistAPP($request)
    {
        $model = CleaningChecklist::where('idplaner', $request->id)->first();
        $model->fill(['options' => $request->checklist])->save();

        return response()->json(['success' => true]);
    }
}