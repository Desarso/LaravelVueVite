<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWhRoleTable extends Migration
{
    public function up()
    {
        Schema::create('wh_role', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->json('permissions')->nullable();
            $table->boolean('notify')->default(1);           
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('wh_role');
    }
}
