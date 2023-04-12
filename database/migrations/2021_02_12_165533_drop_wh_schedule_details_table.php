<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropWhScheduleDetailsTable extends Migration
{
    public function up()
    {
        Schema::dropIfExists('wh_schedule_detail');
    }

    public function down()
    {
        //
    }
}
