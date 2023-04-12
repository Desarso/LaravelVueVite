<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWhUserScheduleTable extends Migration
{
    public function up()
    {
        Schema::create('wh_user_schedule', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('iduser')->unsigned();
            $table->integer('idschedule')->unsigned();
            $table->enum('day',['MON', 'TUES', 'WED', 'THURS', 'FRI', 'SAT', 'SUN']);
            $table->timestamps();

            $table->foreign("iduser")->references("id")->on("wh_user");
            $table->foreign("idschedule")->references("id")->on("wh_schedule");
        });
    }

    public function down()
    {
        Schema::dropIfExists('wh_user_schedule');
    }
}
