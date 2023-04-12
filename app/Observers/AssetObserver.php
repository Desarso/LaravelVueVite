<?php

namespace App\Observers;

use App\Models\Asset;
use App\Repositories\SettingUpdateRepository;

class AssetObserver
{
    protected $settingUpdateRepository;

    public function __construct(SettingUpdateRepository $settingUpdateRepository)
    {
        $this->settingUpdateRepository = $settingUpdateRepository;
    }

    public function created(Asset $asset)
    {
        $this->settingUpdateRepository->register($asset);
    }

    public function updated(Asset $asset)
    {
        $this->settingUpdateRepository->register($asset);
    }

    public function deleted(Asset $asset)
    {
        //
    }

    public function restored(Asset $asset)
    {
        //
    }

    public function forceDeleted(Asset $asset)
    {
        //
    }
}
