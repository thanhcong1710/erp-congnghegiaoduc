<?php

namespace App\Console;

use App\Console\Commands\AddScheduleClass;
use App\Console\Commands\AddScheduleHasStudent;
use App\Console\Commands\AutoWithdraw;
use App\Console\Commands\AutoWithdrawEnrollmentDeposit;
use App\Console\Commands\JobsProcessLockParent;
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
        AddScheduleClass::class,
        JobsSendEmail::class,
        JobsProcessLockParent::class,
        AutoWithdrawEnrollmentDeposit::class,
        AutoWithdraw::class
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
        $schedule->command('autoWithdrawEnrollmentDeposit:process')->dailyAt('23:55')->withoutOverlapping();
        $schedule->command('scheduleHasStudent:process')->dailyAt('00:05')->withoutOverlapping();
        $schedule->command('scheduleHasStudent:add')->dailyAt('01:00')->withoutOverlapping();
        $schedule->command('processdata:command')->dailyAt('2:00'); 
        $schedule->command('jobsProcessLockParent:command')->dailyAt('3:00'); 
        $schedule->command('autoWithdraw:process')->dailyAt('2:30')->withoutOverlapping();
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
