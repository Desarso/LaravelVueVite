<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWhOvertimeTable extends Migration
{
    public function up()
    {
        Schema::create('wh_overtime', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->enum('type', ['BY_SCHEDULE', 'CUMULATIVE'])->default('BY_SCHEDULE');
            $table->integer('weekly_overtime_after')->nullable();
            $table->integer('daily_overtime_after')->nullable();
            $table->integer('daily_doubletime_after')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('wh_overtime');
    }
}
