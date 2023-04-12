<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWhProductionSchedule extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wh_production_schedule', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            // En la descripción se puede comentar el tiempo de inicio y fin
            $table->text('description')->nullable();            
            $table->integer('duration')->unsigned();
            $table->json('dow'); // daysOfWeek            
            $table->json('breaks')->nullable();
            $table->boolean('enabled')->default(1);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('wh_production_schedule');
    }
}
