<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnAutoClockoutToWhClockinLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('wh_clockin_log', function (Blueprint $table) {
            $table->boolean('auto_clockout')->default(0)->after("duration");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('wh_clockin_log', function (Blueprint $table) {
            $table->dropColumn('auto_clockout');
        });
    }
}
