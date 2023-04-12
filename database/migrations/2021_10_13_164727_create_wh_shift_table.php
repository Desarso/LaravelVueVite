<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWhShiftTable extends Migration
{
    public function up()
    {
        Schema::create('wh_shift', function (Blueprint $table) {
            $table->increments('id');
            $table->enum('type', ['DAY', 'NIGHT', 'MIX', 'DAY_OFF'])->default('DAY');
            $table->integer('idschedule')->unsigned();
            $table->integer('idovertime')->unsigned();
            $table->string('name');
            $table->time('start')->nullable();
            $table->time('end')->nullable();
            $table->json('dow')->nullable();
            $table->timestamps();

            $table->foreign("idschedule")->references("id")->on("wh_schedule");
            $table->foreign("idovertime")->references("id")->on("wh_overtime");
        });
    }

    public function down()
    {
        Schema::dropIfExists('wh_shift');
    }
}
