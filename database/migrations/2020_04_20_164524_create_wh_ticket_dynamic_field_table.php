<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWhTicketDynamicFieldTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wh_ticket_dynamic_field', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('idticket')->unsigned();
            $table->integer('iddynamicfield')->unsigned();
            $table->string('value')->nullable(); // ??
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('wh_ticket_dynamic_field');
    }
}
