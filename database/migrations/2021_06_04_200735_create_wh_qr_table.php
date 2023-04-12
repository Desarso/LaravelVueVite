<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWhQrTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wh_qr', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('iditem')->unsigned();
            $table->integer('idspot')->unsigned();
            $table->text('qr')->nullable();
            $table->timestamps();

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
        Schema::dropIfExists('wh_qr');
    }
}
