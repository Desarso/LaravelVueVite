<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWhCleaningLogTable extends Migration
{
    public function up()
    {
        Schema::create('wh_cleaning_log', function (Blueprint $table) {
            $table-> bigIncrements('id');
            $table->enum('action', ['CREATE_PLAN', 'EDIT_PLAN', 'DELETE_PLAN', 'EDIT_SPOT']);
            $table->integer('idspot')->unsigned();
            $table->integer('iduser')->unsigned();            
            $table->json('data');
            $table->timestamps();

            //Foreign keys
            $table->foreign('iduser')->references('id')->on('wh_user');
            $table->foreign('idspot')->references('id')->on('wh_spot');            
        });
    }

    public function down()
    {
        Schema::dropIfExists('wh_cleaning_log');
    }
}
