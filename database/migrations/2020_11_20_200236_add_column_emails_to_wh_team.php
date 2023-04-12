<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnEmailsToWhTeam extends Migration
{
    public function up()
    {
        Schema::table('wh_team', function (Blueprint $table) {
            $table->text('emails')->nullable()->after('color');
        });
    }

    public function down()
    {
        Schema::table('wh_team', function (Blueprint $table) {
            $table->dropColumn('emails');
        });
    }
}
