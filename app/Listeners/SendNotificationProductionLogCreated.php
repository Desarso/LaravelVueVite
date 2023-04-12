<?php

namespace App\Listeners;

use App\Events\ProductionLogCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;
use FirebaseNotification;

class SendNotificationProductionLogCreated
{
    public function __construct()
    {
        //
    }

    public function handle(ProductionLogCreated $event)
    {
        $idteam = $event->productionLog->idteam;

        $tokens = DB::table('wh_user_team as ut')
                    ->join('wh_user_device as ud', 'ud.iduser', '=', 'ut.iduser')
                    ->join('wh_role as r', 'r.id', '=', 'ut.idrole')
                    ->where('ut.idteam', $idteam)
                    ->where('r.notify', true)
                    ->select(['ut.iduser', 'ud.token', 'ud.os'])
                    ->get();
                    
        FirebaseNotification::sendNotification($tokens, $this->getTitle($event), $this->getMessage($event), $event->productionLog);
    }

    private function getMessage($event)
    {
        return $event->productionLog->name;
    }

    private function getTitle($event)
    {
        return "Nueva parada";
    }
}
