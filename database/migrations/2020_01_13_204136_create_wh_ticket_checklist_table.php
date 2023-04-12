<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWhTicketChecklistTable extends Migration
{
    public function up()
    {
        Schema::create('wh_ticket_checklist', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('idticket')->unsigned();
            $table->integer('idchecklist')->unsigned();
            $table->json("options");
            $table->json("results")->nullable();
            $table->integer('idevaluator')->unsigned()->nullable();
            $table->timestamps();

            $table->foreign("idticket")->references("id")->on("wh_ticket");
            $table->foreign("idchecklist")->references("id")->on("wh_checklist");
            $table->foreign("idevaluator")->references("id")->on("wh_user");
        });
    }

    public function down()
    {
        Schema::dropIfExists('wh_ticket_checklist');
    }
}
