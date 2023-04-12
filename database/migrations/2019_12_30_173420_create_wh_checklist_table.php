<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWhChecklistTable extends Migration
{
    public function up()
    {
        Schema::create('wh_checklist', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->text('description')->nullable();        
            $table->string('version')->nullable();                                
            $table->string('code')->nullable();                   
            $table->integer('created_by')->unsigned()->nullable();
            $table->integer('reviewed_by')->unsigned()->nullable(); // reviewed
            $table->integer('enabled')->default(1);         
            $table->timestamps();
            $table->softDeletes();

            $table->foreign("created_by")->references("id")->on("wh_user");
            $table->foreign("reviewed_by")->references("id")->on("wh_user");

        });
    }

    public function down()
    {
        Schema::dropIfExists('wh_checklist');
    }
}
