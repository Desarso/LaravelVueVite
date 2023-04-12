<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWhSpotTable extends Migration
{
    public function up()
    {
        Schema::create('wh_spot', function (Blueprint $table) {
            $table->increments('id');
            $table->string('idexternal', 50)->nullable();
            $table->string('name');
            $table->string('shortname')->nullable();  // Ej: Habitación 001 -> 001 ó Hab001, ..(nombre más corto)
            $table->integer('idtype')->unsigned();          
            $table->integer('idparent')->nullable();          
            $table->boolean('isbranch')->default(0);  
            $table->boolean('cleanable')->default(0);  // can be cleaned            
            //$table->boolean('dnd')->default(0);         // do not disturb    
            //$table->boolean('isrush')->default(0);      // give priority for cleaning...            
            $table->integer('idcleaningstatus')->unsigned()->default(1);
            $table->integer('idcleaningplan')->unsigned()->nullable();
           // $table->bigInteger('idcleaningticket')->unsigned()->nullable();
            $table->integer('floor')->unsigned()->default(1);  // los pisos pueden ser negativos
            // for the cleaning Dashboard...determine area positions...
            //$table->integer('position')->default(-1);
            $table->string('geolocation')->nullable();   
            $table->boolean('enabled')->default(1);      
            $table->timestamps();
            $table->softDeletes();

            $table->foreign("idtype")->references("id")->on("wh_spot_type");     
            $table->foreign("idcleaningstatus")->references("id")->on("wh_cleaning_status");           
            
        
            
        });
    }

    public function down()
    {
        Schema::dropIfExists('wh_spot');
    }
}
