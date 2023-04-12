<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PrductionBreakTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('wh_production_break')->insert([
            [
                'id'              => 1,                
                'name'            => 'Desayuno',
                'description'     => null,                
                'duration'        => 15,
                'dow'             => null,  
                'created_at'      => Carbon::now(),
                'updated_at'      => Carbon::now()
            ],
            [
                'id'              => 2,                
                'name'            => 'Almuerzo',
                'description'     => null,                
                'duration'        => 30,
                'dow'             => null,
                'created_at'      => Carbon::now(),
                'updated_at'      => Carbon::now()
            ],
            [
                'id'              => 3,                
                'name'            => 'Cena',
                'description'     => null,
                'dow'             => null,                
                'duration'        => 30,
                'created_at'      => Carbon::now(),
                'updated_at'      => Carbon::now()
            ],
            [
                'id'              => 4,                
                'name'            => 'Arranque',
                'description'     => null,
                'dow'             => null,                
                'duration'        => 15,
                'created_at'      => Carbon::now(),
                'updated_at'      => Carbon::now()
            ],
            [
                'id'              => 5,                
                'name'            => 'Limpieza',
                'description'     => null,
                'dow'             => null,                
                'duration'        => 10,
                'created_at'      => Carbon::now(),
                'updated_at'      => Carbon::now()
            ],
            [
                'id'              => 6,                
                'name'            => 'Limpieza Profunda',
                'description'     => null,
                'dow'             => '[6]',                
                'duration'        => 45,
                'created_at'      => Carbon::now(),
                'updated_at'      => Carbon::now()
            ],
        ]);
    }
}
