<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWhProtocolTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wh_protocol', function (Blueprint $table) {
            $table->increments('id');           
            $table->string('name');  // question
            $table->integer('idtype')->unsigned(); // categoría/tipo del Protocolo Ej: Limpieza, Habitaciones, Higiene Personal, EPP.
            $table->string('version')->nullable();
            $table->string('code')->nullable(); 
            $table->string('smallimage')->nullable();           
            $table->string('image')->nullable();
            $table->text('html')->nullable();
            $table->boolean('isemergency')->default(0);
            $table->boolean('activated')->default(0); // si isemergency = true
            $table->text('reference')->nullable(); // Referencia al Protocolo, Lineamiento
            $table->string('lan');
            $table->text('qrcode')->nullable();
            $table->boolean('enabled')->default(1);
            $table->integer('idsupervisor')->nullable(); // persona encargada de velar por que se cumpla el protocolo            

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
        Schema::dropIfExists('wh_protocol');
    }
}
