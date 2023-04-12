<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateWhProjectTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('wh_project', function (Blueprint $table) {
            $table->dateTime('start')->nullable();
            $table->dateTime('end')->nullable();
            $table->float('progress')->default(0);
            $table->integer('created_by')->nullable(); //TODO: remove nullable
            $table->integer('updated_by')->nullable();
            $table->boolean('archived')->default(false);
            $table->json('users')->nullable();
            $table->integer('order')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('wh_project', function (Blueprint $table) {
            $table->dropColumn('start');
            $table->dropColumn('end');
            $table->dropColumn('progress');
            $table->dropColumn('created_by');
            $table->dropColumn('updated_by');
            $table->dropColumn('archived');
            $table->dropColumn('users');
        });
     }
}
