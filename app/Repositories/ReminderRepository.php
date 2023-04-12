<?php

namespace App\Repositories;
use Illuminate\Support\Facades\DB;
use App\Models\Reminder;
use App\Enums\ReminderType;
use Carbon\Carbon;
use Session;

class ReminderRepository
{
    public function getAll()
    {
        return Reminder::get();        
    }

    public function create($type, $data)
    {
        $notify_at = null;

        switch($type)
        {
            case ReminderType::Duedate:
                
                $notify_at = Carbon::parse($data->duedate, Session::get('local_timezone'))->setTimezone(config('app.timezone'))->subMinutes(15);
                break;
        }

        Reminder::updateOrCreate(['idticket' => $data->id, 'type' => $type, 'sent' => 0], ['notify_at' => $notify_at]);
    }

    public function update($request)
    {
        $model = Reminder::find($request->id);
        $model->fill($request->all())->save();
        return $model;
    }

    public function delete($request)
    {
        $model = Reminder::findOrFail($request->id);
        $model->delete();
    }
}