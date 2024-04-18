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
        $schedule->command(
            'app:tg-get-updates', ['пины', 'мобильные подписки', 'пинсабмит', 'pinsubmit', 'click2sms',
            'zeydoo', 'affshark', 'applink', 'olimob', 'webwap', 'mvas', 'ivr', 'click2call'
        ])->everyFiveMinutes();

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
