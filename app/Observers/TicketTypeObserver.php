<?php

namespace App\Observers;

use App\Models\TicketType;
use App\Repositories\SettingUpdateRepository;

class TicketTypeObserver
{
    protected $settingUpdateRepository;

    public function __construct(SettingUpdateRepository $settingUpdateRepository)
    {
        $this->settingUpdateRepository = $settingUpdateRepository;
    }

    public function created(TicketType $ticketType)
    {
        $this->settingUpdateRepository->register($ticketType);
    }

    public function updated(TicketType $ticketType)
    {
        $this->settingUpdateRepository->register($ticketType);
    }

    public function deleted(TicketType $ticketType)
    {
        //
    }

    public function restored(TicketType $ticketType)
    {
        //
    }

    public function forceDeleted(TicketType $ticketType)
    {
        //
    }
}
