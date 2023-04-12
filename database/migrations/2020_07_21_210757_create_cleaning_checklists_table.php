<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCleaningChecklistsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wh_cleaning_checklist', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('idplaner')->unsigned();
            $table->integer('idchecklist')->unsigned();
            $table->json("options");
            $table->json("results")->nullable();
          
            $table->timestamps();

            $table->foreign("idplaner")->references("id")->on("wh_cleaning_plan");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('wh_cleaning_checklist');
    }
}
