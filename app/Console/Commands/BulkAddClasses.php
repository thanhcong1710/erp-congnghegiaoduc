<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Providers\UtilityServiceProvider as u;
use App\User;
use Illuminate\Support\Facades\Hash;

/**
 * Class BulkAddClasses
 * Usage: php artisan bulk:add-classes --file=path/to/classes.json
 * Or edit the $classes_to_add array in handle()
 */
class BulkAddClasses extends Command
{
    protected $signature = 'bulk:add-classes {--file= : Path to JSON file with class data}';
    protected $description = 'Add bulk classes with automatic creation of shifts and teachers';

    public function handle()
    {
        $file = $this->option('file');
        $classes_to_add = [];

        if ($file && file_exists($file)) {
            $classes_to_add = json_decode(file_get_contents($file), true);
        } else {
            // Default template / Example data
            $classes_to_add = [
                // [
                //     'code' => 'OPTXXX',
                //     'branch_id' => 9,
                //     'product_id' => 25,
                //     'days' => [2, 5],
                //     'shift' => '18H15-19H45',
                //     'start_time' => '18:15',
                //     'end_time' => '19:45',
                //     'start_date' => '2026-04-02',
                //     'teacher' => 'GV Nguyễn Ngoan',
                // ],
            ];
        }

        if (empty($classes_to_add)) {
            $this->error("No class data found. Please provide a JSON file or update the script.");
            return;
        }

        foreach ($classes_to_add as $c) {
            $branch_id = data_get($c, 'branch_id', 9);
            $product_name = data_get($c, 'product_name', 'Pre-Toeic');
            
            // Get Product
            $product = DB::table('products')->where('name', $product_name)->first();
            if (!$product) {
                $this->error("Product '{$product_name}' not found for class {$c['code']}, skipping...");
                continue;
            }
            $product_id = $product->id;
            $num_sessions = $product->num_sessions ?: 14;

            // Ensure Subject exists (lookup by name, or use product name)
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

            // Ensure Program exists
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

            // Check if exists
            if (DB::table('classes')->where('code', $c['code'])->exists()) {
                $this->info("Class {$c['code']} already exists, skipping...");
                continue;
            }

            // Get or create shift
            $shift = DB::table('shifts')->where('name', $c['shift'])->first();
            if (!$shift) {
                $shift_id = DB::table('shifts')->insertGetId([
                    'name' => $c['shift'],
                    'start_time' => $c['start_time'],
                    'end_time' => $c['end_time'],
                    'status' => 1,
                    'created_at' => now(),
                ]);
            } else {
                $shift_id = $shift->id;
            }

            // Get or create teacher
            $teacher_id = 0;
            if (isset($c['teacher']) && $c['teacher']) {
                $teacher = DB::table('users')->where('name', $c['teacher'])->first();
                if (!$teacher) {
                    $teacher_id = User::insertGetId([
                        'name' => $c['teacher'],
                        'email' => str_replace(' ', '', strtolower(u::convert_slug($c['teacher']))) . '@gmail.com',
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
            }

            // Create Class
            $holidays = u::getPublicHolidays($branch_id, $product_id);
            $data_sessions = null;
            if (isset($c['start_date']) && $c['start_date']) {
                $data_sessions = u::calculatorSessionsByNumberOfSessions($c['start_date'], $num_sessions, $holidays, $c['days']);
            }

            $class_id = u::insertSimpleRow([
                'branch_id' => $branch_id,
                'teacher_id' => $teacher_id,
                'cls_name' => $c['code'],
                'code' => $c['code'],
                'product_id' => $product_id,
                'program_id' => $program_id,
                'cls_startdate' => data_get($c, 'start_date'),
                'cls_enddate' => $data_sessions ? data_get($data_sessions, 'end_date') : null,
                'class_day' => implode(',', $c['days']),
                'max_students' => 20,
                'created_at' => now(),
                'creator_id' => 1,
                'status' => 1,
                'type' => 1,
                'is_online' => data_get($c, 'is_online', 0),
            ], 'classes');

            // Insert Sessions
            foreach ($c['days'] as $day) {
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

            // Insert Subject Relation
            u::insertSimpleRow([
                'class_id' => $class_id,
                'subject_id' => $subject_id,
                'session' => $num_sessions,
                'stt' => 1,
                'created_at' => now(),
            ], 'subject_has_class');

            // Insert Schedules
            if ($data_sessions && isset($data_sessions->dates)) {
                $i = 0;
                foreach ($data_sessions->dates as $date) {
                    $i++;
                    u::insertSimpleRow([
                        'class_date' => $date,
                        'class_id' => $class_id,
                        'status' => 1,
                        'created_at' => now(),
                        'teacher_id' => $teacher_id,
                        'branch_id' => $branch_id,
                        'subject_id' => $subject_id,
                        'subject_stt' => $i,
                    ], 'schedules');
                }
            }

            $this->info("Successfully added class {$c['code']}");
        }
    }
}
