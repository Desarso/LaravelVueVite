<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ProductCategoryTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('wh_product_category')->insert([
            [
                'id'              => 1,
                'name'            => 'Desnudo',
                'description'     => '',
                'created_at'      => Carbon::now(),
                'updated_at'      => Carbon::now()
            ],
            [
                'id'              => 2,
                'name'            => 'Hermético',
                'description'     => '',
                'created_at'      => Carbon::now(),
                'updated_at'      => Carbon::now()
            ]
             
        ]);
    }
}
