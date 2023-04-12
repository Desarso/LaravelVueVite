<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWhAttendanceResumeOvertimeTable extends Migration
{
    public function up()
    {
        Schema::create('wh_attendance_resume_overtime', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('iduser')->unsigned();
            $table->integer('normal_time')->default(0);
            $table->integer('half_time')->default(0);
            $table->integer('double_time')->default(0);
            $table->integer('idapprover')->nullable()->unsigned();
            $table->enum('status', ['PENDING', 'APPROVED', 'REPROBATE']);
            $table->dateTime('approvaldate')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();

            $table->foreign("iduser")->references("id")->on("wh_user");
            $table->foreign("idapprover")->references("id")->on("wh_user");
        });
    }

    public function down()
    {
        Schema::dropIfExists('wh_attendance_resume_overtime');
    }
}
