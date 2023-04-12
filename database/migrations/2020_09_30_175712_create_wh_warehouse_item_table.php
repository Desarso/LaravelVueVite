<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWhWarehouseItemTable extends Migration
{
    public function up()
    {
        Schema::create('wh_warehouse_item', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('idcategory')->unsigned();
            $table->string('name');
            $table->string('code')->nullable();
            $table->string('description')->nullable();
            $table->boolean('enabled')->default(1);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign("idcategory")->references("id")->on("wh_warehouse_category");
        });
    }

    public function down()
    {
        Schema::dropIfExists('wh_warehouse_item');
    }
}
