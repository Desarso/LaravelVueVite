<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class EquipmentTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('wh_equipment')->insert([
            [
                'id'                => 1,                
                'name'              => 'Maisa-1',
                'description'       => null,
                'idtype'            => 1,
                'idstatus'          => 1, // Apagada
                'idproductcategory' => 1,
                'velocity'          => 120,
                'warmup_duration'   => 15,
                'cleaning_duration' => 5,   // Consider más adelante que los viernes la limpieza es profunda y toma más tiempo                
                'created_at'        => Carbon::now(),
                'updated_at'        => Carbon::now()
            ],
            [
                'id'                => 2,                
                'name'              => 'Maisa-2',
                'description'       => null,
                'idtype'            => 1,
                'idstatus'          => 1, // Apagada
                'idproductcategory' => 1,
                'velocity'          => 120,
                'warmup_duration'   => 15,
                'cleaning_duration' => 5,   // Consider más adelante que los viernes la limpieza es profunda y toma más tiempo                
                'created_at'        => Carbon::now(),
                'updated_at'        => Carbon::now()
            ],
            [
                'id'                => 3,                
                'name'              => 'Maisa-3',
                'description'       => null,
                'idtype'            => 1,
                'idstatus'          => 1, // Apagada
                'idproductcategory' => 1,
                'velocity'          => 120,
                'warmup_duration'   => 15,
                'cleaning_duration' => 5,   // Consider más adelante que los viernes la limpieza es profunda y toma más tiempo              
                'created_at'        => Carbon::now(),
                'updated_at'        => Carbon::now()
            ],
            [
                'id'                => 4,                
                'name'              => 'Maisa-4',
                'description'       => null,
                'idtype'            => 1,
                'idstatus'          => 1, // Apagada
                'idproductcategory' => 2,
                'velocity'          => 120,
                'warmup_duration'   => 15,
                'cleaning_duration' => 5,   // Consider más adelante que los viernes la limpieza es profunda y toma más tiempo                
                'created_at'        => Carbon::now(),
                'updated_at'        => Carbon::now()
            ],
            [
                'id'                => 5,                
                'name'              => 'Maisa-5',
                'description'       => null,
                'idtype'            => 1,
                'idstatus'          => 1, // Apagada
                'idproductcategory' => 2,
                'velocity'          => 120,
                'warmup_duration'   => 15,
                'cleaning_duration' => 5,   // Consider más adelante que los viernes la limpieza es profunda y toma más tiempo                
                'created_at'        => Carbon::now(),
                'updated_at'        => Carbon::now()
            ],
            [
                'id'                => 6,                
                'name'              => 'Maisa-6',
                'description'       => null,
                'idtype'            => 1,
                'idstatus'          => 1, // Apagada
                'idproductcategory' => 2,
                'velocity'          => 120,
                'warmup_duration'   => 15,
                'cleaning_duration' => 5,   // Consider más adelante que los viernes la limpieza es profunda y toma más tiempo                
                'created_at'        => Carbon::now(),
                'updated_at'        => Carbon::now()
            ],
            [
                'id'                => 7,                
                'name'              => 'Maisa-7',
                'description'       => null,
                'idtype'            => 1,
                'idstatus'          => 1, // Apagada
                'idproductcategory' => 2,
                'velocity'          => 120,
                'warmup_duration'   => 15,
                'cleaning_duration' => 5,   // Consider más adelante que los viernes la limpieza es profunda y toma más tiempo
               
                'created_at'        => Carbon::now(),
                'updated_at'        => Carbon::now()
            ],
            [
                'id'                => 8,                
                'name'              => 'Maisa-8',
                'description'       => null,
                'idtype'            => 1,
                'idstatus'          => 1, // Apagada                
                'idproductcategory' => 2,
                'velocity'          => 120,
                'warmup_duration'   => 15,
                'cleaning_duration' => 5,   // Consider más adelante que los viernes la limpieza es profunda y toma más tiempo
               
                'created_at'        => Carbon::now(),
                'updated_at'        => Carbon::now()
            ],
            [
                'id'                => 9,                
                'name'              => 'Maisa-9',
                'description'       => null,
                'idtype'            => 1,
                'idstatus'          => 1, // Apagada
                'idproductcategory' => 2,
                'velocity'          => 120,
                'warmup_duration'   => 15,
                'cleaning_duration' => 5,   // Consider más adelante que los viernes la limpieza es profunda y toma más tiempo
                
                'created_at'        => Carbon::now(),
                'updated_at'        => Carbon::now()
            ],
            [
                'id'                => 10,                
                'name'              => 'Maisa-10',
                'description'       => null,
                'idtype'            => 1,
                'idstatus'          => 1, // Apagada
                'idproductcategory' => 2,
                'velocity'          => 120,
                'warmup_duration'   => 15,
                'cleaning_duration' => 5,   // Consider más adelante que los viernes la limpieza es profunda y toma más tiempo
                
                'created_at'        => Carbon::now(),
                'updated_at'        => Carbon::now()
            ],
            [
                'id'                => 11,                
                'name'              => 'Maisa-11',
                'description'       => null,
                'idtype'            => 1,
                'idstatus'          => 1, // Apagada
                'idproductcategory' => 1,
                'velocity'          => 120,
                'warmup_duration'   => 15,
                'cleaning_duration' => 5,   // Consider más adelante que los viernes la limpieza es profunda y toma más tiempo
               
                'created_at'        => Carbon::now(),
                'updated_at'        => Carbon::now()
            ],
            [
                'id'                => 12,                
                'name'              => 'Maisa-12',
                'description'       => null,
                'idtype'            => 1,
                'idstatus'          => 1, // Apagada
                'idproductcategory' => 2,
                'velocity'          => 120,
                'warmup_duration'   => 15,
                'cleaning_duration' => 5,   // Consider más adelante que los viernes la limpieza es profunda y toma más tiempo
                
                'created_at'        => Carbon::now(),
                'updated_at'        => Carbon::now()
            ],
            [
                'id'                => 13,                
                'name'              => 'Maisa-13',
                'description'       => null,
                'idtype'            => 1,
                'idstatus'          => 1, // Apagada
                'idproductcategory' => 2,
                'velocity'          => 120,
                'warmup_duration'   => 15,
                'cleaning_duration' => 5,   // Consider más adelante que los viernes la limpieza es profunda y toma más tiempo
               
                'created_at'        => Carbon::now(),
                'updated_at'        => Carbon::now()
            ],
            [
                'id'                => 14,                
                'name'              => 'Empacadora-1',
                'description'       => null,
                'idtype'            => 2,
                'idstatus'          => 1, // Apagada
                'idproductcategory' => null,
                'velocity'          => 120,
                'warmup_duration'   => 10,
                'cleaning_duration' => 10,   // Consider más adelante que los viernes la limpieza es profunda y toma más tiempo
                
                'created_at'        => Carbon::now(),
                'updated_at'        => Carbon::now()
            ],
            [
                'id'                => 15,                
                'name'              => 'Empacadora-2',
                'description'       => null,
                'idtype'            => 2,
                'idstatus'          => 1, // Apagada
                'idproductcategory' => null,
                'velocity'          => 120,
                'warmup_duration'   => 10,
                'cleaning_duration' => 10,   // Consider más adelante que los viernes la limpieza es profunda y toma más tiempo
                
                'created_at'        => Carbon::now(),
                'updated_at'        => Carbon::now()
            ],
            
        ]);
    }
}
