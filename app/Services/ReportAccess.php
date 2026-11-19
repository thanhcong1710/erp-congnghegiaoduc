<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

/**
 * Danh mục báo cáo tập trung (/lms/reports) + phân quyền theo role.
 * - Admin (999999) xem được tất cả báo cáo.
 * - Role khác chỉ thấy báo cáo có role của mình trong 'roles'.
 * Muốn đổi quyền xem báo cáo cho role nào → sửa mảng 'roles' bên dưới.
 */
class ReportAccess
{
    const ROLE_ADMIN = 999999;
    const ROLE_CEO = 686868;
    const ROLE_EC = 68;
    const ROLE_EC_LEADER = 69;
    const ROLE_MARKETING = 70;
    const ROLE_CM = 55;
    const ROLE_CM_LEADER = 56;
    const ROLE_TEACHER = 36;
    const ROLE_TEACHER_LEADER = 37;
    const ROLE_TA = 54;
    const ROLE_ACCOUNTANT = 1000002;

    const GROUPS = [
        'sale' => ['label' => 'Kinh doanh (Sale)', 'icon' => 'fa-solid fa-sack-dollar'],
        'marketing' => ['label' => 'Marketing', 'icon' => 'fa-solid fa-bullhorn'],
        'cm' => ['label' => 'Chăm sóc khách hàng (CSKH)', 'icon' => 'fa-solid fa-heart'],
        'teacher' => ['label' => 'Giáo viên & Học thuật', 'icon' => 'fa-solid fa-chalkboard-user'],
        'finance' => ['label' => 'Kế toán & Tài chính', 'icon' => 'fa-solid fa-wallet'],
    ];

    /**
     * code => thông tin báo cáo.
     * filters: các bộ lọc FE hiển thị cho báo cáo dạng chung (05+).
     */
    const CATALOG = [
        'overview' => [
            'number' => '01', 'group' => 'sale', 'tag' => 'Doanh thu', 'tag_class' => 'tag-blue',
            'title' => 'BÁO CÁO DOANH THU - TỔNG QUAN',
            'description' => 'Thống kê tổng quan theo từng nhân viên: Lượng data, Hợp đồng (%), Doanh số tạm tính, Doanh số thực thu, Thực thu/data và Tổng nợ cần thu.',
            'roles' => [self::ROLE_CEO, self::ROLE_EC, self::ROLE_EC_LEADER],
        ],
        'team' => [
            'number' => '02', 'group' => 'sale', 'tag' => 'Đội nhóm & KPI', 'tag_class' => 'tag-emerald',
            'title' => 'BÁO CÁO DOANH THU - ĐỘI NHÓM (TEAM SALE)',
            'description' => 'Thống kê kết quả kinh doanh theo Đội nhóm (Leader Sale), theo dõi Chỉ tiêu KPI, Thực đạt và Phần trăm hoàn thành của từng thành viên trong nhóm.',
            'roles' => [self::ROLE_CEO, self::ROLE_EC, self::ROLE_EC_LEADER],
        ],
        'business_source' => [
            'number' => '03', 'group' => 'sale', 'tag' => 'Kinh doanh', 'tag_class' => 'tag-purple',
            'title' => 'BÁO CÁO KINH DOANH THEO NGUỒN KHÁCH HÀNG & CHUYỂN ĐỔI DATA',
            'description' => 'Phân tích doanh thu theo từng Nguồn khách hàng (MKT, B2B, KH giới thiệu...) & Kênh nguồn ngách; xem theo Cá nhân Sale, Team, Cơ sở hoặc Tổng Trung tâm.',
            'roles' => [self::ROLE_CEO, self::ROLE_EC, self::ROLE_EC_LEADER, self::ROLE_MARKETING],
        ],
        'business_product' => [
            'number' => '04', 'group' => 'sale', 'tag' => 'Sản phẩm', 'tag_class' => 'tag-amber',
            'title' => 'BÁO CÁO DOANH THU CHI TIẾT THEO TỪNG SẢN PHẨM (KHÓA HỌC)',
            'description' => 'Thống kê số lượng hợp đồng, tỷ trọng, doanh thu theo nguồn, doanh số tạm tính, thực thu và công nợ của từng sản phẩm (Ucrea, Bright I.G, Tiếng Anh, Tiền Tiểu Học...).',
            'roles' => [self::ROLE_CEO, self::ROLE_EC, self::ROLE_EC_LEADER, self::ROLE_ACCOUNTANT],
        ],

        // ===== Marketing =====
        'mkt_source' => [
            'number' => '05', 'group' => 'marketing', 'tag' => 'Hiệu quả nguồn', 'tag_class' => 'tag-pink',
            'title' => 'BÁO CÁO HIỆU QUẢ NGUỒN & KÊNH MARKETING',
            'description' => 'Lượng data mới, hẹn checkin, đã checkin, mua gói, data xấu, tỷ lệ chuyển đổi và doanh số hợp đồng theo Nguồn / Kênh ngách / Cơ sở / Người tạo data.',
            'roles' => [self::ROLE_CEO, self::ROLE_MARKETING],
            'filters' => ['date', 'branch', 'source', 'group_by'],
            'group_by' => [
                ['value' => 'source', 'label' => 'Theo Nguồn'],
                ['value' => 'source_detail', 'label' => 'Theo Kênh ngách'],
                ['value' => 'branch', 'label' => 'Theo Cơ sở'],
                ['value' => 'creator', 'label' => 'Theo Người tạo data'],
            ],
        ],
        'mkt_trend' => [
            'number' => '06', 'group' => 'marketing', 'tag' => 'Data theo thời gian', 'tag_class' => 'tag-pink',
            'title' => 'BÁO CÁO DATA MỚI THEO THỜI GIAN & NGUỒN',
            'description' => 'Số data mới theo Ngày / Tuần / Tháng, tách theo từng nguồn khách hàng; kèm số đã checkin, đã mua gói và tỷ lệ chuyển đổi của từng kỳ.',
            'roles' => [self::ROLE_CEO, self::ROLE_MARKETING],
            'filters' => ['date', 'branch', 'source', 'group_by'],
            'group_by' => [
                ['value' => 'day', 'label' => 'Theo Ngày'],
                ['value' => 'week', 'label' => 'Theo Tuần'],
                ['value' => 'month', 'label' => 'Theo Tháng'],
            ],
        ],

        // ===== CSKH =====
        'cm_summary' => [
            'number' => '07', 'group' => 'cm', 'tag' => 'Học sinh', 'tag_class' => 'tag-rose',
            'title' => 'BÁO CÁO TỔNG HỢP HỌC SINH THEO CSKH',
            'description' => 'Theo từng nhân viên CSKH: số lớp, HS đang học, chờ xếp lớp, bảo lưu, sắp hết phí, hết phí & tái phí trong kỳ, tỷ lệ tái phí, chuyên cần và công nợ.',
            'roles' => [self::ROLE_CEO, self::ROLE_CM, self::ROLE_CM_LEADER],
            'filters' => ['date', 'branch', 'staff'],
        ],
        'cm_renew' => [
            'number' => '08', 'group' => 'cm', 'tag' => 'Tái phí', 'tag_class' => 'tag-rose',
            'title' => 'DANH SÁCH HỌC SINH SẮP HẾT PHÍ & TÌNH TRẠNG TÁI PHÍ',
            'description' => 'Danh sách HS còn ít buổi / đã hết phí trong kỳ kèm lớp, CSKH, SĐT phụ huynh, ngày học cuối và trạng thái đã tái phí hay chưa để CSKH chủ động chăm sóc.',
            'roles' => [self::ROLE_CEO, self::ROLE_CM, self::ROLE_CM_LEADER],
            'filters' => ['date', 'branch', 'staff', 'renew_type', 'threshold'],
        ],
        'attendance_class' => [
            'number' => '09', 'group' => 'cm', 'tag' => 'Chuyên cần', 'tag_class' => 'tag-teal',
            'title' => 'BÁO CÁO CHUYÊN CẦN THEO LỚP',
            'description' => 'Theo từng lớp: sĩ số, số buổi đã học, lượt có mặt / vắng / chưa điểm danh và tỷ lệ chuyên cần trong kỳ.',
            'roles' => [self::ROLE_CEO, self::ROLE_CM, self::ROLE_CM_LEADER, self::ROLE_TEACHER, self::ROLE_TEACHER_LEADER, self::ROLE_TA],
            'filters' => ['date', 'branch', 'staff'],
        ],

        // ===== Giáo viên =====
        'teacher_workload' => [
            'number' => '10', 'group' => 'teacher', 'tag' => 'Giờ dạy', 'tag_class' => 'tag-indigo',
            'title' => 'BÁO CÁO BUỔI DẠY & NHẬN XÉT CỦA GIÁO VIÊN',
            'description' => 'Theo từng giáo viên / trợ giảng: số lớp, buổi dạy chính, buổi trợ giảng, buổi chưa điểm danh, số tuần cần nhận xét, đã hoàn thành, đã gửi phụ huynh.',
            'roles' => [self::ROLE_CEO, self::ROLE_TEACHER, self::ROLE_TEACHER_LEADER, self::ROLE_TA],
            'filters' => ['date', 'branch', 'staff'],
        ],

        // ===== Kế toán =====
        'finance_receipts' => [
            'number' => '11', 'group' => 'finance', 'tag' => 'Thu tiền', 'tag_class' => 'tag-green',
            'title' => 'SỔ CHI TIẾT THU TIỀN HỌC PHÍ',
            'description' => 'Chi tiết phiếu thu đã duyệt trong kỳ: ngày thu, học sinh, hợp đồng, sản phẩm, cơ sở, hình thức thanh toán, số tiền, Sale hưởng doanh số; tổng hợp theo hình thức thu.',
            'roles' => [self::ROLE_CEO, self::ROLE_ACCOUNTANT],
            'filters' => ['date', 'branch', 'method', 'product'],
        ],
        'finance_debt' => [
            'number' => '12', 'group' => 'finance', 'tag' => 'Công nợ', 'tag_class' => 'tag-red',
            'title' => 'BÁO CÁO CÔNG NỢ HỌC PHÍ',
            'description' => 'Danh sách hợp đồng còn nợ học phí: phải thu, đã thu, còn nợ, số ngày nợ, trạng thái học, Sale phụ trách; tổng hợp nợ theo độ tuổi nợ.',
            'roles' => [self::ROLE_CEO, self::ROLE_ACCOUNTANT],
            'filters' => ['branch', 'product'],
        ],
    ];

    /** Role của user */
    public static function rolesOf($userId)
    {
        return array_map(function ($r) {
            return (int)$r->role_id;
        }, DB::select("SELECT role_id FROM role_has_user WHERE user_id = ?", [(int)$userId]));
    }

    public static function isAdmin($userId)
    {
        return in_array(self::ROLE_ADMIN, self::rolesOf($userId), true);
    }

    public static function can($userId, $code)
    {
        if (!isset(self::CATALOG[$code])) return false;
        $roles = self::rolesOf($userId);
        if (in_array(self::ROLE_ADMIN, $roles, true)) return true;
        return (bool)array_intersect($roles, self::CATALOG[$code]['roles']);
    }

    public static function canAny($userId, array $codes)
    {
        foreach ($codes as $c) {
            if (self::can($userId, $c)) return true;
        }
        return false;
    }

    /** Danh mục báo cáo user được xem, gom theo nhóm */
    public static function catalogFor($userId)
    {
        $roles = self::rolesOf($userId);
        $admin = in_array(self::ROLE_ADMIN, $roles, true);
        $groups = [];
        foreach (self::GROUPS as $gKey => $g) {
            $groups[$gKey] = ['key' => $gKey, 'label' => $g['label'], 'icon' => $g['icon'], 'reports' => []];
        }
        foreach (self::CATALOG as $code => $r) {
            if (!$admin && !array_intersect($roles, $r['roles'])) continue;
            $groups[$r['group']]['reports'][] = [
                'code' => $code,
                'number' => $r['number'],
                'title' => $r['title'],
                'description' => $r['description'],
                'tag' => $r['tag'],
                'tagClass' => $r['tag_class'],
                'filters' => $r['filters'] ?? [],
                'group_by' => $r['group_by'] ?? [],
                'role_names' => self::roleNames($r['roles']),
            ];
        }
        return array_values(array_filter($groups, function ($g) {
            return count($g['reports']) > 0;
        }));
    }

    public static function roleNames(array $roles)
    {
        $map = [
            self::ROLE_CEO => 'GĐ trung tâm', self::ROLE_EC => 'Sale', self::ROLE_EC_LEADER => 'Leader Sale',
            self::ROLE_MARKETING => 'Marketing', self::ROLE_CM => 'CSKH', self::ROLE_CM_LEADER => 'Leader CSKH',
            self::ROLE_TEACHER => 'Giáo viên', self::ROLE_TEACHER_LEADER => 'Leader GV', self::ROLE_TA => 'Trợ giảng',
            self::ROLE_ACCOUNTANT => 'Kế toán',
        ];
        $out = ['Admin'];
        foreach ($roles as $r) {
            if (isset($map[$r])) $out[] = $map[$r];
        }
        return $out;
    }
}
