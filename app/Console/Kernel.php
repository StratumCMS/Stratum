<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $frequency = setting('backup_frequency', 'daily');

        match ($frequency) {
            'daily' => $schedule->command('backup:auto')->daily(),
            'weekly' => $schedule->command('backup:auto')->weekly(),
            'monthly' => $schedule->command('backup:auto')->monthly(),
            'yearly' => $schedule->command('backup:auto')->yearly(),
            default => $schedule->command('backup:auto')->daily(),
        };
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
