<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnForceLoginToTableWhUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('wh_user', function (Blueprint $table) {
            $table->boolean('forcelogin')->default(0)->after("version");
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
            $table->dropColumn('forcelogin');
        });
    }
}
