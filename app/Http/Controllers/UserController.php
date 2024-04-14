<?php

namespace App\Http\Controllers;

use App\User;
use App\Http\Controllers\Controller;
use App\Providers\UtilityServiceProvider as u;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function updateInfo(Request $request)
    {
        $data = $request->data;
        if ($data) {
            $arr_update = [];
            foreach ($data as $k => $item) {
                if ($k == 'birthday') {
                    $date = str_replace('/', '-', $item);
                    $arr_update[$k] = date('Y-m-d', strtotime($date));
                } else {
                    $arr_update[$k] = $item;
                }
            }
            u::updateSimpleRow($arr_update, array('id' => Auth::user()->id), 'users');
        }
        $uesr_info = u::getObject(array('id' => Auth::user()->id), 'users');

        return response()->json([
            'status' => 1,
            'message' => 'Cập nhật thành công.',
            'userData' => u::transformUser($uesr_info)
        ]);
    }

    public function changePassword(Request $request)
    {
        if (password_verify($request->old_password, Auth::user()->password)) {
            u::updateSimpleRow(array(
                'password' => Hash::make($request->new_password)
            ), array('id' => Auth::user()->id), 'users');
            return response()->json([
                'status' => 1,
                'message' => 'Đổi mật khẩu thành công.'
            ]);
        } else {
            return response()->json([
                'status' => 0,
                'message' => 'Mật khẩu cũ không chính xác.'
            ]);
        }
    }

    public function list(Request $request)
    {
        $keyword = isset($request->keyword) ? $request->keyword : '';
        $pagination = (object)$request->pagination;
        $page = isset($pagination->cpage) ? (int) $pagination->cpage : 1;
        $limit = isset($pagination->limit) ? (int) $pagination->limit : 20;
        $offset = $page == 1 ? 0 : $limit * ($page - 1);
        $limitation =  $limit > 0 ? " LIMIT $offset, $limit" : "";
        $cond = " 1=1 ";
        if ($keyword !== '') {
            $cond .= " AND (u.name LIKE '%$keyword%' OR u.hrm_id LIKE '%$keyword%')";
        }
        $total = u::first("SELECT count(u.id) AS total FROM users AS u WHERE $cond ");
        $list = u::query("SELECT u.*, IF(u.manager_id, (SELECT name FROM users WHERE id=u.manager_id), '') AS manager_name
            FROM users AS u 
            WHERE $cond ORDER BY u.id DESC $limitation");
        $data = u::makingPagination($list, $total->total, $page, $limit);
        return response()->json($data);
    }

    public function add(Request $request)
    {
        $uesr_info = u::getObject(['hrm_id'=>$request->hrm_id], 'users');
        if($uesr_info){
            return response()->json([
                'status' => 0,
                'message' => 'Mã nhân viên đã tồn tại trên hệ thống',
            ]);
        }
        if($request->manager_hrm_id){
            $manager_hrm_info = u::first("SELECT * FROM users WHERE hrm_id= '$request->manager_hrm_id'");
        }
        $role_name = '';
        foreach($request->roles AS $role){
            if(data_get($role,'selected')){
                $role_name.= $role_name ? ', '. data_get($role,'name') : data_get($role,'name'); 
            }
        }
        $branch_name = '';
        $branch_id = '';
        foreach($request->branches AS $branch){
            if(data_get($branch,'selected')){
                $branch_name.= $branch_name ? ', '.data_get($branch,'name') : data_get($branch,'name'); 
                $branch_id = data_get($branch,'id');
            }
        }
        $user_id = u::insertSimpleRow(array(
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'hrm_id' => $request->hrm_id,
            'password' => Hash::make($request->password),
            'status' => $request->status,
            'manager_hrm_id' => $request->manager_hrm_id,
            'manager_id' => isset($manager_hrm_info) && $manager_hrm_info ? $manager_hrm_info->id : null,
            'sip_id' => $request->sip_id,
            'role_name' => $role_name,
            'branch_id' => $branch_id,
            'branch_name' => $branch_name,
            'created_at' => date('Y-m-d H:i:s'),
            'creator_id' => Auth::user()->id
        ), 'users');

        foreach($request->roles AS $role){
            if(data_get($role,'selected')){
                u::insertSimpleRow(array(
                    'role_id' => data_get($role,'id'),
                    'user_id' => $user_id,
                ), 'role_has_user');
            }
        }

        foreach($request->branches AS $branch){
            if(data_get($branch,'selected')){
                u::insertSimpleRow(array(
                    'branch_id' => data_get($branch,'id'),
                    'user_id' => $user_id,
                ), 'branch_has_user');
            }
        }

        return response()->json([
            'status' => 1,
            'message' => 'Thêm mới thành công',
        ]);
    }

    public function info(Request $request){
        $user_info = u::first("SELECT * FROM users WHERE id = $request->user_id");
        $roles = u::query("SELECT r.*, IF(u.user_id IS NOT NULL, 1, 0) AS selected 
            FROM roles AS r LEFT JOIN role_has_user AS u ON u.role_id=r.id AND u.user_id = $request->user_id");
        $branches = u::query("SELECT b.*, IF(u.user_id IS NOT NULL, 1, 0) AS selected 
            FROM branches AS b LEFT JOIN branch_has_user AS u ON u.branch_id=b.id AND u.user_id = $request->user_id 
            WHERE b.status=1");
        return response()->json([
            'user_info' => $user_info,
            'roles' => $roles,
            'branches' => $branches
        ]);
    }

    public function update(Request $request){
        $uesr_info = u::first("SELECT id FROM users WHERE hrm_id='$request->hrm_id' AND id!=$request->user_id");
        if($uesr_info){
            return response()->json([
                'status' => 0,
                'message' => 'Mã nhân viên đã tồn tại trên hệ thống',
            ]);
        }
        if($request->manager_hrm_id){
            $manager_hrm_info = u::first("SELECT * FROM users WHERE hrm_id= '$request->manager_hrm_id'");
        }
        $role_name = '';
        foreach($request->roles AS $role){
            if(data_get($role,'selected')){
                $role_name.= $role_name ? ', '. data_get($role,'name') : data_get($role,'name'); 
            }
        }
        $branch_name = '';
        $branch_id = '';
        foreach($request->branches AS $branch){
            if(data_get($branch,'selected')){
                $branch_name.= $branch_name ? ', '.data_get($branch,'name') : data_get($branch,'name'); 
                $branch_id = data_get($branch,'id');
            }
        }
        if($request->change_pass){
            u::updateSimpleRow(array(
                'password' => Hash::make($request->password)
            ), array('id'=>$request->user_id), 'users');
        }
        u::updateSimpleRow(array(
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'hrm_id' => $request->hrm_id,
            'status' => $request->status,
            'manager_hrm_id' => $request->manager_hrm_id,
            'manager_id' => isset($manager_hrm_info) && $manager_hrm_info ? $manager_hrm_info->id : null,
            'sip_id' => $request->sip_id,
            'role_name' => $role_name,
            'branch_id' => $branch_id,
            'branch_name' => $branch_name,
            'updated_at' => date('Y-m-d H:i:s'),
            'updator_id' => Auth::user()->id
        ), array('id'=>$request->user_id), 'users');

        u::query("DELETE FROM role_has_user WHERE user_id=$request->user_id");
        foreach($request->roles AS $role){
            if(data_get($role,'selected')){
                u::insertSimpleRow(array(
                    'role_id' => data_get($role,'id'),
                    'user_id' => $request->user_id,
                ), 'role_has_user');
            }
        }

        u::query("DELETE FROM branch_has_user WHERE user_id=$request->user_id");
        foreach($request->branches AS $branch){
            if(data_get($branch,'selected')){
                u::insertSimpleRow(array(
                    'branch_id' => data_get($branch,'id'),
                    'user_id' => $request->user_id,
                ), 'branch_has_user');
            }
        }

        return response()->json([
            'status' => 1,
            'message' => 'Cập nhật thành công',
        ]);
    }

    public function getUsersManager(Request $request){
        $cond = "";
        if(!Auth::user()->checkPermission('canViewAllUser')){
            $cond = " AND id IN (".Auth::user()->getStaffHasUser().")";
        }
        $data = u::query("SELECT id, CONCAT(hrm_id,' - ',name) AS label_name, id AS `value` 
            FROM users WHERE status=1 $cond");
        return response()->json($data);
    }

    public function getAllUsers(Request $request){
        $cond = "";
        if($request->status !== null){
            $cond = " AND status = ".(int)$request->status;
        }
        $data = u::query("SELECT id, CONCAT(hrm_id,' - ',name) AS label_name, id AS `value` 
            FROM users WHERE 1 $cond");
        return response()->json($data);
    }
    
}
