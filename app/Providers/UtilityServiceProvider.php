<?php

namespace App\Providers;

use App\Models\LogStudents;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UtilityServiceProvider extends ServiceProvider
{
    public static function query($query, $print = false)
    {
        $resp = null;
        $query = trim($query);
        $upperQuery = strtoupper(substr($query, 0, 6));
        if ($print) {
            dd('\n-------------------------------------------------------------\n', $query, '\n-------------------------------------------------------------\n');
        } else {
            if ($upperQuery == ('SELECT')) {
                $resp = DB::select(DB::raw($query));
            } elseif ($upperQuery == ('INSERT')) {
                $resp = DB::insert(DB::raw($query));
            } elseif ($upperQuery == ('UPDATE')) {
                $resp = DB::update(DB::raw($query));
            } elseif ($upperQuery == ('DELETE')) {
                $resp = DB::delete(DB::raw($query));
            } else {
                $resp = DB::statement(DB::raw($query));
            }
        }
        return $resp;
    }
    public static function first($query, $print = false)
    {
        $resp = self::query($query, $print);
        return $resp && is_array($resp) && count($resp) >= 1 ? $resp[0] : $resp;
    }
    public static function getOne($query)
    {
        $finalQuery = $query . " LIMIT 1";
        $resp = DB::select(DB::raw($finalQuery));
        return $resp && is_array($resp) && count($resp) >= 1 ? $resp[0] : $resp;
    }
    public static function getObject($array_input, $table, $order_by_key = '', $order_by_desc = false)
    {
        $sub_sql = '1 ';
        $sub_order = '';
        foreach ($array_input as $key => $value) {
            $sub_sql .= " AND " . $key . "= :" . $key;
        }
        if ($order_by_key != '') {
            if ($order_by_desc) {
                $sub_order = " ORDER BY $order_by_key DESC";
            } else {
                $sub_order = " ORDER BY $order_by_key ASC";
            }
        }
        $query = "SELECT * FROM " . $table . " WHERE " . $sub_sql . $sub_order . " LIMIT 1";
        $resp = DB::select(DB::raw($query), $array_input);
        return $resp && is_array($resp) && count($resp) == 1 ? $resp[0] : $resp;
    }

    public static function getMultiObject($array_input, $table, $limit = 0, $order_by_key = '', $order_by_desc = false)
    {
        $sub_sql = '1 ';
        $sub_order = '';
        $sub_limit = '';
        foreach ($array_input as $key => $value) {
            $sub_sql .= " AND " . $key . "= :" . $key;
        }
        if ($order_by_key != '') {
            if ($order_by_desc) {
                $sub_order = " ORDER BY $order_by_key DESC";
            } else {
                $sub_order = " ORDER BY $order_by_key ASC";
            }
        }
        if ($limit) {
            $sub_limit = " LIMIT $limit";
        }
        $query = "SELECT * FROM " . $table . " WHERE " . $sub_sql . $sub_order . $sub_limit;
        $resp = DB::select(DB::raw($query), $array_input);
        return $resp;
    }

    public static function insertSimpleRow($arr_params, $table)
    {
        $field = "";
        $field_value = "";
        foreach ($arr_params as $key => $value) {
            $field .= "`" . $key . "`,";
            $field_value .= ":" . $key . ",";
        }
        $field = rtrim($field, ",");
        $field_value = rtrim($field_value, ",");
        $sql = "INSERT IGNORE INTO " . $table . "(" . $field . ") VALUES (" . $field_value . ")";
        $resp = DB::insert(DB::raw($sql), $arr_params);
        return $resp ? DB::getPdo()->lastInsertId() : $resp;
    }

    public static function updateSimpleRow($arr_params, $arr_key, $table)
    {
        $set_clause = "";
        $arr_binding = array();
        foreach ($arr_params as $key => $value) {
            $set_clause .= "`" . $key . "`= :value_" . $key . ",";
            $arr_binding['value_' . $key] = $value;
        }
        $set_clause = rtrim($set_clause, ",");

        $sql_cond = '1=1';
        foreach ($arr_key as $key => $value) {
            $sql_cond .= ' AND ' . $key . "= :key_" . $key;
            $arr_binding['key_' . $key] = $value;
        }
        if ($set_clause != '') {
            $sql = 'UPDATE ' . $table . ' SET ' . $set_clause . ' WHERE ' . $sql_cond;
            $resp = DB::update(DB::raw($sql), $arr_binding);
            return $resp;
        }
    }
    public static function makingPagination($list, $total, $page, $limit)
    {
        $pagination = (object)[];
        $data = (object)[];
        $pagination->spage = 1;
        $pagination->cpage = $page;
        $pagination->total = $total;
        $pagination->limit = $limit;
        $pagination->lpage = ($total % $limit) == 0 ? (int)($total / $limit) : (int)($total / $limit) + 1;
        $pagination->ppage = $page > 0 ? $page - 1 : 0;
        $pagination->npage = $page < $pagination->lpage ? $page + 1 : $pagination->lpage;
        $data->list = $list;
        $data->paging = $pagination;
        return $data;
    }
    public static function allLawType()
    {
        return [
            '1' => [
                'title' => 'Đất đai',
                'color' => 'primary'
            ],
            '2' => [
                'title' => 'Hình sự',
                'color' => 'secondary'
            ],
            '3' => [
                'title' => 'Dân sự',
                'color' => 'success'
            ],
            '4' => [
                'title' => 'Thừa kế - Di chúc',
                'color' => 'danger'
            ],
            '5' => [
                'title' => 'Hôn nhân gia đình',
                'color' => 'warning'
            ],
            '6' => [
                'title' => 'Bảo hiểm',
                'color' => 'info'
            ],
            '7' => [
                'title' => 'Hành chính',
                'color' => 'dark'
            ],
        ];
    }
    public static function convertPhoneNumber($phone_number)
    {
        if (substr($phone_number, 0, 2) == '84') {
            $phone_number = '0' + substr($phone_number, 2, strlen($phone_number));
        }
        return $phone_number;
    }
    public static function transformUser($data)
    {
        return array(
            'displayName' => data_get($data, 'name'),
            'name' => data_get($data, 'name'),
            'email' =>  data_get($data, 'email'),
            'phone' => data_get($data, 'phone'),
            'photoURL' => data_get($data, 'avatar_url') ? data_get($data, 'avatar_url') : "/images/avatar-default.jpg",
            'providerId' => "jwt",
            'uid' => data_get($data, 'id'),
            'address' => data_get($data, 'address'),
            'birthday' => data_get($data, 'birthday') ? date('d/m/Y', strtotime(data_get($data, 'birthday'))) : null,
            'note' => data_get($data, 'note'),
            'gender' => data_get($data, 'gender'),
        );
    }
    public static function phoneNew($number = '')
    {
        $resp = false;
        if ($number) {
            $resp = trim(str_replace(array('-', '.', ' '), '', (string)$number));
            if (substr($resp, 0, 2) == "84") {
                $resp = "0" . substr($resp, 2);
            } elseif (substr($resp, 0, 1) != "0") {
                $resp = "0" . $resp;
            }
            // $resp = !preg_match('/(84|0[3|5|7|8|9])+([0-9]{8})\b/', $number) ? false : $resp;
            $resp = !preg_match('/(84|0[0-9])+([0-9]{8})\b/', $resp) ? false : $resp;
            $resp = strlen($resp) != 10 ? false : $resp;
        }
        return $resp;
    }
    public static function getStatusParent($status)
    {
        $arr_status = array(
            '1'=>'C3 rác',
            '2'=>'C3',
            '3'=>'L1',
            '4'=>'L2',
            '5'=>'L3',
            '6'=>'R',
        );
        return $arr_status['status'] ?? '';
    }
    public static function getTitleCallStatus($call_status, $call_status_sub)
    {
        $arr_status_care = array(
            '0'=>'Khách hàng mới',
            '1'=>'Khách hàng không liên lạc được',
            '2'=>'Im lặng sau khi gửi thông tin',
            '3'=>'Hẹn gọi lại sau',
            '4'=>'Trùng lịch học/ Hiện tại đang không có nhu cầu/ Không sắp xếp được thời gian',
            '5'=>'Đang học ở bên khác',
            '6'=>'Đang cân nhắc nhưng không chia sẻ lý do',
            '7'=>'Không thuộc tệp khách hàng mục tiêu',
            '8'=>'Không có trình độ phù hợp',
            '9'=>'Từ chối không có lý do cụ thể',
            '10'=>'Tắt máy ngang',
            '11'=>'Đồng ý đến test',
            '12'=>'Hẹn lịch nhưng không đến',
            '13'=>'Danh sách đen',
        );
        return $arr_status_care['call_status'] ?? '';
    }

    public static function genStatusByCallStatus($call_status, $call_status_sub)
    {
        if ($call_status == 0) {
            return 0;
        } elseif ($call_status == 1) {
            return 10;
        } elseif ($call_status == 2) {
            return 20;
        } elseif ($call_status == 3) {
            return 30;
        } elseif ($call_status == 4) {
            return 40;
        } elseif ($call_status == 5) {
            return 50;
        } elseif ($call_status == 6) {
            return 60;
        } elseif ($call_status == 7) {
            if ($call_status_sub == 71 || $call_status_sub == 72) {
                return 71;
            } elseif ($call_status_sub == 73) {
                return 72;
            } elseif ($call_status_sub == 74) {
                return 73;
            }
        } elseif ($call_status == 9) {
            return 90;
        }
    }

    public static function explodeName($text)
    {
        $data = (object)[
            'firstname' => "",
            'midname' => "",
            'lastname' => "",
        ];
        $arr_text = explode(" ", $text);
        if (count($arr_text) == 1) {
            $data->lastname = $arr_text[0];
        } elseif (count($arr_text) == 2) {
            $data->firstname = $arr_text[0];
            $data->lastname = $arr_text[1];
        } elseif (count($arr_text) == 3) {
            $data->firstname = $arr_text[0];
            $data->lastname = $arr_text[2];
            $data->midname = $arr_text[1];
        } else {
            $data->firstname = $arr_text[0];
            $data->lastname = $arr_text[count($arr_text) - 1];
            foreach ($arr_text as $k => $row) {
                if ($k != 0 && $k != count($arr_text) - 1) {
                    $data->midname .= $data->midname ? " " . $row : $row;
                }
            }
        }
        return $data;
    }

    public static function addLogContracts($contract_id)
    {
        $contract_info = (array)self::getObject(['id' => $contract_id], 'contracts');
        $class_date = date('Y-m-d');
        if (data_get($contract_info, 'status') == 6) {
            $schedule = self::first("SELECT c.student_id, c.branch_id, c.class_id, c.id AS contract_id, c.product_id, cl.program_id,s.subject_id, s.subject_stt
                FROM contracts AS c 
                    LEFT JOIN classes AS cl ON cl.id = c.class_id
                    LEFT JOIN schedules AS s ON s.class_id=cl.id
                WHERE c.id=$contract_id AND s.status=1 AND s.class_date = '$class_date' AND c.enrolment_start_date <= '$class_date'");
            $schedule_has_student_info = self::first("SELECT id FROM schedule_has_student WHERE contract_id= $contract_id AND class_date='$class_date'");
            if ($schedule) {
                $reserve_info = self::first("SELECT id FROM reserves WHERE start_date <= '$class_date' AND end_date>='$class_date' AND status=2 
                    AND student_id=" . data_get($schedule, 'student_id') . " AND contract_id=" . data_get($schedule, 'contract_id') . " AND is_reserved=1");
                if ($schedule_has_student_info) {
                    self::updateSimpleRow(array(
                        'student_id' => data_get($schedule, 'student_id'),
                        'branch_id' => data_get($schedule, 'branch_id'),
                        'class_id' => data_get($schedule, 'class_id'),
                        'product_id' => data_get($schedule, 'product_id'),
                        'program_id' => data_get($schedule, 'program_id'),
                        'subject_id' => data_get($schedule, 'subject_id'),
                        'subject_stt' => data_get($schedule, 'subject_stt'),
                        'status' => $reserve_info ? 2 : 0,
                    ), array('id' => data_get($schedule_has_student_info, 'id')), 'schedule_has_student');
                } else {
                    self::insertSimpleRow(array(
                        'student_id' => data_get($schedule, 'student_id'),
                        'branch_id' => data_get($schedule, 'branch_id'),
                        'class_id' => data_get($schedule, 'class_id'),
                        'contract_id' => data_get($schedule, 'contract_id'),
                        'product_id' => data_get($schedule, 'product_id'),
                        'program_id' => data_get($schedule, 'program_id'),
                        'class_date' => $class_date,
                        'created_at' => date('Y-m-d H:i:s'),
                        'status' => $reserve_info ? 2 : 0,
                        'subject_id' => data_get($schedule, 'subject_id'),
                        'subject_stt' => data_get($schedule, 'subject_stt')
                    ), 'schedule_has_student');
                }
            } else {
                if ($schedule_has_student_info) {
                    self::query("DELETE FROM  schedule_has_student WHERE id=" . (int)data_get($schedule_has_student_info, 'id')." AND `status`=0");
                }
            }
        } else {
            self::query("DELETE FROM  schedule_has_student WHERE contract_id=$contract_id AND class_date='$class_date'  AND `status`=0");
        }
        $contract_info['contract_id'] = data_get($contract_info, 'id');
        $contract_info['log_time'] = date('Y-m-d H:i:s');
        self::updateDoneSessions(data_get($contract_info, 'id'));
        unset($contract_info['id']);
        $log_contract_id = self::insertSimpleRow($contract_info, 'log_contracts');
        return $log_contract_id;
    }

    public static function formatCurrency($currency)
    {
        return number_format($currency) . "đ";
    }

    public static function get_tree_data($array = [], $parent = 'parent_id', $note = 'id')
    {
        $resp = array();
        foreach ($array as $sub) {
            $resp[$sub->$parent][] = $sub;
        }
        $fnBuilder = function ($siblings) use (&$fnBuilder, $resp, $note) {
            foreach ($siblings as $k => $sibling) {
                $id = $sibling->$note;
                if (isset($resp[$id])) {
                    $sibling->icon = 'fa fa-folder-open';
                    $sibling->children = $fnBuilder($resp[$id]);
                }
                $siblings[$k] = $sibling;
            }
            return $siblings;
        };
        $tree = count($resp) > 0 && isset($resp[0]) ? $fnBuilder($resp[0]) : null;
        return $tree;
    }

    public static function calculatorSessions($start, $end, $holidays = [], $classdays = [], $onlyTotal = false)
    {
        $resp = (object)[
            "date" => [],
            "total" => 0,
            "end_date" => null
        ];
        $startTime = strtotime(date("Y-m-d", strtotime($start)));
        $endTime =   strtotime(date("Y-m-d", strtotime($end)));
        if (!$startTime || !$endTime || !is_array($classdays) || !count($classdays)) {
            return $resp;
        }
        $classdays = self::validClassdays($classdays);
        $classdays = array_values(array_sort($classdays));
        $holidays = self::stringToTimestampHolidays($holidays, $startTime, $endTime);
        if ($startTime > $endTime) {
            return $resp;
        }
        $days = self::getDaysForCalcSession($startTime, $endTime, $classdays, $holidays, $onlyTotal);
        $resp->dates = $days;
        $resp->total = $onlyTotal ? $days : count($days);
        $resp->start_date = $onlyTotal ? null : reset($days);
        $resp->end_date = $onlyTotal ? null : end($days);
        return $resp;
    }

    public static function calculatorSessionsByNumberOfSessions($start, $numberOfSessions, $holidays = [], $classdays = [], $onlyEndDate = false)
    {
        $startTime = strtotime(date("Y-m-d", strtotime($start)));
        if ($numberOfSessions <= 0 || !$startTime || !is_array($classdays) || !count($classdays)) {
            return null;
        }
        $classdays = self::validClassdays($classdays);
        $classdays = array_values(array_sort($classdays));
        $holidays = self::stringToTimestampHolidays($holidays, $startTime, PHP_INT_MAX);
        $sessions = self::getSessionsByNumberOfSessions($startTime, $numberOfSessions, $classdays, $holidays, $onlyEndDate);
        if ($onlyEndDate) {
            return $sessions;
        }
        $resp = new \stdClass();
        $resp->dates = $sessions;
        $resp->total = count($sessions);
        $resp->start_date = reset($sessions);
        $resp->end_date = end($sessions);
        return $resp;
    }

    public static function validClassdays($classdays = [])
    {
        foreach($classdays AS $k => $row){
            if($row==8){
                $classdays[$k] = 0;
            }else{
                $classdays[$k] = $row-1;
            }
        }
        $resp = count($classdays) ? $classdays : [2, 5];
        if (count($resp)) {
            $resp = array_unique($resp);
            sort($resp);
            if ($resp[0] == 0) {
                array_shift($resp);
                $resp[] = 0;
            }
        }
        return $resp;
    }

    public static function stringToTimestampHolidays($holidays, $startTime, $endTime)
    {
        if (!$holidays) return null;
        $res = [];
        foreach ($holidays as $holiday) {
            $hStart = strtotime(date("Y-m-d", strtotime($holiday->start_date)));
            $hEnd = strtotime(date("Y-m-d", strtotime($holiday->end_date)));
            $res[] = [
                'start_date' => $hStart,
                'end_date' => $hEnd,
            ];
        }
        usort($res, function ($first, $second) {
            return $first['start_date'] > $second['start_date'];
        });
        $res = self::mergeHolidays($res, $startTime, $endTime);
        return $res;
    }

    public static function getSessionsByNumberOfSessions($startTime, $numberOfSessions, $classdays, $holidays, $onlyEndDate = false)
    { 
        $weekday = (int) date('N', $startTime);
        if ($weekday === 7) {
            $weekday = 0;
        }
        $timeOfDay = 24 * 60 * 60;
        $maxLength = count($classdays) - 1;
        $days = [];
        while ($numberOfSessions >= 0) {
            foreach ($classdays as $key => $classday) {
                if ($weekday > $classday) {
                    if ($key >= $maxLength) {
                        $startTime += (7 - $weekday) * $timeOfDay;
                        $weekday = 0;
                    }
                    continue;
                }
                $startTime += ($classday - $weekday) * $timeOfDay;
                if($numberOfSessions<=0){
                    if($onlyEndDate){
                        $l = count($days);
                        return $l> 0 ? $days[$l - 1] : null;
                    }
                    return $days;
                }
                if (!self::checkInHolidayByTimestampBinarySearch($startTime, $holidays)) {
                    $days[] = date("Y-m-d", $startTime);
                    --$numberOfSessions;
                }
                $weekday = $classday;
                if ($key >= $maxLength) {
                    $weekday = 0;
                    $startTime += (7 - $classday) * $timeOfDay;
                }
            }
        }
        if ($onlyEndDate) {
            $l = count($days);
            return $l> 0 ? $days[$l - 1] : null;
        }
        return $days;
    }

    public static function mergeHolidays($holidays, $pStart, $pEnd)
    {
        if (!$holidays || count($holidays) <= 1) return $holidays;
        $res = [];
        foreach ($holidays as $holiday) {
            if ($holiday['end_date'] >= $pStart) {
                $res[] = $holiday;
            }
        }
        return $res;
    }

    public static function checkInHolidayByTimestampBinarySearch($date, $holidays)
    {
        if (!$holidays) return false;
        foreach ($holidays as $holiday) {
            if ($date >= $holiday['start_date'] && $date <= $holiday['end_date']) {
                return true;
            }
        }
        return false;
    }

    public static function getDaysForCalcSession($startTime, $endTime, $classdays, $holidays, $onlyTotal = false)
    {
        $weekday = (int) date('N', $startTime);
        if ($weekday === 7) {
            $weekday = 0;
        }
        $timeOfDay = 24 * 60 * 60;
        $maxLength = count($classdays) - 1;
        $days = [];
        $total = 0;
        while ($startTime <= $endTime) {
            foreach ($classdays as $key => $classday) {
                if ($weekday > $classday) {
                    if ($key >= $maxLength) {
                        $startTime += (7 - $weekday) * $timeOfDay;
                        $weekday = 0;
                    }
                    continue;
                }
                $startTime += ($classday - $weekday) * $timeOfDay;
                if ($startTime > $endTime) {
                    return $onlyTotal ? $total : $days;
                }
                if (!self::checkInHolidayByTimestampBinarySearch($startTime, $holidays)) {
                    if ($onlyTotal) {
                        ++$total;
                    } else {
                        $days[] = date("Y-m-d", $startTime);
                    }
                }
                $weekday = $classday;
                if ($key >= $maxLength) {
                    $weekday = 0;
                    $startTime += (7 - $classday) * $timeOfDay;
                }
            }
        }
        return $onlyTotal ? $total : $days;
    }

    public static function getPublicHolidays($branch_id = 0, $product = 0)
    {
        $resp = [];
        $where = ($product && $product !== 9999) ? "AND (h.products LIKE '$product,%' OR h.products LIKE '%,$product' OR h.products LIKE '%,$product,%' OR h.products = '$product') AND h.`status` > 0" : ' AND h.`status` > 0 ';

        $resp = self::query("SELECT h.start_date, h.end_date, h.products FROM public_holiday AS h
						WHERE ( h.branch_id LIKE '$branch_id,%' OR h.branch_id LIKE '%,$branch_id,%' OR h.branch_id LIKE '%,$branch_id' OR h.branch_id = '$branch_id' ) $where");
        if (count($resp)) {
            usort($resp, function ($a, $b) {
                return strcmp($a->start_date, $b->start_date);
            });
            if ($product === 9999) {
                $products = self::query("SELECT id FROM products WHERE status = 1");
                $holidays = [];

                foreach ($products as $p) {
                    $holidays[$p->id] = [];
                }

                foreach ($resp as $re) {
                    $product_ids = explode(',', $re->products);
                    foreach ($holidays as $key => $holiday) {
                        if (in_array($key, $product_ids)) {
                            $holidays[$key][] = (object)[
                                'start_date' => $re->start_date,
                                'end_date' => $re->end_date
                            ];
                        }
                    }
                }

                $resp = $holidays;
            } else {
                foreach ($resp as &$re) {
                    $re = (object)[
                        'start_date' => $re->start_date,
                        'end_date' => $re->end_date
                    ];
                }
                unset($re);
            }
        }
        return $resp;
    }

    public static function getClassDayText($class_day)
    {
        $arr_day = explode(',', $class_day);
        $text = "";
        foreach ($arr_day as $day) {
            $text_day = $day == 8 ? 'Chủ nhật' : 'Thứ ' . $day;
            $text .= $text ? ', ' . $text_day : $text_day;
        }
        return $text;
    }

    public static function sendSingleMail($to = [], $subject, $body, $arr_cc = [], $arr_att = [], $from = [])
    {
        DB::table('email_queues')->insert([
            'email_from' => json_encode($from),
            'email_to' => json_encode($to),
            'email_subject' => $subject,
            'email_body' => $body,
            'email_cc' => json_encode($arr_cc),
            'email_attack' => json_encode($arr_att),
            'created_at' => date('Y-m-d H:i:s'),
        ]);
        return true;
    }
    public static function formatDateView($date)
    {
        $weekday = date("l", strtotime($date));
        $weekday = strtolower($weekday);
        switch ($weekday) {
            case 'monday':
                $weekday = 'T2';
                break;
            case 'tuesday':
                $weekday = 'T3';
                break;
            case 'wednesday':
                $weekday = 'T4';
                break;
            case 'thursday':
                $weekday = 'T5';
                break;
            case 'friday':
                $weekday = 'T6';
                break;
            case 'saturday':
                $weekday = 'T7';
                break;
            default:
                $weekday = 'CN';
                break;
        }
        return $weekday . ", " . date('d/m/Y', strtotime($date));
    }

    public static function updateDoneSessions($contract_id)
    {
        $done_sessions = self::first("SELECT count(id) AS total FROM schedule_has_student WHERE contract_id = $contract_id AND status=1 ");
        $contract_info = self::first("SELECT id, product_id, branch_id, class_id, `status`, enrolment_start_date, summary_sessions, student_id, code FROM contracts WHERE id=$contract_id");
        if($contract_info){
            if ($contract_info->status == 6) {
                $holidays = self::getPublicHolidays(data_get($contract_info, 'branch_id'), data_get($contract_info, 'product_id'));
                $reserved_dates = self::getReservedDates_transfer($contract_id);
                if (!empty($reserved_dates)) {
                    $holidays = array_merge($holidays, $reserved_dates);
                }
                $class_info = self::first("SELECT class_day FROM classes WHERE id=$contract_info->class_id");
                $arr_day = explode(",", data_get($class_info, 'class_day'));
                $left_sessions = $contract_info->summary_sessions - $done_sessions->total;
                $data_sessions = self::calculatorSessionsByNumberOfSessions(data_get($contract_info, 'enrolment_start_date'), $contract_info->summary_sessions, $holidays, $arr_day);
                self::updateSimpleRow(array(
                    'enrolment_last_date' => data_get($data_sessions, 'end_date'),
                    'done_sessions' => $done_sessions->total,
                    'left_sessions' => $left_sessions
                ), array('id' => $contract_id), 'contracts');
            } else {
                $left_sessions = $contract_info->summary_sessions - $done_sessions->total;
                self::updateSimpleRow(array(
                    'done_sessions' => $done_sessions->total,
                    'left_sessions' => $left_sessions
                ), array('id' => $contract_id), 'contracts');
            }
        }
        // if($contract_info->status != 7 && $left_sessions == 0 && data_get($contract_info,'summary_sessions')>0){
        //     self::updateSimpleRow(array(
        //         'status' => 7,
        //         'action' => 'Tự động withdraw do hết phí'
        //     ), array('id' => $contract_id), 'contracts');
        //     self::addLogContracts($contract_id);
        //     LogStudents::logAdd(data_get($contract_info, 'student_id'), "Tự động withdraw học sinh khỏi lớp do hợp đồng " .data_get($contract_info, 'code')." hết phí", 0);
        // }
        return true;
    }

    public static function genStatusStudent($status, $type)
    {
        $text = "";
        if ($type == 1) {
            if ($status == 6) {
                $text = "Đang học chính thức";
            } elseif ($status == 7) {
                $text = "Hết phí";
            } elseif (in_array($status, [3, 4, 5])) {
                $text = "Chờ xếp lớp";
            } elseif ($status == 2) {
                $text = "Đặt cọc";
            } elseif ($status == 1) {
                $text = "Chưa đóng phí";
            }
        } else {
            if ($status == 6) {
                $text = "Đang học thử";
            } elseif ($status == 7) {
                $text = "Kết thúc học thử";
            } else {
                $text = "Chờ xếp lớp học thử";
            }
        }
        return $text;
    }

    public static function getPermissions($user_id){
        $permissions = self::query("SELECT DISTINCT p.name , p.group_id
            FROM role_has_user AS ru 
                LEFT JOIN permission_has_role AS pr ON pr.role_id=ru.role_id
                LEFT JOIN permissions AS p ON p.id=pr.permission_id
            WHERE ru.user_id = $user_id");
        $arr = [];
        $arr_group = [0];
        foreach($permissions AS $p){
            $arr[] = $p->name;
            if(!in_array($p->group_id,$arr_group)){
                $arr_group[] = $p->group_id;
            }
        }
        $permission_groups = self::query("SELECT DISTINCT g.name FROM permission_groups AS g WHERE g.id IN(".implode(",",$arr_group).")");
        foreach($permission_groups AS $p){
            $arr[] = $p->name;
        }
        return $arr;
    }

    public static function calcTransferTuitionFeeForTuitionTransfer($from_tuition_fee_id, $transfer_amount, $to_branch_id, $to_product_id, $transfer_session)
    {
        $resp = (object)[];
        if ($from_tuition_fee_id) {
            $available_tuiotion_fee_ids = self::query("SELECT exchange_tuition_fee_id FROM tuition_fee_relation WHERE tuition_fee_id = $from_tuition_fee_id AND status = 1");
            if (count($available_tuiotion_fee_ids)) {
                $available_ids = [];
                foreach ($available_tuiotion_fee_ids as $id) {
                    $available_ids[] = (int)$id->exchange_tuition_fee_id;
                }
                $available_ids = implode(',', $available_ids);
                $to_tuition_fee = self::first("SELECT t.*, p.name AS product_name 
                    FROM tuition_fee AS t LEFT JOIN products AS p ON t.product_id = p.id 
                    WHERE t.product_id = $to_product_id AND (t.branch_id LIKE '%,$to_branch_id' OR t.branch_id LIKE '%,$to_branch_id,%' OR t.branch_id LIKE '$to_branch_id,%' OR t.branch_id = '$to_branch_id') 
                        AND t.id IN ($available_ids)");
                if($to_tuition_fee){
                    // $resp->sessions = ceil($transfer_amount / ( $to_tuition_fee->price / $to_tuition_fee->session));
                    //Quy đổi ngang số buổi
                    $resp->sessions = $transfer_session;
                    $resp->receive_tuition_fee = $to_tuition_fee;
                    $resp->transfer_amount = $transfer_amount;
                }
            }
        }
        return $resp;
    }

    public static function update_file_name($file) 
	{
	  $pos = strrpos($file,'.');
	  $ext = substr($file,$pos); 
	  $dir = strrpos($file,'/');
	  $dr  = substr($file,0,($dir+1)); 
  
	  $arr = explode('/',$file);
	  $fName = self::convert_slug(trim($arr[(count($arr) - 1)],$ext));
  
	  $exist = FALSE;
	  $i = 0;
	  
	  while(!$exist)
	  {
		$file = $i > 0 ? $dr.$fName.'_'.$i.$ext : $dr.$fName.$ext;
		
		if(!file_exists($file))
		  $exist = TRUE;
		
		$i++;
	  }
  
	  return $file;
	}

    public static function convert_slug($str) {
		
        $str = trim(mb_strtolower($str));
		$str = preg_replace('/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/', 'a', $str);
		$str = preg_replace('/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/', 'e', $str);
		$str = preg_replace('/(ì|í|ị|ỉ|ĩ)/', 'i', $str);
		$str = preg_replace('/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/', 'o', $str);
		$str = preg_replace('/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/', 'u', $str);
		$str = preg_replace('/(ỳ|ý|ỵ|ỷ|ỹ)/', 'y', $str);
		$str = preg_replace('/(đ)/', 'd', $str);
		$str = preg_replace('/[^a-z0-9-\s]/', '', $str);
		$str = preg_replace('/([\s]+)/', '_', $str);
		return $str;
    }

    public static function generateRandomAlphanumeric($length = 10) {
        // Define the characters to be used
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        
        // Generate random string
        for ($i = 0; $i < $length; $i++) {
            $randomIndex = rand(0, $charactersLength - 1); // Generate random index
            $randomString .= $characters[$randomIndex];   // Append character
        }
        
        return $randomString;
    }

    public static function convert_number_to_words( $number ){
        $number = str_replace([',', '.'], '', $number);
        $number = (int)$number;
    
        if ($number === 0) {
            return 'Không đồng';
        }
    
        $words = [
            0 => 'không',
            1 => 'một',
            2 => 'hai',
            3 => 'ba',
            4 => 'bốn',
            5 => 'năm',
            6 => 'sáu',
            7 => 'bảy',
            8 => 'tám',
            9 => 'chín',
        ];
    
        $units = ['', 'nghìn', 'triệu', 'tỷ', 'nghìn tỷ', 'triệu tỷ'];
    
        $chunks = array_reverse(str_split(str_pad($number, ceil(strlen($number)/3)*3, '0', STR_PAD_LEFT), 3));
        $result = [];
        $hasNonZeroChunk = false;
    
        foreach ($chunks as $i => $chunk) {
            $hundreds = (int)$chunk[0];
            $tens = (int)$chunk[1];
            $unitsDigit = (int)$chunk[2];
    
            $chunkWords = [];
    
            // Trăm
            if ($hundreds > 0) {
                $chunkWords[] = $words[$hundreds] . ' trăm';
            } elseif (($tens > 0 || $unitsDigit > 0) && $i != count($chunks) - 1) {
                $chunkWords[] = 'không trăm';
            }
    
            // Chục
            if ($tens == 0 && $unitsDigit != 0) {
                if (!empty($chunkWords)) {
                    $chunkWords[] = 'lẻ';
                }
            } elseif ($tens == 1) {
                $chunkWords[] = 'mười';
            } elseif ($tens > 1) {
                $chunkWords[] = $words[$tens] . ' mươi';
            }
    
            // Đơn vị
            if ($unitsDigit > 0) {
                if ($tens == 0 || $tens == 1) {
                    if ($unitsDigit == 5 && $tens >= 1) {
                        $chunkWords[] = 'lăm';
                    } else {
                        $chunkWords[] = $words[$unitsDigit];
                    }
                } else {
                    if ($unitsDigit == 1) {
                        $chunkWords[] = 'mốt';
                    } elseif ($unitsDigit == 5) {
                        $chunkWords[] = 'lăm';
                    } else {
                        $chunkWords[] = $words[$unitsDigit];
                    }
                }
            }
    
            if (!empty(array_filter([$hundreds, $tens, $unitsDigit]))) {
                $result[] = implode(' ', $chunkWords) . ($units[$i] ? ' ' . $units[$i] : '');
                $hasNonZeroChunk = true;
            }
        }
    
        $final = implode(' ', array_reverse($result));
        $final = preg_replace('/\s+/', ' ', trim($final));
    
        return ucfirst($final) ." đồng";
    }

    public static function processDataContractsPast($contract_id, $process_date)
    {
        $contract_info = (array)self::getObject(['id' => $contract_id], 'contracts');
        $curr_date = date('Y-m-d');
        if (data_get($contract_info, 'status') == 6) {
            $schedules = self::query("SELECT c.student_id, c.branch_id, c.class_id, c.id AS contract_id, c.product_id, cl.program_id,s.subject_id, s.subject_stt, s.class_date
                FROM contracts AS c 
                    LEFT JOIN classes AS cl ON cl.id = c.class_id
                    LEFT JOIN schedules AS s ON s.class_id=cl.id
                WHERE c.id=$contract_id AND s.status=1 AND s.class_date < '$curr_date' AND s.class_date >= '$process_date'");
            foreach($schedules AS $schedule){
                $class_date = data_get($schedule, 'class_date');
                $schedule_has_student_info = self::first("SELECT id FROM schedule_has_student WHERE contract_id= $contract_id AND class_date='$class_date'");
                $reserve_info = self::first("SELECT id FROM reserves WHERE start_date <= '$class_date' AND end_date>='$class_date' AND status=2 
                    AND student_id=" . data_get($schedule, 'student_id') . " AND contract_id=" . data_get($schedule, 'contract_id') . " AND is_reserved=1");
                if ($schedule_has_student_info) {
                    self::updateSimpleRow(array(
                        'student_id' => data_get($schedule, 'student_id'),
                        'branch_id' => data_get($schedule, 'branch_id'),
                        'class_id' => data_get($schedule, 'class_id'),
                        'product_id' => data_get($schedule, 'product_id'),
                        'program_id' => data_get($schedule, 'program_id'),
                        'subject_id' => data_get($schedule, 'subject_id'),
                        'subject_stt' => data_get($schedule, 'subject_stt'),
                        'status' => $reserve_info ? 2 : 1,
                    ), array('id' => data_get($schedule_has_student_info, 'id')), 'schedule_has_student');
                } else {
                    self::insertSimpleRow(array(
                        'student_id' => data_get($schedule, 'student_id'),
                        'branch_id' => data_get($schedule, 'branch_id'),
                        'class_id' => data_get($schedule, 'class_id'),
                        'contract_id' => data_get($schedule, 'contract_id'),
                        'product_id' => data_get($schedule, 'product_id'),
                        'program_id' => data_get($schedule, 'program_id'),
                        'class_date' => $class_date,
                        'created_at' => date('Y-m-d H:i:s'),
                        'status' => $reserve_info ? 2 : 1,
                        'subject_id' => data_get($schedule, 'subject_id'),
                        'subject_stt' => data_get($schedule, 'subject_stt')
                    ), 'schedule_has_student');
                }
            }
        }
        self::updateDoneSessions(data_get($contract_info, 'id'));
        return true;
    }

    public static function convertDayToInt($day)
    {
        $weekday = strtolower($day);
        switch ($weekday) {
            case 'monday':
                $weekday = '2';
                break;
            case 'tuesday':
                $weekday = '3';
                break;
            case 'wednesday':
                $weekday = '4';
                break;
            case 'thursday':
                $weekday = '5';
                break;
            case 'friday':
                $weekday = '6';
                break;
            case 'saturday':
                $weekday = '7';
                break;
            default:
                $weekday = '8';
                break;
        }
        return $weekday;
    }
    public static function logRequest($url,$method,$header,$body,$response,$table){
        self::insertSimpleRow(array(
            'url'=>$url,
            'method'=>$method,
            'header'=>is_string($header) ? $header : json_encode($header),
            'body'=>is_string($body) ? $body :json_encode($body),
            'response'=>is_string($response) ? $response :json_encode($response),
            'created_at'=>date('Y-m-d H:i:s')
        ),$table);
        return true;
    }

    public static function updateScheduleHasStudent($contract_id){
        $contractInfo = self::getObject(array('id'=>$contract_id),'contracts');
        if(data_get($contractInfo, 'status') ==6 && data_get($contractInfo, 'class_id')){
            $enrolment_start_date = data_get($contractInfo, 'enrolment_start_date');
            $currDate = date('Y-m-d');
            if( $enrolment_start_date < $currDate){
                $class_info = self::getObject(array('id'=>data_get($contractInfo,'class_id')), 'classes');
                $arr_day = explode(',', $class_info->class_day);
                $holidays = self::getPublicHolidays(data_get($contractInfo,'branch_id'), data_get($contractInfo,'product_id'));
                $data_sessions = self::calculatorSessions($enrolment_start_date, $currDate, $holidays, $arr_day);
                $student_id= data_get($contractInfo,'student_id');
                if(!empty(data_get($data_sessions, 'dates'))){
                    foreach(data_get($data_sessions, 'dates') AS $row){
                        $checkExit = self::first("SELECT id FROM schedule_has_student WHERE student_id = $student_id AND class_date = '$row'");
                        if(!$checkExit){
                            self::insertSimpleRow(array(
                                'student_id'=>$student_id,
                                'branch_id'=>data_get($contractInfo, 'branch_id'),
                                'class_id'=>data_get($contractInfo, 'class_id'),
                                'contract_id'=>data_get($contractInfo, 'id'),
                                'product_id'=>data_get($contractInfo, 'product_id'),
                                'program_id'=>data_get($contractInfo, 'program_id'),
                                'class_date'=>$row,
                                'created_at'=>date('Y-m-d H:i:s'),
                                'status'=>1
                            ), 'schedule_has_student');
                            self::query("UPDATE schedule_has_student AS s SET s.status=2 WHERE s.class_date = '$row' 
                                AND (SELECT count(id) FROM reserves WHERE start_date <= '$row' AND end_date>='$row' AND status=4 AND student_id=s.student_id AND contract_id=s.contract_id AND is_reserved=1)>0");
                        }
                    }
                }

                self::updateDoneSessions(data_get($contractInfo, 'id'));
            }
        }elseif(data_get($contractInfo, 'status') !=7){
            self::query("UPDATE schedule_has_student AS s SET s.status=2 WHERE s.contract_id = $contract_id AND
                (SELECT count(id) FROM reserves WHERE start_date <= s.class_date AND end_date>=s.class_date AND status=4 AND student_id=s.student_id AND contract_id=s.contract_id)>0");
            self::updateDoneSessions(data_get($contractInfo, 'id'));
        }
    }

    public static function updateEnrolmentLastDate($contract_id) {
        $contract_info = self::first("SELECT id, product_id, branch_id, class_id, `status`, enrolment_start_date, summary_sessions, student_id, code FROM contracts WHERE id=$contract_id");
        $holidays = self::getPublicHolidays(data_get($contract_info, 'branch_id'), data_get($contract_info, 'product_id'));
        $class_info = self::first("SELECT class_day FROM classes WHERE id=$contract_info->class_id");
        $arr_day = explode(",", data_get($class_info, 'class_day'));
        $reserved_dates = self::getReservedDates_transfer($contract_id);
        if (!empty($reserved_dates)) {
            $holidays = array_merge($holidays, $reserved_dates);
        };
        $data_sessions = self::calculatorSessionsByNumberOfSessions(data_get($contract_info, 'enrolment_start_date'), $contract_info->summary_sessions, $holidays, $arr_day);
        self::updateSimpleRow(array(
            'enrolment_last_date' => data_get($data_sessions, 'end_date'),
        ), array('id' => $contract_id), 'contracts');
    }

    public static function getReservedDates_transfer($contract_id)
    {
        $res = [];
        if ($contract_id) {
            $query = "SELECT r.contract_id, r.start_date, r.end_date, r.session FROM `reserves` AS r WHERE r.status = 4 AND r.contract_id =$contract_id ";
            $data = self::query($query);

            if (!empty($data)) {
                foreach ($data as $da) {
                $res[] = (object)['start_date' => $da->start_date, 'end_date' => $da->end_date, 'sessions' => $da->session];
                }
            }
        }

        return $res;
    }
    public static function getBranchIdByUserID ($user_id){
        $list_branches = self::query("SELECT u.branch_id FROM branch_has_user AS u WHERE u.user_id = ".$user_id);
        return $list_branches;
    }
}
