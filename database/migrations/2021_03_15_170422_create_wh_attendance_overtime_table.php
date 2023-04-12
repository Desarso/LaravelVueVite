<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWhAttendanceOvertimeTable extends Migration
{
    public function up()
    {
        Schema::create('wh_attendance_overtime', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('idticket')->unsigned();
            $table->bigInteger('idlog')->unsigned();
            $table->integer('iduser')->unsigned();
            $table->time('start');
            $table->time('end');
            $table->integer('time')->default(0);
            $table->float('rate', 8, 1);
            $table->timestamps();

            $table->foreign("idticket")->references("id")->on("wh_ticket");
            $table->foreign("iduser")->references("id")->on("wh_user");
            $table->foreign("idlog")->references("id")->on("wh_log");
        });
    }

    public function down()
    {
        Schema::dropIfExists('wh_attendance_overtime');
    }
}
