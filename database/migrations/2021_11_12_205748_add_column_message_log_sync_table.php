<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnMessageLogSyncTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('log_sync', function (Blueprint $table) {
            $table->longText('message')->nullable()->after("data");
            $table->integer('error')->nullable()->after("data");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('log_sync', function (Blueprint $table) {
            $table->dropColumn('message');
            $table->dropColumn('error');
        });
    }
}
