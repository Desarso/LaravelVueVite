<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFormColumnsToWhChecklistOption extends Migration
{
    public function up()
    {
        Schema::table('wh_checklist_option', function (Blueprint $table) {
            $table->json('properties')->nullable()->after('departments');
        });
    }

    public function down()
    {
        Schema::table('wh_checklist_option', function (Blueprint $table) {
            $table->dropColumn('properties');
        });
    }
}
