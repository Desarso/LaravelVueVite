<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWhTicketTagTable extends Migration
{
    public function up()
    {
        Schema::create('wh_ticket_tag', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('idticket')->unsigned();
            $table->bigInteger('idtag')->unsigned();
            $table->integer('iduser')->unsigned();
            $table->timestamps();

            $table->foreign("idtag")->references("id")->on("wh_tag");
            $table->foreign("idticket")->references("id")->on("wh_ticket");
            $table->foreign("iduser")->references("id")->on("wh_user");
        });
    }

    public function down()
    {
        Schema::dropIfExists('wh_ticket_tag');
    }
}
