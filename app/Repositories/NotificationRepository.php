<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class NotificationRepository
{
    public function getNotifications($request)
    {
        return User::select('id')
                   ->where('id', Auth::id())
                   ->with('notifications')
                   ->with(["notifications" => function ($query) {
                        $query->where('read', 0)
                              ->orWhereDate('wh_notification.created_at', Carbon::today());
                    }])
                   ->withCount(["notifications" => function ($query) {
                        $query->where('read', 0);
                    }])
                   ->first();
    }

    public function getListNotificationAPP($request)
    {
        return Notification::select(['id', 'title', 'message', 'idreference', 'type', 'created_at'])
                            ->whereDate('created_at', DB::raw('CURDATE()'))
                            ->whereHas('users', function ($query) use ($request) {
                                $query->where("iduser", $request->iduser);
                            })
                            ->orderBy('created_at', 'desc')
                            ->get();
    }

    public function getNotificationNotRead($request)
    {
        return  Notification::whereDate('created_at', DB::raw('CURDATE()'))
                            ->whereHas('users', function ($query) use ($request) {
                                $query->where("iduser", $request->iduser)
                                      ->where('read', 0);
                            })
                            ->count();
    }

    public function setNotificationsRead($request)
    {
        DB::table('wh_notification_user')
            ->where('iduser', "=", $request->iduser)
            ->where('read', "=", 0)
            ->update(['read' => 1]);

        return response()->json(['success' => true]); 
    }   
    
    public function create($title, $message, $ref, $type = "Ticket", $users)
    {
        $notification = Notification::create(["title" => $title, "message" => $message, "idreference" => $ref, "type" => $type]);
        $notification->users()->attach($users);
    }

    public function readNotifications($request)
    {
        $result = DB::table('wh_notification_user')
                    ->where('iduser', Auth::id())
                    ->where('read', 0)
                    ->update(['read' => 1]);
    }
}