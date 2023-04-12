<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWhOrganization extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wh_organization', function (Blueprint $table) {
            $table->increments('id');
            $table->string('key'); // EK09...
            $table->string('name');
            $table->string('description')->nullable();
            $table->string('defaultpassword', 50)->default("whagons");
            $table->string('type')->nullable(); // Tipo de Industria
            $table->json('plansettings')->nullable(); // máxima cantidad de usuarios, espacio, etc + módulos
            $table->json('settings')->nullable(); // Settings default de la compañia..
            $table->json('menusettings')->nullable();
            $table->integer('enabled')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('wh_organization');
    }
}
