<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnEmailSentToTableAttendanceLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('wh_attendance_log', function (Blueprint $table) {
            $table->integer('email_sent')->default(0)->after('duration');
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
            $table->dropColumn('email_sent');
        });
    }
}
