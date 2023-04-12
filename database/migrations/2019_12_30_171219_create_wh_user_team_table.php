<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWhUserTeamTable extends Migration
{
    public function up()
    {
        Schema::create('wh_user_team', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('iduser')->unsigned();
            $table->integer('idteam')->unsigned();           
            $table->integer('idrole')->unsigned();
            $table->timestamps();

            $table->foreign("idteam")->references("id")->on("wh_team");
            $table->foreign("iduser")->references("id")->on("wh_user");
            $table->foreign("idrole")->references("id")->on("wh_role");
        });
    }

    public function down()
    {
        Schema::dropIfExists('wh_user_team');
    }
}
