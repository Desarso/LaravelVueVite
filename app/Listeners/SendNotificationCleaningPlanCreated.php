<?php

namespace App\Listeners;

use App\Events\CleaningPlanCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;
use App\Repositories\NotificationRepository;
use App\Repositories\Cleaning\CleaningPlanRepository;
use FirebaseNotification;

class SendNotificationCleaningPlanCreated 
{
    protected $notificationRepository;
    protected $cleaningPlanRepository;

    public function __construct()
    {
        $this->notificationRepository = new NotificationRepository;
        $this->cleaningPlanRepository = new CleaningPlanRepository;
    }

    public function handle(CleaningPlanCreated $event)
    {
        $iduser = $event->cleaningPlan->iduser;

        $tokens = $this->getTokens($iduser);

        $title   = $this->getTitle($event->cleaningPlan);
        $message = $this->getMessage($event->cleaningPlan);

        FirebaseNotification::sendNotification($tokens, $title, $message, $event->cleaningPlan->spot, 'room');

        //$this->notificationRepository->create($title, $message, $event->cleaningPlan->id, $type = "Cleaning", (array) $iduser);
    }

    private function getTokens($iduser)
    {
        $users = [];

        if($iduser == 0)
        {
            $settings = $this->cleaningPlanRepository->getCleaningSettings();

            $users = DB::table('wh_user_team')
                       ->select('iduser')
                       ->whereIn('idteam', $settings->cleaning_teams)
                       ->pluck('iduser')
                       ->toArray();
        }
        else
        {
            $users = (array)$iduser;
        }

        $tokens = DB::table('wh_user_device')
                    ->join('wh_user', 'wh_user.id', '=', 'wh_user_device.iduser')
                    ->where('available', '=', 1)
                    ->whereIn('iduser', $users)
                    ->where('os', '!=', 'WEB')
                    ->select(['token', 'os'])
                    ->get();

        return $tokens;
    }

    private function getMessage($cleaningPlan)
    {
        return $cleaningPlan->item->name . " en " . $cleaningPlan->spot->name;
    }

    private function getTitle($cleaningPlan)
    {
        return "Se te asignó una limpieza";
    }
}
