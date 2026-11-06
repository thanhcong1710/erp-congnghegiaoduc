<?php

namespace App\Services\Hrm;

use App\Models\HrmDepartment;
use App\Models\HrmEmployeeProfile;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HrmScopeService
{
    /**
     * Kiểm tra user có quyền quản trị HRM tối cao hay không (Xem ALL).
     */
    public static function isHrmAdmin(?User $user = null): bool
    {
        $user = $user ?: Auth::user();
        if (!$user) {
            return false;
        }

        if ((int)$user->id === 1) {
            return true;
        }

        if (method_exists($user, 'isHrmAdmin')) {
            return $user->isHrmAdmin();
        }

        if (method_exists($user, 'hasAnyRole') && $user->hasAnyRole(['admin', 'hrm_manager', 999999, 1000004])) {
            return true;
        }

        if (method_exists($user, 'checkPermission') && $user->checkPermission('hrm_admin')) {
            return true;
        }

        return false;
    }

    /**
     * Kiểm tra user có vai trò Leader / Quản lý / Trưởng phòng hay không.
     */
    public static function isLeader(?User $user = null): bool
    {
        $user = $user ?: Auth::user();
        if (!$user) {
            return false;
        }

        if (self::isHrmAdmin($user)) {
            return true;
        }

        if (method_exists($user, 'isLeader') && $user->isLeader()) {
            return true;
        }

        // Kiểm tra xem có cấp dưới trong bảng users hay không
        if (User::where('manager_id', $user->id)->exists()) {
            return true;
        }

        // Kiểm tra xem có được gán là manager của phòng ban nào không
        if (HrmDepartment::where('manager_id', $user->id)->exists()) {
            return true;
        }

        return false;
    }

    /**
     * Lấy toàn bộ danh sách ID cấp dưới trực tiếp và gián tiếp (bao gồm cả chính mình).
     * Dựa trên cây users.manager_id
     *
     * @return int[]
     */
    public static function getSubordinateUserIds(?User $user = null): array
    {
        $user = $user ?: Auth::user();
        if (!$user) {
            return [];
        }

        $allUsers = DB::table('users')->select('id', 'manager_id')->get();
        $subordinateIds = self::buildUserTree($allUsers, (int)$user->id);
        $subordinateIds[] = (int)$user->id;

        return array_values(array_unique(array_filter($subordinateIds)));
    }

    /**
     * Hàm đệ quy duyệt cây users theo manager_id
     */
    private static function buildUserTree($users, int $managerId): array
    {
        $result = [];
        foreach ($users as $u) {
            if ((int)$u->manager_id === $managerId && (int)$u->id !== $managerId) {
                $result[] = (int)$u->id;
                $children = self::buildUserTree($users, (int)$u->id);
                if (!empty($children)) {
                    $result = array_merge($result, $children);
                }
            }
        }
        return $result;
    }

    /**
     * Lấy danh sách ID phòng ban mà user có quyền truy cập (bao gồm cả các phòng ban / team con).
     *
     * @return int[]
     */
    public static function getAccessibleDepartmentIds(?User $user = null): array
    {
        $user = $user ?: Auth::user();
        if (!$user) {
            return [];
        }

        // Admin HRM được quyền xem tất cả phòng ban
        if (self::isHrmAdmin($user)) {
            return HrmDepartment::pluck('id')->all();
        }

        $directDeptIds = [];

        // 1. Các phòng ban do user trực tiếp làm manager_id
        $managedDeptIds = HrmDepartment::where('manager_id', $user->id)->pluck('id')->all();
        if (!empty($managedDeptIds)) {
            $directDeptIds = array_merge($directDeptIds, $managedDeptIds);
        }

        // 2. Nếu user là Leader/Trưởng nhóm, lấy phòng ban trong hồ sơ HRM của họ
        if (self::isLeader($user)) {
            $profile = $user->hrmProfile ?: HrmEmployeeProfile::where('user_id', $user->id)->first();
            if ($profile && $profile->department_id) {
                $directDeptIds[] = (int)$profile->department_id;
            }
        }

        $directDeptIds = array_values(array_unique(array_filter($directDeptIds)));
        if (empty($directDeptIds)) {
            return [];
        }

        // 3. Mở rộng lấy toàn bộ cây phòng ban / nhóm con trực thuộc
        return self::getAllDescendantDepartmentIds($directDeptIds);
    }

    /**
     * Lấy toàn bộ các phòng ban con cháu trực thuộc danh sách department_ids cha
     *
     * @param int[] $parentDeptIds
     * @return int[]
     */
    public static function getAllDescendantDepartmentIds(array $parentDeptIds): array
    {
        if (empty($parentDeptIds)) {
            return [];
        }

        $allDepartments = HrmDepartment::select('id', 'parent_id')->get();
        $accessible = $parentDeptIds;

        $added = true;
        while ($added) {
            $added = false;
            foreach ($allDepartments as $dept) {
                if ($dept->parent_id && in_array((int)$dept->parent_id, $accessible, true) && !in_array((int)$dept->id, $accessible, true)) {
                    $accessible[] = (int)$dept->id;
                    $added = true;
                }
            }
        }

        return array_values(array_unique($accessible));
    }

    /**
     * Áp dụng Data Scope vào Query của bảng HrmEmployeeProfile.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param User|null $user
     * @param string|null $tableAlias
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function applyProfileScope($query, ?User $user = null, ?string $tableAlias = 'hrm_employee_profiles')
    {
        $user = $user ?: Auth::user();
        if (!$user) {
            return $query->whereRaw('1=0');
        }

        // 1. Admin / Quản trị HRM: Xem ALL
        if (self::isHrmAdmin($user)) {
            return $query;
        }

        $colUser = $tableAlias ? "{$tableAlias}.user_id" : 'user_id';
        $colDept = $tableAlias ? "{$tableAlias}.department_id" : 'department_id';
        $colCreated = $tableAlias ? "{$tableAlias}.created_by_user_id" : 'created_by_user_id';

        $isLeader = self::isLeader($user);
        $subordinateUserIds = self::getSubordinateUserIds($user);
        $accessibleDeptIds = self::getAccessibleDepartmentIds($user);

        // 2. Trưởng phòng / Lead sale: Xem cây cấp dưới + phòng ban/team con + tự tạo + chính mình
        if ($isLeader || count($subordinateUserIds) > 1 || !empty($accessibleDeptIds)) {
            return $query->where(function ($q) use ($user, $subordinateUserIds, $accessibleDeptIds, $colUser, $colDept, $colCreated) {
                $hasCondition = false;

                if (!empty($subordinateUserIds)) {
                    $q->whereIn($colUser, $subordinateUserIds);
                    $hasCondition = true;
                }

                if (!empty($accessibleDeptIds)) {
                    if ($hasCondition) {
                        $q->orWhereIn($colDept, $accessibleDeptIds);
                    } else {
                        $q->whereIn($colDept, $accessibleDeptIds);
                        $hasCondition = true;
                    }
                }

                $q->orWhere($colCreated, $user->id)
                  ->orWhere($colUser, $user->id);
            });
        }

        // 3. Nhân viên thông thường: Chỉ xem chính mình
        return $query->where($colUser, $user->id);
    }

    /**
     * Kiểm tra user có quyền xem hồ sơ nhân sự cụ thể không.
     */
    public static function canAccessProfile(?User $user, HrmEmployeeProfile $profile): bool
    {
        $user = $user ?: Auth::user();
        if (!$user) {
            return false;
        }

        if (self::isHrmAdmin($user)) {
            return true;
        }

        // Chính mình
        if ($profile->user_id && (int)$profile->user_id === (int)$user->id) {
            return true;
        }

        // Do mình tạo
        if ($profile->created_by_user_id && (int)$profile->created_by_user_id === (int)$user->id) {
            return true;
        }

        if (self::isLeader($user)) {
            $subordinateIds = self::getSubordinateUserIds($user);
            if ($profile->user_id && in_array((int)$profile->user_id, $subordinateIds, true)) {
                return true;
            }

            $accessibleDeptIds = self::getAccessibleDepartmentIds($user);
            if ($profile->department_id && in_array((int)$profile->department_id, $accessibleDeptIds, true)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Áp dụng Data Scope vào Query của đơn Nghỉ phép (HrmLeaveRequest).
     */
    public static function applyLeaveScope($query, ?User $user = null, ?string $tableAlias = 'hrm_leave_requests')
    {
        $user = $user ?: Auth::user();
        if (!$user) {
            return $query->whereRaw('1=0');
        }

        if (self::isHrmAdmin($user)) {
            return $query;
        }

        $colUser = $tableAlias ? "{$tableAlias}.user_id" : 'user_id';
        $isLeader = self::isLeader($user);
        $subordinateUserIds = self::getSubordinateUserIds($user);
        $accessibleDeptIds = self::getAccessibleDepartmentIds($user);

        if ($isLeader || count($subordinateUserIds) > 1 || !empty($accessibleDeptIds)) {
            return $query->where(function ($q) use ($user, $subordinateUserIds, $accessibleDeptIds, $colUser) {
                $q->whereIn($colUser, $subordinateUserIds)
                  ->orWhere($colUser, $user->id);

                if (!empty($accessibleDeptIds)) {
                    $q->orWhereHas('employee', function ($eq) use ($accessibleDeptIds) {
                        $eq->whereIn('department_id', $accessibleDeptIds);
                    });
                }
            });
        }

        return $query->where($colUser, $user->id);
    }

    /**
     * Áp dụng Data Scope vào Đăng ký làm Online (HrmOnlineRegistration).
     */
    public static function applyOnlineRegistrationScope($query, ?User $user = null)
    {
        $user = $user ?: Auth::user();
        if (!$user) {
            return $query->whereRaw('1=0');
        }

        if (self::isHrmAdmin($user)) {
            return $query;
        }

        $isLeader = self::isLeader($user);
        $subordinateUserIds = self::getSubordinateUserIds($user);
        $accessibleDeptIds = self::getAccessibleDepartmentIds($user);

        if ($isLeader || count($subordinateUserIds) > 1 || !empty($accessibleDeptIds)) {
            return $query->where(function ($q) use ($user, $subordinateUserIds, $accessibleDeptIds) {
                $q->whereIn('user_id', $subordinateUserIds)
                  ->orWhere('user_id', $user->id);

                if (!empty($accessibleDeptIds)) {
                    $q->orWhereHas('employee', function ($eq) use ($accessibleDeptIds) {
                        $eq->whereIn('department_id', $accessibleDeptIds);
                    });
                }
            });
        }

        return $query->where('user_id', $user->id);
    }
}
