<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnIdspotToWhWorkPlanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('wh_work_plan', function (Blueprint $table) {
            $table->integer('idspot')->unsigned()->after('type');

            $table->foreign("idspot")->references("id")->on("wh_spot");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('wh_work_plan', function (Blueprint $table) {
            $table->dropColumn('idspot');
        });
    }
}
