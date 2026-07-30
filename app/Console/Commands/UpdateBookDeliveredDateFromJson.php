<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdateBookDeliveredDateFromJson extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'erp:update-book-delivered-date-json {--phone= : Truyền SDT để test chỉ chạy cho 1 SĐT} {--dry-run : Chạy thử không update DB}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update contracts.book_delivered_date from normalized json files based on phone and class_name';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        ini_set('memory_limit', '-1');
        DB::connection()->disableQueryLog(); // Chống tràn RAM do log SQL ngầm của Laravel
        
        $testPhone = $this->option('phone');
        $isDryRun = $this->option('dry-run');
        
        $files = [
            app_path('Console/Data/students_normalized_1.json'),
            app_path('Console/Data/students_normalized_2.json'),
            app_path('Console/Data/students_normalized_3.json'),
            app_path('Console/Data/students_normalized_4.json'),
            app_path('Console/Data/students_normalized_5.json'),
        ];

        $this->info("Khởi tạo cache danh sách Lớp (Classes)...");
        $classes = DB::table('classes')->select('id', 'cls_name')->get();
        $classMap = [];
        foreach ($classes as $c) {
            $classMap[mb_strtolower(trim($c->cls_name), 'UTF-8')] = $c->id;
        }

        $this->info("Khởi tạo cache danh sách Học sinh (Students) - Vui lòng đợi...");
        $studentsByPhone = [];
        DB::table('students')->select('id', 'gud_mobile1')
            ->whereNotNull('gud_mobile1')
            ->where('gud_mobile1', '!=', '')
            ->orderBy('id')
            ->chunk(10000, function ($stds) use (&$studentsByPhone) {
                foreach ($stds as $s) {
                    $phoneClean = ltrim($s->gud_mobile1, '0');
                    if ($phoneClean) {
                        // Lưu đè id mới nhất nếu trùng SĐT
                        $studentsByPhone[$phoneClean] = $s->id;
                    }
                }
            });
        $this->info("Đã cache xong " . count($studentsByPhone) . " học sinh.");

        $this->info("Khởi tạo cache danh sách Hợp đồng (Contracts) - Vui lòng đợi...");
        $contractsCache = [];
        DB::table('contracts')->select('id', 'student_id', 'class_id', 'book_delivered_date')
            ->orderBy('id')
            ->chunk(10000, function($contracts) use (&$contractsCache) {
                foreach($contracts as $c) {
                    // Cache theo key student_id_class_id
                    $key = $c->student_id . '_' . $c->class_id;
                    $contractsCache[$key] = $c;
                }
            });
        $this->info("Đã cache xong " . count($contractsCache) . " hợp đồng.");

        $processedCount = 0;
        $updatedCount = 0;

        $totalProcessed = 0;
        foreach ($files as $file) {
            if (!file_exists($file)) {
                $this->warn("Không tìm thấy file: {$file}");
                continue;
            }
            
            $this->info("Đang xử lý đọc file: " . basename($file) . " (Giải nén JSON...)");
            $fileContent = file_get_contents($file);
            $data = json_decode($fileContent, true);
            $this->info("Đã giải nén JSON xong, mảng có " . count($data) . " phần tử. Bắt đầu đối chiếu dữ liệu...");
            
            if (!is_array($data)) {
                $this->error("JSON không hợp lệ trong file {$file}");
                continue;
            }
            
            foreach ($data as $index => $item) {
                $totalProcessed++;
                if ($totalProcessed % 1000 == 0 && !$testPhone) {
                    $this->info("... Đã duyệt qua {$totalProcessed} bản ghi JSON...");
                }

                $bookDate = $item['book_sent_date'] ?? null;
                $phone = $item['phone'] ?? '';
                $className = $item['class_name'] ?? '';
                
                if (empty($bookDate) || empty($phone) || empty($className)) {
                    continue;
                }
                
                // Nếu có truyền --phone thì lọc theo SĐT này
                if ($testPhone) {
                    $searchClean = ltrim($testPhone, '0');
                    $itemPhoneClean = ltrim($phone, '0');
                    if (strpos($itemPhoneClean, $searchClean) === false && strpos($searchClean, $itemPhoneClean) === false) {
                        continue;
                    }
                    $this->info("=> TÌM THẤY SĐT KHỚP TRONG JSON: {$phone} (Lớp: {$className}, Ngày gửi: {$bookDate})");
                }
                
                $processedCount++;
                
                // Chuẩn hóa phone để tìm trong DB (loại bỏ số 0 ở đầu vì gud_mobile1 có thể lưu thiếu 0)
                $searchPhone = ltrim($phone, '0');
                
                $studentId = $studentsByPhone[$searchPhone] ?? null;
                    
                if (!$studentId) {
                    if ($testPhone) $this->warn("   -> KHÔNG tìm thấy học sinh trong bảng `students` với số: {$phone}");
                    continue;
                }
                
                if ($testPhone) {
                    $this->info("   -> Đã tìm thấy Học sinh ID: {$studentId} - Khớp SĐT: {$phone}");
                }
                
                $classLower = mb_strtolower(trim($className), 'UTF-8');
                $classId = $classMap[$classLower] ?? null;
                
                if (!$classId) {
                    if ($testPhone) $this->warn("   -> KHÔNG tìm thấy Lớp học trong bảng `classes` với tên: {$className}");
                    continue;
                }
                
                if ($testPhone) {
                    $this->info("   -> Đã tìm thấy Lớp học ID: {$classId}");
                }
                
                // Lấy bản ghi contracts từ RAM Cache (O(1))
                $contractKey = $studentId . '_' . $classId;
                $contract = $contractsCache[$contractKey] ?? null;
                    
                if (!$contract) {
                    if ($testPhone) $this->warn("   -> KHÔNG tìm thấy Hợp đồng (Contract) cho Student ID: {$studentId}, Class ID: {$classId}");
                    continue;
                }
                
                if (!empty($contract->book_delivered_date)) {
                    if ($testPhone) $this->warn("   -> Hợp đồng (ID: {$contract->id}) ĐÃ CÓ `book_delivered_date` = {$contract->book_delivered_date}. Bỏ qua không cập nhật.");
                    continue;
                }
                
                if ($testPhone) {
                    $this->info("   -> [HỢP LỆ] Sẽ cập nhật Contract ID: {$contract->id} cột `book_delivered_date` = {$bookDate}");
                }

                if (!$isDryRun) {
                    // Cập nhật
                    DB::table('contracts')
                        ->where('id', $contract->id)
                        ->update([
                            'book_delivered_date' => $bookDate,
                            'updated_at' => now()
                        ]);
                    $updatedCount++;
                    
                    if (!$testPhone && $updatedCount % 50 == 0) {
                        $this->info("... Đã thực hiện update thành công {$updatedCount} hợp đồng vào database.");
                    }
                }
            }
        }
        
        $this->info("==========================================");
        if ($isDryRun) {
            $this->info("CHẠY THỬ (DRY RUN) HOÀN TẤT. Không có dữ liệu nào bị thay đổi trong DB.");
        } else {
            $this->info("HOÀN TẤT! Đã xử lý {$processedCount} JSON records hợp lệ, cập nhật thành công {$updatedCount} hợp đồng.");
        }
        
        return 0;
    }
}
