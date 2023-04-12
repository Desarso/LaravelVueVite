<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropSchedulesAttendanceTables extends Migration
{
    public function up()
    {
        Schema::dropIfExists('wh_user_attendance');
        Schema::dropIfExists('wh_attendance_resume_overtime');
        Schema::dropIfExists('wh_attendance_overtime');
        Schema::dropIfExists('wh_attendance_log');

        Schema::table('wh_user', function (Blueprint $table) {
            $table->dropForeign('wh_user_idschedule_foreign');
        });

        Schema::dropIfExists('wh_schedule_slot');
        Schema::dropIfExists('wh_user_schedule');

        Schema::dropIfExists('wh_schedule');
        Schema::dropIfExists('wh_schedule_type');


    }

    public function down()
    {
        //
    }
}
