<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Providers\UtilityServiceProvider as u;
use App\Http\Controllers\ChargesController;

class UpdateContractsDiscountAndCharges extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lms:update-contracts-discount-charges {agreement_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cập nhật lại discount_amount và các trường sessions, charges cho bảng contracts theo logic phân bổ mới (Có thể truyền agreement_id để chạy test)';

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
        $agreementId = $this->argument('agreement_id');

        $query = "SELECT id, total_charged, received_amount, transferred_amount, discount_amount, debt_amount FROM agreements";
        if ($agreementId) {
            $query .= " WHERE id = " . (int)$agreementId;
        }

        $agreements = u::query($query);
        $totalAgreements = count($agreements);

        if ($totalAgreements == 0) {
            $this->info("Không tìm thấy agreement nào để update.");
            return 0;
        }

        $this->info("Bắt đầu update cho $totalAgreements agreements...");

        $bar = $this->output->createProgressBar($totalAgreements);
        $bar->start();

        foreach ($agreements as $agreement) {
            $agreement_id = $agreement->id;
            
            // Lấy danh sách hợp đồng (không tính các bản ghi học lại)
            $contracts = u::query("SELECT * FROM contracts WHERE agreement_id=$agreement_id AND status>0 AND status!=8 AND relearn_from_contract_id IS NULL ORDER BY product_id");
            if (count($contracts) == 0) {
                $bar->advance();
                continue;
            }

            // Logic tính toán chia tiền
            $effectiveCharged = (float) data_get($agreement, 'total_charged', 0)
                + (float) data_get($agreement, 'received_amount', 0)
                - (float) data_get($agreement, 'transferred_amount', 0);
            $totalDiscount = (float) data_get($agreement, 'discount_amount', 0);
            
            $isFullyPaidAgreement = (isset($agreement->debt_amount) && (float) $agreement->debt_amount <= 0);
            
            $dataResult = ChargesController::splitChargedAndDiscountAmount($effectiveCharged, $totalDiscount, (array) $contracts, $isFullyPaidAgreement);
            $packages = data_get($dataResult, 'packages');

            if (!empty($packages)) {
                foreach ($packages as $row) {
                    // Logic tính session có sẵn
                    $availableSession = (int) data_get($row, 'contract_data.init_tuition_fee_session') && (int) data_get($row, 'contract_data.must_charge') ?
                        round(((int) data_get($row, 'total_charged') + (int) data_get($row, 'discount_amount')) / ((int) data_get($row, 'contract_data.must_charge') / (int) data_get($row, 'contract_data.init_tuition_fee_session'))) : 0;
                    
                    $currentStatus = (int) data_get($row, 'contract_data.status');
                    $doneSessions = (int) data_get($row, 'contract_data.done_sessions', 0);
                    
                    $totalChargedAllocated = (int) data_get($row, 'total_charged');
                    $discountAllocated = (int) data_get($row, 'discount_amount');
                    $debtAllocated = (int) data_get($row, 'debt_amount');

                    $updateData = [
                        'real_sessions' => $availableSession,
                        'summary_sessions' => $availableSession,
                        'total_charged' => $totalChargedAllocated,
                        'init_total_charged' => $totalChargedAllocated,
                        'discount_amount' => $discountAllocated,
                        'debt_amount' => $debtAllocated,
                        'updated_at' => date('Y-m-d H:i:s')
                    ];

                    if ($currentStatus == 7) {
                        $updateData['done_sessions'] = $availableSession;
                        $updateData['left_sessions'] = 0;
                    } else {
                        $updateData['left_sessions'] = max(0, $availableSession - $doneSessions);
                    }

                    // Update dữ liệu
                    u::updateSimpleRow($updateData, ['id' => data_get($row, 'contract_id')], 'contracts');
                }
            }
            
            $bar->advance();
        }

        $bar->finish();
        $this->info("\nHoàn tất quá trình update dữ liệu contracts.");
        return 0;
    }
}
