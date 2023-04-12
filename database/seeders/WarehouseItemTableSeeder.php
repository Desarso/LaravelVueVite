<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class WarehouseItemTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('wh_warehouse_category')->insert([
            [
                'id'              => 1,                
                'name'            => 'Normal',
                'created_at'      => Carbon::now(),
                'updated_at'      => Carbon::now()
            ]             
        ]);

        DB::table('wh_warehouse_item')->insert([
            [
                'id'              => 1,    
                'idcategory'      => 1,           
                'name'            => 'Producto 1',
                'created_at'      => Carbon::now(),
                'updated_at'      => Carbon::now()
            ],
            [
                'id'              => 2,    
                'idcategory'      => 1,           
                'name'            => 'Producto 2',
                'created_at'      => Carbon::now(),
                'updated_at'      => Carbon::now()
            ],
            [
                'id'              => 3,    
                'idcategory'      => 1,           
                'name'            => 'Producto 3',
                'created_at'      => Carbon::now(),
                'updated_at'      => Carbon::now()
            ]             
        ]);
    }
}
