<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWhChecklistGroupWeightTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wh_checklist_group_weight', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('idchecklist')->unsigned();
            $table->integer('group');
            $table->integer('idparent')->nullable();
            $table->integer('weight')->default(0);

            $table->foreign("idchecklist")->references("id")->on("wh_checklist");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('wh_checklist_group_weight');
    }
}
