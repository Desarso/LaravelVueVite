<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class ModifyWhAttendanceLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE wh_attendance_log MODIFY over_time FLOAT;');
        DB::statement('ALTER TABLE wh_attendance_log CHANGE COLUMN `CheckIn` `check_out` DATETIME NULL DEFAULT NULL ;');
        DB::statement('ALTER TABLE wh_attendance_log CHANGE COLUMN `CheckOut` `check_in` DATETIME NULL DEFAULT NULL ;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
