<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Enums\LogAction;
use App\Repositories\LogRepository;

class LogSuccessfulLogin
{
    public function __construct()
    {
        //
    }

    public function handle(Login $event)
    {
        $logRepository = new LogRepository;
        //$logRepository->register(LogAction::Login, null, $event->user->id, 'Inicio de sesión');
    }
}
