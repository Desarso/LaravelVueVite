<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnExtraToWhAttendanceLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('wh_attendance_log', function (Blueprint $table) {
            $table->float('day_hours')->default(0)->after('duration');
            $table->float('mixed_hours')->default(0)->after('duration');
            $table->float('night_hours')->default(0)->after('duration');
            $table->boolean('isdouble')->default(0)->after('duration');
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
            $table->dropColumn('day_hours');
            $table->dropColumn('mixed_hours');
            $table->dropColumn('night_hours');
            $table->dropColumn('double_hours');
        });
    }
}
