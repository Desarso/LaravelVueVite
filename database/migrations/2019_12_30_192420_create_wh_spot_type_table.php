<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWhSpotTypeTable extends Migration
{
    public function up()
    {
        Schema::create('wh_spot_type', function (Blueprint $table) {
            $table->increments('id');
            $table->string('idexternal', 50)->nullable();   // Impala, etc
            $table->string('name');
            $table->string('description')->nullable();     
            $table->boolean('islodging')->default(0);     
            $table->string('code', 50)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    
    public function down()
    {
        Schema::dropIfExists('wh_spot_type');
    }
}
