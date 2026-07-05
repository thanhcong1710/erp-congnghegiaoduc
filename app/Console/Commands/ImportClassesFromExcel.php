<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Providers\UtilityServiceProvider as u;
use App\User;
use Illuminate\Support\Facades\Hash;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ImportClassesFromExcel extends Command
{
    protected $signature = 'erp:import-classes-excel {--file= : Path to Excel file} {--resume= : Class code to resume from}';
    protected $description = 'Import classes from Excel file. If class code exists (check uppercase, no space), update it; otherwise insert new.';

    public function handle()
    {
        $file = $this->option('file');
        $resumeClass = $this->option('resume');

        if (!$file || !file_exists($file)) {
            $this->error("No file found at {$file}");
            return 1;
        }

        $this->info("Loading Excel file (Read Data Only)...");
        $reader = IOFactory::createReaderForFile($file);
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($file);

        $worksheet = $spreadsheet->getActiveSheet();

        $this->info("Parsing Excel data to array...");
        // Chỉ đọc data, không đọc format để tăng tốc tối đa
        $rows = $worksheet->toArray(null, true, false, false);
        $highestRow = count($rows);
        $this->info("Found {$highestRow} rows in Excel. Preparing caches...");

        // Preload maps to avoid N+1 queries
        $cache_branches = [];
        $cache_products = [];
        $cache_products_by_id = [];
        $cache_shifts = [];
        $cache_teachers = [];
        $cache_subjects = [];
        $cache_programs = [];
        $cache_holidays = [];

        // Pre-cache ALL products by ID and by name for O(1) lookup
        $allProducts = DB::table('products')->get();
        foreach ($allProducts as $p) {
            $cache_products_by_id[$p->id] = ['id' => $p->id, 'num_sessions' => $p->num_sessions ?: 14];
            $cache_products[$p->name] = ['id' => $p->id, 'num_sessions' => $p->num_sessions ?: 14];
        }

        // Load classes_data.json mapping
        $jsonMap = [];
        $jsonPath = base_path('classes_data.json');
        if (file_exists($jsonPath)) {
            $jsonData = json_decode(file_get_contents($jsonPath), true);
            if (is_array($jsonData)) {
                foreach ($jsonData as $item) {
                    if (isset($item['code']) && isset($item['product_name'])) {
                        $jsonMap[strtoupper(str_replace(' ', '', $item['code']))] = trim($item['product_name']);
                    }
                }
            }
        }

        // Preload all classes for O(1) lookup
        $existingClassesMap = [];
        $allClasses = DB::table('classes')->select('id', 'code', 'cls_startdate', 'class_day', 'product_id')->get();
        foreach ($allClasses as $cls) {
            $code_norm = strtoupper(str_replace(' ', '', $cls->code));
            $existingClassesMap[$code_norm] = $cls;
        }

        $this->info("Starting import...");

        $processedCount = 0;
        $resumeFound = empty($resumeClass);

        DB::beginTransaction();
        try {
            foreach ($rows as $index => $row) {
                if ($index === 0) {
                    // Skip header row
                    continue;
                }

                $code_raw = trim($row[1] ?? ''); // B: Mã Lớp

                // Bỏ qua dòng trống hoặc dòng chứa tiêu đề "Mã Lớp"
                if (empty($code_raw) || mb_strtolower($code_raw, 'UTF-8') === 'mã lớp') {
                    continue;
                }

                $code_normalized = strtoupper(str_replace(' ', '', $code_raw));

                if (!$resumeFound) {
                    if ($code_normalized === strtoupper(str_replace(' ', '', $resumeClass))) {
                        $resumeFound = true;
                        $this->info("--> RESUMING FROM CLASS: {$code_normalized}");
                    } else {
                        continue;
                    }
                }

                $existingClass = $existingClassesMap[$code_normalized] ?? null;

                // DEBUG: In ra toàn bộ dữ liệu raw của dòng đầu tiên để xác định đúng vị trí cột
                if ($processedCount === 0) {
                    $this->info("=== DEBUG ROW {$index} RAW DATA ===");
                    foreach ($row as $colIdx => $colVal) {
                        $this->info("  Col[{$colIdx}] = " . json_encode($colVal));
                    }
                    $this->info("=== END DEBUG ===");
                }

                $product_name = trim($row[2] ?? ''); // C: Sản phẩm / Khóa học
                // Ưu tiên lấy tên product từ JSON nếu Excel không có hoặc JSON có ghi đè
                if (isset($jsonMap[$code_normalized]) && empty($product_name)) {
                    $product_name = $jsonMap[$code_normalized];
                }

                $branch_name = trim($row[3] ?? ''); // D: Trung tâm
                $days_raw = trim($row[4] ?? ''); // E: Lịch học (Thứ)
                $start_date_raw = trim($row[6] ?? ''); // G: Ngày khai giảng
                $shift_name = trim($row[8] ?? ''); // I: Ca học
                $teacher_name = trim($row[11] ?? ''); // L: GV VN

                // Branch
                $branch_id = 9;
                if ($branch_name) {
                    if (isset($cache_branches[$branch_name])) {
                        $branch_id = $cache_branches[$branch_name];
                    } else {
                        $branch = DB::table('branches')->where('name', 'like', "%{$branch_name}%")->first();
                        if ($branch)
                            $branch_id = $branch->id;
                        $cache_branches[$branch_name] = $branch_id;
                    }
                }

                // Product
                $product_id = 0;
                $num_sessions = 14;

                if ($existingClass && $existingClass->product_id) {
                    // Nếu là lớp đã tồn tại, BẮT BUỘC dùng luôn product_id chuẩn cũ
                    $product_id = $existingClass->product_id;
                    if (isset($cache_products_by_id[$product_id])) {
                        $num_sessions = $cache_products_by_id[$product_id]['num_sessions'];
                    }
                } else {
                    // Nếu là lớp mới, lấy từ name (đã pre-cached)
                    if ($product_name && isset($cache_products[$product_name])) {
                        $product_id = $cache_products[$product_name]['id'];
                        $num_sessions = $cache_products[$product_name]['num_sessions'];
                    }
                }

                // Nếu không có product_id thì bỏ qua lớp này
                if (!$product_id) {
                    $this->info("Row {$index}: [BỎ QUA] | Code: {$code_normalized} | Lý do: Không lấy được product_id");
                    continue;
                }

                $days = [];
                if ($days_raw) {
                    // Chuẩn hóa: T3+T6, T4+CN, Thứ 2 - Thứ 5, etc.
                    $days_raw_clean = mb_strtolower($days_raw, 'UTF-8');
                    // Thay thế CN/Chủ nhật thành 8 TRƯỚC khi xóa chữ T
                    $days_raw_clean = str_ireplace(['chủ nhật', 'chunhat', 'cn'], '8', $days_raw_clean);
                    // Xóa các chữ "thứ", "thu" trước, rồi mới xóa chữ "t" đơn lẻ
                    $days_raw_clean = str_ireplace(['thứ', 'thu'], '', $days_raw_clean);
                    $days_raw_clean = preg_replace('/t(?=\d)/', '', $days_raw_clean);
                    // Chuẩn hóa tất cả dấu phân cách (+, -, khoảng trắng) thành dấu phẩy
                    $days_raw_clean = str_replace(['+', '-', ' '], ',', $days_raw_clean);
                    $raw_parts = array_filter(array_map('trim', explode(',', $days_raw_clean)));

                    foreach ($raw_parts as $d) {
                        $d_val = (int) $d;
                        if ($d_val >= 2 && $d_val <= 8) {
                            $days[] = $d_val;
                        }
                    }
                    $days = array_unique($days);
                    sort($days);
                }
                if (empty($days))
                    $days = [2, 5];

                $start_date = null;
                if ($start_date_raw) {
                    if (preg_match('/^(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{4})$/', $start_date_raw, $m)) {
                        $start_date = "{$m[3]}-{$m[2]}-{$m[1]}";
                    } else {
                        try {
                            $start_date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($start_date_raw)->format('Y-m-d');
                        } catch (\Exception $e) {
                            $start_date = date('Y-m-d', strtotime($start_date_raw));
                        }
                    }
                }

                // Shift
                $shift_id = 0;
                if ($shift_name) {
                    if (isset($cache_shifts[$shift_name])) {
                        $shift_id = $cache_shifts[$shift_name];
                    } else {
                        $shift = DB::table('shifts')->where('name', $shift_name)->first();
                        if ($shift) {
                            $shift_id = $shift->id;
                        } else {
                            $shift_id = DB::table('shifts')->insertGetId([
                                'name' => $shift_name,
                                'start_time' => '18:00',
                                'end_time' => '19:30',
                                'status' => 1,
                                'created_at' => now(),
                            ]);
                        }
                        $cache_shifts[$shift_name] = $shift_id;
                    }
                }

                // Teacher
                $teacher_id = 0;
                if ($teacher_name) {
                    if (isset($cache_teachers[$teacher_name])) {
                        $teacher_id = $cache_teachers[$teacher_name];
                    } else {
                        $teacher = DB::table('users')->where('name', $teacher_name)->first();
                        if (!$teacher) {
                            $teacher_id = User::insertGetId([
                                'name' => $teacher_name,
                                'email' => str_replace(' ', '', strtolower(u::convert_slug($teacher_name))) . '@gmail.com',
                                'hrm_id' => 'GV' . str_pad(DB::table('users')->count() + 1, 3, '0', STR_PAD_LEFT),
                                'password' => Hash::make('12345678@'),
                                'status' => 1,
                                'menuroles' => 'user',
                                'created_at' => now(),
                            ]);
                            DB::table('role_has_user')->insert(['role_id' => 36, 'user_id' => $teacher_id]);
                            DB::table('branch_has_user')->insert(['branch_id' => $branch_id, 'user_id' => $teacher_id]);
                        } else {
                            $teacher_id = $teacher->id;
                        }
                        $cache_teachers[$teacher_name] = $teacher_id;
                    }
                }

                // Subject
                $subject_id = 0;
                if ($product_name) {
                    if (isset($cache_subjects[$product_name])) {
                        $subject_id = $cache_subjects[$product_name];
                    } else {
                        $subject = DB::table('subjects')->where('name', $product_name)->first();
                        if (!$subject) {
                            $subject_id = DB::table('subjects')->insertGetId([
                                'name' => $product_name,
                                'code' => strtoupper(substr($product_name, 0, 3)),
                                'status' => 1,
                                'created_at' => now(),
                            ]);
                        } else {
                            $subject_id = $subject->id;
                        }
                        $cache_subjects[$product_name] = $subject_id;
                    }
                }

                // Program
                $program_id = 0;
                if ($product_name) {
                    $prog_cache_key = "{$product_name}_{$product_id}";
                    if (isset($cache_programs[$prog_cache_key])) {
                        $program_id = $cache_programs[$prog_cache_key];
                    } else {
                        $program = DB::table('programs')->where('product_id', $product_id)->first();
                        if (!$program) {
                            $program_id = DB::table('programs')->insertGetId([
                                'name' => $product_name,
                                'code' => strtoupper(substr($product_name, 0, 3)),
                                'product_id' => $product_id,
                                'status' => 1,
                                'created_at' => now(),
                            ]);
                        } else {
                            $program_id = $program->id;
                        }
                        $cache_programs[$prog_cache_key] = $program_id;
                    }
                }

                // Holidays cache
                $holiday_key = "{$branch_id}_{$product_id}";
                if (isset($cache_holidays[$holiday_key])) {
                    $holidays = $cache_holidays[$holiday_key];
                } else {
                    $holidays = u::getPublicHolidays($branch_id, $product_id);
                    $cache_holidays[$holiday_key] = $holidays;
                }

                $data_sessions = null;
                if ($start_date && !empty($days)) {
                    $data_sessions = u::calculatorSessionsByNumberOfSessions($start_date, $num_sessions, $holidays, $days);
                }

                $class_data = [
                    'branch_id' => $branch_id,
                    'teacher_id' => $teacher_id,
                    'cls_name' => $code_raw,
                    'product_id' => $product_id,
                    'program_id' => $program_id,
                    'cls_startdate' => $start_date,
                    'cls_enddate' => $data_sessions ? data_get($data_sessions, 'end_date') : null,
                    'class_day' => implode(',', $days),
                    'updated_at' => now(),
                ];

                if ($existingClass) {
                    $date_changed = ($existingClass->cls_startdate != $start_date) || ($existingClass->class_day != implode(',', $days));

                    DB::table('classes')->where('id', $existingClass->id)->update($class_data);
                    $class_id = $existingClass->id;

                    // Update the cached map just in case
                    $existingClass->cls_startdate = $start_date;
                    $existingClass->class_day = implode(',', $days);

                    $action_msg = "[CẬP NHẬT] ";
                    if ($date_changed) {
                        $action_msg .= "(Đổi lịch học) ";
                        DB::table('sessions')->where('class_id', $class_id)->delete();
                        DB::table('schedules')->where('class_id', $class_id)->delete();

                        if ($shift_id) {
                            foreach ($days as $day) {
                                u::insertSimpleRow([
                                    'class_id' => $class_id,
                                    'shift_id' => $shift_id,
                                    'room_id' => 1,
                                    'teacher_id' => $teacher_id,
                                    'class_day' => $day,
                                    'status' => 1,
                                    'branch_id' => $branch_id,
                                    'created_at' => now(),
                                    'creator_id' => 1,
                                ], 'sessions');
                            }
                        }

                        if ($data_sessions && isset($data_sessions->dates) && $subject_id) {
                            $scheduleRows = [];
                            $i = 0;
                            $now = now()->toDateTimeString();
                            foreach ($data_sessions->dates as $date) {
                                $i++;
                                $scheduleRows[] = [
                                    'class_date' => $date,
                                    'class_id' => $class_id,
                                    'status' => 1,
                                    'created_at' => $now,
                                    'teacher_id' => $teacher_id,
                                    'branch_id' => $branch_id,
                                    'subject_id' => $subject_id,
                                    'subject_stt' => $i,
                                ];
                            }
                            if (!empty($scheduleRows)) {
                                DB::table('schedules')->insert($scheduleRows);
                            }
                        }
                    }
                } else {
                    $action_msg = "[TẠO MỚI] ";
                    $class_data['code'] = $code_raw;
                    $class_data['created_at'] = now();
                    $class_data['creator_id'] = 1;
                    $class_data['status'] = 1;
                    $class_data['type'] = 1;
                    $class_data['max_students'] = 20;

                    $class_id = u::insertSimpleRow($class_data, 'classes');

                    // Add to cache
                    $existingClassesMap[$code_normalized] = (object) [
                        'id' => $class_id,
                        'code' => $code_raw,
                        'cls_startdate' => $start_date,
                        'class_day' => implode(',', $days)
                    ];

                    if ($subject_id) {
                        u::insertSimpleRow([
                            'class_id' => $class_id,
                            'subject_id' => $subject_id,
                            'session' => $num_sessions,
                            'stt' => 1,
                            'created_at' => now(),
                        ], 'subject_has_class');
                    }

                    if ($shift_id) {
                        foreach ($days as $day) {
                            u::insertSimpleRow([
                                'class_id' => $class_id,
                                'shift_id' => $shift_id,
                                'room_id' => 1,
                                'teacher_id' => $teacher_id,
                                'class_day' => $day,
                                'status' => 1,
                                'branch_id' => $branch_id,
                                'created_at' => now(),
                                'creator_id' => 1,
                            ], 'sessions');
                        }
                    }

                    if ($data_sessions && isset($data_sessions->dates) && $subject_id) {
                        $scheduleRows = [];
                        $i = 0;
                        $now = now()->toDateTimeString();
                        foreach ($data_sessions->dates as $date) {
                            $i++;
                            $scheduleRows[] = [
                                'class_date' => $date,
                                'class_id' => $class_id,
                                'status' => 1,
                                'created_at' => $now,
                                'teacher_id' => $teacher_id,
                                'branch_id' => $branch_id,
                                'subject_id' => $subject_id,
                                'subject_stt' => $i,
                            ];
                        }
                        if (!empty($scheduleRows)) {
                            DB::table('schedules')->insert($scheduleRows);
                        }
                    }
                }

                $processedCount++;
                $this->info("Row {$index}: {$action_msg} | Code: {$code_normalized} | ProductID: {$product_id} | Start: {$start_date} | Days: " . implode(',', $days));

                // Commit DB sau mỗi 50 dòng để tăng tốc
                if ($processedCount % 50 == 0) {
                    DB::commit();
                    DB::beginTransaction();
                    $this->info("--> Đã commit {$processedCount} bản ghi vào database.");
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("\nError occurred at row: " . ($index ?? '?') . " - " . $e->getMessage());
            return 1;
        }

        $this->info("\nImport completed successfully! Processed {$processedCount} valid rows.");
        return 0;
    }
}
