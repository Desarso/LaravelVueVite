<?php

namespace App\Helpers;
use PushNotification;
use Illuminate\Support\Facades\DB;

class FirebaseNotification
{
    public static function sendNotification($collection, $title, $body, $data, $type = 'task')
    {
        $tokens = $collection->pluck('token')->toArray();
        static::pushNotification($tokens, $title, $body, $data, $type);
    }

    public static function pushNotification($tokens, $title, $body, $data, $type)
    {   
        PushNotification::setService('fcm')
                        ->setMessage([
                                'notification' => [
                                    'title' => $title,
                                    'body'  => $body,
                                    'sound' => 'ding.mp3',
                                    'android_channel_id' => 'noti_push_app_1',
                                    'click_action' => DB::table('wh_organization')->first()->subdomain,
                                    'icon' => 'https://dingdonecdn.nyc3.digitaloceanspaces.com/general/logos/logo.png'
                                ],
                                'data'     => [
                                    'type' => $type, 
                                    'data' => $data->id, 
                                    'code' => ($type == "Ticket" ? $data->code : null), 
                                    'click_action' => "FLUTTER_NOTIFICATION_CLICK",
                                ],
                                'content_available' => true,
                                'priority' => 'high'
                        ])
                        ->setDevicesToken($tokens)
                        ->send()
                        ->getFeedback();
    }
}