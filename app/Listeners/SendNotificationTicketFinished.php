<?php

namespace App\Listeners;

use App\Events\TicketFinished;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;
use FirebaseNotification;

class SendNotificationTicketFinished
{
    public function __construct()
    {
        //
    }

    public function handle(TicketFinished $event)
    {
        $ticket = $event->ticket;

        $usersLogs = DB::table('wh_log')
                        ->where('idticket', $ticket->id)
                        ->where('action', 'CREATE_TICKET')
                        ->select('iduser')
                        ->pluck('iduser')
                        ->toArray();

        $tokens = $this->getTokens($event, $usersLogs);

        $title   = $this->getTitle($event->ticket);
        $message = $this->getMessage($event->ticket);

        FirebaseNotification::sendNotification($tokens, $title, $message, $event->ticket);
    }

    private function getTokens($event, $usersLogs)
    {
        $users = $event->ticket->users->pluck('id')->toArray();

        $tokens = DB::table('wh_user_device')
                    ->join('wh_user', 'wh_user.id', '=', 'wh_user_device.iduser')
                    ->where('available', '=', 1)
                    ->whereIn('iduser', $usersLogs)
                    ->whereNotIn('iduser', $users)
                    ->select(['token', 'os'])
                    ->get();

        return $tokens;
    }

    private function getMessage($ticket)
    {
        return $ticket->name . " finalizado";
    }

    private function getTitle($ticket)
    {
        return $ticket->code . ' - ' . $ticket->name;
    }
}
