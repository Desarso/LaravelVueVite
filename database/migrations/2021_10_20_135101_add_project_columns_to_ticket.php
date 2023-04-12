<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProjectColumnsToTicket extends Migration
{
    
    /*
    // Lo que ocupa el gantt de dhtmlx
    $table->increments('id');
            $table->string('text');  // name
            $table->integer('duration'); // ya está
            $table->float('progress'); // 
            $table->dateTime('start_date'); // start
            $table->integer('parent');
            $table->timestamps();            
    */
    
    

    public function up()
    {
        Schema::table('wh_ticket', function (Blueprint $table) {
            $table->integer('idproject')->after("idplanner")->nullable();
            $table->integer('parent')->after("id")->nullable();            
            $table->integer('goal')->after("idplanner")->nullable();
            $table->float('progress')->after("idplanner")->nullable();
            $table->integer('sortorder')->default(0);
            $table->string('tasktype')->after('idplanner')->nullable();
        });
    }

    public function down()
    {
        Schema::table('wh_ticket', function (Blueprint $table) {
            $table->dropColumn('idproject');
            $table->dropColumn('parent');
            $table->dropColumn('goal');
            $table->dropColumn('progress');
            $table->dropColumn('sortorder');
            $table->dropColumn('tasktype');  
        });
    }
}
