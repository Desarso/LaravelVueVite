<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWhCouponSheetHeaderTable extends Migration
{
    public function up()
    {
        Schema::create('wh_coupon_sheet_header', function (Blueprint $table) {
            $table->increments('id');
            $table->string('initial_code', 20)->unique();
            $table->enum('status', ['OPEN', 'CLOSE'])->default('OPEN');
            $table->dateTime('startdate'); 
            $table->dateTime('finishdate')->nullable(); 
            $table->integer('created_by')->unsigned();
            $table->integer('closed_by')->unsigned()->nullable();
            $table->timestamps();

            $table->foreign("created_by")->references("id")->on("wh_user");
            $table->foreign("closed_by")->references("id")->on("wh_user");
        });
    }

    public function down()
    {
        Schema::dropIfExists('wh_coupon_sheet_header');
    }
}
