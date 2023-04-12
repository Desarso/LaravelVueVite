<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWhSettingUpdateTable extends Migration
{
    public function up()
    {
        Schema::create('wh_setting_update', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->timestamps();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('wh_setting_update');
    }
}
