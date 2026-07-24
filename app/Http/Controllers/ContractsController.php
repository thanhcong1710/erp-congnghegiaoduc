<?php

namespace App\Http\Controllers;

use App\Enums\SystemCode;
use App\User;
use App\Http\Controllers\Controller;
use App\Models\LogClassStudent;
use App\Models\LogStudents;
use App\Models\LogParents;
use App\Providers\UtilityServiceProvider as u;
use App\Services\TicketService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContractsController extends Controller
{
    /**
     * Load danh sách lớp để xếp lớp ngay khi nhập học (form Đăng ký mới).
     * Điều kiện:
     * - class.cls_startdate <= CURRENT_DATE
     * - class.cls_enddate >= CURRENT_DATE
     * - class.product_id = product_id của gói lẻ hoặc gói đầu tiên của combo
     */
    public function loadClassesForEnrolment(Request $request)
    {
        $branch_id = (int) data_get($request, 'branch_id');
        $tuition_fee_id = (int) data_get($request, 'tuition_fee_id');
        $tuition_fee_type = (int) data_get($request, 'tuition_fee_type'); // 1: lẻ, 2: combo

        if (!$branch_id || !$tuition_fee_id || !$tuition_fee_type) {
            return response()->json([]);
        }

        $product_id = 0;
        if ($tuition_fee_type === 1) {
            $fee = u::first("SELECT product_id FROM tuition_fee WHERE id = $tuition_fee_id LIMIT 1");
            $product_id = (int) data_get($fee, 'product_id');
        } elseif ($tuition_fee_type === 2) {
            $firstFee = u::first("SELECT t.product_id
                FROM tuition_fee_relation r
                    LEFT JOIN tuition_fee t ON t.id = r.exchange_tuition_fee_id
                WHERE r.status = 1 AND r.tuition_fee_id = $tuition_fee_id
                ORDER BY r.stt ASC
                LIMIT 1");
            $product_id = (int) data_get($firstFee, 'product_id');
        }

        if (!$product_id) {
            return response()->json([]);
        }

        $current_class_id = (int) data_get($request, 'current_class_id', 0);
        $where_date = "AND (c.cls_startdate >= CURRENT_DATE " . ($current_class_id > 0 ? " OR c.id = $current_class_id" : "") . ")";

        $classes = u::query("SELECT c.id, c.cls_name AS label,
                c.cls_startdate, c.cls_enddate, c.max_students,
                (SELECT COUNT(s.id) FROM contracts s WHERE s.class_id = c.id AND s.status != 0) AS enrolled_students,
                (SELECT CONCAT(u.name, ' - ', u.hrm_id) FROM users u WHERE u.id = c.teacher_id) AS teacher_name,
                (SELECT CONCAT(u.name, ' - ', u.hrm_id) FROM users u WHERE u.id = c.cm_id) AS cm_name,
                c.class_day,
                CASE 
                    WHEN c.cls_startdate > CURRENT_DATE THEN 'Sắp khai giảng'
                    WHEN c.cls_enddate < CURRENT_DATE THEN 'Đã kết thúc'
                    ELSE 'Đang diễn ra'
                END AS status_text,
                CASE 
                    WHEN (SELECT COUNT(s.id) FROM contracts s WHERE s.class_id = c.id AND s.status != 0 ) >= c.max_students THEN 'Đã đầy'
                    WHEN (SELECT COUNT(s.id) FROM contracts s WHERE s.class_id = c.id AND s.status != 0) >= c.max_students * 0.8 THEN 'Sắp đầy'
                    ELSE 'Còn chỗ'
                END AS availability_text
            FROM classes c
            WHERE c.status = 1
                AND c.branch_id = $branch_id
                AND c.product_id = $product_id
                $where_date
            HAVING (enrolled_students < max_students" . ($current_class_id > 0 ? " OR c.id = $current_class_id" : "") . ")
            ORDER BY c.cls_startdate DESC, c.cls_name DESC");

        return response()->json($classes);
    }

    /**
     * Xếp lớp cho 1 contract (logic giống EnrolmentsController@addStudent nhưng dành cho 1 học sinh).
     */
    private function enrolContractToClass(int $contract_id, int $student_id, int $class_id, int $branch_id, int $product_id): void
    {
        $class_info = u::getObject(['id' => $class_id], 'classes');
        if (!$class_info) {
            return;
        }
        $start_date = data_get($class_info,'cls_startdate');
        // Safety: chỉ cho xếp đúng trung tâm & đúng product
        if ((int) data_get($class_info, 'branch_id') !== (int) $branch_id) {
            return;
        }
        if ((int) data_get($class_info, 'product_id') !== (int) $product_id) {
            return;
        }

        $cm_id = data_get($class_info, 'cm_id', null);
        $teacher_id = data_get($class_info, 'teacher_id', null);
        $cm_leader = u::first("SELECT ul.id
            FROM users AS u
                LEFT JOIN users AS ul ON ul.id=u.manager_id
                LEFT JOIN role_has_user AS ru ON ru.user_id= ul.id
                LEFT JOIN roles AS r ON r.id=ru.role_id
            WHERE r.code = '" . SystemCode::ROLE_CM_LEADER . "' AND ul.status=1 AND u.id = " . (int) data_get($class_info, 'cm_id', 0) . " LIMIT 1");
        $cm_leader_id = data_get($cm_leader, 'id') ? data_get($cm_leader, 'id') : $cm_id;

        $contract = u::getObject(['id' => $contract_id], 'contracts');
        if (!$contract) {
            return;
        }

        $session = (int) data_get($contract, 'real_sessions', 0);
        if ($session <= 0) {
            $session = (int) data_get($contract, 'total_sessions', 0);
        }

        $holidays = u::getPublicHolidays($branch_id, $product_id);
        $arr_day = explode(",", (string) data_get($class_info, 'class_day'));
        $data_sessions = u::calculatorSessionsByNumberOfSessions($start_date, $session, $holidays, $arr_day);

        $agreement = u::getObject(['id' => data_get($contract, 'agreement_id')], 'agreements');
        if ($agreement && (int) data_get($agreement, 'is_first_package') == 1 && empty(data_get($agreement, 'first_8th_session_date'))) {
            $arr_day_filtered = array_filter($arr_day);
            if (count($arr_day_filtered) > 0) {
                $eighth_session_info = u::calculatorSessionsByNumberOfSessions($start_date, 8, $holidays, $arr_day_filtered);
                $first_8th_session_date = data_get($eighth_session_info, 'end_date');
            } else {
                $first_8th_session_date = date('Y-m-d', strtotime($start_date . ' + 28 days'));
            }
            if ($first_8th_session_date) {
                u::updateSimpleRow([
                    'first_8th_session_date' => $first_8th_session_date,
                    'updated_at' => date('Y-m-d H:i:s')
                ], ['id' => data_get($agreement, 'id')], 'agreements');
                u::addLogAgreements(data_get($agreement, 'id'));
            }
        }

        u::updateSimpleRow([
            'cm_id' => $cm_id,
            'cm_leader_id' => $cm_leader_id,
            'program_id' => data_get($class_info, 'program_id', null),
            'class_id' => data_get($class_info, 'id', null),
            'enrolment_start_date' => $start_date,
            'enrolment_last_date' => data_get($data_sessions, 'end_date'),
            // 'status' => 6,
            'updated_at' => date('Y-m-d H:i:s'),
            'updator_id' => Auth::user()->id,
        ], ['id' => $contract_id], 'contracts');
        u::addLogContracts($contract_id);

        u::updateSimpleRow([
            'cm_id' => $cm_id,
            'teacher_id' => $teacher_id,
            'cm_leader_id' => $cm_leader_id,
            'updated_at' => date('Y-m-d H:i:s'),
            'updator_id' => Auth::user()->id
        ], ['student_id' => $student_id], 'term_student_user');

        LogStudents::logAdd($student_id, 'Xếp vào lớp ' . data_get($class_info, 'cls_name'), Auth::user()->id);
        LogClassStudent::logAction($class_id, $student_id, $contract_id, 1, Auth::user()->id);

        $actions = ['Phát sách', 'Thông báo lịch học'];
        TicketService::createTicketsForStudentEnrollment($student_id, $class_id, $contract_id, $actions);
    }

    public function loadTuitionFee(Request $request)
    {
        $branch_id = $request->branch_id;
        $study_type = $request->study_type ?? null; // 1: Offline, 2: Online, null: Tất cả

        $where_type = "";
        if ($study_type !== null && $study_type !== '') {
            $where_type = " AND (t.type = 0 OR t.type = $study_type)";
        }

        $data = u::query("SELECT t.name, t.id, t.price, t.receivable,t.session, t.type_fee, t.product_id, t.type, '' AS label, '' AS tuition_fee_relation
            FROM tuition_fee AS t 
            WHERE t.status=1 AND t.available_date <= CURRENT_DATE AND expired_date >= CURRENT_DATE 
            AND ( t.branch_id LIKE '$branch_id,%' OR t.branch_id LIKE '%,$branch_id,%' OR t.branch_id LIKE '%,$branch_id' OR t.branch_id = '$branch_id' ) 
            $where_type
            ORDER BY t.type_fee, t.price  ");
        foreach ($data as $k => $row) {
            $data[$k]->label = data_get($row, 'name') . " (" . number_format(data_get($row, 'price')) . "đ)";
            $data[$k]->tuition_fee_relation = u::query("SELECT r.price_combo, t.price, t.name, t.session, t.product_id FROM tuition_fee_relation AS r 
                LEFT JOIN tuition_fee AS t ON r.exchange_tuition_fee_id=t.id WHERE r.tuition_fee_id = " . data_get($row, 'id'));
        }
        return response()->json($data);
    }

    public function loadDiscountCode(Request $request)
    {
        $tuition_fee_id = $request->tuition_fee_id;
        $data = u::query("SELECT d.name, d.id, d.percent, d.discount
            FROM discount_codes AS d 
            WHERE d.status=1 AND d.start_date <= CURRENT_DATE AND d.end_date >= CURRENT_DATE 
            AND ( d.fee_ids LIKE '$tuition_fee_id,%' OR d.fee_ids LIKE '%,$tuition_fee_id,%' OR d.fee_ids LIKE '%,$tuition_fee_id' OR d.fee_ids = '$tuition_fee_id' ) 
            ORDER BY d.name DESC");
        return response()->json($data);
    }

    public function checkCoupon(Request $request)
    {
        $coupon_code = $request->coupon_code;
        $data = u::first("SELECT c.id, c.status, c.end_date, c.start_date, c.coupon_amount,c.coupon_session
            FROM coupons AS c 
            WHERE c.code='$coupon_code' AND c.status!=0 ");
        if ($data) {
            $message = "";
            if ($data->status == 2) {
                $message = "Mã voucher đã được sử dụng";
            } elseif ($data->end_date < date('Y-m-d')) {
                $message = "Mã voucher đã hết hạn";
            } elseif ($data->start_date > date('Y-m-d')) {
                $message = "Mã voucher chỉ được kích hoạt từ ngày " . $data->start_date;
            }
            if ($message) {
                $result = array(
                    'status' => 0,
                    'message' => $message
                );
            } else {
                $result = array(
                    'status' => 1,
                    'message' => 'ok',
                    'data' => $data
                );
            }
        } else {
            $result = array(
                'status' => 0,
                'message' => 'Mã voucher không hợp lệ'
            );
        }
        return response()->json($result);
    }

    /**
     * Lấy thông tin EC Leader khi chọn EC
     */
    public function getEcLeader(Request $request)
    {
        $ec_id = (int) $request->ec_id;
        $ec_leader = u::first("SELECT u.id, CONCAT(u.name, ' - ', u.hrm_id) AS name
            FROM users AS u
            WHERE u.id = (SELECT manager_id FROM users WHERE id = $ec_id)
            AND u.status = 1
            LIMIT 1");

        return response()->json([
            'status' => 1,
            'data' => $ec_leader
        ]);
    }

    public function add(Request $request)
    {
        $parent_info = u::getObject(['id' => data_get($request, 'parent_id')], 'crm_parents');
        if ($parent_info && $request->has('point_toeic')) {
            u::updateSimpleRow(
                ['point_toeic' => $request->point_toeic],
                ['id' => $parent_info->id],
                'crm_parents'
            );
        }
        if (!data_get($parent_info, 'student_id')) {
            $arr_name = u::explodeName(data_get($parent_info, 'name'));
            $student_id = u::insertSimpleRow(array(
                'lms_code' => '',
                'name' => data_get($parent_info, 'name'),
                'firstname' => data_get($arr_name, 'firstname'),
                'midname' => data_get($arr_name, 'midname'),
                'lastname' => data_get($arr_name, 'lastname'),
                'gender' => data_get($parent_info, 'gender'),
                'date_of_birth' => data_get($parent_info, 'birthday'),
                'gud_mobile1' => data_get($parent_info, 'mobile_1'),
                'gud_email1' => data_get($parent_info, 'email'),
                'address' => data_get($parent_info, 'address'),
                'province_id' => data_get($parent_info, 'province_id'),
                'district_id' => data_get($parent_info, 'district_id'),
                'created_at' => date('Y-m-d H:i:s'),
                'creator_id' => Auth::user()->id,
                'branch_id' => data_get($request, 'branch_id'),
                'status' => 1,
                'source_detail_id' => data_get($parent_info, 'source_detail_id'),
                'source_id' => data_get($parent_info, 'source_id'),
                'avatar_url' => data_get($parent_info, 'gender') == 'F' ? '/images/common/avatar-girl.svg' : '/images/common/avatar-boy.svg'
            ), 'students');
            $last_lms_code = str_pad((string) $student_id, 6, '0', STR_PAD_LEFT);
            $lms_code = config('app.prefix_student_code') . $last_lms_code;
            u::updateSimpleRow(array('lms_code' => $lms_code), array('id' => $student_id), 'students');

            $ceo_info = u::first("SELECT u.id FROM role_has_user AS ru 
                LEFT JOIN roles AS r ON r.id = ru.role_id
                LEFT JOIN users AS u ON u.id = ru.user_id
                WHERE u.status=1 AND r.code ='" . SystemCode::ROLE_CEO_BRANCH . "'");
            
            $input_ec_id = data_get($request, 'ec_id') ? (int) data_get($request, 'ec_id') : (int) data_get($parent_info, 'owner_id');
            $ec_info = u::first("SELECT u.id, u.manager_id FROM users AS u WHERE u.status=1 AND u.id = " . $input_ec_id);
            $ec_id = data_get($ec_info, 'id');
            $ec_leader_id = data_get($ec_info, 'manager_id') ? data_get($ec_info, 'manager_id'): $ec_id;
            
            u::insertSimpleRow(array(
                'student_id' => $student_id,
                'ec_id' => $ec_id,
                'branch_id' => data_get($request, 'branch_id'),
                'ceo_branch_id' => data_get($ceo_info, 'id'),
                'ec_leader_id' => $ec_leader_id,
                'created_at' => date('Y-m-d H:i:s'),
                'creator_id' => Auth::user()->id,
                'status' => 1
            ), 'term_student_user');
            u::updateSimpleRow(array('student_id' => $student_id), array('id' => data_get($request, 'parent_id')), 'crm_parents');
        } else {
            $student_id = data_get($parent_info, 'student_id');
            $input_ec_id = data_get($request, 'ec_id') ? (int) data_get($request, 'ec_id') : (int) data_get($parent_info, 'owner_id');
            $ec_info = u::first("SELECT u.id, u.manager_id FROM users AS u WHERE u.status=1 AND u.id = " . $input_ec_id);
            $ec_id = data_get($ec_info, 'id');
            $ec_leader_id = data_get($ec_info, 'manager_id') ? data_get($ec_info, 'manager_id'): $ec_id;
        }
        // Tính count_recharge cho agreement: 1 nếu đã có gói trước đó (status > 0), 0 nếu là gói đầu
        $first_agreement = u::first("SELECT id, first_8th_session_date FROM agreements WHERE student_id = $student_id AND status > 0 ORDER BY id ASC LIMIT 1");
        $count_recharge = $first_agreement ? 1 : 0;
        $is_first_package = $first_agreement ? 0 : 1;
        $first_8th = $first_agreement ? data_get($first_agreement, 'first_8th_session_date') : null;

        $agreement_id = u::insertSimpleRow(array(
            'student_id' => $student_id,
            'branch_id' => data_get($request, 'branch_id'),
            'ec_id' => $ec_id,
            'ec_leader_id' => $ec_leader_id,
            'type_fee' => data_get($request, 'tuition_fee_type'),
            'tuition_fee_id' => data_get($request, 'tuition_fee_id'),
            'must_charge' => data_get($request, 'tuition_fee_amount'),
            'debt_amount' => data_get($request, 'tuition_fee_amount'),
            'start_date' => data_get($request, 'start_date'),
            'note' => data_get($request, 'note'),
            'book_receive' => data_get($request, 'book_receive', 0),
            'book_receive_address' => data_get($request, 'book_receive_address', ''),
            'contract_receive' => data_get($request, 'contract_receive', 0),
            'group_type' => data_get($request, 'group_type', 0),
            'count_recharge' => $count_recharge,
            'is_first_package' => $is_first_package,
            'first_8th_session_date' => $first_8th,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'creator_id' => Auth::user()->id,
        ), 'agreements');
        $agreement_code = str_pad((string) $agreement_id, 6, '0', STR_PAD_LEFT);
        $agreement_code = config('app.prefix_agreement_code') . $agreement_code;
        u::updateSimpleRow(array('code' => $agreement_code), array('id' => $agreement_id), 'agreements');
        u::addLogAgreements($agreement_id);

        if (data_get($request, 'tuition_fee_type') == 1) {
            $tuition_fee_info = u::getObject(['id' => data_get($request, 'tuition_fee_id')], 'tuition_fee');
            if ($tuition_fee_info) {
                $contract_id = u::insertSimpleRow(array(
                    'type' => 1,
                    'student_id' => $student_id,
                    'branch_id' => data_get($request, 'branch_id'),
                    'ec_id' => $ec_id,
                    'ec_leader_id' => $ec_leader_id,
                    'product_id' => data_get($tuition_fee_info, 'product_id'),
                    'tuition_fee_id' => data_get($tuition_fee_info, 'id'),
                    'init_tuition_fee_id' => data_get($tuition_fee_info, 'id'),
                    'init_tuition_fee_amount' => data_get($tuition_fee_info, 'price'),
                    'init_tuition_fee_session' => data_get($tuition_fee_info, 'session'),
                    'init_total_charged' => 0,
                    'must_charge' => data_get($tuition_fee_info, 'price'),
                    'total_charged' => 0,
                    'debt_amount' => data_get($tuition_fee_info, 'price'),
                    'total_sessions' => data_get($tuition_fee_info, 'session'),
                    'real_sessions' => data_get($tuition_fee_info, 'session'),
                    'bonus_sessions' => 0,
                    'summary_sessions' => 0, // chưa đóng phí
                    'reservable_sessions' => 0, // khi nào có buổi summary_sessions mới được bảo lưu,
                    'start_date' => data_get($request, 'start_date'),
                    'note' => data_get($request, 'note'),
                    'created_at' => date('Y-m-d H:i:s'),
                    'creator_id' => Auth::user()->id,
                    'status' => 1,
                    'count_recharge' => 1,
                    'agreement_id' => $agreement_id
                ), 'contracts');
                $contract_code = str_pad((string) $contract_id, 6, '0', STR_PAD_LEFT);
                $contract_code = config('app.prefix_contract_code') . $contract_code;
                u::updateSimpleRow(array('code' => $contract_code), array('id' => $contract_id), 'contracts');
                u::addLogContracts($contract_id);
                LogStudents::logAdd($student_id, 'Thêm mới hợp đồng nhập học - ' . $contract_code, Auth::user()->id);
            }
        } elseif (data_get($request, 'tuition_fee_type') == 2) {
            $relation_tuition_fee = u::query("SELECT t.*, r.price_combo, r.stt FROM tuition_fee_relation AS r 
                LEFT JOIN tuition_fee AS t ON r.exchange_tuition_fee_id=t.id 
                WHERE r.status=1 AND r.tuition_fee_id = " . data_get($request, 'tuition_fee_id'));
            foreach ($relation_tuition_fee as $tuition_fee_info) {
                $contract_id = u::insertSimpleRow(array(
                    'type' => 1,
                    'student_id' => $student_id,
                    'branch_id' => data_get($request, 'branch_id'),
                    'ec_id' => $ec_id,
                    'ec_leader_id' => $ec_leader_id,
                    'product_id' => data_get($tuition_fee_info, 'product_id'),
                    'tuition_fee_id' => data_get($tuition_fee_info, 'id'),
                    'init_tuition_fee_id' => data_get($tuition_fee_info, 'id'),
                    'init_tuition_fee_amount' => data_get($tuition_fee_info, 'price_combo'),
                    'init_tuition_fee_session' => data_get($tuition_fee_info, 'session'),
                    'init_total_charged' => 0,
                    'must_charge' => data_get($tuition_fee_info, 'price_combo'),
                    'total_charged' => 0,
                    'debt_amount' => data_get($tuition_fee_info, 'price_combo'),
                    'total_sessions' => data_get($tuition_fee_info, 'session'),
                    'real_sessions' => data_get($tuition_fee_info, 'session'),
                    'bonus_sessions' => 0,
                    'summary_sessions' => 0, // chưa đóng phí
                    'reservable_sessions' => 0, // khi nào có buổi summary_sessions mới được bảo lưu,
                    'start_date' => data_get($request, 'start_date'),
                    'note' => data_get($request, 'note'),
                    'created_at' => date('Y-m-d H:i:s'),
                    'creator_id' => Auth::user()->id,
                    'status' => 1,
                    'count_recharge' => data_get($tuition_fee_info, 'stt'),
                    'agreement_id' => $agreement_id
                ), 'contracts');
                $contract_code = str_pad((string) $contract_id, 6, '0', STR_PAD_LEFT);
                $contract_code = config('app.prefix_contract_code') . $contract_code;
                u::updateSimpleRow(array('code' => $contract_code), array('id' => $contract_id), 'contracts');
                u::addLogContracts($contract_id);
                LogStudents::logAdd($student_id, 'Thêm mới hợp đồng nhập học - ' . $contract_code, Auth::user()->id);
            }
        }

        // Xếp lớp ngay khi nhập học (nếu chọn)
        $class_id = (int) data_get($request, 'class_id', 0);
        if ($class_id > 0) {
            // Lấy danh sách contracts vừa tạo
            $created_contracts = u::query("SELECT id, product_id FROM contracts 
                WHERE agreement_id = $agreement_id AND status > 0 
                ORDER BY count_recharge ASC, id ASC");

            if (count($created_contracts) > 0) {
                // Chọn contract đầu tiên để xếp lớp
                $enrol_contract = $created_contracts[0];
                $enrol_contract_id = (int) $enrol_contract->id;
                $product_id = (int) $enrol_contract->product_id;

                $this->enrolContractToClass(
                    $enrol_contract_id,
                    $student_id,
                    $class_id,
                    (int) data_get($request, 'branch_id'),
                    $product_id
                );
            }
        }

        $result = array(
            'status' => 1,
            'message' => 'Thêm mới nhập học thành công',
            'agreement_id' => $agreement_id
        );
        return response()->json($result);
    }

    /**
     * Thêm mới nhập học cho học sinh mới (không cần parent_id)
     */
    public function addWithNewStudent(Request $request)
    {
        // Kiểm tra SĐT đã tồn tại chưa
        $phone = data_get($request, 'student_phone');
        $duplicate_parent = u::first("SELECT p.id, p.name, u.name AS owner_name, u.hrm_id 
            FROM crm_parents AS p 
            LEFT JOIN users AS u ON u.id = p.owner_id
            WHERE (p.mobile_1 = '$phone' OR p.mobile_2 = '$phone')
            LIMIT 1");

        if ($duplicate_parent) {
            return response()->json([
                'status' => 0,
                'message' => "Số điện thoại $phone đã tồn tại trong hệ thống. Khách hàng: {$duplicate_parent->name}, được quản lý bởi: {$duplicate_parent->owner_name} - {$duplicate_parent->hrm_id}"
            ]);
        }

        // 1. Tạo parent trước (tham khảo ParentsController)
        $parent_id = u::insertSimpleRow(array(
            'name' => data_get($request, 'student_name'),
            'email' => data_get($request, 'student_email'),
            'mobile_1' => data_get($request, 'student_phone'),
            'mobile_2' => data_get($request, 'student_phone2', ''),
            'address' => data_get($request, 'student_address'),
            'province_id' => data_get($request, 'province_id'),
            'district_id' => data_get($request, 'district_id'),
            'source_id' => data_get($request, 'source_id'),
            'gender' => data_get($request, 'student_gender', 0),
            'birthday' => data_get($request, 'student_birthday'),
            'link_facebook' => data_get($request, 'student_facebook'),
            'point_toeic' => data_get($request, 'student_point_toeic'),
            'note' => data_get($request, 'note'),
            'created_at' => date('Y-m-d H:i:s'),
            'creator_id' => Auth::user()->id,
            'last_assign_date' => date('Y-m-d H:i:s'),
            'owner_id' => data_get($request, 'ec_id'),
            'status' => 1,
        ), 'crm_parents');

        u::updateBranchIDParents();
        LogParents::logAdd($parent_id, 'Tạo parent từ form nhập học HS mới', Auth::user()->id);

        // 2. Tạo học sinh mới với parent_id
        $arr_name = u::explodeName(data_get($request, 'student_name'));
        $student_id = u::insertSimpleRow(array(
            'lms_code' => '',
            'name' => data_get($request, 'student_name'),
            'firstname' => data_get($arr_name, 'firstname'),
            'midname' => data_get($arr_name, 'midname'),
            'lastname' => data_get($arr_name, 'lastname'),
            'gender' => data_get($request, 'student_gender', 0),
            'date_of_birth' => data_get($request, 'student_birthday'),
            'gud_mobile1' => data_get($request, 'student_phone'),
            'gud_mobile2' => data_get($request, 'student_phone2', ''),
            'gud_email1' => data_get($request, 'student_email'),
            'address' => data_get($request, 'student_address'),
            'created_at' => date('Y-m-d H:i:s'),
            'creator_id' => Auth::user()->id,
            'branch_id' => data_get($request, 'branch_id'),
            'source_id' => data_get($request, 'source_id'),
            'status' => 1,
            'avatar_url' => data_get($request, 'student_gender') == 'F' ? '/images/common/avatar-girl.svg' : '/images/common/avatar-boy.svg'
        ), 'students');

        $last_lms_code = str_pad((string) $student_id, 6, '0', STR_PAD_LEFT);
        $lms_code = config('app.prefix_student_code') . $last_lms_code;
        u::updateSimpleRow(array('lms_code' => $lms_code), array('id' => $student_id), 'students');

        // Cập nhật student_id vào parent
        u::updateSimpleRow(array('student_id' => $student_id), array('id' => $parent_id), 'crm_parents');

        // Lấy thông tin EC
        $ec_id = data_get($request, 'ec_id');
        $ec_info = u::first("SELECT u.id, u.manager_id FROM users AS u WHERE u.status=1 AND u.id = " . (int) $ec_id);
        $ec_leader_id = data_get($ec_info, 'manager_id') ? data_get($ec_info, 'manager_id'): $ec_id;

        $ceo_info = u::first("SELECT u.id FROM role_has_user AS ru 
            LEFT JOIN roles AS r ON r.id = ru.role_id
            LEFT JOIN users AS u ON u.id = ru.user_id
            WHERE u.status=1 AND r.code ='" . SystemCode::ROLE_CEO_BRANCH . "'");

        // Tạo term_student_user
        u::insertSimpleRow(array(
            'student_id' => $student_id,
            'ec_id' => $ec_id,
            'branch_id' => data_get($request, 'branch_id'),
            'ceo_branch_id' => data_get($ceo_info, 'id'),
            'ec_leader_id' => $ec_leader_id,
            'created_at' => date('Y-m-d H:i:s'),
            'creator_id' => Auth::user()->id,
            'status' => 1
        ), 'term_student_user');

        // Tính count_recharge: 0 nếu HS chưa có agreement nào, 1 nếu đã có
        $first_agreement = u::first("SELECT id, first_8th_session_date FROM agreements WHERE student_id = $student_id AND status > 0 ORDER BY id ASC LIMIT 1");
        $count_recharge2 = $first_agreement ? 1 : 0;
        $is_first_package2 = $first_agreement ? 0 : 1;
        $first_8th2 = $first_agreement ? data_get($first_agreement, 'first_8th_session_date') : null;

        // Tạo agreement
        $agreement_id = u::insertSimpleRow(array(
            'student_id' => $student_id,
            'branch_id' => data_get($request, 'branch_id'),
            'ec_id' => $ec_id,
            'ec_leader_id' => $ec_leader_id,
            'type_fee' => data_get($request, 'tuition_fee_type'),
            'tuition_fee_id' => data_get($request, 'tuition_fee_id'),
            'must_charge' => data_get($request, 'tuition_fee_amount'),
            'debt_amount' => data_get($request, 'tuition_fee_amount'),
            'start_date' => data_get($request, 'start_date'),
            'note' => data_get($request, 'note'),
            'book_receive' => data_get($request, 'book_receive', 0),
            'book_receive_address' => data_get($request, 'book_receive_address', ''),
            'contract_receive' => data_get($request, 'contract_receive', 0),
            'group_type' => data_get($request, 'group_type', 0),
            'count_recharge' => $count_recharge2,
            'is_first_package' => $is_first_package2,
            'first_8th_session_date' => $first_8th2,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'creator_id' => Auth::user()->id,
            'type_obj' => data_get($request, 'type_obj'),
        ), 'agreements');

        $agreement_code = str_pad((string) $agreement_id, 6, '0', STR_PAD_LEFT);
        $agreement_code = config('app.prefix_agreement_code') . $agreement_code;
        u::updateSimpleRow(array('code' => $agreement_code), array('id' => $agreement_id), 'agreements');
        u::addLogAgreements($agreement_id);

        // Tạo contract
        $created_contract_ids = [];
        if (data_get($request, 'tuition_fee_type') == 1) {
            $tuition_fee_info = u::getObject(['id' => data_get($request, 'tuition_fee_id')], 'tuition_fee');
            if ($tuition_fee_info) {
                $contract_id = u::insertSimpleRow(array(
                    'type' => 1,
                    'student_id' => $student_id,
                    'branch_id' => data_get($request, 'branch_id'),
                    'ec_id' => $ec_id,
                    'ec_leader_id' => $ec_leader_id,
                    'product_id' => data_get($tuition_fee_info, 'product_id'),
                    'tuition_fee_id' => data_get($tuition_fee_info, 'id'),
                    'init_tuition_fee_id' => data_get($tuition_fee_info, 'id'),
                    'init_tuition_fee_amount' => data_get($tuition_fee_info, 'price'),
                    'init_tuition_fee_session' => data_get($tuition_fee_info, 'session'),
                    'init_total_charged' => 0,
                    'must_charge' => data_get($tuition_fee_info, 'price'),
                    'total_charged' => 0,
                    'debt_amount' => data_get($tuition_fee_info, 'price'),
                    'total_sessions' => data_get($tuition_fee_info, 'session'),
                    'real_sessions' => data_get($tuition_fee_info, 'session'),
                    'bonus_sessions' => 0,
                    'summary_sessions' => 0,
                    'reservable_sessions' => 0,
                    'start_date' => data_get($request, 'start_date'),
                    'note' => data_get($request, 'note'),
                    'created_at' => date('Y-m-d H:i:s'),
                    'creator_id' => Auth::user()->id,
                    'status' => 1,
                    'count_recharge' => 1,
                    'agreement_id' => $agreement_id
                ), 'contracts');
                $contract_code = str_pad((string) $contract_id, 6, '0', STR_PAD_LEFT);
                $contract_code = config('app.prefix_contract_code') . $contract_code;
                u::updateSimpleRow(array('code' => $contract_code), array('id' => $contract_id), 'contracts');
                u::addLogContracts($contract_id);
                LogStudents::logAdd($student_id, 'Thêm mới học sinh và hợp đồng nhập học - ' . $contract_code, Auth::user()->id);
                $created_contract_ids[] = $contract_id;
            }
        } elseif (data_get($request, 'tuition_fee_type') == 2) {
            $relation_tuition_fee = u::query("SELECT t.*, r.price_combo, r.stt FROM tuition_fee_relation AS r
                LEFT JOIN tuition_fee AS t ON r.exchange_tuition_fee_id=t.id
                WHERE r.status=1 AND r.tuition_fee_id = " . (int) data_get($request, 'tuition_fee_id') . "
                ORDER BY r.stt ASC");
            foreach ($relation_tuition_fee as $tuition_fee_info) {
                $contract_id = u::insertSimpleRow(array(
                    'type' => 1,
                    'student_id' => $student_id,
                    'branch_id' => data_get($request, 'branch_id'),
                    'ec_id' => $ec_id,
                    'ec_leader_id' => $ec_leader_id,
                    'product_id' => data_get($tuition_fee_info, 'product_id'),
                    'tuition_fee_id' => data_get($tuition_fee_info, 'id'),
                    'init_tuition_fee_id' => data_get($tuition_fee_info, 'id'),
                    'init_tuition_fee_amount' => data_get($tuition_fee_info, 'price_combo'),
                    'init_tuition_fee_session' => data_get($tuition_fee_info, 'session'),
                    'init_total_charged' => 0,
                    'must_charge' => data_get($tuition_fee_info, 'price_combo'),
                    'total_charged' => 0,
                    'debt_amount' => data_get($tuition_fee_info, 'price_combo'),
                    'total_sessions' => data_get($tuition_fee_info, 'session'),
                    'real_sessions' => data_get($tuition_fee_info, 'session'),
                    'bonus_sessions' => 0,
                    'summary_sessions' => 0,
                    'reservable_sessions' => 0,
                    'start_date' => data_get($request, 'start_date'),
                    'note' => data_get($request, 'note'),
                    'created_at' => date('Y-m-d H:i:s'),
                    'creator_id' => Auth::user()->id,
                    'status' => 1,
                    'count_recharge' => data_get($tuition_fee_info, 'stt'),
                    'agreement_id' => $agreement_id
                ), 'contracts');
                $contract_code = str_pad((string) $contract_id, 6, '0', STR_PAD_LEFT);
                $contract_code = config('app.prefix_contract_code') . $contract_code;
                u::updateSimpleRow(array('code' => $contract_code), array('id' => $contract_id), 'contracts');
                u::addLogContracts($contract_id);
                LogStudents::logAdd($student_id, 'Thêm mới học sinh và hợp đồng nhập học - ' . $contract_code, Auth::user()->id);
                $created_contract_ids[] = $contract_id;
            }
        }

        // Xếp lớp ngay khi nhập học (nếu chọn)
        $class_id = (int) data_get($request, 'class_id', 0);
        if ($class_id > 0) {
            // chọn contract để xếp lớp: gói lẻ => contract đầu; combo => contract stt=1 (count_recharge=1)
            $enrol_contract_id = 0;
            if (data_get($request, 'tuition_fee_type') == 1) {
                $enrol_contract_id = (int) ($created_contract_ids[0] ?? 0);
            } else {
                $first = u::first("SELECT id, product_id FROM contracts
                    WHERE agreement_id = $agreement_id AND status > 0
                    ORDER BY count_recharge ASC, id ASC
                    LIMIT 1");
                $enrol_contract_id = (int) data_get($first, 'id', 0);
            }

            if ($enrol_contract_id > 0) {
                $contract_info = u::first("SELECT id, product_id FROM contracts WHERE id = $enrol_contract_id LIMIT 1");
                $product_id = (int) data_get($contract_info, 'product_id', 0);
                $start_date_enrol = date('Y-m-d');
                $this->enrolContractToClass($enrol_contract_id, $student_id, $class_id, (int) data_get($request, 'branch_id'), $product_id);
            }
        }

        $result = array(
            'status' => 1,
            'message' => 'Thêm mới học sinh và nhập học thành công',
            'student_id' => $student_id,
            'lms_code' => $lms_code,
            'agreement_id' => $agreement_id
        );
        return response()->json($result);
    }


    public function list(Request $request)
    {
        $branch_id = isset($request->branch_id) ? $request->branch_id : [];
        $keyword = isset($request->keyword) ? $request->keyword : '';
        $end_date = isset($request->end_date) ? $request->end_date : '';
        $start_date = isset($request->start_date) ? $request->start_date : '';
        $student_id = isset($request->student_id) ? $request->student_id : null;

        $pagination = (object) $request->pagination;
        $page = isset($pagination->cpage) ? (int) $pagination->cpage : 1;
        $limit = isset($pagination->limit) ? (int) $pagination->limit : 20;
        $offset = $page == 1 ? 0 : $limit * ($page - 1);
        $limitation = $limit > 0 ? " LIMIT $offset, $limit" : "";
        $cond = " c.status > 0 ";
        
        if ($student_id) {
            $cond .= " AND c.student_id = " . (int)$student_id;
        }

        $cond .= " AND c.branch_id IN (" . Auth::user()->getBranchesHasUser() . ")";
        
        // Filter by ec_id for Sales and Sale Leader (role_id 68, 69)
        $role_ids = u::query("SELECT role_id FROM role_has_user WHERE user_id = " . Auth::user()->id);
        $roles = array_map(function($r) { return $r->role_id; }, $role_ids);
        if (in_array(68, $roles) || in_array(69, $roles)) {
            $cond .= " AND c.ec_id IN (" . Auth::user()->getStaffHasUser() . ")";
        }

        if (!empty($branch_id)) {
            $cond .= " AND c.branch_id IN (" . implode(",", $branch_id) . ")";
        }

        if ($keyword !== '') {
            $cond .= " AND (s.lms_code LIKE '%$keyword%' OR s.gud_mobile1 LIKE '%$keyword%' OR s.name LIKE '%$keyword%' OR c.code LIKE '%$keyword%') ";
        }

        if ($end_date !== '') {
            $cond .= " AND c.created_at < '$end_date 23:59:59'";
        }
        if ($start_date !== '') {
            $cond .= " AND c.created_at > '$start_date 00:00:00'";
        }
        
        $is_class = data_get($request, 'is_class', -1);
        if ($is_class == 1) {
            $cond .= " AND EXISTS (SELECT 1 FROM contracts ct WHERE ct.agreement_id = c.id AND ct.class_id > 0 AND ct.status > 0) ";
        } elseif ($is_class == 0) {
            $cond .= " AND NOT EXISTS (SELECT 1 FROM contracts ct WHERE ct.agreement_id = c.id AND ct.class_id > 0 AND ct.status > 0) ";
        }

        // Filter by Team KD (sale leader)
        $team_id = isset($request->team_id) && $request->team_id ? (int) $request->team_id : 0;
        if ($team_id > 0) {
            $cond .= " AND (c.ec_leader_id = $team_id OR c.ec_id = $team_id)";
        }

        // Filter by Nhân viên sale
        $filter_ec_id = isset($request->ec_id) && $request->ec_id ? (int) $request->ec_id : 0;
        if ($filter_ec_id > 0) {
            $cond .= " AND c.ec_id = $filter_ec_id";
        }

        $order_by = " ORDER BY c.id DESC ";

        $total = u::first("SELECT count(s.id) AS total 
            FROM agreements AS c LEFT JOIN students AS s ON s.id=c.student_id WHERE $cond");

        $list = u::query("SELECT c.id AS agreement_id, s.name, s.lms_code, 
                (SELECT name FROM branches WHERE id =c.branch_id) AS branch_name,
                (SELECT CONCAT(name,'-',hrm_id) FROM users WHERE id= c.ec_id) AS ec_name,
                (SELECT CONCAT(name,'-',hrm_id) FROM users WHERE id= c.ec_leader_id) AS ec_leader_name,
                (SELECT name FROM products WHERE id =c.product_id) AS product_name,
                c.code, (SELECT name FROM tuition_fee WHERE id=c.tuition_fee_id) AS tuition_fee_name,
                (SELECT name FROM sources WHERE id = s.source_id) AS source_name,
                c.must_charge, c.debt_amount, c.total_charged, c.transferred_amount, c.received_amount, c.status, c.student_id
            FROM agreements AS c 
                LEFT JOIN students AS s ON s.id=c.student_id
            WHERE $cond $order_by $limitation");
        foreach ($list as $k => $row) {
            $list[$k]->label_status = u::genLearningStatusByContracts($row->student_id, true, $row->agreement_id);
        }
        $data = u::makingPagination($list, $total->total, $page, $limit);
        return response()->json($data);
    }

    public function listByStudent(Request $request)
    {
        $student_id = isset($request->student_id) ? (int)$request->student_id : 0;
        
        if (!$student_id) {
            return response()->json(u::makingPagination([], 0, 1, 20));
        }

        $pagination = (object) $request->pagination;
        $page = isset($pagination->cpage) ? (int) $pagination->cpage : 1;
        $limit = isset($pagination->limit) ? (int) $pagination->limit : 20;
        $offset = $page == 1 ? 0 : $limit * ($page - 1);
        $limitation = $limit > 0 ? " LIMIT $offset, $limit" : "";
        $cond = " c.status > 0 AND c.student_id = " . $student_id;
        
        $order_by = " ORDER BY c.id DESC ";

        $total = u::first("SELECT count(s.id) AS total 
            FROM agreements AS c LEFT JOIN students AS s ON s.id=c.student_id WHERE $cond");

        $list = u::query("SELECT c.id AS agreement_id, s.name, s.lms_code, 
                (SELECT name FROM branches WHERE id =c.branch_id) AS branch_name,
                (SELECT CONCAT(name,'-',hrm_id) FROM users WHERE id= c.ec_id) AS ec_name,
                (SELECT CONCAT(name,'-',hrm_id) FROM users WHERE id= c.ec_leader_id) AS ec_leader_name,
                (SELECT name FROM products WHERE id =c.product_id) AS product_name,
                c.code, (SELECT name FROM tuition_fee WHERE id=c.tuition_fee_id) AS tuition_fee_name,
                (SELECT name FROM sources WHERE id = s.source_id) AS source_name,
                c.must_charge, c.debt_amount, c.total_charged, c.transferred_amount, c.received_amount, c.status, c.student_id
            FROM agreements AS c 
                LEFT JOIN students AS s ON s.id=c.student_id
            WHERE $cond $order_by $limitation");
        foreach ($list as $k => $row) {
            $list[$k]->label_status = u::genLearningStatusByContracts($row->student_id, true, $row->agreement_id);
        }
        $data = u::makingPagination($list, $total->total, $page, $limit);
        return response()->json($data);
    }

    public function transferExcess(Request $request)
    {
        $from_agreement_id = (int)$request->from_agreement_id;
        $to_agreement_id = (int)$request->to_agreement_id;
        $amount = (float)$request->amount;
        $note = $request->note ?? 'Chuyển tiền thừa sang gói khác';

        if ($from_agreement_id <= 0 || $to_agreement_id <= 0 || $amount <= 0) {
            return response()->json(['status' => 0, 'message' => 'Dữ liệu không hợp lệ']);
        }

        if ($from_agreement_id == $to_agreement_id) {
            return response()->json(['status' => 0, 'message' => 'Không thể chuyển tiền cho cùng một gói']);
        }

        $from = u::first("SELECT * FROM agreements WHERE id = $from_agreement_id");
        $to = u::first("SELECT * FROM agreements WHERE id = $to_agreement_id");

        if (!$from || !$to) {
            return response()->json(['status' => 0, 'message' => 'Gói học phí không tồn tại']);
        }

        if ($from->student_id != $to->student_id) {
            return response()->json(['status' => 0, 'message' => 'Chỉ có thể chuyển tiền giữa các gói của cùng một học sinh']);
        }

        $from_transferred = (float) data_get($from, 'transferred_amount', 0);
        $from_received = (float) data_get($from, 'received_amount', 0);
        $from_total_charged = (float) data_get($from, 'total_charged', 0);
        $from_must_charge = (float) data_get($from, 'must_charge', 0);

        $from_effective = $from_total_charged + $from_received - $from_transferred;
        $excess = $from_effective - $from_must_charge;

        if ($excess < $amount) {
            return response()->json(['status' => 0, 'message' => "Số tiền chuyển vượt quá số tiền thừa của gói cũ ($excess)"]);
        }

        // Cập nhật gói cũ: tăng transferred_amount, giữ nguyên total_charged
        $new_from_transferred = $from_transferred + $amount;
        $new_from_effective = $from_total_charged + $from_received - $new_from_transferred;
        $new_from_debt = $from_must_charge - $new_from_effective;

        u::updateSimpleRow([
            'transferred_amount' => $new_from_transferred,
            'debt_amount' => $new_from_debt > 0 ? $new_from_debt : 0,
            'updated_at' => date('Y-m-d H:i:s'),
            'updator_id' => Auth::user()->id,
        ], ['id' => $from_agreement_id], 'agreements');

        // Cập nhật gói mới: tăng received_amount, giữ nguyên total_charged
        $to_transferred = (float) data_get($to, 'transferred_amount', 0);
        $to_received = (float) data_get($to, 'received_amount', 0);
        $to_total_charged = (float) data_get($to, 'total_charged', 0);
        $to_must_charge = (float) data_get($to, 'must_charge', 0);

        $new_to_received = $to_received + $amount;
        $new_to_effective = $to_total_charged + $new_to_received - $to_transferred +data_get($to, 'discount_amount', 0);
        $new_to_debt = $to_must_charge - $new_to_effective;

        u::updateSimpleRow([
            'received_amount' => $new_to_received,
            'debt_amount' => $new_to_debt > 0 ? $new_to_debt : 0,
            'updated_at' => date('Y-m-d H:i:s'),
            'updator_id' => Auth::user()->id,
        ], ['id' => $to_agreement_id], 'agreements');

        // Ghi log chuyển tiền
        u::insertSimpleRow([
            'student_id' => $from->student_id,
            'from_agreement_id' => $from_agreement_id,
            'to_agreement_id' => $to_agreement_id,
            'amount' => $amount,
            'note' => $note,
            'creator_id' => Auth::user()->id,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ], 'agreement_transfers');

        // Ghi log thay đổi gói học phí
        u::addLogAgreements($from_agreement_id);
        u::addLogAgreements($to_agreement_id);

        // Chia lại contract total
        $chargesController = new ChargesController();
        $chargesController->processContractsByAgreement($from_agreement_id);
        $chargesController->processContractsByAgreement($to_agreement_id);

        return response()->json(['status' => 1, 'message' => 'Chuyển tiền thừa thành công']);
    }

    public function delete(Request $request)
    {
        $cagreement_info = u::first("SELECT student_id, code FROM agreements WHERE id=$request->agreement_id");
        u::updateSimpleRow(array('status' => 0), array('id' => $request->agreement_id), 'agreements');
        $listContractDelete = u::query("SELECT id FROM contracts WHERE agreement_id=$request->agreement_id");
        foreach ($listContractDelete as $contract) {
            u::updateSimpleRow(array('status' => 0), array('id' => $contract->id), 'contracts');
            u::addLogContracts($contract->id);
        }

        LogStudents::logAdd(data_get($cagreement_info, 'student_id'), 'Hủy hợp đồng nhập học - ' . data_get($cagreement_info, 'code'), Auth::user()->id);
        $result = array(
            'status' => 1,
            'message' => 'Hủy hợp đồng nhập học ' . data_get($cagreement_info, 'code') . ' thành công.'
        );
        return response()->json($result);
    }

    public function show(Request $request, $agreement_id)
    {
        $data = u::first("SELECT c.*,c.id AS agreement_id, s.name, s.lms_code, s.gud_name1, s.gud_mobile1, s.address, s.gud_email1, p.point_toeic,
            (SELECT name FROM branches WHERE id =c.branch_id) AS branch_name,
            (SELECT CONCAT(name,'-',hrm_id) FROM users WHERE id= c.ec_id) AS ec_name,
            (SELECT CONCAT(name,'-',hrm_id) FROM users WHERE id= c.ec_leader_id) AS ec_leader_name,
            (SELECT name FROM products WHERE id =c.product_id) AS product_name,
            (SELECT name FROM tuition_fee WHERE id=c.tuition_fee_id) AS tuition_fee_name,
            (SELECT CONCAT(name,'-',hrm_id) FROM users WHERE id= c.creator_id) AS creator_name,
            (SELECT cl.id FROM contracts ct LEFT JOIN classes cl ON cl.id = ct.class_id WHERE ct.agreement_id = c.id AND ct.status > 0 AND ct.class_id IS NOT NULL LIMIT 1) AS class_id,
            (SELECT cl.cls_name FROM contracts ct LEFT JOIN classes cl ON cl.id = ct.class_id WHERE ct.agreement_id = c.id AND ct.status > 0 AND ct.class_id IS NOT NULL LIMIT 1) AS class_name,
            (SELECT cl.cls_startdate FROM contracts ct LEFT JOIN classes cl ON cl.id = ct.class_id WHERE ct.agreement_id = c.id AND ct.status > 0 AND ct.class_id IS NOT NULL LIMIT 1) AS class_start_date,
            '' AS contracts, c.total_charged, 0 AS total_left_amount
        FROM agreements AS c 
            LEFT JOIN students AS s ON s.id=c.student_id 
            LEFT JOIN crm_parents AS p ON p.student_id = s.id
            WHERE c.id=$agreement_id");
        $mapComboPrice = [];
        if (!empty($data->tuition_fee_id)) {
            $relationFees = u::query("SELECT exchange_tuition_fee_id, price_combo FROM tuition_fee_relation WHERE status=1 AND tuition_fee_id=" . (int) $data->tuition_fee_id);
            foreach ($relationFees as $rf) {
                $mapComboPrice[$rf->exchange_tuition_fee_id] = (float) $rf->price_combo;
            }
        }

        $total_left_amount = 0;
        $dataContracts = u::query("SELECT c.code, c.must_charge, c.total_charged, c.debt_amount, c.status,c.real_sessions, c.done_sessions, c.left_sessions,c.summary_sessions, c.tuition_fee_id,
                    (SELECT name FROM tuition_fee WHERE id=c.tuition_fee_id) AS tuition_fee_name, c.product_id
                FROM contracts AS c 
                WHERE c.agreement_id= $agreement_id AND c.status>0 
                ORDER BY DATE_FORMAT(c.created_at, '%Y-%m-%d'), c.count_recharge ASC");
        foreach ($dataContracts as $k => $contract) {
            $dataContracts[$k]->label_status = u::geLabelStatusContract($contract->status);
            $price = isset($mapComboPrice[$contract->tuition_fee_id]) && $mapComboPrice[$contract->tuition_fee_id] > 0 ? $mapComboPrice[$contract->tuition_fee_id] : $contract->total_charged;
            if ($contract->summary_sessions > 0) {
                $left_amount = round($price * $contract->left_sessions / $contract->summary_sessions);
            } elseif ($contract->real_sessions > 0) {
                $left_amount = round($price * ($contract->real_sessions - $contract->done_sessions) / $contract->real_sessions);
            } else {
                $left_amount = $price;
            }
            $dataContracts[$k]->left_amount = $left_amount;
            $total_left_amount = $total_left_amount + $left_amount;
        }
        $data->contracts = $dataContracts;
        $data->total_left_amount = round($total_left_amount);

        $current_user_id = Auth::user()->id;
        $is_admin = u::first("SELECT 1 FROM role_has_user WHERE user_id = $current_user_id AND role_id = " . SystemCode::ROLE_ADMIN);
        $data->is_admin = !empty($is_admin) ? true : false;

        $is_sale = u::first("SELECT 1 FROM role_has_user WHERE user_id = $current_user_id AND role_id IN (" . SystemCode::ROLE_EC . ", " . SystemCode::ROLE_EC_LEADER . ")");
        $data->is_sale_role = !empty($is_sale) ? true : false;

        $is_ketoan = u::first("SELECT 1 FROM role_has_user WHERE user_id = $current_user_id AND role_id = 83");
        $data->can_edit_lower_fee = (!empty($is_admin) || !empty($is_ketoan)) ? true : false;

        $tmp_payment = u::first("SELECT SUM(charge_amount) as tmp_payment_amount FROM tmp_payments WHERE agreement_id = $agreement_id AND status IN (0, 1)");
        $data->tmp_payment_amount = $tmp_payment ? (int) $tmp_payment->tmp_payment_amount : 0;

        return response()->json($data);
    }

    public function update(Request $request)
    {
        $current_user_id = Auth::user()->id;
        $is_sale = u::first("SELECT 1 FROM role_has_user WHERE user_id = $current_user_id AND role_id IN (" . SystemCode::ROLE_EC . ", " . SystemCode::ROLE_EC_LEADER . ")");


        $agreement_id = data_get($request, 'id');
        $agreementInfo = u::getObject(['id' => data_get($request, 'id')], 'agreements');
        if ($agreementInfo) {
            if ($request->has('point_toeic')) {
                u::updateSimpleRow(
                    ['point_toeic' => $request->point_toeic],
                    ['student_id' => $agreementInfo->student_id],
                    'crm_parents'
                );
            }
            $updateChargesFee = false;
            if (data_get($agreementInfo, 'tuition_fee_id') != data_get($request, 'tuition_fee_id')) {
                if ($is_sale) {
                    return response()->json([
                        'status' => 0,
                        'message' => 'Bạn không có quyền thay đổi Gói học phí'
                    ]);
                }
                // Lấy contracts hiện tại
                $oldContracts = u::query("SELECT * FROM contracts WHERE agreement_id = $agreement_id AND status != 8 AND status > 0");

                $newProductIds = [];
                $tuition_fee_info = null;
                $relationFees = [];
                
                if (data_get($request, 'tuition_fee_type') == 1) {
                    $tuition_fee_info = u::getObject(['id' => data_get($request, 'tuition_fee_id')], 'tuition_fee');
                    if ($tuition_fee_info) {
                        $newProductIds[] = $tuition_fee_info->product_id;
                    }
                } elseif (data_get($request, 'tuition_fee_type') == 2) {
                    $relationFees = u::query("SELECT t.*, r.price_combo, r.stt 
                        FROM tuition_fee_relation r
                            LEFT JOIN tuition_fee t ON r.exchange_tuition_fee_id = t.id
                        WHERE r.status = 1 AND r.tuition_fee_id = " . data_get($request, 'tuition_fee_id')
                    );
                    foreach ($relationFees as $fee) {
                        $newProductIds[] = $fee->product_id;
                    }
                }

                // $studied_amount_overlapped = 0;
                // foreach ($oldContracts as $contract) {
                //     if (in_array($contract->product_id, $newProductIds)) {
                //         $left_amount = $contract->summary_sessions > 0 ? ($contract->total_charged * $contract->left_sessions / $contract->summary_sessions) : $contract->total_charged;
                //         $studied_amount = $contract->total_charged - $left_amount;
                //         $studied_amount_overlapped += $studied_amount;
                //     }
                // }

                // $new_total_charged = data_get($request, 'total_left_amount') + $studied_amount_overlapped;
                $new_total_charged = data_get($request, 'total_left_amount');

                u::updateSimpleRow(array(
                    'type_fee' => data_get($request, 'tuition_fee_type'),
                    'tuition_fee_id' => data_get($request, 'tuition_fee_id'),
                    'must_charge' => data_get($request, 'tuition_fee_amount'),
                    'debt_amount' => data_get($request, 'debt_amount'),
                    'total_charged' => $new_total_charged,
                    'start_date' => data_get($request, 'start_date'),
                    'note' => data_get($request, 'note'),
                    'book_receive' => data_get($request, 'book_receive', 0),
                    'book_receive_address' => data_get($request, 'book_receive_address', ''),
                    'contract_receive' => data_get($request, 'contract_receive', 0),
                    'group_type' => data_get($request, 'group_type', 0),
                    'status' => $new_total_charged == 0 ? 1 : (data_get($request, 'debt_amount') > 0 ? 3 : 2),
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updator_id' => Auth::user()->id,
                ), array('id' => data_get($request, 'id')), 'agreements');
                u::addLogAgreements($agreement_id);

                // ====== XỬ LÝ GÓI LẺ ======
                if (data_get($request, 'tuition_fee_type') == 1) {

                    if ($tuition_fee_info) {

                        // kiểm tra contract đã tồn tại chưa
                        $existContract = u::first("SELECT * FROM contracts
                            WHERE agreement_id = $agreement_id AND product_id = {$tuition_fee_info->product_id} AND status NOT IN (0, 8) LIMIT 1");

                        if ($existContract) {
                            // UPDATE
                            u::updateSimpleRow([
                                'tuition_fee_id' => $tuition_fee_info->id,
                                'must_charge' => $tuition_fee_info->price,
                                'updated_at' => date('Y-m-d H:i:s'),
                                'updator_id' => Auth::user()->id,
                            ], ['id' => $existContract->id], 'contracts');

                            u::addLogContracts($existContract->id);

                        } else {
                            // INSERT MỚI
                            $contract_id = u::insertSimpleRow([
                                'type' => 1,
                                'student_id' => data_get($agreementInfo, 'student_id'),
                                'branch_id' => data_get($request, 'branch_id'),
                                'ec_id' => data_get($agreementInfo, 'ec_id'),
                                'ec_leader_id' => data_get($agreementInfo, 'ec_leader_id'),
                                'product_id' => $tuition_fee_info->product_id,
                                'tuition_fee_id' => $tuition_fee_info->id,
                                'init_tuition_fee_id' => $tuition_fee_info->id,
                                'init_tuition_fee_amount' => $tuition_fee_info->price,
                                'init_tuition_fee_session' => $tuition_fee_info->session,
                                'must_charge' => $tuition_fee_info->price,
                                'total_charged' => 0,
                                'debt_amount' => $tuition_fee_info->price,
                                'total_sessions' => $tuition_fee_info->session,
                                'real_sessions' => $tuition_fee_info->session,
                                'start_date' => data_get($request, 'start_date'),
                                'note' => data_get($request, 'note'),
                                'created_at' => now(),
                                'creator_id' => Auth::user()->id,
                                'status' => 1,
                                'count_recharge' => 1,
                                'agreement_id' => $agreement_id
                            ], 'contracts');

                            u::updateSimpleRow(
                                ['code' => config('app.prefix_contract_code') . str_pad($contract_id, 6, '0', STR_PAD_LEFT)],
                                ['id' => $contract_id],
                                'contracts'
                            );

                            u::addLogContracts($contract_id);
                        }
                    }
                } elseif (data_get($request, 'tuition_fee_type') == 2) {

                    foreach ($relationFees as $fee) {

                        $existContract = u::first("SELECT * FROM contracts
                            WHERE agreement_id = $agreement_id AND product_id = {$fee->product_id} AND status NOT IN (0, 8) LIMIT 1");

                        if ($existContract) {
                            // UPDATE
                            u::updateSimpleRow([
                                'tuition_fee_id' => $fee->id,
                                'must_charge' => $fee->price_combo,
                                'updated_at' => now(),
                                'updator_id' => Auth::user()->id,
                            ], ['id' => $existContract->id], 'contracts');

                            u::addLogContracts($existContract->id);

                        } else {
                            // INSERT
                            $contract_id = u::insertSimpleRow([
                                'type' => 1,
                                'student_id' => data_get($agreementInfo, 'student_id'),
                                'branch_id' => data_get($agreementInfo, 'branch_id'),
                                'ec_id' => data_get($agreementInfo, 'ec_id'),
                                'ec_leader_id' => data_get($agreementInfo, 'ec_leader_id'),
                                'product_id' => $fee->product_id,
                                'tuition_fee_id' => $fee->id,
                                'init_tuition_fee_amount' => $fee->price_combo,
                                'must_charge' => $fee->price_combo,
                                'debt_amount' => $fee->price_combo,
                                'total_sessions' => $fee->session,
                                'real_sessions' => $fee->session,
                                'created_at' => now(),
                                'creator_id' => Auth::user()->id,
                                'status' => 1,
                                'count_recharge' => $fee->stt,
                                'agreement_id' => $agreement_id
                            ], 'contracts');

                            u::updateSimpleRow([
                                'code' => config('app.prefix_contract_code') . str_pad($contract_id, 6, '0', STR_PAD_LEFT)
                            ], ['id' => $contract_id], 'contracts');

                            u::addLogContracts($contract_id);
                        }
                    }
                }

                foreach ($oldContracts as $contract) {
                    if (!in_array($contract->product_id, $newProductIds)) {

                        // xác định status theo số buổi đã học
                        $newStatus = (int) ($contract->done_sessions ?? 0) > 0 ? 8 : 0;

                        u::updateSimpleRow([
                            'status' => $newStatus,
                            'updated_at' => date('Y-m-d H:i:s'),
                            'updator_id' => Auth::user()->id
                        ], [
                            'id' => $contract->id
                        ], 'contracts');

                        u::addLogContracts($contract->id);
                    }
                }
                $updateChargesFee = true;
            } else {
                u::updateSimpleRow(array(
                    'start_date' => data_get($request, 'start_date'),
                    'note' => data_get($request, 'note'),
                    'book_receive' => data_get($request, 'book_receive', 0),
                    'book_receive_address' => data_get($request, 'book_receive_address', ''),
                    'contract_receive' => data_get($request, 'contract_receive', 0),
                    'group_type' => data_get($request, 'group_type', 0),
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updator_id' => Auth::user()->id,
                ), array('id' => data_get($request, 'id')), 'agreements');
                u::addLogAgreements($agreement_id);
            }

            // Update EC and EC Leader if provided (only for admin)
            if ($request->has('ec_id') && $request->ec_id > 0) {
                $ec_id = (int)$request->ec_id;
                $ec_info = u::first("SELECT u.id, u.manager_id FROM users AS u WHERE u.status=1 AND u.id = " . $ec_id);
                if ($ec_info) {
                    $ec_leader_id = data_get($ec_info, 'manager_id') ? data_get($ec_info, 'manager_id') : $ec_id;
                    u::updateSimpleRow([
                        'ec_id' => $ec_id,
                        'ec_leader_id' => $ec_leader_id,
                    ], ['id' => $agreement_id], 'agreements');
                    
                    u::updateSimpleRow([
                        'ec_id' => $ec_id,
                        'ec_leader_id' => $ec_leader_id,
                    ], ['agreement_id' => $agreement_id], 'contracts');

                    u::updateSimpleRow([
                        'ec_id' => $ec_id,
                        'ec_leader_id' => $ec_leader_id,
                    ], ['student_id' => $agreementInfo->student_id], 'term_student_user');
                }
            }

            // Xếp lớp ngay khi nhập học (nếu chọn)
            $class_id = (int) data_get($request, 'class_id', 0);
            if ($class_id > 0) {
                // Kiểm tra xem đã được xếp lớp này chưa
                $already_enrolled = u::first("SELECT id FROM contracts WHERE agreement_id = $agreement_id AND class_id = $class_id AND status > 0");
                if (!$already_enrolled) {
                    // Lấy danh sách contracts hiện tại
                    $contracts = u::query("SELECT id, product_id FROM contracts 
                        WHERE agreement_id = $agreement_id AND status > 0 
                        ORDER BY count_recharge ASC, id ASC");

                    if (count($contracts) > 0) {
                        // Chọn contract đầu tiên để xếp lớp (phù hợp với logic của add/addWithNewStudent)
                        $enrol_contract = $contracts[0];
                        $enrol_contract_id = (int) $enrol_contract->id;
                        $product_id = (int) $enrol_contract->product_id;
                        $start_date_enrol = date('Y-m-d');

                        $this->enrolContractToClass(
                            $enrol_contract_id,
                            $agreementInfo->student_id,
                            $class_id,
                            (int) $agreementInfo->branch_id,
                            $product_id
                        );
                    }
                }
            }
            if ($updateChargesFee){
                $chargesController = new ChargesController();
                $chargesController->processContractsByAgreement($agreement_id);
            }

        }

        $result = array(
            'status' => 1,
            'message' => 'Cập nhật thông tin nhập học thành công'
        );
        return response()->json($result);
    }
}
