<?php

namespace App\Console\Commands;

use App\Repositories\ResetTablesRepository;
use Illuminate\Console\Command;

class ResetTable extends Command
{
    protected $resetTablesRepository;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wh:reset_tables';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'limpia registro de tablas cada dias';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->resetTablesRepository = new ResetTablesRepository;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->resetTablesRepository->resetWhUserNotification();
        $this->resetTablesRepository->resetLogSync();
        return 0;
    }
}
