<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWhBookingSetTable extends Migration
{
    
    public function up()
    {
        Schema::create('wh_booking_set', function (Blueprint $table) {
            $table->increments('id');
            $table->string('idimpala', 50);
            $table->date('startdate');
            $table->date('enddate');
            $table->json('contact');
            $table->json('bookingIds');
            $table->timestamps();
        });
    }

   
    public function down()
    {
        Schema::dropIfExists('wh_booking_set');
    }
}
