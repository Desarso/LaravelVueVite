<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWhTicketPriorityTable extends Migration
{
    public function up()
    {
        Schema::create('wh_ticket_priority', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->boolean('isurgent')->default(0);
            $table->string('color', 50);
            $table->integer('sla')->default(0)->comment('Service Level Agreement');
            $table->json('options')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('wh_ticket_priority');
    }
}
