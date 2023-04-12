<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnIdBossToWhUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('wh_user', function (Blueprint $table) {
            $table->integer('idboss')->unsigned()->after('idschedule')->nullable();
            $table->foreign("idboss")->references("id")->on("wh_user");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('wh_user', function (Blueprint $table) {
            $table->dropColumn('idboss');
        });
    }
}
