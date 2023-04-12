<?php

namespace App\Observers;

use App\Models\Team;
use App\Repositories\SettingUpdateRepository;

class TeamObserver
{
    protected $settingUpdateRepository;

    public function __construct(SettingUpdateRepository $settingUpdateRepository)
    {
        $this->settingUpdateRepository = $settingUpdateRepository;
    }

    public function created(Team $team)
    {
        $this->settingUpdateRepository->register($team);
    }

    public function updated(Team $team)
    {
        $this->settingUpdateRepository->register($team);
    }

    public function deleted(Team $team)
    {
        //
    }

    public function restored(Team $team)
    {
        //
    }

    public function forceDeleted(Team $team)
    {
        //
    }
}
