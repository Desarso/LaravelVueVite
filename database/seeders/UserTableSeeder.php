<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class UserTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('wh_user')->insert([
            [
                'id'         => 1,
                'username'   => 'whagons',
                'email'      => 'help@dinganddone.com',
                'password'   => bcrypt('lavacaloca'),
                'firstname'  => 'Whagons',
                'lastname'   => 'Corporation',
                'spots'      => '[0]',
                'urlpicture' => 'https://dingdonecdn.nyc3.digitaloceanspaces.com/demov2/tickets/wzG6bH0YAlfX2N4TtBU6FsMcuqC37Y0KyxIkq4Ct.png',
                'preferences' => '{"theme": "light", "sidebarCollapsed": false}',
                'isadmin' => true,
                'issuperadmin' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]
        ]);

        DB::table('wh_user_team')->insert([
            ['iduser' => 1, 'idteam' => 1, 'idrole' => 1],
            ['iduser' => 1, 'idteam' => 2, 'idrole' => 1]
        ]);
    }
}
