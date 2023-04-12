<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Console\Commands\GenerateRecurringTickets;
use App\Console\Commands\GenerateCleaningPlans;
use App\Console\Commands\SendAuditEmails;
use App\Console\Commands\ResetCleaningStatus;
use App\Console\Commands\SendUsageReminders;
use App\Console\Commands\SendReminders;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        GenerateRecurringTickets::class,
        GenerateCleaningPlans::class,
        SendAuditEmails::class,
        ResetCleaningStatus::class,
        SendUsageReminders::class,
        SendReminders::class,
    ];

    protected function schedule(Schedule $schedule)
    {
        // limpia las tablas de wh_notifications y log_sync
        $schedule->command('wh:reset_tables')->daily();
        // recordatorios de uso de la herramienta
        $schedule->command('command:send_usage_reminders')->everyTenMinutes();
        // envia los recordatorios de tareas vencidas
        $schedule->command('wh:send_reminders')->everyFiveMinutes();

        $time = env('UTC_WORKPLAN_REMINDER', '0');

        if ($time != '0') {
            $schedule->command('wh:send_workplan_reminders')->dailyAt($time);
        }


        $cronjobs = env('CRONJOB_AVAILABLE', '0');
        if ($cronjobs == '0') return 0;

        $cronjobs = explode(",", $cronjobs);

        foreach ($cronjobs as &$cronjob) {
            
            switch ($cronjob) {

                case 'send_audit_emails':
                    $schedule->command('command:send_audit_emails')->everyThirtyMinutes();
                    break;

                case 'reset_cleaning_status':
                    $schedule->command('command:reset_cleaning_status')->dailyAt('08:30');
                    break;

                case 'generate_recurring_tickets':
                    $schedule->command('command:generate_recurring_tickets')->dailyAt('08:35');
                    break;

                case 'generate_cleaning_plans':
                    $schedule->command('command:generate_cleaning_plans')->dailyAt('08:40');
                    break;

                case 'calculate_attendance_overtime':
                    $schedule->command('command:calculate_attendance_overtime')->dailyAt('05:55');
                    break;
            }
        }
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
