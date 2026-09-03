<?php

namespace App\Http\Controllers;

use App\User;
use App\Http\Controllers\Controller;
use App\Models\LogStudents;
use App\Providers\UtilityServiceProvider as u;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChargesController extends Controller
{
    public function waitchargesList(Request $request)
    {
        $branch_id = isset($request->branch_id) ? $request->branch_id : [];
        $keyword = isset($request->keyword) ? $request->keyword : '';
        $type = isset($request->type) ? $request->type : 1;

        $pagination = (object) $request->pagination;
        $page = isset($pagination->cpage) ? (int) $pagination->cpage : 1;
        $limit = isset($pagination->limit) ? (int) $pagination->limit : 20;
        $offset = $page == 1 ? 0 : $limit * ($page - 1);
        $limitation = $limit > 0 ? " LIMIT $offset, $limit" : "";
        if ($type == 1) {
            $cond = " c.status > 0  AND c.must_charge > 0 AND c.debt_amount > 0 ";
            $cond .= " AND c.branch_id IN (" . Auth::user()->getBranchesHasUser() . ")";

            if (!empty($branch_id)) {
                $cond .= " AND c.branch_id IN (" . implode(",", $branch_id) . ")";
            }

            if ($keyword !== '') {
                $cond .= " AND (s.lms_code LIKE '%$keyword%' OR s.name LIKE '%$keyword%' OR c.code LIKE '%$keyword%' OR s.gud_mobile1 LIKE '%$keyword%') ";
            }
            $role_ids = u::query("SELECT role_id FROM role_has_user WHERE user_id = " . Auth::user()->id);
            $roles = array_map(function ($r) {
                return $r->role_id;
            }, $role_ids);
            if (in_array(68, $roles) || in_array(69, $roles)) {
                $cond .= " AND c.ec_id IN (" . Auth::user()->getStaffHasUser() . ")";
            }

            $order_by = " ORDER BY c.id DESC ";

            $total = u::first("SELECT count(c.id) AS total 
                FROM agreements AS c LEFT JOIN students AS s ON s.id=c.student_id WHERE $cond");

            $list = u::query("SELECT c.id AS agreement_id, s.name, s.lms_code, 
                    (SELECT CONCAT(name,'-',hrm_id) FROM users WHERE id= c.ec_id) AS ec_name,
                    (SELECT CONCAT(name,'-',hrm_id) FROM users WHERE id= c.ec_leader_id) AS ec_leader_name,
                    c.code, (SELECT name FROM tuition_fee WHERE id=c.tuition_fee_id) AS tuition_fee_name,
                    c.must_charge, c.debt_amount, c.status
                FROM agreements AS c 
                    LEFT JOIN students AS s ON s.id=c.student_id
                WHERE $cond $order_by $limitation");
        } else {
            $cond = " c.status = 5  AND c.must_charge > 0 AND c.debt_amount > 0 ";
            $cond .= " AND c.branch_id IN (" . Auth::user()->getBranchesHasUser() . ")";

            if (!empty($branch_id)) {
                $cond .= " AND c.branch_id IN (" . implode(",", $branch_id) . ")";
            }

            if ($keyword !== '') {
                $cond .= " AND (s.lms_code LIKE '%$keyword%' OR s.name LIKE '%$keyword%' OR s.gud_mobile1 LIKE '%$keyword%') ";
            }
            $role_ids = u::query("SELECT role_id FROM role_has_user WHERE user_id = " . Auth::user()->id);
            $roles = array_map(function ($r) {
                return $r->role_id;
            }, $role_ids);
            if (in_array(68, $roles) || in_array(69, $roles)) {
                $cond .= " AND t.ec_id IN (" . Auth::user()->getStaffHasUser() . ")";
            }

            $order_by = " ORDER BY c.id DESC ";

            $total = u::first("SELECT count(c.id) AS total 
                FROM reserves AS c LEFT JOIN students AS s ON s.id=c.student_id 
                LEFT JOIN term_student_user AS t ON t.student_id=s.id WHERE $cond");

            $list = u::query("SELECT c.id AS reserve_id,c.id AS agreement_id, s.name, s.lms_code, 
                    (SELECT CONCAT(name,'-',hrm_id) FROM users WHERE id= t.ec_id) AS ec_name,
                    (SELECT CONCAT(name,'-',hrm_id) FROM users WHERE id= t.ec_leader_id) AS ec_leader_name,
                    (SELECT CONCAT(name,'-',hrm_id) FROM users WHERE id= c.creator_id) AS creator_name,
                    c.must_charge, c.debt_amount, c.status
                FROM reserves AS c 
                    LEFT JOIN students AS s ON s.id=c.student_id
                    LEFT JOIN term_student_user AS t ON t.student_id=s.id 
                WHERE $cond $order_by $limitation");
        }

        $data = u::makingPagination($list, $total->total, $page, $limit);
        return response()->json($data);
    }

    public function add(Request $request)
    {
        // Handle file uploads
        $attachments = [];
        if ($request->hasFile('attachments')) {
            $files = $request->file('attachments');
            foreach ($files as $file) {
                if ($file->isValid()) {
                    $dir = public_path('static/upload/payment_attachments/' . date('Y_m') . '/');
                    if (!file_exists($dir)) {
                        mkdir($dir, 0755, true);
                    }
                    $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                    $file->move($dir, $filename);
                    $newFilePath = $dir . $filename;
                    $dir_file_insert = str_replace(public_path(), '', $newFilePath);
                    $attachments[] = $dir_file_insert;
                }
            }
        }

        if ($request->type == 1) {
            $agreement_info = u::getObject(array('id' => $request->agreement_id), 'agreements');
            u::insertSimpleRow(array(
                'agreement_id' => data_get($request, 'agreement_id'),
                'charge_amount' => data_get($request, 'amount'),
                'debt_amount' => (int) data_get($agreement_info, 'must_charge') - (int) data_get($agreement_info, 'total_charged') - (int) data_get($request, 'amount'),
                'total_charged' => (int) data_get($agreement_info, 'total_charged') + (int) data_get($request, 'amount'),
                'charge_date' => data_get($request, 'charge_date'),
                'method' => data_get($request, 'method'),
                'note' => data_get($request, 'note'),
                'meta_data' => json_encode($request->input()),
                'attachments' => !empty($attachments) ? json_encode($attachments, JSON_UNESCAPED_SLASHES) : null,
                'created_at' => date('Y-m-d H:i:s'),
                'creator_id' => Auth::user()->id,
                'type' => $request->type,
                'status' => 0
            ), 'tmp_payments');
        } else {
            $reserve_info = u::getObject(array('id' => $request->agreement_id), 'reserves');
            u::insertSimpleRow(array(
                'agreement_id' => data_get($request, 'agreement_id'),
                'charge_amount' => data_get($request, 'amount'),
                'debt_amount' => (int) data_get($reserve_info, 'must_charge') - (int) data_get($reserve_info, 'total_charged') - (int) data_get($request, 'amount'),
                'total_charged' => (int) data_get($reserve_info, 'total_charged') + (int) data_get($request, 'amount'),
                'charge_date' => data_get($request, 'charge_date'),
                'method' => data_get($request, 'method'),
                'note' => data_get($request, 'note'),
                'meta_data' => json_encode($request->input()),
                'attachments' => !empty($attachments) ? json_encode($attachments, JSON_UNESCAPED_SLASHES) : null,
                'created_at' => date('Y-m-d H:i:s'),
                'creator_id' => Auth::user()->id,
                'type' => $request->type,
                'status' => 0
            ), 'tmp_payments');
        }

        $result = array(
            'status' => 1,
            'message' => 'Thêm mới phiếu thu thành công.'
        );
        return response()->json($result);
    }

    public function update(Request $request)
    {
        // Handle existing attachments - prioritize from request
        if ($request->has('existing_attachments')) {
            $existing_attachments = json_decode($request->existing_attachments, true) ?: [];
        } else {
            $existing_payment = u::getObject(array('id' => data_get($request, 'id')), 'tmp_payments');
            $existing_attachments = !empty($existing_payment->attachments) ? json_decode($existing_payment->attachments, true) : [];
        }

        // Handle new file uploads
        $attachments = $existing_attachments;
        if ($request->hasFile('attachments')) {
            $files = $request->file('attachments');
            foreach ($files as $file) {
                if ($file->isValid()) {
                    $dir = public_path('static/upload/payment_attachments/' . date('Y_m') . '/');
                    if (!file_exists($dir)) {
                        mkdir($dir, 0755, true);
                    }
                    $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                    $file->move($dir, $filename);
                    $newFilePath = $dir . $filename;
                    $dir_file_insert = str_replace(public_path(), '', $newFilePath);
                    $attachments[] = $dir_file_insert;
                }
            }
        }

        $agreement_info = u::getObject(array('id' => $request->agreement_id), 'agreements');
        u::updateSimpleRow(array(
            'agreement_id' => data_get($request, 'agreement_id'),
            'charge_amount' => data_get($request, 'amount'),
            'debt_amount' => (int) data_get($agreement_info, 'must_charge') - (int) data_get($agreement_info, 'total_charged') - (int) data_get($request, 'amount'),
            'total_charged' => (int) data_get($agreement_info, 'total_charged') + (int) data_get($request, 'amount'),
            'charge_date' => data_get($request, 'charge_date'),
            'method' => data_get($request, 'method'),
            'note' => data_get($request, 'note'),
            'meta_data' => json_encode($request->input()),
            'attachments' => !empty($attachments) ? json_encode($attachments, JSON_UNESCAPED_SLASHES) : null,
            'updated_at' => date('Y-m-d H:i:s'),
            'updator_id' => Auth::user()->id
        ), array('id' => data_get($request, 'id')), 'tmp_payments');
        $result = array(
            'status' => 1,
            'message' => 'Cập nhật phiếu thu thành công.'
        );
        return response()->json($result);
    }

    public function approve(Request $request)
    {
        $tmp_payment = u::getObject(array('id' => $request->id), 'tmp_payments');
        if (data_get($request, 'status') == 1) {
            if (data_get($tmp_payment, 'type') == 1) {
                $agreement_info = u::getObject(array('id' => data_get($tmp_payment, 'agreement_id')), 'agreements');

                $must_charge = (int) data_get($agreement_info, 'must_charge');
                $total_charged = (int) data_get($agreement_info, 'total_charged');
                $charge_amount = (int) data_get($tmp_payment, 'charge_amount');
                $discount_amount = (int) data_get($agreement_info, 'discount_amount');
                $received_amount = (int) data_get($agreement_info, 'received_amount', 0);
                $transferred_amount = (int) data_get($agreement_info, 'transferred_amount', 0);

                // ---- Validate cân bằng ----
                // effective = tiền thực tế đã có: total_charged + received_amount - transferred_amount
                // must_charge = effective + charge_amount + debt_after + discount_amount
                $current_effective = $total_charged + $received_amount - $transferred_amount;
                $debt_after = $must_charge - $current_effective - $charge_amount - $discount_amount;
                $balance = $current_effective + $charge_amount + max(0, $debt_after) + $discount_amount;
                if ($balance !== $must_charge) {
                    return response()->json([
                        'status' => 0,
                        'message' => 'Số liệu không cân bằng: '
                            . 'Đã thu ' . number_format($current_effective)
                            . ' + Thu ' . number_format($charge_amount)
                            . ' + Giảm trừ ' . number_format($discount_amount)
                            . ' + Công nợ ' . number_format(max(0, $debt_after))
                            . ' = ' . number_format($balance)
                            . ' ≠ Phải đóng ' . number_format($must_charge)
                    ], 422);
                }

                // ---- Ghi payments ----
                u::insertSimpleRow(array(
                    'agreement_id' => data_get($agreement_info, 'id'),
                    'student_id' => data_get($agreement_info, 'student_id'),
                    'branch_id' => data_get($agreement_info, 'branch_id'),
                    'cm_id' => data_get($agreement_info, 'cm_id'),
                    'ec_id' => data_get($agreement_info, 'ec_id'),
                    'method' => data_get($tmp_payment, 'method'),
                    'must_charge' => $must_charge,
                    'amount' => $charge_amount,
                    'total' => $total_charged + $charge_amount,
                    'debt' => max(0, $debt_after),
                    'charge_date' => data_get($tmp_payment, 'charge_date'),
                    'note' => data_get($tmp_payment, 'note'),
                    'created_at' => date('Y-m-d H:i:s'),
                    'creator_id' => Auth::user()->id,
                    'type' => 1
                ), 'payments');

                // ---- Cập nhật agreements ----
                $new_total_charged = $total_charged + $charge_amount;
                $charge_date = date('Y-m-d', strtotime(data_get($tmp_payment, 'charge_date')));
                if ($debt_after <= 0) {
                    $count_recharge = (int) data_get($agreement_info, 'count_recharge');
                    $first_8th = data_get($agreement_info, 'first_8th_session_date');
                    if (!empty($first_8th)) {
                        if ($charge_date <= $first_8th) {
                            $count_recharge = 0;
                        } else {
                            $count_recharge = 1;
                        }
                    } else {
                        $count_recharge = 0;
                    }

                    u::updateSimpleRow(array(
                        'status' => 3,
                        'total_charged' => $new_total_charged,
                        'debt_amount' => 0,
                        'full_fee_date' => $charge_date,
                        'last_pay_date' => $charge_date,
                        'count_recharge' => $count_recharge,
                        'updated_at' => date('Y-m-d H:i:s'),
                        'updator_id' => Auth::user()->id
                    ), array('id' => data_get($agreement_info, 'id')), 'agreements');
                    LogStudents::logAdd(data_get($agreement_info, 'student_id'), 'Thu đủ phí cho hợp đồng - ' . data_get($agreement_info, 'code'), Auth::user()->id);
                } else {
                    u::updateSimpleRow(array(
                        'status' => 2,
                        'total_charged' => $new_total_charged,
                        'debt_amount' => $debt_after,
                        'last_pay_date' => $charge_date,
                        'updated_at' => date('Y-m-d H:i:s'),
                        'updator_id' => Auth::user()->id,
                    ), array('id' => data_get($agreement_info, 'id')), 'agreements');
                    LogStudents::logAdd(data_get($agreement_info, 'student_id'), 'Đặt cọc ' . u::formatCurrency($charge_amount) . ' cho hợp đồng - ' . data_get($agreement_info, 'code'), Auth::user()->id);
                }
                $report = new ReportsController();
                $report->updateSalaryMonthAll(data_get($agreement_info, 'id'));
                    
                u::addLogAgreements(data_get($agreement_info, 'id'));
                $this->processContractsByAgreement(data_get($agreement_info, 'id'));

            } else {
                $reserve_info = u::getObject(array('id' => data_get($tmp_payment, 'agreement_id')), 'reserves');
                u::insertSimpleRow(array(
                    'agreement_id' => data_get($reserve_info, 'id'),
                    'student_id' => data_get($reserve_info, 'student_id'),
                    'branch_id' => data_get($reserve_info, 'branch_id'),
                    'method' => data_get($tmp_payment, 'method'),
                    'must_charge' => data_get($reserve_info, 'must_charge'),
                    'amount' => data_get($tmp_payment, 'charge_amount'),
                    'total' => (int) data_get($reserve_info, 'total_charged') + (int) data_get($tmp_payment, 'charge_amount'),
                    'debt' => (int) data_get($reserve_info, 'must_charge') - (int) data_get($reserve_info, 'total_charged') - (int) data_get($tmp_payment, 'charge_amount'),
                    'charge_date' => data_get($tmp_payment, 'charge_date'),
                    'note' => data_get($tmp_payment, 'note'),
                    'created_at' => date('Y-m-d H:i:s'),
                    'creator_id' => Auth::user()->id,
                    'type' => 2
                ), 'payments');

                $debt_amount = (int) data_get($reserve_info, 'must_charge') - (int) data_get($reserve_info, 'total_charged') - (int) data_get($tmp_payment, 'charge_amount');
                if ($debt_amount == 0) {
                    u::updateSimpleRow(array(
                        'status' => 6,
                        'total_charged' => (int) data_get($reserve_info, 'total_charged') + (int) data_get($tmp_payment, 'charge_amount'),
                        'debt_amount' => 0,
                        'updated_at' => date('Y-m-d H:i:s'),
                        'updator_id' => Auth::user()->id,
                    ), array('id' => data_get($reserve_info, 'id')), 'reserves');
                    LogStudents::logAdd(data_get($reserve_info, 'student_id'), 'Thu đủ phí bảo lưu ', Auth::user()->id);
                    if (data_get($reserve_info, 'start_date') > date('Y-m-d')) {
                        u::insertSimpleRow(array(
                            'student_id' => data_get($reserve_info, 'student_id'),
                            'data_id' => data_get($reserve_info, 'id'),
                            'type' => 1,
                            'status' => 1,
                            'created_at' => date('Y-m-d H:i:s'),
                            'processed_at' => data_get($reserve_info, 'start_date')
                        ), 'student_waitting_process');
                    } else {
                        $reservesController = new ReservesController();
                        $reservesController->processReserve(data_get($reserve_info, 'id'));
                    }
                } else {
                    u::updateSimpleRow(array(
                        'total_charged' => (int) data_get($reserve_info, 'total_charged') + (int) data_get($tmp_payment, 'charge_amount'),
                        'debt_amount' => $debt_amount,
                        'updated_at' => date('Y-m-d H:i:s'),
                        'updator_id' => Auth::user()->id,
                    ), array('id' => data_get($reserve_info, 'id')), 'reserves');
                    LogStudents::logAdd(data_get($reserve_info, 'student_id'), 'Thu phí bảo lưu ', Auth::user()->id);
                }
            }

            u::updateSimpleRow(array(
                'status' => 1,
                'approver_id' => Auth::user()->id,
                'approved_at' => date('Y-m-d H:i:s'),
            ), array('id' => data_get($tmp_payment, 'id')), 'tmp_payments');
            $result = array(
                'status' => 1,
                'message' => 'Duyệt phiếu thu thành công.'
            );
        } else {
            u::updateSimpleRow(array(
                'status' => data_get($request, 'status'),
                'approver_id' => Auth::user()->id,
                'approved_at' => date('Y-m-d H:i:s'),
            ), array('id' => data_get($request, 'id')), 'tmp_payments');
            $result = array(
                'status' => 1,
                'message' => 'Từ chối duyệt phiếu thu thành công.'
            );
        }

        return response()->json($result);
    }

    public static function processContractsByAgreement($agreement_id)
    {
        $agreementInfo = u::getObject(array('id' => $agreement_id), 'agreements');
        $contracts = u::query("SELECT * FROM contracts WHERE agreement_id=$agreement_id AND status>0 AND status!=8 AND relearn_from_contract_id IS NULL ORDER BY product_id");
        $effectiveCharged = (float) data_get($agreementInfo, 'total_charged', 0)
            + (float) data_get($agreementInfo, 'received_amount', 0)
            - (float) data_get($agreementInfo, 'transferred_amount', 0);
        $totalDiscount = (float) data_get($agreementInfo, 'discount_amount', 0);
        $isFullyPaidAgreement = (isset($agreementInfo->debt_amount) && (float) $agreementInfo->debt_amount <= 0);

        $dataResult = self::splitChargedAndDiscountAmount($effectiveCharged, $totalDiscount, (array) $contracts, $isFullyPaidAgreement);
        $packages = data_get($dataResult, 'packages');
        if (!empty($packages)) {
            foreach ($packages as $row) {
                $availableSession = (int) data_get($row, 'contract_data.init_tuition_fee_session') && (int) data_get($row, 'contract_data.must_charge') ?
                    round(((int) data_get($row, 'total_charged') + (int) data_get($row, 'discount_amount')) / ((int) data_get($row, 'contract_data.must_charge') / (int) data_get($row, 'contract_data.init_tuition_fee_session'))) : 0;
                
                if (data_get($row, 'contract_data.status') ==7) {
                    $status = 7;
                }elseif (data_get($row, 'contract_data.class_id')) {
                    $status = 6;
                } else {
                    $status = data_get($row, 'is_fully_paid') ? (data_get($row, 'status') > 3 ? data_get($row, 'status') : 3) : 2;
                }
                u::updateSimpleRow([
                    'status' => $status,
                    'real_sessions' => $availableSession,
                    'summary_sessions' => $availableSession,
                    'left_sessions' => $availableSession - data_get($row, 'done_sessions'),
                    'total_charged' => (int) data_get($row, 'total_charged'),
                    'init_total_charged' => (int) data_get($row, 'total_charged'),
                    'discount_amount' => (int) data_get($row, 'discount_amount'),
                    'debt_amount' => (int) data_get($row, 'debt_amount'),
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updator_id' => Auth::user()->id ?? 0,
                ], array('id' => data_get($row, 'contract_id')), 'contracts');
                u::addLogContracts(data_get($row, 'contract_id'));
            }
        }

        return true;
    }
    public static function splitChargedAndDiscountAmount(float $totalCharged, float $totalDiscount, array $packages, bool $isFullyPaidAgreement = false): array
    {
        // Kiểm tra điều kiện ưu tiên đặc biệt: tồn tại cả 28 & 29, trong đó 28 chưa xếp lớp và 29 đã xếp lớp
        $has28Unassigned = false;
        $has29Assigned = false;

        foreach ($packages as $p) {
            $pid = (int) data_get($p, 'product_id');
            $classId = (int) data_get($p, 'class_id');
            if ($pid === 28 && empty($classId)) {
                $has28Unassigned = true;
            }
            if ($pid === 29 && !empty($classId)) {
                $has29Assigned = true;
            }
        }

        $swap28And29 = $has28Unassigned && $has29Assigned;

        $getProductPriority = function ($package) use ($swap28And29) {
            $pid = (int) data_get($package, 'product_id');
            if ($swap28And29) {
                $orderMap = [25 => 1, 26 => 2, 27 => 3, 29 => 4, 28 => 5];
            } else {
                $orderMap = [25 => 1, 26 => 2, 27 => 3, 28 => 4, 29 => 5];
            }

            return $orderMap[$pid] ?? (1000 + $pid);
        };

        // Sắp xếp theo ưu tiên product_id (25 -> 26 -> 27 -> 28 -> 29 hay 25 -> 26 -> 27 -> 29 -> 28), sau đó tới count_recharge
        usort($packages, function ($a, $b) use ($getProductPriority) {
            $prioA = $getProductPriority($a);
            $prioB = $getProductPriority($b);
            if ($prioA !== $prioB) {
                return $prioA <=> $prioB;
            }
            $cntA = (int) data_get($a, 'count_recharge', 0);
            $cntB = (int) data_get($b, 'count_recharge', 0);
            if ($cntA !== $cntB) {
                return $cntA <=> $cntB;
            }
            return (int) data_get($a, 'id', 0) <=> (int) data_get($b, 'id', 0);
        });

        $total_must_charge = array_sum(array_map(function ($p) {
            return (float) $p->must_charge;
        }, $packages));

        $remainCharged = $totalCharged;
        $remainDiscount = $totalDiscount;
        $result = [];

        $discountRatio = $total_must_charge > 0 ? $totalDiscount / $total_must_charge : 0;
        $accumulatedDiscount = 0;
        $accumulatedMustCharge = 0;
        
        $chargedRatio = $total_must_charge > 0 ? $totalCharged / $total_must_charge : 0;
        $accumulatedCharged = 0;

        foreach ($packages as $package) {
            $must_charge = (float) $package->must_charge;

            if ($total_must_charge > 0) {
                $accumulatedMustCharge += $must_charge;
                $targetAccumulatedDiscount = round($accumulatedMustCharge * $discountRatio);
                
                $discount = $targetAccumulatedDiscount - $accumulatedDiscount;
                
                $discount = min($discount, $remainDiscount);
                $discount = min($discount, $must_charge);
                $discount = max($discount, 0);
                
                $accumulatedDiscount += $discount;
                
                if ($isFullyPaidAgreement) {
                    $targetAccumulatedCharged = round($accumulatedMustCharge * $chargedRatio);
                    $paid = $targetAccumulatedCharged - $accumulatedCharged;
                    $paid = min($paid, $remainCharged);
                    $paid = max($paid, 0);
                    $accumulatedCharged += $paid;
                }
            } else {
                $discount = 0;
                $paid = 0;
            }
            
            $remainDiscount -= $discount;

            if (!$isFullyPaidAgreement) {
                $remain_must_charge = $must_charge - $discount;

                if ($remainCharged <= 0) {
                    $paid = 0;
                } else {
                    $paid = min($remain_must_charge, $remainCharged);
                }
            }
            
            $remainCharged -= $paid;
            
            $is_contract_fully_paid = $isFullyPaidAgreement ? true : ($paid + $discount) >= $package->must_charge;
            $contract_debt_amount = $isFullyPaidAgreement ? 0 : ($must_charge - $paid - $discount);

            $result[] = [
                'contract_id' => $package->id,
                'must_charge' => $package->must_charge,
                'total_charged' => $paid,
                'discount_amount' => $discount,
                'debt_amount' => $contract_debt_amount,
                'count_recharge' => $package->count_recharge,
                'is_fully_paid' => $is_contract_fully_paid,
                'contract_data' => $package
            ];
        }

        return [
            'total_charged' => $totalCharged,
            'total_discount' => $totalDiscount,
            'remain_charged' => max($remainCharged, 0),
            'remain_discount' => max($remainDiscount, 0),
            'packages' => $result
        ];
    }

    public function list(Request $request)
    {
        $branch_id = isset($request->branch_id) ? $request->branch_id : [];
        $keyword = isset($request->keyword) ? $request->keyword : '';
        $end_date = isset($request->end_date) ? $request->end_date : '';
        $start_date = isset($request->start_date) ? $request->start_date : '';
        $type = isset($request->type) ? $request->type : '';

        $pagination = (object) $request->pagination;
        $page = isset($pagination->cpage) ? (int) $pagination->cpage : 1;
        $limit = isset($pagination->limit) ? (int) $pagination->limit : 20;
        $offset = $page == 1 ? 0 : $limit * ($page - 1);
        $limitation = $limit > 0 ? " LIMIT $offset, $limit" : "";
        if ($type == 1) {
            $cond = " c.branch_id IN (" . Auth::user()->getBranchesHasUser() . ")";

            if (!empty($branch_id)) {
                $cond .= " AND c.branch_id IN (" . implode(",", $branch_id) . ")";
            }

            if ($keyword !== '') {
                $cond .= " AND (s.lms_code LIKE '%$keyword%' OR s.name LIKE '%$keyword%' OR c.code LIKE '%$keyword%' OR s.gud_mobile1 LIKE '%$keyword%') ";
            }
            if ($end_date !== '') {
                $cond .= " AND p.charge_date <= '$end_date'";
            }
            if ($start_date !== '') {
                $cond .= " AND p.charge_date >= '$start_date'";
            }
            $role_ids = u::query("SELECT role_id FROM role_has_user WHERE user_id = " . Auth::user()->id);
            $roles = array_map(function ($r) {
                return $r->role_id;
            }, $role_ids);
            if (in_array(68, $roles) || in_array(69, $roles)) {
                $cond .= " AND c.ec_id IN (" . Auth::user()->getStaffHasUser() . ")";
            }

            $order_by = " ORDER BY p.id DESC ";

            $total = u::first("SELECT count(p.id) AS total 
                    FROM payments AS p
                        LEFT JOIN agreements AS c ON c.id=p.agreement_id 
                        LEFT JOIN students AS s ON s.id=c.student_id WHERE $cond");

            $list = u::query("SELECT c.id AS contract_id, s.name, s.lms_code, 
                    (SELECT CONCAT(name,'-',hrm_id) FROM users WHERE id= c.ec_id) AS ec_name,
                    (SELECT CONCAT(name,'-',hrm_id) FROM users WHERE id= c.ec_leader_id) AS ec_leader_name,
                    (SELECT CONCAT(name,'-',hrm_id) FROM users WHERE id= p.creator_id) AS creator_name,
                    c.code, (SELECT name FROM tuition_fee WHERE id=c.tuition_fee_id) AS tuition_fee_name,
                    p.amount, p.must_charge, p.total, p.debt,p.charge_date, p.created_at, p.type AS payment_type, p.note
                FROM payments AS p
                    LEFT JOIN agreements AS c ON c.id=p.agreement_id
                    LEFT JOIN students AS s ON s.id=c.student_id
                WHERE $cond $order_by $limitation");
        } else {
            $cond = " c.branch_id IN (" . Auth::user()->getBranchesHasUser() . ")";

            if (!empty($branch_id)) {
                $cond .= " AND c.branch_id IN (" . implode(",", $branch_id) . ")";
            }

            if ($keyword !== '') {
                $cond .= " AND (s.lms_code LIKE '%$keyword%' OR s.name LIKE '%$keyword%' OR c.code LIKE '%$keyword%' OR s.gud_mobile1 LIKE '%$keyword%') ";
            }
            if ($end_date !== '') {
                $cond .= " AND p.charge_date <= '$end_date'";
            }
            if ($start_date !== '') {
                $cond .= " AND p.charge_date >= '$start_date'";
            }
            $role_ids = u::query("SELECT role_id FROM role_has_user WHERE user_id = " . Auth::user()->id);
            $roles = array_map(function ($r) {
                return $r->role_id;
            }, $role_ids);
            if (in_array(68, $roles) || in_array(69, $roles)) {
                $cond .= " AND t.ec_id IN (" . Auth::user()->getStaffHasUser() . ")";
            }

            $order_by = " ORDER BY p.id DESC ";

            $total = u::first("SELECT count(p.id) AS total 
                    FROM payments AS p
                        LEFT JOIN reserves AS c ON c.id=p.agreement_id 
                        LEFT JOIN students AS s ON s.id=c.student_id 
                        LEFT JOIN term_student_user AS t ON t.student_id=s.id WHERE $cond");

            $list = u::query("SELECT c.id AS contract_id, s.name, s.lms_code, 
                    (SELECT CONCAT(name,'-',hrm_id) FROM users WHERE id= t.ec_id) AS ec_name,
                    (SELECT CONCAT(name,'-',hrm_id) FROM users WHERE id= t.ec_leader_id) AS ec_leader_name,
                    (SELECT CONCAT(name,'-',hrm_id) FROM users WHERE id= p.creator_id) AS creator_name,
                    p.amount, p.must_charge, p.total, p.debt,p.charge_date, p.created_at, p.type AS payment_type, p.note
                FROM payments AS p
                    LEFT JOIN reserves AS c ON c.id=p.agreement_id
                    LEFT JOIN students AS s ON s.id=c.student_id
                    LEFT JOIN term_student_user AS t ON t.student_id=s.id 
                WHERE $cond $order_by $limitation");
        }

        $data = u::makingPagination($list, $total->total, $page, $limit);
        return response()->json($data);
    }

    public static function processC2C($stuent_id, $tuition_fee_id, $contract_id)
    {
        $stuent_info = u::first("SELECT source_id, source_detail_id, c2c_mobile, gud_mobile1 FROM students WHERE id=$stuent_id");
        if (data_get($stuent_info, 'source_id') == 3 && data_get($stuent_info, 'source_detail_id')) {
            $source_detail = u::first("SELECT campaign_id FROM source_detail WHERE id =" . data_get($stuent_info, 'source_detail_id'));
            $c2c_campaign = u::first("SELECT * FROM c2c_campaigns WHERE id=" . data_get($source_detail, 'campaign_id'));
            if (
                data_get($c2c_campaign, 'status') == 1 && in_array($tuition_fee_id, explode(',', data_get($c2c_campaign, 'list_tuition_fee')))
                && data_get($c2c_campaign, 'start_date') <= date('Y-m-d') && data_get($c2c_campaign, 'end_date') >= date('Y-m-d')
            ) {
                $meta_data = json_decode(data_get($c2c_campaign, 'meta_data'));
                $voucher_amount = 0;
                $voucher_bonus_session = 0;
                $voucher_ref_amount = 0;
                $voucher_ref_bonus_session = 0;
                foreach ($meta_data as $row) {
                    if ($tuition_fee_id == data_get($row, 'tuition_fee.id')) {
                        $voucher_amount = data_get($row, 'amount');
                        $voucher_bonus_session = data_get($row, 'bonus_session');
                        $voucher_ref_amount = data_get($row, 'ref_amount');
                        $voucher_ref_bonus_session = data_get($row, 'ref_bonus_session');
                    }
                }
                if ($voucher_amount || $voucher_bonus_session) {
                    $voucher_code = u::generateRandomAlphanumeric(6);
                    $check_exit = u::first("SELECT id FROM coupons WHERE code='$voucher_code'");
                    while (!empty($check_exit)) {
                        $voucher_code = u::generateRandomAlphanumeric(6);
                        $check_exit = u::first("SELECT id FROM coupons WHERE code='$voucher_code'");
                    }
                    u::insertSimpleRow(array(
                        'code' => $voucher_code,
                        'coupon_amount' => $voucher_amount,
                        'coupon_session' => $voucher_bonus_session,
                        'start_date' => date('Y-m-d'),
                        'end_date' => date('Y-m-d', strtotime('+1 year')),
                        'status' => 1,
                        'created_at' => date('Y-m-d H:i:s'),
                        'creator_id' => Auth::user()->id,
                        'campaign_id' => data_get($c2c_campaign, 'id'),
                        'source_id' => 3,
                        'c2c_mobile' => data_get($stuent_info, 'c2c_mobile'),
                        'c2c_contract_id' => $contract_id
                    ), 'coupons');
                }
                if ($voucher_ref_amount || $voucher_ref_bonus_session) {
                    $voucher_code = u::generateRandomAlphanumeric(6);
                    $check_exit = u::first("SELECT id FROM coupons WHERE code='$voucher_code'");
                    while (!empty($check_exit)) {
                        $voucher_code = u::generateRandomAlphanumeric(6);
                        $check_exit = u::first("SELECT id FROM coupons WHERE code='$voucher_code'");
                    }
                    u::insertSimpleRow(array(
                        'code' => $voucher_code,
                        'coupon_amount' => $voucher_amount,
                        'coupon_session' => $voucher_bonus_session,
                        'start_date' => date('Y-m-d'),
                        'end_date' => date('Y-m-d', strtotime('+1 year')),
                        'status' => 1,
                        'created_at' => date('Y-m-d H:i:s'),
                        'creator_id' => Auth::user()->id,
                        'campaign_id' => data_get($c2c_campaign, 'id'),
                        'source_id' => 3,
                        'c2c_mobile' => data_get($stuent_info, 'gud_mobile1'),
                        'c2c_contract_id' => $contract_id
                    ), 'coupons');
                }
            }
        }
        return "ok";
    }

    public function waitchargeApproveList(Request $request)
    {
        $branch_id = isset($request->branch_id) ? $request->branch_id : [];
        $keyword = isset($request->keyword) ? $request->keyword : '';
        $type = isset($request->type) ? $request->type : 1;
        $start_date = isset($request->start_date) ? $request->start_date : '';
        $end_date = isset($request->end_date) ? $request->end_date : '';
        $status = isset($request->status) ? $request->status : '';

        $pagination = (object) $request->pagination;
        $page = isset($pagination->cpage) ? (int) $pagination->cpage : 1;
        $limit = isset($pagination->limit) ? (int) $pagination->limit : 20;
        $offset = $page == 1 ? 0 : $limit * ($page - 1);
        $limitation = $limit > 0 ? " LIMIT $offset, $limit" : "";
        if ($type == 1) {

            $cond = " c.branch_id IN (" . Auth::user()->getBranchesHasUser() . ") AND tp.type=1";

            if (!empty($branch_id)) {
                $cond .= " AND c.branch_id IN (" . implode(",", $branch_id) . ")";
            }

            if ($keyword !== '') {
                $cond .= " AND (s.lms_code LIKE '%$keyword%' OR s.name LIKE '%$keyword%' OR c.code LIKE '%$keyword%' OR s.gud_mobile1 LIKE '%$keyword%') ";
            }
            if ($start_date !== '') {
                $cond .= " AND tp.charge_date >= '$start_date'";
            }
            if ($end_date !== '') {
                $cond .= " AND tp.charge_date <= '$end_date'";
            }
            if ($status !== '') {
                $cond .= " AND tp.status = " . (int) $status;
            }
            $role_ids = u::query("SELECT role_id FROM role_has_user WHERE user_id = " . Auth::user()->id);
            $roles = array_map(function ($r) {
                return $r->role_id;
            }, $role_ids);
            if (in_array(68, $roles) || in_array(69, $roles)) {
                $cond .= " AND c.ec_id IN (" . Auth::user()->getStaffHasUser() . ")";
            }
            $order_by = " ORDER BY tp.id DESC ";

            $total = u::first("SELECT count(c.id) AS total 
                FROM tmp_payments AS tp 
                    LEFT JOIN agreements AS c ON c.id = tp.agreement_id
                    LEFT JOIN students AS s ON s.id=c.student_id WHERE $cond");

            $list = u::query("SELECT c.id AS agreement_id, s.name, s.lms_code, 
                    (SELECT CONCAT(name,'-',hrm_id) FROM users WHERE id= c.ec_id) AS ec_name,
                    (SELECT CONCAT(name,'-',hrm_id) FROM users WHERE id= c.ec_leader_id) AS ec_leader_name,
                    (SELECT CONCAT(name,'-',hrm_id) FROM users WHERE id= tp.creator_id) AS creator_name,
                    (SELECT CONCAT(name,'-',hrm_id) FROM users WHERE id= tp.approver_id) AS approver_name,
                    c.code, (SELECT name FROM tuition_fee WHERE id=c.tuition_fee_id) AS tuition_fee_name,
                    tp.*
                FROM tmp_payments AS tp 
                    LEFT JOIN agreements AS c ON c.id = tp.agreement_id
                    LEFT JOIN students AS s ON s.id=c.student_id
                WHERE $cond $order_by $limitation");
        } else {
            $cond = " c.branch_id IN (" . Auth::user()->getBranchesHasUser() . ") AND tp.type=2";

            if (!empty($branch_id)) {
                $cond .= " AND c.branch_id IN (" . implode(",", $branch_id) . ")";
            }

            if ($keyword !== '') {
                $cond .= " AND (s.lms_code LIKE '%$keyword%' OR s.name LIKE '%$keyword%' OR c.code LIKE '%$keyword%' OR s.gud_mobile1 LIKE '%$keyword%') ";
            }
            if ($start_date !== '') {
                $cond .= " AND tp.charge_date >= '$start_date'";
            }
            if ($end_date !== '') {
                $cond .= " AND tp.charge_date <= '$end_date'";
            }
            if ($status !== '') {
                $cond .= " AND tp.status = " . (int) $status;
            }
            $role_ids = u::query("SELECT role_id FROM role_has_user WHERE user_id = " . Auth::user()->id);
            $roles = array_map(function ($r) {
                return $r->role_id;
            }, $role_ids);
            if (in_array(68, $roles) || in_array(69, $roles)) {
                $cond .= " AND t.ec_id IN (" . Auth::user()->getStaffHasUser() . ")";
            }

            $order_by = " ORDER BY tp.id DESC ";

            $total = u::first("SELECT count(c.id) AS total 
                FROM tmp_payments AS tp 
                    LEFT JOIN reserves AS c ON c.id = tp.agreement_id
                    LEFT JOIN students AS s ON s.id=c.student_id 
                     LEFT JOIN term_student_user AS t ON t.student_id=s.id WHERE $cond");

            $list = u::query("SELECT c.id AS agreement_id, s.name, s.lms_code, 
                    (SELECT CONCAT(name,'-',hrm_id) FROM users WHERE id= t.ec_id) AS ec_name,
                    (SELECT CONCAT(name,'-',hrm_id) FROM users WHERE id= t.ec_leader_id) AS ec_leader_name,
                    (SELECT CONCAT(name,'-',hrm_id) FROM users WHERE id= tp.creator_id) AS creator_name,
                    (SELECT CONCAT(name,'-',hrm_id) FROM users WHERE id= tp.approver_id) AS approver_name,
                    tp.*
                FROM tmp_payments AS tp 
                    LEFT JOIN reserves AS c ON c.id = tp.agreement_id
                    LEFT JOIN students AS s ON s.id=c.student_id
                    LEFT JOIN term_student_user AS t ON t.student_id=s.id
                WHERE $cond $order_by $limitation");
        }
        $data = u::makingPagination($list, $total->total, $page, $limit);
        return response()->json($data);
    }

    public function getWaitchargeApproveInfo(Request $request, $id)
    {
        $paymentInfo = u::getObject(array('id' => $id), 'tmp_payments');
        if (data_get($paymentInfo, 'type') == 1) {
            $agreementInfo = u::first("SELECT c.*,c.id AS agreement_id, s.name, s.lms_code, s.gud_name1, s.gud_mobile1, s.address, s.gud_email1,
                (SELECT name FROM branches WHERE id =c.branch_id) AS branch_name,
                (SELECT CONCAT(name,'-',hrm_id) FROM users WHERE id= c.ec_id) AS ec_name,
                (SELECT CONCAT(name,'-',hrm_id) FROM users WHERE id= c.ec_leader_id) AS ec_leader_name,
                (SELECT name FROM tuition_fee WHERE id=c.tuition_fee_id) AS tuition_fee_name,
                (SELECT CONCAT(name,'-',hrm_id) FROM users WHERE id= c.creator_id) AS creator_name
            FROM agreements AS c 
                LEFT JOIN students AS s ON s.id=c.student_id WHERE c.id=" . data_get($paymentInfo, 'agreement_id', 0));
        } else {
            $agreementInfo = u::first("SELECT c.*,c.id AS agreement_id, s.name, s.lms_code, s.gud_name1, s.gud_mobile1, s.address, s.gud_email1,
                (SELECT name FROM branches WHERE id =c.branch_id) AS branch_name,
                (SELECT CONCAT(name,'-',hrm_id) FROM users WHERE id= c.creator_id) AS creator_name
            FROM reserves AS c 
                LEFT JOIN students AS s ON s.id=c.student_id WHERE c.id=" . data_get($paymentInfo, 'agreement_id', 0));
        }
        if ($paymentInfo && isset($paymentInfo->attachments)) {
            $paymentInfo->attachments = !empty($paymentInfo->attachments) ? json_decode($paymentInfo->attachments, true) : [];
        }

        return response()->json([
            'payment_info' => $paymentInfo,
            'agreement_info' => $agreementInfo
        ]);
    }

    public function printWaitcharge(Request $request, $id)
    {
        $data = u::first("SELECT '' AS text_1,c.code AS contract_code, c.debt_amount, tp.charge_amount,tp.method,
            s.gud_name1,s.address, s.name,
            (SELECT number_of_months FROM tuition_fee WHERE id = c.tuition_fee_id) AS number_of_months,
            (SELECT name FROM products WHERe id=c.product_id) AS product_name, tp.note,
            tp.attachments,
            '' AS text_2, '' AS text_amount, '' AS text_amount_words,'' AS text_3,'' AS text_debt_amount
          FROM tmp_payments AS tp
            LEFT JOIN agreements AS c ON tp.agreement_id=c.id
            LEFT JOIN students AS s ON c.student_id = s.id
          WHERE tp.id = $id");
        $data->text_1 = "Ngày " . date('d') . " tháng " . date('m') . " năm " . date('Y');
        $data->text_2 = "Thanh toán học phí chương trình học " . $data->product_name . " " . $data->number_of_months . " tháng cho học viên " . $data->name;
        $data->text_debt_amount = number_format($data->debt_amount, 0, '', '.');
        $data->text_amount = number_format($data->charge_amount, 0, '', '.');
        $data->text_amount_words = u::convert_number_to_words($data->charge_amount) . " đồng";
        $data->text_3 = $data->method == 0 ? "Tiền mặt" : ($data->method == 2 ? "Quẹt thẻ tín dụng" : "Chuyển khoản");

        $data->attachments = !empty($data->attachments) ? json_decode($data->attachments, true) : [];

        return response()->json([
            'payment_info' => $data,
        ]);
    }

    /**
     * Kế toán áp dụng giảm trừ (discount) trước khi phê duyệt phiếu thu.
     *
     * Công thức: must_charge = total_charged + debt_amount + discount_amount
     * → debt_amount = must_charge - total_charged - discount_amount
     * must_charge KHÔNG thay đổi.
     *
     * Với combo (nhiều contracts): phân bổ discount_amount theo tỷ lệ must_charge của từng contract.
     */
    public function applyDiscount(Request $request)
    {
        $agreement_id = (int) $request->agreement_id;
        $discount_amount = (int) $request->discount_amount;
        $discount_note = trim($request->discount_note ?? '');

        if ($agreement_id <= 0) {
            return response()->json(['status' => 0, 'message' => 'Thiếu agreement_id.'], 422);
        }
        if ($discount_amount < 0) {
            return response()->json(['status' => 0, 'message' => 'Số tiền giảm trừ không được âm.'], 422);
        }

        $agreement = u::getObject(['id' => $agreement_id], 'agreements');
        if (!$agreement) {
            return response()->json(['status' => 0, 'message' => 'Không tìm thấy hợp đồng.'], 404);
        }

        $must_charge = (int) data_get($agreement, 'must_charge');
        $total_charged = (int) data_get($agreement, 'total_charged');

        // Validate: discount không được vượt quá phần còn nợ
        $max_discount = $must_charge - $total_charged;
        if ($discount_amount > $max_discount) {
            return response()->json([
                'status' => 0,
                'message' => 'Giảm trừ (' . number_format($discount_amount) . ') vượt quá công nợ hiện tại (' . number_format($max_discount) . ').'
            ], 422);
        }

        $new_debt_amount = $must_charge - $total_charged - $discount_amount;

        // ---- 1. Cập nhật agreements ----
        u::updateSimpleRow([
            'discount_amount' => $discount_amount,
            'discount_note' => $discount_note,
            'debt_amount' => $new_debt_amount,
            'updated_at' => date('Y-m-d H:i:s'),
            'updator_id' => Auth::user()->id,
        ], ['id' => $agreement_id], 'agreements');

        // ---- 2. Log agreements ----
        u::addLogAgreements($agreement_id);

        // ---- 3. Phân bổ discount_amount cho các contracts trong combo ----
        $contracts = u::query("
            SELECT id, must_charge, total_charged, debt_amount, discount_amount
            FROM contracts
            WHERE agreement_id = $agreement_id AND status > 0
            ORDER BY id ASC
        ");

        if ($contracts && count($contracts) > 0) {
            // Tổng must_charge của các contracts để tính tỷ lệ
            $total_contract_must = array_sum(array_map(function ($c) {
                return (int) $c->must_charge;
            }, $contracts));

            $allocated = 0;
            $contractCount = count($contracts);

            foreach ($contracts as $i => $contract) {
                $cid = (int) $contract->id;
                $cMust = (int) $contract->must_charge;
                $cTotalCharged = (int) $contract->total_charged;

                // Phần cuối: lấy phần dư để tránh sai số làm tròn
                if ($i === $contractCount - 1) {
                    $cDiscount = $discount_amount - $allocated;
                } else {
                    $cDiscount = $total_contract_must > 0
                        ? (int) round($discount_amount * ($cMust / $total_contract_must))
                        : 0;
                }
                $allocated += $cDiscount;

                $cDebt = $cMust - $cTotalCharged - $cDiscount;

                u::updateSimpleRow([
                    'discount_amount' => $cDiscount,
                    'discount_note' => $discount_note,
                    'debt_amount' => max(0, $cDebt),
                    'updated_at' => date('Y-m-d H:i:s'),
                ], ['id' => $cid], 'contracts');

                // Log contract
                u::addLogContracts($cid);
            }
        }

        $updatedAgreement = u::getObject(['id' => $agreement_id], 'agreements');

        return response()->json([
            'status' => 1,
            'message' => 'Áp dụng giảm trừ thành công.',
            'debt_amount' => $new_debt_amount,
            'agreement' => $updatedAgreement,
        ]);
    }
}
