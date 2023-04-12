<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterWhScheduleTable extends Migration
{
    public function up()
    {
        DB::statement('ALTER TABLE wh_schedule ADD COLUMN starttime TIME NULL AFTER idtype;');
        DB::statement('ALTER TABLE wh_schedule ADD COLUMN endtime TIME NULL AFTER starttime;');
    }

    public function down()
    {
        //
    }
}
