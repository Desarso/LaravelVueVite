<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class EquipmentTypeTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('wh_equipment_type')->insert([
            [
                'id'              => 1,
                'name'            => 'Máquina',
                'description'     => 'Las Maisas',
                'destinations'    => '[1, 2, 3, 4]',
                'created_at'      => Carbon::now(),
                'updated_at'      => Carbon::now()
            ],
            [
                'id'              => 2,
                'name'            => 'Envolvedora',
                'description'     => '',
                'destinations'    => '[1, 2, 3, 4]',
                'created_at'      => Carbon::now(),
                'updated_at'      => Carbon::now()
            ]             
        ]);
    }
}
