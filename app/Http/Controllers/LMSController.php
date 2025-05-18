<?php

namespace App\Http\Controllers;

use App\User;
use App\Http\Controllers\Controller;
use App\Providers\UtilityServiceProvider as u;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LMSController extends Controller
{
    public function syncDataBranch()
    {
        $url = sprintf('%s/data/setup.asmx/CounStaffBranch', config('lms.url'));
        $client = new Client();
        $arr_ug_type = ['APRIL', 'i-GARTEN', 'CDI'];
        // u::query("DELETE FROM lms_branches");
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
            if (data_get($dataResponse, 'd.result')) {
                $result = data_get($dataResponse, 'd.result');
                $result = json_decode($result, true);
                $branches = data_get($result, 'Table', []);
                if (!empty($branches)) {
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
                $query .= " ON DUPLICATE KEY UPDATE `brch_id` = VALUES(`brch_id`)";
                u::query($query);
                self::addItemsLmsBranch(array_slice($list, 5000));
            } else {
                foreach ($list as $item) {
                    $item = (object)$item;
                    $query .= "('$item->brch_id', '$item->brch_name', '$item->brch_admission_type', '$item->brch_eng_name', '$item->brch_target', '$item->is_term_free', '$created_at', '$ug_type' ),";
                }
                $query = substr($query, 0, -1);
                $query .= " ON DUPLICATE KEY UPDATE `brch_id` = VALUES(`brch_id`)";
                u::query($query);
            }
        }
    }

    public function erpCreateBranch()
    {
        $listBranches = u::query("SELECT DISTINCT brch_name FROM lms_branches WHERE branch_id IS NULL");
        foreach ($listBranches as $k => $row) {
            $branch_info = u::query("SELECT id FROM branches WHERE name = '" . data_get($row, 'brch_name') . "'");
            if (!$branch_info) {
                $k++;
                $branch_id = u::insertSimpleRow(array(
                    'name' => data_get($row, 'brch_name'),
                    'code' => "ANT" . ($k < 10 ? "0" . $k : $k),
                    'created_at' => date('Y-m-d H:i:s'),
                    'status' =>  1,
                ), 'branches');
                u::updateSimpleRow(array('branch_id' => $branch_id), array('brch_name' => data_get($row, 'brch_name')), 'lms_branches');
            }
        }
        return "ok";
    }

    public function syncDataSemester()
    {
        $url = sprintf('%s/data/setup.asmx/CounSemester', config('lms.url'));
        $client = new Client();
        // u::query("DELETE FROM lms_semesters");
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
            if (data_get($dataResponse, 'd.result')) {
                $result = data_get($dataResponse, 'd.result');
                $result = json_decode($result, true);
                $semesters = data_get($result, 'Table', []);
                if (!empty($semesters)) {
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
                $query .= " ON DUPLICATE KEY UPDATE `bsem_id` = VALUES(`bsem_id`)";
                u::query($query);
                self::addItemsSemester(array_slice($list, 5000));
            } else {
                foreach ($list as $item) {
                    $item = (object)$item;
                    $query .= "('$brch_id', '$item->bsem_id', '$item->bsem_name', '$item->bsem_order', '$item->brch_target', '$item->is_test_branch', '$item->is_use_crm', '$item->is_use_crm', '$created_at' ),";
                }
                $query = substr($query, 0, -1);
                $query .= " ON DUPLICATE KEY UPDATE `bsem_id` = VALUES(`bsem_id`)";
                u::query($query);
            }
        }
    }

    public function erpCreateSemester()
    {
        $created_at = date('Y-m-d H:i:s');
        $listSemesters = u::query("SELECT s.*, b.branch_id, b.ug_type FROM lms_semesters AS s 
                LEFT JOIN lms_branches AS b ON s.brch_id = b.brch_id
                LEFT JOIN semesters AS sem ON s.bsem_id = sem.lms_id
            WHERE b.brch_id IS NOT NULL AND sem.id IS NULL");
        if ($listSemesters) {
            $query = "INSERT INTO semesters (lms_id, `name`, branch_id, product_id,  created_at) VALUES ";
            foreach ($listSemesters as $k => $row) {
                $product_id = $this->getProductIdByCode($row->ug_type);
                $query .= "('$row->bsem_id', '$row->bsem_name', '$row->branch_id', '$product_id',  '$created_at' ),";
            }
            $query = substr($query, 0, -1);
            $query .= " ON DUPLICATE KEY UPDATE `lms_id` = VALUES(`lms_id`)";
            u::query($query);
        }
        return "ok";
    }

    private function getProductIdByCode($type)
    {
        $product_id = null;
        switch ($type) {
            case 'APRIL':
                $product_id = 1;
                break;
            case 'i-GARTEN':
                $product_id = 2;
                break;
            case 'CDI':
                $product_id = 3;
                break;
            default:
                break;
        }
        return $product_id;
    }

    public function syncDataPrograms($bsem_id = null)
    {
        $url = sprintf('%s/staff/counseling/ClassInfoTreeResults.aspx', config('lms.url'));
        $client = new Client();
        // u::query("DELETE FROM lms_programs WHERE bsem_id = '$bsem_id'");
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
        if ($dataResponse) {
            $programs = $dataResponse;
            if (!empty($programs)) {
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
                    if ($item->id) {
                        $query .= "('$bsem_id', '$item->id', '" . (int)data_get($item, 'parent') . "', '$item->text', '" . data_get($item, 'icon') . "', '$created_at' ),";
                        $run = 1;
                    }
                }
                if ($run) {
                    $query = substr($query, 0, -1);
                    $query .= " ON DUPLICATE KEY UPDATE `bsem_id` = VALUES(`bsem_id`), `id` = VALUES(`id`)";
                    u::query($query);
                    self::addItemsPrograms(array_slice($list, 5000));
                }
            } else {
                $run = 0;
                foreach ($list as $item) {
                    $item = (object)$item;
                    if ($item->id) {
                        $query .= "('$bsem_id', '$item->id', '" . (int)data_get($item, 'parent') . "', '$item->text', '" . data_get($item, 'icon') . "', '$created_at' ),";
                        $run = 1;
                    }
                }
                if ($run) {
                    $query = substr($query, 0, -1);
                    $query .= " ON DUPLICATE KEY UPDATE `bsem_id` = VALUES(`bsem_id`), `id` = VALUES(`id`)";
                    u::query($query);
                }
            }
        }
    }

    public function erpCreateProgram()
    {
        $created_at = date('Y-m-d H:i:s');
        $listPrograms = u::query("SELECT p.*, s.product_id, s.id AS semester_id, s.branch_id FROM lms_programs AS p 
                LEFT JOIN semesters AS s ON s.lms_id=p.bsem_id
                LEFT JOIN programs AS pg ON pg.lms_id=p.id
            WHERE p.id NOT LIKE '%:B' AND pg.id IS NULL");
        if ($listPrograms) {
            $query = "INSERT INTO programs ( `name`, created_at, product_id, branch_id, semester_id, lms_id, lms_parent_id) VALUES ";
            foreach ($listPrograms as $k => $row) {
                $query .= "('$row->text', '$created_at', '$row->product_id', '$row->branch_id', '$row->semester_id', '$row->id', '$row->parent' ),";
            }
            $query = substr($query, 0, -1);
            u::query($query);
        }
        u::query("UPDATE programs AS p LEFT JOIN programs AS p1 ON p.lms_parent_id = p1.lms_id SET p.parent_id = p1.id");
        u::query("UPDATE programs SET parent_id = 0  WHERE parent_id IS NULL");
        return "ok";
    }

    public function syncDataClassInfo($cls_id = null, $syl_id)
    {
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
        if (data_get($dataResponse, 'd.result')) {
            $result = data_get($dataResponse, 'd.result');
            $result = json_decode($result, true);
            $classInfo = data_get($result, 'Table', []);
            $teacherInfo = data_get($result, 'Table1', []);
            $sessionInfo = data_get($result, 'Table2', []);
            if (!empty($classInfo)) {
                $classInfo = $classInfo[0];
                $teacherInfo = $teacherInfo[0];
                u::query("DELETE FROM lms_classes WHERE cls_id = '$cls_id'");
                u::query("DELETE FROM lms_sessions WHERE cls_id = '$cls_id'");
                u::insertSimpleRow(array(
                    'cls_id' => $cls_id,
                    'syl_name' => data_get($classInfo, 'syl_name'),
                    'syl_id' => $syl_id,
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

    public function erpCreateClass()
    {
        $created_at = date('Y-m-d H:i:s');
        $listClass = u::query("SELECT DISTINCT cl.cls_id AS lms_id, cl.cls_name, cl.cls_startdate, cl.cls_enddate, cl.capaNum AS max_students,
                stf_id AS teacher_id, pg.id AS program_id, pg.product_id ,pg.branch_id
            FROM lms_classes AS cl LEFT JOIN lms_programs AS p ON cl.syl_id=p.id 
                LEFT JOIN programs AS pg ON pg.lms_id=p.id 
                LEFT JOIN classes AS c ON c.lms_id = cls_id
            WHERE c.id IS NULL");
        if ($listClass) {
            $dataInsert = [];
            foreach ($listClass as $item) {
                $item = (object)$item;
                $dataInsert[] = [
                    'teacher_id' => data_get($item, 'teacher_id'),
                    'cls_name' => data_get($item, 'cls_name'),
                    'product_id' => data_get($item, 'product_id'),
                    'program_id' => data_get($item, 'program_id'),
                    'cm_id' => data_get($item, 'cm_id'),
                    'cls_startdate' => data_get($item, 'cls_startdate'),
                    'cls_enddate' => data_get($item, 'cls_enddate'),
                    'class_day' => data_get($item, 'class_day'),
                    'created_at' =>  $created_at,
                    'branch_id' => data_get($item, 'branch_id'),
                    'lms_id' => data_get($item, 'lms_id'),
                    'max_students' => data_get($item, 'max_students'),
                    'created_at' => $created_at,
                ];
            }
            if($dataInsert){
                DB::table('classes')->insertOrIgnore($dataInsert);
            }
        }
        return "ok";
    }

    public function getDataRoomSessionInClass($class_id) {
        //Create sessions
        $arrSessions = u::query("SELECT c.id AS class_id , (SELECT id FROM rooms WHERE `name` = ls.room_name LIMIT 1) AS room_id, c.teacher_id,
                ls.ctime_day_type, c.branch_id 
            FROM lms_sessions AS ls 
                LEFT JOIN classes AS c ON c.lms_id = ls.cls_id 
            WHERE c.id= $class_id");
        u::query("DELETE FROM sessions WHERE class_id = $class_id");
        $dataInsert = [];
        foreach($arrSessions AS $session){
            $dataInsert[] = [
                'class_id' => data_get($session, 'class_id'),
                'shift_id' => data_get($session, 'shift_id'),
                'room_id' => data_get($session, 'room_id'),
                'teacher_id' => data_get($session, 'teacher_id'),
                'class_day' => u::convertDayToInt(data_get($session, 'ctime_day_type')),
                'branch_id' => data_get($session, 'branch_id'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
        }
        if($dataInsert){
            DB::table('sessions')->insert($dataInsert);
        }
        u::query("UPDATE classes c
            JOIN (
                SELECT class_id, GROUP_CONCAT(class_day ORDER BY class_day) AS session_days
                FROM sessions
                GROUP BY class_id
            ) s ON c.id = s.class_id
            SET c.class_day = s.session_days WHERE c.id = $class_id");
    }

    public function syncDataSchedules($cls_id = null)
    {
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
        if (data_get($dataResponse, 'd.result')) {
            $result = data_get($dataResponse, 'd.result');
            $result = json_decode($result, true);
            $schedules = data_get($result, 'Table', []);
            if (!empty($schedules)) {
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
                    if ($item->cjrn_id) {
                        $query .= "('$item->cls_id', '$item->cjrn_id', '$item->cjrn_classdate', '$created_at' ),";
                        $run = 1;
                    }
                }
                if ($run) {
                    $query = substr($query, 0, -1);
                    u::query($query);
                    self::addItemsSchedules(array_slice($list, 5000));
                }
            } else {
                $run = 0;
                foreach ($list as $item) {
                    $item = (object)$item;
                    if ($item->cjrn_id) {
                        $query .= "('$item->cls_id', '$item->cjrn_id', '$item->cjrn_classdate', '$created_at' ),";
                        $run = 1;
                    }
                }
                if ($run) {
                    $query = substr($query, 0, -1);
                    u::query($query);
                }
            }
        }
    }

    public function syncDataClassStudents($cls_id = null)
    {
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
        Log::info('LMS syncDataClassStudents Data', ['params' => $params, 'data' => $dataResponse]);
        if (data_get($dataResponse, 'd.result')) {
            $result = data_get($dataResponse, 'd.result');
            $result = json_decode($result, true);
            $students = data_get($result, 'Table', []);
            if (!empty($students)) {
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
                    if ($item->cjrn_id) {
                        $query .= "('$item->cls_id', '$item->std_name', '$item->std_nickname', '$item->std_id','$item->std_gender','$item->std_grade_type','$item->cstd_startdate','$item->cstd_enddate','$item->S_Status','$item->C_Status','$item->cstd_id','$item->status_check','" . (int)$item->isnew . "','$item->cjrn_id','$item->chk','$item->brch_id','$item->cstd_startdate1','$item->code_order','$item->std_status','$item->spto_name','$item->used_crm','" . ($item->point ? $item->point : 0) . "','$item->ranking','$item->No', '$created_at' ),";
                        $run = 1;
                    }
                }
                if ($run) {
                    $query = substr($query, 0, -1);
                    $query .= " ON DUPLICATE KEY UPDATE `cls_id` = VALUES(`cls_id`), `std_id` = VALUES(`std_id`)";
                    u::query($query);
                    self::addItemsClassStudents(array_slice($list, 5000));
                }
            } else {
                $run = 0;
                foreach ($list as $item) {
                    $item = (object)$item;
                    if ($item->cjrn_id) {
                        $query .= "('$item->cls_id', '$item->std_name', '$item->std_nickname', '$item->std_id','$item->std_gender','$item->std_grade_type','$item->cstd_startdate','$item->cstd_enddate','$item->S_Status','$item->C_Status','$item->cstd_id','$item->status_check','" . (int)$item->isnew . "','$item->cjrn_id','$item->chk','$item->brch_id','$item->cstd_startdate1','$item->code_order','$item->std_status','$item->spto_name','$item->used_crm','" . ($item->point ? $item->point : 0) . "','$item->ranking','$item->No', '$created_at' ),";
                        $run = 1;
                    }
                }
                if ($run) {
                    $query = substr($query, 0, -1);
                    $query .= " ON DUPLICATE KEY UPDATE `cls_id` = VALUES(`cls_id`), `std_id` = VALUES(`std_id`)";
                    u::query($query);
                }
            }
        }
    }

    public function addOrUpdateStudent()
    {
        $url = sprintf('%s/data/setup.asmx/CounStudentSave', config('lms.url'));
        $client = new Client();
        $params = [
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
            'json' => [
                "counn" => [
                    "coun_std_type" => "N",
                    "coun_std_id" => 0,  // 0: add new, > 0: update
                    "coun_preferred_brch_id" => "327",
                    "coun_std_name" => "testapi1",
                    "coun_std_email" => "test1@gmail.com",
                    "coun_std_home_phone" => "0389941903",
                    "coun_std_school" => "test1api",
                    "coun_std_nickname" => "testapi",
                    "coun_std_birthdate" => "",
                    "coun_std_gender" => "M",
                    "coun_std_address" => "Ha noi",
                    "coun_std_grade_type" => "toddler 3 age",
                    "coun_std_login" => "",
                    "coun_std_password" => "",
                    "coun_std_photo" => "",
                    "coun_std_guardian1" => "",
                    "coun_std_guardian_email" => "test1@gmail.com",
                    "coun_std_guardian1_tel" => "",
                    "coun_std_guardian2" => "",
                    "coun_std_guardian2_tel" => "",
                    "coun_std_status" => "Active",
                    "coun_std_trialstart" => "2025-04-18",
                    "coun_std_trialend" => "2025-04-20",
                    "coun_sales_stf_id" => 0,
                    "coun_assigned_stf_id" => 0,
                    "coun_std_note" => "",
                    "coun_saddr_isvalid" => 0,
                    "coun_std_istemporary" => 1
                ],
                "staff" => [
                    "stf_id" => 8824
                ]
            ]
        ];
        $response = $client->request('POST', $url, $params);
        $dataResponse = json_decode($response->getBody()->getContents(), true);
        Log::info('LMS syncDataClassStudents Data', ['params' => $params, 'data' => $dataResponse]);
        if (data_get($dataResponse, 'd.status') == 'ok') {
            $student_id = data_get($dataResponse, 'd.message');
        }
        return "ok";
    }

    public function addStudentToClass()
    {
        $url = sprintf('%s/data/setup.asmx/CounStudentSearchSave', config('lms.url'));
        $client = new Client();
        $params = [
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
            'json' => [
                "staff" => [
                    "stf_id" => 8824
                ],
                "counn" => [
                    "coun_std_type" => "add",
                    "conn_cls_id" => "87",
                    "conn_syl_id" => 0,
                    "conn_old_cstd_id" => 0,
                    "conn_cstf_teacher_fault" => 0,
                    "conn_lecture_date" => ""
                ],
                "setupList" => [
                    [
                        "c_std_id" => 24,
                        "c_registration_date" => "2025-01-03",
                        "c_from_cstd_id" => 0
                    ]
                ]
            ]
        ];
        $response = $client->request('POST', $url, $params);
        $dataResponse = json_decode($response->getBody()->getContents(), true);
        Log::info('LMS addStudentToClass Data', ['params' => $params, 'data' => $dataResponse]);
        if (data_get($dataResponse, 'd.status') == 'ok') {
            $student_id = data_get($dataResponse, 'd.message');
        }
        return "ok";
    }

    public function studentTransferClass()
    {
        $url = sprintf('%s/data/setup.asmx/CounStudentClassAddSubmit', config('lms.url'));
        $client = new Client();
        $params = [
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
            'json' => [
                "counn" => [
                    "coun_std_type" => "transfer",
                    "conn_cls_id" => 149,
                    "coun_std_id" => 24,
                    "coun_registration_date" => "2024-12-24",
                    "coun_payment_date" => "",
                    "conn_from_cstd_id" => 0,
                    "conn_syl_id" => 15,
                    "conn_old_cstd_id" => 838,
                    "conn_cstf_teacher_fault" => 0,
                    "conn_lecture_date" => ""
                ],
                "staff" => [
                    "stf_id" => 8824
                ]
            ]
        ];
        $response = $client->request('POST', $url, $params);
        $dataResponse = json_decode($response->getBody()->getContents(), true);
        Log::info('LMS studentTransferClass Data', ['params' => $params, 'data' => $dataResponse]);
        if (data_get($dataResponse, 'd.status') == 'ok') {
        }
        return "ok";
    }

    public function studentWithdraw()
    {
        $url = sprintf('%s/data/setup.asmx/CounStudentWithdrawSave', config('lms.url'));
        $client = new Client();
        $params = [
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
            'json' => [
                "counn" => [
                    "coun_std_id" => "24",
                    "coun_cstd_id" => "839",
                    "coun_istd_type" => "Homework - Too much homework",
                    "coun_Reservation" => 0,
                    "coun_is_reserved_date" => "",
                    "coun_work_type" => "S",
                ],
                "staff" => ["stf_id" => 8824],
            ]
        ];
        $response = $client->request('POST', $url, $params);
        $dataResponse = json_decode($response->getBody()->getContents(), true);
        Log::info('LMS studentWithdraw Data', ['params' => $params, 'data' => $dataResponse]);
        if (data_get($dataResponse, 'd.status') == 'ok') {
        }
        return "ok";
    }

    public function syncDataRoom($brch_id = null)
    {
        $url = sprintf('%s/data/setup.asmx/AcadRoomSetupList', config('lms.url'));
        $client = new Client();
        $params = [
            'headers' => [
                'Accept' => 'application/json'
            ],
            'json' => [
                "acad" => [
                    "acad_brch_id" => $brch_id,
                    "acad_room_id" => 0
                ]
            ]
        ];
        $response = $client->request('POST', $url, $params);
        $dataResponse = json_decode($response->getBody()->getContents(), true);
        // Log::info('LMS Class Data', ['params' => $params,'data' => $dataResponse]);
        if ($dataResponse) {
            $result = data_get($dataResponse, 'd.result');
            $result = json_decode($result, true);
            $rooms = data_get($result, 'Table', []);
            if (!empty($rooms)) {
                $this->addItemsRoom($rooms, $brch_id);
            }
        }
        return "ok";
    }

    public function addItemsRoom($list, $brch_id = null)
    {
        if ($list) {
            $created_at = date('Y-m-d H:i:s');
            $query = "INSERT INTO lms_rooms (room_id, room_name, `bldg_code`, bldg_name, room_isclass, brch_id, created_at) VALUES ";
            if (count($list) > 5000) {
                $run = 0;
                for ($i = 0; $i < 5000; $i++) {
                    $item = (object)$list[$i];
                    if ($item->room_id) {
                        $query .= "('$item->room_id', '$item->room_name', '$item->bldg_code', '$item->bldg_name', '" . (int)$item->room_isclass . "', '$brch_id','$created_at' ),";
                        $run = 1;
                    }
                }
                if ($run) {
                    $query = substr($query, 0, -1);
                    $query .= " ON DUPLICATE KEY UPDATE `room_id` = VALUES(`room_id`)";
                    u::query($query);
                    self::addItemsPrograms(array_slice($list, 5000));
                }
            } else {
                $run = 0;
                foreach ($list as $item) {
                    $item = (object)$item;
                    if ($item->room_id) {
                        $query .= "('$item->room_id', '$item->room_name', '$item->bldg_code', '$item->bldg_name', '" . (int)$item->room_isclass . "', '$brch_id','$created_at' ),";
                        $run = 1;
                    }
                }
                if ($run) {
                    $query = substr($query, 0, -1);
                    $query .= " ON DUPLICATE KEY UPDATE `room_id` = VALUES(`room_id`)";
                    u::query($query);
                }
            }
        }
    }

    public function erpCreateRoom()
    {
        $created_at = date('Y-m-d H:i:s');
        $listRooms = u::query("SELECT r.*, b.branch_id FROM lms_rooms AS r LEFT JOIN lms_branches AS b ON b.brch_id=r.brch_id 
            LEFT JOIN rooms AS rm ON rm.lms_id=r.room_id
            WHERE rm.id IS NULL");
        if ($listRooms) {
            $query = "INSERT INTO rooms ( `name`, created_at, branch_id, lms_id, `status`) VALUES ";
            foreach ($listRooms as $k => $row) {
                $query .= "('$row->room_name', '$created_at', '$row->branch_id', '$row->room_id', 1 ),";
            }
            $query = substr($query, 0, -1);
            u::query($query);
        }
        return "ok";
    }

    public function syncDataTeacher($brch_id = null, $ug_type = null, $branch_id = null)
    {
        $url = sprintf('%s/data/setup.asmx/HRStaffList', config('lms.url'));
        $client = new Client();
        $params = [
            'headers' => [
                'Accept' => 'application/json'
            ],
            'json' =>  [
                'setup' => [
                    "hr_brch_id" => $brch_id,
                    "hr_brch_schl_type" => $ug_type,
                    "hr_stf_status" => "",
                    "hr_stf_type" => "Teacher",
                    "hr_stf_name" => ""
                ]
            ]
        ];
        $response = $client->request('POST', $url, $params);
        $dataResponse = json_decode($response->getBody()->getContents(), true);
        if ($dataResponse) {
            $result = data_get($dataResponse, 'd.result');
            $result = json_decode($result, true);
            $teachers = data_get($result, 'Table', []);
            if (!empty($teachers)) {
                $this->addItemsTeacher($teachers, $brch_id, $branch_id);
            }
        }
        return "ok";
    }

    public function addItemsTeacher($list, $brch_id = null, $branch_id)
    {
        if ($list) {
            $created_at = date('Y-m-d H:i:s');
            if (count($list) > 5000) {
                $dataInsert = [];
                for ($i = 0; $i < 5000; $i++) {
                    $item = (object)$list[$i];
                    $dataInsert[] = [
                        'stf_id' => $item->stf_id,
                        'brch_schl_type' => $item->brch_schl_type,
                        'brch_name' => $item->brch_name,
                        'full_name' => $item->full_name,
                        'stf_status' => $item->stf_status,
                        'tm_isteacher' => $item->tm_isteacher,
                        'stf_login_id' => $item->stf_login_id,
                        'stf_password_encryption' => $item->stf_password_encryption,
                        'stf_email' => $item->stf_email,
                        'stf_id' => $item->stf_id,
                        'stf_gender' => $item->stf_gender,
                        'stf_image' => $item->stf_image,
                        'brch_id' => $brch_id,
                        'created_at' => $created_at,
                    ];
                }
                if($dataInsert){
                    DB::table('lms_teachers')->insertOrIgnore($dataInsert);
                }
                self::addItemsTeacher(array_slice($list, 5000), $brch_id = null, $branch_id);  
            } else {
                $dataInsert = [];
                foreach ($list as $item) {
                    $item = (object)$item;
                    $dataInsert[] = [
                        'stf_id' => $item->stf_id,
                        'brch_schl_type' => $item->brch_schl_type,
                        'brch_name' => $item->brch_name,
                        'full_name' => $item->full_name,
                        'stf_status' => $item->stf_status,
                        'tm_isteacher' => $item->tm_isteacher,
                        'stf_login_id' => $item->stf_login_id,
                        'stf_password_encryption' => $item->stf_password_encryption,
                        'stf_email' => $item->stf_email,
                        'stf_id' => $item->stf_id,
                        'stf_gender' => $item->stf_gender,
                        'stf_image' => $item->stf_image,
                        'brch_id' => $brch_id,
                        'created_at' => $created_at,
                    ];
                }
                if($dataInsert){
                    DB::table('lms_teachers')->insertOrIgnore($dataInsert);
                }
            }
        }
    }
}
