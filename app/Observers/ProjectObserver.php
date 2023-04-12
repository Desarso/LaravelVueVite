<?php

namespace App\Observers;

use App\Models\Project;
use App\Repositories\SettingUpdateRepository;

class ProjectObserver
{
    protected $settingUpdateRepository;

    public function __construct(SettingUpdateRepository $settingUpdateRepository)
    {
        $this->settingUpdateRepository = $settingUpdateRepository;
    }

    public function created(Project $project)
    {
        $this->settingUpdateRepository->register($project);
    }

    public function updated(Project $project)
    {
        $this->settingUpdateRepository->register($project);
    }

    public function deleted(Project $project)
    {
        //
    }

    public function restored(Project $project)
    {
        //
    }

    public function forceDeleted(Project $project)
    {
        //
    }
}
