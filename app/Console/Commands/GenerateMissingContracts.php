<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Providers\UtilityServiceProvider as u;

class GenerateMissingContracts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'erp:generate-missing-contracts {agreement_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sinh các hợp đồng bị thiếu cho một agreement_id';

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
        $agreement_id = (int) $this->argument('agreement_id');
        if (!$agreement_id) {
            $this->error('Vui lòng cung cấp agreement_id');
            return 0;
        }

        $agreement = u::first("SELECT * FROM agreements WHERE id = $agreement_id AND status > 0");
        if (!$agreement) {
            $this->error("Không tìm thấy thông tin đăng ký (agreement) ID: $agreement_id");
            return 0;
        }

        $created_count = 0;
        
        if ($agreement->type_fee == 1) {
            $tuition_fee_info = u::getObject(['id' => $agreement->tuition_fee_id], 'tuition_fee');
            if ($tuition_fee_info) {
                // Check exist contract
                $existContract = u::first("SELECT id FROM contracts WHERE agreement_id = $agreement_id AND product_id = {$tuition_fee_info->product_id} AND status NOT IN (0, 8) LIMIT 1");
                if (!$existContract) {
                    $contract_id = u::insertSimpleRow(array(
                        'type' => 1,
                        'student_id' => $agreement->student_id,
                        'branch_id' => $agreement->branch_id,
                        'tuition_fee_id' => $agreement->tuition_fee_id,
                        'ec_id' => $agreement->ec_id,
                        'ec_leader_id' => $agreement->ec_leader_id,
                        'product_id' => $tuition_fee_info->product_id,
                        'status' => 1,
                        'count_recharge' => 1,
                        'agreement_id' => $agreement_id,
                        'book_delivered_date' => $agreement->book_delivered_date ?? null,
                        'must_charge' => $tuition_fee_info->price,
                        'init_tuition_fee_id' => $tuition_fee_info->id,
                        'init_tuition_fee_amount' => $tuition_fee_info->price,
                        'init_tuition_fee_session' => $tuition_fee_info->session,
                        'total_charged' => 0,
                        'debt_amount' => $tuition_fee_info->price,
                        'total_sessions' => $tuition_fee_info->session,
                        'real_sessions' => $tuition_fee_info->session,
                    ), 'contracts');
                    $contract_code = str_pad((string) $contract_id, 6, '0', STR_PAD_LEFT);
                    $contract_code = config('app.prefix_contract_code') . $contract_code;
                    u::updateSimpleRow(array('code' => $contract_code), array('id' => $contract_id), 'contracts');
                    u::addLogContracts($contract_id);
                    $created_count++;
                }
            }
        } elseif ($agreement->type_fee == 2) {
            $relation_tuition_fee = u::query("SELECT t.*, r.price_combo, r.stt 
            FROM tuition_fee_relation AS r 
                LEFT JOIN tuition_fee AS t ON r.exchange_tuition_fee_id=t.id 
                WHERE r.status=1 AND r.tuition_fee_id = " . $agreement->tuition_fee_id . "
                ORDER BY r.stt ASC");
            foreach ($relation_tuition_fee as $fee) {
                $existContract = u::first("SELECT id FROM contracts WHERE agreement_id = $agreement_id AND product_id = {$fee->product_id} AND status NOT IN (0, 8) LIMIT 1");
                if (!$existContract) {
                    $contract_id = u::insertSimpleRow(array(
                        'type' => 1,
                        'student_id' => $agreement->student_id,
                        'branch_id' => $agreement->branch_id,
                        'tuition_fee_id' => $fee->id,
                        'ec_id' => $agreement->ec_id,
                        'ec_leader_id' => $agreement->ec_leader_id,
                        'product_id' => $fee->product_id,
                        'status' => 1,
                        'count_recharge' => $fee->stt,
                        'agreement_id' => $agreement_id,
                        'book_delivered_date' => $fee->stt == 1 ? ($agreement->book_delivered_date ?? null) : null,
                        'must_charge' => $fee->price_combo,
                        'init_tuition_fee_id' => $fee->id,
                        'init_tuition_fee_amount' => $fee->price_combo,
                        'init_tuition_fee_session' => $fee->session,
                        'total_charged' => 0,
                        'debt_amount' => $fee->price_combo,
                        'total_sessions' => $fee->session,
                        'real_sessions' => $fee->session,
                    ), 'contracts');
                    $contract_code = str_pad((string) $contract_id, 6, '0', STR_PAD_LEFT);
                    $contract_code = config('app.prefix_contract_code') . $contract_code;
                    u::updateSimpleRow(array('code' => $contract_code), array('id' => $contract_id), 'contracts');
                    u::addLogContracts($contract_id);
                    $created_count++;
                } else {
                    u::updateSimpleRow(array(
                        'tuition_fee_id' => $fee->id,
                        'must_charge' => $fee->price_combo,
                        'init_tuition_fee_id' => $fee->id,
                        'init_tuition_fee_amount' => $fee->price_combo,
                        'init_tuition_fee_session' => $fee->session,
                        'total_charged' => 0,
                        'debt_amount' => $fee->price_combo,
                        'total_sessions' => $fee->session,
                        'real_sessions' => $fee->session,
                    ),array('id'=>$existContract->id), 'contracts');
                }
            }
        }

        $this->info("Chạy tool thành công. Đã tạo mới $created_count contract bị thiếu cho agreement $agreement_id.");
        return 0;
    }
}
