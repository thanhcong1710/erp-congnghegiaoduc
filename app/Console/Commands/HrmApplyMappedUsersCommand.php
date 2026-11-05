<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\HrmEmployeeProfile;
use App\User;
use Illuminate\Support\Facades\DB;

class HrmApplyMappedUsersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hrm:apply-mapped-users {--dry-run : Chạy mô phỏng, không ghi thay đổi vào CSDL}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Thực thi liên kết nhân sự HRM (hrm_employee_profiles) với users từ dữ liệu mapping';

    /**
     * Danh sách 24 bản ghi cần liên kết mới hoặc điều chỉnh
     */
    private $mappingList = [
        ['hrm_id' => 68,  'employee_code' => 'CHAM02',  'user_id' => 60,  'user_hrm_id' => 'CT038', 'name' => 'Phạm Thị Thanh Nga'],
        ['hrm_id' => 73,  'employee_code' => 'CHAM07',  'user_id' => 87,  'user_hrm_id' => 'GV128', 'name' => 'Đỗ Minh Vũ', 'reassign_from_hrm_id' => 392],
        ['hrm_id' => 75,  'employee_code' => 'CHAM09',  'user_id' => 63,  'user_hrm_id' => 'CT041', 'name' => 'Nguyễn Vũ Diễm Quỳnh'],
        ['hrm_id' => 76,  'employee_code' => 'CHAM10',  'user_id' => 47,  'user_hrm_id' => 'CT025', 'name' => 'Dương Thị Thái Bảo'],
        ['hrm_id' => 84,  'employee_code' => 'CHAM18',  'user_id' => 72,  'user_hrm_id' => 'CT050', 'name' => 'Lê Thị Ngọc Anh'],
        ['hrm_id' => 89,  'employee_code' => 'CHAM23',  'user_id' => 199, 'user_hrm_id' => 'GV106', 'name' => 'PHAM TUAN THANH'],
        ['hrm_id' => 90,  'employee_code' => 'CHAM24',  'user_id' => 32,  'user_hrm_id' => 'HĐ002', 'name' => 'PHAM KIM ANH'],
        ['hrm_id' => 103, 'employee_code' => 'CHAM37',  'user_id' => 362, 'user_hrm_id' => 'GV327', 'name' => 'NGUYEN PHUONG UYEN'],
        ['hrm_id' => 121, 'employee_code' => 'CHAM55',  'user_id' => 73,  'user_hrm_id' => 'CT051', 'name' => 'DINH THY LINH'],
        ['hrm_id' => 122, 'employee_code' => 'CHAM56',  'user_id' => 57,  'user_hrm_id' => 'CT035', 'name' => 'Nguyễn Hải Yến'],
        ['hrm_id' => 124, 'employee_code' => 'CHAM58',  'user_id' => 39,  'user_hrm_id' => 'CT022', 'name' => 'Hoàng Thành Đạt'],
        ['hrm_id' => 125, 'employee_code' => 'CHAM59',  'user_id' => 61,  'user_hrm_id' => 'CT039', 'name' => 'Nguyễn Văn Tùng'],
        ['hrm_id' => 128, 'employee_code' => 'CHAM62',  'user_id' => 65,  'user_hrm_id' => 'CT043', 'name' => 'Hà Thị Thiệp'],
        ['hrm_id' => 133, 'employee_code' => 'CHAM67',  'user_id' => 46,  'user_hrm_id' => 'CT024', 'name' => 'Nguyễn Xuân Đạt'],
        ['hrm_id' => 166, 'employee_code' => 'CHAM100', 'user_id' => 137, 'user_hrm_id' => 'CT087', 'name' => 'HOANG THI THANH HIEN'],
        ['hrm_id' => 171, 'employee_code' => 'CHAM105', 'user_id' => 357, 'user_hrm_id' => 'GV322', 'name' => 'DO THI DUYEN'],
        ['hrm_id' => 389, 'employee_code' => 'CHAM323', 'user_id' => 62,  'user_hrm_id' => 'CT040', 'name' => 'Bùi Thế Anh'],
        ['hrm_id' => 390, 'employee_code' => 'CHAM324', 'user_id' => 51,  'user_hrm_id' => 'CT029', 'name' => 'Đặng Thị Anh Thư'],
        ['hrm_id' => 391, 'employee_code' => 'CHAM325', 'user_id' => 45,  'user_hrm_id' => 'CT023', 'name' => 'Đặng Thị Hồng Anh'],
        ['hrm_id' => 395, 'employee_code' => 'CHAM329', 'user_id' => 67,  'user_hrm_id' => 'CT045', 'name' => 'Hà Thị Thu Hiền'],
        ['hrm_id' => 401, 'employee_code' => 'CHAM335', 'user_id' => 330, 'user_hrm_id' => 'CT235', 'name' => 'Nguyễn Thanh Hà'],
        ['hrm_id' => 402, 'employee_code' => 'CHAM336', 'user_id' => 269, 'user_hrm_id' => 'HO007', 'name' => 'Nguyễn Thị Kim Thoa'],
        ['hrm_id' => 404, 'employee_code' => 'CHAM338', 'user_id' => 58,  'user_hrm_id' => 'CT036', 'name' => 'Phùng Thùy Dương'],
    ];

    public function handle()
    {
        $isDryRun = (bool) $this->option('dry-run');

        $this->info("=========================================================");
        $this->info("       TIẾN TRÌNH ÁP DỤNG LIÊN KẾT NHÂN SỰ VÀ USERS      ");
        $this->info("=========================================================");
        if ($isDryRun) {
            $this->warn(">>> CHẾ ĐỘ DRY-RUN: Chỉ mô phỏng, không ghi thay đổi vào CSDL <<<");
        }

        DB::beginTransaction();

        try {
            $tableRows = [];
            $successCount = 0;

            foreach ($this->mappingList as $item) {
                $hrmId = $item['hrm_id'];
                $userId = $item['user_id'];
                $profile = HrmEmployeeProfile::find($hrmId);

                if (!$profile) {
                    $this->error("Không tìm thấy hồ sơ HRM ID = {$hrmId}");
                    continue;
                }

                $user = User::find($userId);
                if (!$user) {
                    $this->error("Không tìm thấy User ID = {$userId}");
                    continue;
                }

                // Xử lý chuyển nhượng user nếu hồ sơ khác đang giữ
                if (!empty($item['reassign_from_hrm_id'])) {
                    $oldProfile = HrmEmployeeProfile::find($item['reassign_from_hrm_id']);
                    if ($oldProfile && $oldProfile->user_id == $userId) {
                        $oldProfile->user_id = null;
                        $oldProfile->save();
                        $this->line("  [Gỡ liên kết cũ] Đã gỡ User {$userId} khỏi hồ sơ ID {$oldProfile->id} ({$oldProfile->employee_code})");
                    }
                }

                // Kiểm tra ràng buộc 1-1: user có đang bị profile khác giữ không
                $conflict = HrmEmployeeProfile::where('user_id', $userId)
                    ->where('id', '!=', $hrmId)
                    ->first();

                if ($conflict) {
                    $this->error("Xung đột: User {$userId} đang được gán cho hồ sơ ID {$conflict->id} ({$conflict->employee_code})");
                    continue;
                }

                $profile->user_id = $userId;
                $profile->save();

                $successCount++;
                $tableRows[] = [
                    'HRM ID' => $profile->id,
                    'Mã HRM' => $profile->employee_code,
                    'Họ tên HRM' => $profile->full_name,
                    'User ID' => $user->id,
                    'Mã User (hrm_id)' => $user->hrm_id,
                    'Tên User' => $user->name,
                    'Trạng thái' => 'Thành công'
                ];
            }

            if ($isDryRun) {
                DB::rollBack();
                $this->warn("\n>>> [DRY-RUN HOÀN TẤT] Đã kiểm tra mô phỏng thành công {$successCount}/" . count($this->mappingList) . " hồ sơ. DB được Rollback nguyên vẹn.");
            } else {
                DB::commit();
                $this->info("\n>>> [HOÀN TẤT THỰC THI] Đã cập nhật thành công {$successCount}/" . count($this->mappingList) . " hồ sơ vào CSDL!");
            }

            $this->table(['HRM ID', 'Mã HRM', 'Họ tên HRM', 'User ID', 'Mã User (hrm_id)', 'Tên User', 'Trạng thái'], $tableRows);

            return 0;
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("Lỗi trong quá trình xử lý: " . $e->getMessage());
            return 1;
        }
    }
}
