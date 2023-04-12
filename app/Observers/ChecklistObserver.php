<?php

namespace App\Observers;

use App\Models\Checklist;
use App\Repositories\SettingUpdateRepository;

class ChecklistObserver
{
    protected $settingUpdateRepository;

    public function __construct(SettingUpdateRepository $settingUpdateRepository)
    {
        $this->settingUpdateRepository = $settingUpdateRepository;
    }

    public function created(Checklist $checklist)
    {
        $this->settingUpdateRepository->register($checklist);
    }

    public function updated(Checklist $checklist)
    {
        $this->settingUpdateRepository->register($checklist);
    }

    public function deleted(Checklist $checklist)
    {
        //
    }

    public function restored(Checklist $checklist)
    {
        //
    }

    public function forceDeleted(Checklist $checklist)
    {
        //
    }
}
