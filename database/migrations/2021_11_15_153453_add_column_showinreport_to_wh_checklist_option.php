<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnShowinreportToWhChecklistOption extends Migration
{
    public function up()
    {
        Schema::table('wh_checklist_option', function (Blueprint $table) {
            $table->boolean('showinreport')->default(1)->after("properties");
        });
    }

    public function down()
    {
        Schema::table('wh_checklist_option', function (Blueprint $table) {
            $table->dropColumn('showinreport');
        });
    }
}
