<?php

namespace App\Console\Commands;
use Illuminate\Support\Facades\DB;
use Illuminate\Console\Command;
use App\Repositories\Cleaning\CleaningPlanRepository;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class GenerateCleaningPlans extends Command
{
    protected $signature = 'command:generate_cleaning_plans';

    protected $description = 'Command description';

    protected $cleaningPlanRepository;

    public function __construct()
    {
        parent::__construct();
        $this->cleaningPlanRepository = new CleaningPlanRepository;
    }

    public function handle()
    {
        $idsuperadmin = DB::table('wh_user')->where('issuperadmin', true)->first()->id;

        Auth::loginUsingId($idsuperadmin);

        $this->cleaningPlanRepository->createCleaningPlan();

        Log::info("command:generate_cleaning_plans");
    }
}
