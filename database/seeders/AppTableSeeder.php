<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AppTableSeeder extends Seeder
{
    public function run()
    {

        DB::table('wh_app')->insert([
            [
                'id' => 1,
                'name'            => 'Evaluaciones',                
                'description'     => 'Evalue el rendimiento de sus colaboradores por medio de checklist personalizados.',
                'icon'            => 'fad fa-user-check',                
                'color'           => '#24F29B',
                'url'             => 'approvals',
                'route_app'       => 'userEvaluation',
                'enabled'         => true,
                'settings'        => '{"default_item": 1000, "default_spot": 1}',
                'created_at'      => Carbon::now(),
                'updated_at'      => Carbon::now()
            ],
            [
                'id'              => 2,
                'name'            => 'Activos',                
                'description'     => 'Mantega la historial de instalaciones, reparaciones y mantenimientos de sus activos.',
                'icon'            => 'fad fa-forklift',
                'color'           => '#df614c',
                'url'             => 'assets',
                'route_app'       => 'assets',
                'enabled'         => true,
                'settings'        => '{"default_user": 1}',
                'created_at'      => Carbon::now(),
                'updated_at'      => Carbon::now()
            ],
            [
                'id'              => 3,
                'name'            => 'Asistencia & GPS',                
                'description'     => 'Conozca la ubicación geográfica real de recursos, activos, maquinaria y medios de transporte.',
                'url'             => 'attendance',
                'route_app'       => 'attendance',
                'icon'            => 'fad fa-map-marker-alt',
                'color'           => '#65a7e5',
                'enabled'         => true,
                'settings'        => null,
                'created_at'      => Carbon::now(),
                'updated_at'      => Carbon::now()
            ],
            [
                'id'              => 4,
                'name'            => 'Limpieza',                
                'description'     => 'Controle las limpiezas de su organización y genere bitácoras de manera automática',
                'icon'            => 'fad fa-broom',
                'color'           => '#f09950',
                'url'             => 'cleaning',
                'route_app'       => 'cleaningPage',
                'enabled'         => true,
                'settings'        => '{"cleaning_teams": [2], "cleaning_products": [6], "cleaning_ticket_type": [3], "default_cleaning_item": 9}',
                'created_at'      => Carbon::now(),
                'updated_at'      => Carbon::now()
            ],
            [
                'id'              => 5,
                'name'            => 'Protocolos',                
                'description'     => 'Comparta los protocolos basicos higiene con todos los colaboradores de la empresa.',
                'icon'            => 'fad fa-shield-virus',
                'color'           => '#f2af3d',
                'url'             => 'protocols',
                'route_app'       => 'protocollist',
                'enabled'         => true,
                'settings'        => null,
                'created_at'      => Carbon::now(),
                'updated_at'      => Carbon::now()
            ],
            [
                'id'              => 6,
                'name'            => 'Solicitudes',                
                'description'     => 'Realize solicitud de productos de higiene y limpieza.',
                'icon'            => 'fad fa-pump-soap',
                'color'           => '#24D4F2',
                'url'             => '',
                'route_app'       => 'requestList',
                'enabled'         => true,
                'settings'        => null,
                'created_at'      => Carbon::now(),
                'updated_at'      => Carbon::now()
            ],
            [
                'id'              => 7,
                'name'            => 'Firma Digital',                
                'description'     => 'Permite que los clientes aprueben con su firma órdenes de trabajo finalizados.',
                'icon'            => 'fad fa-signature',
                'color'           => '#8E47E6',
                'url'             => 'signature',
                'route_app'       => null,
                'enabled'         => true,
                'settings'        => null,
                'created_at'      => Carbon::now(),
                'updated_at'      => Carbon::now()
            ],
            [
                'id'              => 8,
                'name'            => 'Flujos de trabajo',                
                'description'     => 'Asegurarte de que el flujo de trabajo y las entregas de tu equipo se lleven a cabo sin problemas.',                
                'icon'            => 'fad fa-network-wired',
                'color'           => '#E5E5E5',
                'url'             => 'workflows',
                'route_app'       => null,
                'enabled'         => false,
                'settings'        => null,
                'created_at'      => Carbon::now(),
                'updated_at'      => Carbon::now()
            ],
            [
                'id'              => 9,
                'name'            => 'Planificador',                
                'description'     => 'Organiza tus actividades semanales de la forma más sencilla.',                
                'icon'            => 'fad fa-calendar-week',
                'color'           => '#E5E5E5',
                'url'             => 'config-planner',
                'route_app'       => null,
                'enabled'         => false,
                'settings'        => '{"timezone": "America/Costa_Rica"}',
                'created_at'      => Carbon::now(),
                'updated_at'      => Carbon::now()
            ],
            [
                'id'              => 10,
                'name'            => 'Solicitudes Bodega',                
                'description'     => 'gestione las solicitudes suministro para su organización.',
                'icon'            => 'fad fa-warehouse',
                'color'           => '#2A9D8F',
                'url'             => 'warehouse',
                'route_app'       => 'warehouse',
                'enabled'         => true,
                'settings'        => null,
                'created_at'      => Carbon::now(),
                'updated_at'      => Carbon::now()
            ],
            [
                'id'              => 11,
                'name'            => 'Proyectos',                
                'description'     => 'Construir planes de proyectos que se ajusten a las cambiantes condiciones de su organización.',
                'icon'            => 'fad fa-project-diagram',
                'color'           => '#E5E5E5',
                'url'             => 'projects',
                'route_app'       => null,
                'enabled'         => false,
                'settings'        => null,
                'created_at'      => Carbon::now(),
                'updated_at'      => Carbon::now()
            ],
        ]);


    }


}