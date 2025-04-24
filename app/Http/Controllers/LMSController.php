<?php

namespace App\Http\Controllers;

use App\User;
use App\Http\Controllers\Controller;
use App\Providers\UtilityServiceProvider as u;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class LMSController extends Controller
{
    public function syncDataBranch(){
        $url = sprintf('%s/data/setup.asmx/CounStaffBranch', config('lms.url'));
        $client = new Client();
        $arr_ug_type = ['APRIL', 'i-GARTEN', 'CDI'];
        u::query("DELETE FROM lms_branches");
        foreach ($arr_ug_type as $ug_type) {
            $params = [
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'setup' => [
                        'ug_type' => $ug_type,
                    ],
                    'staff' => [
                        'stf_id' => config('lms.stf_id'),
                    ]
                ]
            ];
            $response = $client->request('POST', $url, $params);
            $dataResponse = json_decode($response->getBody()->getContents(), true);
            // Log::info('LMS Branch Data', ['params' => $params,'data' => $dataResponse]);
            if (data_get($dataResponse, 'd.result')){
                $result = data_get($dataResponse, 'd.result');
                $result = json_decode($result, true);
                $branches = data_get($result, 'Table', []);
                if(!empty($branches)){
                    $this->addItemsLmsBranch($branches, $ug_type);
                }
            }
        }
        return "ok";
    }

    public function addItemsLmsBranch($list, $ug_type = null)
    {
        if ($list) {
            $created_at = date('Y-m-d H:i:s');
            $query = "INSERT INTO lms_branches (brch_id, brch_name, brch_admission_type, brch_eng_name, brch_target, is_term_free, created_at, ug_type) VALUES ";
            if (count($list) > 5000) {
                for ($i = 0; $i < 5000; $i++) {
                    $item = (object)$list[$i];
                    $query .= "('$item->brch_id', '$item->brch_name', '$item->brch_admission_type', '$item->brch_eng_name', '$item->brch_target', '$item->is_term_free', '$created_at', '$ug_type' ),";
                }
                $query = substr($query, 0, -1);
                u::query($query);
                self::addItemsLmsBranch(array_slice($list, 5000));
            } else {
                foreach ($list as $item) {
                    $item = (object)$item;
                    $query .= "('$item->brch_id', '$item->brch_name', '$item->brch_admission_type', '$item->brch_eng_name', '$item->brch_target', '$item->is_term_free', '$created_at', '$ug_type' ),";
                }
                $query = substr($query, 0, -1);
                u::query($query);
            }
        }
    }

    public function syncDataSemester(){
        $url = sprintf('%s/data/setup.asmx/CounSemester', config('lms.url'));
        $client = new Client();
        u::query("DELETE FROM lms_semesters");
        $listBranches = u::query("SELECT brch_id FROM lms_branches");
        foreach ($listBranches as $row) {
            $params = [
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'setup' => [
                        'hr_brch_id' => $row->brch_id,
                    ],
                    'staff' => [
                        'stf_id' => config('lms.stf_id'),
                    ]
                ]
            ];
            $response = $client->request('POST', $url, $params);
            $dataResponse = json_decode($response->getBody()->getContents(), true);
            // Log::info('LMS Semester Data', ['params' => $params,'data' => $dataResponse]);
            if (data_get($dataResponse, 'd.result')){
                $result = data_get($dataResponse, 'd.result');
                $result = json_decode($result, true);
                $semesters = data_get($result, 'Table', []);
                if(!empty($semesters)){
                    $this->addItemsSemester($semesters, $row->brch_id);
                }
            }
        }
        return "ok";
    }

    public function addItemsSemester($list, $brch_id = null)
    {
        if ($list) {
            $created_at = date('Y-m-d H:i:s');
            $query = "INSERT INTO lms_semesters (brch_id, bsem_id, bsem_name, bsem_order, brch_target, is_test_branch, is_use_crm, is_term_free, created_at) VALUES ";
            if (count($list) > 5000) {
                for ($i = 0; $i < 5000; $i++) {
                    $item = (object)$list[$i];
                    $query .= "('$brch_id', '$item->bsem_id', '$item->bsem_name', '$item->bsem_order', '$item->brch_target', '$item->is_test_branch', '$item->is_use_crm', '$item->is_use_crm', '$created_at' ),";
                }
                $query = substr($query, 0, -1);
                u::query($query);
                self::addItemsSemester(array_slice($list, 5000));
            } else {
                foreach ($list as $item) {
                    $item = (object)$item;
                    $query .= "('$brch_id', '$item->bsem_id', '$item->bsem_name', '$item->bsem_order', '$item->brch_target', '$item->is_test_branch', '$item->is_use_crm', '$item->is_use_crm', '$created_at' ),";
                }
                $query = substr($query, 0, -1);
                u::query($query);
            }
        }
    }

    public function syncDataPrograms($bsem_id = null){
        $url = sprintf('%s/staff/counseling/ClassInfoTreeResults.aspx', config('lms.url'));
        $client = new Client();
        u::query("DELETE FROM lms_programs WHERE bsem_id = '$bsem_id'");
        $params = [
            'headers' => [
                'Accept' => 'application/json'
            ],
            'form_params' => [
                'bsem_id' => $bsem_id,
                'cls_isclosed' => '',
            ],
        ];
        $response = $client->request('POST', $url, $params);
        $dataResponse = json_decode($response->getBody()->getContents(), true);
        // Log::info('LMS Class Data', ['params' => $params,'data' => $dataResponse]);
        if ($dataResponse){
            $programs = $dataResponse;
            if(!empty($programs)){
                $this->addItemsPrograms($programs, $bsem_id);
            }
        }
        return "ok";
    }

    public function addItemsPrograms($list, $bsem_id = null)
    {
        if ($list) {
            $created_at = date('Y-m-d H:i:s');
            $query = "INSERT INTO lms_programs (bsem_id, id, parent, `text`, icon, created_at) VALUES ";
            if (count($list) > 5000) {
                $run = 0;
                for ($i = 0; $i < 5000; $i++) {
                    $item = (object)$list[$i];
                    if($item->id){    
                        $query .= "('$bsem_id', '$item->id', '".(int)data_get($item, 'parent')."', '$item->text', '".data_get($item, 'icon')."', '$created_at' ),";
                        $run = 1;
                    }
                }
                if($run){ 
                    $query = substr($query, 0, -1);
                    u::query($query);
                    self::addItemsPrograms(array_slice($list, 5000));  
                }
            } else {
                $run = 0;
                foreach ($list as $item) {
                    $item = (object)$item;
                    if($item->id){    
                        $query .= "('$bsem_id', '$item->id', '".(int)data_get($item, 'parent')."', '$item->text', '".data_get($item, 'icon')."', '$created_at' ),";
                        $run = 1;
                    }
                }
                if($run){ 
                    $query = substr($query, 0, -1);
                    u::query($query);
                }
            }
        }
    }

    public function syncDataClassInfo($cls_id = null){
        $url = sprintf('%s/data/setup.asmx/CounClassInfo', config('lms.url'));
        $client = new Client();
        $params = [
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'counn' => [
                    'conn_cls_id' => $cls_id,
                ],
            ]
        ];
        $response = $client->request('POST', $url, $params);
        $dataResponse = json_decode($response->getBody()->getContents(), true);
        if (data_get($dataResponse, 'd.result')){
            $result = data_get($dataResponse, 'd.result');
            $result = json_decode($result, true);
            $classInfo = data_get($result, 'Table', []);
            $teacherInfo = data_get($result, 'Table1', []);
            $sessionInfo = data_get($result, 'Table2', []);
            if(!empty($classInfo)){
                $classInfo = $classInfo[0];
                $teacherInfo = $teacherInfo[0];
                u::query("DELETE FROM lms_classes WHERE cls_id = '$cls_id'");
                u::query("DELETE FROM lms_sessions WHERE cls_id = '$cls_id'");
                u::insertSimpleRow(array(
                    'cls_id' => $cls_id,
                    'syl_name' => data_get($classInfo, 'syl_name'),
                    'cls_name' => data_get($classInfo, 'cls_name'),
                    'cls_startdate' => data_get($classInfo, 'cls_startdate'),
                    'cls_enddate' => data_get($classInfo, 'cls_enddate'),
                    'capacity' => data_get($classInfo, 'capacity'),
                    'inStuNum' => data_get($classInfo, 'inStuNum'),
                    'capaNum' => data_get($classInfo, 'capaNum'),
                    'used_crm' => data_get($classInfo, 'used_crm'),
                    'ins_name' => data_get($teacherInfo, 'ins_name'),
                    'ins_img' => data_get($teacherInfo, 'ins_img'),
                    'stf_login_id' => data_get($teacherInfo, 'stf_login_id'),
                    'stf_password' => data_get($teacherInfo, 'stf_password'),
                    'stf_id' => data_get($teacherInfo, 'stf_id'),
                    'created_at' => date('Y-m-d H:i:s'),
                ), 'lms_classes');
                foreach ($sessionInfo as $item) {
                    u::insertSimpleRow(array(
                        'cls_id' => $cls_id,
                        'ctime_day_type' => data_get($item, 'ctime_day_type'),
                        'ctime_start_time_type' => data_get($item, 'ctime_start_time_type'),
                        'ctime_end_time_type' => data_get($item, 'ctime_end_time_type'),
                        'room_name' => data_get($item, 'room_name'),
                        'created_at' => date('Y-m-d H:i:s'),
                    ), 'lms_sessions');
                }
                
            }
        }
        return "ok";
    }

    public function syncDataSchedules($cls_id = null){
        $url = sprintf('%s/data/setup.asmx/CounStudentFirstLectureDate', config('lms.url'));
        $client = new Client();
        $params = [
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'counn' => [
                    'conn_cls_id' => $cls_id,
                ],
            ]
        ];
        $response = $client->request('POST', $url, $params);
        $dataResponse = json_decode($response->getBody()->getContents(), true);
        if (data_get($dataResponse, 'd.result')){
            $result = data_get($dataResponse, 'd.result');
            $result = json_decode($result, true);
            $schedules = data_get($result, 'Table', []);
            if(!empty($schedules)){
                u::query("DELETE FROM lms_schedules WHERE cls_id = '$cls_id'");
                $this->addItemsSchedules($schedules, $cls_id);
            }
        }
        return "ok";
    }

    public function addItemsSchedules($list, $cls_id = null)
    {
        if ($list) {
            $created_at = date('Y-m-d H:i:s');
            $query = "INSERT INTO lms_schedules (cls_id, cjrn_id, cjrn_classdate, created_at) VALUES ";
            if (count($list) > 5000) {
                $run = 0;
                for ($i = 0; $i < 5000; $i++) {
                    $item = (object)$list[$i];
                    if($item->cjrn_id){    
                        $query .= "('$item->cls_id', '$item->cjrn_id', '$item->cjrn_classdate', '$created_at' ),";
                        $run = 1;
                    }
                }
                if($run){ 
                    $query = substr($query, 0, -1);
                    u::query($query);
                    self::addItemsSchedules(array_slice($list, 5000));  
                }
            } else {
                $run = 0;
                foreach ($list as $item) {
                    $item = (object)$item;
                    if($item->cjrn_id){    
                        $query .= "('$item->cls_id', '$item->cjrn_id', '$item->cjrn_classdate', '$created_at' ),";
                        $run = 1;
                    }
                }
                if($run){ 
                    $query = substr($query, 0, -1);
                    u::query($query);
                }
            }
        }
    }

    public function syncDataClassStudents($cls_id = null){
        $url = sprintf('%s/data/setup.asmx/CounClassInfoStudentsList', config('lms.url'));
        $client = new Client();
        $params = [
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'counn' => [
                    'coun_cls_id' => $cls_id,
                ],
            ]
        ];
        $response = $client->request('POST', $url, $params);
        $dataResponse = json_decode($response->getBody()->getContents(), true);
        if (data_get($dataResponse, 'd.result')){
            $result = data_get($dataResponse, 'd.result');
            $result = json_decode($result, true);
            $students = data_get($result, 'Table', []);
            if(!empty($students)){
                u::query("DELETE FROM lms_class_students WHERE cls_id = '$cls_id'");
                $this->addItemsClassStudents($students, $cls_id);
            }
        }
        return "ok";
    }

    public function addItemsClassStudents($list, $cls_id = null)
    {
        if ($list) {
            $created_at = date('Y-m-d H:i:s');
            $query = "INSERT INTO lms_class_students (cls_id, std_name, std_nickname, std_id, std_gender, std_grade_type, cstd_startdate, cstd_enddate, S_Status, C_Status, cstd_id, status_check, isnew, cjrn_id, chk, brch_id, cstd_startdate1, code_order, std_status, spto_name, used_crm, `point`, `ranking`, `No`, created_at) VALUES ";
            if (count($list) > 5000) {
                $run = 0;
                for ($i = 0; $i < 5000; $i++) {
                    $item = (object)$list[$i];
                    if($item->cjrn_id){    
                        $query .= "('$item->cls_id', '$item->std_name', '$item->std_nickname', '$item->std_id','$item->std_gender','$item->std_grade_type','$item->cstd_startdate','$item->cstd_enddate','$item->S_Status','$item->C_Status','$item->cstd_id','$item->status_check','".(int)$item->isnew."','$item->cjrn_id','$item->chk','$item->brch_id','$item->cstd_startdate1','$item->code_order','$item->std_status','$item->spto_name','$item->used_crm','".($item->point ? $item->point : 0)."','$item->ranking','$item->No', '$created_at' ),";
                        $run = 1;
                    }
                }
                if($run){ 
                    $query = substr($query, 0, -1);
                    u::query($query);
                    self::addItemsClassStudents(array_slice($list, 5000));  
                }
            } else {
                $run = 0;
                foreach ($list as $item) {
                    $item = (object)$item;
                    if($item->cjrn_id){    
                        $query .= "('$item->cls_id', '$item->std_name', '$item->std_nickname', '$item->std_id','$item->std_gender','$item->std_grade_type','$item->cstd_startdate','$item->cstd_enddate','$item->S_Status','$item->C_Status','$item->cstd_id','$item->status_check','".(int)$item->isnew."','$item->cjrn_id','$item->chk','$item->brch_id','$item->cstd_startdate1','$item->code_order','$item->std_status','$item->spto_name','$item->used_crm','".($item->point ? $item->point : 0)."','$item->ranking','$item->No', '$created_at' ),";
                        $run = 1;
                    }
                }
                if($run){ 
                    $query = substr($query, 0, -1);
                    u::query($query);
                }
            }
        }
    }
}
