<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AssetStatusTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('wh_asset_status')->insert([
            [
                'id'              => 1,
                'name'            => 'Funcional',   
                'color'           => '#28c76f',         
                'description'     => 'Equipos de montacargas',                
                'created_at'      => Carbon::now(),
                'updated_at'      => Carbon::now()
            ],
            [
                'id'              => 2,
                'name'            => 'En Reparación',                
                'description'     => 'Se está arreglando alguna avería',                 
                'color'           => '#ffad4b',
                'created_at'      => Carbon::now(),
                'updated_at'      => Carbon::now()
            ],
            [
                'id'              => 3,
                'name'            => 'En Mantenimiento',                                
                'description'     => 'Se le está dando mantenimiento',  
                'color'           => 'orange',               
                'created_at'      => Carbon::now(),
                'updated_at'      => Carbon::now()
            ],           
            [
                'id'              => 4,
                'name'            => 'Averiado',                
                'description'     => 'Tiene una avería. No significa necesariamente que no funciona',                 
                'color'           => '#CB183C',
                'created_at'      => Carbon::now(),
                'updated_at'      => Carbon::now()
            ],
             
              
        ]);
    }
}
