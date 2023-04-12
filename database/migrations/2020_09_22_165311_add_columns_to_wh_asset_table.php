<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToWhAssetTable extends Migration
{
    public function up()
    {
        Schema::table('wh_asset', function (Blueprint $table) {
            $table->boolean('leased')->default(1)->after('plans');
            $table->string('buyer')->nullable()->after('plans');
            $table->string('lessee')->nullable()->after('plans');
            $table->date('lease_start_date')->nullable()->after('plans');  
            $table->date('lease_finish_date')->nullable()->after('plans'); 
            $table->date('warranty_duedate')->nullable()->after('plans'); 
            $table->integer('idresponsible')->unsigned()->nullable()->after('plans');
            $table->integer('idteam')->unsigned()->nullable()->after('plans');

            $table->foreign("idresponsible")->references("id")->on("wh_user");
            $table->foreign("idteam")->references("id")->on("wh_team");
        });
    }

    public function down()
    {
        Schema::table('wh_asset', function (Blueprint $table) {
            $table->dropColumn('leased');
            $table->dropColumn('buyer');
            $table->dropColumn('lessee');
            $table->dropColumn('lease_start_date');
            $table->dropColumn('lease_finish_date');
            $table->dropColumn('warranty_duedate');
            $table->dropColumn('idresponsible');
            $table->dropColumn('idteam');
        });
    }
}
