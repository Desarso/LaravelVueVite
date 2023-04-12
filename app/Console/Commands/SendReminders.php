<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Reminder;
use App\Models\Ticket;
use Carbon\Carbon;
use App\Enums\ReminderType;
use App\Enums\TicketStatus;
use App\Events\SendReminder;

class SendReminders extends Command
{
    protected $signature = 'wh:send_reminders';

    protected $description = 'Este comando envía los recordatorios';

    protected $timezone;

    public function __construct()
    {
        parent::__construct();
        $this->timezone = 'America/Costa_Rica';
    }

    public function handle()
    {
        $start = Carbon::today($this->timezone)->startOfDay()->setTimezone(config('app.timezone'));
        $end   = Carbon::today($this->timezone)->endOfDay()->setTimezone(config('app.timezone'));

        $tickets = Ticket::select(['id', 'created_by', 'duedate', 'created_at'])
                         ->where('idstatus', '!=', 4)
                         ->whereBetween('created_at', [$start, $end])
                         ->where('duedate', '<=', Carbon::now())
                         ->doesntHave('reminders')
                         ->get();

        $collection = collect();

        foreach($tickets as $ticket)
        {
            $users = $ticket->users->pluck('id')->toArray();

            if(count($users) == 0) $users = (array) $ticket->created_by;

            foreach ($users as $user)
            {
                $result = $collection->firstWhere('iduser', $user);

                if(is_null($result))
                {
                    $item = (object) ["iduser" => $user, "tickets" => 1];
                    $collection->push($item);
                }
                else
                {
                    $result->tickets += 1;
                }
            }

            Reminder::create(['idticket' => $ticket->id, 'sent' => 1, 'notify_at' => Carbon::now()]);
        }

        $this->sendReminder($collection);
    }

    private function sendReminder($collection)
    {
        foreach ($collection as $item)
        {
            event(new SendReminder($item));
        }
    }
}
