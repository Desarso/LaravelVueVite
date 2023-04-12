<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class ModifyFrecuencyColumnWhPlanner extends Migration
{
    public function up()
    {
        DB::statement("ALTER TABLE wh_planner MODIFY frequency ENUM('DAILY','WEEKLY','MONTHLY','NEVER');");
    }

    public function down()
    {
        DB::statement("ALTER TABLE wh_planner MODIFY frequency ENUM('DAILY','WEEKLY','MONTHLY');");
    }
}
