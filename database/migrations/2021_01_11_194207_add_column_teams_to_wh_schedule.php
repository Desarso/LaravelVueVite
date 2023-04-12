<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnTeamsToWhSchedule extends Migration
{
    public function up()
    {
        Schema::table('wh_schedule', function (Blueprint $table) {
            $table->json('teams')->nullable()->after('name');
        });
    }

    public function down()
    {
        Schema::table('wh_schedule', function (Blueprint $table) {
            $table->dropColumn('bosses');
        });
    }
}
