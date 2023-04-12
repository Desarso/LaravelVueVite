<?php

namespace App\Repositories;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\AppReminder;
use App\Helpers\Helper;


class AppReminderRepository
{
    protected $configRepository;

    public function __construct()
    {
        $this->configRepository = new ConfigRepository;
    }

    public function getAll()
    {
        return AppReminder::get();
    }

    public function create($request)
    {
        $request->merge(['teams' => json_encode(array_map('intval', (array)$request->teams))]);

        $request->merge(['dow' => json_encode((array)$request->dow)]);

        $request->merge(['users_exception' => json_encode(array_map('intval', (array)$request->users_exception))]);

        $appReminder = AppReminder::create($request->all());

        return response()->json(['success' => true, 'model' => $appReminder]);
    }

    public function update($request)
    {
        $request->merge(['teams' => json_encode(array_map('intval', (array)$request->teams))]);

        $request->merge(['dow' => json_encode((array)$request->dow)]);

        $request->merge(['users_exception' => json_encode(array_map('intval', (array)$request->users_exception))]);

        $appReminder = AppReminder::find($request->id);

        $appReminder->fill($request->all())->save();  
        
        return response()->json(['success' => true, 'model' => $appReminder]);
    }

    public function delete($request)
    {
        $appReminder = AppReminder::findOrFail($request->id);

        $appReminder->delete();

        return response()->json(['success' => true, 'model' => $appReminder]);
    }

    public function change($request)
    {
        $appReminder = AppReminder::find($request->id);

        $appReminder->fill($request->all())->save();  
        
        return response()->json(['success' => true, 'model' => $appReminder]);
    }

    public function getReminderToSend()
    {
        $notifications = array();
        $reminders = AppReminder::select('id', 'message', 'time', 'teams', 'users_exception')
                                ->whereTime('time', '<=', Carbon::now())
                                ->whereJsonContains('dow', $this->getDOW())
                                ->where(function ($query){
                                    $query->whereNull('last_send')
                                          ->orWhereDate('last_send', "!=", Carbon::today());
                                })
                                ->where('enabled', 1)
                                ->get();

        foreach ($reminders as $reminder) {

            $teams = json_decode($reminder->teams);
            $users_exception = json_decode($reminder->users_exception);


            $tokens = DB::table('wh_user_team')
                    ->join('wh_user_device', 'wh_user_device.iduser', '=', 'wh_user_team.iduser')
                    ->join('wh_user', 'wh_user.id', '=', 'wh_user_team.iduser')
                    ->where('wh_user.available', '=', 1)
                    ->whereIn('wh_user_team.idteam', $teams)
                    ->when(!is_null($users_exception), function ($query) use ($users_exception) {
                        $query->whereNotIn('wh_user.id', $users_exception);
                     })
                    ->select(['wh_user_device.iduser', 'wh_user_device.token' , 'wh_user_device.os'])
                    ->distinct()
                    ->get();

             array_push($notifications, [
                'id' => $reminder->id,
                'message' => $reminder->message,
                'tokens' => $tokens
             ]);
        }

       return $notifications;
    }

    public function getDOW()
    {
        $weekMap = [
            0 => 'SU',
            1 => 'MO',
            2 => 'TU',
            3 => 'WE',
            4 => 'TH',
            5 => 'FR',
            6 => 'SA',
        ];
        $dayOfTheWeek = Carbon::now()->dayOfWeek;
        return $weekMap[$dayOfTheWeek];
    }

    public function updateLastSendReminder($id)
    {
        AppReminder::where('id', $id)
                    ->update(['last_send' => Carbon::now()]);
    }
}