<?php

namespace App\Repositories;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ResetTablesRepository
{
    public function resetWhUserNotification()
    {
        
        $notifications = DB::table('wh_notification')->whereDate('created_at', '<=', Carbon::now()->subDays(30))->get();
        $idToDelete = $notifications->pluck('id')->toArray();

        // dd($idToDelete[0]);

        DB::table('wh_notification_user')
            ->whereIn('idnotification', $idToDelete)
            ->delete();

        DB::table('wh_notification')
            ->whereIn('id', $idToDelete)
            ->delete();


        return 0;
    }

    public function resetLogSync()
    {
        DB::table('log_sync')
            ->whereDate('created_at', '<=', Carbon::now()->subDays(30))
            ->delete();

        return 0;
    }
}