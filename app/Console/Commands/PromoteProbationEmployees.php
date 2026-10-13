<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\HrmEmployeeProfile;

class PromoteProbationEmployees extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hrm:promote-probation';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tự động chuyển nhân sự hết thời gian thử việc sang nhân sự Fulltime';

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
        $today = date('Y-m-d');
        $updated = HrmEmployeeProfile::where('employee_type', 'probation')
            ->whereNotNull('probation_end_date')
            ->where('probation_end_date', '<=', $today)
            ->where('work_status', 'working')
            ->update([
                'employee_type' => 'fulltime'
            ]);

        $this->info("Đã tự động chuyển {$updated} nhân sự thử việc sang Fulltime thành công!");
        return 0;
    }
}
