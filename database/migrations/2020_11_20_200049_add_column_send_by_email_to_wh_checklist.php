<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnSendByEmailToWhChecklist extends Migration
{
    public function up()
    {
        Schema::table('wh_checklist', function (Blueprint $table) {
            $table->boolean('send_by_email')->default(0)->after('reviewed_by'); 
        });
    }

    public function down()
    {
        Schema::table('wh_checklist', function (Blueprint $table) {
            $table->dropColumn('send_by_email');
        });
    }
}
