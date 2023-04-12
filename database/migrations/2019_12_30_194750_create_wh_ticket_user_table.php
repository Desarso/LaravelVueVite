<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWhTicketUserTable extends Migration
{
    public function up()
    {
        Schema::create('wh_ticket_user', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('idticket')->unsigned();
            $table->integer('iduser')->unsigned();
            $table->boolean('copy')->default(0);
            $table->timestamps();

            $table->foreign("iduser")->references("id")->on("wh_user");
            $table->foreign("idticket")->references("id")->on("wh_ticket");
        });
    }

    public function down()
    {
        Schema::dropIfExists('wh_ticket_user');
    }
}
