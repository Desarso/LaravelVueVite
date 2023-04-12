<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnIdworkplanEvaluateToWhPlannerTable extends Migration
{
    public function up()
    {
        Schema::table('wh_planner', function (Blueprint $table) {
            $table->integer('idworkplan_evaluate')->after("idasset")->unsigned()->nullable();
        });

        Schema::table('wh_planner', function (Blueprint $table) {
            $table->foreign("idworkplan_evaluate")->references("id")->on("wh_work_plan");
        });
    }

    public function down()
    {
        Schema::table('wh_planner', function (Blueprint $table) {
            //
        });
    }
}
