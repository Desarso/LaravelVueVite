<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWhScheduleSlotTable extends Migration
{
    public function up()
    {
        Schema::create('wh_schedule_slot', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('idschedule')->unsigned();
            $table->json('dow');
            $table->time('start');
            $table->time('end');
            $table->float('rate', 8, 1);
            $table->timestamps();

            $table->foreign("idschedule")->references("id")->on("wh_schedule");
        });
    }

    public function down()
    {
        Schema::dropIfExists('wh_schedule_slot');
    }
}
