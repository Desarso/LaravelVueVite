<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWhTicketApproverTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wh_ticket_approver', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('idticket')->unsigned();
            $table->integer('iduser')->unsigned();
            $table->integer('sequence')->unsigned();
            $table->dateTime('approved_at')->nullable();
            $table->timestamps();
            
            $table->foreign("iduser")->references("id")->on("wh_user");
            $table->foreign("idticket")->references("id")->on("wh_ticket");
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('wh_ticket_approver');
    }
}
