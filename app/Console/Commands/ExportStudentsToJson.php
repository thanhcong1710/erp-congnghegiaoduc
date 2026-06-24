<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\StudentDataTransformer;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;

/**
 * Command: erp:export-students-json
 *
 * Reads an Excel file, transforms each row into a normalized structure (using
 * StudentDataTransformer) and writes the whole collection to a JSON file.
 *
 * Usage:
 *   php artisan erp:export-students-json path/to/file.xlsx [output.json]
 */
class ExportStudentsToJson extends Command
{
    protected $signature = 'erp:export-students-json {excel : Duong dan toi file Excel} {json? : Duong dan output JSON}';
    protected $description = 'Export Excel student data to a normalized JSON file (usable by erp:import-students-json).';

    public function handle()
    {
        ini_set('memory_limit', '-1'); // Cho phép dùng không giới hạn RAM để parse file Excel lớn
        $excelPath = $this->argument('excel');
        $jsonPath   = $this->argument('json') ?? storage_path('app/exports/students_'.date('Ymd_His').'.json');

        if (!file_exists($excelPath)) {
            $this->error("File Excel không tồn tại: $excelPath");
            return 1;
        }

        $this->info('Đọc Excel...');
        $spreadsheet = IOFactory::load($excelPath);
        $worksheet = $spreadsheet->getActiveSheet();
        
        $rows = [];
        foreach ($worksheet->getRowIterator() as $row) {
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false); 
            $rowData = [];
            foreach ($cellIterator as $cell) {
                if ($cell->isFormula()) {
                    // Lấy giá trị đã được tính toán sẵn bởi Excel (bỏ qua engine tính toán của PhpSpreadsheet để tránh lỗi #NAME?)
                    $val = $cell->getOldCalculatedValue();
                    // Nếu old calculated value ko có, fall back về formatted value
                    if ($val === null) {
                        $val = $cell->getFormattedValue();
                    }
                } else {
                    $val = $cell->getFormattedValue();
                }
                $rowData[] = $val;
            }
            $rows[] = $rowData;
        }

        if (empty($rows)) {
            $this->error("File Excel rỗng");
            return 1;
        }

        $header = array_shift($rows); // Dòng 1: Header
        array_shift($rows); // Dòng 2: Dòng phụ/đánh số (bỏ qua vì data từ dòng 3)
        $normalized = [];
        $skipped = [];
        
        $globalCombinedPath = dirname($jsonPath) . '/global_skipped_combined.json';
        $skippedCombined = [];
        $dummyPhoneCounter = 1;

        if (file_exists($globalCombinedPath)) {
            $existing = json_decode(file_get_contents($globalCombinedPath), true);
            if (is_array($existing)) {
                $skippedCombined = $existing;
                foreach ($existing as $item) {
                    if (isset($item['phone']) && is_numeric($item['phone']) && str_starts_with($item['phone'], '00000')) {
                        $num = (int)$item['phone'];
                        if ($num >= $dummyPhoneCounter) {
                            $dummyPhoneCounter = $num + 1;
                        }
                    }
                }
            }
        }

        foreach ($rows as $index => $row) {
            $assoc = [];
            foreach ($header as $colIndex => $colName) {
                if (!empty($colName)) {
                    // Normalize header similarly to Maatwebsite/Excel (slugify and convert dash to underscore)
                    $key = str_replace('-', '_', Str::slug(trim((string)$colName)));
                    $assoc[$key] = $row[$colIndex] ?? null;
                }
            }
            // Bỏ qua dòng trống
            if (!empty(array_filter($assoc))) {
                $transformed = StudentDataTransformer::transformRow($assoc);
                
                // Cần có tên học viên mới được phép xử lý
                if (!empty($transformed['name'])) {
                    if (!empty($transformed['phone'])) {
                        // Chuẩn: Có cả tên và số điện thoại
                        $normalized[] = $transformed;
                    } else {
                        // Có tên nhưng KHÔNG có SĐT -> Đưa vào skipped_combined với SĐT giả định
                        $transformed['phone'] = str_pad((string)$dummyPhoneCounter, 10, '0', STR_PAD_LEFT);
                        $dummyPhoneCounter++;
                        $skippedCombined[] = $transformed;
                        
                        // Không đưa vào skipped invalid nữa để file _skipped_invalid.json chỉ chứa các dòng lỗi thực sự không thể cứu
                    }
                } else {
                    $assoc['excel_row_number'] = $index + 3;
                    $assoc['reason'] = 'Thiếu tên học viên';
                    $skipped[] = $assoc;
                }
            }
        }

        $json = json_encode($normalized, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        if ($json === false) {
            $this->error('Failed to encode JSON');
            return 1;
        }

        // Ensure directory exists
        $dir = dirname($jsonPath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        file_put_contents($jsonPath, $json);
        $this->info("✅ Export thành công → $jsonPath");

        if (count($skipped) > 0) {
            $skippedPath = preg_replace('/\.json$/', '_skipped_invalid.json', $jsonPath);
            file_put_contents($skippedPath, json_encode($skipped, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            $this->warn("⚠️  Phát hiện " . count($skipped) . " dòng bị lỗi (thiếu tên).");
            $this->warn("   → Đã xuất chi tiết các dòng lỗi ra file: " . $skippedPath);
        }
        
        if (count($skippedCombined) > 0) {
            file_put_contents($globalCombinedPath, json_encode($skippedCombined, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            $this->info("🔄 Đã gộp và cập nhật file chứa các record thiếu SĐT vào chung 1 file (SĐT giả định tự động tăng): $globalCombinedPath");
        }

        return 0;
    }
}
?>
