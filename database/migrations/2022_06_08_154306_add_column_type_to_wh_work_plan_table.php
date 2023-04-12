<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnTypeToWhWorkPlanTable extends Migration
{
    public function up()
    {
        Schema::table('wh_work_plan', function (Blueprint $table) {
            $table->enum('type', ['STANDARD', 'EVALUATIVE'])->after('name');
        });
    }

    public function down()
    {
        Schema::table('wh_work_plan', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
}
