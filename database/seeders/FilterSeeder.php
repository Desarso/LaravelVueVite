<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class FilterSeeder extends Seeder
{
    public function run()
    {
        DB::table('wh_filter')->insert([
            [
                'name'       => 'Mis tareas',            
                'data'       => '{"logic": "and", "filters": [{"field": "iduser", "value": "?", "operator": "eq"}]}',                
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name'       => 'Reportados por mí',            
                'data'       => '{"logic": "and", "filters": [{"field": "created_by", "value": "?", "operator": "eq"}]}',                
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
        ]);
    }
}
