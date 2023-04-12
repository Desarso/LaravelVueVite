<?php

namespace App\Observers;

use App\Models\UserTeam;
use App\Repositories\SettingUpdateRepository;

class UserTeamObserver
{
    protected $settingUpdateRepository;

    public function __construct(SettingUpdateRepository $settingUpdateRepository)
    {
        $this->settingUpdateRepository = $settingUpdateRepository;
    }

    public function created(UserTeam $userTeam)
    {
        $this->settingUpdateRepository->register($userTeam);
    }

    public function updated(UserTeam $userTeam)
    {
        $this->settingUpdateRepository->register($userTeam);
    }

    public function deleted(UserTeam $userTeam)
    {
        $this->settingUpdateRepository->register($userTeam);
    }

    public function restored(UserTeam $userTeam)
    {
        //
    }

    public function forceDeleted(UserTeam $userTeam)
    {
        //
    }
}
