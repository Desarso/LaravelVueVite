<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wh_notification_user', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('idnotification')->unsigned();
            $table->integer('iduser')->unsigned();
            $table->boolean('read')->default(0);
            $table->timestamps();

            $table->foreign("idnotification")->references("id")->on("wh_notification");
            $table->foreign("iduser")->references("id")->on("wh_user");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('wh_notification_user');
    }
}
