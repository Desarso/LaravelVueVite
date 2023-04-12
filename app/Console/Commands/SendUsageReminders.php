<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Events\SendUsageReminder;

class SendUsageReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:send_usage_reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to send a push notification to people who dont use the app';

    /**
     * Create a new command instance.
     *
     * @return void
     */

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        event(new SendUsageReminder(null));
    }
}
