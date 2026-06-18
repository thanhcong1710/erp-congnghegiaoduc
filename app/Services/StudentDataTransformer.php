<?php

namespace App\Services;

use Carbon\Carbon;

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
        // Remove everything after '(' or '-' or '['
        $clean = preg_replace('/\s*[\(\-\[].*$/', '', $raw);
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
        $priceRaw = $row['giá_khóa_học'] ?? 0;
        $price = floatval(str_replace([',', ' '], '', $priceRaw));
        $address = $row['địa_chỉ_nhận_sách'] ?? '';
        $rawName = $row['họ và tên'] ?? ($row['ho_va_ten'] ?? '');
        $rawClass = $row['tên lớp'] ?? ($row['lớp đăng ký'] ?? '');
        $rawStatus = $row['lớp đăng ký'] ?? '';
        $startDate = $row['ngày khai giảng'] ?? null;

        $name = self::cleanName((string) $rawName);
        $className = self::cleanClassName((string) $rawClass);
        $statusWhenNoStart = trim(strtolower((string) $rawStatus)) === 'chờ lớp' ? '3' : '4';

        return [
            'phone'            => $phone,
            'price'            => $price,
            'address'          => $address,
            'name'             => $name,
            'class_name'       => $className,
            'raw_status'       => $statusWhenNoStart,
            'start_date'       => $startDate ? Carbon::parse($startDate)->format('Y-m-d') : null,
        ];
    }
}
?>
