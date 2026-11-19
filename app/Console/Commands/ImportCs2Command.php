<?php

namespace App\Console\Commands;

use App\Providers\UtilityServiceProvider as u;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

/**
 * Import dữ liệu CS2 — Eurowindow River Park từ 2 file của trung tâm:
 *  - ds_hoc_sinh.xlsx ("Final và theo dõi DS ĐÔNG ANH", sheet "DSHS tổng" + "DS TTH"): học sinh, gói phí, tiền.
 *  - diem_danh.xlsx (sổ điểm danh, mỗi sheet 1 lớp): lớp, giáo viên, lịch học, điểm danh từng buổi.
 *
 * Nguyên tắc:
 *  - Gói phí / tiền lấy theo file 1. Lớp + điểm danh lấy theo file 2.
 *  - Học sinh đã có (khớp SĐT + tên) → cập nhật, không tạo trùng. HĐ đã có cùng HS + sản phẩm → dùng lại.
 *  - HĐ đang có phiếu thu chờ duyệt → KHÔNG tạo phiếu thu (Kế toán duyệt phiếu sẵn có).
 *  - Mọi bản ghi tạo mới / giá trị cũ bị ghi đè được lưu vào batch JSON để --rollback.
 *
 *   php artisan import:cs2 --dry-run          xem trước, không ghi DB
 *   php artisan import:cs2 --run              ghi DB (dừng nếu còn mục CẦN XÁC NHẬN)
 *   php artisan import:cs2 --run --skip-pending   ghi DB, bỏ qua các HĐ còn chờ xác nhận
 *   php artisan import:cs2 --rollback         hoàn tác batch đã chạy
 */
class ImportCs2Command extends Command
{
    protected $signature = 'import:cs2
        {--dry-run : Đọc file, đối chiếu DB và in báo cáo, không ghi DB}
        {--run : Ghi dữ liệu vào DB}
        {--skip-pending : Khi --run, bỏ qua các hợp đồng còn chờ xác nhận}
        {--rollback : Hoàn tác batch đã import}';

    protected $description = 'Import học sinh, lớp, hợp đồng, điểm danh, phiếu thu cho CS2 — Eurowindow River Park';

    const BRANCH_ID = 2;
    const BRANCH_NAME = 'CS2 — Eurowindow River Park';
    const EC_ID = 140;          // Nguyễn Thanh Hà (S005)
    const EC_LEADER_ID = 139;   // Đỗ Thị Thảo Nguyên (S004)
    const ROOM_ID = 59;         // phòng duy nhất của CS2
    const METHOD_CARD = 2;      // QT = Quẹt thẻ
    const SYSTEM_USER = 1;
    const TOTAL_CLASS_SESSIONS = 96;
    const BATCH_FILE = 'import_batches/cs2_batch.json';
    const FILE_STUDENTS = 'import_batches/cs2/ds_hoc_sinh.xlsx';
    const FILE_ATTENDANCE = 'import_batches/cs2/diem_danh.xlsx';
    const TAG = '[Import CS2 202611]';

    const TEACHERS = [
        'trang' => ['name' => 'Thái Thị Trang', 'hrm_id' => 'S008', 'email' => 'trang.tt@stemkey.vn'],
        'hue' => ['name' => 'Hà Thị Huệ', 'hrm_id' => 'S009', 'email' => 'hue.ht@stemkey.vn'],
        'rose' => ['name' => 'Rose', 'hrm_id' => 'S010', 'email' => 'rose@stemkey.vn'],
    ];

    /** sheet (file 2) => lớp. product: 1 Ucrea, 2 Bright I.G (gồm BH), 3 Tiếng Anh, 4 TTH */
    const CLASSES = [
        '001.C2.21.T4-6.' => ['name' => 'CS2.001.U.21', 'product' => 1, 'program' => 520, 'teacher' => 'trang'],
        '002.C2.22.T3-T5' => ['name' => 'CS2.002.U.22', 'product' => 1, 'program' => 520, 'teacher' => 'hue'],
        '003.20.C1.T3-T5(01)' => ['name' => 'CS2.003.IG.20', 'product' => 2, 'program' => 534, 'teacher' => 'trang'],
        '004.21.C1.T4-T7' => ['name' => 'CS2.004.IG.21', 'product' => 2, 'program' => 534, 'teacher' => 'trang'],
        '005.18.C1.T3-T5' => ['name' => 'CS2.005.IG.18', 'product' => 2, 'program' => 534, 'teacher' => 'hue'],
        '006.21.C1T7-C2CN' => ['name' => 'CS2.006.U.21', 'product' => 1, 'program' => 520, 'teacher' => 'trang'],
        '007.23.C1.T4-T6' => ['name' => 'CS2.007.U.23', 'product' => 1, 'program' => 520, 'teacher' => 'hue'],
        '008.22.C1S.T7-CN' => ['name' => 'CS2.008.U.22', 'product' => 1, 'program' => 520, 'teacher' => 'trang'],
        '009.18.C1.T5' => ['name' => 'CS2.009.IG.18', 'product' => 2, 'program' => 534, 'teacher' => 'trang'],
        '010.23.C1.T7-CN' => ['name' => 'CS2.010.U.23', 'product' => 1, 'program' => 520, 'teacher' => 'hue'],
        '101.TTH.21.C2.T3-5' => ['name' => 'CS2.101.TTH.21', 'product' => 4, 'program' => 532, 'teacher' => 'trang'],
        '102.TTH.C2.T3-7' => ['name' => 'CS2.102.TTH', 'product' => 4, 'program' => 532, 'teacher' => 'trang'],
        '103.TTH.21.C2.T5' => ['name' => 'CS2.103.TTH.21', 'product' => 4, 'program' => 532, 'teacher' => 'trang'],
        '1.TA.T4-T7' => ['name' => 'CS2.104.TA', 'product' => 3, 'program' => 526, 'teacher' => 'rose'],
    ];

    /** Học sinh không có trong file 1 (chỉ có ở sổ điểm danh) */
    const EXTRA_STUDENTS = [
        'X_LINHANH' => ['name' => 'Đào Linh Anh', 'phone' => '0964156481', 'mother' => 'Khánh Linh'],
    ];

    /**
     * Hợp đồng cần có. att = [sheet => tên HS trong sheet]; nhiều sheet = chuyển lớp (sheet cuối là lớp hiện tại).
     * gift = gói tặng 0đ. money = 'f1' lấy trị giá/đã thu theo dòng STM ở DSHS tổng; 'tth' lấy từ sheet DS TTH.
     * pending = cần user xác nhận trước khi ghi.
     */
    const CONTRACTS = [
        ['stm' => 'STM01', 'product' => 1, 'fee' => 239, 'sessions' => 96, 'bonus' => 8, 'money' => 'f1', 'att' => ['001.C2.21.T4-6.' => 'Nguyễn Minh Uyên'], 'note' => 'Học bổng 1 tháng (Giải nhì) = +8 buổi'],
        ['stm' => 'STM01', 'product' => 4, 'fee' => 254, 'sessions' => 32, 'gift' => 'Tặng 4 tháng TTH', 'att' => ['102.TTH.C2.T3-7' => 'Nguyễn Minh Uyên']],
        ['stm' => 'STM02', 'product' => 1, 'fee' => 237, 'sessions' => 24, 'money' => 'f1', 'att' => ['001.C2.21.T4-6.' => 'Nguyễn Minh Khôi']],
        ['stm' => 'STM03', 'product' => 1, 'fee' => 239, 'sessions' => 96, 'money' => 'f1', 'att' => ['001.C2.21.T4-6.' => 'Lê Minh Anh'], 'note' => 'Học bổng: 1 tháng TTH (giới thiệu)'],
        ['stm' => 'STM03', 'product' => 4, 'fee' => 254, 'sessions' => 32, 'gift' => 'Tặng 4 tháng TTH', 'att' => ['102.TTH.C2.T3-7' => 'Lê Minh Anh']],
        ['stm' => 'STM04', 'product' => 1, 'fee' => 239, 'sessions' => 96, 'money' => 'f1', 'att' => ['001.C2.21.T4-6.' => 'Bùi Thảo Nhi']],
        ['stm' => 'STM04', 'product' => 4, 'fee' => 254, 'sessions' => 32, 'gift' => 'Tặng 4 tháng TTH', 'att' => ['102.TTH.C2.T3-7' => 'Bùi Thảo Nhi']],
        ['stm' => 'STM05', 'product' => 1, 'fee' => 239, 'sessions' => 96, 'money' => 'f1', 'att' => ['001.C2.21.T4-6.' => 'Nguyễn Hoàng Trang']],
        ['stm' => 'STM05', 'product' => 4, 'fee' => 254, 'sessions' => 32, 'gift' => 'Tặng 4 tháng TTH', 'att' => ['102.TTH.C2.T3-7' => 'Nguyễn Hoàng Trang']],
        ['stm' => 'STM06', 'product' => 1, 'fee' => 238, 'sessions' => 48, 'bonus' => 12, 'money' => 'f1', 'att' => ['002.C2.22.T3-T5' => 'Hoàng Trần Diệp Anh'], 'note' => 'Học bổng 1,5 tháng (Giải nhất) = +12 buổi'],
        ['stm' => 'STM07', 'product' => 1, 'fee' => 252, 'sessions' => 8, 'bonus' => 8, 'money' => 'f1', 'att' => ['002.C2.22.T3-T5' => 'Nguyễn Xuân Phúc'], 'note' => '1 tháng + 1 tháng trải nghiệm'],
        ['stm' => 'STM08', 'product' => 2, 'fee' => 243, 'sessions' => 96, 'bonus' => 8, 'money' => 'f1', 'att' => ['003.20.C1.T3-T5(01)' => 'Ngô Ngọc Linh San'], 'note' => 'Tặng 1 tháng trải nghiệm = +8 buổi'],
        ['stm' => 'STM09', 'product' => 2, 'fee' => 242, 'sessions' => 48, 'bonus' => 8, 'money' => 'f1', 'att' => ['003.20.C1.T3-T5(01)' => 'Nguyễn Khánh Nam'], 'note' => 'Tặng 1 tháng trải nghiệm = +8 buổi'],
        ['stm' => 'STM10', 'product' => 1, 'fee' => 239, 'sessions' => 96, 'money' => 'f1', 'att' => ['004.21.C1.T4-T7' => 'Chu Hoàng Nam', '001.C2.21.T4-6.' => 'Chu Hoàng Nam'], 'note' => 'Học lớp 004 rồi chuyển sang lớp 001'],
        ['stm' => 'STM10', 'product' => 4, 'fee' => 254, 'sessions' => 32, 'gift' => 'Tặng 4 tháng TTH', 'att' => ['102.TTH.C2.T3-7' => 'Chu Hoàng Nam'], 'note' => 'File 1 không ghi, trung tâm xác nhận tặng TTH'],
        ['stm' => 'STM11', 'product' => 2, 'fee' => 253, 'sessions' => 8, 'money' => 'f1', 'att' => ['004.21.C1.T4-T7' => 'Đinh Phú Cường']],
        ['stm' => 'STM11', 'product' => 4, 'fee' => 245, 'sessions' => 8, 'money' => 'tth', 'att' => ['103.TTH.21.C2.T5' => 'Đinh Phú Cường']],
        ['stm' => 'STM12', 'product' => 2, 'fee' => 243, 'sessions' => 96, 'bonus' => 8, 'money' => 'f1', 'att' => ['005.18.C1.T3-T5' => 'Ngô Ngọc Linh My'], 'note' => 'Khóa BH (Bright I.G). Tặng 1 tháng trải nghiệm = +8 buổi'],
        ['stm' => 'STM13', 'product' => 1, 'fee' => 239, 'sessions' => 96, 'money' => 'f1', 'att' => ['006.21.C1T7-C2CN' => 'Nguyễn Bảo Linh']],
        ['stm' => 'STM13', 'product' => 4, 'fee' => 254, 'sessions' => 32, 'gift' => 'Tặng 4 tháng TTH', 'att' => ['102.TTH.C2.T3-7' => 'Nguyễn Bảo Linh']],
        ['stm' => 'STM14', 'product' => 1, 'fee' => 239, 'sessions' => 96, 'money' => 'f1', 'att' => ['007.23.C1.T4-T6' => 'Lê Việt Khánh']],
        ['stm' => 'STM15', 'product' => 1, 'fee' => 239, 'sessions' => 96, 'money' => 'f1', 'att' => ['006.21.C1T7-C2CN' => 'Phạm Minh An']],
        ['stm' => 'STM15', 'product' => 4, 'fee' => 254, 'sessions' => 32, 'gift' => 'Tặng 4 tháng TTH', 'att' => ['101.TTH.21.C2.T3-5' => 'Phạm Minh An']],
        ['stm' => 'STM16', 'product' => 1, 'fee' => 239, 'sessions' => 96, 'money' => 'f1', 'att' => ['008.22.C1S.T7-CN' => 'Nguyễn Quang Anh', '002.C2.22.T3-T5' => 'Nguyễn Quang Anh'], 'note' => 'Học lớp 008 rồi chuyển sang lớp 002'],
        ['stm' => 'STM17', 'product' => 3, 'fee' => 250, 'sessions' => 96, 'money' => 'f1', 'att' => ['1.TA.T4-T7' => 'Đào Quang Vinh']],
        ['stm' => 'STM17', 'product' => 2, 'fee' => 241, 'sessions' => 32, 'gift' => 'Tặng 4 tháng TTD', 'att' => ['005.18.C1.T3-T5' => 'Đào Quang Vinh']],
        ['stm' => 'STM18', 'product' => 3, 'fee' => 250, 'sessions' => 96, 'money' => 'fee', 'att' => ['1.TA.T4-T7' => 'Nguyễn Tiến Phúc'], 'note' => 'File 1 trống trị giá → lấy theo gói phí'],
        ['stm' => 'STM18', 'product' => 2, 'fee' => 241, 'sessions' => 32, 'gift' => 'Tặng 4 tháng TTD', 'att' => ['009.18.C1.T5' => 'Nguyễn Tiến Phúc']],
        ['stm' => 'STM19', 'product' => 1, 'fee' => 239, 'sessions' => 96, 'money' => 'f1', 'att' => ['010.23.C1.T7-CN' => 'Chu Đức Khoa']],
        ['stm' => 'STM 20', 'product' => 4, 'fee' => 246, 'sessions' => 48, 'money' => 'tth', 'att' => ['101.TTH.21.C2.T3-5' => 'Nguyễn Gia Hân']],
        ['stm' => 'STM 021', 'product' => 3, 'fee' => 250, 'sessions' => 96, 'money' => 'f1', 'att' => ['1.TA.T4-T7' => 'Phạm An Khuê']],
        ['stm' => 'STM 021', 'product' => 1, 'fee' => 237, 'sessions' => 32, 'gift' => 'Tặng 4 tháng TTD', 'att' => ['002.C2.22.T3-T5' => 'Phạm An Khuê']],
        ['stm' => 'STM 022', 'product' => 1, 'fee' => 237, 'sessions' => 24, 'money' => 'f1', 'att' => ['007.23.C1.T4-T6' => 'Nguyễn Văn Minh Quân']],
        ['stm' => 'STM 023', 'product' => 3, 'fee' => 250, 'sessions' => 96, 'money' => 'f1', 'att' => ['1.TA.T4-T7' => 'Nguyễn Ngọc Bảo Hân']],
        ['stm' => 'STM 023', 'product' => 2, 'fee' => 241, 'sessions' => 32, 'gift' => 'Tặng 4 tháng TTD', 'att' => ['005.18.C1.T3-T5' => 'Nguyễn Ngọc Bảo Hân']],
        ['stm' => 'STM 024', 'product' => 1, 'fee' => 239, 'sessions' => 96, 'money' => 'f1', 'att' => []],
        ['stm' => 'X_LINHANH', 'product' => 1, 'fee' => 239, 'sessions' => 96, 'money' => 'fee_full', 'att' => ['002.C2.22.T3-T5' => 'Đào Linh Anh'], 'note' => 'Không có trong file 1 → trị giá = đã thu = giá gói'],
    ];

    /** @var array */
    private $f1 = [];
    private $tth = [];
    private $sheets = [];
    private $warnings = [];
    private $batch = [];

    public function handle()
    {
        ini_set('memory_limit', '1024M');
        if ($this->option('rollback')) return $this->rollback();
        if (!$this->option('dry-run') && !$this->option('run')) {
            $this->warn('Chọn --dry-run (xem trước), --run (ghi DB) hoặc --rollback (hoàn tác).');
            return 0;
        }
        if ($this->option('run') && file_exists(storage_path('app/' . self::BATCH_FILE))) {
            $this->error('Đã có batch import CS2 (' . self::BATCH_FILE . '). Chạy --rollback trước nếu muốn import lại.');
            return 1;
        }

        $this->readFiles();
        $plan = $this->buildPlan();
        $this->printPlan($plan);

        if (!$this->option('run')) return 0;

        $pending = array_filter($plan['contracts'], function ($c) { return !empty($c['cfg']['pending']); });
        if ($pending && !$this->option('skip-pending')) {
            $this->error("\nCòn " . count($pending) . ' hợp đồng CẦN XÁC NHẬN. Trả lời các câu hỏi hoặc chạy kèm --skip-pending để bỏ qua.');
            return 1;
        }
        return $this->runImport($plan);
    }

    /* ============================ ĐỌC FILE ============================ */

    private function readFiles()
    {
        $f1 = IOFactory::load(storage_path('app/' . self::FILE_STUDENTS));
        foreach (['DSHS tổng' => 'f1', 'DS TTH' => 'tth'] as $sheetName => $target) {
            $ws = $f1->getSheetByName($sheetName);
            $max = $ws->getHighestDataRow();
            for ($r = 7; $r <= $max; $r++) {
                $code = trim((string)$ws->getCellByColumnAndRow(3, $r)->getCalculatedValue());
                $name = trim((string)$ws->getCellByColumnAndRow(4, $r)->getCalculatedValue());
                if ($code === '' || $name === '' || $name === '0') continue;
                $g = function ($c) use ($ws, $r) { return $ws->getCellByColumnAndRow($c, $r)->getCalculatedValue(); };
                $row = [
                    'code' => $code,
                    'name' => trim(explode("\n", $name)[0]),
                    'reg_date' => $this->date($g(2)),
                    'year' => (int)$g(6) ?: null,
                    'course' => trim((string)$g(7)),
                    'months' => $g(8),
                    'gift' => trim((string)$g(9)),
                    'scholarship' => trim((string)$g(10)),
                    'school' => trim((string)$g(12)),
                    'father' => trim((string)$g(13)), 'father_phone' => $this->phone($g(14)),
                    'mother' => trim((string)$g(15)), 'mother_phone' => $this->phone($g(16)),
                    'address' => trim((string)$g(17)),
                    'state' => trim((string)$g(18)),
                    'start' => $this->date($g(19)),
                    'value' => $g(22) === null || $g(22) === '' ? null : (int)$g(22),
                    'paid' => (int)$g(23),
                    'fee_state' => trim((string)$g(25)),
                    'gift_item' => trim((string)$g(27)),
                    'note' => trim((string)$g(29)),
                ];
                if ($target === 'f1') $this->f1[$code] = $row;
                else $this->tth[$code . '|' . $row['course']] = $row;
            }
        }

        $f2 = IOFactory::load(storage_path('app/' . self::FILE_ATTENDANCE));
        foreach (array_keys(self::CLASSES) as $sheetName) {
            $ws = $f2->getSheetByName($sheetName);
            if (!$ws) { $this->warnings[] = "Không thấy sheet $sheetName trong file điểm danh"; continue; }
            $hdr = null;
            for ($r = 1; $r <= 12; $r++) if (trim((string)$ws->getCellByColumnAndRow(1, $r)->getValue()) === 'STT') { $hdr = $r; break; }
            $maxCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($ws->getHighestDataColumn());
            $dates = [];
            for ($c = 15; $c <= $maxCol; $c++) {
                $d = $this->date($ws->getCellByColumnAndRow($c, $hdr + 1)->getCalculatedValue());
                if ($d) $dates[$c] = $d;
            }
            $students = [];
            for ($r = $hdr + 2; $r <= $ws->getHighestDataRow(); $r++) {
                $name = trim((string)$ws->getCellByColumnAndRow(3, $r)->getValue());
                $code = trim((string)$ws->getCellByColumnAndRow(2, $r)->getValue());
                if ($name === '' || $code === '') continue;
                $att = [];
                foreach ($dates as $c => $d) {
                    $v = strtoupper(trim((string)$ws->getCellByColumnAndRow($c, $r)->getCalculatedValue()));
                    if ($v !== '') $att[$d] = $v;
                }
                $students[$this->norm($name)] = [
                    'name' => $name,
                    'phone' => $this->phone($ws->getCellByColumnAndRow(5, $r)->getValue()),
                    'start' => $this->date($ws->getCellByColumnAndRow(7, $r)->getCalculatedValue()),
                    'done' => (int)$ws->getCellByColumnAndRow(9, $r)->getCalculatedValue(),
                    'att' => $att,
                ];
            }
            $this->sheets[$sheetName] = [
                'title' => trim((string)$ws->getCell('A2')->getValue()),
                'time' => trim((string)$ws->getCell('A5')->getValue()) ?: trim((string)$ws->getCell('A4')->getValue()),
                'dates' => array_values($dates),
                'students' => $students,
            ];
        }
    }

    /* ============================ LẬP KẾ HOẠCH ============================ */

    private function buildPlan()
    {
        $today = date('Y-m-d');
        $plan = ['teachers' => [], 'classes' => [], 'students' => [], 'contracts' => [], 'parents_fix' => 0];

        foreach (self::TEACHERS as $k => $t) {
            $ex = DB::table('users')->where('hrm_id', $t['hrm_id'])->orWhere('email', $t['email'])->first();
            $plan['teachers'][$k] = $t + ['existing_id' => $ex ? $ex->id : null, 'conflict' => $ex && $ex->name !== $t['name']];
            if ($ex && $ex->name !== $t['name']) $this->warnings[] = "Mã {$t['hrm_id']} / email {$t['email']} đã thuộc tài khoản khác ({$ex->name}) → đổi mã trước khi chạy";
        }

        foreach (self::CLASSES as $sheet => $c) {
            $s = $this->sheets[$sheet] ?? ['dates' => [], 'time' => '', 'title' => ''];
            $days = [];
            foreach (array_slice($s['dates'], 0, 14) as $d) {
                $n = (int)date('N', strtotime($d)); // 1=T2 .. 7=CN
                $days[$n === 7 ? 8 : $n + 1] = true;
            }
            ksort($days);
            $days = array_keys($days);
            $label = 'Thứ ' . implode('-', array_map(function ($d) { return $d === 8 ? 'CN' : $d; }, $days));
            $exist = DB::table('classes')->where('branch_id', self::BRANCH_ID)->where('cls_name', $c['name'] . '.' . $label)->first();
            $plan['classes'][$sheet] = $c + [
                'cls_name' => $c['name'] . '.' . $label,
                'days' => $days,
                'dates' => $s['dates'],
                'time' => $s['time'],
                'past' => count(array_filter($s['dates'], function ($d) use ($today) { return $d <= $today; })),
                'existing_id' => $exist ? $exist->id : null,
            ];
            if ($exist) $this->warnings[] = "Lớp {$c['name']} đã tồn tại (id {$exist->id}) → sẽ không tạo lại";
        }

        // Học sinh CS2 hiện có (để khớp)
        $existing = DB::table('students')->where('branch_id', self::BRANCH_ID)->get();

        foreach (self::CONTRACTS as $i => $cfg) {
            $key = $cfg['stm'];
            if (!isset($plan['students'][$key])) {
                $plan['students'][$key] = $this->planStudent($key, $existing);
            }
            $st = $plan['students'][$key];
            $f1 = $this->f1[$key] ?? null;

            // Tiền
            $gift = !empty($cfg['gift']);
            $must = 0; $paid = 0;
            if (!$gift) {
                if ($cfg['money'] === 'f1' && $f1) { $must = $f1['value']; $paid = $f1['paid']; }
                if ($cfg['money'] === 'tth') {
                    $t = $this->tth[$key . '|TTH'] ?? null;
                    $must = $t ? $t['value'] : null; $paid = $t ? $t['paid'] : 0;
                }
                if ($cfg['money'] === 'none') { $must = null; $paid = 0; }
                if ($cfg['money'] === 'fee' || $cfg['money'] === 'fee_full') {
                    $must = (int)DB::table('tuition_fee')->where('id', $cfg['fee'])->value('price');
                    $paid = $cfg['money'] === 'fee_full' ? $must : (int)($f1['paid'] ?? 0);
                }
            }

            // Hợp đồng đã có (cùng HS + sản phẩm)
            $exContract = null; $pendingReceipts = 0;
            if ($st['student_id']) {
                $exContract = DB::table('contracts')->where('student_id', $st['student_id'])->where('product_id', $cfg['product'])
                    ->where('branch_id', self::BRANCH_ID)->where('type', 1)->where('status', '>', 0)->orderBy('id')->first();
                if ($exContract) {
                    $pendingReceipts = (int)DB::table('tmp_payments')->where('contract_id', $exContract->id)->where('status', 0)->sum('charge_amount');
                    $approved = (int)DB::table('payments')->where('contract_id', $exContract->id)->sum('amount');
                    if ($must === null) $must = (int)$exContract->must_charge; // file 1 trống → giữ giá trị trên hệ thống
                    if ($approved) $this->warnings[] = "HĐ {$exContract->code} đã có phiếu thu duyệt " . number_format($approved) . ' → không tạo thêm phiếu';
                    if ((int)$exContract->must_charge !== (int)$must) {
                        $this->warnings[] = "HĐ {$exContract->code} ({$st['name']}): trị giá trên hệ thống " . number_format($exContract->must_charge) . ' ≠ file 1 ' . number_format($must) . ' → cập nhật theo file 1';
                    }
                }
            }

            // Điểm danh
            $sessions = [];
            $classSeq = [];
            foreach ($cfg['att'] as $sheet => $nm) {
                $row = $this->sheets[$sheet]['students'][$this->norm($nm)] ?? null;
                if (!$row) { $this->warnings[] = "Không tìm thấy \"$nm\" trong sheet $sheet"; continue; }
                $classSeq[] = $sheet;
                foreach ($row['att'] as $d => $mark) {
                    if ($d > $today) { $this->warnings[] = "$nm ($sheet): bỏ qua điểm danh ngày tương lai $d"; continue; }
                    if (!in_array($mark, ['X', 'O', 'BL'], true)) { $this->warnings[] = "$nm ($sheet) ngày $d: ký hiệu \"$mark\" không rõ → bỏ qua"; continue; }
                    $sessions[$d] = ['sheet' => $sheet, 'mark' => $mark];
                }
            }
            ksort($sessions);
            $done = count(array_filter($sessions, function ($s) { return $s['mark'] !== 'BL'; }));
            $total = (int)$cfg['sessions'] + (int)($cfg['bonus'] ?? 0);
            // Số buổi được học: như ChargesController@approve – đóng đủ = cả gói (kể cả buổi tặng), đóng thiếu = theo tỷ lệ tiền
            $summary = $total;
            if (!$gift && (int)$must > 0 && (int)$paid > 0 && (int)$paid < (int)$must && !$pendingReceipts) {
                $summary = (int)round((int)$paid / ((int)$must / (int)$cfg['sessions']));
                $this->warnings[] = "{$st['name']} – " . $this->productName($cfg['product']) . ': mới đóng ' . number_format($paid) . '/' . number_format($must) . " → được học $summary/$total buổi (theo tỷ lệ tiền, như khi Kế toán duyệt phiếu)";
            }
            $left = $summary - $done;
            if ($left < 0) $this->warnings[] = "{$st['name']} – " . $this->productName($cfg['product']) . ": đã học $done buổi > $summary buổi được học → HĐ hết phí (status 7)";

            if (!empty($cfg['trial'])) $status = 7;
            elseif ($left <= 0 && $sessions) $status = 7;
            elseif ($sessions) $status = 6;
            elseif ($gift) $status = 3;
            else $status = $must ? u::contractStatusByCharged((int)$must, (int)$paid, 1) : 3;

            $firstDate = $sessions ? array_keys($sessions)[0] : null;
            $start = $firstDate ?: ($f1['start'] ?? null) ?: $today;
            $chargeDate = ($f1['reg_date'] ?? null) ?: $start;

            $plan['contracts'][] = [
                'cfg' => $cfg, 'student_key' => $key, 'gift' => $gift,
                'must' => $must, 'paid' => $gift ? 0 : $paid, 'debt' => $gift ? 0 : max(0, (int)$must - (int)$paid),
                'existing' => $exContract, 'pending_receipts' => $pendingReceipts,
                'create_payment' => !$gift && $paid > 0 && !$pendingReceipts && !($exContract && DB::table('payments')->where('contract_id', $exContract->id)->exists()),
                'sessions' => $sessions, 'class_seq' => $classSeq, 'done' => $done, 'total' => $total, 'summary' => $summary, 'left' => max(0, $left),
                'status' => $status, 'start' => $start, 'charge_date' => $chargeDate,
                'gift_note' => trim(implode(' + ', array_filter([$f1['gift_item'] ?? '', $gift ? $cfg['gift'] : ($f1['gift'] ?? '')])), ' +'),
            ];
        }

        $plan['parents_fix'] = DB::table('crm_parents')->whereNull('branch_id')->where('tmp_branch_id', self::BRANCH_ID)->count();
        return $plan;
    }

    private function planStudent($key, $existing)
    {
        $f1 = $this->f1[$key] ?? null;
        $extra = self::EXTRA_STUDENTS[$key] ?? null;
        $name = $f1 ? $this->cleanName($f1['name']) : $extra['name'];
        $phones = array_values(array_filter($f1 ? [$f1['mother_phone'], $f1['father_phone']] : [$extra['phone']]));
        // Bổ sung SĐT từ sổ điểm danh (file 1 có dòng thiếu SĐT)
        foreach (self::CONTRACTS as $cfg) {
            if ($cfg['stm'] !== $key) continue;
            foreach ($cfg['att'] as $sheet => $nm) {
                $p = $this->sheets[$sheet]['students'][$this->norm($nm)]['phone'] ?? '';
                if ($p && !in_array($p, $phones, true)) $phones[] = $p;
            }
        }

        // Khớp: cùng SĐT + tên giống; nếu không có SĐT thì khớp tên tuyệt đối
        $match = null; $candidates = [];
        foreach ($existing as $s) {
            $sp = array_filter([$this->phone($s->gud_mobile1), $this->phone($s->gud_mobile2)]);
            $samePhone = (bool)array_intersect($sp, $phones);
            $nameScore = $this->nameScore($name, $s->name);
            if (($samePhone && $nameScore >= 1) || $nameScore >= 3) $candidates[] = [$s, $nameScore + ($samePhone ? 2 : 0)];
        }
        usort($candidates, function ($a, $b) { return $b[1] <=> $a[1]; });
        if ($candidates) $match = $candidates[0][0];
        if (count($candidates) > 1 && $candidates[0][1] === $candidates[1][1]) {
            $this->warnings[] = "$name khớp nhiều học sinh (" . implode(', ', array_map(function ($c) { return $c[0]->lms_code; }, $candidates)) . ") → chọn {$match->lms_code}";
        }
        // Bản ghi gộp nhiều bé ("A và B") không dùng lại
        if ($match && preg_match('/\b(và|va)\b|\d{4}\)/u', mb_strtolower($match->name)) && $this->nameScore($name, $match->name) < 3) {
            $this->warnings[] = "$name: bản ghi {$match->lms_code} \"{$match->name}\" gộp nhiều bé → tạo học sinh mới, giữ nguyên bản ghi cũ";
            $match = null;
        }
        $parentPhone = $phones[0] ?? '';
        $mother = $f1 ? $f1['mother'] : ($extra['mother'] ?? '');
        $father = $f1 ? $f1['father'] : '';
        return [
            'key' => $key, 'name' => $name, 'phones' => $phones, 'year' => $f1['year'] ?? null,
            'mother' => $mother, 'father' => $father,
            'mother_phone' => $f1['mother_phone'] ?? $parentPhone, 'father_phone' => $f1['father_phone'] ?? '',
            'school' => $f1['school'] ?? '', 'address' => $f1['address'] ?? '', 'note' => $f1['note'] ?? '',
            'student_id' => $match ? (int)$match->id : null,
            'lms_code' => $match ? $match->lms_code : null,
            'db_name' => $match ? $match->name : null,
        ];
    }

    /* ============================ IN BÁO CÁO ============================ */

    private function printPlan($plan)
    {
        $this->info("\n===== 1. TÀI KHOẢN GIÁO VIÊN (role Giáo viên, cơ sở CS2, mật khẩu @12345678) =====");
        $this->table(['Mã', 'Tên', 'Email', 'Hành động'], array_map(function ($t) {
            return [$t['hrm_id'], $t['name'], $t['email'], $t['existing_id'] ? ($t['conflict'] ? 'TRÙNG MÃ – kiểm tra' : 'Đã có, gán thêm quyền') : 'Tạo mới'];
        }, $plan['teachers']));

        $this->info('===== 2. LỚP HỌC =====');
        $this->table(['Sheet', 'Tên lớp', 'GV', 'Thứ', 'Buổi trên sheet', 'Đã qua', 'Từ', 'Hành động'], array_map(function ($sheet, $c) {
            return [$sheet, $c['cls_name'], self::TEACHERS[$c['teacher']]['name'], implode(',', $c['days']), count($c['dates']), $c['past'], $c['dates'][0] ?? '', $c['existing_id'] ? 'Đã có' : 'Tạo mới'];
        }, array_keys($plan['classes']), $plan['classes']));

        $this->info('===== 3. HỌC SINH =====');
        $this->table(['Mã cũ', 'Tên chuẩn', 'Năm sinh', 'SĐT', 'Khớp DB', 'Tên trên DB'], array_map(function ($s) {
            return [$s['key'], $s['name'], $s['year'], implode(' / ', $s['phones']), $s['student_id'] ? $s['lms_code'] : 'TẠO MỚI', $s['db_name'] ? str_replace("\n", ' ', $s['db_name']) : ''];
        }, $plan['students']));

        $this->info('===== 4. HỢP ĐỒNG + ĐIỂM DANH + PHIẾU THU =====');
        $rows = [];
        $sum = ['must' => 0, 'paid' => 0, 'shs' => 0, 'pay' => 0];
        foreach ($plan['contracts'] as $c) {
            $st = $plan['students'][$c['student_key']];
            $classes = implode(' → ', array_map(function ($s) use ($plan) { return $plan['classes'][$s]['name']; }, $c['class_seq']));
            $rows[] = [
                $st['name'], $this->productName($c['cfg']['product']) . ($c['gift'] ? ' (tặng)' : '') . (!empty($c['cfg']['trial']) ? ' (học thử)' : ''),
                $c['total'] . ($c['cfg']['bonus'] ?? 0 ? " ({$c['cfg']['sessions']}+{$c['cfg']['bonus']})" : ''),
                $c['must'] === null ? '??' : number_format($c['must']), number_format($c['paid']), number_format($c['debt']),
                $classes ?: '—', $c['done'] . '/' . $c['left'], u::genStatusStudent($c['status'], 1),
                $c['existing'] ? $c['existing']->code : 'mới',
                $c['create_payment'] ? 'Tạo PT ' . number_format($c['paid']) : ($c['pending_receipts'] ? 'Chờ KT duyệt ' . number_format($c['pending_receipts']) : '—'),
                !empty($c['cfg']['pending']) ? 'CẦN XÁC NHẬN' : '',
            ];
            $sum['must'] += (int)$c['must']; $sum['paid'] += (int)$c['paid']; $sum['shs'] += count($c['sessions']);
            if ($c['create_payment']) $sum['pay'] += $c['paid'];
        }
        $this->table(['Học sinh', 'Sản phẩm', 'Buổi', 'Trị giá', 'Đã thu', 'Còn nợ', 'Lớp', 'Học/Còn', 'Trạng thái', 'HĐ', 'Phiếu thu', ''], $rows);
        $this->line(sprintf('Tổng: %d hợp đồng | trị giá %s | đã thu %s | phiếu thu tạo mới %s | %d dòng điểm danh',
            count($plan['contracts']), number_format($sum['must']), number_format($sum['paid']), number_format($sum['pay']), $sum['shs']));

        $this->info("===== 5. DỌN DỮ LIỆU =====");
        $this->line("Gán branch_id = 2 cho {$plan['parents_fix']} phụ huynh đang trống cơ sở (tmp_branch_id = 2)");

        $pend = array_filter($plan['contracts'], function ($c) { return !empty($c['cfg']['pending']); });
        if ($pend) {
            $this->warn("\n===== CẦN XÁC NHẬN =====");
            foreach ($pend as $c) $this->warn(' - ' . $c['cfg']['pending']);
        }
        if ($this->warnings) {
            $this->warn("\n===== CẢNH BÁO =====");
            foreach (array_unique($this->warnings) as $w) $this->line(' - ' . $w);
        }
    }

    /* ============================ GHI DB ============================ */

    private function runImport($plan)
    {
        $now = date('Y-m-d H:i:s');
        $this->batch = ['imported_at' => $now, 'inserted' => [], 'updated' => []];
        DB::beginTransaction();
        try {
            // 1. Giáo viên
            $teacherIds = [];
            foreach ($plan['teachers'] as $k => $t) {
                if ($t['existing_id']) { $teacherIds[$k] = (int)$t['existing_id']; continue; }
                $id = $this->insert('users', [
                    'name' => $t['name'], 'email' => $t['email'], 'hrm_id' => $t['hrm_id'],
                    'password' => Hash::make('@12345678'), 'menuroles' => 'user', 'status' => 1,
                    'role_name' => 'Giáo viên', 'branch_id' => self::BRANCH_ID, 'branch_name' => self::BRANCH_NAME,
                    'created_at' => $now, 'creator_id' => self::SYSTEM_USER,
                ]);
                DB::table('role_has_user')->insert(['role_id' => 36, 'user_id' => $id]);
                DB::table('branch_has_user')->insert(['branch_id' => self::BRANCH_ID, 'user_id' => $id]);
                $teacherIds[$k] = $id;
            }

            // 2. Lớp + lịch
            $classMap = [];
            foreach ($plan['classes'] as $sheet => $c) {
                $classMap[$sheet] = $this->ensureClass($c, $teacherIds[$c['teacher']], $now);
            }

            // 3. Học sinh
            $studentIds = [];
            foreach ($plan['students'] as $key => $s) {
                $needed = array_filter($plan['contracts'], function ($c) use ($key) { return $c['student_key'] === $key && (empty($c['cfg']['pending']) || !$this->option('skip-pending')); });
                if (!$needed) continue;
                $studentIds[$key] = $this->ensureStudent($s, $now);
            }

            // 4. Hợp đồng + điểm danh + phiếu thu
            $bar = $this->output->createProgressBar(count($plan['contracts']));
            foreach ($plan['contracts'] as $c) {
                $bar->advance();
                if (!empty($c['cfg']['pending']) && $this->option('skip-pending')) continue;
                $this->importContract($c, $studentIds[$c['student_key']], $classMap, $plan, $teacherIds, $now);
            }
            $bar->finish();
            $this->line('');

            // 5. Gán cơ sở cho phụ huynh CS2
            $ids = DB::table('crm_parents')->whereNull('branch_id')->where('tmp_branch_id', self::BRANCH_ID)->pluck('id')->all();
            foreach ($ids as $id) $this->update('crm_parents', $id, ['branch_id' => self::BRANCH_ID]);

            @mkdir(dirname(storage_path('app/' . self::BATCH_FILE)), 0777, true);
            file_put_contents(storage_path('app/' . self::BATCH_FILE), json_encode($this->batch, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->error('Lỗi, đã huỷ toàn bộ thay đổi: ' . $e->getMessage() . ' @' . $e->getFile() . ':' . $e->getLine());
            return 1;
        }

        $this->info("\n===== IMPORT CS2 THÀNH CÔNG =====");
        foreach ($this->batch['inserted'] as $t => $ids) $this->line(sprintf(' - %s: tạo mới %d', $t, count($ids)));
        foreach ($this->batch['updated'] as $t => $rows) $this->line(sprintf(' - %s: cập nhật %d', $t, count($rows)));
        $this->line('Batch: storage/app/' . self::BATCH_FILE . ' (dùng --rollback để hoàn tác)');
        return 0;
    }

    private function ensureClass($c, $teacherId, $now)
    {
        if ($c['existing_id']) {
            return ['id' => (int)$c['existing_id'], 'program_id' => $c['program'], 'product_id' => $c['product'], 'teacher_id' => $teacherId];
        }
        // Lịch: buổi trên sheet, nối thêm cho đủ 96 buổi
        $dates = $c['dates'];
        if (count($dates) < self::TOTAL_CLASS_SESSIONS && $dates) {
            $holidays = u::getPublicHolidays(self::BRANCH_ID, $c['product']);
            $more = u::calculatorSessionsByNumberOfSessions(date('Y-m-d', strtotime(end($dates) . ' +1 day')), self::TOTAL_CLASS_SESSIONS - count($dates), $holidays, $c['days']);
            $dates = array_merge($dates, data_get($more, 'dates', []));
        }
        $dates = array_values(array_unique($dates));
        sort($dates);
        $teacherName = DB::table('users')->where('id', $teacherId)->value('name');
        $id = $this->insert('classes', [
            'cls_name' => $c['cls_name'], 'product_id' => $c['product'], 'program_id' => $c['program'],
            'branch_id' => self::BRANCH_ID, 'teacher_id' => $teacherId, 'teacher_name' => $teacherName,
            'shift_name' => mb_substr(preg_replace('/^(Thời gian|Họ và tên giáo viên):\s*/u', '', $c['time']), 0, 190),
            'cls_startdate' => $dates[0] ?? null, 'cls_enddate' => end($dates) ?: null,
            'class_day' => implode(',', $c['days']), 'max_students' => 16, 'total_cycles' => 1, 'type' => 1, 'status' => 1,
            'creator_id' => self::SYSTEM_USER, 'created_at' => $now,
        ]);
        foreach ($c['days'] as $day) {
            DB::table('sessions')->insert(['class_id' => $id, 'shift_id' => null, 'room_id' => self::ROOM_ID, 'teacher_id' => $teacherId,
                'class_day' => $day, 'status' => 1, 'branch_id' => self::BRANCH_ID, 'creator_id' => self::SYSTEM_USER, 'created_at' => $now]);
        }
        DB::table('subject_has_class')->insert(['class_id' => $id, 'subject_id' => 1, 'session' => count($dates), 'stt' => 1, 'created_at' => $now]);
        $rows = [];
        foreach ($dates as $i => $d) {
            $rows[] = ['class_date' => $d, 'class_id' => $id, 'subject_id' => 1, 'class_stt' => $i + 1, 'subject_stt' => $i + 1, 'status' => 1,
                'branch_id' => self::BRANCH_ID, 'teacher_id' => $teacherId, 'created_at' => $now];
        }
        foreach (array_chunk($rows, 200) as $chunk) DB::table('schedules')->insert($chunk);
        return ['id' => $id, 'program_id' => $c['program'], 'product_id' => $c['product'], 'teacher_id' => $teacherId, 'dates' => $dates];
    }

    private function ensureStudent($s, $now)
    {
        $dob = $s['year'] ? $s['year'] . '-01-01' : null;
        $arr = u::explodeName($s['name']);
        $phone1 = $s['mother_phone'] ?: ($s['phones'][0] ?? '');
        $data = array_filter([
            'name' => $s['name'],
            'firstname' => data_get($arr, 'firstname'), 'midname' => data_get($arr, 'midname'), 'lastname' => data_get($arr, 'lastname'),
            'date_of_birth' => $dob, 'birthday_year_only' => $dob ? 1 : null,
            'gud_name1' => $s['mother'] ?: null, 'gud_mobile1' => $phone1 ?: null,
            'gud_name2' => $s['father'] ?: null, 'gud_mobile2' => $s['father_phone'] ?: null,
            'school' => $s['school'] ?: null, 'address' => $s['address'] ?: null,
        ], function ($v) { return $v !== null && $v !== ''; });

        // Phụ huynh CRM
        $parent = $phone1 ? DB::table('crm_parents')->where('mobile_1', $phone1)->first() : null;
        if ($parent) {
            $this->update('crm_parents', $parent->id, array_filter(['status' => 10, 'level' => 10, 'branch_id' => self::BRANCH_ID,
                'name' => ($s['mother'] ?: $s['father']) ?: null], function ($v) { return $v !== null; }));
            $parentId = (int)$parent->id;
        } else {
            $parentId = $this->insert('crm_parents', [
                'name' => $s['mother'] ?: ($s['father'] ?: 'PH ' . $s['name']), 'mobile_1' => $phone1 ?: null, 'mobile_2' => $s['father_phone'] ?: null,
                'address' => $s['address'] ?: null, 'branch_id' => self::BRANCH_ID, 'status' => 10, 'level' => 10,
                'creator_id' => self::SYSTEM_USER, 'owner_id' => self::EC_ID, 'note' => self::TAG, 'created_at' => $now,
            ]);
        }

        if ($s['student_id']) {
            $id = (int)$s['student_id'];
            $old = DB::table('students')->where('id', $id)->first();
            $this->update('students', $id, $data + ['branch_id' => self::BRANCH_ID, 'note' => trim(($old->note ? $old->note . ' | ' : '') . self::TAG . ' Mã cũ: ' . $s['key'])]);
        } else {
            $id = $this->insert('students', $data + [
                'lms_code' => '', 'gender' => 'M', 'type' => 0, 'branch_id' => self::BRANCH_ID, 'status' => 1,
                'avatar_url' => '/images/common/avatar-boy.svg', 'note' => self::TAG . ' Mã cũ: ' . $s['key'],
                'creator_id' => self::SYSTEM_USER, 'created_at' => $now,
            ]);
            DB::table('students')->where('id', $id)->update(['lms_code' => config('app.prefix_student_code', 'STK') . str_pad((string)$id, 6, '0', STR_PAD_LEFT)]);
        }

        // Học sinh CRM ↔ LMS
        $crm = DB::table('crm_students')->where('lms_id', $id)->first()
            ?: DB::table('crm_students')->where('parent_id', $parentId)->where('name', $s['name'])->first();
        if ($crm) {
            $this->update('crm_students', $crm->id, ['lms_id' => $id, 'parent_id' => $parentId]);
        } else {
            $this->insert('crm_students', ['parent_id' => $parentId, 'name' => $s['name'], 'birthday' => $dob, 'birthday_year_only' => $dob ? 1 : 0,
                'status' => 3, 'lms_id' => $id, 'creator_id' => self::SYSTEM_USER, 'created_at' => $now]);
        }

        $term = DB::table('term_student_user')->where('student_id', $id)->where('status', 1)->first();
        $termData = ['ec_id' => self::EC_ID, 'ec_leader_id' => self::EC_LEADER_ID, 'branch_id' => self::BRANCH_ID];
        if ($term) $this->update('term_student_user', $term->id, $termData);
        else $this->insert('term_student_user', $termData + ['student_id' => $id, 'status' => 1, 'creator_id' => self::SYSTEM_USER, 'created_at' => $now]);
        return $id;
    }

    private function importContract($c, $studentId, $classMap, $plan, $teacherIds, $now)
    {
        $cfg = $c['cfg'];
        $currentSheet = $c['class_seq'] ? end($c['class_seq']) : null;
        $cls = $currentSheet ? $classMap[$currentSheet] : null;
        $fee = DB::table('tuition_fee')->where('id', $cfg['fee'])->first();
        $must = (int)$c['must'];
        $paidNow = $c['existing'] && $c['pending_receipts'] ? (int)$c['existing']->total_charged : (int)$c['paid'];
        $debt = max(0, $must - $paidNow);
        $note = trim(self::TAG . ' ' . ($cfg['note'] ?? '') . ($c['gift'] ? ' ' . $cfg['gift'] . ' (0đ)' : ''));

        $data = [
            'type' => !empty($cfg['trial']) ? 0 : 1,
            'student_id' => $studentId, 'branch_id' => self::BRANCH_ID,
            'ec_id' => self::EC_ID, 'ec_leader_id' => self::EC_LEADER_ID,
            'product_id' => $cfg['product'], 'program_id' => $cls ? $cls['program_id'] : null,
            'tuition_fee_id' => $cfg['fee'], 'init_tuition_fee_id' => $cfg['fee'],
            'init_tuition_fee_amount' => $c['gift'] ? 0 : (int)data_get($fee, 'price', $must),
            'init_tuition_fee_receivable' => $must, 'tuition_receivable' => $must,
            'init_tuition_fee_session' => (int)$cfg['sessions'],
            'total_discount' => $c['gift'] ? 0 : max(0, (int)data_get($fee, 'price', $must) - $must),
            'must_charge' => $must, 'total_charged' => $paidNow, 'init_total_charged' => $paidNow, 'debt_amount' => $debt,
            'total_sessions' => $c['total'], 'real_sessions' => (int)$cfg['sessions'], 'bonus_sessions' => (int)($cfg['bonus'] ?? 0),
            'summary_sessions' => $c['summary'], 'reservable_sessions' => (int)floor((int)$cfg['sessions'] / max(1, (int)config('app.num_session_of_reservable', 24))),
            'class_id' => $cls ? $cls['id'] : null,
            'start_date' => $c['start'], 'enrolment_start_date' => $c['start'],
            'status' => $c['status'], 'gift_note' => $c['gift_note'] ?: null,
            'cm_id' => null, 'count_recharge' => 0,
        ];

        if ($c['existing']) {
            $contractId = (int)$c['existing']->id;
            $data['note'] = trim(($c['existing']->note ? $c['existing']->note . ' | ' : '') . $note);
            $data['updated_at'] = $now;
            $data['updator_id'] = self::SYSTEM_USER;
            $this->update('contracts', $contractId, $data);
        } else {
            $contractId = $this->insert('contracts', $data + ['code' => '', 'note' => $note, 'creator_id' => self::SYSTEM_USER, 'created_at' => $now]);
            DB::table('contracts')->where('id', $contractId)->update(['code' => config('app.prefix_contract_code', 'C') . str_pad((string)$contractId, 6, '0', STR_PAD_LEFT)]);
        }

        // Điểm danh từng buổi (lớp theo đúng sheet đã học)
        $rows = [];
        $stt = 0;
        foreach ($c['sessions'] as $date => $s) {
            $clsAt = $classMap[$s['sheet']];
            $isBL = $s['mark'] === 'BL';
            if (!$isBL) $stt++;
            $rows[] = [
                'student_id' => $studentId, 'branch_id' => self::BRANCH_ID, 'class_id' => $clsAt['id'], 'contract_id' => $contractId,
                'product_id' => $cfg['product'], 'program_id' => $clsAt['program_id'], 'subject_id' => 1, 'subject_stt' => max(1, $stt),
                'class_date' => $date, 'created_at' => $now, 'updator_id' => self::SYSTEM_USER,
                'status' => $isBL ? 2 : 1,                       // 1: buổi tính phí, 2: bảo lưu buổi
                'attendance_status' => $s['mark'] === 'X' ? 1 : ($s['mark'] === 'O' ? 2 : 0),
                'note' => $isBL ? 'Bảo lưu (BL) theo sổ điểm danh' : null,
            ];
        }
        foreach ($rows as $r) $this->insert('schedule_has_student', $r);

        // Chuyển lớp
        if (count($c['class_seq']) > 1) {
            $fromSheet = $c['class_seq'][0];
            $transferDate = null;
            foreach ($c['sessions'] as $d => $s) if ($s['sheet'] === $currentSheet) { $transferDate = $d; break; }
            $this->insert('class_transfer', [
                'student_id' => $studentId, 'contract_id' => $contractId,
                'from_class_id' => $classMap[$fromSheet]['id'], 'from_branch_id' => self::BRANCH_ID, 'from_product_id' => $cfg['product'], 'from_program_id' => $classMap[$fromSheet]['program_id'],
                'to_class_id' => $cls['id'], 'to_branch_id' => self::BRANCH_ID, 'to_product_id' => $cfg['product'], 'to_program_id' => $cls['program_id'],
                'transfer_date' => $transferDate, 'status' => 2, 'note' => self::TAG . ' Chuyển lớp theo sổ điểm danh',
                'creator_id' => self::SYSTEM_USER, 'created_at' => $now,
            ]);
        }

        // Phiếu thu (đã thu theo file 1, hình thức quẹt thẻ)
        if ($c['create_payment']) {
            $tmpId = $this->insert('tmp_payments', [
                'contract_id' => $contractId, 'charge_amount' => (int)$c['paid'], 'debt_amount' => $debt, 'total_charged' => (int)$c['paid'],
                'charge_date' => $c['charge_date'], 'method' => self::METHOD_CARD, 'note' => self::TAG . ' Thu phí theo sổ trung tâm',
                'created_at' => $now, 'creator_id' => self::SYSTEM_USER, 'status' => 1, 'approved_at' => $now, 'approver_id' => self::SYSTEM_USER,
            ]);
            $code = 'PT' . str_pad((string)$tmpId, 6, '0', STR_PAD_LEFT);
            DB::table('tmp_payments')->where('id', $tmpId)->update(['code' => $code]);
            $this->insert('payments', [
                'code' => $code, 'contract_id' => $contractId, 'student_id' => $studentId, 'branch_id' => self::BRANCH_ID,
                'ec_id' => self::EC_ID, 'method' => self::METHOD_CARD, 'must_charge' => $must, 'amount' => (int)$c['paid'],
                'total' => (int)$c['paid'], 'debt' => $debt, 'charge_date' => $c['charge_date'], 'count' => 0,
                'note' => self::TAG . ' Thu phí theo sổ trung tâm', 'created_at' => $now, 'creator_id' => self::SYSTEM_USER,
            ]);
        }

        // Số buổi + ngày học cuối
        $done = DB::table('schedule_has_student')->where('contract_id', $contractId)->where('status', 1)->count();
        $left = max(0, $c['summary'] - $done);
        $last = $c['sessions'] ? max(array_keys($c['sessions'])) : null;
        $upd = ['done_sessions' => $done, 'left_sessions' => $left, 'last_done_sessions' => 0];
        if ($c['status'] === 6 && $cls && $left > 0) {
            $holidays = u::getPublicHolidays(self::BRANCH_ID, $cfg['product']);
            $calc = u::calculatorSessionsByNumberOfSessions(date('Y-m-d', strtotime('+1 day')), $left, $holidays, explode(',', DB::table('classes')->where('id', $cls['id'])->value('class_day')));
            $upd['enrolment_last_date'] = data_get($calc, 'end_date');
        } else {
            $upd['enrolment_last_date'] = $last;
            if ($c['status'] === 7) $upd['completed_date'] = $last;
        }
        DB::table('contracts')->where('id', $contractId)->update($upd);

        // Giáo viên phụ trách trên term_student_user
        if ($cls) {
            $term = DB::table('term_student_user')->where('student_id', $studentId)->where('status', 1)->first();
            if ($term) $this->update('term_student_user', $term->id, ['teacher_id' => $cls['teacher_id']]);
        }
        u::addLogContracts($contractId);
    }

    /* ============================ ROLLBACK ============================ */

    private function rollback()
    {
        $path = storage_path('app/' . self::BATCH_FILE);
        if (!file_exists($path)) { $this->warn('Không có batch import CS2 để hoàn tác.'); return 0; }
        $b = json_decode(file_get_contents($path), true);
        DB::beginTransaction();
        try {
            $contractIds = array_merge($b['inserted']['contracts'] ?? [], array_keys($b['updated']['contracts'] ?? []));
            if ($contractIds) DB::table('log_contracts')->whereIn('contract_id', $contractIds)->where('created_at', '>=', $b['imported_at'])->delete();
            $order = ['payments', 'tmp_payments', 'class_transfer', 'schedule_has_student', 'contracts', 'term_student_user', 'crm_students', 'students', 'crm_parents', 'classes', 'users'];
            foreach ($order as $t) {
                $ids = $b['inserted'][$t] ?? [];
                if (!$ids) continue;
                if ($t === 'classes') {
                    DB::table('schedules')->whereIn('class_id', $ids)->delete();
                    DB::table('sessions')->whereIn('class_id', $ids)->delete();
                    DB::table('subject_has_class')->whereIn('class_id', $ids)->delete();
                    DB::table('schedule_has_student')->whereIn('class_id', $ids)->delete(); // gồm dòng cron sinh thêm
                }
                if ($t === 'users') {
                    DB::table('role_has_user')->whereIn('user_id', $ids)->delete();
                    DB::table('branch_has_user')->whereIn('user_id', $ids)->delete();
                }
                DB::table($t)->whereIn('id', $ids)->delete();
                $this->line(" - Xoá $t: " . count($ids));
            }
            foreach ($b['updated'] ?? [] as $t => $rows) {
                foreach ($rows as $id => $old) DB::table($t)->where('id', $id)->update($old);
                $this->line(" - Khôi phục $t: " . count($rows));
            }
            // Dòng lịch học cron sinh thêm cho HĐ cũ đã được gán lớp
            $updatedContracts = array_keys($b['updated']['contracts'] ?? []);
            if ($updatedContracts) DB::table('schedule_has_student')->whereIn('contract_id', $updatedContracts)->where('created_at', '>=', $b['imported_at'])->delete();
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->error('Rollback lỗi: ' . $e->getMessage());
            return 1;
        }
        rename($path, $path . '.rolledback-' . date('YmdHis'));
        $this->info('Đã hoàn tác batch import CS2.');
        return 0;
    }

    /* ============================ TIỆN ÍCH ============================ */

    private function insert($table, array $data)
    {
        $id = DB::table($table)->insertGetId($data);
        $this->batch['inserted'][$table][] = $id;
        return $id;
    }

    /** Cập nhật và lưu giá trị cũ (lần đầu) để rollback */
    private function update($table, $id, array $data)
    {
        if (!$data) return;
        if (in_array($id, $this->batch['inserted'][$table] ?? [], true)) {
            DB::table($table)->where('id', $id)->update($data);
            return;
        }
        $old = (array)DB::table($table)->where('id', $id)->first(array_keys($data));
        $saved = $this->batch['updated'][$table][$id] ?? [];
        foreach ($old as $k => $v) if (!array_key_exists($k, $saved)) $saved[$k] = $v;
        $this->batch['updated'][$table][$id] = $saved;
        DB::table($table)->where('id', $id)->update($data);
    }

    private function date($v)
    {
        if ($v === null || $v === '') return null;
        if ($v instanceof \DateTimeInterface) return $v->format('Y-m-d');
        if (is_numeric($v) && $v > 20000 && $v < 80000) return ExcelDate::excelToDateTimeObject($v)->format('Y-m-d');
        $t = strtotime((string)$v);
        return $t ? date('Y-m-d', $t) : null;
    }

    private function phone($v)
    {
        $p = preg_replace('/\D/', '', (string)$v);
        if (strlen($p) === 9) $p = '0' . $p;
        return strlen($p) >= 9 ? $p : '';
    }

    private function cleanName($n)
    {
        $n = preg_replace('/\(.*?\)|\(.*$/u', '', (string)$n);
        $n = preg_replace('/^\s*(bé|be)\s+/iu', '', $n);
        return trim(preg_replace('/\s+/u', ' ', $n));
    }

    private function norm($n)
    {
        return mb_strtolower($this->cleanName($n), 'UTF-8');
    }

    /** Điểm giống tên: 3 = trùng hoàn toàn, 2 = trùng 2 chữ cuối, 1 = trùng tên gọi */
    private function nameScore($a, $b)
    {
        $a = $this->norm($a); $b = $this->norm($b);
        if ($a === '' || $b === '') return 0;
        if ($a === $b) return 3;
        $ta = explode(' ', $a); $tb = explode(' ', $b);
        $la = array_slice($ta, -2); $lb = array_slice($tb, -2);
        if (count($la) === 2 && $la === $lb) return 2;
        if (end($ta) === end($tb)) return 1;
        return 0;
    }

    private function productName($id)
    {
        return [1 => 'Ucrea', 2 => 'Bright I.G', 3 => 'Tiếng Anh', 4 => 'TTH'][$id] ?? '#' . $id;
    }
}
