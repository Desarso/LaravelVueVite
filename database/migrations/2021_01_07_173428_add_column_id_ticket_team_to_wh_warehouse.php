<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnIdTicketTeamToWhWarehouse extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('wh_warehouse', function (Blueprint $table) {
            $table->bigInteger('idticket')->unsigned()->nullable()->after('iditem');

            $table->foreign("idticket")->references("id")->on("wh_ticket");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('wh_warehouse', function (Blueprint $table) {
            $table->dropColumn('idticket');
        });
    }
}
