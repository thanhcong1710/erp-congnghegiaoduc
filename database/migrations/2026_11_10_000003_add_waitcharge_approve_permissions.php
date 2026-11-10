<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddWaitchargeApprovePermissions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 1. Cập nhật hoặc thêm permission 80: lms_waitcharge_approve (Duyệt phí)
        $perm80 = DB::table('permissions')->where('id', 80)->orWhere('name', 'lms_waitcharge_approve')->first();
        if ($perm80) {
            DB::table('permissions')->where('id', $perm80->id)->update([
                'name' => 'lms_waitcharge_approve',
                'description' => 'Duyệt phí',
                'group_id' => 2,
                'status' => 1,
            ]);
            $perm80Id = $perm80->id;
        } else {
            $perm80Id = DB::table('permissions')->insertGetId([
                'id' => 80,
                'name' => 'lms_waitcharge_approve',
                'description' => 'Duyệt phí',
                'group_id' => 2,
                'display_order' => 12,
                'status' => 1,
            ]);
        }

        // 2. Cập nhật hoặc thêm permission 81: approve_add_fee (Phê duyệt đóng phí)
        $perm81 = DB::table('permissions')->where('id', 81)->orWhere('name', 'approve_add_fee')->first();
        if ($perm81) {
            DB::table('permissions')->where('id', $perm81->id)->update([
                'name' => 'approve_add_fee',
                'description' => 'Phê duyệt đóng phí',
                'group_id' => 8,
                'status' => 1,
            ]);
            $perm81Id = $perm81->id;
        } else {
            $perm81Id = DB::table('permissions')->insertGetId([
                'id' => 81,
                'name' => 'approve_add_fee',
                'description' => 'Phê duyệt đóng phí',
                'group_id' => 8,
                'display_order' => 1,
                'status' => 1,
            ]);
        }

        // 3. Gán quyền xem danh sách duyệt phí (lms_waitcharge_approve) cho:
        // - Admin (999999)
        // - Giám đốc trung tâm / CEO (686868)
        // - Kế toán (1000002)
        // - Sale / EC (68)
        // - Sale Leader / EC Leader (69)
        $viewRoles = [999999, 686868, 1000002];
        foreach ($viewRoles as $roleId) {
            $exists = DB::table('permission_has_role')
                ->where('role_id', $roleId)
                ->where('permission_id', $perm80Id)
                ->exists();
            if (!$exists) {
                DB::table('permission_has_role')->insert([
                    'role_id' => $roleId,
                    'permission_id' => $perm80Id,
                ]);
            }
        }

        // 4. Gán quyền phê duyệt/từ chối đóng phí (approve_add_fee) cho:
        // - Admin (999999)
        // - Giám đốc trung tâm / CEO (686868)
        // - Kế toán (1000002)
        // (Không cấp cho Sale 68/69 để Sale chỉ xem được danh sách phiếu đã tạo mà không duyệt được)
        $approveRoles = [999999, 686868, 1000002];
        foreach ($approveRoles as $roleId) {
            $exists = DB::table('permission_has_role')
                ->where('role_id', $roleId)
                ->where('permission_id', $perm81Id)
                ->exists();
            if (!$exists) {
                DB::table('permission_has_role')->insert([
                    'role_id' => $roleId,
                    'permission_id' => $perm81Id,
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Có thể rollback các bản ghi nếu cần
    }
}

