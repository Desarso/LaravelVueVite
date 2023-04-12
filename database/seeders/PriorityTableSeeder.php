<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PriorityTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('wh_priority')->insert([
            [
                'id'              => 1,
                'name'            => 'Baja',
                'color'           => '#575962',
                'created_at'      => Carbon::now(),
                'updated_at'      => Carbon::now()
            ],
            [
                'id'              => 2,
                'name'            => 'Media',
                'color'           => '#575962',
                'created_at'      => Carbon::now(),
                'updated_at'      => Carbon::now()
            ],
            [
                'id'              => 3,
                'name'            => 'Alta',
                'color'           => 'white',
                'created_at'      => Carbon::now(),
                'updated_at'      => Carbon::now()
            ],
            [
                'id'              => 4,
                'name'            => 'Por cliente',
                'color'           => 'white',
                'created_at'      => Carbon::now(),
                'updated_at'      => Carbon::now()
            ]
        ]);
    }
}
