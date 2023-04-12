<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ProtocolTypeTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('wh_protocol_type')->insert([
            [
                'id'              => 1,
                'name'            => 'Habitaciones',     
                'description'     => '',                             
                'created_at'      => Carbon::now(),
                'updated_at'      => Carbon::now()
            ],
            [
                'id'              => 2,
                'name'            => 'Áreas comunes',          
                'description'     => '',                          
                'created_at'      => Carbon::now(),
                'updated_at'      => Carbon::now()
            ],
            [
                'id'              => 3,
                'name'            => 'Limpieza',          
                'description'     => '',                          
                'created_at'      => Carbon::now(),
                'updated_at'      => Carbon::now()
            ],
            [
                'id'              => 4,
                'name'            => 'Higiene y Cuidado Personal',
                'description'     => '',               
                'created_at'      => Carbon::now(),
                'updated_at'      => Carbon::now()
            ],
            [
                'id'              => 5,
                'name'            => 'EPP',                  
                'description'     => 'Equipo de protección personal',
                'created_at'      => Carbon::now(),
                'updated_at'      => Carbon::now()
            ],
            [
                'id'              => 6,
                'name'            => 'Protocolos para uso de áreas',                  
                'description'     => '',
                'created_at'      => Carbon::now(),
                'updated_at'      => Carbon::now()
            ],
            
            
        ]);
    }
}
