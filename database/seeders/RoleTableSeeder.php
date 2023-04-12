<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class RoleTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('wh_role')->insert([
            [
                'id'          => 1,
                'name'        => 'Jefe',
                'permissions' => '{"edit": true, "create": true, "delete": true, "verify": true, "escalate": true, "evaluate": true, "multitask": true, "assigntask": true, "setduration": true, "changestatus": true, "editfinished": true, "createconfig": true, "chatadmin": true, "setduedate": true}',
                'created_at'  => Carbon::now(),
                'updated_at'  => Carbon::now()
            ],
            [
                'id'          => 2,
                'name'        => 'Colaborador',
                'permissions' => '{"edit": false, "create": true, "delete": false, "verify": false, "escalate": false, "evaluate": false, "multitask": false, "assigntask": true, "setduration": false, "changestatus": true, "editfinished": false, "chatadmin": false, "setduedate": false}',
                'created_at'  => Carbon::now(),
                'updated_at'  => Carbon::now()
            ],
            [
                'id'          => 3,
                'name'        => 'Puedo Reportar',
                'permissions' => '{"edit": false, "create": true, "delete": false, "verify": false, "escalate": false, "evaluate": false, "multitask": false, "assigntask": false, "setduration": false, "changestatus": false, "editfinished": false, "chatadmin": false, "setduedate": false}',
                'created_at'  => Carbon::now(),
                'updated_at'  => Carbon::now()
            ]

        ]);
    }
}
