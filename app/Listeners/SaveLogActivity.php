<?php

namespace App\Listeners;

use App\Events\LogActivity;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Repositories\LogRepository;

class SaveLogActivity
{
    public function __construct()
    {
        //
    }

    public function handle(LogActivity $event)
    {
        $logRepository = new LogRepository;
        $logRepository->register($event->action, $event->ticket, $event->iduser, $event->data);
    }
}
