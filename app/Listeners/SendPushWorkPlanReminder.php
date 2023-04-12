<?php

namespace App\Listeners;

use App\Events\SendWorkPlanReminder;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;
use FirebaseNotification;

class SendPushWorkPlanReminder
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  SendWorkPlanReminder  $event
     * @return void
     */
    public function handle(SendWorkPlanReminder $event)
    {
        $message = "Tienes " . $event->data->tickets . " tareas pendientes";

        $tokens  = $this->getTokens((array)$event->data->iduser);

        $data = (object)['id' => null];

        FirebaseNotification::sendNotification($tokens, "Tareas Programadas", $message, $data, "workplan");
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
