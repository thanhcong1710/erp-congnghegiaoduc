<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /** Response chuẩn khi user không có quyền truy cập bản ghi (phân quyền Sale theo cây quản lý). */
    protected function denyRecordAccess()
    {
        return response()->json((object)['status' => 0, 'message' => 'Bạn không có quyền truy cập bản ghi này.', 'data' => null], 403);
    }
}
