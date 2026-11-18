<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * - Thêm role Marketing (id = code = 70).
 * - Chuẩn hoá quyền role Giáo viên (36) & Marketing (70).
 * - Tạo 2 tài khoản demo cho màn đăng nhập nhanh: S006 (Giáo viên), S007 (Marketing) – mật khẩu @12345678, cơ sở CS1.
 */
class AddMarketingRoleAndDemoAccounts extends Migration
{
    const ROLE_TEACHER = 36;
    const ROLE_MARKETING = 70;

    /** Giáo viên: dashboard (TKB), điểm danh, nhận xét & đánh giá, báo cáo học thuật/giáo viên. Không xem CRM, hợp đồng, tài chính. */
    const TEACHER_PERMS = [
        'dashboard', 'lms_attendances', 'lms_assessments',
        'lms_reports', 'lms_reports_academic', 'lms_reports_academic_01', 'lms_reports_academic_02',
        'lms_reports_teacher', 'lms_reports_teacher_01',
    ];

    /** Marketing: chiến dịch, B2B/C2C, coupon, nguồn KH, import data, báo cáo kinh doanh theo nguồn (toàn trung tâm). Không xem/sửa hợp đồng, phiếu thu. */
    const MARKETING_PERMS = [
        'dashboard',
        'marketing_campaigns', 'marketing_b2b_sources', 'marketing_b2b_campaigns', 'marketing_c2c_campaigns', 'marketing_coupons',
        'settings_sources', 'crm_imports', 'canViewAllImport',
        'lms_reports', 'lms_reports_business', 'lms_reports_business_01', 'canViewAllSale',
    ];

    private function setPerms($roleId, array $names)
    {
        $ids = DB::table('permissions')->whereIn('name', $names)->pluck('id')->all();
        DB::table('permission_has_role')->where('role_id', $roleId)->delete();
        DB::table('permission_has_role')->insert(array_map(function ($pid) use ($roleId) {
            return ['role_id' => $roleId, 'permission_id' => $pid];
        }, $ids));
    }

    private function upsertUser($hrmId, array $data, $roleId, $branchId)
    {
        $existing = DB::table('users')->where('hrm_id', $hrmId)->first();
        $branch = DB::table('branches')->where('id', $branchId)->first();
        $row = array_merge($data, [
            'hrm_id' => $hrmId,
            'menuroles' => 'user',
            'status' => 1,
            'branch_id' => $branchId,
            'branch_name' => $branch->name ?? null,
            'updated_at' => now(),
        ]);
        if ($existing) {
            DB::table('users')->where('id', $existing->id)->update($row);
            $id = $existing->id;
        } else {
            $row['password'] = Hash::make('@12345678');
            $row['created_at'] = now();
            $row['creator_id'] = 1;
            $id = DB::table('users')->insertGetId($row);
        }
        DB::table('role_has_user')->where('user_id', $id)->delete();
        DB::table('role_has_user')->insert(['role_id' => $roleId, 'user_id' => $id]);
        DB::table('branch_has_user')->where('user_id', $id)->delete();
        DB::table('branch_has_user')->insert(['branch_id' => $branchId, 'user_id' => $id]);
        return $id;
    }

    public function up()
    {
        if (!DB::table('roles')->where('id', self::ROLE_MARKETING)->exists()) {
            DB::table('roles')->insert([
                'id' => self::ROLE_MARKETING, 'code' => (string)self::ROLE_MARKETING, 'name' => 'marketing',
                'description' => 'Nhân viên Marketing', 'guard_name' => 'api', 'created_at' => now(), 'creator_id' => 1,
            ]);
        }
        $this->setPerms(self::ROLE_TEACHER, self::TEACHER_PERMS);
        $this->setPerms(self::ROLE_MARKETING, self::MARKETING_PERMS);

        $branchId = DB::table('branches')->where('id', 1)->exists() ? 1 : (int)DB::table('branches')->where('status', 1)->value('id');
        $this->upsertUser('S006', [
            'name' => 'Giáo viên Demo', 'email' => 'giaovien.demo@stemkey.vn', 'phone' => '0988000006', 'role_name' => 'Giáo viên',
        ], self::ROLE_TEACHER, $branchId);
        $this->upsertUser('S007', [
            'name' => 'Marketing Demo', 'email' => 'marketing.demo@stemkey.vn', 'phone' => '0988000007', 'role_name' => 'Nhân viên Marketing',
        ], self::ROLE_MARKETING, $branchId);
    }

    public function down()
    {
        $ids = DB::table('users')->whereIn('hrm_id', ['S006', 'S007'])->pluck('id')->all();
        DB::table('role_has_user')->whereIn('user_id', $ids)->delete();
        DB::table('branch_has_user')->whereIn('user_id', $ids)->delete();
        DB::table('users')->whereIn('id', $ids)->update(['status' => 0]);
        DB::table('permission_has_role')->where('role_id', self::ROLE_MARKETING)->delete();
        DB::table('roles')->where('id', self::ROLE_MARKETING)->delete();
    }
}
