<?php
namespace Database\Seeders;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SpotTableSeeder extends Seeder
{
    public function run()
    {
        DB::statement('SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO"');

        DB::table('wh_spot_type')->insert([
            [
                'id' => 1,
                'name' => "Organización",            
                'islodging' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => 2,
                'name' => "Área",
                'islodging' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => 3,
                'name' => "Habitación",
                'islodging' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => 4,
                'name' => "Oficina",
                'islodging' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => 5,
                'name' => "Zona común",
                'islodging' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => 6,
                'name' => "Cliente",
                'islodging' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            
        ]);

        DB::table('wh_spot')->insert([
            [
                'id' => 0,
                'idtype' => 1,
                'isbranch' => 1,
                'idparent' => null,
                'name' => "Todos",
                'shortname' => null,
                'cleanable' => 0,
                'floor'=> 1,                
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        ]);
    }
}
