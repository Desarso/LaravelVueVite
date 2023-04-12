<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWhTicketTable extends Migration
{
    public function up()
    {
        Schema::create('wh_ticket', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('uuid')->nullable();
            $table->string('name');
            $table->integer('iditem')->unsigned();
            $table->string('code')->nullable();
            $table->integer('quantity')->usigned()->nullable();
            $table->integer('idstatus')->unsigned()->default(1);
            $table->integer('idspot')->unsigned();
            $table->integer('idteam')->unsigned();
            $table->integer('idasset')->unsigned()->nullable();
            $table->integer('idpriority')->unsigned()->default(1);
            $table->text('description')->nullable();
            $table->text('files')->nullable();
            $table->text('justification')->nullable();
            //workflow
            $table->integer('previous')->nullable();
            $table->json('next')->nullable();
            
            $table->boolean('byclient')->default(0);
            $table->integer('created_by')->unsigned();
            $table->integer('updated_by')->unsigned()->nullable();
            $table->dateTime('start')->nullable()->comment('Fecha de inicio en el calendario');
            $table->dateTime('end')->nullable()->comment('Fecha de fin en el calendario');
            $table->dateTime('startdate')->nullable();
            $table->dateTime('resumedate')->nullable();
            $table->dateTime('finishdate')->nullable();
            $table->integer('duration')->default(0);
            $table->dateTime('duedate')->nullable();
            $table->integer('approved')->nullable(); // 0-waiting approval; 1-approved; 2-not approved
            $table->string('geolocation')->nullable();
            $table->string('signature')->nullable();  
            $table->bigInteger('idplanner')->unsigned()->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign("iditem")->references("id")->on("wh_item");
            //$table->foreign("idasset")->references("id")->on("wh_asset");
            $table->foreign("idspot")->references("id")->on("wh_spot");
            $table->foreign("idteam")->references("id")->on("wh_team");
            $table->foreign("idstatus")->references("id")->on("wh_ticket_status");
            $table->foreign("idpriority")->references("id")->on("wh_ticket_priority");
            $table->foreign("created_by")->references("id")->on("wh_user");
            $table->foreign("updated_by")->references("id")->on("wh_user");
            $table->foreign("idplanner")->references("id")->on("wh_planner");
        });
    }

    public function down()
    {
        Schema::dropIfExists('wh_ticket');
    }
}
