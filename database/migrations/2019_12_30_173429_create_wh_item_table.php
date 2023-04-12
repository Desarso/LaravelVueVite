<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWhItemTable extends Migration
{
    public function up()
    {
        Schema::create('wh_item', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('description')->nullable();
            $table->integer('idtype')->unsigned();
            // Algunas tareas queremos que ocurran lo menos posible, cómo en el caso de averías.
            // Las identificamos con isglitch = true.
            // Alrededor de este tipo de tareas podemos contruír kpis, en dónde buscamos
            // minimizar la ocurrencia de los mismos.            
            $table->boolean('isglitch')->nullable()->default(0); 
            // Lista de tareas que se deben generar al finalizar una tarea con el item actual.
            $table->json('next')->nullable();
            
            $table->integer('idteam')->unsigned();
            $table->integer('idchecklist')->unsigned()->nullable();
            $table->json('spots')->nullable();
            $table->json('users')->nullable();
            $table->integer('code')->nullable();
            $table->json('tags')->nullable();
            $table->integer('idpriority')->unsigned()->default(1);
            // expected resolution time
            $table->integer('sla')->nullable();
            $table->integer('points')->default(0);

            $table->integer('idprotocol')->unsigned()->nullable();
            $table->boolean('isprivate')->default(0);
            $table->boolean('enabled')->default(1);

            $table->timestamps();
            $table->softDeletes();

            $table->foreign("idtype")->references("id")->on("wh_ticket_type");
            $table->foreign("idpriority")->references("id")->on("wh_ticket_priority");
            $table->foreign("idteam")->references("id")->on("wh_team");
            $table->foreign("idchecklist")->references("id")->on("wh_checklist");
            $table->foreign("idprotocol")->references("id")->on("wh_protocol");
        });
    }

    public function down()
    {
        Schema::dropIfExists('wh_item');
    }
}
