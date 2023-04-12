<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWhScheduleDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wh_schedule_detail', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('idschedule')->unsigned();
            $table->enum('day',['MON', 'TUES', 'WED', 'THURS', 'FRI', 'SAT', 'SUN']);
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->timestamps();

            $table->foreign("idschedule")->references("id")->on("wh_schedule");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('wh_schedule_detail');
    }
}
