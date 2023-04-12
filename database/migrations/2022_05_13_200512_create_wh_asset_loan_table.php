<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWhAssetLoanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wh_asset_loan', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('idasset')->unsigned();
            $table->enum('status', ['OPEN','CLOSE'])->default('OPEN');
            $table->integer('iduser')->unsigned();
            $table->integer('create_by')->unsigned();
            $table->dateTime('duedate')->nullable();
            $table->text('signature')->nullable();
            $table->text('comment')->nullable();
            $table->dateTime('returned_date')->nullable();
            $table->integer('iduser_returned')->unsigned()->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign("idasset")->references("id")->on("wh_asset");
            $table->foreign("iduser")->references("id")->on("wh_user");
            $table->foreign("create_by")->references("id")->on("wh_user");
            $table->foreign("iduser_returned")->references("id")->on("wh_user");

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('wh_asset_loan');
    }
}
