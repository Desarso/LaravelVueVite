<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWhBookingTable extends Migration
{
    public function up()
    {
        Schema::create('wh_booking', function (Blueprint $table) {
            $table->increments('id');
            $table->string('idimpala', 50);
            $table->string('status');
            $table->integer('idbookingset')->unsigned();
            $table->integer('idspot')->unsigned();
            $table->integer('idtype')->unsigned(); 
            $table->date('startdate');
            $table->date('enddate');
            $table->integer('adultcount')->nullable();
            $table->integer('childcount')->nullable();
            $table->integer('infantcount')->nullable();
 
            $table->timestamps();
 
            $table->foreign("idbookingset")->references("id")->on("wh_booking_set");
            $table->foreign('idspot')->references('id')->on('wh_spot');
            $table->foreign("idtype")->references("id")->on("wh_spot_type");
            
         });
    }

    
    public function down()
    {
        Schema::dropIfExists('wh_booking');
    }
}
