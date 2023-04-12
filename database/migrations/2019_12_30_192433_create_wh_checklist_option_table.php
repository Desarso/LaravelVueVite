<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWhChecklistOptionTable extends Migration
{
    public function up()
    {
        Schema::create('wh_checklist_option', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');            
            $table->time('starttime')->nullable();
            $table->integer('idchecklist')->unsigned();
            $table->integer('weight')->usigned()->nullable(); // ponderación
            $table->integer('optiontype')->default(0);
            $table->integer('idmetric')->unsigned()->nullable();
            $table->boolean('isgroup')->default(0);
            $table->integer('iddata')->unsigned()->nullable(); // dropdown
            $table->integer('position')->default(1);
            $table->integer('group')->unsigned()->nullable();            
            $table->integer('iditem')->unsigned()->nullable();
            $table->integer('idspot')->unsigned()->nullable();
            $table->integer('idasset')->unsigned()->nullable();
            $table->json('departments')->nullable();
            $table->boolean('enabled')->default(1);
            $table->timestamps();

            $table->foreign("idchecklist")->references("id")->on("wh_checklist");
            $table->foreign("iditem")->references("id")->on("wh_item");
            $table->foreign("idspot")->references("id")->on("wh_spot");
            $table->foreign("iddata")->references("id")->on("wh_checklist_data");
            $table->foreign("idmetric")->references("id")->on("wh_metric");
        });
    }

    public function down()
    {
        Schema::dropIfExists('wh_checklist_option');
    }
}
