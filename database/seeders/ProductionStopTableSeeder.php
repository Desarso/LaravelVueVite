<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ProductionStopTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('wh_production_stop')->insert([
            [
                'id'              => 1,               
                'name'            => 'P-Cambio de papel',                
                'idtype'          => 1,                
                'idteam'           => 5,                
                'created_at'      => Carbon::now(),
                'updated_at'      => Carbon::now()
            ],
            [
                'id'              => 2,               
                'name'            => 'P-Cambio de etiquetas',                
                'idtype'          => 1,                
                'idteam'           => 5,                
                'created_at'      => Carbon::now(),
                'updated_at'      => Carbon::now()
            ],
            [
                'id'              => 3,               
                'name'            => 'P-Cambio de hilo',                
                'idtype'          => 1,                
                'idteam'           => 5,                
                'created_at'      => Carbon::now(),
                'updated_at'      => Carbon::now()
            ],
            [
                'id'              => 4,               
                'name'            => 'M-Falla mecánica',                
                'idtype'          => 1,                
                'idteam'           => 3,                
                'created_at'      => Carbon::now(),
                'updated_at'      => Carbon::now()
            ],
            [
                'id'              => 5,               
                'name'            => 'NP-Sin producto',                
                'idtype'          => 1,                
                'idteam'           => 4,                
                'created_at'      => Carbon::now(),
                'updated_at'      => Carbon::now()
            ],
            [
                'id'              => 6,               
                'name'            => 'NP-Sin hojas de peso',         
                'idtype'          => 1,                                       
                'idteam'           => 4,                               
                'created_at'      => Carbon::now(),
                'updated_at'      => Carbon::now()
            ],
            [
                'id'              => 7,               
                'name'            => 'M-Ajuste de troquelador',       
                'idtype'          => 1,                                         
                'idteam'           => 3,                                
                'created_at'      => Carbon::now(),
                'updated_at'      => Carbon::now()
            ],
            [
                'id'              => 8,               
                'name'            => 'M-Ajuste de sensor',                                
                'idtype'          => 1,                
                'idteam'           => 3,                                
                'created_at'      => Carbon::now(),
                'updated_at'      => Carbon::now()
            ],
            [
                'id'              => 9,               
                'name'            => 'M-Ajuste de taco',    
                'idtype'          => 1,                                            
                'idteam'           => 3,                                
                'created_at'      => Carbon::now(),
                'updated_at'      => Carbon::now()
            ],
            [
                'id'              => 10,               
                'name'            => 'M-Falla Marcadora',       
                'idtype'          => 1,                         
                'idteam'           => 3,                                
                'created_at'      => Carbon::now(),
                'updated_at'      => Carbon::now()
            ],
             
        ]);
    }
}
