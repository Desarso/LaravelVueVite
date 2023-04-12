<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ScheduleTypeSeeder extends Seeder
{
    public function run()
    {
        DB::table('wh_schedule_type')->insert([
            [
                'id'              => 1,               
                'name'            => 'Diurna', 
                'check_in'        => '05:00:00',   
                'check_out'       => '19:00:00',   
                'hours'           => 8,                           
                'created_at'      => Carbon::now(),
                'updated_at'      => Carbon::now()
            ],
            [
                'id'              => 2,               
                'name'            => 'Nocturna',    
                'check_in'        => '19:00:00',   
                'check_out'       => '05:00:00',   
                'hours'           => 6,                      
                'created_at'      => Carbon::now(),
                'updated_at'      => Carbon::now()
            ],
            [
                'id'              => 3,               
                'name'            => 'Mixta',      
                'check_in'        => '00:00:00',   
                'check_out'       => '00:00:00', 
                'hours'           => 7,                           
                'created_at'      => Carbon::now(),
                'updated_at'      => Carbon::now()
            ],
        ]);
    }
}
