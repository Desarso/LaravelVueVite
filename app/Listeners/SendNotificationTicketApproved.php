<?php

namespace App\Listeners;

use App\Events\TicketApproved;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;
use FirebaseNotification;

class SendNotificationTicketApproved
{
    public function __construct()
    {
        //
    }

    public function handle(TicketApproved $event)
    {
        $users = $event->ticket->users->pluck('id')->toArray();
        
        $tokens = DB::table('wh_user_device')
                    ->join('wh_user', 'wh_user.id', '=', 'wh_user_device.iduser')
                    ->where('available', '=', 1)
                    ->where('iduser', '!=', $event->ticket->updated_by)
                    ->whereIn('iduser', $users)
                    ->select(['token', 'os'])
                    ->get();

        FirebaseNotification::sendNotification($tokens, $this->getTitle($event->ticket), $this->getMessage($event->ticket), $event->ticket);
    }

    private function getMessage($ticket)
    {
        $message = $ticket->approved == 1 ? "aprobada" : "reprobada";

        return "Esta tarea fue " . $message;
    }

    private function getTitle($ticket)
    {
        return $ticket->id . ' - ' . $ticket->name;
    }
}
