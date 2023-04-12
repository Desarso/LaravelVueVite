<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWhProductionInput extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wh_production_input', function (Blueprint $table) {
            $table->increments('id');                        
            /***** IMPORTANTE ****/
            // Si no se especifica la categoría, aplica a cualquier categoría
            $table->integer('idproductcategory')->unsigned()->nullable();
            $table->string('name');            
            $table->text('description')->nullable();
            $table->double('formula',15, 8)->default(1);
            $table->string('measure')->default('unidad');
            $table->double('pack_size',15,8);
            $table->integer('buffer')->nullable();
            $table->integer('pack_placing_duration')->unsigned();
            $table->integer('idstop')->unsigned()->nullable();
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
        Schema::dropIfExists('wh_production_input');
    }
}
