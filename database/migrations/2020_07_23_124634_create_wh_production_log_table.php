<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWhProductionLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wh_production_log', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('type')->unsigned()->default(1); // 1 = stops
            $table->integer('idstop')->unsigned()->nullable();
            $table->integer('idstatus')->unsigned()->default(1);
            $table->integer('created_by')->unsigned();
            $table->integer('iduser')->unsigned()->nullable();
            $table->integer('idteam')->unsigned()->nullable();
            $table->integer('idproduction')->unsigned()->nullable();
            // Maquina
            $table->integer('idequipment')->unsigned()->nullable();
            $table->datetime('started')->nullable();
            $table->datetime('finished')->nullable();
            $table->datetime('resumed')->nullable();
            $table->integer('duration')->unsigned()->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('wh_production_log');
    }
}
