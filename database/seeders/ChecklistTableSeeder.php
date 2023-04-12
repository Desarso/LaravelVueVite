<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ChecklistTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('wh_checklist')->insert([
            [
                'id'              => 1,
                'name'            => 'Mantenimiento A/C',
                'created_at'      => Carbon::now(),
                'updated_at'      => Carbon::now()
            ]           
        ]);

        DB::table('wh_checklist_data')->insert([
            [
                'id'              => 1,
                'name'            => 'Bueno/Malo/Regular',
                'data'            => '[{"value":1, "text":"Bueno"}, {"value":2, "text":"Regular"}, {"value":3, "text":"Malo"}]',
                'created_at'      => Carbon::now(),
                'updated_at'      => Carbon::now()
            ],   
            [
                'id'              => 2,
                'name'            => 'Si/No/NA',
                'data'            => '[{"value":1, "text":"Si"}, {"value":0, "text":"No"}, {"value":2, "text":"N/A"}]',
                'created_at'      => Carbon::now(),
                'updated_at'      => Carbon::now()
            ]          
        ]);

        DB::table('wh_checklist_option')->insert([
            [
                'id'          => 1,
                'idchecklist' => 1,
                'name'        => 'General',
                'optiontype'  => 6,
                'iddata'      => null,
                'position'    => 0,
                'group'       => 0,
                'created_at'  => Carbon::now(),
                'updated_at'  => Carbon::now()
            ],
            [
                'id'          => 2,
                'idchecklist' => 1,
                'name'        => 'Filtros limpios',
                'optiontype'  => 1,
                'iddata'      => null,
                'position'    => 0,
                'group'       => 0,
                'created_at'  => Carbon::now(),
                'updated_at'  => Carbon::now()
            ],
            [
                'id'          => 3,
                'idchecklist' => 1,
                'name'        => 'Soportes en buen estado',
                'optiontype'  => 2,
                'iddata'      => 1,
                'position'    => 0,
                'group'       => 0,
                'created_at'  => Carbon::now(),
                'updated_at'  => Carbon::now()
            ],
            [
                'id'          => 4,
                'idchecklist' => 1,
                'name'        => 'Temperatura',
                'optiontype'  => 4,
                'iddata'      => null,
                'position'    => 0,
                'group'       => 0,
                'created_at'  => Carbon::now(),
                'updated_at'  => Carbon::now()
            ],
            [
                'id'          => 5,
                'idchecklist' => 1,
                'name'        => 'Otros',
                'optiontype'  => 6,
                'iddata'      => null,
                'position'    => 0,
                'group'       => 0,
                'created_at'  => Carbon::now(),
                'updated_at'  => Carbon::now()
            ],
            [
                'id'          => 6,
                'idchecklist' => 1,
                'name'        => 'Estado del A/C',
                'optiontype'  => 5,
                'iddata'      => 1,
                'position'    => 0,
                'group'       => 0,
                'created_at'  => Carbon::now(),
                'updated_at'  => Carbon::now()
            ],   
            [
                'id'          => 7,
                'idchecklist' => 1,
                'name'        => 'Comentario de revisión',
                'optiontype'  => 3,
                'iddata'      => null,
                'position'    => 0,
                'group'       => 0,
                'created_at'  => Carbon::now(),
                'updated_at'  => Carbon::now()
            ]       
        ]);
    }
}
