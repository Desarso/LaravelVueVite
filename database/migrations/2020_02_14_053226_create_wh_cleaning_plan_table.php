<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWhCleaningPlanTable extends Migration
{
    public function up()
    {
        Schema::create('wh_cleaning_plan', function (Blueprint $table) {            
            $table->bigIncrements('id');
            $table->date('date');               
            $table->integer('idspot')->unsigned();                  
            $table->integer('idcleaningstatus')->unsigned()->default(1);
            $table->integer('iditem')->unsigned();
            $table->integer('iduser')->unsigned()->nullable();            
            $table->time('cleanat')->nullable();
            $table->integer('sequence')->default(0);;
            $table->dateTime('startdate')->nullable();
            $table->dateTime('resumedate')->nullable();
            $table->dateTime('finishdate')->nullable();
            $table->integer('duration')->default(0);
            $table->timestamps();

            //Foreign keys
            $table->foreign('iduser')->references('id')->on('wh_user');
            $table->foreign('iditem')->references('id')->on('wh_item');
            $table->foreign('idspot')->references('id')->on('wh_spot');            
            $table->foreign("idcleaningstatus")->references("id")->on("wh_cleaning_status");             
        });
    }

    public function down()
    {
        Schema::dropIfExists('wh_cleaning_plan');
    }
}
