<?php

namespace App\Listeners;

use App\Events\SendReminder;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Enums\ReminderType;
use Illuminate\Support\Facades\DB;
use FirebaseNotification;

class SendPushReminder
{
    public function __construct()
    {
        //
    }

    public function handle(SendReminder $event)
    {
        $message = "Tienes " . $event->data->tickets . " tareas vencidas";

        $tokens  = $this->getTokens((array)$event->data->iduser);

        $data = (object)['id' => null];

        FirebaseNotification::sendNotification($tokens, "Recordatorio", $message, $data, "task_overdue");
    }

    private function getTokens($users)
    {
        $tokens = DB::table('wh_user_device')
                    ->join('wh_user', 'wh_user.id', '=', 'wh_user_device.iduser')
                    ->where('available', '=', 1)
                    ->whereIn('iduser', $users)
                    ->select(['token', 'os'])
                    ->get();
                    
        return $tokens;
    }
}
