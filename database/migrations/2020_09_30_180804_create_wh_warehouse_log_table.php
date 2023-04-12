<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWhWarehouseLogTable extends Migration
{
    public function up()
    {
        Schema::create('wh_warehouse_log', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->json('data');
            $table->bigInteger('idwarehouse')->unsigned();
            $table->integer('idstatus')->unsigned();
            $table->timestamps();

            $table->foreign("idwarehouse")->references("id")->on("wh_warehouse");
            $table->foreign("idstatus")->references("id")->on("wh_warehouse_status");
        });
    }

    public function down()
    {
        Schema::dropIfExists('wh_warehouse_log');
    }
}
