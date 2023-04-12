<?php

namespace App\Console\Commands;
use Illuminate\Support\Facades\DB;
use Illuminate\Console\Command;
use App\Repositories\PlannerRepository;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class GenerateRecurringTickets extends Command
{
    protected $signature = 'command:generate_recurring_tickets';

    protected $description = 'Comando para generar tareas recurrentes';

    protected $plannerRepository;  

    public function __construct()
    {
        parent::__construct();
        $this->plannerRepository = new PlannerRepository;
    }

    public function handle()
    {
        $idsuperadmin = DB::table('wh_user')->where('issuperadmin', true)->first()->id;

        session(['iduser' => $idsuperadmin]);

        Auth::loginUsingId($idsuperadmin);

        $this->plannerRepository->generateRecurringTickets();

        Log::info("command:generate_recurring_tickets");
    }
}
