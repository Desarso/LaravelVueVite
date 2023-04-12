<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyWhAssetTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();
        Schema::table('wh_planner', function (BluePrint $table) {
            $table->dropForeign('wh_planner_idasset_foreign');
        });
        Schema::dropIfExists('wh_asset');
        Schema::enableForeignKeyConstraints();

        Schema::create('wh_asset', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('idcategory')->unsigned();
            $table->integer('idstatus')->unsigned();
            $table->string('code')->unique();
            $table->text('photo')->nullable();
            $table->string('model')->nullable();
            $table->dateTime('purchase_date')->nullable();
            $table->decimal('cost', 9, 2)->default(0);
            $table->text('description')->nullable();
            $table->boolean('isloaned')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign("idcategory")->references("id")->on("wh_asset_category");
            $table->foreign("idstatus")->references("id")->on("wh_asset_status");
        });

        Schema::table('wh_planner', function (Blueprint $table) {
            $table->foreign("idasset")->references("id")->on("wh_asset");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
