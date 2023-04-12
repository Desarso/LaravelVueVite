<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CleaningStatusTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('wh_cleaning_status')->insert([
            [
                'id'              => 1,
                'name'            => 'Sucio',
                'color'           => 'white',
                'background'      => '#94999d',
                'icon'            => 'fas fa-disease',
                'created_at'      => Carbon::now(),
                'updated_at'      => Carbon::now()
            ],
            [
                'id'              => 2,
                'name'            => 'Limpiando',
                'color'           => 'white',
                'background'      => '#28c76f',
                'icon'            => 'fas fa-broom',
                'created_at'      => Carbon::now(),
                'updated_at'      => Carbon::now()
            ],
            [
                'id'              => 3,
                'name'            => 'Pausado',
                'color'           => 'white',
                'background'      => '#ffad4b',
                'icon'            => 'fas fa-pause',
                'created_at'      => Carbon::now(),
                'updated_at'      => Carbon::now()
            ],
            [
                'id'              => 4,
                'name'            => 'Limpio',
                'color'           => 'white',
                'background'      => '#00cfe8',
                'icon'            => 'fas fa-sparkles',
                'created_at'      => Carbon::now(),
                'updated_at'      => Carbon::now()
            ],
            [
                'id'              => 5,
                'name'            => 'Inspeccionado',
                'color'           => 'white',
                'background'      => '#7876fe',
                'icon'            => 'fas fa-check-double',
                'created_at'      => Carbon::now(),
                'updated_at'      => Carbon::now()
            ],
            [
                'id'              => 6,
                'name'            => 'No Limpiar',
                'color'           => 'white',
                'background'      => '#2E2E2E',
                'icon'            => 'fas fa-ban',
                'created_at'      => Carbon::now(),
                'updated_at'      => Carbon::now()
            ],
            [
                'id'              => 7,
                'name'            => 'Rush',
                'color'           => 'white',
                'background'      => '#CB183C',
                'icon'            => 'fas fa-running',
                'created_at'      => Carbon::now(),
                'updated_at'      => Carbon::now()
            ],
        ]);
    }
}