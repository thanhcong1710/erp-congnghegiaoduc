<?php

namespace App\Services;

use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Shared\Date;

/**
 * Class StudentDataTransformer
 *
 * Provides static methods to normalize raw Excel rows into a structured array
 * that can be exported as JSON or fed directly to the import service.
 */
class StudentDataTransformer
{
    /**
     * Clean full name – remove any parenthetical or dash‑separated notes.
     */
    public static function cleanName(string $raw): string
    {
        // Loại bỏ mọi thứ từ các dấu ngoặc hoặc gạch nối (cả gạch nối ascii và gạch ngang unicode) trở đi
        $clean = preg_replace('/\s*[\(\-\[–—_].*$/us', '', $raw);
        
        // Loại bỏ thêm nếu họ viết liền số điện thoại phía sau mà không có dấu gạch (vd: "Nguyễn Văn A 0981234567")
        $clean = preg_replace('/\s+\d{6,}.*$/us', '', $clean);
        
        // Xóa các ký tự xuống dòng nếu còn sót
        $clean = str_replace(["\r", "\n"], ' ', $clean);

        return trim($clean);
    }

    /**
     * Clean class name – keep only the first token (e.g. "OS16" from "OS16 Nghỉ ...").
     */
    public static function cleanClassName(string $raw): string
    {
        $parts = preg_split('/[\s,\-]+/', $raw);
        return trim($parts[0] ?? $raw);
    }

    /**
     * Normalise phone – strip spaces.
     */
    public static function normalizePhone(string $raw): string
    {
        return preg_replace('/\s+/', '', $raw);
    }

    /**
     * Convert a raw Excel row (associative array with heading keys) into a
     * normalized representation used for JSON export / import.
     *
     * Expected columns (case‑insensitive, auto‑converted to snake_case by
     * Laravel‑Excel):
     * - sdt
     * - giá_khóa_học
     * - địa_chỉ_nhận_sách
     * - họ và tên / ho_va_ten
     * - tên lớp / lớp đăng ký
     * - ngày khai giảng (optional)
     * - lớp đăng ký (optional – may contain status like "Chờ lớp")
     */
    public static function transformRow(array $row): array
    {
        $phone = self::normalizePhone($row['sdt'] ?? '');
        $priceRaw = $row['giá_khóa_học'] ?? ($row['gia_khoa_hoc'] ?? 0);
        $price = floatval(str_replace([',', ' '], '', $priceRaw));
        $address = $row['địa_chỉ_nhận_sách'] ?? ($row['dia_chi_nhan_sach'] ?? '');
        $rawName = $row['họ và tên'] ?? ($row['ho_va_ten'] ?? '');
        $rawClass = $row['tên lớp'] ?? ($row['ten_lop'] ?? ($row['lớp đăng ký'] ?? ($row['lop_dang_ky'] ?? '')));
        $rawStatus = $row['lớp đăng ký'] ?? ($row['lop_dang_ky'] ?? '');
        $startDate = $row['ngày khai giảng'] ?? ($row['ngay_khai_giang'] ?? null);

        $teamKinhDoanh = $row['team kinh doanh'] ?? ($row['team_kinh_doanh'] ?? '');
        $pay1AmountRaw = $row['học phí đã nộp đợt 1'] ?? ($row['hoc_phi_da_nop_dot_1'] ?? 0);
        $pay1Date = $row['ngày ck đợt 1'] ?? ($row['ngay_ck_dot_1'] ?? null);
        $pay2AmountRaw = $row['học phí đã nộp đợt 2'] ?? ($row['hoc_phi_da_nop_dot_2'] ?? 0);
        $pay2Date = $row['ngày_ck_đợt_2'] ?? ($row['ngay_ck_dot_2'] ?? null);

        $course = $row['khóa học đăng kí'] ?? ($row['khoa_hoc_dang_ki'] ?? '');
        $linkFb = $row['link_fb'] ?? '';
        $email = $row['email'] ?? '';
        $saleMem = $row['thanh_vien_sale'] ?? ($row['sale_member'] ?? ($row['sale'] ?? ''));
        $shipNote = $row['ghi_chu_van_don'] ?? ($row['shipping_note'] ?? '');

        $pay1Amount = floatval(str_replace([',', ' '], '', $pay1AmountRaw));
        $pay2Amount = floatval(str_replace([',', ' '], '', $pay2AmountRaw));

        $name = self::cleanName((string) $rawName);
        $className = self::cleanClassName((string) $rawClass);
        $statusWhenNoStart = trim(strtolower((string) $rawStatus)) === 'chờ lớp' ? '3' : '4';

        return [
            'phone'            => $phone,
            'price'            => $price,
            'address'          => $address,
            'name'             => $name,
            'class_name'       => $className,
            'course'           => trim((string)$course),
            'raw_status'       => $statusWhenNoStart,
            'start_date'       => self::safeParseDate($startDate),
            'team_kinh_doanh'  => trim($teamKinhDoanh),
            'sale_team'        => trim($teamKinhDoanh),
            'sale_member'      => trim((string)$saleMem),
            'shipping_note'    => trim((string)$shipNote),
            'link_fb'          => trim((string)$linkFb),
            'email'            => trim((string)$email),
            'payment_1_amount' => $pay1Amount,
            'payment_1_date'   => self::safeParseDate($pay1Date),
            'payment_2_amount' => $pay2Amount,
            'payment_2_date'   => self::safeParseDate($pay2Date),
            'raw_start_date'   => $startDate,
        ];
    }

    /**
     * Parse date safely, ignoring Excel formula errors like #NAME?
     */
    public static function safeParseDate($dateStr): ?string
    {
        if (empty($dateStr)) {
            return null;
        }
        $dateStr = trim((string)$dateStr);
        if (str_starts_with($dateStr, '#')) {
            return null; // Ignore #NAME?, #REF!, #VALUE!
        }
        try {
            // Excel serial date (e.g., 45110)
            if (is_numeric($dateStr)) {
                return Carbon::instance(Date::excelToDateTimeObject($dateStr))->format('Y-m-d');
            }
            
            // Check for common Vietnamese format DD/MM/YYYY
            if (preg_match('/^(\d{1,2})[\/\-\.](\d{1,2})[\/\-\.](\d{4})$/', $dateStr, $m)) {
                return Carbon::createFromDate($m[3], $m[2], $m[1])->format('Y-m-d');
            }
            
            return Carbon::parse($dateStr)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }
}
?>
