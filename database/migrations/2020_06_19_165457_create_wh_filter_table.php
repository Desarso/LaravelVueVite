<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWhFilterTable extends Migration
{
    public function up()
    {
        Schema::create('wh_filter', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100);
            $table->json('data');
            $table->integer('iduser')->unsigned()->nullable();
            $table->timestamps();

            $table->foreign('iduser')->references('id')->on('wh_user'); 
        });
    }

    public function down()
    {
        Schema::dropIfExists('wh_filter');
    }
}
