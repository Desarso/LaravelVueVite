<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnLocationNameToWhLogTable extends Migration
{
    public function up()
    {
        Schema::table('wh_log', function (Blueprint $table) {
            $table->text('locationname')->nullable()->after("location");
        });
    }

    public function down()
    {
        Schema::table('wh_log', function (Blueprint $table) {
            $table->dropColumn('locationname');
        });
    }
}
