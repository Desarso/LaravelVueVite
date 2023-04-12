<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWhAssetLoanNoteTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wh_asset_loan_note', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('idassetloan')->unsigned();
            $table->string('note');
            $table->integer('created_by')->unsigned();
            $table->enum('type', ['TEXT','IMG'])->default('TEXT');
            $table->timestamps();

            $table->foreign("idassetloan")->references("id")->on("wh_asset_loan");
            $table->foreign("created_by")->references("id")->on("wh_user");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('wh_asset_loan_note');
    }
}
