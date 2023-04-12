<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWhCouponSheetDetailTable extends Migration
{
    public function up()
    {
        Schema::create('wh_coupon_sheet_detail', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('idsheet')->unsigned();
            $table->string('barcode', 10);
            $table->integer('position');
            $table->string('description')->nullable();
            $table->dateTime('scandate')->nullable(); 
            $table->timestamps();

            $table->foreign("idsheet")->references("id")->on("wh_coupon_sheet");
        });
    }

    public function down()
    {
        Schema::dropIfExists('wh_coupon_sheet_detail');
    }
}
