<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWhWarehouseStatusTable extends Migration
{
    public function up()
    {
        Schema::create('wh_warehouse_status', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('color')->nullable();
            $table->string('icon')->nullable();
            $table->json('nextstatus')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('wh_warehouse_status');
    }
}
