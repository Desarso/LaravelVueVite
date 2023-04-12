<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnPfdTemplateToWhOrganizationTable extends Migration
{
    public function up()
    {
        Schema::table('wh_organization', function (Blueprint $table) {
            $table->string('pdf_template')->default("pdf-checklist")->after("appmenu");
        });
    }

    public function down()
    {
        Schema::table('wh_user', function (Blueprint $table) {
            $table->dropColumn('pdf_template');
        });
    }
}
