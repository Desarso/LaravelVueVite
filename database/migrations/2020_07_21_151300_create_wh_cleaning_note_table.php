<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWhCleaningNoteTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wh_cleaning_note', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('idplaner')->unsigned();
            $table->enum('type',['TEXT','IMAGE'])->default('TEXT');
            $table->string('note');
            $table->integer('created_by')->unsigned();
            $table->timestamps();

            $table->foreign("idplaner")->references("id")->on("wh_cleaning_plan");
            $table->foreign("created_by")->references("id")->on("wh_user");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('wh_cleaning_note');
    }
}
