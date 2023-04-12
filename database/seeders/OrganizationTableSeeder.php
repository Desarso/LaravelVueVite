<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class OrganizationTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO"');

        DB::table('wh_organization')->insert([
            [
                'id'         => 1,
                'key'        => 'acXY',
                'name'       => "ACME",
                'type'  => "Medicina",
                'settings'  => '{"hide_columns": ["created_by",  "idasset"], "hidden_fields": ["code"]}',
                'plansettings' => '[{"icon":"fad fa-car-building","name":"Spot Types","color":"#144A75","table":"wh_spot_type","maxvalue":5,"position":7},{"icon":"fad fa-store-alt","name":"Spots","color":"#1F73B7","table":"wh_spot","maxvalue":20,"position":1},{"icon":"fad fa-flask-potion","name":"Task Types","color":"#E34F31","table":"wh_ticket_type","maxvalue":5,"position":8},{"icon":"fad fa-tools","name":"Items","color":"#C72A1B","table":"wh_item","maxvalue":30,"position":2},{"icon":"fad fa-calendar-week","name":"Planner","color":"#7367f0","table":"wh_planner","maxvalue":50,"position":10},{"icon":"fad fa-users","name":"Users","color":"#6A27B9","table":"wh_user","maxvalue":20,"position":3},{"icon":"fad fa-users-class","name":"Teams","color":"#B552E3","table":"wh_team","maxvalue":4,"position":4},{"icon":"fad fa-user-unlock","name":"Roles","color":"#ED553B","table":"wh_role","maxvalue":5,"position":6},{"icon":"fad fa-tasks","name":"Forms","color":"#F5A133","table":"wh_checklist","maxvalue":10,"position":5}]',
                'menusettings' => '{"menu":[{"url":"","icon":"feather icon-home","badge":"2","navheader":"Dashboards","badgeClass":"badge badge-warning badge-pill float-right mr-2"},{"url":"dashboard-tasks","i18n":"nav.dashboard_tasks","icon":"feather icon-alert-circle","name":"Tasks"},{"url":"dashboard-cleaning","i18n":"nav.dashboard_cleaningschedule","icon":"fad fa-broom","name":"Cleaning","idapp":4},{"url":"dashboard-production","i18n":"nav.dashboard_production","icon":"fad fa-cogs","name":"Production","idapp":7},{"url":"attendance","i18n":"nav.dashboard_production","icon":"fas fa-clipboard-user","name":"Attendance","idapp":3},{"url":"","icon":"","navheader":"Analytics"},{"url":"","icon":"feather icon-pie-chart","name":"Reports","badge":"3","submenu":[{"url":"report-priority","i18n":"nav.chart_apex","icon":"feather icon-circle","name":"Report Priority"},{"url":"warehouse-report","i18n":"nav.warehouse-report","icon":"feather icon-circle","name":"Warehouse Report"},{"url":"report-cleaning-request","i18n":"nav.chart_chartjs","icon":"feather icon-circle","name":"Report Cleaning Request","idapp":4}],"badgeClass":"badge badge-pill badge-success float-right mr-2"},{"url":"","icon":"","navheader":"Configuration"},{"url":"config-dashboard","i18n":"nav.configdashboard","icon":"feather icon-server","name":"Dashboard"},{"url":"config-apps","i18n":"nav.configapps","icon":"fad fa-shapes","name":"Apps"},{"url":"","icon":"","navheader":"Account Settings"},{"url":"","icon":"fas fa-question","name":"Support","slug":"dashboard","badge":"2","submenu":[{"url":"https://whagons.com/","i18n":"nav.documentation","icon":"feather icon-folder","name":"Documentation"},{"url":"https://whagons.com/soporte","i18n":"nav.raise_support","icon":"feather icon-life-buoy","name":"Support"}],"badgeClass":"badge badge-warning badge-pill float-right mr-2"}]}',
                'appbar' => '["task", "apps", "dashboard", "help"]',
                'appmenu' => '[{"name":"protocol","enabled":true},{"name":"clockin","enabled":false},{"name":"work-plan","enabled":false},{"name":"asset-loan","enabled":false}]',
                'enabled' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
        ]);
    }
}
