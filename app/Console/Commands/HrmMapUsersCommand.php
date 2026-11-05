<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\HrmEmployeeProfile;
use App\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class HrmMapUsersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hrm:map-users 
                            {--dry-run : Chạy kiểm tra mô phỏng, không ghi thay đổi vào DB} 
                            {--create-users : Tự động tạo User cho nhân sự chưa có User} 
                            {--force : Quét và map lại cả những hồ sơ đã được gán user_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Liên kết hồ sơ nhân sự HRM (hrm_employee_profiles) với bảng users';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $isDryRun = $this->option('dry-run');
        $createUsers = $this->option('create-users');
        $force = $this->option('force');

        $this->info("=================================================");
        $this->info("       TIẾN TRÌNH MAP NHÂN SỰ HRM VÀ USERS       ");
        $this->info("=================================================");
        if ($isDryRun) {
            $this->warn(">>> CHẾ ĐỘ DRY-RUN: Không có dữ liệu nào được ghi vào DB <<<");
        }

        // Lấy danh sách profiles cần map
        $query = HrmEmployeeProfile::query();
        if (!$force) {
            $query->whereNull('user_id');
        }
        $profiles = $query->orderBy('id', 'asc')->get();

        $this->info("Tổng số hồ sơ HRM cần xử lý: " . $profiles->count());

        // Lấy danh sách users không bị soft-deleted
        $allUsers = User::whereNull('deleted_at')->get();
        $this->info("Tổng số User đang có trong hệ thống: " . $allUsers->count());

        // Lấy danh sách user_id đã được profile khác map (để tránh 1 user map 2 profile)
        $mappedUserIds = HrmEmployeeProfile::whereNotNull('user_id')
            ->when(!$force, function ($q) {
                // Nếu không force, loại trừ profile hiện tại
            })
            ->pluck('user_id')
            ->toArray();

        // Helper chuẩn hóa số điện thoại
        $normalizePhone = function ($phone) {
            if (empty($phone)) return '';
            $p = preg_replace('/[^0-9]/', '', $phone);
            if (str_starts_with($p, '84') && strlen($p) >= 10) {
                $p = '0' . substr($p, 2);
            }
            return (strlen($p) >= 9) ? $p : '';
        };

        // Helper chuẩn hóa họ tên
        $normalizeName = function ($name) {
            $n = mb_strtolower(trim($name), 'UTF-8');
            return preg_replace('/\s+/', ' ', $n);
        };

        $matchedEmailCount = 0;
        $matchedPhoneCount = 0;
        $matchedNameCount = 0;
        $createdUserCount = 0;
        $unmatchedCount = 0;

        $resultsTable = [];
        $unmatchedList = [];

        DB::beginTransaction();

        try {
            foreach ($profiles as $profile) {
                $matchedUser = null;
                $matchMethod = '';

                // 1. Khớp theo Email chuẩn hóa
                if (!empty($profile->email) && !str_starts_with($profile->email, 'no_email_')) {
                    $cleanEmail = strtolower(trim($profile->email));
                    $candidates = $allUsers->filter(function ($u) use ($cleanEmail, $mappedUserIds) {
                        return !in_array($u->id, $mappedUserIds) && strtolower(trim($u->email)) === $cleanEmail;
                    });

                    if ($candidates->count() > 0) {
                        // Ưu tiên tài khoản đang hoạt động (status = 1)
                        $matchedUser = $candidates->where('status', 1)->first() ?: $candidates->first();
                        $matchMethod = 'Email';
                    }
                }

                // 2. Khớp theo Số điện thoại nếu chưa tìm thấy
                if (!$matchedUser && !empty($profile->phone)) {
                    $cleanPhone = $normalizePhone($profile->phone);
                    if (!empty($cleanPhone)) {
                        $candidates = $allUsers->filter(function ($u) use ($cleanPhone, $normalizePhone, $mappedUserIds) {
                            return !in_array($u->id, $mappedUserIds) && $normalizePhone($u->phone) === $cleanPhone;
                        });

                        if ($candidates->count() > 0) {
                            $matchedUser = $candidates->where('status', 1)->first() ?: $candidates->first();
                            $matchMethod = 'Số điện thoại';
                        }
                    }
                }

                // 3. Khớp theo Họ tên duy nhất (chỉ khi có đúng 1 user trùng tên)
                if (!$matchedUser && !empty($profile->full_name)) {
                    $cleanName = $normalizeName($profile->full_name);
                    $candidates = $allUsers->filter(function ($u) use ($cleanName, $normalizeName, $mappedUserIds) {
                        return !in_array($u->id, $mappedUserIds) && $normalizeName($u->name) === $cleanName;
                    });

                    if ($candidates->count() === 1) {
                        $matchedUser = $candidates->first();
                        $matchMethod = 'Họ tên duy nhất';
                    }
                }

                // Xử lý nếu match thành công
                if ($matchedUser) {
                    $mappedUserIds[] = $matchedUser->id;

                    if ($matchMethod === 'Email') $matchedEmailCount++;
                    elseif ($matchMethod === 'Số điện thoại') $matchedPhoneCount++;
                    elseif ($matchMethod === 'Họ tên duy nhất') $matchedNameCount++;

                    if (!$isDryRun) {
                        $profile->user_id = $matchedUser->id;
                        $profile->save();
                    }

                    if (count($resultsTable) < 25) {
                        $resultsTable[] = [
                            'Mã HRM' => $profile->employee_code,
                            'Tên HRM' => $profile->full_name,
                            'User ID' => $matchedUser->id,
                            'Tên User' => $matchedUser->name,
                            'Mã User cũ (hrm_id)' => $matchedUser->hrm_id,
                            'Phương thức' => $matchMethod
                        ];
                    }
                } else {
                    // Chưa match được
                    if ($createUsers) {
                        // Tự động tạo user mới cho nhân sự này
                        $newEmail = (!empty($profile->email) && !str_starts_with($profile->email, 'no_email_')) 
                            ? strtolower(trim($profile->email)) 
                            : (strtolower($profile->employee_code) . '@erp.local');

                        // Tránh duplicate email
                        if ($allUsers->contains('email', $newEmail)) {
                            $newEmail = strtolower($profile->employee_code) . '_' . time() . '@erp.local';
                        }

                        $newUser = null;
                        if (!$isDryRun) {
                            $newUser = User::create([
                                'name' => $profile->full_name,
                                'hrm_id' => $profile->employee_code, // Dùng luôn mã HRM làm hrm_id
                                'email' => $newEmail,
                                'phone' => $profile->phone,
                                'password' => Hash::make('12345678'),
                                'status' => 1,
                                'menuroles' => 'user',
                                'role_name' => 'Nhân viên',
                                'creator_id' => 1,
                            ]);

                            $profile->user_id = $newUser->id;
                            $profile->save();

                            $mappedUserIds[] = $newUser->id;
                        }

                        $createdUserCount++;

                        if (count($resultsTable) < 25) {
                            $resultsTable[] = [
                                'Mã HRM' => $profile->employee_code,
                                'Tên HRM' => $profile->full_name,
                                'User ID' => $newUser ? $newUser->id : '(Sẽ tạo)',
                                'Tên User' => $profile->full_name,
                                'Mã User cũ (hrm_id)' => $profile->employee_code,
                                'Phương thức' => 'Tạo mới User'
                            ];
                        }
                    } else {
                        $unmatchedCount++;
                        if (count($unmatchedList) < 15) {
                            $unmatchedList[] = [
                                'Mã HRM' => $profile->employee_code,
                                'Họ tên' => $profile->full_name,
                                'Email' => $profile->email,
                                'SĐT' => $profile->phone,
                            ];
                        }
                    }
                }
            }

            if ($isDryRun) {
                DB::rollBack();
            } else {
                DB::commit();
            }
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("Lỗi trong quá trình xử lý: " . $e->getMessage());
            return 1;
        }

        // In kết quả
        $this->line('');
        $this->info("=================== KẾT QUẢ XỬ LÝ ===================");
        $this->info("- Khớp theo Email:            " . $matchedEmailCount);
        $this->info("- Khớp theo Số điện thoại:     " . $matchedPhoneCount);
        $this->info("- Khớp theo Họ tên duy nhất:  " . $matchedNameCount);
        $totalMatched = $matchedEmailCount + $matchedPhoneCount + $matchedNameCount;
        $this->info("=> TỔNG SỐ KHỚP THÀNH CÔNG:   " . $totalMatched);

        if ($createUsers) {
            $this->info("- Tạo mới tài khoản User:     " . $createdUserCount);
        } else {
            $this->warn("- Chưa thể ghép nối:          " . $unmatchedCount . " (Dùng --create-users để tự động tạo tài khoản)");
        }
        $this->info("=====================================================");

        if (!empty($resultsTable)) {
            $this->line('');
            $this->info("Mẫu 25 bản ghi đã ghép nối thành công:");
            $this->table(['Mã HRM', 'Tên HRM', 'User ID', 'Tên User', 'Mã User cũ (hrm_id)', 'Phương thức'], $resultsTable);
        }

        if (!empty($unmatchedList) && !$createUsers) {
            $this->line('');
            $this->warn("Mẫu 15 bản ghi chưa tìm thấy User tương ứng:");
            $this->table(['Mã HRM', 'Họ tên', 'Email', 'SĐT'], $unmatchedList);
        }

        if ($isDryRun) {
            $this->line('');
            $this->warn("Đây là kết quả chạy thử (dry-run). Để thực thi ghi vào CSDL, hãy chạy lệnh bỏ cờ --dry-run.");
        } else {
            $this->line('');
            $this->info("ĐÃ CẬP NHẬT THÀNH CÔNG VÀO CƠ SỞ DỮ LIỆU!");
        }

        return 0;
    }
}
