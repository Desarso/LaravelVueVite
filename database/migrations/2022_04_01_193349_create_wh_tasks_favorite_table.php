<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWhTasksFavoriteTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wh_tasks_favorite', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('name');
            $table->integer('iduser')->unsigned();
            $table->integer('iditem')->unsigned();
            $table->integer('idspot')->unsigned();
            $table->timestamps();

            $table->foreign("iduser")->references("id")->on("wh_user");
            $table->foreign("iditem")->references("id")->on("wh_item");
            $table->foreign("idspot")->references("id")->on("wh_spot");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('wh_tasks_favorite');
    }
}
