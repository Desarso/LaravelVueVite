<?php
namespace Database\Seeders;
use App\Models\Production\Production;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ProductionTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('wh_production')->insert([
            [
                'id'              => 1,               
                'idequipment'     => 1,
                'idproduct'       => 282,
                'idschedule'      => 1,
                'lot'             => 'PP2000901',
                'productionorder' => '55106',   
                'idpresentation'  => 1,
                'iddestination'   => 1,
                'productiongoal'  => 47340,
                'productiondate'  => Carbon::now(),              
                'created_at'      => Carbon::now(),
                'updated_at'      => Carbon::now()
            ],
            [
                'id'              => 2,               
                'idequipment'     => 2,
                'idproduct'       => 283,
                'idschedule'      => 1,
                'lot'             => 'PP2000902',
                'productionorder' => '5517',  
                'idpresentation'  => 1,
                'iddestination'   => 3,  
                'productiongoal'  => 47340,
                'productiondate'  => Carbon::now(),              
                'created_at'      => Carbon::now(),
                'updated_at'      => Carbon::now()
            ],
            [
                'id'              => 3,               
                'idequipment'     => 3,
                'idproduct'       => 284,
                'idschedule'      => 1,
                'lot'             => 'PP2000903',
                'productionorder' => '55103', 
                'idpresentation'  => 6,
                'iddestination'   => 1, 
                'productiongoal'  => 47340,
                'productiondate'  => Carbon::now(),                
                'created_at'      => Carbon::now(),
                'updated_at'      => Carbon::now()
            ],
            [
                'id'              => 4,               
                'idequipment'     => 4,
                'idproduct'       => 285,
                'idschedule'      => 1,
                'lot'             => 'PP2000904',
                'productionorder' => '55104',  
                'idpresentation'  => 1,
                'iddestination'   => 4,   
                'productiongoal'  => 47340,
                'productiondate'  => Carbon::now(),             
                'created_at'      => Carbon::now(),
                'updated_at'      => Carbon::now()
            ],
            [
                'id'              => 5,               
                'idequipment'     => 5,
                'idproduct'       => 286,
                'idschedule'      => 1,
                'lot'             => 'PP2000905',
                'productionorder' => '55105',  
                'idpresentation'  => 1,
                'iddestination'   => 3, 
                'productiongoal'  => 47340,
                'productiondate'  => Carbon::now(),               
                'created_at'      => Carbon::now(),
                'updated_at'      => Carbon::now()
            ],
            [
                'id'              => 6,               
                'idequipment'     => 6,
                'idproduct'       => 287,
                'idschedule'      => 1,
                'lot'             => 'PP2000966',
                'productionorder' => '55166', 
                'idpresentation'  => 22,
                'iddestination'   => 1,
                'productiongoal'  => 47340,
                'productiondate'  => Carbon::now(),                 
                'created_at'      => Carbon::now(),
                'updated_at'      => Carbon::now()
            ],
            [
                'id'              => 7,               
                'idequipment'     => 7,
                'idproduct'       => 288,
                'idschedule'      => 1,
                'lot'             => 'PP2000907',
                'productionorder' => '55107',  
                'idpresentation'  => 18,
                'iddestination'   => 3,
                'productiongoal'  => 47340,
                'productiondate'  => Carbon::now(),                
                'created_at'      => Carbon::now(),
                'updated_at'      => Carbon::now()
            ], [
                'id'              => 8,               
                'idequipment'     => 8,
                'idproduct'       => 289,
                'idschedule'      => 1,
                'lot'             => 'PP2000908',
                'productionorder' => '55108',  
                'idpresentation'  => 19,
                'iddestination'   => 3,
                'productiongoal'  => 47340,
                'productiondate'  => Carbon::now(),                
                'created_at'      => Carbon::now(),
                'updated_at'      => Carbon::now()
            ],
            [
                'id'              => 9,               
                'idequipment'     => 9,
                'idproduct'       => 290,
                'idschedule'      => 1,
                'lot'             => 'PP2000909',
                'productionorder' => '55109', 
                'idpresentation'  => 22,
                'iddestination'   => 1, 
                'productiongoal'  => 47340,
                'productiondate'  => Carbon::now(),                
                'created_at'      => Carbon::now(),
                'updated_at'      => Carbon::now()
            ],
            [
                'id'              => 10,               
                'idequipment'     => 10,
                'idproduct'       => 291,
                'idschedule'      => 1,
                'lot'             => 'PP2000910',
                'productionorder' => '551010',  
                'idpresentation'  => 16,
                'iddestination'   => 3, 
                'productiongoal'  => 47340,
                'productiondate'  => Carbon::now(),               
                'created_at'      => Carbon::now(),
                'updated_at'      => Carbon::now()
            ],
            [
                'id'              => 11,               
                'idequipment'     => 11,
                'idproduct'       => 292,
                'idschedule'      => 1,
                'lot'             => 'PP2000911',
                'productionorder' => '55111',  
                'idpresentation'  => 22,
                'iddestination'   => 1,  
                'productiongoal'  => 47340,
                'productiondate'  => Carbon::now(),              
                'created_at'      => Carbon::now(),
                'updated_at'      => Carbon::now()
            ],
            [
                'id'              => 12,               
                'idequipment'     => 12,
                'idproduct'       => 293,
                'idschedule'      => 1,
                'lot'             => 'PP2000912',
                'productionorder' => '55112', 
                'idpresentation'  => 18,
                'iddestination'   => 3, 
                'productiongoal'  => 47340,
                'productiondate'  => Carbon::now(),                
                'created_at'      => Carbon::now(),
                'updated_at'      => Carbon::now()
            ],
            [
                'id'              => 13,               
                'idequipment'     => 13,
                'idproduct'       => 294,
                'idschedule'      => 1,
                'lot'             => 'PP2000913',
                'productionorder' => '55113',  
                'idpresentation'  => 22,
                'iddestination'   => 1,
                'productiongoal'  => 47340,
                'productiondate'  => Carbon::now(),                
                'created_at'      => Carbon::now(),
                'updated_at'      => Carbon::now()
            ]
       ]);
       // factory(Production::class,1000)->create();
        
    }
}
