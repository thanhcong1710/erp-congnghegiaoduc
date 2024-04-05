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
}
