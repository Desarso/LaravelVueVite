<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ProductionInputTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('wh_production_input')->insert([
            [
                'id'              => 1,
                'name'            => 'Etiquetas',
                'description'     => '',
                'formula'         => 1,
                'measure'         => 'unidades',
                'pack_size'       => 10000,
                'pack_placing_duration' => 90,
                'buffer'          => 0,  
                'idstop'          => null,
                'created_at'      => Carbon::now(),
                'updated_at'      => Carbon::now()
            ],
            [
                'id'              => 2,
                'name'            => 'Hilo',
                'description'     => '',
                'formula'         => 1,
                'measure'         => 'klg',
                'pack_size'       => 59000,
                'pack_placing_duration' => 120,
                'buffer'          => 1,  
                'idstop'          => null,
                'created_at'      => Carbon::now(),
                'updated_at'      => Carbon::now()
            ],
            [
                'id'              => 3,
                'name'            => 'Filtro',
                'description'     => '',
                'formula'         => 1,
                'measure'         => 'klg',
                'pack_size'       => 68000,
                'pack_placing_duration' => 120,
                'buffer'          => 1,  
                'idstop'          => null,
                'created_at'      => Carbon::now(),
                'updated_at'      => Carbon::now()
            ],
            [
                'id'              => 4,
                'name'            => 'SE Hermético',
                'description'     => '',
                'formula'         => 1,
                'measure'         => 'klg',
                'pack_size'       => 22000,
                'pack_placing_duration' => 120,
                'buffer'          => 1,  
                'idstop'          => null,
                'created_at'      => Carbon::now(),
                'updated_at'      => Carbon::now()
            ]
           
        ]);
    }
}
