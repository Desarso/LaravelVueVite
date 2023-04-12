<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ProductionStatusTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('wh_production_status')->insert([
            [
                'id'              => 1,               
                'name'            => 'Pendiente',
                'description'     => null,
                'icon'            => 'fad fa-circle',
                'color'           => '#de5656',
                'created_at'      => Carbon::now(),
                'updated_at'      => Carbon::now()
            ],
            [
                'id'              => 2,                
                'name'            => 'En Progreso',
                'description'     => null,
                'icon'            => 'fal fa-cog faa-spin animated',
                'color'           => '#8ec63f',
                'created_at'      => Carbon::now(),
                'updated_at'      => Carbon::now()
            ],
           
            [
                'id'              => 3,                
                'name'            => 'Pausada',
                'description'     => null,
                'icon'            => 'fal fa-pause-circle',
                'color'           => '#6c757d',
                'created_at'      => Carbon::now(),
                'updated_at'      => Carbon::now()
            ],    
            [
                'id'              => 4,                
                'name'            => 'Finalizada',
                'description'     => null,
                'icon'            => 'fal fa-power-off',
                'color'           => '#6c757d',
                'created_at'      => Carbon::now(),
                'updated_at'      => Carbon::now()
            ]       
        ]);
    }
}
