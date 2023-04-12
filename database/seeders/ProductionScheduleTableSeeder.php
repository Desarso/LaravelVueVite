<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ProductionScheduleTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('wh_production_schedule')->insert([
            [
                'id'              => 1,
                'name'            => 'Horario de Día',
                'description'     => 'Se trabaja de 6am a 2pm, Lunes a Domingo',                
                'duration'        => '8',  // horas
                'dow'             => '[1,2,3,4,5,6,7]',
                'breaks'          => '[1, 2, 4, 5, 6]',
                'created_at'      => Carbon::now(),
                'updated_at'      => Carbon::now()
            ],
            [
                'id'              => 2,
                'name'            => 'Horario de Tarde',
                'description'     => 'Se trabaja de 2pm a 10pm, Lunes a Sábado',                 
                'duration'        => '8',  // horas      
                'dow'             => '[1,2,3,4,5,6]',
                'breaks'          => '[4, 3, 5, 6]'  ,
                'created_at'      => Carbon::now(),
                'updated_at'      => Carbon::now()
            ],         
            [
                'id'              => 3,
                'name'            => 'Horario de Noche',
                'description'     => 'Se trabaja de 2pm a 10pm, Lunes a Viernes',                       
                'duration'        => '8',  // horas
                'dow'             => '[1,2,3,4,5]',
                'breaks'          => '[3, 4, 5]',
                'created_at'      => Carbon::now(),
                'updated_at'      => Carbon::now()
            ],         
        ]);
    }
}
