<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnCreatedByToWhCleaningPlanTable extends Migration
{
    public function up()
    {
        Schema::table('wh_cleaning_plan', function (Blueprint $table) {
            $table->integer('created_by')->after("duration")->unsigned()->nullable();
        });

        Schema::table('wh_cleaning_plan', function (Blueprint $table) {
            $table->foreign("created_by")->references("id")->on("wh_user");
        });
    }

    public function down()
    {
        Schema::table('wh_cleaning_plan', function (Blueprint $table) {
            //
        });
    }
}
