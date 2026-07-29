<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdateContractEnrolmentLastDate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'erp:update-contract-enrolment-last-date';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update enrolment_last_date for contracts with status = 7';

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
     * @return int
     */
    public function handle()
    {
        $this->info("Start updating contracts...");

        $contracts = DB::table('contracts')
            ->whereNotNull('enrolment_start_date')
            ->whereNull('enrolment_last_date')
            ->where('status', 7)
            ->get();

        $count = 0;

        foreach ($contracts as $contract) {
            $class = null;
            if ($contract->class_id) {
                $class = DB::table('classes')->where('id', $contract->class_id)->first();
            }

            $lastDate = null;
            if ($class && $class->cls_enddate) {
                $lastDate = $class->cls_enddate;
            } else {
                $totalSessions = (int)$contract->total_sessions;
                if ($totalSessions > 0) {
                    $weeks = ceil($totalSessions / 2);
                    $lastDate = date('Y-m-d', strtotime($contract->enrolment_start_date . " + $weeks weeks"));
                }
            }

            if ($lastDate) {
                DB::table('contracts')
                    ->where('id', $contract->id)
                    ->update([
                        'enrolment_last_date' => $lastDate
                    ]);
                $this->info("Updated contract ID {$contract->id} with enrolment_last_date = {$lastDate}");
                $count++;
            }
        }

        $this->info("Done! Updated {$count} contracts.");
        return 0;
    }
}
