<?php

namespace App\Http\Controllers;

use App\Providers\UtilityServiceProvider as u;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as x;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Import khách hàng từ file Excel.
 * crm_imports.status: 0 = đã kiểm tra, chờ phân chia | 1 = hoàn thành
 * crm_import_parents.status: 1 hợp lệ | 2 dữ liệu lỗi | 3 trùng trong file | 4 trùng KH đang chăm sóc | 6 đã import
 * Mỗi người chỉ xem / thao tác được các lượt import do chính mình tạo.
 */
class ImportsController extends Controller
{
    const MAX_ROWS = 10000;
    const CHUNK = 1000;

    private static function esc($v)
    {
        return addslashes(trim(str_replace(["\\"], '', (string)$v)));
    }

    /** Lấy lượt import thuộc user hiện tại, null nếu không có quyền */
    private function ownImport($import_id)
    {
        $import_id = (int)$import_id;
        if ($import_id <= 0) {
            return null;
        }
        return u::first("SELECT * FROM crm_imports WHERE id = $import_id AND creator_id = " . (int)Auth::user()->id);
    }

    public function list(Request $request)
    {
        $user_id = (int)Auth::user()->id;
        $status = isset($request->status) ? (string)$request->status : '-1';

        $pagination = (object)$request->pagination;
        $page = isset($pagination->cpage) ? max(1, (int)$pagination->cpage) : 1;
        $limit = isset($pagination->limit) ? (int)$pagination->limit : 20;
        $limit = $limit > 0 ? min($limit, 200) : 20;
        $offset = $limit * ($page - 1);

        // Chỉ hiển thị lượt import do chính người dùng tạo
        $cond = " i.creator_id = $user_id ";
        if ($status !== '-1' && $status !== '') {
            $cond .= " AND i.status = " . (int)$status;
        }
        if ($request->keyword) {
            $cond .= " AND i.file_name LIKE '%" . self::esc($request->keyword) . "%'";
        }
        if ($request->from_date && strtotime($request->from_date)) {
            $cond .= " AND i.created_at >= '" . date('Y-m-d 00:00:00', strtotime($request->from_date)) . "'";
        }
        if ($request->to_date && strtotime($request->to_date)) {
            $cond .= " AND i.created_at <= '" . date('Y-m-d 23:59:59', strtotime($request->to_date)) . "'";
        }

        $total = u::first("SELECT count(i.id) AS total FROM crm_imports AS i WHERE $cond");
        $list = u::query("SELECT i.id, i.file_name, i.file_link, i.status, i.created_at,
                u.name AS creator_name,
                IFNULL(c.total, 0) AS count_total,
                IFNULL(c.success, 0) AS count_success,
                IFNULL(c.valid, 0) AS count_valid,
                IFNULL(c.overwrite, 0) AS count_overwrite,
                IFNULL(c.total, 0) - IFNULL(c.success, 0) AS count_error,
                (SELECT b.name FROM crm_import_parents AS ip JOIN branches AS b ON b.id = ip.branch_id WHERE ip.import_id = i.id LIMIT 1) AS branch_name
            FROM crm_imports AS i
                LEFT JOIN users AS u ON u.id = i.creator_id
                LEFT JOIN (
                    SELECT import_id, count(id) AS total,
                        SUM(status = 6) AS success,
                        SUM(status = 1) AS valid,
                        SUM(status = 4 AND is_lock = 0) AS overwrite
                    FROM crm_import_parents GROUP BY import_id
                ) AS c ON c.import_id = i.id
            WHERE $cond ORDER BY i.id DESC LIMIT $offset, $limit");

        $data = u::makingPagination($list, $total->total, $page, $limit);
        $data->summary = u::first("SELECT count(i.id) AS total_import,
                SUM(i.status = 0) AS total_pending,
                (SELECT count(p.id) FROM crm_import_parents AS p JOIN crm_imports AS i2 ON i2.id = p.import_id
                    WHERE i2.creator_id = $user_id AND p.status = 6) AS total_customer
            FROM crm_imports AS i WHERE i.creator_id = $user_id");
        return response()->json($data);
    }

    public function upload(Request $request)
    {
        $user_id = (int)$request->user()->id;
        $branch_id = (int)$request->branch_id;
        $attachedFile = (string)$request->input('files');

        if ($branch_id <= 0) {
            return response()->json(['error' => true, 'message' => 'Vui lòng chọn trung tâm nhận dữ liệu']);
        }
        $allowed = array_map('intval', explode(',', (string)Auth::user()->getBranchesHasUser()));
        if (!in_array($branch_id, $allowed)) {
            return response()->json(['error' => true, 'message' => 'Bạn không thuộc trung tâm đã chọn']);
        }
        $parts = explode(',', $attachedFile, 2);
        if (count($parts) < 2 || strpos($parts[0], 'spreadsheetml') === false) {
            return response()->json(['error' => true, 'message' => 'File không hợp lệ, chỉ chấp nhận file Excel .xlsx theo mẫu']);
        }
        $binary = base64_decode($parts[1]);
        if (!$binary) {
            return response()->json(['error' => true, 'message' => 'Không đọc được nội dung file']);
        }

        $stored = md5($user_id . microtime(true) . mt_rand()) . '.xlsx';
        $p = public_path('static/upload/' . $stored);
        file_put_contents($p, $binary);

        try {
            $reader = new x();
            $reader->setReadDataOnly(true);
            $sheet = $reader->load($p)->setActiveSheetIndex(0);
            $rows = $sheet->toArray();
        } catch (\Throwable $e) {
            @unlink($p);
            return response()->json(['error' => true, 'message' => 'File Excel bị lỗi hoặc không đúng định dạng']);
        }
        array_shift($rows); // bỏ dòng tiêu đề
        // Bỏ các dòng trống hoàn toàn (Excel hay để thừa dòng định dạng)
        $rows = array_values(array_filter($rows, function ($r) {
            return is_array($r) && trim(implode('', array_map('strval', array_slice($r, 0, 13)))) !== '';
        }));
        if (!count($rows)) {
            @unlink($p);
            return response()->json(['error' => true, 'message' => 'File không có dữ liệu khách hàng']);
        }
        if (count($rows) > self::MAX_ROWS) {
            @unlink($p);
            return response()->json(['error' => true, 'message' => 'Mỗi lần import tối đa ' . number_format(self::MAX_ROWS) . ' khách hàng (file có ' . number_format(count($rows)) . ' dòng)']);
        }

        $origin = trim(preg_replace('/[\\\\\/:*?"<>|]/', '', (string)$request->file_name));
        $import_id = u::insertSimpleRow([
            'file_name' => mb_substr($origin ?: $stored, 0, 250),
            'file_link' => 'static/upload/' . $stored,
            'status' => 0,
            'created_at' => date('Y-m-d H:i:s'),
            'creator_id' => $user_id,
        ], 'crm_imports');

        $this->addItemDataImport($rows, $import_id, $user_id, $branch_id);
        $this->processCheckDuplicateData($import_id);
        u::query("UPDATE crm_import_parents AS p LEFT JOIN users AS u ON u.hrm_id = p.owner_hrm SET p.owner_id = u.id WHERE p.import_id = $import_id AND p.owner_hrm IS NOT NULL AND p.owner_hrm != ''");

        $data = $this->checkResult($import_id);
        $data->error = false;
        $data->message = 'Kiểm tra file thành công!';
        $data->import_id = $import_id;
        $data->branch_id = $branch_id;
        return response()->json($data);
    }

    /** Xem lại kết quả kiểm tra của 1 lượt import (để tiếp tục phân chia hoặc xem chi tiết) */
    public function detail(Request $request)
    {
        $import = $this->ownImport($request->import_id);
        if (!$import) {
            return response()->json(['error' => true, 'message' => 'Không tìm thấy lượt import hoặc bạn không có quyền xem']);
        }
        $data = $this->checkResult($import->id);
        $branch = u::first("SELECT b.id, b.name FROM crm_import_parents AS p JOIN branches AS b ON b.id = p.branch_id WHERE p.import_id = " . (int)$import->id . " LIMIT 1");
        $data->error = false;
        $data->import = $import;
        $data->import_id = (int)$import->id;
        $data->branch_id = $branch ? (int)$branch->id : 0;
        $data->branch_name = $branch ? $branch->name : '';
        return response()->json($data);
    }

    private function checkResult($import_id)
    {
        $import_id = (int)$import_id;
        $rows = u::query("SELECT id, name, gud_mobile1, gud_mobile2, email, owner_hrm, student_name_1, student_name_2,
                status, is_lock, error_message
            FROM crm_import_parents WHERE import_id = $import_id ORDER BY id");
        $c = u::first("SELECT count(id) AS total,
                SUM(status = 1) AS valid,
                SUM(status = 4 AND is_lock = 0) AS overwrite,
                SUM(status = 6) AS success
            FROM crm_import_parents WHERE import_id = $import_id");
        $total = (int)$c->total;
        $valid = (int)$c->valid;
        $overwrite = (int)$c->overwrite;
        $success = (int)$c->success;
        return (object)[
            'data' => $rows,
            'total' => $total,
            'total_validate' => $valid,
            'total_open_lock' => $overwrite,
            'total_success' => $success,
            'total_error' => max(0, $total - $valid - $overwrite - $success),
        ];
    }

    public function addItemDataImport($list, $import_id, $creator_id, $branch_id = 0)
    {
        if (!$list) {
            return;
        }
        $import_id = (int)$import_id;
        $creator_id = (int)$creator_id;
        $branch_id = (int)$branch_id;
        $created_at = date('Y-m-d H:i:s');
        $head = "INSERT INTO crm_import_parents (import_id,`name`,email,gud_mobile1,`address`,note,created_at,creator_id,`status`,error_message,student_name_1,student_name_2,student_birthday_1,student_birthday_2,student_birthday_year_only_1,student_birthday_year_only_2,owner_hrm,gud_mobile2, checkin_at, checkin_branch_accounting_id, branch_id) VALUES ";
        $q = function ($v) {
            return $v === null || $v === '' ? 'NULL' : "'" . self::esc($v) . "'";
        };
        foreach (array_chunk($list, self::CHUNK) as $chunk) {
            $values = [];
            foreach ($chunk as $raw) {
                $raw = array_pad(array_values((array)$raw), 13, null);
                $item = $this->convertData($raw);
                $validate = $this->validateData($item);
                $status = $validate->has_error ? 2 : 1;
                // SĐT không hợp lệ vẫn giữ giá trị gốc để người dùng biết sai ở đâu
                $mobile1 = $item->gud_mobile1 ?: trim((string)$raw[1]);
                $mobile2 = $item->gud_mobile2 ?: trim((string)$raw[2]);
                $values[] = "($import_id," . $q($item->name) . "," . $q($item->email) . "," . $q($mobile1) . "," . $q($item->address) . "," . $q($item->note)
                    . ",'$created_at',$creator_id,$status," . $q($validate->message) . "," . $q($item->student_name_1) . "," . $q($item->student_name_2)
                    . "," . $q($item->student_birthday_1) . "," . $q($item->student_birthday_2) . "," . (int)$item->student_birthday_year_only_1
                    . "," . (int)$item->student_birthday_year_only_2 . "," . $q($item->owner_hrm) . "," . $q($mobile2)
                    . "," . $q($item->checkin_at) . "," . $q($item->checkin_branch_accounting_id) . ",$branch_id)";
            }
            u::query($head . implode(',', $values));
        }
    }

    public function convertData($data)
    {
        // Ngày sinh: chấp nhận năm (2019), dd/mm/yyyy, yyyy-mm-dd hoặc ô ngày Excel
        $bd1 = u::parseBirthday($data[8]);
        $bd2 = u::parseBirthday($data[10]);
        $clean = function ($v) {
            return trim(preg_replace('/\s+/u', ' ', str_replace(["'", "]", "\\"], '', (string)$v)));
        };
        $mobile1 = u::phoneNew($data[1]);
        $mobile2 = u::phoneNew($data[2]);
        $checkin_at = null;
        if ($data[12] !== null && $data[12] !== '') {
            $checkin_at = is_numeric($data[12])
                ? date('Y-m-d H:i:s', \PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($data[12]))
                : (strtotime(str_replace('/', '-', $data[12])) ? date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $data[12]))) : null);
        }
        return (object)[
            'name' => $clean($data[0]),
            'gud_mobile1' => $mobile1,
            'gud_mobile2' => ($mobile2 && $mobile2 != $mobile1) ? $mobile2 : false,
            'raw_mobile2' => trim((string)$data[2]),
            'email' => trim((string)$data[3]),
            'address' => $clean($data[4]),
            'note' => trim((string)$data[5]),
            'owner_hrm' => trim((string)$data[6]),
            'student_name_1' => $clean($data[7]),
            'student_birthday_1' => $bd1['date'],
            'student_birthday_year_only_1' => $bd1['year_only'],
            'student_name_2' => $clean($data[9]),
            'student_birthday_2' => $bd2['date'],
            'student_birthday_year_only_2' => $bd2['year_only'],
            'checkin_branch_accounting_id' => $data[11] ? trim((string)$data[11]) : null,
            'checkin_at' => $checkin_at,
        ];
    }

    public function validateData($data)
    {
        $errors = [];
        if (!$data->name) {
            $errors[] = 'Tên phụ huynh không để trống';
        }
        if (!$data->gud_mobile1) {
            $errors[] = 'Số điện thoại không hợp lệ';
        }
        if ($data->raw_mobile2 !== '' && !$data->gud_mobile2 && u::phoneNew($data->raw_mobile2) === false) {
            $errors[] = 'Số điện thoại 2 không hợp lệ';
        }
        if ($data->email !== '' && !filter_var($data->email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email không hợp lệ';
        }
        return (object)[
            'has_error' => count($errors) ? 1 : 0,
            'message' => mb_substr(implode('; ', $errors), 0, 250),
        ];
    }

    public function processCheckDuplicateData($import_id)
    {
        $import_id = (int)$import_id;
        // Trùng lặp trong chính file import (giữ dòng đầu tiên)
        $list = u::query("SELECT DISTINCT p.id
            FROM crm_import_parents AS p
                JOIN crm_import_parents AS p1 ON p1.import_id = p.import_id AND p1.id < p.id AND p1.status = 1
                    AND (p1.gud_mobile1 = p.gud_mobile1
                        OR (p1.gud_mobile2 != '' AND p1.gud_mobile2 = p.gud_mobile1)
                        OR (p.gud_mobile2 != '' AND p1.gud_mobile1 = p.gud_mobile2)
                        OR (p.gud_mobile2 != '' AND p1.gud_mobile2 = p.gud_mobile2))
            WHERE p.import_id = $import_id AND p.status = 1");
        if (!empty($list)) {
            $ids = implode(',', array_map(function ($r) { return (int)$r->id; }, $list));
            u::query("UPDATE crm_import_parents SET status = 3, error_message = 'Trùng lặp dữ liệu trong file import' WHERE id IN ($ids)");
        }

        // Trùng với khách hàng đã có trong CRM (chỉ xét dòng hợp lệ)
        $list = u::query("SELECT p.id, u.name, u.hrm_id, u.branch_name, ps.is_lock, ps.id AS parent_id
                FROM crm_import_parents AS p
                    JOIN crm_parents AS ps ON ps.mobile_1 = p.gud_mobile1
                    LEFT JOIN users AS u ON ps.owner_id = u.id
                WHERE p.import_id = $import_id AND p.status = 1
            UNION
                SELECT p.id, u.name, u.hrm_id, u.branch_name, ps.is_lock, ps.id AS parent_id
                FROM crm_import_parents AS p
                    JOIN crm_parents AS ps ON ps.mobile_2 = p.gud_mobile1 AND ps.mobile_2 IS NOT NULL AND ps.mobile_2 != ''
                    LEFT JOIN users AS u ON ps.owner_id = u.id
                WHERE p.import_id = $import_id AND p.status = 1
            UNION
                SELECT p.id, u.name, u.hrm_id, u.branch_name, ps.is_lock, ps.id AS parent_id
                FROM crm_import_parents AS p
                    JOIN crm_parents AS ps ON ps.mobile_1 = p.gud_mobile2 AND p.gud_mobile2 IS NOT NULL AND p.gud_mobile2 != ''
                    LEFT JOIN users AS u ON ps.owner_id = u.id
                WHERE p.import_id = $import_id AND p.status = 1
            UNION
                SELECT p.id, u.name, u.hrm_id, u.branch_name, ps.is_lock, ps.id AS parent_id
                FROM crm_import_parents AS p
                    JOIN crm_parents AS ps ON ps.mobile_2 = p.gud_mobile2 AND ps.mobile_2 IS NOT NULL AND ps.mobile_2 != '' AND p.gud_mobile2 IS NOT NULL AND p.gud_mobile2 != ''
                    LEFT JOIN users AS u ON ps.owner_id = u.id
                WHERE p.import_id = $import_id AND p.status = 1");
        if (!empty($list)) {
            $done = [];
            $values = [];
            foreach ($list as $row) {
                if (isset($done[$row->id])) {
                    continue;
                }
                $done[$row->id] = 1;
                $who = $row->name ? trim("$row->name - $row->hrm_id $row->branch_name") : 'chưa có người phụ trách';
                $msg = (int)$row->is_lock == 0
                    ? "SĐT đã có trong hệ thống, đang thuộc $who (có thể ghi đè)"
                    : "SĐT đang thuộc quyền quản lý của nhân viên $who";
                $values[] = "(" . (int)$row->id . ",4,'" . self::esc(mb_substr($msg, 0, 250)) . "'," . (int)$row->is_lock . "," . (int)$row->parent_id . ")";
            }
            foreach (array_chunk($values, self::CHUNK) as $chunk) {
                u::query("INSERT INTO crm_import_parents (id,`status`,error_message,is_lock,parent_id) VALUES " . implode(',', $chunk)
                    . " ON DUPLICATE KEY UPDATE `status` = VALUES(`status`), `error_message` = VALUES(`error_message`), `is_lock` = VALUES(`is_lock`), `parent_id` = VALUES(`parent_id`)");
            }
        }
    }

    public function assign(Request $request)
    {
        $import = $this->ownImport($request->import_id);
        if (!$import) {
            return response()->json(['error' => true, 'message' => 'Không tìm thấy lượt import hoặc bạn không có quyền thao tác']);
        }
        if ((int)$import->status == 1) {
            return response()->json(['error' => true, 'message' => 'Lượt import này đã được phân chia trước đó']);
        }
        $import_id = (int)$import->id;
        $source_id = (int)$request->source_id;
        $source_detail_id = (int)$request->source_detail_id;
        $arr_owner = array_values(array_unique(array_filter(array_map('intval', (array)$request->owners_id))));
        if (!$source_id) {
            return response()->json(['error' => true, 'message' => 'Nguồn dữ liệu không được để trống']);
        }
        if (!count($arr_owner)) {
            return response()->json(['error' => true, 'message' => 'Người phụ trách không được để trống']);
        }
        // Trung tâm lấy theo dữ liệu đã lưu khi upload, không tin dữ liệu gửi lên
        $branch = u::first("SELECT branch_id FROM crm_import_parents WHERE import_id = $import_id LIMIT 1");
        $branch_id = $branch ? (int)$branch->branch_id : 0;
        $creator_id = (int)$request->user()->id;
        $prefer_file_owner = $request->has('prefer_file_owner') ? (bool)$request->prefer_file_owner : true;

        // Khoá lượt import trước khi xử lý để tránh bấm 2 lần sinh dữ liệu trùng
        $locked = u::query("UPDATE crm_imports SET status = 1, updated_at = '" . date('Y-m-d H:i:s') . "', updator_id = $creator_id WHERE id = $import_id AND status = 0");
        if (!$locked) {
            return response()->json(['error' => true, 'message' => 'Không thể phân chia lượt import này']);
        }

        $list_data = u::query("SELECT * FROM crm_import_parents WHERE import_id = $import_id AND status = 1 ORDER BY id");
        $this->addItemDataParent($list_data, $arr_owner, $source_id, $creator_id, $source_detail_id, $branch_id, $prefer_file_owner);
        $allow_overwrite = $request->has('allow_overwrite') ? (bool)$request->allow_overwrite : true;
        $list_overwrite = !$allow_overwrite ? [] : u::query("SELECT i.*, p.owner_id AS curr_owner_id FROM crm_import_parents AS i LEFT JOIN crm_parents AS p ON p.id = i.parent_id
            WHERE i.import_id = $import_id AND i.status = 4 AND i.is_lock = 0 AND i.parent_id > 0 ORDER BY i.id");
        $this->OverwirteItemDataParent($list_overwrite, $arr_owner, $source_id, $creator_id, $source_detail_id, $branch_id, $prefer_file_owner);

        u::query("UPDATE crm_import_parents SET status = 6 WHERE import_id = $import_id AND (status = 1" . ($allow_overwrite ? " OR (status = 4 AND is_lock = 0 AND parent_id > 0)" : "") . ")");
        u::query("UPDATE crm_students AS s LEFT JOIN crm_parents AS p ON s.gud_mobile_1 = p.mobile_1 SET s.parent_id = p.id WHERE s.parent_id IS NULL");
        u::query("UPDATE crm_students AS s LEFT JOIN branches AS b ON s.checkin_branch_accounting_id = b.accounting_id SET s.checkin_branch_id = b.id WHERE s.checkin_branch_id IS NULL AND s.checkin_branch_accounting_id IS NOT NULL");
        // Học sinh import cũng lên chính thức ngay (giống tạo thủ công) để có thể nhập học luôn
        $this->promoteImportedStudents($import_id);

        $data = u::first("SELECT SUM(status = 6) AS total_success, SUM(status != 6) AS total_error FROM crm_import_parents WHERE import_id = $import_id");
        $owners = u::query("SELECT u.id, u.name, u.hrm_id, count(p.id) AS total
            FROM crm_parents AS p JOIN users AS u ON u.id = p.owner_id
            WHERE p.import_id = $import_id GROUP BY u.id, u.name, u.hrm_id ORDER BY total DESC");
        return response()->json([
            'error' => false,
            'message' => 'Phân chia dữ liệu thành công',
            'total_success' => (int)$data->total_success,
            'total_error' => (int)$data->total_error,
            'total_overwrite' => count($list_overwrite),
            'owners' => $owners,
        ]);
    }

    public static function promoteImportedStudents($import_id = 0)
    {
        set_time_limit(0);
        $cond = $import_id > 0 ? "p.import_id = " . (int)$import_id : "p.import_id > 0";
        $list = u::query("SELECT cs.id FROM crm_students AS cs
                INNER JOIN crm_parents AS p ON p.id = cs.parent_id
            WHERE $cond AND IFNULL(cs.lms_id, 0) = 0
            ORDER BY cs.id");
        $total = 0;
        foreach ($list as $row) {
            if (StudentsController::promoteToOfficialStudent($row->id)) {
                $total++;
            }
        }
        return $total;
    }

    /** Chọn người phụ trách: ưu tiên người ghi trong file (nếu nằm trong danh sách đã chọn), còn lại chia vòng tròn */
    private function pickOwner($item, $arr_owner, &$rr, $prefer_file_owner)
    {
        $file_owner = (int)data_get($item, 'owner_id');
        if ($prefer_file_owner && $file_owner && in_array($file_owner, $arr_owner)) {
            return $file_owner;
        }
        $owner = $arr_owner[$rr % count($arr_owner)];
        $rr++;
        return $owner;
    }

    public function addItemDataParent($list, $arr_owner, $source_id, $creator_id, $source_detail_id, $branch_id = 0, $prefer_file_owner = true)
    {
        if (!$list) {
            return;
        }
        $created_at = date('Y-m-d H:i:s');
        $source_id = (int)$source_id;
        $source_detail_id = (int)$source_detail_id;
        $creator_id = (int)$creator_id;
        $branch_id = (int)$branch_id;
        $q = function ($v) {
            return $v === null || $v === '' ? 'NULL' : "'" . addslashes((string)$v) . "'";
        };
        $s = function ($v) {
            return "'" . addslashes((string)$v) . "'";
        };
        $rr = 0;
        foreach (array_chunk($list, self::CHUNK) as $chunk) {
            $parents = [];
            $students = [];
            foreach ($chunk as $item) {
                $item = (object)$item;
                $owner_id = (int)$this->pickOwner($item, $arr_owner, $rr, $prefer_file_owner);
                $parents[] = "(" . $s($item->name) . "," . $s($item->email) . "," . $s($item->gud_mobile1) . "," . $s($item->address) . "," . $s($item->note)
                    . ",'$created_at',$creator_id,1,$source_id,$source_detail_id,$owner_id," . $s($item->gud_mobile2) . ",'$created_at',$branch_id," . (int)$item->import_id . ")";
                foreach ([1, 2] as $n) {
                    $name = $item->{"student_name_$n"};
                    if ($name) {
                        $students[] = "(" . $s($name) . "," . $q($item->{"student_birthday_$n"}) . "," . (int)$item->{"student_birthday_year_only_$n"}
                            . ",'$created_at',$creator_id," . $s($item->gud_mobile1) . "," . $q($item->checkin_at) . "," . $q($item->checkin_branch_accounting_id) . ")";
                    }
                }
            }
            u::query("INSERT INTO crm_parents (`name`,email,mobile_1,`address`,note,created_at,creator_id,`status`,source_id,source_detail_id,owner_id,mobile_2,last_assign_date,tmp_branch_id,import_id) VALUES " . implode(',', $parents));
            if (count($students)) {
                u::query("INSERT INTO crm_students (`name`,`birthday`,birthday_year_only,created_at,creator_id,gud_mobile_1,checkin_at,checkin_branch_accounting_id) VALUES " . implode(',', $students));
            }
        }
    }

    public function OverwirteItemDataParent($list, $arr_owner, $source_id, $creator_id, $source_detail_id, $branch_id = 0, $prefer_file_owner = true)
    {
        if (!$list) {
            return;
        }
        $created_at = date('Y-m-d H:i:s');
        $creator_id = (int)$creator_id;
        $branch_id = (int)$branch_id;
        $rr = 0;
        $seen = [];
        foreach (array_chunk($list, self::CHUNK) as $chunk) {
            $owners = [];
            $overwrites = [];
            $logs = [];
            foreach ($chunk as $item) {
                $item = (object)$item;
                $parent_id = (int)$item->parent_id;
                if (!$parent_id || isset($seen[$parent_id])) {
                    continue;
                }
                $seen[$parent_id] = 1;
                $owner_id = (int)$this->pickOwner($item, $arr_owner, $rr, $prefer_file_owner);
                $last_owner = (int)data_get($item, 'curr_owner_id');
                $owners[] = "($parent_id,'$created_at',$creator_id,$owner_id,'$created_at',1)";
                $overwrites[] = "($parent_id,$last_owner,$owner_id,'$created_at',$creator_id,$branch_id)";
                $logs[] = "($parent_id,'Ghi đè người phụ trách khi import: từ $last_owner thành $owner_id',$creator_id,'$created_at',1)";
            }
            if (!count($owners)) {
                continue;
            }
            u::query("INSERT INTO crm_parents (id,updated_at,updator_id,owner_id,last_assign_date,is_lock) VALUES " . implode(',', $owners)
                . " ON DUPLICATE KEY UPDATE `updated_at` = VALUES(`updated_at`), `updator_id` = VALUES(`updator_id`), `owner_id` = VALUES(`owner_id`), `last_assign_date` = VALUES(`last_assign_date`), `is_lock` = VALUES(`is_lock`)");
            u::query("INSERT INTO crm_parent_overwrite (`parent_id`,last_owner_id,owner_id,`created_at`,creator_id,branch_id) VALUES " . implode(',', $overwrites));
            u::query("INSERT INTO crm_parent_logs (`parent_id`,`content`,creator_id,created_at,`status`) VALUES " . implode(',', $logs));
        }
    }
}
