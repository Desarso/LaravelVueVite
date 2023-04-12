<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SettingUpdateTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('wh_setting_update')->insert([
            [
                'id'          => 1, 
                'name'        => 'wh_spot',
                'created_at'  => Carbon::now(),
                'updated_at'  => Carbon::now()
            ],
            [
                'id'          => 2,
                'name'        => 'wh_item',
                'created_at'  => Carbon::now(),
                'updated_at'  => Carbon::now()
            ],
            [
                'id'          => 3,
                'name'        => 'wh_user',
                'created_at'  => Carbon::now(),
                'updated_at'  => Carbon::now()
            ],
            [
                'id'          => 4,
                'name'        => 'wh_checklist',
                'created_at'  => Carbon::now(),
                'updated_at'  => Carbon::now()
            ],
            [
                'id'          => 5,
                'name'        => 'wh_checklist_data',
                'created_at'  => Carbon::now(),
                'updated_at'  => Carbon::now()
            ],
            [
                'id'          => 6,
                'name'        => 'wh_user_team',
                'created_at'  => Carbon::now(),
                'updated_at'  => Carbon::now()
            ],
            [
                'id'          => 7,
                'name'        => 'wh_tag',
                'created_at'  => Carbon::now(),
                'updated_at'  => Carbon::now()
            ],
            [
                'id'          => 8,
                'name'        => 'wh_ticket_priority',
                'created_at'  => Carbon::now(),
                'updated_at'  => Carbon::now()
            ]
        ]);
    }
}
