<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateAddColumnAppmenuToWhOrganization extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $default = '[{"name":"protocol","enabled":true},{"name":"clockin","enabled":false},{"name":"work-plan","enabled":false},{"name":"asset-loan","enabled":false}]'; 

        Schema::table('wh_organization', function (Blueprint $table) {
            $table->json('appmenu')->nullable()->after('appbar');
        });

        DB::table('wh_organization')
                ->where('id', 1)
                ->update(['appmenu' => $default]);

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('wh_organization', function (Blueprint $table) {
            $table->dropColumn('appmenu');
        });
    }
}
