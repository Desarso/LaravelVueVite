<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWhTicketStatusTable extends Migration
{
    public function up()
    {
        Schema::create('wh_ticket_status', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('color', 50)->nullable();
            $table->string('icon', 50)->nullable();
            $table->json('nextstatus')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('wh_ticket_status');
    }
}