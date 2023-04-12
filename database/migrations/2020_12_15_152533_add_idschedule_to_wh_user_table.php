<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIdscheduleToWhUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('wh_user', function (Blueprint $table) {
            $table->integer('idschedule')->unsigned()->nullable()->after('spots');
            $table->foreign("idschedule")->references("id")->on("wh_schedule");
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
            $table->dropColumn('idschedule');
        });
    }
}
