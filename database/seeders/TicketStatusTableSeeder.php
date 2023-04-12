<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TicketStatusTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('wh_ticket_status')->insert([
            [
                'id'         => 1,
                'name'       => "Pendiente",
                'color'      => '#F4516C',
                'nextstatus' => '[2]'
            ],
            [
                'id'         => 2,
                'name'       => "En Progreso",
                'color'      => '#12c684',
                'nextstatus' => '[3,4]'
            ],
            [
                'id'         => 3,
                'name'       => "Pausado",
                'color'      => '#f6a213',
                'nextstatus' => '[2,4]'
            ],
            [
                'id'         => 4,
                'name'       => "Finalizado",
                'color'      => '#C4C5D6',
                'nextstatus' => '[]'
            ]
        ]);
    }
}
