<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToWhAttendanceResumeOvertime extends Migration
{
    public function up()
    {
        Schema::table('wh_attendance_resume_overtime', function (Blueprint $table) {
            $table->time('normal_time_approved')->after('double_time')->nullable();
            $table->time('half_time_approved')->after('double_time')->nullable();
            $table->time('double_time_approved')->after('double_time')->nullable();
        });
    }

    public function down()
    {
        Schema::table('wh_attendance_resume_overtime', function (Blueprint $table) {
            $table->dropColumn('normal_time_approved');
            $table->dropColumn('half_time_approved');
            $table->dropColumn('double_time_approved');
        });
    }
}
