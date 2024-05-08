<?php

namespace App\Providers;

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
			'photoURL' => "/images/avatar-s-5.jpg?99691e543d9e33cf745f6ac56f5800b8",
			'providerId' => "jwt",
			'uid' => data_get($data, 'id'),
			'address' => data_get($data, 'address'),
			'birthday' => data_get($data, 'birthday') ? date('d/m/Y', strtotime(data_get($data, 'birthday'))) : null,
			'note' => data_get($data, 'note'),
			'gender' => data_get($data, 'gender'),
		);
	}
	public static function phoneNew($number = '') {
        $resp = false;
        if ($number) {
            $resp = trim(str_replace(array('-', '.', ' '), '', (string)$number)); 
            if(substr($resp,0,2)=="84"){
                $resp = "0".substr($resp,2);
            }elseif(substr($resp,0,1)!="0"){
                $resp = "0".$resp;
            }
            // $resp = !preg_match('/(84|0[3|5|7|8|9])+([0-9]{8})\b/', $number) ? false : $resp;
            $resp = !preg_match('/(84|0[0-9])+([0-9]{8})\b/', $resp) ? false : $resp;
            $resp = strlen($resp) != 10 ? false : $resp; 
        }
        return $resp;
    }
	public static function getStatusParent($status){
        $tmp ="";
        switch ($status) {
            case 0:
                $tmp = 'KH mới';
                break;
            case 10:
                $tmp = 'KH không liên lạc được';
                break;
            case 20:
                $tmp = 'KH ở vùng CMS không có cơ sở';
                break;
            case 30:
                $tmp = 'KH không nghe máy';
                break;
            case 40:
                $tmp = 'KH hẹn gọi lại sau';
                break;
            case 50:
                $tmp = 'KH không quan tâm';
                break;
            case 60:
                $tmp = 'KH không tiềm năng';
                break;
            case 71:
                $tmp = 'KH quan tâm, cần follow up date';
                break;
            case 72:
                $tmp = 'KH tiềm năng nhưng không muốn làm phiền';
                break;
            case 73:
                $tmp = 'KH đồng ý đặt lịch Checkin';
                break;
            case 81:
                $tmp = 'KH đến hạn tái tục';
                break;
            case 82:
                $tmp = 'KH đã mua gói phí';
                break;
            case 83:
                $tmp = 'KH đến hạn tái tục';
                break;
            case 90:
                $tmp = 'Danh sách đen';
                break;
            default:
                $tmp = 'KH mới';
          }
        return $tmp;
    }

	public static function getTitleCallStatus($call_status, $call_status_sub){
        if($call_status === 0){
            return 'Blank';
        } elseif($call_status == 1){
            return 'Thuê bao - Tắt máy - Sai số';
        } elseif($call_status == 2){
            return 'Location';
        } elseif($call_status == 3){
            return 'Máy bận - Không nghe máy';
        } elseif($call_status == 4){
            return 'KH hẹn gọi lại sau';
        } elseif($call_status == 5){
            if($call_status_sub == 51){
                return 'KH đã từng sử dụng dịch vụ';
            }elseif($call_status_sub == 52){
                return 'KH không quan tâm';
            }elseif($call_status_sub == 53){
                return 'KH thực sự không muốn nói chuyện';
            }
        }elseif($call_status == 6){
            if($call_status_sub == 61){
                return 'Không có con';
            }elseif($call_status_sub == 62){
                return 'Lý do khác';
            }
        } elseif($call_status == 7){
            if($call_status_sub == 71){
                return 'KH đang cân nhắc';
            }elseif($call_status_sub == 72){
                return 'KH hẹn thời gian khác';
            }elseif($call_status_sub == 73){
                return 'KH ko muốn làm phiền';
            }elseif($call_status_sub == 74){
                return 'Confirm 1';
            }
        } elseif($call_status == 9){
            return 'Blacklist';
        }  
    }

	public static function genStatusByCallStatus($call_status, $call_status_sub){
        if($call_status == 0){
            return 0;
        } elseif($call_status == 1){
            return 10;
        } elseif($call_status == 2){
            return 20;
        } elseif($call_status == 3){
            return 30;
        } elseif($call_status == 4){
            return 40;
        } elseif($call_status == 5){
            return 50;
        }elseif($call_status == 6){
            return 60;
        }  elseif($call_status == 7){
            if($call_status_sub == 71 || $call_status_sub == 72){
                return 71;
            }elseif($call_status_sub == 73){
                return 72;
            }elseif($call_status_sub == 74){
                return 73;
            }
        } elseif($call_status == 9){
            return 90;
        } 
    }

	public static function explodeName($text){
        $data = (object)[
            'firstname'=>"",
            'midname'=>"",
            'lastname'=>"",
        ];
        $arr_text = explode(" ",$text);
        if(count($arr_text)==1){
            $data->lastname = $arr_text[0];
        }elseif(count($arr_text)==2){
            $data->firstname = $arr_text[0];
            $data->lastname = $arr_text[1];
        }elseif(count($arr_text)==3){
            $data->firstname = $arr_text[0];
            $data->lastname = $arr_text[2];
            $data->midname = $arr_text[1];
        }else{
            $data->firstname = $arr_text[0];
            $data->lastname = $arr_text[count($arr_text)-1];
            foreach($arr_text AS $k=> $row){
                if($k!=0 && $k!=count($arr_text)-1){
                    $data->midname.= $data->midname?" ".$row:$row;
                }
            }
        }
        return $data;
    }

	public static function addLogContracts($contract_id){
		$contract_info = (array)self::getObject(['id'=>$contract_id], 'contracts');
        $class_date = date('Y-m-d');
        if(data_get($contract_info, 'status') == 6){
            $schedule = self::first("SELECT c.student_id, c.branch_id, c.class_id, c.id AS contract_id, c.product_id, cl.program_id
                FROM contracts AS c 
                    LEFT JOIN classes AS cl ON cl.id = c.class_id
                    LEFT JOIN schedules AS s ON s.class_id=cl.id
                WHERE c.id=$contract_id AND s.status=1 AND s.class_date = '$class_date'");
            $schedule_has_student_info = self::first("SELECT id FROM schedule_has_student WHERE contract_id= $contract_id AND class_date='$class_date'") ;
            if($schedule){
                $reserve_info = self::first("SELECT id FROM reserves WHERE start_date <= '$class_date' AND end_date>='$class_date' AND status=2 
                    AND student_id=".data_get($schedule, 'student_id')." AND contract_id=".data_get($schedule, 'contract_id')." AND is_reserved=1");
                if($schedule_has_student_info){
                    self::updateSimpleRow(array(
                        'student_id'=> data_get($schedule, 'student_id'),
                        'branch_id'=> data_get($schedule, 'branch_id'),
                        'class_id'=> data_get($schedule, 'class_id'),
                        'product_id'=> data_get($schedule, 'product_id'),
                        'program_id'=> data_get($schedule, 'program_id'),
                        'status'=> $reserve_info ? 2 : 1,
                    ), array('id'=>data_get($schedule_has_student_info, 'id')), 'schedule_has_student');
                }else{
                    self::insertSimpleRow(array(
                        'student_id'=> data_get($schedule, 'student_id'),
                        'branch_id'=> data_get($schedule, 'branch_id'),
                        'class_id'=> data_get($schedule, 'class_id'),
                        'contract_id'=> data_get($schedule, 'contract_id'),
                        'product_id'=> data_get($schedule, 'product_id'),
                        'program_id'=> data_get($schedule, 'program_id'),
                        'class_date'=> $class_date,
                        'created_at'=> date('Y-m-d H:i:s'),
                        'status'=> $reserve_info ? 2 : 1,
                    ), 'schedule_has_student');
                }
            }else{
                if($schedule_has_student_info){
                    self::query("UPDATE schedule_has_student SET status=0 WHERE id=".(int)data_get($schedule_has_student_info,'id'));
                } 
            }
        }else{
            self::query("UPDATE schedule_has_student SET status=0 WHERE contract_id=$contract_id AND class_date='$class_date'");
        }
		$contract_info['contract_id'] = data_get($contract_info, 'id');
        $contract_info['log_time'] = date('Y-m-d H:i:s');
		unset($contract_info['id']);
		$log_contract_id = self::insertSimpleRow($contract_info, 'log_contracts');
		return $log_contract_id;
	}

	public static function formatCurrency($currency){
        return number_format($currency)."đ";
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

	public static function calculatorSessions($start, $end, $holidays = [], $classdays = [], $onlyTotal = false) {
        $resp = (Object)[
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
        $days = self::getDaysForCalcSession($startTime,$endTime, $classdays, $holidays, $onlyTotal);
        $resp->dates = $days;
        $resp->total = $onlyTotal? $days: count($days);
        $resp->end_date = $onlyTotal ? null: end($days);
        return $resp;
    }

    public static function calculatorSessionsByNumberOfSessions($start, $numberOfSessions, $holidays = [], $classdays = [], $onlyEndDate = false) {
        $startTime = strtotime(date("Y-m-d", strtotime($start)));
        if ($numberOfSessions<=0 || !$startTime || !is_array($classdays) || !count($classdays)) {
            return null;
        }
        $classdays = self::validClassdays($classdays);
        $classdays = array_values(array_sort($classdays));
        $holidays = self::stringToTimestampHolidays($holidays, $startTime, PHP_INT_MAX);
        $sessions = self::getSessionsByNumberOfSessions($startTime,$numberOfSessions, $classdays, $holidays, $onlyEndDate);
        if ($onlyEndDate) {
            return $sessions;
        }
        $resp = new \stdClass();
        $resp->dates = $sessions;
        $resp->total = count($sessions);
        $resp->end_date = end($sessions);
        return $resp;
    }

	public static function validClassdays($classdays = [])
    {
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

	public static function stringToTimestampHolidays($holidays, $startTime, $endTime) {
        if(!$holidays) return null;
        $res = [];
        foreach ($holidays as $holiday) {
            $hStart = strtotime(date("Y-m-d", strtotime($holiday->start_date)));
            $hEnd = strtotime(date("Y-m-d", strtotime($holiday->end_date)));
            $res[] = [
                'start_date' => $hStart,
                'end_date' => $hEnd,
            ];
        }
        usort($res,function($first,$second){
            return $first['start_date'] > $second['start_date'];
        });
        $res = self::mergeHolidays($res, $startTime, $endTime);
        return $res;
    }

	public static function getSessionsByNumberOfSessions ($startTime, $numberOfSessions, $classdays, $holidays, $onlyEndDate=false){
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

	public static function mergeHolidays($holidays, $pStart, $pEnd) {
        if(!$holidays || count($holidays) <= 1) return $holidays;
        $res = [];
        foreach ($holidays as $holiday) {
            if ($holiday['end_date']>= $pStart ) {
                $res[] = $holiday;
            }
        }
        return $res;
    }

	public static function checkInHolidayByTimestampBinarySearch($date, $holidays) {
        if(!$holidays) return false;
        foreach ($holidays as $holiday) {
            if ($date>=$holiday['start_date'] && $date <= $holiday['end_date']) {
                return true;
            }
        }
        return false;
    }

	public static function getDaysForCalcSession ($startTime, $endTime, $classdays, $holidays, $onlyTotal=false) {
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
                    return $onlyTotal ? $total: $days;
                }
                if (!self::checkInHolidayByTimestampBinarySearch($startTime, $holidays)) {
                    if ($onlyTotal){
                        ++$total;
                    }else {
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
        return $onlyTotal ? $total: $days;
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
            if($product === 9999){
                $products = self::query("SELECT id FROM products WHERE status = 1");
                $holidays = [];

                foreach ($products as $p){
                    $holidays[$p->id] = [];
                }

                foreach ($resp as $re){
                    $product_ids = explode(',',$re->products);
                    foreach ($holidays as $key => $holiday){
                        if(in_array($key, $product_ids)){
                            $holidays[$key][] = (Object)[
                                'start_date' => $re->start_date,
                                'end_date' => $re->end_date
                            ];
                        }
                    }
                }

                $resp = $holidays;
            }else{
                foreach ($resp as &$re){
                    $re = (Object)[
                        'start_date' => $re->start_date,
                        'end_date' => $re->end_date
                    ];
                }
                unset($re);
            }
        }
        return $resp;
    }

    public static function getClassDayText($class_day){
        $arr_day =explode(',', $class_day);
        $text = "";
        foreach($arr_day AS $day){
            $text_day = $day == 8 ? 'Chủ nhật' : 'Thứ '.$day;
            $text.= $text ? ', '.$text_day : $text_day;
        }
        return $text;
    }

    public static function sendSingleMail($to=[],$subject, $body,$arr_cc = [], $arr_att = [],$from =[]){
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
}
