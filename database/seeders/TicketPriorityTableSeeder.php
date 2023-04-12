<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TicketPriorityTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('wh_ticket_priority')->insert([
            [
                'id'              => 1,
                'name'            => 'Baja',
                'color'           => '#12c684',
                'sla'             => 0,
                'created_at'      => Carbon::now(),
                'updated_at'      => Carbon::now()
            ],
            [
                'id'              => 2,
                'name'            => 'Media',
                'color'           => '#f6a213',
                'sla'             => 0,
                'created_at'      => Carbon::now(),
                'updated_at'      => Carbon::now()
            ],
            [
                'id'              => 3,
                'name'            => 'Alta',
                'color'           => '#ec616a',
                'sla'             => 0,
                'created_at'      => Carbon::now(),
                'updated_at'      => Carbon::now()
            ],
            [
                'id'              => 4,
                'name'            => 'Por cliente',
                'color'           => '#9261EC',
                'sla'             => 0,
                'created_at'      => Carbon::now(),
                'updated_at'      => Carbon::now()
            ]
        ]);
    }
}
