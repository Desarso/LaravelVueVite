<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnBusinessDaysToWhPlannerTable extends Migration
{
    public function up()
    {
        Schema::table('wh_planner', function (Blueprint $table) {
            $table->integer('business_days')->default(0)->after("until");
        });
    }

    public function down()
    {
        Schema::table('wh_planner', function (Blueprint $table) {
            $table->dropColumn('business_days');
        });
    }
}
