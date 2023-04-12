<?php

namespace App\Listeners;

use App\Events\NoteCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;
use App\Repositories\NotificationRepository;
use FirebaseNotification;

class SendNotificationNoteCreated
{
    protected $notificationRepository;

    public function __construct()
    {
        $this->notificationRepository = new NotificationRepository;
    }

    public function handle(NoteCreated $event)
    {
        $ticket = $event->note->ticket;
        if(is_null($ticket)) return;
        $users = $ticket->users()->pluck('iduser')->toArray();

        $tokens = DB::table('wh_user_device')
                    ->join('wh_user', 'wh_user.id', '=', 'wh_user_device.iduser')
                    ->where('available', '=', 1)
                    ->where('iduser', '!=', $event->note->created_by)
                    ->whereIn('iduser', $users)
                    ->select(['token', 'os'])
                    ->get();

        $title   = $this->getTitle($ticket);
        $message = $this->getMessage($event->note);

        FirebaseNotification::sendNotification($tokens, $title, $message, $ticket);

        $this->notificationRepository->create($title, $message, $ticket->id, $type = "Ticket", $users);
    }

    private function getMessage($note)
    {
        return "Nota: " . $note->note;
    }

    private function getTitle($ticket)
    {
        return $ticket->id . ' - ' . $ticket->name;
    }
}
