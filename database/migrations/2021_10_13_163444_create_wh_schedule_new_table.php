<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWhScheduleNewTable extends Migration
{
    public function up()
    {
        Schema::create('wh_schedule', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('idtype')->unsigned();
            $table->string('name');
            $table->timestamps();

            $table->foreign("idtype")->references("id")->on("wh_schedule_type");
        });
    }

    public function down()
    {
        Schema::dropIfExists('wh_schedule');
    }
}
