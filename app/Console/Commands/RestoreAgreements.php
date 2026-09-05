<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RestoreAgreements extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'erp:restore-agreements {--agreement_id= : Cụ thể ID của bản ghi muốn restore} {--salary_month=2026-08 : Tháng tính lương của các bản ghi bị xoá nhầm}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Phục hồi lại các bản ghi agreements đã bị xoá nhầm từ bảng log_agreements';

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
        $agreementId = $this->option('agreement_id');
        $salaryMonth = $this->option('salary_month');

        if ($agreementId) {
            $this->info("Đang phục hồi cho agreement_id: " . $agreementId);
            $this->restoreAgreement($agreementId);
        } else {
            if (!$this->confirm("Bạn không truyền agreement_id. Bạn có chắc chắn muốn phục hồi TẤT CẢ các bản ghi bị xoá trong tháng salary_month = {$salaryMonth} không?")) {
                $this->info("Đã huỷ bỏ lệnh.");
                return 0;
            }

            // Lấy danh sách các agreement_id có trong log_agreements với salary_month tương ứng
            // nhưng không còn tồn tại trong bảng agreements
            $missingAgreements = DB::select("
                SELECT DISTINCT la.agreement_id
                FROM log_agreements la
                LEFT JOIN agreements a ON a.id = la.agreement_id
                WHERE la.salary_month = ? AND a.id IS NULL
            ", [$salaryMonth]);

            $this->info("Tìm thấy " . count($missingAgreements) . " bản ghi cần phục hồi.");

            foreach ($missingAgreements as $missing) {
                $this->restoreAgreement($missing->agreement_id);
            }
        }

        $this->info("Hoàn tất quá trình phục hồi.");
        return 0;
    }

    private function restoreAgreement($agreementId)
    {
        // Kiểm tra xem bản ghi đã tồn tại chưa
        $exists = DB::table('agreements')->where('id', $agreementId)->exists();
        if ($exists) {
            $this->warn("Agreement ID {$agreementId} đã tồn tại trong bảng agreements. Bỏ qua.");
            return;
        }

        // Lấy bản ghi cuối cùng từ log_agreements
        $latestLog = DB::table('log_agreements')
            ->where('agreement_id', $agreementId)
            ->orderBy('id', 'desc')
            ->first();

        if (!$latestLog) {
            $this->error("Không tìm thấy log nào cho agreement_id: {$agreementId}");
            return;
        }

        // Chuyển sang mảng để dễ thao tác
        $logData = (array) $latestLog;

        // Xóa các trường của riêng log_agreements
        unset($logData['id']); // log id
        
        // Đặt lại ID thành agreement_id
        $logData['id'] = $logData['agreement_id'];
        unset($logData['agreement_id']); // Không cần field này trong agreements nếu không có thiết kế sẵn

        // Insert lại vào agreements
        try {
            DB::table('agreements')->insert($logData);
            $this->info("Đã phục hồi thành công agreement_id: {$agreementId}");
        } catch (\Exception $e) {
            $this->error("Lỗi khi phục hồi agreement_id {$agreementId}: " . $e->getMessage());
        }
    }
}
