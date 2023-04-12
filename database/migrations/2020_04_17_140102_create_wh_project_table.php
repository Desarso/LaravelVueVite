<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWhProjectTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wh_project', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('code')->nullable(); //Project Code

            $table->integer('idstatus')->unsigned()->default(1);        
            $table->integer('idteam')->unsigned()->nullable(); // Si un proyecto es privado a un equipo
            $table->boolean('isprivate')->default(0);
            $table->text('files')->nullable();
   
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('idstatus')->references('id')->on('wh_ticket_status');             
            $table->foreign('idteam')->references('id')->on('wh_team');
      
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('wh_project');
    }
}
