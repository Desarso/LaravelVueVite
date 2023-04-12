<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ResetCleaningStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:reset_cleaning_status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'command to change cleaning status';

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
        DB::table('wh_spot')
            ->where('idcleaningstatus', "!=", 1)
            ->update(['idcleaningstatus' => 1, 'idcleaningplan' => null]);
    }
}
