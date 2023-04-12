<?php

namespace App\Observers;

use App\Models\Cleaning\CleaningPlan;
use App\Repositories\Cleaning\CleaningLogRepository;
use App\Enums\CleaningLog as CleaningLogAction;
use App\Events\CleaningPlanCreated;


class CleaningPlanObserver
{
    protected $cleaningLogRepository;

    public function __construct()
    {
        $this->cleaningLogRepository = new CleaningLogRepository;
    }

    public function created(CleaningPlan $cleaningPlan)
    {
        if(!is_null($cleaningPlan->iduser) && ($cleaningPlan->iduser != 0)) 
        {
            $this->cleaningLogRepository->register(CleaningLogAction::CreatePlan, $cleaningPlan);
        }

        event(new CleaningPlanCreated($cleaningPlan));
    }

    public function updated(CleaningPlan $cleaningPlan)
    {
        $this->cleaningLogRepository->register(CleaningLogAction::EditPlan, $cleaningPlan);
    }

    public function deleted(CleaningPlan $cleaningPlan)
    {
        $this->cleaningLogRepository->register(CleaningLogAction::DeletePlan, $cleaningPlan);
    }

    public function restored(CleaningPlan $cleaningPlan)
    {
        //
    }

    public function forceDeleted(CleaningPlan $cleaningPlan)
    {
        //
    }
}
