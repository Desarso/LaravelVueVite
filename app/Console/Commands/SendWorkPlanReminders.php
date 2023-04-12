<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Repositories\WorkPlanRepository;
use App\Events\SendWorkPlanReminder;

class SendWorkPlanReminders extends Command
{
    protected $workPlanRepository;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wh:send_workplan_reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */

    public function __construct()
    {
        parent::__construct();
        $this->workPlanRepository = new WorkPlanRepository;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $collection = $this->workPlanRepository->getPlannerToNotify();

        foreach ($collection as $item)
        {
            event(new SendWorkPlanReminder($item));
        }

        return 0;
    }
}
