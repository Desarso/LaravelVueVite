<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWhAppTable extends Migration
{
    public function up()
    {
        Schema::create('wh_app', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description');
            $table->string('url');
            $table->string('icon');
            $table->string('color');
            $table->integer('position')->default(1);
            $table->boolean('enabled')->default(1);
            $table->json('settings')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('wh_app');
    }
}
