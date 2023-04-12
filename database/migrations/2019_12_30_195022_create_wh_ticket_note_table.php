<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWhTicketNoteTable extends Migration
{
    public function up()
    {
        Schema::create('wh_ticket_note', function (Blueprint $table) {
            $table->increments('id');
            $table->string('uuid')->nullable();
            $table->bigInteger('idticket')->unsigned();
            $table->integer('idchecklistoption')->nullable();
            $table->string('note');
            $table->integer('created_by')->unsigned();
            $table->integer('type')->default(1);
            $table->timestamps();

            $table->foreign("idticket")->references("id")->on("wh_ticket");
            $table->foreign("created_by")->references("id")->on("wh_user");
        });
    }

    public function down()
    {
        Schema::dropIfExists('wh_ticket_note');
    }
}
