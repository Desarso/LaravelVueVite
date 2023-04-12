<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToWhPlannerTable extends Migration
{
    public function up()
    {
        Schema::table('wh_planner', function (Blueprint $table) {
            $table->json('copies')->nullable()->after('users');
            $table->json('tags')->nullable()->after('users');
            $table->integer('idasset')->unsigned()->nullable()->after('idspot'); 

            $table->foreign("idasset")->references("id")->on("wh_asset");
        });
    }

    public function down()
    {
        Schema::table('wh_planner', function (Blueprint $table) {
            $table->dropColumn('copies');
            $table->dropColumn('tags');
            $table->dropColumn('idasset');
        });
    }
}
