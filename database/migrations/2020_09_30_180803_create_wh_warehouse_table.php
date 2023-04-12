<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWhWarehouseTable extends Migration
{
    public function up()
    {
        Schema::create('wh_warehouse', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('oc', 50)->nullable();
            $table->integer('idstatus')->unsigned()->default(1);
            $table->integer('idspot')->unsigned();
            $table->integer('iditem')->unsigned();
            $table->integer('idsupplier')->unsigned()->nullable();
            $table->integer('idpriority')->unsigned()->default(1);
            $table->unsignedInteger('iduser');
            $table->text('description')->nullable();
            $table->integer('amount');
            $table->enum('coin', ['₡', '$'])->default('₡');
            $table->double('cost', 8, 2)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign("iduser")->references("id")->on("wh_user"); 
            $table->foreign("idpriority")->references("id")->on("wh_ticket_priority");
            $table->foreign("idspot")->references("id")->on("wh_spot");
            $table->foreign("iditem")->references("id")->on("wh_warehouse_item");
            $table->foreign("idstatus")->references("id")->on("wh_warehouse_status");
            $table->foreign("idsupplier")->references("id")->on("wh_warehouse_supplier");
        });
    }

    public function down()
    {
        Schema::dropIfExists('wh_warehouse');
    }
}
