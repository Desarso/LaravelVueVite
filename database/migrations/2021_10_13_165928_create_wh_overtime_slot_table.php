<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWhOvertimeSlotTable extends Migration
{
    public function up()
    {
        Schema::create('wh_overtime_slot', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('idovertime')->unsigned();
            $table->time('start');
            $table->time('end');
            $table->float('rate', 8, 1)->default(1);
            $table->timestamps();

            $table->foreign("idovertime")->references("id")->on("wh_overtime");
        });
    }

    public function down()
    {
        Schema::dropIfExists('wh_overtime_slot');
    }
}
