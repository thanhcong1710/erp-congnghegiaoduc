<?php

namespace App\Http\Controllers;

use App\Enums\SystemCode;
use App\Http\Controllers\Controller;
use App\Providers\UtilityServiceProvider as u;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class SystemController extends Controller
{
    public function getAllBranches(Request $request)
    {
        $data = u::query("SELECT *, 0 AS selected, id AS `value`  FROM branches WHERE status = 1");
        return response()->json($data);
    }

    public function getBranchesHasUser(Request $request)
    {
        $cond = " AND id IN (" . Auth::user()->getBranchesHasUser() . ")";
        $data = u::query("SELECT *, 0 AS selected, id AS `value`  FROM branches WHERE status = 1 $cond");
        return response()->json($data);
    }

    public function getRoles(Request $request)
    {
        $data = u::query("SELECT *, 0 AS selected, id AS `value` FROM roles ");
        return response()->json($data);
    }

    public function getSources()
    {
        $data = u::query("SELECT *, id AS `value` FROM sources WHERE status=1");
        return response()->json($data);
    }

    public function getSourceDetail(Request $request)
    {
        $cond = " AND (branch_id IN (" . Auth::user()->getBranchesHasUser() . ") OR branch_id IS NULL OR branch_id=0)";
        $cond .= data_get($request, 'source_id') ? " AND source_id = " . data_get($request, 'source_id') : '';
        $data = u::query("SELECT *, id AS `value` FROM source_detail WHERE status=1 $cond");
        return response()->json($data);
    }

    public function getProvinces()
    {
        $data = u::getMultiObject([], 'provinces');
        return response()->json($data);
    }

    public function getDistrictsByProvice($province_id)
    {
        $data = u::getMultiObject(['province_id' => $province_id], 'districts');
        return response()->json($data);
    }

    public function getJobs()
    {
        $data = u::query("SELECT * FROM jobs WHERE status=1");
        return response()->json($data);
    }

    public function getMethods()
    {
        $data = u::query("SELECT * FROM crm_contact_methods WHERE status=1");
        return response()->json($data);
    }

    public function getProducts()
    {
        $data = u::query("SELECT *, 0 AS selected FROM products WHERE status=1");
        return response()->json($data);
    }

    public function getPrograms()
    {
        $data = u::query("SELECT *, 0 AS selected FROM programs WHERE status=1");
        return response()->json($data);
    }

    public function getShifts()
    {
        $data = u::query("SELECT * FROM shifts WHERE status=1");
        return response()->json($data);
    }

    public function getRooms($branch_id)
    {
        $data = u::query("SELECT *, name AS label FROM rooms WHERE status=1 AND branch_id=$branch_id");
        return response()->json($data);
    }

    public function getCMs($branch_id)
    {
        $cond = $branch_id ? " AND b.branch_id=$branch_id " : "";
        $data = u::query("SELECT DISTINCT u.id, CONCAT(u.name, ' - ', u.hrm_id) AS label 
            FROM role_has_user AS ru 
                LEFT JOIN users AS u ON ru.user_id=u.id 
                LEFT JOIN roles AS r ON r.id=ru.role_id 
                LEFT JOIN branch_has_user AS b ON b.user_id= ru.user_id
            WHERE u.status=1 $cond AND (r.code='" . SystemCode::ROLE_CM . "' OR r.code='" . SystemCode::ROLE_CM_LEADER . "')");
        return response()->json($data);
    }

    public function getTeachers($branch_id)
    {
        $cond = $branch_id ? " AND b.branch_id=$branch_id " : "";
        $data = u::query("SELECT DISTINCT u.id, CONCAT(u.name, ' - ', u.hrm_id) AS label 
            FROM role_has_user AS ru 
                LEFT JOIN users AS u ON ru.user_id=u.id 
                LEFT JOIN roles AS r ON r.id=ru.role_id 
                LEFT JOIN branch_has_user AS b ON b.user_id= ru.user_id
            WHERE u.status=1 $cond AND (r.code='" . SystemCode::ROLE_TEACHER . "' OR r.code='" . SystemCode::ROLE_TEACHER_LEADER . "')");
        return response()->json($data);
    }

    public function getEndDateInClass(Request $request)
    {
        $start_date = data_get($request, 'start_date');
        $session = data_get($request, 'session');
        $class_id = data_get($request, 'class_id');
        $class_info = u::first("SELECT class_day, branch_id, product_id FROM classes WHERE id= $class_id");

        $holidays = u::getPublicHolidays(data_get($class_info, 'branch_id'), data_get($class_info, 'product_id'));
        $arr_day = explode(",", data_get($class_info, 'class_day'));
        $data_sessions = u::calculatorSessionsByNumberOfSessions($start_date, $session, $holidays, $arr_day);

        return data_get($data_sessions, 'end_date');
    }

    public function getClassesActiveByBranchProduct(Request $request)
    {
        $branch_id = data_get($request, 'branch_id');
        $product_id = data_get($request, 'product_id');
        $data = u::query("SELECT cls_name AS label, id FROM classes WHERE status=1 AND product_id=$product_id AND branch_id = $branch_id");
        return response()->json($data);
    }

    public function getProgramsByProduct(Request $request, $product_id)
    {
        $is_parent = data_get($request, 'is_parent', 0);
        if ($is_parent) {
            $data = u::query("SELECT name, id FROM programs WHERE status=1 AND parent_id =0 AND product_id=$product_id ");
        } else {
            $data = u::query("SELECT name, id FROM programs WHERE status=1 AND product_id=$product_id ");
        }
        return response()->json($data);
    }

    public function getTuitionFees(Request $request)
    {
        $status = data_get($request, 'status', null);
        $type_fee = data_get($request, 'type_fee', null);
        $cond = $status !== null ? '1' : " status = $status";
        if ($type_fee) {
            $cond .= " AND t.type_fee = " . $type_fee;
        }
        $data = u::query("SELECT t.name, t.id, t.available_date, t.expired_date, t.status, t.price, '' AS price_combo,
                (SELECT name FROM products WHERE id=t.product_id) AS product_name    
            FROM tuition_fee AS t WHERE $cond ORDER BY t.id DESC ");
        return response()->json($data);
    }

    public function getSubjects()
    {
        $data = u::query("SELECT *, 0 AS selected, 0 AS stt, 0 AS session FROM subjects WHERE status=1");
        return response()->json($data);
    }

    public function getB2BSources()
    {
        $data = u::query("SELECT *, id AS `value` FROM b2b_sources WHERE status=1");
        return response()->json($data);
    }

    public function getTAs($branch_id)
    {
        $cond = $branch_id ? " AND b.branch_id=$branch_id " : "";
        $data = u::query("SELECT DISTINCT u.id, CONCAT(u.name, ' - ', u.hrm_id) AS label 
            FROM role_has_user AS ru 
                LEFT JOIN users AS u ON ru.user_id=u.id 
                LEFT JOIN roles AS r ON r.id=ru.role_id 
                LEFT JOIN branch_has_user AS b ON b.user_id= ru.user_id
            WHERE u.status=1 $cond AND r.code='" . SystemCode::ROLE_TA . "'");
        return response()->json($data);
    }

    public function getClassByCM($cm_id)
    {
        $data = u::query("SELECT cl.* 
            FROM classes AS cl 
            WHERE cl.status=1 AND cl.cm_id=$cm_id");
        return response()->json($data);
    }
    public function getCustomerStatuses()
    {
        return response()->json(config('crm.customer_statuses'));
    }

    public function getCallStatuses()
    {
        return response()->json(config('crm.call_statuses'));
    }

    /**
     * GET /api/system/users?role_id=69
     * Lấy danh sách users theo role_id, lọc theo branch của user hiện tại
     */
    public function getUsersByRole(Request $request)
    {
        $role_id = (int) data_get($request, 'role_id', 0);
        $branch_id = (int) data_get($request, 'branch_id', 0);

        $exclude_role_id = (int) data_get($request, 'exclude_role_id', 0);

        if (!$role_id) {
            return response()->json([]);
        }

        $branchCond = '';
        if ($branch_id > 0) {
            $branchCond = " AND b.branch_id = $branch_id";
        } else {
            $branchIds = Auth::user()->getBranchesHasUser();
            $branchCond = " AND (b.branch_id IN ($branchIds) OR b.branch_id IS NULL)";
        }

        $excludeCond = '';
        if ($exclude_role_id > 0) {
            $excludeCond = " AND u.id NOT IN (SELECT user_id FROM role_has_user WHERE role_id = $exclude_role_id) ";
        }

        $data = u::query("
            SELECT DISTINCT
                u.id,
                u.name,
                u.hrm_id,
                CONCAT(u.name, ' - ', u.hrm_id) AS label
            FROM role_has_user AS ru
                LEFT JOIN users AS u ON u.id = ru.user_id
                LEFT JOIN branch_has_user AS b ON b.user_id = ru.user_id
            WHERE ru.role_id = $role_id
              AND u.status = 1
              $branchCond
              $excludeCond
            ORDER BY u.name ASC
        ");

        return response()->json($data);
    }
}
