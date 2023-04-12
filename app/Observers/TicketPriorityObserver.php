<?php

namespace App\Observers;

use App\Models\TicketPriority;
use App\Repositories\SettingUpdateRepository;

class TicketPriorityObserver
{
    protected $settingUpdateRepository;

    public function __construct(SettingUpdateRepository $settingUpdateRepository)
    {
        $this->settingUpdateRepository = $settingUpdateRepository;
    }

    public function creating(TicketPriority $item)
    {
    }

    public function created(TicketPriority $item)
    {
    }

    public function updated(TicketPriority $item)
    {
        $this->settingUpdateRepository->register($item);
    }

    public function deleted(TicketPriority $item)
    {
    }

    public function restored(TicketPriority $item)
    {
        //
    }

    public function forceDeleted(TicketPriority $item)
    {
        //
    }
}
