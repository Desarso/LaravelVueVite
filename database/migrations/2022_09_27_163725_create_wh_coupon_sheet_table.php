<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWhCouponSheetTable extends Migration
{
    public function up()
    {
        Schema::create('wh_coupon_sheet', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('idheader')->unsigned();
            $table->string('barcode', 20)->unique();
            $table->integer('created_by')->unsigned();
            $table->timestamps();

            $table->foreign("idheader")->references("id")->on("wh_coupon_sheet_header");
            $table->foreign("created_by")->references("id")->on("wh_user");
        });
    }

    public function down()
    {
        Schema::dropIfExists('wh_coupon_sheet');
    }
}
