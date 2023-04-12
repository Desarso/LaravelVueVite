<?php

namespace App\Observers;

use App\Models\SpotType;
use App\Repositories\SettingUpdateRepository;

class SpotTypeObserver
{
    protected $settingUpdateRepository;

    public function __construct(SettingUpdateRepository $settingUpdateRepository)
    {
        $this->settingUpdateRepository = $settingUpdateRepository;
    }

    public function created(SpotType $spotType)
    {
        $this->settingUpdateRepository->register($spotType);
    }

    public function updated(SpotType $spotType)
    {
        $this->settingUpdateRepository->register($spotType);
    }

    public function deleted(SpotType $spotType)
    {
        //
    }

    public function restored(SpotType $spotType)
    {
        //
    }

    public function forceDeleted(SpotType $spotType)
    {
        //
    }
}
