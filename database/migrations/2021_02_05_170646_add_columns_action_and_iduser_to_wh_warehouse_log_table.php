<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsActionAndIduserToWhWarehouseLogTable extends Migration
{
    public function up()
    {
        Schema::table('wh_warehouse_log', function (Blueprint $table) {
            $table->enum('action', ['CREATE', 'EDIT', 'DELETE', 'CHANGE_STATUS'])->after('id');
            $table->integer('iduser')->after('idstatus')->unsigned();
        });

        Schema::table('wh_warehouse_log', function (Blueprint $table) {
            $table->foreign("iduser")->references("id")->on("wh_user");
        });
    }

    public function down()
    {
        Schema::table('wh_warehouse_log', function (Blueprint $table) {
            $table->dropColumn('action');
            $table->dropColumn('iduser');
        });
    }
}
