<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Logout;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Enums\LogAction;
use App\Repositories\LogRepository;

class LogSuccessfulLogout
{
    public function __construct()
    {
        //
    }

    public function handle(Logout $event)
    {
        $logRepository = new LogRepository;
        //$logRepository->register(LogAction::Login, null, $event->user->id, 'Cierre de sesión');
    }
}
