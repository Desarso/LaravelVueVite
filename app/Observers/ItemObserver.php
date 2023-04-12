<?php

namespace App\Observers;

use App\Models\Item;
use App\Repositories\SettingUpdateRepository;

class ItemObserver
{
    protected $settingUpdateRepository;

    public function __construct(SettingUpdateRepository $settingUpdateRepository)
    {
        $this->settingUpdateRepository = $settingUpdateRepository;
    }

    public function creating(Item $item)
    {
        if(is_null($item->users))
        {
            $item->users = json_encode([]);
        }
    }

    public function created(Item $item)
    {
        $this->settingUpdateRepository->register($item);
    }

    public function updated(Item $item)
    {
        $this->settingUpdateRepository->register($item);
    }

    public function deleted(Item $item)
    {
        $this->settingUpdateRepository->register($item);
    }

    public function restored(Item $item)
    {
        //
    }

    public function forceDeleted(Item $item)
    {
        //
    }
}
