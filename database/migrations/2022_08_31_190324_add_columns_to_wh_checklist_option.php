<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToWhChecklistOption extends Migration
{
    public function up()
    {
        Schema::table('wh_checklist_option', function (Blueprint $table) {
            $table->integer('idparent')->nullable()->after('idasset');  
        });
    }

    public function down()
    {
        Schema::table('wh_checklist_option', function (Blueprint $table) {
            $table->dropColumn('idparent');
        });
    }
}
