<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnDeletedAtToWhCleaningPlanTable extends Migration
{
    public function up()
    {
        Schema::table('wh_cleaning_plan', function (Blueprint $table) {
            $table->softDeletes()->after("updated_at");
        });
    }

    public function down()
    {
        Schema::table('wh_cleaning_plan', function (Blueprint $table) {
            $table->dropColumn('deleted_at');
        });
    }
}
