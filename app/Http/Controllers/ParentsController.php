<?php

namespace App\Http\Controllers;

use App\Providers\UtilityServiceProvider as u;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as x;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\LogParents;

class ParentsController extends Controller
{
    public function list(Request $request)
    {
        $status = isset($request->status) ? $request->status : [];
        $level = isset($request->level) ? $request->level : [];
        $keyword = isset($request->keyword) ? $request->keyword : '';
        $owner_id = isset($request->owner_id) ? $request->owner_id : [];
        $source_id = isset($request->source_id) ? $request->source_id : [];
        $source_detail_id = isset($request->source_detail_id) ? $request->source_detail_id : [];
        $product_id = isset($request->product_id) ? $request->product_id : [];
        $end_date = isset($request->end_date) ? $request->end_date : '';
        $start_date = isset($request->start_date) ? $request->start_date : '';
        $type_search = isset($request->type_search) ? $request->type_search : 0;
        $branch_id = isset($request->branch_id) ? $request->branch_id : 0;

        $pagination = (object) $request->pagination;
        $page = isset($pagination->cpage) ? (int) $pagination->cpage : 1;
        $limit = isset($pagination->limit) ? (int) $pagination->limit : 20;
        $offset = $page == 1 ? 0 : $limit * ($page - 1);
        $limitation = $limit > 0 ? " LIMIT $offset, $limit" : "";
        $cond = " 1 ";

        if (!Auth::user()->checkPermission('canViewAllParents')) {
            $cond .= " AND p.owner_id IN (" . Auth::user()->getStaffHasUser() . ")";
        }

        if (!empty($status)) {
            $cond .= " AND p.status IN (" . implode(",", $status) . ")";
        }
        if (!empty($owner_id)) {
            $cond .= " AND p.owner_id IN (" . implode(",", $owner_id) . ")";
        }
        if (!empty($source_id)) {
            $cond .= " AND p.source_id IN (" . implode(",", $source_id) . ")";
        }
        if (!empty($source_detail_id)) {
            $cond .= " AND p.source_detail_id IN (" . implode(",", $source_detail_id) . ")";
        }
        if (!empty($product_id)) {
            $ids_product = is_array($product_id)
                ? implode(",", array_map('intval', $product_id))
                : (int) $product_id;
            $cond .= " AND EXISTS (
                SELECT 1 FROM crm_students AS cs
                LEFT JOIN crm_student_checkin AS csc ON csc.crm_student_id = cs.id
                WHERE cs.parent_id = p.id
                  AND (cs.type_product IN ($ids_product) OR csc.type_product IN ($ids_product))
            )";
        }

        if ($keyword !== '') {
            $cond .= " AND (p.name LIKE '%$keyword%' OR p.mobile_1 LIKE '%$keyword%' OR p.mobile_2 LIKE '%$keyword%') ";
        }
        if ($end_date !== '') {
            $cond .= " AND p.next_care_date < '$end_date 23:59:59' AND p.next_care_date IS NOT NULL";
        }
        if ($start_date !== '') {
            $cond .= " AND p.next_care_date > '$start_date 00:00:00'";
        }
        //type_search=1
        $cond_1 = " AND p.care_date IS NULL ";
        //type_search=2
        $cond_2 = " AND DATE_FORMAT(next_care_date,'%Y-%m-%d') = '" . date('Y-m-d') . "'";
        $cond_3 = " AND next_care_date < '" . date('Y-m-d') . "' 
            AND (p.care_date < p.next_care_date OR p.care_date IS NULL) ";
        $cond_4 = " AND (SELECT count(id) FROM crm_tickets WHERE parent_id = p.id AND status !=4 AND status!=5)>0 ";

        $order_by = " ORDER BY p.id DESC ";
        $tmp_cond = "";
        if ($type_search == 1) {
            $tmp_cond = $cond_1;
        } elseif ($type_search == 2) {
            $tmp_cond = $cond_2;
            $order_by = " ORDER BY next_care_date ASC ";
        } elseif ($type_search == 3) {
            $tmp_cond = $cond_3;
            $order_by = " ORDER BY next_care_date ASC ";
        } elseif ($type_search == 4) {
            $tmp_cond = $cond_4;
            $order_by = " ORDER BY p.last_ticket_date DESC ";
        }

        $total = u::first("SELECT count(p.id) AS total FROM crm_parents AS p WHERE $cond $tmp_cond");
        $list = u::query("SELECT p.name,p.id,p.mobile_1,p.status,p.next_care_date, (SELECT name FROM sources WHERE id=p.source_id) AS source_name,
                (SELECT name FROM source_detail WHERE id=p.source_detail_id) AS source_detail_name,
                (SELECT note FROM crm_customer_care WHERE parent_id=p.id AND status=1 ORDER BY care_date DESC LIMIT 1) AS last_care,
                p.care_date AS last_time_care,
                (SELECT name FROM users WHERE id=p.owner_id) AS owner_name ,
                (SELECT m.name FROM users uo JOIN users m ON m.id = uo.manager_id WHERE uo.id = p.owner_id LIMIT 1) AS owner_leader_name,
                (SELECT COUNT(*) FROM role_has_user WHERE user_id = p.owner_id AND role_id = 69) AS is_owner_leader,
                (SELECT name FROM crm_students WHERE parent_id=p.id LIMIT 0,1) AS hs1_name,
                (SELECT name FROM crm_students WHERE parent_id=p.id LIMIT 1,1) AS hs2_name,
                (SELECT GROUP_CONCAT(DISTINCT pr.name SEPARATOR ', ')
                 FROM crm_students AS cs
                 LEFT JOIN crm_student_checkin AS csc ON csc.crm_student_id = cs.id
                 INNER JOIN products AS pr ON (pr.id = csc.type_product OR pr.id = cs.type_product)
                 WHERE cs.parent_id = p.id) AS product_name
            FROM crm_parents AS p WHERE $cond $tmp_cond $order_by $limitation");
        if (!empty($list)) {
            foreach ($list as $item) {
                if (!empty($item->owner_leader_name)) {
                    $item->owner_team_name = 'Team ' . $item->owner_leader_name;
                    $item->owner_display = $item->owner_name . ' / Team ' . $item->owner_leader_name;
                } elseif (!empty($item->is_owner_leader) && !empty($item->owner_name)) {
                    $item->owner_team_name = 'Team ' . $item->owner_name;
                    $item->owner_display = $item->owner_name . ' / Team ' . $item->owner_name;
                } else {
                    $item->owner_team_name = '';
                    $item->owner_display = $item->owner_name;
                }
            }
        }
        $data = u::makingPagination($list, $total->total, $page, $limit);

        $total_0 = u::first("SELECT count(p.id) AS total FROM crm_parents AS p WHERE $cond ");
        $total_1 = u::first("SELECT count(p.id) AS total FROM crm_parents AS p WHERE $cond $cond_1 ");
        $total_2 = u::first("SELECT count(p.id) AS total FROM crm_parents AS p WHERE $cond $cond_2 ");
        $total_3 = u::first("SELECT count(p.id) AS total FROM crm_parents AS p WHERE $cond $cond_3 ");
        $total_4 = u::first("SELECT count(p.id) AS total FROM crm_parents AS p WHERE $cond $cond_4 ");
        $data->detail_total = (object) array(
            'total_0' => $total_0->total,
            'total_1' => $total_1->total,
            'total_2' => $total_2->total,
            'total_3' => $total_3->total,
            'total_4' => $total_4->total,
        );
        return response()->json($data);
    }

    public function detailList(Request $request)
    {
        $status = isset($request->status) ? $request->status : [];
        $level = isset($request->level) ? $request->level : [];
        $keyword = isset($request->keyword) ? trim($request->keyword) : '';
        $owner_id = isset($request->owner_id) ? $request->owner_id : [];
        $source_id = isset($request->source_id) ? $request->source_id : [];
        $source_detail_id = isset($request->source_detail_id) ? $request->source_detail_id : [];
        $product_id = isset($request->product_id) ? $request->product_id : [];
        $end_date = isset($request->end_date) ? $request->end_date : '';
        $start_date = isset($request->start_date) ? $request->start_date : '';
        $type_search = isset($request->type_search) ? (int) $request->type_search : 0;
        $branch_id = isset($request->branch_id) ? (int) $request->branch_id : 0;

        $pagination = (object) $request->pagination;
        $page = isset($pagination->cpage) ? (int) $pagination->cpage : 1;
        $limit = isset($pagination->limit) ? (int) $pagination->limit : 20;
        $offset = $page == 1 ? 0 : $limit * ($page - 1);
        $limitation = $limit > 0 ? " LIMIT $offset, $limit" : "";
        $cond = " 1 ";

        if (!Auth::user()->checkPermission('canViewAllParents')) {
            $cond .= " AND p.owner_id IN (" . Auth::user()->getStaffHasUser() . ")";
        }

        if (!empty($status)) {
            $status_ids = is_array($status) ? implode(",", array_map('intval', $status)) : (int) $status;
            $cond .= " AND p.status IN ($status_ids)";
        }
        if (!empty($owner_id)) {
            $owner_ids = is_array($owner_id) ? implode(",", array_map('intval', $owner_id)) : (int) $owner_id;
            $cond .= " AND p.owner_id IN ($owner_ids)";
        }
        if (!empty($source_id)) {
            $source_ids = is_array($source_id) ? implode(",", array_map('intval', $source_id)) : (int) $source_id;
            $cond .= " AND p.source_id IN ($source_ids)";
        }
        if (!empty($source_detail_id)) {
            $source_det_ids = is_array($source_detail_id) ? implode(",", array_map('intval', $source_detail_id)) : (int) $source_detail_id;
            $cond .= " AND p.source_detail_id IN ($source_det_ids)";
        }
        if (!empty($product_id)) {
            $ids_product = is_array($product_id)
                ? implode(",", array_map('intval', $product_id))
                : (int) $product_id;
            $cond .= " AND EXISTS (
                SELECT 1 FROM crm_students AS cs
                LEFT JOIN crm_student_checkin AS csc ON csc.crm_student_id = cs.id
                WHERE cs.parent_id = p.id
                  AND (cs.type_product IN ($ids_product) OR csc.type_product IN ($ids_product))
            )";
        }

        if ($keyword !== '') {
            $kw = addslashes($keyword);
            $cond .= " AND (p.name LIKE '%$kw%' OR p.mobile_1 LIKE '%$kw%' OR p.mobile_2 LIKE '%$kw%') ";
        }
        if ($end_date !== '') {
            $ed = addslashes($end_date);
            $cond .= " AND p.next_care_date < '$ed 23:59:59' AND p.next_care_date IS NOT NULL";
        }
        if ($start_date !== '') {
            $sd = addslashes($start_date);
            $cond .= " AND p.next_care_date > '$sd 00:00:00'";
        }
        // type_search tabs
        $cond_1 = " AND p.care_date IS NULL ";
        $cond_2 = " AND DATE_FORMAT(next_care_date,'%Y-%m-%d') = '" . date('Y-m-d') . "'";
        $cond_3 = " AND next_care_date < '" . date('Y-m-d') . "' 
            AND (p.care_date < p.next_care_date OR p.care_date IS NULL) ";
        $cond_4 = " AND (SELECT count(id) FROM crm_tickets WHERE parent_id = p.id AND status !=4 AND status!=5) > 0 ";

        $order_by = " ORDER BY p.id DESC ";
        $tmp_cond = "";
        if ($type_search == 1) {
            $tmp_cond = $cond_1;
        } elseif ($type_search == 2) {
            $tmp_cond = $cond_2;
            $order_by = " ORDER BY next_care_date ASC ";
        } elseif ($type_search == 3) {
            $tmp_cond = $cond_3;
            $order_by = " ORDER BY next_care_date ASC ";
        } elseif ($type_search == 4) {
            $tmp_cond = $cond_4;
            $order_by = " ORDER BY p.last_ticket_date DESC ";
        }

        $total = u::first("SELECT count(p.id) AS total FROM crm_parents AS p WHERE $cond $tmp_cond");

        $list = u::query("SELECT 
                p.id,
                p.name,
                p.email,
                p.mobile_1,
                p.mobile_2,
                p.gender,
                p.birthday,
                p.job_id,
                p.address,
                p.province_id,
                p.district_id,
                p.status,
                p.level,
                p.note,
                p.c2c_mobile,
                p.owner_id,
                p.source_id,
                p.source_detail_id,
                p.branch_id,
                p.creator_id,
                p.created_at,
                p.last_assign_date,
                p.next_care_date,
                p.care_date AS last_time_care,
                (SELECT name FROM sources WHERE id=p.source_id) AS source_name,
                (SELECT name FROM source_detail WHERE id=p.source_detail_id) AS source_detail_name,
                (SELECT note FROM crm_customer_care WHERE parent_id=p.id AND status=1 ORDER BY care_date DESC LIMIT 1) AS last_care,
                (SELECT name FROM users WHERE id=p.owner_id) AS owner_name,
                (SELECT m.name FROM users uo JOIN users m ON m.id = uo.manager_id WHERE uo.id = p.owner_id LIMIT 1) AS owner_leader_name,
                (SELECT COUNT(*) FROM role_has_user WHERE user_id = p.owner_id AND role_id = 69) AS is_owner_leader,
                (SELECT name FROM users WHERE id=p.creator_id) AS creator_name,
                (SELECT name FROM provinces WHERE id=p.province_id) AS province_name,
                (SELECT name FROM districts WHERE id=p.district_id) AS district_name,
                (SELECT name FROM branches WHERE id=p.branch_id) AS branch_name,
                (SELECT COUNT(ct.id) FROM crm_students cs INNER JOIN contracts ct ON (ct.student_id = cs.lms_id AND cs.parent_id = p.id) WHERE ct.status > 0) AS contract_count,
                (SELECT u.name FROM crm_parent_logs cpl LEFT JOIN users u ON u.id=cpl.creator_id WHERE cpl.parent_id=p.id ORDER BY cpl.id ASC LIMIT 1) AS first_owner_name,
                (SELECT name FROM crm_students WHERE parent_id=p.id LIMIT 0,1) AS hs1_name,
                (SELECT name FROM crm_students WHERE parent_id=p.id LIMIT 1,1) AS hs2_name,
                (SELECT GROUP_CONCAT(DISTINCT pr.name SEPARATOR ', ')
                 FROM crm_students AS cs
                 LEFT JOIN crm_student_checkin AS csc ON csc.crm_student_id = cs.id
                 INNER JOIN products AS pr ON (pr.id = csc.type_product OR pr.id = cs.type_product)
                 WHERE cs.parent_id = p.id) AS product_name
            FROM crm_parents AS p WHERE $cond $tmp_cond $order_by $limitation");

        if (!empty($list)) {
            $pids = [];
            foreach ($list as $item) {
                $pids[] = (int) $item->id;
                if (!empty($item->owner_leader_name)) {
                    $item->owner_team_name = 'Team ' . $item->owner_leader_name;
                    $item->owner_display = $item->owner_name . ' / Team ' . $item->owner_leader_name;
                } elseif (!empty($item->is_owner_leader) && !empty($item->owner_name)) {
                    $item->owner_team_name = 'Team ' . $item->owner_name;
                    $item->owner_display = $item->owner_name . ' / Team ' . $item->owner_name;
                } else {
                    $item->owner_team_name = '';
                    $item->owner_display = $item->owner_name;
                }
                if (empty($item->first_owner_name)) {
                    $item->first_owner_name = !empty($item->creator_name) ? $item->creator_name : $item->owner_name;
                }
                $item->username = !empty($item->email) ? $item->email : '';
                $item->ielts_target = !empty($item->level) ? $item->level : '';
                $item->reg_link = '';
            }
            $pids_str = implode(',', $pids);

            // Fetch recent care logs for these parents
            $care_logs = u::query("SELECT c.id, c.parent_id, c.care_date, c.next_care_date, c.note, c.call_status, c.call_status_sub, c.method_id,
                    (SELECT name FROM users WHERE id = c.creator_id) AS creator_name,
                    (SELECT name FROM crm_contact_methods WHERE id = c.method_id) AS method_name
                FROM crm_customer_care c
                WHERE c.parent_id IN ($pids_str) AND c.status = 1
                ORDER BY c.care_date DESC");

            $cares_by_parent = [];
            foreach ($care_logs as $c) {
                $c->call_status_label = u::getTitleCallStatus($c->call_status, $c->call_status_sub);
                // Keep up to 8 recent care notes per parent
                if (!isset($cares_by_parent[$c->parent_id]) || count($cares_by_parent[$c->parent_id]) < 8) {
                    $cares_by_parent[$c->parent_id][] = $c;
                }
            }

            // Fetch students for these parents
            $students_list = u::query("SELECT cs.id, cs.parent_id, cs.name, cs.gender, cs.birthday, cs.school_level, cs.school, cs.note, cs.lms_id,
                    (SELECT name FROM branches WHERE id=cs.checkin_branch_id) AS branch_name,
                    (SELECT GROUP_CONCAT(name SEPARATOR ', ') FROM products WHERE id = cs.type_product) AS product_name
                FROM crm_students cs
                WHERE cs.parent_id IN ($pids_str)
                ORDER BY cs.id ASC");

            $students_by_parent = [];
            foreach ($students_list as $st) {
                $students_by_parent[$st->parent_id][] = $st;
            }

            // Fetch test scores for students if any
            $tests_list = u::query("SELECT r.id, r.total_score, r.subject_total, r.thinking_total, r.assigned_level, r.eval_dt, cs.parent_id
                FROM igbh_student_results r
                INNER JOIN crm_students cs ON cs.lms_id = r.student_id
                WHERE cs.parent_id IN ($pids_str)
                ORDER BY r.eval_dt DESC");

            $tests_by_parent = [];
            foreach ($tests_list as $t) {
                if (!isset($tests_by_parent[$t->parent_id])) {
                    $tests_by_parent[$t->parent_id] = $t;
                }
            }

            foreach ($list as $item) {
                $item->care_history = isset($cares_by_parent[$item->id]) ? $cares_by_parent[$item->id] : [];
                $item->students = isset($students_by_parent[$item->id]) ? $students_by_parent[$item->id] : [];
                $item->test_score = isset($tests_by_parent[$item->id]) ? $tests_by_parent[$item->id] : null;
            }
        }

        $data = u::makingPagination($list, $total->total, $page, $limit);

        $total_0 = u::first("SELECT count(p.id) AS total FROM crm_parents AS p WHERE $cond ");
        $total_1 = u::first("SELECT count(p.id) AS total FROM crm_parents AS p WHERE $cond $cond_1 ");
        $total_2 = u::first("SELECT count(p.id) AS total FROM crm_parents AS p WHERE $cond $cond_2 ");
        $total_3 = u::first("SELECT count(p.id) AS total FROM crm_parents AS p WHERE $cond $cond_3 ");
        $total_4 = u::first("SELECT count(p.id) AS total FROM crm_parents AS p WHERE $cond $cond_4 ");
        $data->detail_total = (object) array(
            'total_0' => $total_0->total,
            'total_1' => $total_1->total,
            'total_2' => $total_2->total,
            'total_3' => $total_3->total,
            'total_4' => $total_4->total,
        );
        return response()->json($data);
    }

    public function validatePhone(Request $request)
    {
        $parent_id = isset($request->parent_id) ? $request->parent_id : '';
        $phone = isset($request->phone) ? $request->phone : '';
        $result = (object) array(
            'status' => 1,
            'message' => '',
            'dup_parent_id' => '',
        );
        if ($parent_id) {
            $duplicate_info = u::first("SELECT p.is_lock,u.name,u.hrm_id, u.branch_name,p.id AS parent_id, p.last_assign_date, p.care_date
                FROM crm_parents AS p 
                    LEFT JOIN users AS u ON u.id=p.owner_id
                WHERE (p.mobile_1='$phone' OR p.mobile_2='$phone') AND p.id!='$parent_id'");
            if ($duplicate_info) {
                $result->status = 0;
                $result->message = "Khách hàng có SĐT: $phone đang thuộc quyền quản lý của nhân viên $duplicate_info->name - $duplicate_info->hrm_id $duplicate_info->branch_name";
                $result->dup_parent_id = $duplicate_info->parent_id;
            }
        } else {
            $duplicate_info = u::first("SELECT p.is_lock,u.name,u.hrm_id, u.branch_name,p.status,p.id AS parent_id,p.owner_id, p.last_assign_date, p.care_date
                FROM crm_parents AS p 
                    LEFT JOIN users AS u ON u.id=p.owner_id 
                WHERE (p.mobile_1='$phone' OR p.mobile_2='$phone')");
            if ($duplicate_info) {
                if (data_get($duplicate_info, 'is_lock') == 1) {
                    $result->status = 0;
                    $result->dup_parent_id = $duplicate_info->parent_id;
                    $text = "";
                    if ($duplicate_info->care_date && (15 - floor((time() - strtotime($duplicate_info->care_date)) / (3600 * 24))) > 0) {
                        $thoi_gian_con = 15 - floor((time() - strtotime($duplicate_info->care_date)) / (3600 * 24));
                        $text .= "<br> Thời gian chăm sóc gần nhất: $duplicate_info->care_date <br> Thời gian còn lại sẽ được ghi đè sau $thoi_gian_con ngày";
                    } else {
                        $thoi_gian_con = 15 - floor((time() - strtotime($duplicate_info->last_assign_date)) / (3600 * 24));
                        $text .= "<br> Thời gian còn lại sẽ được ghi đè sau $thoi_gian_con ngày";
                    }
                    $result->message = "Khách hàng có SĐT: $phone đang thuộc quyền quản lý của nhân viên $duplicate_info->name - $duplicate_info->hrm_id $duplicate_info->branch_name ." . $text;
                } else {
                    $result->status = 2;
                    $result->message = "Khách hàng có SĐT: $phone đã tồn tại trên hệ thống, bạn có muốn chăm sóc?";
                    $result->dup_parent_id = $duplicate_info->parent_id;
                }
            }
        }
        return response()->json($result);
    }

    public function overwrite(Request $request)
    {
        $phone = isset($request->phone) ? $request->phone : '';
        $parent_info = u::first("SELECT * FROM crm_parents WHERE mobile_1='$phone' OR mobile_2='$phone'");
        if ($parent_info) {
            u::updateSimpleRow(array(
                'updated_at' => date('Y-m-d H:i:s'),
                'updator_id' => Auth::user()->id,
                'owner_id' => Auth::user()->id,
                'last_assign_date' => date('Y-m-d H:i:s'),
                'is_lock' => 1,
            ), array('id' => $parent_info->id), 'crm_parents');
            u::insertSimpleRow(array(
                'parent_id' => $parent_info->id,
                'last_owner_id' => $parent_info->owner_id,
                'owner_id' => Auth::user()->id,
                'created_at' => date('Y-m-d H:i:s'),
                'creator_id' => Auth::user()->id,
            ), 'crm_parent_overwrite');
            LogParents::logAssign($parent_info->id, $parent_info->owner_id, Auth::user()->id, Auth::user()->id, true);
        }
        $result = (object) array(
            'status' => 1,
            'message' => 'Ghi đè quyền chăm sóc thành công'
        );
        return response()->json($result);
    }

    public function validateC2CPhone(Request $request)
    {
        $phone = isset($request->phone) ? $request->phone : '';

        $parent_info = u::first("SELECT name,mobile_1,mobile_2 FROM crm_parents WHERE mobile_1 = '$phone' OR mobile_2 ='$phone'");
        if ($parent_info) {
            $result = (object) array(
                'status' => 1,
                'message' => "Khách hàng: " . $parent_info->name . " - " . $parent_info->mobile_1 . ($parent_info->mobile_2 ? " - " . $parent_info->mobile_2 : '')
            );
        } else {
            $result = (object) array(
                'status' => 0,
                'message' => 'Không tồn tại số điện thoại của khách hàng giới thiệu trên hệ thống'
            );
        }
        return response()->json($result);
    }

    public function add(Request $request)
    {
        $parent = data_get($request, 'parent');
        $parent_id = u::insertSimpleRow(array(
            'name' => data_get($parent, 'name'),
            'email' => data_get($parent, 'email'),
            'mobile_1' => data_get($parent, 'mobile_1'),
            'mobile_2' => data_get($parent, 'mobile_2'),
            'address' => data_get($parent, 'address'),
            'province_id' => data_get($parent, 'province_id'),
            'district_id' => data_get($parent, 'district_id'),
            'gender' => data_get($parent, 'gender'),
            'birthday' => data_get($parent, 'birthday'),
            'job_id' => data_get($parent, 'job_id'),
            'source_id' => data_get($parent, 'source_id'),
            'source_detail_id' => data_get($parent, 'emsource_detail_idail'),
            'note' => data_get($parent, 'note'),
            'created_at' => date('Y-m-d H:i:s'),
            'creator_id' => Auth::user()->id,
            'last_assign_date' => date('Y-m-d H:i:s'),
            'owner_id' => data_get($parent, 'owner_id') ? data_get($parent, 'owner_id') : Auth::user()->id,
            'status' => data_get($parent, 'status') ? data_get($parent, 'status') : 1,
            'c2c_mobile' => data_get($parent, 'c2c_mobile'),
            'is_lock' => 1,
        ), 'crm_parents');
        LogParents::logAdd($parent_id, 'Khởi tạo khách hàng thủ công', Auth::user()->id);
        $result = (object) array(
            'status' => 1,
            'message' => 'Thêm mới khách hàng thành công'
        );
        $students = data_get($request, 'students', []);
        $parent_owner_id = data_get($parent, 'owner_id') ? data_get($parent, 'owner_id') : Auth::user()->id;
        $parent_branch_id = data_get($parent, 'branch_id');
        foreach ($students as $student) {
            if (data_get($student, 'name')) {
                $crm_student_id = u::insertSimpleRow(array(
                    'parent_id' => $parent_id,
                    'name' => data_get($student, 'name'),
                    'gender' => data_get($student, 'gender'),
                    'birthday' => data_get($student, 'birthday'),
                    'school_level' => data_get($student, 'school_level'),
                    'school' => data_get($student, 'school'),
                    'note' => data_get($student, 'note'),
                    'checkin_branch_id' => $parent_branch_id ?: 0,
                    'status' => 3, // Lên chính thức ngay
                    'created_at' => date('Y-m-d H:i:s'),
                    'creator_id' => Auth::user()->id,
                ), 'crm_students');

                // Tự động thăng cấp thành học sinh chính thức LMS (bỏ qua bước checkin)
                $upgrade = StudentsController::promoteToOfficialStudent($crm_student_id, $parent_branch_id, $parent_owner_id);

                $content = "Thêm mới học sinh chính thức: " . data_get($student, 'name') . " (LMS: " . data_get($upgrade, 'lms_code') . ")";
                LogParents::logAdd($parent_id, $content, Auth::user()->id);
            }
        }
        return response()->json($result);
    }

    public function show(Request $request, $parent_id)
    {
        $cond = "";
        if (!Auth::user()->checkPermission('canViewAllParents')) {
            $cond .= " AND p.owner_id IN (" . Auth::user()->getStaffHasUser() . ")";
        }

        $data = u::first("SELECT p.*,(SELECT name FROM users WHERE id=p.creator_id) AS creator_name,
                (SELECT name FROM districts WHERE id=p.district_id) AS district_name,
                (SELECT name FROM provinces WHERE id=p.province_id) AS province_name,
                (SELECT name FROM sources WHERE id=p.source_id) AS source_name,
                (SELECT name FROM source_detail WHERE id=p.source_detail_id) AS source_detail_name,
                (SELECT title FROM jobs WHERE id=p.job_id) AS job_name,
                (SELECT count(id) FROM crm_customer_care WHERE parent_id=p.id AND status=1) AS num_care,
                (SELECT care_date FROM crm_customer_care WHERE parent_id=p.id  AND status=1 ORDER BY care_date DESC LIMIT 1) AS last_care
            FROM crm_parents AS p WHERE p.id=$parent_id $cond");
        return response()->json($data);
    }

    public function update(Request $request)
    {
        $pre_parent_info = u::first("SELECT * FROM crm_parents WHERE id = $request->id");
        $data_update = array(
            'name' => $request->name,
            'email' => $request->email,
            'mobile_1' => $request->mobile_1,
            'mobile_2' => $request->mobile_2,
            'address' => $request->address,
            'province_id' => $request->province_id,
            'district_id' => $request->district_id,
            'gender' => $request->gender,
            'birthday' => $request->birthday,
            'job_id' => $request->job_id,
            'source_id' => $request->source_id,
            'source_detail_id' => $request->source_detail_id,
            'note' => $request->note,
            'updated_at' => date('Y-m-d H:i:s'),
            'updator_id' => Auth::user()->id,
            'c2c_mobile' => $request->c2c_mobile,
        );
        $data = u::updateSimpleRow($data_update, array('id' => $request->id), 'crm_parents');
        LogParents::logUpdateInfo($pre_parent_info, $data_update, Auth::user()->id);
        $students = u::query("SELECT s.id FROM students AS s LEFT JOIN crm_students AS cs ON cs.lms_id=s.id WHERE cs.parent_id=$request->id");
        foreach ($students as $student) {
            u::updateSimpleRow(array(
                'gud_mobile1' => data_get($request, 'mobile_1'),
                'gud_name1' => data_get($request, 'name'),
                'gud_email1' => data_get($request, 'email'),
                'gud_birth_day1' => data_get($request, 'birthday'),
                'gud_gender1' => data_get($request, 'gender'),
                'gud_job1' => data_get($request, 'job_id'),
            ), array('id' => data_get($student, 'id')), 'students');
        }
        $result = (object) array(
            'status' => 1,
            'message' => 'Cập nhật khách hàng thành công'
        );
        return response()->json($result);
    }

    public function changeStatus(Request $request)
    {
        $pre_parent_info = u::first("SELECT status FROM crm_parents WHERE id=$request->parent_id");
        $data = u::updateSimpleRow(array(
            'updated_at' => date('Y-m-d H:i:s'),
            'updator_id' => Auth::user()->id,
            'status' => $request->status,
        ), array('id' => $request->parent_id), 'crm_parents');
        LogParents::logStatus($request->parent_id, $pre_parent_info->status, $request->status, Auth::user()->id);
        $result = (object) array(
            'status' => 1,
            'message' => 'Cập nhật trạng thái khách hàng thành công'
        );
        return response()->json($result);
    }

    public function changeLevel(Request $request)
    {
        $pre_parent_info = u::first("SELECT level FROM crm_parents WHERE id=$request->parent_id");
        $data = u::updateSimpleRow(array(
            'updated_at' => date('Y-m-d H:i:s'),
            'updator_id' => Auth::user()->id,
            'level' => $request->level,
        ), array('id' => $request->parent_id), 'crm_parents');
        $content = "Thay đổi level khách hàng từ `$pre_parent_info->level` thành `$request->level`";
        LogParents::logAdd($request->parent_id, $content, Auth::user()->id);
        $result = (object) array(
            'status' => 1,
            'message' => 'Cập nhật level khách hàng thành công'
        );
        return response()->json($result);
    }

    public function assign(Request $request)
    {
        $pre_parent_info = u::first("SELECT owner_id,last_assign_date FROM crm_parents WHERE id=$request->parent_id");
        $data = u::updateSimpleRow(array(
            'updated_at' => date('Y-m-d H:i:s'),
            'updator_id' => Auth::user()->id,
            'owner_id' => $request->owner_id,
            'last_assign_date' => $request->owner_id != $pre_parent_info->owner_id ? date('Y-m-d H:i:s') : $pre_parent_info->last_assign_date,
            'is_lock' => 1,
        ), array('id' => $request->parent_id), 'crm_parents');
        LogParents::logAssign($request->parent_id, $pre_parent_info->owner_id, $request->owner_id, Auth::user()->id);
        $result = (object) array(
            'status' => 1,
            'message' => 'Cập nhật người phụ trách thành công'
        );
        return response()->json($result);
    }

    public function assignList(Request $request)
    {
        $cond = implode(",", $request->parents);
        $arr_owner = $request->owners;
        $list_parent_info = u::query("SELECT p.id AS parent_id,p.owner_id,(SELECT CONCAT(name,' (',hrm_id,')') FROM users WHERE id= p.owner_id) AS pre_owner,p.last_assign_date FROM crm_parents AS p WHERE p.id IN ($cond)");
        foreach ($list_parent_info as $k => $row) {
            $owner_id = $arr_owner[$k % count($arr_owner)];
            $last_assign_date = $owner_id != $row->owner_id ? date('Y-m-d H:i:s') : $row->last_assign_date;
            u::updateSimpleRow(array(
                'updated_at' => date('Y-m-d H:i:s'),
                'updator_id' => Auth::user()->id,
                'owner_id' => $owner_id,
                'last_assign_date' => $last_assign_date,
                'is_lock' => 1,
            ), array('id' => $row->parent_id), 'crm_parents');
            LogParents::logAssign($row->parent_id, $row->owner_id, $owner_id, Auth::user()->id);
        }
        $result = (object) array(
            'status' => 1,
            'message' => 'Bàn giao thành công'
        );
        return response()->json($result);
    }

    public function updateNextCareDate(Request $request)
    {
        $data = u::updateSimpleRow(array(
            'updated_at' => date('Y-m-d H:i:s'),
            'updator_id' => $request->user()->id,
            'next_care_date' => date('Y-m-d H:i:s', strtotime($request->next_care_date)),
        ), array('id' => $request->parent_id), 'crm_parents');
        LogParents::logAdd($request->parent_id, 'Cập nhật lịch chăm sóc tiếp theo: ' . date('Y-m-d H:i:s', strtotime($request->next_care_date)), $request->user()->id);
        $result = (object) array(
            'status' => 1,
            'message' => 'Cập nhật lịch chăm sóc thành công'
        );
        return response()->json($result);
    }

    public function getLogs(Request $request, $parent_id)
    {
        $data = u::query("SELECT l.*,(SELECT name FROM users WHERE id=l.creator_id) AS creator_name
            FROM crm_parent_logs AS l WHERE l.parent_id=$parent_id AND l.status=1 
            ORDER BY l.id DESC");
        return response()->json($data);
    }

    public function sendSms(Request $request)
    {
        $parent_info = u::first("SELECT id,mobile_1,mobile_2 FROM crm_parents WHERE id='$request->parent_id'");
        $phone = $request->phone ? $request->phone : $parent_info->mobile_1;
        if ($parent_info) {
            u::insertSimpleRow(array(
                'parent_id' => $parent_info->id,
                'note' => $request->content,
                'created_at' => date('Y-m-d H:i:s'),
                'creator_id' => $request->user()->id,
                'method_id' => 4,
                'care_date' => date('Y-m-d H:i:s'),
                'phone' => $phone,
                'branch_id' => Auth::user()->branch_id,
            ), 'crm_customer_care');
        }
        $result = (object) array(
            'status' => 1,
            'message' => 'Gửi tin nhắn sms thành công'
        );
        return response()->json($result);
    }

    public function getAllDataTicketByParent(Request $request, $parent_id)
    {
        $list = u::query("SELECT t.*
            FROM crm_tickets AS t WHERE parent_id=$parent_id ORDER BY t.id DESC");
        $total = u::first("SELECT count(t.id) AS total
            FROM crm_tickets AS t WHERE parent_id=$parent_id AND status NOT IN (4,5)");
        return response()->json([
            'list' => $list,
            'total_unsuccess' => $total->total
        ]);
    }

    public function addTicket(Request $request)
    {
        $id = u::insertSimpleRow(array(
            'parent_id' => data_get($request, 'parent_id'),
            'type' => data_get($request, 'type'),
            'description' => data_get($request, 'description'),
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'creator_id' => Auth::user()->id,
        ), 'crm_tickets');
        $data = (object) [
            'status' => 1,
            'message' => "Thêm mới ticket thành công",
            'data' => $id
        ];
        u::updateSimpleRow(array('last_ticket_date' => date('Y-m-d H:i:s')), array('id' => data_get($request, 'parent_id')), 'crm_parents');

        return response()->json($data);
    }

    public function updateTicket(Request $request)
    {
        u::updateSimpleRow(array(
            'status' => data_get($request, 'status'),
            'note' => data_get($request, 'note'),
            'updated_at' => date('Y-m-d H:i:s'),
            'updator_id' => Auth::user()->id,
        ), array('id' => data_get($request, 'id')), 'crm_tickets');
        $data = (object) [
            'status' => 1,
            'message' => "Cập nhật thành công",
        ];
        return response()->json($data);
    }

    public function uploadAvatar(Request $request)
    {
        $parent_id = isset($_POST['parent_id']) ? $_POST['parent_id'] : '';
        $total = count($_FILES['files']['name']);
        if ($total > 0) {
            $tmpFilePath = $_FILES['files']['tmp_name'][0];
            if ($tmpFilePath != "") {
                $dir = __DIR__ . '/../../../public/static/upload/avatar_parents/' . date('Y_m') . '/';
                if (!file_exists($dir)) {
                    mkdir($dir);
                }
                $newFilePath = $dir . $_FILES['files']['name'][0];
                $newFilePath = u::update_file_name($newFilePath);
                $dir_file_insert = str_replace(__DIR__ . '/../../../public', '', $newFilePath);
                move_uploaded_file($tmpFilePath, $newFilePath);
                u::updateSimpleRow(array(
                    'avatar_url' => $dir_file_insert
                ), array('id' => $parent_id), 'crm_parents');
                return response()->json([
                    'status' => 1,
                    'message' => 'Upload avatar thành công.',
                ]);
            }
        }

        return response()->json([
            'status' => 0,
            'message' => 'Upload avatar thất bại vui lòng kiểm tra dung lượng và định dạng file.',
        ]);
    }

    public function getVouchers(Request $request, $parent_id)
    {
        $parent_info = u::first("SELECT mobile_1 FROM crm_parents WHERE id=$parent_id");
        $data = u::query("SELECT c.*
            FROM coupons AS c WHERE c.c2c_mobile = '" . data_get($parent_info, 'mobile_1') . "' 
            ORDER BY c.id DESC");
        return response()->json($data);
    }

    public function processParentLock()
    {
        u::query("UPDATE crm_parents SET is_lock = 1");
        u::query("UPDATE crm_parents AS p SET p.care_date=(SELECT care_date FROM crm_customer_care WHERE parent_id=p.id AND creator_id=p.owner_id AND `status`=1 ORDER BY id DESC LIMIT 1)");
        u::query("UPDATE crm_parents SET is_lock = 0 
            WHERE
                (care_date IS NULL AND DATEDIFF( CURRENT_DATE, last_assign_date )> 15) OR
                (care_date IS NOT NULL AND DATEDIFF( CURRENT_DATE, care_date )> 15) ");
        return "ok";
    }

    public static function processParentLockById($parent_id)
    {
        u::query("UPDATE crm_parents AS p SET 
                p.care_date=(SELECT care_date FROM crm_customer_care WHERE parent_id=p.id AND creator_id=p.owner_id AND `status`=1 ORDER BY id DESC LIMIT 1),
                p.is_lock = 1
            WHERE p.id=$parent_id ");
        u::query("UPDATE crm_parents SET is_lock = 0 
            WHERE
                id=$parent_id AND (
                    (care_date IS NULL AND DATEDIFF( CURRENT_DATE, last_assign_date )> 15) OR
                    (care_date IS NOT NULL AND DATEDIFF( CURRENT_DATE, care_date )> 15) 
                ) ");
        return true;
    }

    public static function processGetStatus()
    {
        u::query(" UPDATE crm_parents AS p
            LEFT JOIN crm_students AS s ON s.parent_id = p.id
            SET p.`status` = 8
            WHERE (SELECT count(id) FROM contracts WHERE student_id = s.id AND type>0) >0 ");
    }

    public static function processCrmStudent()
    {
        u::query("UPDATE crm_students AS s 
                    SET s.status = 4
                WHERE
                    ((DATEDIFF(CURRENT_DATE, s.checkin_at) > 10 AND s.checkined_at IS NULL) OR DATEDIFF(CURRENT_DATE, s.checkined_at) > 10) 
                    AND ( 
                        DATEDIFF(CURRENT_DATE, (SELECT IF(enrolment_last_date IS NOT NULL, enrolment_last_date, created_at) FROM contracts WHERE type=0 AND student_id=s.lms_id ORDER BY id DESC LIMIT 1))>10
                        OR (SELECT count(id) FROM contracts WHERE student_id=s.lms_id)=0
                    ) 
                    AND  (
                        DATEDIFF(CURRENT_DATE, (SELECT IF(status !=7, CURRENT_DATE, enrolment_last_date) FROM contracts WHERE type=1 AND student_id=s.lms_id ORDER BY id DESC LIMIT 1))>60
                        OR (SELECT count(id) FROM contracts WHERE student_id=s.lms_id AND type=1)=0
                    )
        ");
    }
}
