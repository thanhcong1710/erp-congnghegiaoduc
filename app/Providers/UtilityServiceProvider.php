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
}
