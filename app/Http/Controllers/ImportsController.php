<?php

namespace App\Http\Controllers;

use App\Providers\UtilityServiceProvider as u;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as x;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ImportsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function list(Request $request)
    {
        $status = isset($request->status) ? $request->status : '';
        
        $pagination = (object)$request->pagination;
        $page = isset($pagination->cpage) ? (int) $pagination->cpage : 1;
        $limit = isset($pagination->limit) ? (int) $pagination->limit : 20;
        $offset = $page == 1 ? 0 : $limit * ($page-1);
        $limitation =  $limit > 0 ? " LIMIT $offset, $limit": "";
        $cond = " 1 ";
        if($status != '-1'){
            $cond .= " AND status=$status";
        }
        if(!Auth::user()->checkPermission('canViewAllImport')){
            $cond .= " AND i.creator_id IN (".Auth::user()->getStaffHasUser().")";
        }

        $total = u::first("SELECT count(id) AS total FROM crm_imports AS i WHERE $cond ");
        $list = u::query("SELECT i.*, (SELECT name FROM users WHERE id=i.creator_id) AS creator_name ,
                (SELECT count(id) FROM crm_import_parents WHERE import_id=i.id AND status=6) AS count_success,
                (SELECT count(id) FROM crm_import_parents WHERE import_id=i.id AND status!=6) AS count_error
            FROM crm_imports AS i WHERE $cond ORDER BY i.id DESC $limitation");
        $data = u::makingPagination($list, $total->total, $page, $limit);
        return response()->json($data);
    }
    public function upload(Request $request){
        $dataRequest = $request->all();
        $data_mes = (object) [];

        $attachedFile = $dataRequest['files'];
        if (!$attachedFile) {
            $data_mes->error = true;
            $data_mes->message = "File upload không hợp lệ";
        }

        // SAVE FILES TO SERVER
        $explod = explode(',', $attachedFile);
        $decod = base64_decode($explod[1]);
        if (str_contains($explod[0], 'spreadsheetml')) {
            $extend = 'xlsx';
        } else {
            $data_mes->error = true;
            $data_mes->message = "File upload không hợp lệ";
        }
        $fileAttached = md5($request->attached_file.date('YmdHis')).'.'.$extend;
        $p = __DIR__.'/../../../public/static/upload/'.$fileAttached;
        file_put_contents($p, $decod);
        $reader = new x();
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($p);
        $sheet = $spreadsheet->setActiveSheetIndex(0);

        $dataXslx = $sheet->toArray();
        unset($dataXslx[0]);
        $import_id = u::insertSimpleRow(array(
            'file_name'=>$fileAttached,
            'file_link'=>'static/upload/'.$fileAttached,
            'status'=>0,
            'created_at'=> date('Y-m-d H:i:s'),
            'creator_id'=>(int)$request->user()->id,
        ),'crm_imports');
        $this->addItemDataImport($dataXslx,$import_id,(int)$request->user()->id);
        $this->processCheckDuplicateData($import_id);
        u::query("UPDATE crm_import_parents AS p LEFT JOIN users AS u ON u.hrm_id=p.owner_hrm SET p.owner_id=u.id WHERE p.import_id =$import_id");
        $data = u::query("SELECT * FROM crm_import_parents WHERE import_id =$import_id");
        $total_open_lock = u::first("SELECT count(id) AS total FROM crm_import_parents WHERE import_id =$import_id AND status=4 AND is_lock=0");
        $total_error = u::first("SELECT count(id) AS total FROM crm_import_parents WHERE import_id =$import_id AND status IN (2,3,4)");
        $total_validate = u::first("SELECT count(id) AS total FROM crm_import_parents WHERE import_id =$import_id AND status IN (1)");
        
        $data_mes->data = $data;
        $data_mes->total_error = $total_error->total - $total_open_lock->total;
        $data_mes->total_open_lock = $total_open_lock->total;
        $data_mes->total_validate = $total_validate->total;
        $data_mes->message = 'Import file thành công!';
        $data_mes->import_id = $import_id;
        $data_mes->error = false;
        return response()->json($data_mes);
       
    }
    public function addItemDataImport($list,$import_id,$creator_id) {
        if ($list) {
            $created_at = date('Y-m-d H:i:s');
            $query = "INSERT INTO crm_import_parents (import_id,`name`,email,gud_mobile1,`address`,note,created_at,creator_id,`status`,error_message,owner_hrm,link_facebook) VALUES ";
            if (count($list) > 10000) {
                for($i = 0; $i < 10000; $i++) {
                    $item = $this->convertData($list[$i]);
                    $validate = $this->validateData($item);
                    $status = $validate->has_error ? 2 : 1;
                    $error_message = $validate->message;
                    $gud_mobile1 = $item->gud_mobile1 ? $item->gud_mobile1 : $list[$i][1];
                    $query.= "('$import_id','$item->name','$item->email','$gud_mobile1','$item->address','$item->note','$created_at','$creator_id',$status,'$error_message','$item->owner_hrm','$item->link_facebook'),";
                }
                $query = substr($query, 0, -1);
                u::query($query);
                $this->addItemDataImport(array_slice($list, 10000),$import_id,$creator_id);
            } else {
                foreach($list as $i=>$item) {
                    $item = $this->convertData($item);
                    $validate = $this->validateData($item);
                    $status = $validate->has_error ? 2 : 1;
                    $error_message = $validate->message;
                    $gud_mobile1 = $item->gud_mobile1 ? $item->gud_mobile1 : $list[$i][1];
                    $query.= "('$import_id','$item->name','$item->email','$gud_mobile1','$item->address','$item->note','$created_at','$creator_id',$status,'$error_message','$item->owner_hrm','$item->link_facebook'),";
                }
                $query = substr($query, 0, -1);
                u::query($query);
            }
        }
    }
    public function convertData($data){
        $result = (object)array(
            'name'=> str_replace(array("'","]","\\"),"",$data[0]),
            'gud_mobile1'=> u::phoneNew($data[1]),
            'email'=>$data[2],
            'link_facebook'=> $data[2],
            'address'=>str_replace(array("'","]","\\"),"",$data[4]),
            'note'=>$data[5],
            'owner_hrm'=>$data[6],
        );
        return $result;
    }
    public function validateData($data){
        $result = (object)array(
            'has_error'=>0,
            'message'=>""
        );
        if(!$data->name){
            $result->has_error = 1;
            $result->message = "Tên phụ huynh không để trống";
        }if(!$data->gud_mobile1){
            $result->has_error = 1;
            $result->message = "Số điện thoại không hợp lệ";
        }
        return $result;
    }
    public function processCheckDuplicateData($import_id){
        // check duplicate crm_import_parents
        $list = u::query("SELECT p.id, p1.id
            FROM
                crm_import_parents AS p
                LEFT JOIN crm_import_parents AS p1 ON (p.gud_mobile1 = p1.gud_mobile1 AND p.id>p1.id)
            WHERE
                p.import_id = $import_id AND p1.import_id=$import_id 
                AND p1.id IS NOT NULL");
        if(!empty($list)){
            $sql_update = "INSERT INTO crm_import_parents (id,`status`,error_message) VALUES ";
            foreach($list AS $row){
                $sql_update.="($row->id,3,'Trùng lặp dữ liệu trong file import'),";
            }
            $sql_update = substr($sql_update, 0, -1);
            $sql_update.=" ON DUPLICATE KEY UPDATE `id` = VALUES(`id`), `status` = VALUES(`status`), `error_message` = VALUES(`error_message`)";
            u::query($sql_update);
        }
        // check duplicate crm_parents
        $list = u::query("SELECT p.id, u.name, u.hrm_id,u.branch_name, ps.is_lock, ps.id AS parent_id
                    FROM
                        crm_import_parents AS p
                        LEFT JOIN crm_parents AS ps ON ps.mobile_1 = p.gud_mobile1
                        LEFT JOIN users AS u ON ps.owner_id = u.id 
                    WHERE
                        p.import_id = $import_id 
                        AND ps.id IS NOT NULL");
        if(!empty($list)){
            $sql_update = "INSERT INTO crm_import_parents (id,`status`,error_message,is_lock,parent_id) VALUES ";
            foreach($list AS $row){
                if($row->is_lock==0){
                    $sql_update.="($row->id,4,'SĐT đang thuộc quyền quản lý của nhân viên $row->name - $row->hrm_id $row->branch_name (có thể ghi đè)',$row->is_lock,$row->parent_id),";
                }else{
                    $sql_update.="($row->id,4,'SĐT đang thuộc quyền quản lý của nhân viên $row->name - $row->hrm_id $row->branch_name',$row->is_lock,$row->parent_id),";
                }
            }
            $sql_update = substr($sql_update, 0, -1);
            $sql_update.=" ON DUPLICATE KEY UPDATE `id` = VALUES(`id`), `status` = VALUES(`status`), `error_message` = VALUES(`error_message`), `is_lock` = VALUES(`is_lock`), `parent_id` = VALUES(`parent_id`)";
            u::query($sql_update);
        }
    }
    public function assign(Request $request){
        $source_id = $request->source_id;
        $source_detail_id = $request->source_detail_id ? $request->source_detail_id : 0;
        $arr_owner = $request->owners_id;
        $import_id = $request->import_id;
        $list_data = u::query("SELECT * FROM crm_import_parents WHERE import_id=$import_id AND status=1");
        $this->addItemDataParent($list_data,$arr_owner,$source_id,$request->user()->id, $source_detail_id);
        $list_data_overwirte = u::query("SELECT i.*, p.owner_id AS curr_owner_id FROM crm_import_parents AS i LEFT JOIN crm_parents AS p ON p.id=i.parent_id WHERE i.import_id=$import_id AND i.status=4 AND i.is_lock=0");
        $this->OverwirteItemDataParent($list_data_overwirte,$arr_owner,$source_id,$request->user()->id, $source_detail_id);

        u::query("UPDATE crm_import_parents SET status=6 WHERE import_id=$import_id AND status=1");
        u::query("UPDATE crm_import_parents SET status=6 WHERE import_id=$import_id AND status=4 AND is_lock=0");
        u::query("UPDATE crm_imports SET status=1 WHERE id=$import_id ");
        $data = u::first("SELECT (SELECT count(id) FROM crm_import_parents WHERE import_id=$import_id AND status=6) AS total_success,
            (SELECT count(id) FROM crm_import_parents WHERE import_id=$import_id AND status!=6) AS total_error");
        u::updateBranchIDParents();
        return response()->json($data);
    }
    public function addItemDataParent($list,$arr_owner,$source_id,$creator_id,$source_detail_id) {
        if ($list) {
            $created_at = date('Y-m-d H:i:s');
            $query = "INSERT INTO crm_parents (`name`,email,mobile_1,`address`,note,created_at,creator_id,`status`,source_id,source_detail_id,owner_id,link_facebook,last_assign_date) VALUES ";
            if (count($list) > 10000) {
                for($i = 0; $i < 10000; $i++) {
                    $item = (object)$list[$i];
                    $owner_id = $item->owner_id? $item->owner_id : $arr_owner[$i%count($arr_owner)];
                    $query.= "('$item->name','$item->email','$item->gud_mobile1','$item->address','$item->note','$created_at','$creator_id',0,'$source_id','$source_detail_id','$owner_id','$item->link_facebook',''$created_at''),";
                }
                $query = substr($query, 0, -1);
                u::query($query);
                $this->addItemDataParent(array_slice($list, 10000),$arr_owner,$source_id,$creator_id,$source_detail_id);
            } else {
                foreach($list as $i=>$item) {
                    $item = (object)$item;
                    $owner_id = $item->owner_id? $item->owner_id : $arr_owner[$i%count($arr_owner)];
                    $query.= "('$item->name','$item->email','$item->gud_mobile1','$item->address','$item->note','$created_at','$creator_id',0,'$source_id','$source_detail_id','$owner_id','$item->link_facebook','$created_at'),";
                }
                $query = substr($query, 0, -1);
                u::query($query);
            }
        }
    }

    public function OverwirteItemDataParent($list,$arr_owner,$source_id,$creator_id,$source_detail_id) {
        if ($list) {
            $created_at = date('Y-m-d H:i:s');
            $sql_update_owner = "INSERT INTO crm_parents (id,updated_at,updator_id,owner_id,last_assign_date,is_lock,source_id,source_detail_id,`name`,email,`address`,note) VALUES ";
            $sql_crm_parent_overwrite = "INSERT INTO crm_parent_overwrite (`parent_id`,last_owner_id,owner_id,`created_at`,creator_id) VALUES ";
            $sql_crm_parent_logs = "INSERT INTO crm_parent_logs (`parent_id`,`content`,creator_id,created_at,`status`) VALUES ";
            if (count($list) > 10000) {
                for($i = 0; $i < 10000; $i++) {
                    $item = (object)$list[$i];
                    $owner_id = $item->owner_id? $item->owner_id : $arr_owner[$i%count($arr_owner)];
                    $sql_update_owner.=" ($item->parent_id,'$created_at',$creator_id,$owner_id,'$created_at',1,'$source_id','$source_detail_id','$item->name','$item->email','$item->address','$item->note'),";
                    $sql_crm_parent_overwrite.=" ($item->parent_id,'$item->curr_owner_id',$owner_id,'$created_at',$creator_id),";
                    
                    $content = "Ghi đè người phụ trách khi import: từ $item->curr_owner_id thành $owner_id`";
                    $sql_crm_parent_logs.=" ($item->parent_id,'$content',$creator_id,'$created_at',0),";
                    
                }
                $this->OverwirteItemDataParent(array_slice($list, 10000),$arr_owner,$source_id,$creator_id,$source_detail_id);
            } else {
                foreach($list as $i=>$item) {
                    $item = (object)$list[$i];
                    $owner_id = $item->owner_id? $item->owner_id : $arr_owner[$i%count($arr_owner)];
                    $sql_update_owner.=" ($item->parent_id,'$created_at',$creator_id,$owner_id,'$created_at',1,'$source_id','$source_detail_id','$item->name','$item->email','$item->address','$item->note'),";
                    $sql_crm_parent_overwrite.=" ($item->parent_id,'$item->curr_owner_id',$owner_id,'$created_at',$creator_id),";
                    
                    $content = "Ghi đè người phụ trách khi import: từ $item->curr_owner_id thành $owner_id`";
                    $sql_crm_parent_logs.=" ($item->parent_id,'$content',$creator_id,'$created_at',1),";
                }
            }
        }
    }

}
