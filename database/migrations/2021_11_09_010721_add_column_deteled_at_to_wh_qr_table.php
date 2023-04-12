<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnDeteledAtToWhQrTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('wh_qr', function (Blueprint $table) {
            $table->datetime('deleted_at')->nullable()->after("updated_at");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('wh_qr', function (Blueprint $table) {
            $table->dropColumn('deleted_at');
        });
    }
}
