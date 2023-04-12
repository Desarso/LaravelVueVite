<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWhChecklistData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wh_checklist_data', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->json('data'); // [{value:1, text: 'malo'},{value: 2, text: 'bueno'}]
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('wh_checklist_data');
    }
}
