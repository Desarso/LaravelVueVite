<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnBossesToTableWhTeam extends Migration
{
    public function up()
    {
        Schema::table('wh_team', function (Blueprint $table) {
            $table->json('bosses')->after('emails')->nullable();
        });
    }

    public function down()
    {
        Schema::table('wh_team', function (Blueprint $table) {
            $table->dropColumn('bosses');
        });
    }
}
