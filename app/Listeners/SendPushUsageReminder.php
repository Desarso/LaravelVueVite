<?php

namespace App\Listeners;

use App\Events\SendUsageReminder;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log as LavavelLog;
use App\Repositories\AppReminderRepository;
use FirebaseNotification;

class SendPushUsageReminder
{
    protected $appReminderRepository;
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        $this->appReminderRepository = new AppReminderRepository;
    }

    /**
     * Handle the event.
     *
     * @param  SendUsageReminder  $event
     * @return void
     */

    public function handle(SendUsageReminder $event)
    {
        $data    = (object)['id' => 1];
        $reminders  = $this->appReminderRepository->getReminderToSend();
        
        foreach ($reminders as $reminder) {
            $tokens  = $reminder['tokens'];
            $message  = $reminder['message'];
            $title  = "Recordatorio Whagons:";

            FirebaseNotification::sendNotification($tokens, $title, $message, $data, "reminder");
            $this->appReminderRepository->updateLastSendReminder($reminder['id']);
        }
    }
}
