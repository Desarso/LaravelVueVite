<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnFreedayToWhAttendanceLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('wh_attendance_log', function (Blueprint $table) {
            $table->boolean('free_day')->default(0)->after('duration');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('wh_attendance_log', function (Blueprint $table) {
            $table->dropColumn('free_day');
        });
    }
}
