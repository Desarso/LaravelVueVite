<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWhWarehouseNoteTable extends Migration
{
    public function up()
    {
        Schema::create('wh_warehouse_note', function (Blueprint $table) {
            $table->id('id');
            $table->bigInteger('idwarehouse')->unsigned();
            $table->string('note');
            $table->integer('created_by')->unsigned();
            $table->timestamps();

            $table->foreign("idwarehouse")->references("id")->on("wh_warehouse");
            $table->foreign("created_by")->references("id")->on("wh_user");
        });
    }

    public function down()
    {
        Schema::dropIfExists('wh_warehouse_note');
    }
}
