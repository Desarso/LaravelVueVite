<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnIdworkplanToWhPlannerTable extends Migration
{
    public function up()
    {
        Schema::table('wh_planner', function (Blueprint $table) {
            $table->integer('idworkplan')->after("idspot")->unsigned()->nullable();
        });

        Schema::table('wh_planner', function (Blueprint $table) {
            $table->foreign("idworkplan")->references("id")->on("wh_work_plan");
        });
    }

    public function down()
    {
        Schema::table('wh_planner', function (Blueprint $table) {
            //
        });
    }
}
