<?php

namespace App\Console;

use App\Console\Commands\AddScheduleHasStudent;
use App\Console\Commands\JobsSendEmail;
use App\Console\Commands\ProcessDataScheduleHasStudent;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        ProcessDataScheduleHasStudent::class,
        AddScheduleHasStudent::class,
        JobsSendEmail::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('jobsSendEmail:command')->cron('* * * * *');
        $schedule->command('scheduleHasStudent:process')->dailyAt('00:05')->withoutOverlapping();
        $schedule->command('scheduleHasStudent:add')->dailyAt('01:00')->withoutOverlapping();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
