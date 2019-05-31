<?php

namespace App\Console;

use App\Jobs\ProcessPendingWebsites;
use App\Jobs\ProcessPublicAdministrationsUpdateFromIpa;
use App\Jobs\ProcessWebsitesMonitoring;
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
        $schedule->job(new ProcessPublicAdministrationsUpdateFromIpa())->dailyAt('06:30')->runInBackground()->onOneServer();
        $schedule->job(new ProcessPendingWebsites())->hourly()->runInBackground()->onOneServer();
        $schedule->job(new ProcessWebsitesMonitoring())->daily()->runInBackground()->onOneServer();
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
