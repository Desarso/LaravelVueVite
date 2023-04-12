<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TeamTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('wh_team')->insert([
            [
                'id'              => 1,
                'name'            => 'Mantenimiento',
                'color'           => '#221C69',
                'bosses'           => '[]',
                'created_at'      => Carbon::now(),
                'updated_at'      => Carbon::now()
            ],
            [
                'id'              => 2,
                'name'            => 'Housekeeping',
                'color'           => '#D753C2',
                'bosses'           => '[]',
                'created_at'      => Carbon::now(),
                'updated_at'      => Carbon::now()
            ],
            [
                'id'              => 3,
                'name'            => 'Técnicos',
                'color'           => 'blue',
                'bosses'           => '[]',
                'created_at'      => Carbon::now(),
                'updated_at'      => Carbon::now()
            ],
            [
                'id'              => 4,
                'name'            => 'Auxiliares',
                'color'           => 'green',
                'bosses'           => '[]',
                'created_at'      => Carbon::now(),
                'updated_at'      => Carbon::now()
            ],
            [
                'id'              => 5,
                'name'            => 'Operarios',
                'color'           => 'orange',
                'bosses'           => '[]',
                'created_at'      => Carbon::now(),
                'updated_at'      => Carbon::now()
            ]
        ]);
    }
}
