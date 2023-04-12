<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnSubdomainToWhOrganizationTable extends Migration
{
    public function up()
    {
        Schema::table('wh_organization', function (Blueprint $table) {
            $table->string('subdomain')->nullable()->after('description');
        });
    }

    public function down()
    {
        Schema::table('wh_organization', function (Blueprint $table) {
            $table->dropColumn('subdomain');
        });
    }
}
