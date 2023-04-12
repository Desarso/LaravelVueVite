<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWhLogTable extends Migration
{
    public function up()
    {
        Schema::create('wh_log', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->enum('action', ['LOGIN', 'CREATE_TICKET', 'EDIT_TICKET', 'DELETE_TICKET', 'USER', 'CREATE_NOTE', 'DELETE_NOTE', 'TAG', 'COPY', 'APPROVER']);
            $table->json('data');
            $table->bigInteger('idticket')->unsigned()->nullable();
            $table->integer('idstatus')->unsigned()->nullable();
            $table->integer('iduser')->unsigned();
            $table->timestamps();

            $table->foreign("idticket")->references("id")->on("wh_ticket");
            $table->foreign("idstatus")->references("id")->on("wh_ticket_status");
            $table->foreign("iduser")->references("id")->on("wh_user");
        });
    }

    public function down()
    {
        Schema::dropIfExists('wh_log');
    }
}
