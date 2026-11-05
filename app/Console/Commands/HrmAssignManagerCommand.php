<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\User;
use Illuminate\Support\Facades\DB;

class HrmAssignManagerCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hrm:assign-manager {user : ID, email hoặc hrm_id của nhân sự} {--remove : Thu hồi quyền Quản lý HRM}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Gán hoặc thu hồi vai trò Quản lý HRM (HRM Manager) cho người dùng';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $userInput = $this->argument('user');
        $isRemove = $this->option('remove');

        $user = User::where('id', $userInput)
            ->orWhere('email', $userInput)
            ->orWhere('hrm_id', $userInput)
            ->first();

        if (!$user) {
            $this->error("Không tìm thấy người dùng với thông tin: {$userInput}");
            return 1;
        }

        // Lấy role hrm_manager
        $role = DB::table('roles')->where('name', 'hrm_manager')->first();
        if (!$role) {
            $roleId = DB::table('roles')->insertGetId([
                'name' => 'hrm_manager',
                'code' => 'hrm_manager',
                'description' => 'Quản lý Nhân sự (HRM Manager)',
                'guard_name' => 'api',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'creator_id' => 1
            ]);
        } else {
            $roleId = $role->id;
        }

        // Lấy permission hrm_admin
        $perm = DB::table('permissions')->where('name', 'hrm_admin')->first();
        $permId = $perm ? $perm->id : null;

        if ($isRemove) {
            DB::table('role_has_user')->where('user_id', $user->id)->where('role_id', $roleId)->delete();
            $this->info("Đã thu hồi vai trò 'Quản lý HRM' (hrm_manager) của người dùng: [ID: {$user->id}] {$user->name} ({$user->email})");
        } else {
            $exists = DB::table('role_has_user')->where('user_id', $user->id)->where('role_id', $roleId)->exists();
            if (!$exists) {
                DB::table('role_has_user')->insert([
                    'user_id' => $user->id,
                    'role_id' => $roleId
                ]);
            }
            $this->info("Đã gán vai trò 'Quản lý HRM' (hrm_manager) thành công cho: [ID: {$user->id}] {$user->name} ({$user->email})");
            $this->line("Người dùng này hiện có quyền xem ALL và toàn quyền quản trị module HRM.");
        }

        return 0;
    }
}

