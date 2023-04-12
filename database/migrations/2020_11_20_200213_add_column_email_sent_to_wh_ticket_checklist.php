<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnEmailSentToWhTicketChecklist extends Migration
{
    public function up()
    {
        Schema::table('wh_ticket_checklist', function (Blueprint $table) {
            $table->integer('email_sent')->default(0)->after('idevaluator'); 
        });
    }

    public function down()
    {
        Schema::table('wh_ticket_checklist', function (Blueprint $table) {
            $table->dropColumn('email_sent');
        });
    }
}
