<?php

namespace App\Listeners;

use App\Events\CleaningStatusChange;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;
use App\Repositories\Cleaning\CleaningPlanRepository;
use FirebaseNotification;

class SendNotificationCleaningStatusChange
{
    protected $cleaningPlanRepository;

    public function __construct()
    {
        $this->cleaningPlanRepository = new CleaningPlanRepository;
    }

    public function handle(CleaningStatusChange $event)
    {
        $cleaningStatus = $event->cleaningStatus;

        $spot = $event->spot;

        $tokens = $this->getTokens();

        $title   = $this->getTitle();

        $message = $this->getMessage($spot, $cleaningStatus);

        FirebaseNotification::sendNotification($tokens, $title, $message, $spot, 'room');
    }

    private function getMessage($spot, $cleaningStatus)
    {
        return $spot->name . " cambió a " . $cleaningStatus->name;
    }

    private function getTitle()
    {
        return "Notificación de limpieza";
    }

    private function getTokens()
    {
        $settings = $this->cleaningPlanRepository->getCleaningSettings();

        $users = DB::table('wh_user_team')
                   ->select('iduser')
                   ->whereIn('idteam', $settings->notify_cleaning_to)
                   ->pluck('iduser')
                   ->toArray();

        $tokens = DB::table('wh_user_device')
                    ->join('wh_user', 'wh_user.id', '=', 'wh_user_device.iduser')
                    ->where('available', '=', 1)
                    ->whereIn('iduser', $users)
                    ->select(['token', 'os'])
                    ->get();

        return $tokens;
    }
}
