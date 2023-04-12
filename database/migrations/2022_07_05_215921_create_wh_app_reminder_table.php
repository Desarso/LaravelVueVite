<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWhAppReminderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wh_app_reminder', function (Blueprint $table) {
            $table->increments('id');
            $table->string('message');
            $table->datetime('time');
            $table->json('dow');
            $table->json('teams')->nullable();
            $table->json('users_exception')->nullable();
            $table->datetime('last_send')->nullable();
            $table->boolean('enabled')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('wh_app_reminder');
    }
}
