<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWhEquipmentStatusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wh_equipment_status', function (Blueprint $table) {
            $table->increments('id');
            //No se estaba usando, es mejor por el momento no incluirlo
            //$table->integer('idtype')->nullable();  // Equipment Type
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('icon')->nullable();
            $table->string('color')->nullable();
            $table->timestamps();
            
            // Foreigns
           // $table->foreign("idtype")->references("id")->on("wh_equipment_type");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('wh_equipment_status');
    }
}
