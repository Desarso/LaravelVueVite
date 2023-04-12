<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWhProductionDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wh_production_detail', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('idproduction')->unsigned();
            $table->time('time');
            $table->integer('quantity')->default(0);
            $table->integer('idoperator')->unsigned();
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
        Schema::dropIfExists('wh_production_detail');
    }
}
