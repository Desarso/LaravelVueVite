<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnShowingridToWhTicketTypeTable extends Migration
{
    public function up()
    {
        Schema::table('wh_ticket_type', function (Blueprint $table) {
            $table->boolean('showingrid')->default(1)->after('hassla');
        });
    }

    public function down()
    {
        Schema::table('wh_ticket_type', function (Blueprint $table) {
            $table->dropColumn('showingrid');
        });
    }
}
