<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Providers\UtilityServiceProvider as u;
use Illuminate\Support\Facades\DB;

class UpdateAgreementsData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'erp:update-agreements-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update agreements data: last_pay_date, full_fee_date, count_recharge, first_8th_session_date, end_session_date, is_first_package';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info("Bắt đầu cập nhật dữ liệu...");
        
        $this->info("1. Đang cập nhật last_pay_date và full_fee_date...");
        DB::statement("
            UPDATE agreements a
            LEFT JOIN (
                SELECT agreement_id, MAX(charge_date) as last_charge_date 
                FROM payments 
                GROUP BY agreement_id
            ) p ON p.agreement_id = a.id
            SET a.last_pay_date = p.last_charge_date,
                a.full_fee_date = CASE WHEN a.debt_amount = 0 THEN p.last_charge_date ELSE NULL END
            WHERE a.status > 0
        ");

        $this->info("2. Đang tải dữ liệu agreements...");
        $agreements = DB::select("
            SELECT id, student_id, full_fee_date
            FROM agreements
            WHERE status > 0
            ORDER BY 
                CASE WHEN last_pay_date IS NULL THEN 1 ELSE 0 END, 
                last_pay_date ASC, 
                created_at ASC
        ");

        $student_agreements = [];
        $first_agr_ids = [];
        foreach ($agreements as $agr) {
            if (!isset($student_agreements[$agr->student_id])) {
                $student_agreements[$agr->student_id] = [];
                $first_agr_ids[] = $agr->id;
            }
            $student_agreements[$agr->student_id][] = $agr;
        }

        $this->info("3. Đang tải thông tin lớp học (contracts) của gói đầu tiên...");
        $contracts_by_agr = [];
        $chunks = array_chunk($first_agr_ids, 1000);
        foreach ($chunks as $chunk) {
            $ids_str = implode(',', $chunk);
            $contracts = DB::select("
                SELECT c.agreement_id, c.id, c.enrolment_start_date, c.enrolment_last_date,
                       c.real_sessions, c.summary_sessions, cls.class_day, cls.branch_id, cls.product_id
                FROM contracts c
                JOIN classes cls ON c.class_id = cls.id
                WHERE c.agreement_id IN ($ids_str) 
                  AND c.status > 0 
                  AND c.class_id IS NOT NULL 
                  AND c.enrolment_start_date IS NOT NULL
                ORDER BY c.enrolment_start_date ASC
            ");
            foreach ($contracts as $c) {
                if (!isset($contracts_by_agr[$c->agreement_id])) {
                    $contracts_by_agr[$c->agreement_id] = $c;
                }
            }
        }

        $this->info("4. Đang tính toán ngày học thứ 8, ngày học cuối và chuẩn bị cập nhật...");
        $updates = [];
        $holidays_cache = [];
        foreach ($student_agreements as $student_id => $agrs) {
            $first_agr = $agrs[0];
            $first_agr_id = $first_agr->id;
            
            $first_8th_session_date = null;
            $end_session_date = null;

            if (isset($contracts_by_agr[$first_agr_id])) {
                $first_contract = $contracts_by_agr[$first_agr_id];
                $branch_id = $first_contract->branch_id;
                $product_id = $first_contract->product_id;
                $cache_key = "{$branch_id}_{$product_id}";
                
                if (!isset($holidays_cache[$cache_key])) {
                    $holidays_cache[$cache_key] = u::getPublicHolidays($branch_id, $product_id);
                }
                
                $holidays = $holidays_cache[$cache_key];
                if (!empty($first_contract->class_day)) {
                    $arr_day = array_filter(explode(',', $first_contract->class_day));
                    if (count($arr_day) > 0) {
                        $eighth_session_info = u::calculatorSessionsByNumberOfSessions($first_contract->enrolment_start_date, 8, $holidays, $arr_day);
                        $first_8th_session_date = data_get($eighth_session_info, 'end_date');
                    } else {
                        $first_8th_session_date = date('Y-m-d', strtotime($first_contract->enrolment_start_date . ' + 28 days'));
                    }
                } else {
                    $first_8th_session_date = date('Y-m-d', strtotime($first_contract->enrolment_start_date . ' + 28 days'));
                }

                // Tính end_session_date: ưu tiên dùng enrolment_last_date đã lưu sẵn
                if (!empty($first_contract->enrolment_last_date)) {
                    $end_session_date = date('Y-m-d', strtotime($first_contract->enrolment_last_date));
                } else {
                    // Fallback: tính lại từ số buổi của contract
                    $sessions = (int)$first_contract->real_sessions ?: (int)$first_contract->summary_sessions;
                    if ($sessions > 0 && !empty($first_contract->class_day)) {
                        $arr_day_es = array_filter(explode(',', $first_contract->class_day));
                        if (count($arr_day_es) > 0) {
                            $session_info = u::calculatorSessionsByNumberOfSessions(
                                $first_contract->enrolment_start_date,
                                $sessions,
                                $holidays,
                                $arr_day_es
                            );
                            $end_session_date = data_get($session_info, 'end_date');
                        }
                    }
                }
            }
            
            foreach ($agrs as $index => $agr) {
                $is_first_package = ($index === 0) ? 1 : 0;
                $count_recharge = 1;
                if ($is_first_package === 1) {
                    $count_recharge = 0;
                } else if ($agr->full_fee_date !== null && $first_8th_session_date !== null && $agr->full_fee_date <= $first_8th_session_date) {
                    $count_recharge = 0;
                } else if ($agr->full_fee_date !== null && $end_session_date !== null && $agr->full_fee_date > date('Y-m-d', strtotime($end_session_date . ' + 2 months'))) {
                    // Ngày full fee > ngày buổi học cuối + 2 tháng => học sinh quay lại sau thời gian dài => tính là mới
                    $count_recharge = 0;
                }
                
                $updates[] = [
                    'id' => $agr->id,
                    'is_first_package' => $is_first_package,
                    'count_recharge' => $count_recharge,
                    'first_8th_session_date' => $first_8th_session_date,
                    'end_session_date' => $end_session_date,
                ];
            }
        }

        $this->info("5. Đang lưu vào cơ sở dữ liệu...");
        $bar = $this->output->createProgressBar(count($updates));
        $bar->start();

        DB::beginTransaction();
        try {
            $update_chunks = array_chunk($updates, 500);
            foreach ($update_chunks as $chunk) {
                $cases_first = [];
                $cases_recharge = [];
                $cases_8th = [];
                $cases_end = [];
                $ids = [];
                
                foreach ($chunk as $u) {
                    $ids[] = $u['id'];
                    $cases_first[] = "WHEN id = {$u['id']} THEN {$u['is_first_package']}";
                    $cases_recharge[] = "WHEN id = {$u['id']} THEN {$u['count_recharge']}";
                    
                    if ($u['first_8th_session_date'] === null) {
                        $cases_8th[] = "WHEN id = {$u['id']} THEN NULL";
                    } else {
                        $cases_8th[] = "WHEN id = {$u['id']} THEN '{$u['first_8th_session_date']}'";
                    }

                    if ($u['end_session_date'] === null) {
                        $cases_end[] = "WHEN id = {$u['id']} THEN NULL";
                    } else {
                        $cases_end[] = "WHEN id = {$u['id']} THEN '{$u['end_session_date']}'";
                    }
                }
                
                $ids_str = implode(',', $ids);
                $cases_first_str = implode(' ', $cases_first);
                $cases_recharge_str = implode(' ', $cases_recharge);
                $cases_8th_str = implode(' ', $cases_8th);
                $cases_end_str = implode(' ', $cases_end);
                
                DB::statement("
                    UPDATE agreements 
                    SET is_first_package = CASE $cases_first_str END,
                        count_recharge = CASE $cases_recharge_str END,
                        first_8th_session_date = CASE $cases_8th_str END,
                        end_session_date = CASE $cases_end_str END
                    WHERE id IN ($ids_str)
                ");
                
                $bar->advance(count($chunk));
            }
            
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("Lỗi: " . $e->getMessage());
            return 1;
        }

        $bar->finish();
        $this->line("");
        $this->info("Hoàn tất cập nhật dữ liệu thành công!");
        return 0;
    }
}
