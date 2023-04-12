<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWhReminderTable extends Migration
{
    public function up()
    {
        Schema::create('wh_reminder', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->enum('type', ['DUEDATE', 'BY_CLIENT']);
            $table->bigInteger('idticket')->unsigned()->nullable();
            $table->dateTime('notify_at');
            $table->boolean('sent')->default(0);
            $table->timestamps();

            $table->foreign("idticket")->references("id")->on("wh_ticket");
        });
    }

    public function down()
    {
        Schema::dropIfExists('wh_reminder');
    }
}
