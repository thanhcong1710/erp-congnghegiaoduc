<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Agreement; // Assuming this exists, but using DB facade is safer since I am not sure of the model path
use Carbon\Carbon;

class SnapshotAgreementRevenue extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'snapshot:agreement-revenue {salary_month? : The salary month to snapshot (format: YYYY-MM)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Snapshot agreement revenue for historical report (report-25)';

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
        $targetMonth = $this->argument('salary_month');

        if (!$targetMonth) {
            $today = Carbon::now();
            if ($today->day <= 5) {
                // Processing for the previous month
                $targetMonth = $today->copy()->subMonth()->format('Y-m');
            } else {
                // Processing for the current month
                $targetMonth = $today->format('Y-m');
            }
        }

        $this->info("Starting snapshot for salary month: {$targetMonth}");

        // Select all agreements where salary_month matches targetMonth
        $agreements = DB::table('agreements')
            ->select('id', 'must_charge', 'discount_amount', 'debt_amount')
            ->where('salary_month', $targetMonth)
            ->where('debt_amount', 0) // Salary calculated only when debt_amount = 0 (based on report25 logic)
            ->get();

        $count = 0;
        foreach ($agreements as $a) {
            $revenue = (float)$a->must_charge - (float)$a->discount_amount;
            DB::table('agreements_revenue_histories')->updateOrInsert(
                [
                    'agreement_id' => $a->id,
                    'salary_month' => $targetMonth
                ],
                [
                    'must_charge' => (float)$a->must_charge,
                    'discount_amount' => (float)$a->discount_amount,
                    'revenue_amount' => $revenue,
                    'updated_at' => Carbon::now()
                ]
            );
            $count++;
        }

        $this->info("Processed {$count} agreements for {$targetMonth}.");

        return 0;
    }
}
