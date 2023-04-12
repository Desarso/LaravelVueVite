<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class BookingStatusTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('wh_booking_status')->insert([
            [
                'id'         => 1,
                'name'       => 'EXPECTED',
                'icon'       => 'fas fa-long-arrow-alt-right',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'id'         => 2,
                'name'       => 'NO-SHOW',
                'icon'       => 'fas fa-long-arrow-alt-right',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'id'         => 3,
                'name'       => 'CANCELLED',
                'icon'       => 'fas fa-long-arrow-alt-right',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            [
                'id'         => 4,
                'name'       => 'CHECKED-IN',
                'icon'       => 'fas fa-long-arrow-alt-right',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'id'         => 5,
                'name'       => 'CHECKED-OUT',
                'icon'       => 'fas fa-long-arrow-alt-left',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'id'         => 6,
                'name'       => 'TRANSFERED',
                'icon'       => 'fas fa-exchange-alt',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'id'         => 7,
                'name'       => 'OTHER',
                'icon'       => 'fas fa-exchange-alt',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]
        ]);
    }
}
