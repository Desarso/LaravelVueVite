<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class RepairUserSpots extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:repair_user_spots';

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
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $deleted = DB::table('wh_spot')->whereNotNull('deleted_at')->pluck('id')->toArray();

        $users = User::get();

        foreach ($users as $user)
        {
            $array1 = json_decode($user->spots);

            $resultado = array_values(array_diff($array1, $deleted));

            $user->spots = json_encode($resultado);
            
            $user->save();
        }

        return 0;
    }
}
