<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnNotifyToWhCleaningStatus extends Migration
{
    public function up()
    {
        Schema::table('wh_cleaning_status', function (Blueprint $table) {
            $table->boolean('notify')->default(0)->after('icon');
        });
    }

    public function down()
    {
        Schema::table('wh_cleaning_status', function (Blueprint $table) {
            $table->dropColumn('notify');
        });
    }
}
