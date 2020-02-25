<?php

namespace App\Console;

use App\Jobs\MonitorWebsitesTracking;
use App\Jobs\ProcessPendingWebsites;
use App\Jobs\ProcessPublicAdministrationsUpdateFromIpa;
use App\Jobs\PurgePendingInvitations;
use App\Jobs\ResetEnvironment;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

/**
 * Application command scheduler.
 */
class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param Schedule $schedule the command scheduler
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->job(new ProcessPublicAdministrationsUpdateFromIpa())->dailyAt('06:30')->onOneServer();
        $schedule->job(new ProcessPendingWebsites())->hourly()->onOneServer();
        $schedule->job(new ProcessPendingWebsites(true))->dailyAt('04:30')->onOneServer();
        $schedule->job(new MonitorWebsitesTracking())->daily()->onOneServer();
        $schedule->job(new PurgePendingInvitations())->dailyAt('03:30')->onOneServer();
        $schedule->job(new ResetEnvironment())->weekly()->sundays()->at('23:30')->onOneServer()->environments(['public-playground']);
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
