<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWhCleaningScheduleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wh_cleaning_schedule', function (Blueprint $table) {
            $table->increments('id');      
            $table->integer('iduser')->unsigned()->nullable();
            $table->integer('idspot')->unsigned(); 
            $table->integer('iditem')->unsigned(); 
            $table->json('dow'); 
            $table->datetime('time')->nullable();
            $table->integer('sequence')->nullable();
            $table->boolean('enabled')->default(1);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign("iduser")->references("id")->on("wh_user");
            $table->foreign("idspot")->references("id")->on("wh_spot");
            $table->foreign("iditem")->references("id")->on("wh_item");
         
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('wh_cleaning_schedule');
    }
}
