<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;


class WarehouseStatusTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('wh_warehouse_status')->insert([
            [
                'id'              => 1,                
                'name'            => 'Pendiente',
                'icon'            => null,
                'color'           => '#F4516C',
                'nextstatus'      => '[2,3]',
                'created_at'      => Carbon::now(),
                'updated_at'      => Carbon::now()
            ],
            [
                'id'              => 2,               
                'name'            => 'Recibida',
                'icon'            => null,
                'color'           => '#1AD6A2',
                'nextstatus'      => '[3,4]',
                'created_at'      => Carbon::now(),
                'updated_at'      => Carbon::now()
            ],
            [
                'id'              => 3,                
                'name'            => 'Rechazada',
                'icon'            => null,
                'color'           => '#FF9D27',
                'nextstatus'      => '[]',
                'created_at'      => Carbon::now(),
                'updated_at'      => Carbon::now()
            ],
            [
                'id'              => 4,                
                'name'            => 'Orden generada',
                'icon'            => null,
                'color'           => '#1EB82C',
                'nextstatus'      => '[3,5]',
                'created_at'      => Carbon::now(),
                'updated_at'      => Carbon::now()
            ],
            [
                'id'              => 5,                
                'name'            => 'Finalizada',
                'icon'            => null,
                'color'           => '#C5C5C5',
                'nextstatus'      => '[]',
                'created_at'      => Carbon::now(),
                'updated_at'      => Carbon::now()
            ]                
        ]);
    }
}
