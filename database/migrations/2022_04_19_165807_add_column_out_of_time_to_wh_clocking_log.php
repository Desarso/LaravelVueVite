<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnOutOfTimeToWhClockingLog extends Migration
{
    public function up()
    {
        Schema::table('wh_clockin_log', function (Blueprint $table) {
            $table->boolean('out_of_time')->default(0)->after('fake_location');
        });
    }

    public function down()
    {
        Schema::table('wh_clockin_log', function (Blueprint $table) {
            $table->dropColumn('out_of_time');
        });
    }
}
