<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWhClockinActivityTable extends Migration
{
    public function up()
    {
        Schema::create('wh_clockin_activity', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('description')->nullable();
            $table->boolean('timesensitive')->default(0);
            $table->boolean('gps')->default(0);
            $table->string('color')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('wh_clockin_activity');
    }
}
