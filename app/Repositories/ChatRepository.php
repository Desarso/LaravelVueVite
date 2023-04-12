<?php

namespace App\Repositories;

use App\Models\Item;
use Illuminate\Support\Facades\DB;
use App\Helpers\Helper;
use FirebaseNotification;


class ChatRepository
{
    private $config;

    private function getTokens($created_by, $users)
    {
        $tokens = DB::table('wh_user_device')
                    ->where('iduser', '!=', $created_by)
                    ->whereIn('iduser', $users)
                    ->select(['token', 'os'])
                    ->get();

        return $tokens;
    }

    public function sendNotification($request)
    {
        $tokens = $this->getTokens($request->created_by, $request->users);
        $chat_room = new \stdClass;
        $chat_room->id = $request->chat_room;
        // dd($chat_room);

        FirebaseNotification::sendNotification($tokens, $request->title, $request->message, $chat_room, 'chat');
        // $this->notificationRepository->create($title, $message, $event->ticket->id, $type = "Ticket", $users);

        return response()->json([
            "result" => "ok"
        ]);
    }

}