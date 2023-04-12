<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class EquipmentStatusTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('wh_equipment_status')->insert([
            [
                'id'              => 1,                
                'name'            => 'Apagado',
                'description'     => null,
                'icon'            => 'fad fa-power-off',
                'color'           => '#6c757d',
                'created_at'      => Carbon::now(),
                'updated_at'      => Carbon::now()
            ],
            [
                'id'              => 2,               
                'name'            => 'Trabajando',
                'description'     => null,
                'icon'            => 'fad fa-cog fa-spin animated',
                'color'           => '#8ec63f',
                'created_at'      => Carbon::now(),
                'updated_at'      => Carbon::now()
            ],
            [
                'id'              => 3,                
                'name'            => 'Detenido',
                'description'     => null,
                'icon'            => 'fad fa-wrench',
                'color'           => '#de5656',
                'created_at'      => Carbon::now(),
                'updated_at'      => Carbon::now()
            ]       
        ]);
    }
}
