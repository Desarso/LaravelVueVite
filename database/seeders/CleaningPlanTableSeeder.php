<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CleaningPlanTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('wh_cleaning_plan')->insert([
            [
                'id'              => 1,
                'date'            => '2020-02-17',
                'idresource'      => 1,
                'idspot'          => 0,
                'cleanat'         => '14:24:21',
                'idcleaningstatus'=> 1,
                'idcleaningtype'  => 1,
                'idticket'        => 0
            ],
            [
                'id'              => 2,
                'date'            => '2020-02-17',
                'idresource'      => 1,
                'idspot'          => 1,
                'cleanat'         => '14:24:21',
                'idcleaningstatus'=> 2,
                'idcleaningtype'  => 1,
                'idticket'        => 0
            ],
            [
                'id'              => 3,
                'date'            => '2020-02-17',
                'idresource'      => 1,
                'idspot'          => 2,
                'cleanat'         => '14:24:21',
                'idcleaningstatus'=> 3,
                'idcleaningtype'  => 1,
                'idticket'        => 0
            ],
            [
                'id'              => 4,
                'date'            => '2020-02-17',
                'idresource'      => 1,
                'idspot'          => 1,
                'cleanat'         => '14:24:21',
                'idcleaningstatus'=> 4,
                'idcleaningtype'  => 1,
                'idticket'        => 0
            ]
        ]);
    }
}
