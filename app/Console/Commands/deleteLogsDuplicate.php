<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class deleteLogsDuplicate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:delete_logs_duplicate';

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
        $sql = 'SELECT uuid, COUNT(uuid) - 1 as count FROM wh_log
                    where created_at between "2021-06-03 00:00:00" AND "2021-06-07 23:59:59"
                    GROUP BY uuid
                    HAVING COUNT(uuid) > 1;';

        $logsDuplicated = DB::select(DB::raw($sql));

        foreach ($logsDuplicated as $log) {

            $sql = 'DELETE FROM wh_log
                        WHERE uuid = "'.$log->uuid.'"
                        ORDER BY id DESC
                        LIMIT '.$log->count.';';
            
            DB::statement($sql);
        }

        $sql = 'SELECT  COUNT(created_at) - 1 as count, created_at, idticket, iduser 
                FROM wh_log
                where created_at between "2021-06-03 00:00:00" AND "2021-06-07 23:59:59"
                GROUP BY created_at,`data`,`iduser`,idticket
                HAVING 	COUNT(created_at) > 1 
                        AND COUNT(`data`) > 1
                        AND COUNT(iduser) > 1
                        AND COUNT(idticket) > 1;';

        $logsDuplicated = DB::select(DB::raw($sql));

        foreach ($logsDuplicated as $log) {

            $sql = 'DELETE FROM wh_log
                        WHERE idticket = "'.$log->idticket.'" 
                        AND iduser = "'.$log->iduser.'" 
                        AND created_at = "'.$log->created_at.'"
                        ORDER BY id DESC
                        LIMIT '.$log->count.';';
            
            DB::statement($sql);
        }

        return 0;
    }
}
