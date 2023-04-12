<?php

namespace App\Listeners;

use App\Events\TicketAssigned;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;
use App\Repositories\NotificationRepository;
use FirebaseNotification;

class SendNotificationTicketAssigned
{
    protected $notificationRepository;

    public function __construct()
    {
        $this->notificationRepository = new NotificationRepository;
    }

    public function handle(TicketAssigned $event)
    {
        $tokens = DB::table('wh_user_device')
                    ->join('wh_user', 'wh_user.id', '=', 'wh_user_device.iduser')
                    ->where('available', '=', 1)
                    ->where('iduser', '!=', $event->ticket->updated_by)
                    ->whereIn('iduser', $event->users)
                    ->select(['token', 'os'])
                    ->get();

        $title   = $this->getTitle($event->ticket);
        $message = $this->getMessage($event->ticket, $event->relation);

        FirebaseNotification::sendNotification($tokens, $title, $message, $event->ticket);
        
        $this->notificationRepository->create($title, $message, $event->ticket->id, $type = "Ticket", $event->users);
    }

    private function getMessage($ticket, $relation)
    {
        $action = "";

        switch ($relation) {
            case 'users':
                $action = " te asignó ";
                break;
            case 'usersCopy':
                $action = " te copió ";
                break;
            case 'withoutUsers':
                $action = " dice: ";
                break;
        }

        return $ticket->updatedby->firstname . $action . $ticket->name . " en " . $ticket->spot->name;
    }

    private function getTitle($ticket)
    {
        return $ticket->id . ' - ' . $ticket->name;
    }
}
