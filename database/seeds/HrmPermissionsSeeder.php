<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HrmPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Add Permission Group
        $groupId = DB::table('permission_groups')->insertGetId([
            'name' => 'HRM',
            'description' => 'Quản lý nhân sự',
            'status' => 1,
            'display_order' => 4,
        ]);

        // Add Permissions
        $permissions = [
            [
                'name' => 'HRM',
                'description' => 'Module Quản lý nhân sự',
                'group_id' => $groupId,
                'display_order' => 1,
                'status' => 1,
                'parent_id' => 0,
            ],
            [
                'name' => 'hrm_departments',
                'description' => 'Quản lý Phòng ban',
                'group_id' => $groupId,
                'display_order' => 2,
                'status' => 1,
                'parent_id' => 0,
            ],
            [
                'name' => 'hrm_employees',
                'description' => 'Quản lý Nhân viên',
                'group_id' => $groupId,
                'display_order' => 3,
                'status' => 1,
                'parent_id' => 0,
            ],
            [
                'name' => 'hrm_leaves',
                'description' => 'Quản lý Nghỉ phép',
                'group_id' => $groupId,
                'display_order' => 4,
                'status' => 1,
                'parent_id' => 0,
            ],
        ];

        DB::table('permissions')->insert($permissions);
    }
}
