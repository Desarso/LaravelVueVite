<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AssetCategoryTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('wh_asset_category')->insert([
            [
                'id'              => 1,
                'name'            => 'Montacargas',            
                'description'     => 'Equipos de montacargas',                
                'created_at'      => Carbon::now(),
                'updated_at'      => Carbon::now()
            ],
            [
                'id'              => 2,
                'name'            => 'Transporte',                
                'description'     => '',                 
                'created_at'      => Carbon::now(),
                'updated_at'      => Carbon::now()
            ],
            [
                'id'              => 3,
                'name'            => 'Refrigeración',                
                'description'     => '',                 
                'created_at'      => Carbon::now(),
                'updated_at'      => Carbon::now()
            ],
            [
                'id'              => 4,
                'name'            => 'Equipos de Cómputo',                
                'description'     => 'Computadoreas, impresoras, televisores inteligentes, etc.',                 
                'created_at'      => Carbon::now(),
                'updated_at'      => Carbon::now()
            ],
              
        ]);
    }
}
