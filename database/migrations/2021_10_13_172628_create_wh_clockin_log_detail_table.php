<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWhClockinLogDetailTable extends Migration
{
    public function up()
    {
        Schema::create('wh_clockin_log_detail', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('iduser')->unsigned();
            $table->bigInteger('idclockin')->unsigned();
            $table->date('date');
            $table->time('start');
            $table->time('end');
            $table->float('rate', 8, 1)->default(1);
            $table->integer('time');
            $table->timestamps();

            $table->foreign("iduser")->references("id")->on("wh_user");
            $table->foreign("idclockin")->references("id")->on("wh_clockin_log");
        });
    }

    public function down()
    {
        Schema::dropIfExists('wh_clockin_log_detail');
    }
}
