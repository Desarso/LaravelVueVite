<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWhAttendanceLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wh_attendance_log', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('iduser')->unsigned();
            $table->datetime('CheckIn');
            $table->datetime('CheckOut')->nullable();
            $table->integer('over_time')->default(0);
            $table->integer('late_time')->default(0);
            $table->integer('duration')->default(0);
            $table->timestamps();

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
        Schema::dropIfExists('wh_attendance_log');
    }
}
