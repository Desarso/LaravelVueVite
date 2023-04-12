<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWhUserScheduleNewTable extends Migration
{
    public function up()
    {
        Schema::create('wh_user_schedule', function (Blueprint $table) {
            $table->id();
            $table->integer('iduser')->unsigned();
            $table->integer('idshift')->unsigned();
            $table->date('date');
            $table->timestamps();

            $table->foreign("iduser")->references("id")->on("wh_user");
            $table->foreign("idshift")->references("id")->on("wh_shift");
        });
    }

    public function down()
    {
        Schema::dropIfExists('wh_user_schedule');
    }
}
