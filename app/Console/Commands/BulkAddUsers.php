<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Providers\UtilityServiceProvider as u;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class BulkAddUsers extends Command
{
    protected $signature = 'user:bulk-add';
    protected $description = 'Bulk add users from the predefined list';

    public function handle()
    {
        $names = [
            "Quang Huy Meca", "Thu Phương Meca", "Team Đạt", "Hồng Anh Meca", "Xuân Đạt Meca",
            "Thái Bảo Meca", "Hồng Vân", "Team Hà Hiền", "Tiến Dũng Meca", "Anh Thư Meca",
            "Minh TD", "Team Phương", "PAGE", "Nguồn ngoài", "Ngô Đức Meca",
            "Hải Yến Meca", "Dương Meca", "Thu Hà Meca", "Thanh Nga Meca", "Tùng Meca",
            "Thế Anh Meca", "Diễm Quỳnh Meca", "Hải Anh Meca", "Hà Thiệp Meca", "Hương Trúc Meca",
            "Thu Hiền Meca", "Uyên LP", "Mỹ Linh LP", "Nguyệt LP", "Yến Nhi Meca",
            "Ngọc Anh Meca", "Đinh Linh Meca", "Nguyễn Yến Meca"
        ];

        $branch_id = 9;
        $branchInfo = DB::table('branches')->where('id', $branch_id)->first();
        if (!$branchInfo) {
            $this->error("Branch ID 9 not found.");
            return;
        }
        $branch_name = $branchInfo->name;

        $role_description = "Trưởng nhóm Kinh doanh";
        $roleInfo = DB::table('roles')->where('description', $role_description)->first();
        if (!$roleInfo) {
            // Fallback to searching by name or SystemCode if possible, but user specified by description
            $roleInfo = DB::table('roles')->where('name', 'LIKE', '%Teacher Leader%')->first();
            if (!$roleInfo) {
                // If still not found, check ID 37 which is ROLE_TEACHER_LEADER in SystemCode
                $roleInfo = DB::table('roles')->where('id', 37)->first();
            }
        }

        if (!$roleInfo) {
            $this->error("Role 'Trưởng nhóm Kinh doanh' not found.");
            return;
        }
        $role_id = $roleInfo->id;
        $role_name = $roleInfo->description ?: $roleInfo->name;

        $start_id = 20;
        $password = Hash::make('12345678@');

        foreach ($names as $index => $name) {
            $hrm_number = str_pad($start_id + $index, 3, '0', STR_PAD_LEFT);
            $hrm_id = "CT" . $hrm_number;

            // Generate a simple email
            $email = strtolower($hrm_id) . "@9gate.vn";

            // Check if user exists
            $exists = DB::table('users')->where('hrm_id', $hrm_id)->exists();
            if ($exists) {
                $this->warn("User $hrm_id already exists. Skipping.");
                continue;
            }

            $user_id = u::insertSimpleRow([
                'name' => $name,
                'hrm_id' => $hrm_id,
                'email' => $email,
                'password' => $password,
                'status' => 1,
                'role_name' => $role_name,
                'branch_id' => $branch_id,
                'branch_name' => $branch_name,
                'created_at' => date('Y-m-d H:i:s'),
                'creator_id' => 1 // Assuming admin ID is 1
            ], 'users');

            if ($user_id) {
                // Insert into role_has_user
                u::insertSimpleRow([
                    'role_id' => $role_id,
                    'user_id' => $user_id
                ], 'role_has_user');

                // Insert into branch_has_user
                u::insertSimpleRow([
                    'branch_id' => $branch_id,
                    'user_id' => $user_id
                ], 'branch_has_user');

                $this->info("Successfully added user: $name ($hrm_id)");
            }
            else {
                $this->error("Failed to add user: $name");
            }
        }

        $this->info("Bulk addition completed.");
    }
}