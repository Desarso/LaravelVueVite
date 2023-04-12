<?php

namespace App\Observers;

use App\Models\Tag;
use App\Repositories\SettingUpdateRepository;

class TagObserver
{
    protected $settingUpdateRepository;

    public function __construct(SettingUpdateRepository $settingUpdateRepository)
    {
        $this->settingUpdateRepository = $settingUpdateRepository;
    }

    public function created(Tag $tag)
    {
        $this->settingUpdateRepository->register($tag);
    }

    public function updated(Tag $tag)
    {
        $this->settingUpdateRepository->register($tag);
    }

    public function deleted(Tag $tag)
    {
        $this->settingUpdateRepository->register($tag);
    }

    public function restored(Tag $tag)
    {
        //
    }

    public function forceDeleted(Tag $tag)
    {
        //
    }
}
