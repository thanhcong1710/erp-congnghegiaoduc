<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SetupSaleTargetsAndDongAnhUsers extends Migration
{
    public function up()
    {
        // 1. Tạo bảng sale_targets lưu chỉ tiêu (KPI) tháng cho từng nhân sự Sale / Team
        if (!Schema::hasTable('sale_targets')) {
            Schema::create('sale_targets', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->index();
                $table->unsignedInteger('branch_id')->default(0)->index();
                $table->unsignedSmallInteger('year')->index();
                $table->unsignedTinyInteger('month')->index();
                $table->decimal('target_amount', 15, 2)->default(0);
                $table->string('note', 255)->nullable();
                $table->unsignedBigInteger('updated_by')->nullable();
                $table->timestamps();
                $table->unique(['user_id', 'year', 'month'], 'uniq_user_year_month');
            });
        }

        // Đảm bảo cột menuroles có default 'user' để tránh lỗi khi thêm mới user
        try {
            DB::statement("ALTER TABLE `users` MODIFY COLUMN `menuroles` VARCHAR(255) DEFAULT 'user'");
        } catch (\Throwable $e) {
            // ignore
        }

        // 2. Xác định cơ sở Đông Anh (CS2 — Eurowindow River Park, id = 2)
        $branch = DB::table('branches')->where('id', 2)->first();
        if (!$branch) {
            $branch = DB::table('branches')->where('status', 1)->first();
        }
        $branchId = $branch ? $branch->id : 2;
        $branchName = $branch ? $branch->name : 'CS2 — Eurowindow River Park';

        // 3. Tạo / cập nhật Trưởng nhóm Kinh doanh: Đỗ Thị Thảo Nguyên (role_id = 69)
        $leader = DB::table('users')->where('name', 'Đỗ Thị Thảo Nguyên')->orWhere('hrm_id', 'DA_TN01')->first();
        if (!$leader) {
            $leaderId = DB::table('users')->insertGetId([
                'name' => 'Đỗ Thị Thảo Nguyên',
                'phone' => '0988000001',
                'email' => 'thaonguyen@stemkey.vn',
                'hrm_id' => 'DA_TN01',
                'password' => Hash::make('123456'),
                'menuroles' => 'user',
                'status' => 1,
                'manager_hrm_id' => null,
                'manager_id' => null,
                'role_name' => 'Trưởng nhóm Kinh doanh',
                'branch_id' => $branchId,
                'branch_name' => $branchName,
                'created_at' => now(),
                'updated_at' => now(),
                'creator_id' => 1,
            ]);
        } else {
            $leaderId = $leader->id;
            DB::table('users')->where('id', $leaderId)->update([
                'name' => 'Đỗ Thị Thảo Nguyên',
                'role_name' => 'Trưởng nhóm Kinh doanh',
                'branch_id' => $branchId,
                'branch_name' => $branchName,
                'status' => 1,
                'updated_at' => now(),
            ]);
            $leader = DB::table('users')->where('id', $leaderId)->first();
        }

        $leaderHrmId = $leader && !empty($leader->hrm_id) ? $leader->hrm_id : 'DA_TN01';

        DB::table('role_has_user')->where('user_id', $leaderId)->delete();
        DB::table('role_has_user')->insert([
            'role_id' => 69, // Trưởng nhóm Kinh doanh
            'user_id' => $leaderId,
        ]);

        DB::table('branch_has_user')->where('user_id', $leaderId)->delete();
        DB::table('branch_has_user')->insert([
            'branch_id' => $branchId,
            'user_id' => $leaderId,
        ]);

        // 4. Tạo / cập nhật Sale: Nguyễn Thanh Hà (role_id = 68), thuộc Team Đỗ Thị Thảo Nguyên
        $sale = DB::table('users')->where('name', 'Nguyễn Thanh Hà')->orWhere('hrm_id', 'DA_SALE01')->first();
        if (!$sale) {
            $saleId = DB::table('users')->insertGetId([
                'name' => 'Nguyễn Thanh Hà',
                'phone' => '0988000002',
                'email' => 'thanhha@stemkey.vn',
                'hrm_id' => 'DA_SALE01',
                'password' => Hash::make('123456'),
                'menuroles' => 'user',
                'status' => 1,
                'manager_hrm_id' => $leaderHrmId,
                'manager_id' => $leaderId,
                'role_name' => 'Nhân viên kinh doanh',
                'branch_id' => $branchId,
                'branch_name' => $branchName,
                'created_at' => now(),
                'updated_at' => now(),
                'creator_id' => 1,
            ]);
        } else {
            $saleId = $sale->id;
            DB::table('users')->where('id', $saleId)->update([
                'name' => 'Nguyễn Thanh Hà',
                'manager_hrm_id' => $leaderHrmId,
                'manager_id' => $leaderId,
                'role_name' => 'Nhân viên kinh doanh',
                'branch_id' => $branchId,
                'branch_name' => $branchName,
                'status' => 1,
                'updated_at' => now(),
            ]);
        }

        DB::table('role_has_user')->where('user_id', $saleId)->delete();
        DB::table('role_has_user')->insert([
            'role_id' => 68, // Nhân viên kinh doanh (Sale)
            'user_id' => $saleId,
        ]);

        DB::table('branch_has_user')->where('user_id', $saleId)->delete();
        DB::table('branch_has_user')->insert([
            'branch_id' => $branchId,
            'user_id' => $saleId,
        ]);

        // 5. Đảm bảo quyền truy cập cho Role 68 (Sale) và Role 69 (Trưởng nhóm KD)
        // Các quyền cần thiết: crm_parent (1), lms_students (3), lms_contracts (5), lms_enrolments (6), dashboard (24), lms_reports (28), lms_reports_business (41), lms_reports_business_01 (48)
        $requiredPermissions = [1, 3, 5, 6, 24, 28, 41, 48];
        foreach ([68, 69] as $rId) {
            foreach ($requiredPermissions as $pId) {
                $exists = DB::table('permission_has_role')
                    ->where('role_id', $rId)
                    ->where('permission_id', $pId)
                    ->exists();
                if (!$exists) {
                    DB::table('permission_has_role')->insert([
                        'role_id' => $rId,
                        'permission_id' => $pId,
                    ]);
                }
            }
            // Đảm bảo Sale (68) và Trưởng nhóm KD (69) không có quyền canViewAllParents (32) và canViewAllSale (31)
            // cũng như không có quyền Kế toán: lms_charges (26), lms_waitcharges (27), lms_waitcharge_approve (80), approve_add_fee (81)
            DB::table('permission_has_role')
                ->where('role_id', $rId)
                ->whereIn('permission_id', [26, 27, 31, 32, 80, 81])
                ->delete();
        }

        // 6. Khởi tạo KPI mẫu tháng hiện tại cho Đỗ Thị Thảo Nguyên và Nguyễn Thanh Hà nếu chưa có
        $currentYear = (int)date('Y');
        $currentMonth = (int)date('n');
        foreach ([
            ['user_id' => $leaderId, 'target' => 150000000],
            ['user_id' => $saleId, 'target' => 80000000],
        ] as $item) {
            $hasTarget = DB::table('sale_targets')
                ->where('user_id', $item['user_id'])
                ->where('year', $currentYear)
                ->where('month', $currentMonth)
                ->exists();
            if (!$hasTarget) {
                DB::table('sale_targets')->insert([
                    'user_id' => $item['user_id'],
                    'branch_id' => $branchId,
                    'year' => $currentYear,
                    'month' => $currentMonth,
                    'target_amount' => $item['target'],
                    'note' => 'Chỉ tiêu doanh số tháng ' . $currentMonth . '/' . $currentYear,
                    'updated_by' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function down()
    {
        Schema::dropIfExists('sale_targets');
    }
}
