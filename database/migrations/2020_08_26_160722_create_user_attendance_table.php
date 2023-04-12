<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserAttendanceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wh_user_attendance', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('iduser')->unsigned();
            $table->enum('status',['WORKING','OUT'])->default('WORKING');
            $table->json('start_location');
            $table->json('end_location')->nullable();
            $table->datetime('punch_in');
            $table->datetime('punch_out')->nullable();
            $table->boolean('fake_location')->default(0);
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
        Schema::dropIfExists('user_attendance');
    }
}
