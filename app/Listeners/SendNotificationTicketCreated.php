<?php

namespace App\Listeners;

use App\Events\TicketCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;
use App\Repositories\NotificationRepository;
use App\Repositories\UserRepository;
use FirebaseNotification;

class SendNotificationTicketCreated
{
    protected $notificationRepository;
    protected $userRepository;

    public function __construct()
    {
        $this->notificationRepository = new NotificationRepository;
        $this->userRepository = new UserRepository;
    }

    public function handle(TicketCreated $event)
    {
        
        if ($event->ticket->users->count() > 0) {
            $users  = $event->ticket->users->pluck('id')->toArray();
        } else {
            $users  = $this->userRepository->getUserToNotify($event->ticket);
        }
        
        $copies = $event->ticket->usersCopy->pluck('id')->toArray();

        $tokens_users  = $this->getTokens($event, $users);
        $tokens_copies = $this->getTokens($event, $copies);

        $title   = $this->getTitle($event->ticket);
        $message = $this->getMessage($event->ticket);

        FirebaseNotification::sendNotification($tokens_users, $title, $message, $event->ticket);
        FirebaseNotification::sendNotification($tokens_copies, $title, $this->getMessageCopy($event->ticket), $event->ticket);
        $this->notificationRepository->create($title, $message, $event->ticket->id, $type = "Ticket", $users);
    }

    private function getTokens($event, $users)
    {
        $tokens = DB::table('wh_user_device')
                    ->join('wh_user', 'wh_user.id', '=', 'wh_user_device.iduser')
                    ->where('available', '=', 1)
                    ->where('iduser', '!=', $event->ticket->created_by)
                    ->whereIn('iduser', $users)
                    ->select(['token', 'os'])
                    ->get();

        return $tokens;
    }

    private function getMessage($ticket)
    {
        return $ticket->createdby->firstname . " dice: " . $ticket->name . " en " . $ticket->spot->name;
    }

    private function getMessageCopy($ticket)
    {
        return $ticket->createdby->firstname . " te copió en " . $ticket->name;
    }

    private function getTitle($ticket)
    {
        return $ticket->code . ' - ' . $ticket->name;
    }
}
