<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWhProductionBreakTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wh_production_break', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->text('description')->nullable(); // Indicar el horario acá            
            $table->integer('duration')->unsigned()->default(0);
            // Day Of Week. Si está null, aplica para cualquier día.
            $table->json('dow')->nullable(); 
            $table->boolean('enabled')->default(1);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('wh_production_break');
    }
}
