<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnCoreTeamToTableWhUserTeam extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('wh_user_team', function (Blueprint $table) {
            $table->boolean('core_team')->default(0)->after('idrole');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('wh_user_team', function (Blueprint $table) {
            $table->dropColumn('core_team');
        });
    }
}
