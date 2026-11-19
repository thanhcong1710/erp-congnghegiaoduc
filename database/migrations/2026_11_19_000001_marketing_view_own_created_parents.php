<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Marketing (role 70) được xem menu Khách hàng nhưng CHỈ thấy khách hàng do mình tạo (crm_parents.creator_id):
 * - Thêm permission `crm_parent_own_created` (cùng nhóm với canViewAllParents).
 * - Gán `crm_parent` + `crm_parent_own_created` cho role Marketing.
 */
class MarketingViewOwnCreatedParents extends Migration
{
    const ROLE_MARKETING = 70;
    const PERM = 'crm_parent_own_created';

    public function up()
    {
        if (!DB::table('permissions')->where('name', self::PERM)->exists()) {
            $groupId = DB::table('permissions')->where('name', 'canViewAllParents')->value('group_id') ?: 1;
            DB::table('permissions')->insert([
                'name' => self::PERM,
                'description' => 'Chỉ xem khách hàng do mình tạo',
                'group_id' => $groupId,
                'status' => 1,
            ]);
        }
        $ids = DB::table('permissions')->whereIn('name', ['crm_parent', self::PERM])->pluck('id')->all();
        foreach ($ids as $pid) {
            $exists = DB::table('permission_has_role')->where(['role_id' => self::ROLE_MARKETING, 'permission_id' => $pid])->exists();
            if (!$exists) DB::table('permission_has_role')->insert(['role_id' => self::ROLE_MARKETING, 'permission_id' => $pid]);
        }
    }

    public function down()
    {
        $ids = DB::table('permissions')->whereIn('name', ['crm_parent', self::PERM])->pluck('id')->all();
        DB::table('permission_has_role')->where('role_id', self::ROLE_MARKETING)->whereIn('permission_id', $ids)->delete();
        DB::table('permissions')->where('name', self::PERM)->delete();
    }
}
