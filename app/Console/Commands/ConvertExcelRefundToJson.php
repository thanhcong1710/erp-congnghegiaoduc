<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class ConvertExcelRefundToJson extends Command
{
    protected $signature = 'erp:convert-excel-refund-to-json
                            {file : Đường dẫn tới file Excel (.xlsx, .xls)}
                            {--output=storage/import/refund_students.json : Đường dẫn lưu file JSON đầu ra}';

    protected $description = 'Convert Excel refund file (Col A: Name, Col B: Phone, Col D: Amount, Col E: Refund Date) to JSON';

    public function handle(): int
    {
        $file = $this->argument('file');
        $outputFile = $this->option('output');

        if (!file_exists($file)) {
            $this->error("Không tìm thấy file Excel tại: {$file}");
            return 1;
        }

        $this->info("Đang đọc file Excel: {$file}");

        try {
            $reader = IOFactory::createReaderForFile($file);
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($file);
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray(null, true, false, false);
        } catch (\Exception $e) {
            $this->error("Lỗi khi đọc file Excel: " . $e->getMessage());
            return 1;
        }

        $result = [];
        $skippedCount = 0;

        foreach ($rows as $index => $row) {
            $nameRaw = trim((string)($row[0] ?? ''));
            $phoneRaw = trim((string)($row[1] ?? ''));
            $amountRaw = $row[3] ?? null;
            $dateRaw = $row[4] ?? null;

            // Header skip if it's header row
            if ($index === 0 && (stripos($nameRaw, 'tên') !== false || stripos($phoneRaw, 'phone') !== false || stripos($phoneRaw, 'sđt') !== false)) {
                continue;
            }

            if (!$phoneRaw && !$nameRaw) {
                continue;
            }

            $phone = $this->normalizePhone($phoneRaw);
            if (!$phone && !$nameRaw) {
                $skippedCount++;
                continue;
            }

            // Amount processing: multiply by 1000
            $amountNum = (float)str_replace([',', ' '], '', (string)$amountRaw);
            $amount = $amountNum * 1000;

            // Refund date processing
            $refundDate = $this->parseDate($dateRaw);

            $result[] = [
                'name' => $nameRaw,
                'phone' => $phone,
                'raw_phone' => $phoneRaw,
                'amount' => $amount,
                'refund_date' => $refundDate,
            ];
        }

        $outputDir = dirname($outputFile);
        if (!is_dir($outputDir)) {
            mkdir($outputDir, 0755, true);
        }

        file_put_contents($outputFile, json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        $this->info("Đã chuyển đổi thành công " . count($result) . " bản ghi sang JSON: {$outputFile}");
        if ($skippedCount > 0) {
            $this->warn("Bỏ qua {$skippedCount} dòng không hợp lệ.");
        }

        return 0;
    }

    private function normalizePhone(string $raw): ?string
    {
        if (!$raw) return null;
        if (stripos($raw, 'e+') !== false) {
            $raw = number_format((float) $raw, 0, '.', '');
        }
        $digits = preg_replace('/\D/', '', $raw);
        if (!$digits) return null;

        if (str_starts_with($digits, '84') && strlen($digits) >= 11) {
            $digits = '0' . substr($digits, 2);
        }
        if (!str_starts_with($digits, '0')) {
            $digits = '0' . $digits;
        }
        $digits = substr($digits, 0, 10);
        return strlen($digits) >= 9 ? $digits : null;
    }

    private function parseDate($rawDate): string
    {
        if (empty($rawDate)) {
            return date('Y-m-d');
        }

        if (is_numeric($rawDate)) {
            try {
                return ExcelDate::excelToDateTimeObject($rawDate)->format('Y-m-d');
            } catch (\Exception $e) {
                // Fallthrough
            }
        }

        $rawDateStr = trim((string)$rawDate);
        $time = strtotime($rawDateStr);
        if ($time !== false) {
            return date('Y-m-d', $time);
        }

        return date('Y-m-d');
    }
}
