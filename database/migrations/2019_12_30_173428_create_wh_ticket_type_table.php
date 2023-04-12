<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWhTicketTypeTable extends Migration
{
    public function up()
    {
        Schema::create('wh_ticket_type', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('description')->nullable();    
            $table->integer('idteam')->unsigned();
            $table->string('icon')->nullable()->default("fad fa-exclamation-circle");
            $table->string('color')->nullable()->default('#fd774d');
            $table->boolean('iscleaningtask')->default(0);
            $table->boolean('hassla')->default(0)->comment("Has Service Level Agreement");
            $table->json('template')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Foreign
            $table->foreign("idteam")->references("id")->on("wh_team");
        });
    }

    public function down()
    {
        Schema::dropIfExists('wh_ticket_type');
    }
}
