<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWhAssetTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wh_asset', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('idcategory')->unsigned();
            $table->integer('idstatus')->unsigned();
            $table->integer('idspot')->unsigned()->nullable();
            $table->string('picture_url')->nullable();
            $table->string('code')->nullable();
            $table->string('brand')->nullable();
            $table->string('model')->nullable();
            $table->binary('qrcode')->nullable();
            $table->string('icon')->nullable();
            $table->string('color')->nullable();            
            $table->dateTime('purchase_date')->nullable();
            $table->integer('warranty')->nullable(); // en número de días...y se considera a partir de fecha de compra
            $table->dateTime('installation_date')->nullable();
            $table->dateTime('expiration_date')->nullable();            
            $table->integer('useful_life')->nullable(); // meses
            $table->json('documents')->nullable();
            $table->json('plans')->nullable();
            $table->boolean('enabled')->default(1);

            // Analizar si agregamos un campo para documentos/manuales/especificaciones del Asset (ocupamos un Documents App)            
            // Analizar si agregamos un campo que apunta a planes del planner (ej: mantenimiento)

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
        Schema::dropIfExists('wh_asset');
    }
}
