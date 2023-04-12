<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWhProductionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wh_production', function (Blueprint $table) {
            $table->id();
                     
            $table->integer('idstatus')->unsigned()->default(1); // Pendiente
            $table->integer('idequipment')->unsigned(); 
            $table->integer('idproduct')->unsigned(); // El equipo determina la categoría de producto
            $table->integer('idpresentation')->unsigned()->nullable();  // se toma del producto
            $table->integer('iddestination')->unsigned()->nullable();  // se toma del producto
            $table->integer('idschedule')->unsigned()->nullable();

            $table->datetime('productiondate')->nullable();     // fecha de la producción
            $table->datetime('productionstarted')->nullable();  // momento en que comienza la producción
            $table->datetime('productionfinished')->nullable(); // momento en que finaliza la producción 

            $table->integer('productiongoal')->unasigned()->nullable()->default(0); // Cantidad que se desea producir

            $table->string('productionorder')->nullable();   // Orden de Producción
            $table->string('lot')->nullable();  
            
          
            $table->integer('initialcount')->unsigned()->nullable()->default(0);
            $table->integer('finalcount')->unsigned()->nullable()->default(0);
            
            $table->integer('totalproduced')->unsigned()->nullable()->default(0); // total producido
            $table->integer('totalpacked')->unsigned()->nullable()->default(0);   // total empacado

            $table->integer('idoperator')->unsigned()->nullable();    
            $table->text('notes')->nullable();            
            
            
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
        Schema::dropIfExists('wh_production');
    }
}
