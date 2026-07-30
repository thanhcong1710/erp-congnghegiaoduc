<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Providers\UtilityServiceProvider as u;

class MoveHolidaySchedules extends Command
{
    protected $signature = 'erp:move-holiday-schedules {--dry-run : Chạy thử để kiểm tra xem đổi ngày có đúng không}';
    protected $description = 'Chuyển lịch học của các buổi nghỉ xuống cuối khóa học (Max date + 1)';

    public function handle()
    {
        // Danh sách data lấy từ ảnh bạn cung cấp
        $data = [
            ['class' => 'VIP280', 'date' => '01/07/2026'],
            ['class' => 'VIP297', 'date' => '14/07/2026'],
            ['class' => 'VIP297', 'date' => '24/07/2026'],
            ['class' => 'VIP279', 'date' => '17/07/2026'],
            ['class' => 'PT439',  'date' => '15/07/2026'],
            ['class' => 'PT445',  'date' => '15/07/2026'],
            ['class' => 'VIP293', 'date' => '03/07/2026'],
            ['class' => 'VIP278', 'date' => '03/07/2026'],
            ['class' => 'T463',   'date' => '13/07/2026'],
            ['class' => 'PT467',  'date' => '13/07/2026'],
            ['class' => 'PT451',  'date' => '02/07/2026'],
            ['class' => 'T468',   'date' => '09/07/2026'],
            ['class' => 'PT469',  'date' => '10/07/2026'],
            ['class' => 'T474',   'date' => '10/07/2026'],
            ['class' => 'VIP295', 'date' => '03/07/2026'],
            ['class' => 'VIP286', 'date' => '20/07/2026'],
            ['class' => 'VIP299', 'date' => '21/07/2026'],
            ['class' => 'VIP272', 'date' => '09/07/2026'],
            ['class' => 'T464',   'date' => '16/07/2026'],
            ['class' => 'PT418',  'date' => '24/07/2026'],
            ['class' => 'PT459',  'date' => '13/07/2026'],
            ['class' => 'T458',   'date' => '28/07/2026'],
            ['class' => 'T465',   'date' => '28/07/2026'],
            ['class' => 'PT481',  'date' => '06/07/2026'],
            ['class' => 'T456',   'date' => '29/07/2026'],
        ];

        $isDryRun = $this->option('dry-run');

        if ($isDryRun) {
            $this->info("==== ĐANG CHẠY THỬ (DRY RUN) - SẼ KHÔNG LƯU VÀO DATABASE ====\n");
        }

        foreach ($data as $item) {
            $className = trim($item['class']);
            $dateStr = trim($item['date']);
            $holidayDate = Carbon::createFromFormat('d/m/Y', $dateStr)->format('Y-m-d');

            $class = DB::table('classes')->where('cls_name', $className)->first();
            if (!$class) {
                $this->warn("[-] Không tìm thấy lớp: {$className}");
                continue;
            }

            // Tìm lịch học của buổi bị nghỉ
            $schedule = DB::table('schedules')
                ->where('class_id', $class->id)
                ->where('class_date', $holidayDate)
                ->first();

            if (!$schedule) {
                $this->warn("[-] Lớp {$className}: Không có lịch học nào vào ngày {$dateStr}");
                continue;
            }

            // Tìm ngày kết thúc hiện tại của lớp
            $maxDate = DB::table('schedules')
                ->where('class_id', $class->id)
                ->where('status', 1) // status=1 là các lịch đang active
                ->max('class_date');

            if (!$maxDate) {
                $this->warn("[-] Lớp {$className}: Không xác định được ngày học cuối cùng");
                continue;
            }

            // Tính buổi tiếp theo (Max date + 1 buổi)
            // Lịch học trong tuần của lớp (vd: "2,4,6")
            $classDays = array_map('intval', explode(',', $class->class_day)); 

            // Lấy danh sách ngày lễ để tránh việc đẩy buổi bù vào đúng ngày lễ khác
            $holidays = u::getPublicHolidays($class->branch_id, $class->product_id);
            $holidaysTimestamp = [];
            if ($holidays && is_array($holidays)) {
                foreach ($holidays as $h) {
                    $holidaysTimestamp[] = [
                        'start_date' => strtotime(date('Y-m-d', strtotime($h->start_date))),
                        'end_date'   => strtotime(date('Y-m-d', strtotime($h->end_date)))
                    ];
                }
            }

            $nextDate = Carbon::parse($maxDate)->addDay();
            
            while (true) {
                $weekday = $nextDate->dayOfWeekIso; // 1 (Mon) - 7 (Sun)
                $mappedWeekday = $weekday == 7 ? 8 : $weekday + 1; // Hệ thống dùng 8 cho Chủ nhật, 2->Thứ 2

                if (in_array($mappedWeekday, $classDays)) {
                    // Check xem có rơi vào ngày nghỉ lễ nào không
                    $ts = $nextDate->timestamp;
                    $isHoliday = false;
                    foreach ($holidaysTimestamp as $h) {
                        if ($ts >= $h['start_date'] && $ts <= $h['end_date']) {
                            $isHoliday = true;
                            break;
                        }
                    }

                    if (!$isHoliday) {
                        // Tìm được ngày hợp lệ
                        break; 
                    }
                }
                $nextDate->addDay();
            }

            $newDateStr = $nextDate->format('Y-m-d');
            
            $this->info("[+] Lớp {$className}: Chuyển lịch ngày {$holidayDate} ---> Buổi bù: {$newDateStr} (Ngày cuối cũ là {$maxDate})");

            if (!$isDryRun) {
                DB::table('schedules')
                    ->where('id', $schedule->id)
                    ->update([
                        'class_date' => $newDateStr,
                        'updated_at' => now()
                    ]);
            }
        }
        
        $this->info("\n==========================================");
        if ($isDryRun) {
            $this->info("CHẠY THỬ (DRY RUN) HOÀN TẤT. KHÔNG CÓ GÌ THAY ĐỔI TRONG DATABASE.");
            $this->info("Nếu bạn thấy kết quả log in ra đã chuẩn, hãy bỏ cờ --dry-run để chạy thật!");
        } else {
            $this->info("HOÀN TẤT! ĐÃ CẬP NHẬT LỊCH THÀNH CÔNG VÀO DATABASE.");
        }
    }
}
