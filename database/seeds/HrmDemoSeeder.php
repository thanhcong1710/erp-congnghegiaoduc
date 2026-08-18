<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\User;
use App\Models\HrmDepartment;
use App\Models\HrmJobLevel;
use App\Models\HrmJobTitle;
use App\Models\HrmEmployeeProfile;
use App\Models\HrmLeaveType;
use App\Models\HrmShift;
use Carbon\Carbon;

class HrmDemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Delete existing HRM demo data
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('hrm_shift_assignments')->truncate();
        DB::table('hrm_shifts')->truncate();
        DB::table('hrm_attendances')->truncate();
        DB::table('hrm_overtime_requests')->truncate();
        DB::table('hrm_leave_requests')->truncate();
        DB::table('hrm_leave_balances')->truncate();
        DB::table('hrm_leave_types')->truncate();
        DB::table('hrm_employment_history')->truncate();
        DB::table('hrm_employee_documents')->truncate();
        DB::table('hrm_employee_profiles')->truncate();
        DB::table('hrm_job_titles')->truncate();
        DB::table('hrm_job_levels')->truncate();
        DB::table('hrm_departments')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 1. Departments
        $deptTech = HrmDepartment::create(['name' => 'Khối Công nghệ', 'description' => 'Khối phát triển sản phẩm công nghệ', 'status' => 1]);
        $deptBackend = HrmDepartment::create(['name' => 'Phòng Backend', 'parent_id' => $deptTech->id, 'description' => 'Phát triển Backend', 'status' => 1]);
        $deptFrontend = HrmDepartment::create(['name' => 'Phòng Frontend', 'parent_id' => $deptTech->id, 'description' => 'Phát triển Giao diện', 'status' => 1]);
        $deptBiz = HrmDepartment::create(['name' => 'Khối Kinh doanh', 'description' => 'Phát triển kinh doanh', 'status' => 1]);
        $deptSales = HrmDepartment::create(['name' => 'Phòng Sales', 'parent_id' => $deptBiz->id, 'description' => 'Bán hàng', 'status' => 1]);

        // 2. Job Levels
        $levelJunior = HrmJobLevel::create(['name' => 'Junior', 'description' => 'Nhân viên mới/ít kinh nghiệm', 'status' => 1]);
        $levelMid = HrmJobLevel::create(['name' => 'Mid-Level', 'description' => 'Nhân viên cứng', 'status' => 1]);
        $levelSenior = HrmJobLevel::create(['name' => 'Senior', 'description' => 'Chuyên gia', 'status' => 1]);
        $levelManager = HrmJobLevel::create(['name' => 'Manager', 'description' => 'Quản lý', 'status' => 1]);

        // 3. Job Titles
        $titleDev = HrmJobTitle::create(['name' => 'Lập trình viên', 'description' => 'Software Engineer', 'status' => 1]);
        $titleLead = HrmJobTitle::create(['name' => 'Tech Lead', 'description' => 'Trưởng nhóm kỹ thuật', 'status' => 1]);
        $titleSales = HrmJobTitle::create(['name' => 'Chuyên viên Kinh doanh', 'description' => 'Sales Executive', 'status' => 1]);

        // 4. Leave Types
        $leaveAnnual = HrmLeaveType::create(['name' => 'Nghỉ phép năm', 'days_per_year' => 12, 'is_paid' => 1, 'status' => 1]);
        $leaveSick = HrmLeaveType::create(['name' => 'Nghỉ ốm', 'days_per_year' => 0, 'is_paid' => 0, 'status' => 1]);
        $leaveUnpaid = HrmLeaveType::create(['name' => 'Nghỉ không lương', 'days_per_year' => 0, 'is_paid' => 0, 'status' => 1]);

        // 5. Shifts
        $shiftMorning = HrmShift::create(['name' => 'Ca Sáng', 'start_time' => '08:00:00', 'end_time' => '12:00:00', 'status' => 1]);
        $shiftAfternoon = HrmShift::create(['name' => 'Ca Chiều', 'start_time' => '13:00:00', 'end_time' => '17:00:00', 'status' => 1]);
        $shiftFull = HrmShift::create(['name' => 'Ca Hành Chính', 'start_time' => '08:00:00', 'end_time' => '17:00:00', 'status' => 1]);

        // Get 5 existing users to map to employees
        $users = User::limit(5)->get();
        
        $today = Carbon::today();

        if ($users->count() > 0) {
            foreach ($users as $index => $user) {
                // Determine roles based on index
                $deptId = ($index % 2 == 0) ? $deptBackend->id : $deptSales->id;
                $levelId = ($index == 0) ? $levelManager->id : $levelMid->id;
                $titleId = ($index % 2 == 0) ? $titleDev->id : $titleSales->id;

                // Create Employee Profile
                HrmEmployeeProfile::create([
                    'user_id' => $user->id,
                    'department_id' => $deptId,
                    'job_level_id' => $levelId,
                    'job_title_id' => $titleId,
                    'employee_code' => 'EMP' . str_pad($user->id, 4, '0', STR_PAD_LEFT),
                    'start_date' => Carbon::now()->subMonths(rand(1, 24))->format('Y-m-d'),
                    'contract_type' => 'Full-time'
                ]);

                // Shift Assignment
                DB::table('hrm_shift_assignments')->insert([
                    'user_id' => $user->id,
                    'shift_id' => $shiftFull->id,
                    'date' => Carbon::now()->startOfMonth()->format('Y-m-d'),
                    'is_overtime' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Create some dummy attendances (last 5 days)
                for ($i = 1; $i <= 5; $i++) {
                    $date = Carbon::today()->subDays($i);
                    // skip weekends
                    if ($date->isWeekend()) continue;

                    $checkIn = $date->copy()->setTime(rand(7, 8), rand(45, 59));
                    $checkOut = $date->copy()->setTime(rand(17, 18), rand(0, 30));

                    DB::table('hrm_attendances')->insert([
                        'user_id' => $user->id,
                        'date' => $date->format('Y-m-d'),
                        'check_in' => $checkIn,
                        'check_out' => $checkOut,
                        'status' => 1, // Hợp lệ
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                // Create Leave Request
                if ($index % 2 == 0) {
                    DB::table('hrm_leave_requests')->insert([
                        'user_id' => $user->id,
                        'leave_type_id' => $leaveAnnual->id,
                        'type' => 'vacation', // Legacy column
                        'start_date' => Carbon::today()->addDays(2)->format('Y-m-d'),
                        'end_date' => Carbon::today()->addDays(3)->format('Y-m-d'),
                        'reason' => 'Nghỉ đi du lịch cùng gia đình',
                        'status' => ($index == 0) ? 1 : 0, // 1 = Approved, 0 = Pending
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                // Create Overtime Request
                if ($index % 3 == 0) {
                    DB::table('hrm_overtime_requests')->insert([
                        'user_id' => $user->id,
                        'date' => Carbon::today()->subDays(1)->format('Y-m-d'),
                        'start_time' => '17:30:00',
                        'end_time' => '20:30:00',
                        'reason' => 'Fix bug server production',
                        'status' => 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                // Create Employment History
                if ($index < 3) {
                    DB::table('hrm_employment_history')->insert([
                        'user_id' => $user->id,
                        'type' => 'Thăng tiến',
                        'from_department_id' => $deptId,
                        'to_department_id' => $deptId,
                        'from_position_id' => null, // Placeholder if no position table
                        'to_position_id' => null,
                        'from_salary' => 10000000 + ($index * 2000000),
                        'to_salary' => 15000000 + ($index * 3000000),
                        'effective_date' => Carbon::today()->subMonths(6)->format('Y-m-d'),
                        'note' => 'Được thăng chức do hoàn thành xuất sắc dự án',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                // Create Employee Document
                if ($index < 3) {
                    DB::table('hrm_employee_documents')->insert([
                        'user_id' => $user->id,
                        'document_type' => 'Hợp đồng lao động',
                        'title' => 'HĐLĐ_Chính_thức_' . $user->id,
                        'file_path' => '/storage/documents/hop_dong_' . $user->id . '.pdf',
                        'expiry_date' => Carbon::today()->addYears(1)->format('Y-m-d'),
                        'note' => 'Hợp đồng lao động chính thức 1 năm',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    DB::table('hrm_employee_documents')->insert([
                        'user_id' => $user->id,
                        'document_type' => 'Chứng minh nhân dân',
                        'title' => 'CMND_' . $user->id,
                        'file_path' => '/storage/documents/cmnd_' . $user->id . '.jpg',
                        'expiry_date' => null,
                        'note' => 'Bản sao y công chứng',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }
}
