<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWhUserTable extends Migration
{
    public function up()
    {
        Schema::create('wh_user', function (Blueprint $table) {
            $table->increments('id');
            $table->string('firstname');
            $table->string('lastname');
            $table->string('nickname')->nullable();   // apodo
            $table->string('username')->unique();
            $table->string('password');
            $table->json('spots')->nullable();   
            $table->date('birthdate')->nullable(); // opcional y solo podría prover mes y día (año podría ser opcional)
            $table->string('email')->unique()->nullable();   
            $table->string('phonenumber')->unique()->nullable();
            $table->string('gender')->nullable(); 
            $table->integer('idcountry')->nullable(); 
            $table->integer('idcity')->nullable();            
            $table->string('job')->nullable(); // Describe mi puesto de Trabajo: Ej: Director Ejecutivo   
            $table->string('urlpicture')->nullable();                    
                
                     
            $table->string('idchinesezodiacsign')->nullable(); // signo del zodiaco chino..basado año de nacimiento /Ej: rata)
                                                               // Solo si el usuario provee el año de nacimiento.
            $table->date('idastrologicalsign')->nullable(); // signo astrologico (Ej: Sagitario)
            $table->boolean('issuperadmin')->default(0); // usuario whagons
            $table->boolean('isadmin')->default(0);  // poder configurar
            $table->boolean('enabled')->default(1); // Disponible en el sistema
            $table->boolean('online')->default(1);   // para saber si la persona está conectada o no
            $table->integer('idstatus')->default(1); // trabajando, enfermo, de vacaciones, en reunión, etc, no molestar..
            $table->integer('credits')->default(0); // gamification
            $table->integer('idlevel')->nullable();  // gamification          
            $table->rememberToken();
            $table->string('version')->nullable();
            $table->boolean('available')->default(0); // gamification
            $table->json('preferences')->nullable();
            $table->dateTime('last_login_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
        });
    }

    public function down()
    {
        Schema::dropIfExists('wh_user');
    }
}
