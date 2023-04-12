<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWhEquipmentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wh_equipment', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('idtype')->unsigned()->nullable();
            $table->integer('idproductcategory')->unsigned()->nullable();
            // En un minuto se hacen 120 sobres...
            $table->integer('velocity')->unsigned()->default(120);
            $table->integer('idstatus')->unsigned()->nullable();
            $table->integer('idproduction')->unsigned()->nullable();  // current Production    
            // Tiempo que ocupa la máquina para calentar
            $table->integer('warmup_duration')->unsigned()->default(0);
            // Tiempo que ocupa la máquina para enfriar
            $table->integer('cleaning_duration')->unsigned()->default(0);

            $table->boolean('enabled')->default(1);
            
            $table->softDeletes();
            $table->timestamps();

            //$table->foreign('idtype')->references('id')->on('wh_product_type'); 
            //$table->foreign('idstatus')->references('id')->on('wh_product_status'); 
            $table->foreign('idproductcategory')->references('id')->on('wh_product_category'); 

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('wh_equipment');
    }
}
