<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\StudentsImport; // we will reuse the same importer for validation
use App\Services\StudentDataTransformer;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use League\Csv\Writer;
use SplTempFileObject;

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
        $excelPath = $this->argument('excel');
        $jsonPath   = $this->argument('json') ?? storage_path('app/exports/students_'.date('Ymd_His').'.json');

        if (!file_exists($excelPath)) {
            $this->error("File Excel không tồn tại: $excelPath");
            return 1;
        }

        $this->info('Đọc Excel...');
        $rows = Excel::toCollection(new \stdClass, $excelPath)->first(); // collection of rows

        $normalized = [];
        foreach ($rows as $row) {
            // Convert row (which is a collection) to associative array with lower‑case keys
            $assoc = [];
            foreach ($row as $key => $value) {
                $assoc[ strtolower(str_replace(' ', '_', $key)) ] = $value;
            }
            $normalized[] = StudentDataTransformer::transformRow($assoc);
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
        return 0;
    }
}
?>
