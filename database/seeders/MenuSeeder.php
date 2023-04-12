<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MenuSeeder extends Seeder
{
    public function run()
    {
        DB::table('wh_menu')->insert([
            [
                'name'        => 'Dashboards',
                'type'        => 'HEADER',
                'icon'        => null,
                'url'         => null,
                'idparent'    => null,
                'position'    => 1,
                'enable'    => 1,
            ],
            [
                'name'        => 'Tasks',
                'type'        => 'NAV',
                'icon'        => 'feather icon-alert-circle',
                'url'         => 'dashboard-tasks',
                'idparent'    => null,
                'position'    => 2,
                'enable'    => 1,
            ],
            [
                'name'        => 'Workplan',
                'type'        => 'NAV',
                'icon'        => 'fa fa-calendar',
                'url'         => 'work-plan',
                'idparent'    => null,
                'position'    => 2,
                'enable'    => 1,
            ],
            [
                'name'        => 'Cleaning',
                'type'        => 'NAV',
                'icon'        => 'fad fa-broom',
                'url'         => 'dashboard-cleaning',
                'idparent'    => null,
                'position'    => 3,
                'enable'    => 0,
            ],
            [
                'name'        => 'Analytics',
                'type'        => 'HEADER',
                'icon'        => null,
                'url'         => null,
                'idparent'    => null,
                'position'    => 5,
                'enable'    => 1,
            ],
            [
                'name'        => 'Reports',
                'type'        => 'PARENT',
                'icon'        => 'feather icon-pie-chart',
                'url'         => null,
                'idparent'    => null,
                'position'    => 6,
                'enable'    => 1,
            ],
            [
                'name'        => 'Report User',
                'type'        => 'CHILD',
                'icon'        => 'feather icon-circle',
                'url'         => 'report-users',
                'idparent'    => 5,
                'position'    => 7,
                'enable'    => 1,
            ],
            [
                'name'        => 'Report Task',
                'type'        => 'CHILD',
                'icon'        => 'feather icon-circle',
                'url'         => 'report-task',
                'idparent'    => 5,
                'position'    => 8,
                'enable'    => 1,
            ],
            [
                'name'        => 'Report Checklist',
                'type'        => 'CHILD',
                'icon'        => 'feather icon-circle',
                'url'         => 'report-checklist',
                'idparent'    => 5,
                'position'    => 8,
                'enable'    => 0,
                'position'    => 9,
                'enable'    => 1,
            ],
            [
                'name'        => 'Report Checklist Audit',
                'type'        => 'CHILD',
                'icon'        => 'feather icon-circle',
                'url'         => 'report-checklist-audit',
                'idparent'    => 5,
                'position'    => 8,
                'enable'    => 0,
                'position'    => 10,
                'enable'    => 0,
            ],
            [
                'name'        => 'Report Item',
                'type'        => 'CHILD',
                'icon'        => 'feather icon-circle',
                'url'         => 'report-item',
                'idparent'    => 5,
                'position'    => 11,
                'enable'    => 1,
            ],
            [
                'name'        => 'Report Branch',
                'type'        => 'CHILD',
                'icon'        => 'feather icon-circle',
                'url'         => 'report-average-branch',
                'idparent'    => 5,
                'position'    => 12,
                'enable'    => 0,
            ],
            [
                'name'        => 'Report Productivity',
                'type'        => 'CHILD',
                'icon'        => 'feather icon-circle',
                'url'         => 'report-productivity',
                'idparent'    => 5,
                'position'    => 13,
                'enable'    => 0,
            ],
            [
                'name'        => 'Report Duration',
                'type'        => 'CHILD',
                'icon'        => 'feather icon-circle',
                'url'         => 'report-duration',
                'idparent'    => 5,
                'position'    => 14,
                'enable'    => 1,
            ],
            [
                'name'        => 'Report Overtime',
                'type'        => 'CHILD',
                'icon'        => 'feather icon-circle',
                'url'         => 'report-overtime',
                'idparent'    => 5,
                'position'    => 15,
                'enable'    => 0,
            ],
            [
                'name'        => 'Report Location',
                'type'        => 'CHILD',
                'icon'        => 'feather icon-circle',
                'url'         => 'report-location',
                'idparent'    => 5,
                'position'    => 16,
                'enable'    => 0,
            ],
            [
                'name'        => 'Report Branch',
                'type'        => 'CHILD',
                'icon'        => 'feather icon-circle',
                'url'         => 'report-branch',
                'idparent'    => 5,
                'position'    => 17,
                'enable'    => 0,
            ],
            [
                'name'        => 'Report checklist note',
                'type'        => 'CHILD',
                'icon'        => 'feather icon-circle',
                'url'         => 'report-checklist-note',
                'idparent'    => 5,
                'position'    => 18,
                'enable'    => 0,
            ],
            [
                'name'        => 'Report Overtime',
                'type'        => 'CHILD',
                'icon'        => 'feather icon-circle',
                'url'         => 'report-clockin',
                'idparent'    => 5,
                'position'    => 19,
                'enable'    => 0,
            ],
            [
                'name'        => 'Report Location',
                'type'        => 'CHILD',
                'icon'        => 'feather icon-circle',
                'url'         => 'report-clockin-map',
                'idparent'    => 5,
                'position'    => 20,
                'enable'    => 0,
            ],
            [
                'name'        => 'Attendance',
                'type'        => 'CHILD',
                'icon'        => 'feather icon-circle',
                'url'         => 'report-clockin-device',
                'idparent'    => 5,
                'position'    => 21,
                'enable'    => 0,
            ],
            [
                'name'        => 'Report General',
                'type'        => 'CHILD',
                'icon'        => 'feather icon-circle',
                'url'         => 'report-general',
                'idparent'    => 5,
                'position'    => 22,
                'enable'    => 1,
            ],
            [
                'name'        => 'Report checklist invoice',
                'type'        => 'CHILD',
                'icon'        => 'feather icon-circle',
                'url'         => 'report-checklist-invoice',
                'idparent'    => 5,
                'position'    => 23,
                'enable'    => 0,
            ],
            [
                'name'        => 'Report Priority',
                'type'        => 'CHILD',
                'icon'        => 'feather icon-circle',
                'url'         => 'report-priority',
                'idparent'    => 5,
                'position'    => 24,
                'enable'    => 1,
            ],
            [
                'name'        => 'Report Cleaning',
                'type'        => 'CHILD',
                'icon'        => 'feather icon-circle',
                'url'         => 'report-cleaning',
                'idparent'    => 5,
                'position'    => 25,
                'enable'    => 0,
            ],
            [
                'name'        => 'Report Team',
                'type'        => 'CHILD',
                'icon'        => 'feather icon-circle',
                'url'         => 'report-team',
                'idparent'    => 5,
                'position'    => 26,
                'enable'    => 1,
            ],
            [ 
                'name'        => 'Configuration',
                'type'        => 'HEADER',
                'icon'        => null,
                'url'         => null,
                'idparent'    => null,
                'position'    => 27,
                'enable'    => 1,
            ],
            [   
                'name'        => 'Dashboard',
                'type'        => 'NAV',
                'icon'        => 'feather icon-server',
                'url'         => 'config-dashboard',
                'idparent'    => null,
                'position'    => 28,
                'enable'    => 1,
            ],
            [   
                'name'        => 'Apps',
                'type'        => 'NAV',
                'icon'        => 'fad fa-shapes',
                'url'         => 'config-apps',
                'idparent'    => null,
                'position'    => 29,
                'enable'    => 1,
            ],
            [   
                'name'        => 'Soporte',
                'type'        => 'HEADER',
                'icon'        => null,
                'url'         => null,
                'idparent'    => null,
                'position'    => 30,
                'enable'    => 1,
            ],
            [
                'name'        => 'WhatsApp',
                'type'        => 'NAV',
                'icon'        => 'feather icon-alert-circle',
                'url'         => 'https://api.whatsapp.com/send?phone=70681468',
                'idparent'    => null,
                'position'    => 31,
                'enable'    => 1,
            ]
        ]);
    }
}
