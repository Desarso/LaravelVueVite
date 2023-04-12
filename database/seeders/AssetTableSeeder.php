<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AssetTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('wh_asset')->insert([
            [
                'idcategory'      => 1,
                'idstatus'        => 1,
                'name'            => 'Montinor LG',                
                'code'            => '300218',
                'model'           => 'LG',
                'description'     => 'Equipo informático',
                'created_at'      => Carbon::now(),
                'updated_at'      => Carbon::now()
            ],
            [
                'idcategory'      => 1,
                'idstatus'        => 1,
                'name'            => 'Mouse Logitech',                
                'code'            => '30043218',
                'model'           => 'Logitech',
                'description'     => 'Equipo informático',
                'created_at'      => Carbon::now(),
                'updated_at'      => Carbon::now()
            ],
            [
                'idcategory'      => 1,
                'idstatus'        => 1,
                'name'            => 'Parlante JBL',                
                'code'            => '30025418',
                'model'           => 'JBL',
                'description'     => 'Equipo informático',
                'created_at'      => Carbon::now(),
                'updated_at'      => Carbon::now()
            ]      
        ]);
    }
}
