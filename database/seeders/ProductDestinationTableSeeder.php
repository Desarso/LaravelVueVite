<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ProductDestinationTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('wh_product_destination')->insert([
            [
                'id'              => 1,
                'name'            => 'Bodega',
                'description'     => '',
                'created_at'      => Carbon::now(),
                'updated_at'      => Carbon::now()
            ],
            [
                'id'              => 2,
                'name'            => 'Envolvedora',
                'description'     => '',
                'created_at'      => Carbon::now(),
                'updated_at'      => Carbon::now()
            ],
            [
                'id'              => 3,
                'name'            => 'Exportación',
                'description'     => '',
                'created_at'      => Carbon::now(),
                'updated_at'      => Carbon::now()
            ],
            [
                'id'              => 4,
                'name'            => 'Walmart',
                'description'     => '',
                'created_at'      => Carbon::now(),
                'updated_at'      => Carbon::now()
            ]
        ]);
    }
}
