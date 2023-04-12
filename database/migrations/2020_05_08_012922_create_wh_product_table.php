<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWhProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wh_product', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('code')->nullable();
            $table->text('description')->nullable();
            $table->string('imageurl')->nullable();
            $table->integer('idequipmenttype')->unsigned()->nullable();
            $table->integer('idproductcategory')->unsigned()->nullable();            
            $table->integer('idformula')->unsigned()->nullable();
          
            $table->integer('iddestination')->unsigned()->nullable();
            $table->integer('idpresentation')->unsigned()->nullable();
            $table->boolean('enabled')->default(1);
            //$table->integer('idproductpack')->unsigned()->nullable(); PENDIENTE
            
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
        Schema::dropIfExists('wh_product');
    }
}
