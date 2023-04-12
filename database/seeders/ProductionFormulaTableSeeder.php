<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ProductionFormulaTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('wh_production_formula')->insert([
            [
                'id'              => 1,
                'name'            => 'Fórmula Desnudo',
                'description'     => '',                                
                'inputs'          => '[1, 2, 3]',                
                'created_at'      => Carbon::now(),
                'updated_at'      => Carbon::now()
            ],
            [
                'id'              => 2,
                'name'            => 'Fórmula Hermético',
                'description'     => '',                                
                'inputs'          => '[1, 2, 3, 4]',                
                'created_at'      => Carbon::now(),
                'updated_at'      => Carbon::now()
            ],
            [
                'id'              => 3,
                'name'            => 'Fórmula Manzanilla B25',
                'description'     => '',                                
                'inputs'          => '[2, 3]',                
                'created_at'      => Carbon::now(),
                'updated_at'      => Carbon::now()
            ],
            
        ]);
    }
}
