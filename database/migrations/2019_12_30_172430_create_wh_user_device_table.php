<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWhUserDeviceTable extends Migration
{
    public function up()
    {
        Schema::create('wh_user_device', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('iduser')->unsigned();
            $table->string('token');
            $table->enum('os', ['ANDROID', 'IOS', 'WEB']);
            $table->timestamps();

            $table->foreign("iduser")->references("id")->on("wh_user");
        });
    }

    public function down()
    {
        Schema::dropIfExists('wh_user_device');
    }
}
