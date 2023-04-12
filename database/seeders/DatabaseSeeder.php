<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // $this->call(ActivityTableSeeder::class);
        // $this->call(GanttLinkTableSeeder::class);
        
        $this->call([            
            AppTableSeeder::class,
            MenuSeeder::class,
            TicketStatusTableSeeder::class,
            RoleTableSeeder::class,
            TeamTableSeeder::class,
            TicketPriorityTableSeeder::class,
            ChecklistTableSeeder::class,
            ItemTableSeeder::class,
            CleaningStatusTableSeeder::class,
            SpotTableSeeder::class,
            UserTableSeeder::class,           
            PriorityTableSeeder::class,
            
           
            BookingStatusTableSeeder::class,
            OrganizationTableSeeder::class,
            SettingUpdateTableSeeder::class,
            
            AssetCategoryTableSeeder::class,
            AssetStatusTableSeeder::class,
            AssetTableSeeder::class,

            ProtocolTypeTableSeeder::class,
            ProtocolTableSeeder::class,
            
            // ProductDestinationTableSeeder::class,
            // PrductionBreakTableSeeder::class,
            // ProductionStatusTableSeeder::class,
            // ProductionInputTableSeeder::class,
            // ProductionFormulaTableSeeder::class,
            // ProductCategoryTableSeeder::class,
            // EquipmentTableSeeder::class,          
            // ProductionStopTableSeeder::class,
            // EquipmentTypeTableSeeder::class,
            // EquipmentStatusTableSeeder::class,
            // ProductionScheduleTableSeeder::class,
            // ProductionTableSeeder::class,

            WarehouseItemTableSeeder::class,
            WarehouseStatusTableSeeder::class,

        ]);

        
    }
}
