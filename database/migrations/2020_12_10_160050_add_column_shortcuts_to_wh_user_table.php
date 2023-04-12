<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnShortcutsToWhUserTable extends Migration
{
    public function up()
    {
        Schema::table('wh_user', function (Blueprint $table) {
            $table->json('shortcuts')->nullable()->after('preferences');
        });
    }

    public function down()
    {
        Schema::table('wh_user', function (Blueprint $table) {
            $table->dropColumn('shortcuts');
        });
    }
}
