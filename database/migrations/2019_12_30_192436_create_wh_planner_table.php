<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWhPlannerTable extends Migration
{
    public function up()
    {
        Schema::create('wh_planner', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('iditem')->unsigned(); 
            $table->integer('idspot')->unsigned(); 
            $table->json('users')->nullable();  
            $table->text('description')->nullable();            
            $table->datetime('start');  
            $table->datetime('end');      
            $table->boolean('all_day')->default(0);
            $table->string('by_day', 255)->nullable();
            $table->string('by_month_day', 255)->nullable();
            $table->enum('frequency', ['DAILY', 'WEEKLY', 'MONTHLY']);
            $table->integer('interval')->default(0);   
            $table->datetime('until')->nullable();     
            $table->boolean('isfinished')->default(0);
            $table->boolean('enabled')->default(1);
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign("iditem")->references("id")->on("wh_item");
            $table->foreign("idspot")->references("id")->on("wh_spot");
        });
    }

    public function down()
    {
        Schema::dropIfExists('wh_planner');
    }
}
