<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWhMenuTable extends Migration
{
    public function up()
    {
        Schema::create('wh_menu', function (Blueprint $table) {
            $table->id();
            $table->text('name');
            $table->enum('type', ['HEADER', 'NAV', 'PARENT', 'CHILD'])->default('NAV');
            $table->text('icon')->nullable();
            $table->text('url')->nullable();
            $table->integer('idparent')->nullable();
            $table->integer('position')->default(1);
            $table->boolean('enable')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('wh_menu');
    }
}
