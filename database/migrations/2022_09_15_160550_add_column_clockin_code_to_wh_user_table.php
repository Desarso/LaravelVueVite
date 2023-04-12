<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnClockinCodeToWhUserTable extends Migration
{
    public function up()
    {
        Schema::table('wh_user', function (Blueprint $table) {
            $table->string('clockin_code')->unique()->after("shortcuts")->nullable();
        });

        Schema::table('wh_user', function (Blueprint $table) {
            $table->dropColumn('nickname');
            $table->dropColumn('birthdate');
            $table->dropColumn('phonenumber');
            $table->dropColumn('gender');
            $table->dropColumn('idcountry');
            $table->dropColumn('idcity');
            $table->dropColumn('idchinesezodiacsign');
            $table->dropColumn('idastrologicalsign');
            $table->dropColumn('idlevel');
        });
    }

    public function down()
    {
        Schema::table('wh_user', function (Blueprint $table) {
            $table->dropColumn('clockin_code');
        });
    }
}
