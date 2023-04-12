<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;


class ItemTableSeeder extends Seeder
{
    public function run()
    {
        DB::statement('SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO"');

        DB::table('wh_ticket_type')->insert([
            [
                'id'          => 1,
                'name'        => 'Avería',
                'idteam'      => 1,
                'icon'        => 'fas fa-exclamation-circle',
                'color'       => '#fd774d',
                'template'    => '{"template": [{"field": "spots", "attributes": [{"name": "tooltip", "value": "Se puede escoger más de un spot"}]}], "shortdescription": "Trabajos de Mantenimiento"}',
                'iscleaningtask' => 0,
                'created_at'  => Carbon::now(),
                'updated_at'  => Carbon::now()
            ], 
            [
                'id'          => 2,
                'name'        => 'Solicitud',
                'idteam'      => 2,
                'icon'        => 'fad fa-bell',
                'template'     => '{"width": 500, "template": [{"field": "quantity", "attributes": [{"name": "hidden", "value": false}, {"name": "position", "value": 2}]}, {"field": "description", "attributes": [{"name": "position", "value": 4}]}, {"field": "byclient", "attributes": [{"name": "position", "value": 3}, {"name": "label", "value": "Reported by Guest"}, {"name": "highlight", "value": true}, {"name": "tooltip", "value": "Favor indicar si la solicitud la realiza directamente un huésped del hotel"}]}], "shortdescription": "Solicitudes de huéspedes"}',
                'color'       => '#3CAEA3',
                'iscleaningtask' => 0,
                'created_at'  => Carbon::now(),
                'updated_at'  => Carbon::now()
            ] ,
            [
                'id'          => 3,
                'name'        => 'Limpieza',
                'idteam'      => 2,
                'icon'        => 'fad fa-broom',
                'template'    => null,
                'color'       => '#6146D9',
                'iscleaningtask' => 1,
                'created_at'  => Carbon::now(),
                'updated_at'  => Carbon::now()
            ] ,
            [
                'id'          => 4,
                'name'        => 'Trabajo',
                'idteam'      => 1,
                'icon'        => 'fad fa-tools',
                'template'    => '{"width": 450, "template": [{"field": "idasset", "attributes": [{"name": "hidden", "value": false}, {"name": "position", "value": 3}]}], "shortdescription": "Trabajo para Cliente"}',
                'color'       => '#20639B',
                'iscleaningtask' => 0,
                'created_at'  => Carbon::now(),
                'updated_at'  => Carbon::now()
            ] ,
            [
                'id'          => 5,
                'name'        => 'Contabilidad',
                'idteam'      => 1,
                'icon'        => 'fad fa-usd-circle',
                'template'    => '{"width":600,"template":[{"field":"approvers","attributes":[{"name":"hidden","value":false},{"name":"position","value":5}]},{"field":"byclient","attributes":[{"name":"hidden","value":true}]}],"shortdescription":"Tareas de Contabilidad"}',
                'color'       => '#82C91E',
                'iscleaningtask' => 0,
                'created_at'  => Carbon::now(),
                'updated_at'  => Carbon::now()
            ] ,
            [
                'id'          => 6,
                'name'        => 'Higiene & Desinfección',
                'idteam'      => 2,
                'icon'        => 'fad fa-pump-soap',
                'template'    => '{"width":500,"template":[{"field":"quantity","attributes":[{"name":"hidden","value":false},{"name":"highlight","value":true},{"name":"position","value":2},{"name":"tooltip","value":"Favor indicar la cantidd de producto que se ocupa!"}]},{"field":"byclient","attributes":[{"name":"hidden","value":true}]},{"field":"description","attributes":[{"name":"position","value":4}]},{"field":"duedate","attributes":[{"name":"position","value":7}]}],"shortdescription":"Productos de Limpieza y Desinfección"}',
                'color'       => '#1E4BFF',
                'iscleaningtask' => 1,
                'created_at'  => Carbon::now(),
                'updated_at'  => Carbon::now()
            ] ,
        ]);

        DB::table('wh_item')->insert([
            [
                'id'          => 1,
                'idtype'      => 1,
                'idteam'      => 1,
                'idpriority'  => 1,
                'name'        => 'A/C no funciona',
                'description' => '',
                'isglitch'    => true,  
                'idchecklist' => null,
                'isprivate'   => 'false',
                'enabled'     => true,
                'sla'         => 15,
                'users'       => '[]',
                'created_at'  => Carbon::now(),
                'updated_at'  => Carbon::now()
            ],
            [
                'id'          => 2,
                'idtype'      => 1,
                'idteam'      => 1,
                'idpriority'  => 1,
                'name'        => 'Bombillo quemado',
                'description' => '',
                'isglitch'    => true,  
                'idchecklist' => null,
                'isprivate'   => 'false',
                'enabled'     => false,
                'sla'         => null,
                'users'       => '[]',
                'created_at'  => Carbon::now(),
                'updated_at'  => Carbon::now()
            ],
            [
                'id'          => 3,
                'idtype'      => 1,
                'idteam'      => 1,
                'idpriority'  => 1,
                'name'        => 'Abanico no enciende',
                'isglitch'    => true,  
                'description' => '',
                'idchecklist' => null,
                'isprivate'   => 'false',
                'enabled'     => true,
                'sla'         => null,
                'users'       => '[]',
                'created_at'  => Carbon::now(),
                'updated_at'  => Carbon::now()
            ],
            [
                'id'          => 4,
                'idtype'      => 2,
                'idteam'      => 2,
                'idpriority'  => 1,
                'name'        => 'Paños para piscina',
                'description' => '',
                'isglitch'    => false,  
                'idchecklist' => null,
                'isprivate'   => 'false',
                'enabled'     => true,
                'sla'         => 15,
                'users'       => '[]',
                'created_at'  => Carbon::now(),
                'updated_at'  => Carbon::now()
            ],
            [
                'id'          => 5,
                'idtype'      => 4,
                'idteam'      => 1,
                'idpriority'  => 1,
                'name'        => 'Trabajo de Montacargas',
                'description' => '',
                'isglitch'    => false,  
                'idchecklist' => null,
                'isprivate'   => 'false',
                'enabled'     => true,
                'sla'         => null,
                'users'       => '[]',
                'created_at'  => Carbon::now(),
                'updated_at'  => Carbon::now()
            ],
            [
                'id'          => 6,
                'idtype'      => 5,
                'idteam'      => 1,
                'idpriority'  => 1,
                'name'        => 'Nueva Contratación',
                'description' => 'Nuevos puestos',
                'isglitch'    => false,  
                'idchecklist' => null,
                'isprivate'   => '1',
                'enabled'     => true,
                'sla'         => null,
                'users'       => '[]',
                'created_at'  => Carbon::now(),
                'updated_at'  => Carbon::now()
            ],
            [
                'id'          => 7,
                'idtype'      => 1,
                'idteam'      => 1,
                'idpriority'  => 1,
                'name'        => 'Mantenimiento A/C',
                'description' => '',
                'isglitch'    => false,  
                'idchecklist' => 1,
                'isprivate'   => 'false',
                'enabled'     => true,
                'sla'         => 15,
                'users'       => '[]',
                'created_at'  => Carbon::now(),
                'updated_at'  => Carbon::now()
            ],
            [
                'id'          => 8,
                'idtype'      => 3,
                'idteam'      => 2,
                'idpriority'  => 1,
                'name'        => 'Limpieza',
                'description' => '',
                'isglitch'    => false,  
                'idchecklist' => null,
                'isprivate'   => 'false',
                'enabled'     => true,
                'sla'         => null,
                'users'       => '[]',
                'created_at'  => Carbon::now(),
                'updated_at'  => Carbon::now()
            ],
            [
                'id'          => 9,
                'idtype'      => 3,
                'idteam'      => 2,
                'idpriority'  => 1,
                'name'        => 'Limpieza Profunda',
                'description' => '',
                'isglitch'    => false,  
                'idchecklist' => null,
                'isprivate'   => 'false',
                'enabled'     => true,
                'sla'         => null,
                'users'       => '[]',
                'created_at'  => Carbon::now(),
                'updated_at'  => Carbon::now()
            ],

            [
                'id'          => 10,
                'idtype'      => 6,
                'idteam'      => 2,
                'idpriority'  => 1,
                'name'        => 'Alcohol en gel',
                'description' => 'Mínimo 70% de alcohol',
                'isglitch'    => true,    
                'idchecklist' => null,
                'isprivate'   => 'false',
                'enabled'     => true,
                'sla'         => null,
                'users'       => '[]',
                'created_at'  => Carbon::now(),
                'updated_at'  => Carbon::now()
            ],

            [
                'id'          => 11,
                'idtype'      => 6,
                'idteam'      => 2,
                'idpriority'  => 1,
                'name'        => 'Caretas',
                'description' => '',
                'isglitch'    => true,    
                'idchecklist' => null,
                'isprivate'   => 'false',
                'enabled'     => true,
                'sla'         => null,
                'users'       => '[]',
                'created_at'  => Carbon::now(),
                'updated_at'  => Carbon::now()
            ],

            [
                'id'          => 12,
                'idtype'      => 6,
                'idteam'      => 2,
                'idpriority'  => 1,
                'name'        => 'Detergente',
                'description' => 'El autorizado',
                'isglitch'    => true,    
                'idchecklist' => null,
                'isprivate'   => 'false',
                'enabled'     => true,
                'sla'         => null,
                'users'       => '[]',
                'created_at'  => Carbon::now(),
                'updated_at'  => Carbon::now()
            ],
            [
                'id'          => 13,
                'idtype'      => 6,
                'idteam'      => 2,
                'idpriority'  => 1,
                'name'        => 'Guantes desechables',
                'description' => '',
                'isglitch'    => true,    
                'idchecklist' => null,
                'isprivate'   => 'false',
                'enabled'     => true,
                'sla'         => null,
                'users'       => '[]',
                'created_at'  => Carbon::now(),
                'updated_at'  => Carbon::now()
            ],
            [
                'id'          => 14,
                'idtype'      => 6,
                'idteam'      => 2,
                'idpriority'  => 1,
                'name'        => 'Jabón antibacterial',
                'description' => '',
                'isglitch'    => true,    
                'idchecklist' => null,
                'isprivate'   => 'false',
                'enabled'     => true,
                'sla'         => null,
                'users'       => '[]',
                'created_at'  => Carbon::now(),
                'updated_at'  => Carbon::now()
            ],
            [
                'id'          => 15,
                'idtype'      => 6,
                'idteam'      => 2,
                'idpriority'  => 1,
                'name'        => 'Lentes (protectores de ojos)',
                'description' => '',
                'isglitch'    => true,    
                'idchecklist' => null,
                'isprivate'   => 'false',
                'enabled'     => true,
                'sla'         => null,
                'users'       => '[]',
                'created_at'  => Carbon::now(),
                'updated_at'  => Carbon::now()
            ],
            [
                'id'          => 16,
                'idtype'      => 6,
                'idteam'      => 2,
                'idpriority'  => 1,
                'name'        => 'Papel higiénico',
                'description' => '',
                'isglitch'    => true,    
                'idchecklist' => null,
                'isprivate'   => 'false',
                'enabled'     => true,
                'sla'         => null,
                'users'       => '[]',
                'created_at'  => Carbon::now(),
                'updated_at'  => Carbon::now()
            ],
            [
                'id'          => 17,
                'idtype'      => 6,
                'idteam'      => 2,
                'idpriority'  => 1,
                'name'        => 'Solución desinfectante',
                'description' => 'Mínimo 3800 ppm de hipoclorito',
                'isglitch'    => true,    
                'idchecklist' => null,
                'isprivate'   => 'false',
                'enabled'     => true,
                'sla'         => null,
                'users'       => '[]',
                'created_at'  => Carbon::now(),
                'updated_at'  => Carbon::now()
            ],
            [
                'id'          => 18,
                'idtype'      => 6,
                'idteam'      => 2,
                'idpriority'  => 1,
                'name'        => 'Tapabocas desechables',
                'description' => '',
                'isglitch'    => true,    
                'idchecklist' => null,
                'isprivate'   => 'false',
                'enabled'     => true,
                'sla'         => null,
                'users'       => '[]',
                'created_at'  => Carbon::now(),
                'updated_at'  => Carbon::now()
            ],
            [
                'id'          => 19,
                'idtype'      => 6,
                'idteam'      => 2,
                'idpriority'  => 1,
                'name'        => 'Toallas de papel',
                'description' => 'Para secado de manos',
                'isglitch'    => true,    
                'idchecklist' => null,
                'isprivate'   => 'false',
                'enabled'     => true,
                'sla'         => null,
                'users'       => '[]',
                'created_at'  => Carbon::now(),
                'updated_at'  => Carbon::now()
            ],
            [
                'id'          => 20,
                'idtype'      => 6,
                'idteam'      => 2,
                'idpriority'  => 1,
                'name'        => 'Toallas para limpieza de superficies',
                'description' => 'Lavables pero desechables',
                'isglitch'    => true,    
                'idchecklist' => null,
                'isprivate'   => 'false',
                'enabled'     => true,
                'sla'         => null,
                'users'       => '[]',
                'created_at'  => Carbon::now(),
                'updated_at'  => Carbon::now()
            ],
        ]);
    }
}
