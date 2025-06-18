<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Providers\UtilityServiceProvider as u;
use App\User;
use Illuminate\Http\Request;

class UpdateEnrolmentLastDate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'updateEnrolmentLastDate:command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Enrolment Last Date';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(Request $request)
    {
        $lists = u::query("SELECT id FROM contracts WHERE status=6");
        foreach ($lists as $row) {
            u::updateEnrolmentLastDate($row->id);
            echo $row->id . "/";
        }
        return "ok";
    }
}
