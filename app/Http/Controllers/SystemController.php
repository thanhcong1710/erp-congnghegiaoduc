<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Providers\UtilityServiceProvider as u;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class SystemController extends Controller
{
    public function getBranches(Request $request)
    {
        $data = u::query("SELECT *, 0 AS selected  FROM branches WHERE status = 1");
        return response()->json($data);
    }

    public function getRoles(Request $request)
    {
        $data = u::query("SELECT *, 0 AS selected FROM roles ");
        return response()->json($data);
    }

    public function getSources(){
        $data = u::query("SELECT * FROM sources WHERE status=1");
        return response()->json($data);
    }

    public function getSourceDetail(Request $request){
        $cond = "";
        if(!Auth::user()->checkPermission('canViewAllSourceDetail')){
            $cond = " AND (branch_id IN (".Auth::user()->getBranchesHasUser().") OR branch_id IS NULL OR branch_id=0)";
        }
        $data = u::query("SELECT * FROM source_detail WHERE status=1 $cond");
        return response()->json($data);
    }
}
