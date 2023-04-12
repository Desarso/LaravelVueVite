<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWhClockinLogTable extends Migration
{
    public function up()
    {
        Schema::create('wh_clockin_log', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->enum('action', ['CLOCK-IN','CLOCK-OUT'])->default('CLOCK-IN');
            $table->integer('iduser')->unsigned();
            $table->integer('idactivity')->unsigned()->nullable();
            $table->json('start_location')->nullable();
            $table->json('end_location')->nullable();
            $table->datetime('clockin');
            $table->datetime('clockout')->nullable();
            $table->integer('duration')->default(0);
            $table->boolean('fake_location')->default(0);
            $table->timestamps();

            $table->foreign("iduser")->references("id")->on("wh_user");
            $table->foreign("idactivity")->references("id")->on("wh_clockin_activity");
        });
    }

    public function down()
    {
        Schema::dropIfExists('wh_clockin_log');
    }
}
