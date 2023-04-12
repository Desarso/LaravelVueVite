<?php

namespace App\Observers;

use App\Models\ChecklistOption;
use App\Repositories\SettingUpdateRepository;

class ChecklistOptionObserver
{
    protected $settingUpdateRepository;

    public function __construct(SettingUpdateRepository $settingUpdateRepository)
    {
        $this->settingUpdateRepository = $settingUpdateRepository;
    }

    public function created(ChecklistOption $checklistOption)
    {
        $checklistOption->checklist->touch();
    }

    public function updated(ChecklistOption $checklistOption)
    {
        $checklistOption->checklist->touch();
    }

    public function deleted(ChecklistOption $checklistOption)
    {
        $checklistOption->checklist->touch();
    }

    public function restored(ChecklistOption $checklistOption)
    {
        //
    }

    public function forceDeleted(ChecklistOption $checklistOption)
    {
        //
    }
}
