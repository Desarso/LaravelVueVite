<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnIdtypeToWhSchedule extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('wh_schedule', function (Blueprint $table) {
            $table->integer('idtype')->after('name')->unsigned();
        });

        Schema::table('wh_schedule', function (Blueprint $table) {
            $table->foreign("idtype")->references("id")->on("wh_schedule_type");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('wh_schedule', function (Blueprint $table) {
            $table->dropColumn('idtype');
        });
    }
}
