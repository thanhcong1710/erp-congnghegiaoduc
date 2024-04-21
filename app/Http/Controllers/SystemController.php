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
        $cond = " AND id IN (" . Auth::user()->getBranchesHasUser().")";
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
        $cond = "";
        if (!Auth::user()->checkPermission('canViewAllSourceDetail')) {
            $cond = " AND (branch_id IN (" . Auth::user()->getBranchesHasUser() . ") OR branch_id IS NULL OR branch_id=0)";
        }
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

    public function getMethods(){
        $data = u::query("SELECT * FROM crm_contact_methods WHERE status=1");
        return response()->json($data);
    }

    public function getProducts(){
        $data = u::query("SELECT * FROM products WHERE status=1");
        return response()->json($data);
    }

    public function getShifts(){
        $data = u::query("SELECT * FROM shifts WHERE status=1");
        return response()->json($data);
    }
    
    public function getRooms($branch_id){
        $data = u::query("SELECT *, room_name AS label FROM rooms WHERE status=1 AND branch_id=$branch_id");
        return response()->json($data);
    }

    public function getCMs($branch_id){
        $data = u::query("SELECT DISTINCT u.id, CONCAT(u.name, ' - ', u.hrm_id) AS label 
            FROM role_has_user AS ru 
                LEFT JOIN users AS u ON ru.user_id=u.id 
                LEFT JOIN roles AS r ON r.id=ru.role_id 
                LEFT JOIN branch_has_user AS b ON b.user_id= ru.user_id
            WHERE u.status=1 AND b.branch_id=$branch_id AND (r.code='".SystemCode::ROLE_CM."' OR r.code='".SystemCode::ROLE_CM_LEADER."')");
        return response()->json($data);
    }

    public function getTeachers($branch_id){
        $data = u::query("SELECT DISTINCT u.id, CONCAT(u.name, ' - ', u.hrm_id) AS label 
            FROM role_has_user AS ru 
                LEFT JOIN users AS u ON ru.user_id=u.id 
                LEFT JOIN roles AS r ON r.id=ru.role_id 
                LEFT JOIN branch_has_user AS b ON b.user_id= ru.user_id
            WHERE u.status=1 AND b.branch_id=$branch_id AND (r.code='".SystemCode::ROLE_TEACHER."' OR r.code='".SystemCode::ROLE_TEACHER_LEADER."')");
        return response()->json($data);
    }
}
