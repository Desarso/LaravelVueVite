<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWhClockinLogSummaryTable extends Migration
{
    public function up()
    {
        Schema::create('wh_clockin_log_summary', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('iduser')->unsigned();
            $table->enum('status', ['PENDING', 'VERIFIED'])->default('PENDING');
            $table->date('date');
            $table->integer('late_time')->default(0);
            $table->integer('regular_time')->default(0);
            $table->integer('overtime')->default(0);
            $table->integer('double_time')->default(0);
            $table->boolean('isholiday')->default(0);
            $table->integer('idapprover')->unsigned()->nullable();
            $table->integer('regular_time_approved')->nullable();
            $table->integer('overtime_approved')->nullable();
            $table->integer('double_time_approved')->nullable();
            $table->date('date_approved')->nullable();
            $table->string('note_approved')->nullable();
            $table->boolean('fully_approved')->default(0);
            $table->timestamps();

            $table->foreign("iduser")->references("id")->on("wh_user");
            $table->foreign("idapprover")->references("id")->on("wh_user");
        });
    }

    public function down()
    {
        Schema::dropIfExists('wh_clockin_log_summary');
    }
}
