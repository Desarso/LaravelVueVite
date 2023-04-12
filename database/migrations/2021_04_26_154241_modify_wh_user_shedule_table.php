<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyWhUserSheduleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('wh_user_schedule', function (Blueprint $table) {
            $table->date('date')->after('idschedule');
        });

        Schema::table('wh_user_schedule', function (Blueprint $table) {
            $table->dropColumn('day');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('wh_user_schedule', function (Blueprint $table) {
            $table->dropColumn('date');
        });
    }
}
