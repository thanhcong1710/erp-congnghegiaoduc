<?php

namespace App\Http\Controllers;

use App\Providers\UtilityServiceProvider as u;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ParentCareController extends Controller
{
    public function getAllDataByParent(Request $request, $parent_id){
        $cond = "";
        $data = u::query("SELECT c.*, (SELECT CONCAT(name,' - ',hrm_id) FROM users WHERE id=c.creator_id) AS creator_name,
                (SELECT name FROM crm_contact_methods WHERE id=c.method_id) AS method_name ,
                (SELECT name FROM branches WHERE id=c.branch_id) AS branch_name
            FROM crm_customer_care AS c WHERE parent_id=$parent_id $cond ORDER BY c.care_date DESC");
        foreach($data AS $k=> $row){
            $data[$k]->call_status_label = u::getTitleCallStatus($row->call_status,$row->call_status_sub);
        }
        return response()->json($data);
    }

    public function add(Request $request)
    {
        $dataRequest = $request->all();
        $attachedFile = $dataRequest['attached_file'];
        
        if ($attachedFile) {
            // SAVE FILES TO SERVER
            $explod = explode(',', $attachedFile);
            $decod = base64_decode($explod[1]);
            if ( in_array(strtolower($explod[0]),['data:image/png;base64','data:image/jpg;base64','data:image/jpeg;base64'])) {
                $extend = 'jpg';
            } else {
                $data = (object)[
                    'status' => 0 ,
                    'message' => "File upload không hợp lệ. chỉ hỗ trợ định dạng JPG, PNG",
                ];
                return response()->json($data);
            }
            $fileAttached = 'customer_care_'.md5($request->attached_file.date('YmdHis')).'.'.$extend;
            $p = __DIR__.'/../../../public/static/upload/'.$fileAttached;
            file_put_contents($p, $decod);
        }
        $id = u::insertSimpleRow(array(
            'parent_id'=>$request->parent_id,
            'note'=>$request->note,
            'method_id' => $request->method_id,
            'care_date' => date('Y-m-d H:i:s'),
            'created_at' => date('Y-m-d H:i:s'),
            'creator_id' => Auth::user()->id,
            'branch_id' => Auth::user()->branch_id,
            'attached_file'=>$attachedFile ? '/static/upload/'.$fileAttached : '',
            'next_care_date' =>  data_get($dataRequest, 'next_care_date' )?  date('Y-m-d H:i:s',strtotime(data_get($dataRequest, 'next_care_date' ))) : null,
            'call_status' => data_get($dataRequest, 'call_status' ),
            'call_status_sub' => data_get($dataRequest, 'call_status_sub' ),
        ), 'crm_customer_care');
        $data = (object)[
            'status' => 1 ,
            'message' => "Thành công",
            'data'=>$id
        ];
        if(data_get($dataRequest, 'call_status')){
            $parent_info = u::first("SELECT * FROM crm_parents WHERE id=$request->parent_id");
            $parent_status = u::genStatusByCallStatus(data_get($dataRequest, 'call_status'), data_get($dataRequest, 'call_status_sub' ));
            if(($parent_info->status < 70 && $parent_info->status!=$parent_status) || $parent_status > $parent_info->status){
                if( data_get($dataRequest, 'next_care_date' )){
                    $data=u::updateSimpleRow(array(
                        'updated_at' => date('Y-m-d H:i:s'),
                        'updator_id' => $request->user()->id,
                        'next_care_date'=>date('Y-m-d H:i:s',strtotime($request->next_care_date)),
                        'status' => $parent_status
                    ), array('id' => $request->parent_id), 'crm_parents');
                }else{
                    $data=u::updateSimpleRow(array(
                        'updated_at' => date('Y-m-d H:i:s'),
                        'updator_id' => $request->user()->id,
                        'status' => $parent_status
                    ), array('id' => $request->parent_id), 'crm_parents');
                }
            }
        }
       
        return response()->json($data);
    }
    
    public function getInfoCall($care_id){
        $data = u::first("SELECT c.id AS care_id,c.data_state,v.* 
            FROM crm_customer_care AS c 
                LEFT JOIN voip24h_data AS v ON v.id=c.data_id
            WHERE c.id=$care_id");
        return response()->json($data);
    }
    public function updateNoteCare(Request $request){
        u::updateSimpleRow(array('note'=>$request->note,'status'=>1),array('id'=>$request->care_id),'crm_customer_care');
        $data_info=u::first("SELECT parent_id FROM crm_customer_care WHERE id=".$request->care_id);
        Log::info("updateNoteCare $request->care_id",['parent_id'=>$data_info->parent_id]);
        if($data_info){
            ParentsController::processParentLockById($data_info->parent_id);
            Log::info("updateNoteCare processParentLockById $request->care_id",['parent_id'=>$data_info->parent_id]);
        }
        return "ok";
    }
    public function deleteFileAttached(){
        $list = u::query("SELECT id,attached_file FROM crm_customer_care WHERE attached_file IS NOT NULL AND created_at<'".date('Y-m-d 23:59:59',time()-24*3600*60)."'");
        foreach($list AS $row){
            $p = __DIR__.'/../../../public/'.$row->attached_file;
            if (file_exists($p)) {
                unlink($p);
            }
            u::query("UPDATE crm_customer_care SET attached_file=NULL WHERE id=".$row->id);
        }
        return "ok";
    }
}
