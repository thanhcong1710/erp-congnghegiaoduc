<?php

namespace App\Services;

use DB;

class TATutoringService
{
    public static function generateTutorings($class_id)
    {
        $class = DB::table('classes')->where('id', $class_id)->first();
        if (!$class || empty($class->ta_id)) return;

        $ta_ids = explode(',', $class->ta_id);
        if (empty($ta_ids)) return;

        $product = DB::table('products')->where('id', $class->product_id)->first();
        if (!$product) return;

        // Fetch active students
        $students = DB::table('contracts')
            ->where('class_id', $class_id)
            ->whereIn('status', [1, 6])
            ->pluck('student_id')
            ->toArray();

        // Group and 1:1 period counts based on product
        $group_count = 0;
        $one_on_one_count = 0;

        if ($product->id == 25) { // Pre: 4 đợt kèm nhóm, 2 đợt kèm 1:1
            $group_count = 4;
            $one_on_one_count = 2;
        } elseif ($product->id == 26) { // Lv1: 6 đợt kèm nhóm, 1 đợt kèm 1:1
            $group_count = 6;
            $one_on_one_count = 1;
        } elseif ($product->id == 27) { // Lv2: 6 đợt kèm nhóm, 2 đợt kèm 1:1
            $group_count = 6;
            $one_on_one_count = 2;
        } else {
            return;
        }

        // Generate Group Tutorings
        $is_online = $class->is_online == 1;
        $num_groups = $is_online ? 2 : 4;
        
        for ($session_index = 1; $session_index <= $group_count; $session_index++) {
            for ($g = 1; $g <= $num_groups; $g++) {
                // Determine TA
                if ($is_online) {
                    $ta_id = $ta_ids[0];
                } else {
                    $ta_id = ($g <= 2) ? $ta_ids[0] : (isset($ta_ids[1]) ? $ta_ids[1] : $ta_ids[0]);
                }

                // Check if exists
                $exists = DB::table('ta_group_tutorings')
                    ->where('class_id', $class_id)
                    ->where('session_index', $session_index)
                    ->where('group_name', "Nhóm $g")
                    ->first();

                if (!$exists) {
                    DB::table('ta_group_tutorings')->insert([
                        'ta_id' => $ta_id,
                        'product_id' => $class->product_id,
                        'class_id' => $class_id,
                        'group_name' => "Nhóm $g",
                        'session_index' => $session_index,
                        'expected_date' => null,
                        'expected_time' => null,
                        'lp_check_status' => 'Chưa check',
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
                } else {
                    // Update TA in case it changed
                    DB::table('ta_group_tutorings')->where('id', $exists->id)->update([
                        'ta_id' => $ta_id,
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
                }
            }
        }

        // Generate 1:1 Tutorings
        // Split students between TAs
        $ta_student_map = [];
        if ($is_online || count($ta_ids) == 1) {
            foreach ($students as $stu_id) {
                $ta_student_map[$stu_id] = $ta_ids[0];
            }
        } else {
            $half = ceil(count($students) / 2);
            $idx = 0;
            foreach ($students as $stu_id) {
                if ($idx < $half) {
                    $ta_student_map[$stu_id] = $ta_ids[0];
                } else {
                    $ta_student_map[$stu_id] = $ta_ids[1];
                }
                $idx++;
            }
        }

        for ($session_index = 1; $session_index <= $one_on_one_count; $session_index++) {
            foreach ($students as $stu_id) {
                $ta_id = $ta_student_map[$stu_id];
                $exists = DB::table('ta_one_on_one_tutorings')
                    ->where('class_id', $class_id)
                    ->where('student_id', $stu_id)
                    ->where('session_index', $session_index)
                    ->first();

                if (!$exists) {
                    DB::table('ta_one_on_one_tutorings')->insert([
                        'ta_id' => $ta_id,
                        'product_id' => $class->product_id,
                        'class_id' => $class_id,
                        'student_id' => $stu_id,
                        'session_index' => $session_index,
                        'expected_date' => null,
                        'expected_time' => null,
                        'lp_check_status' => 'Chưa check',
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
                } else {
                    DB::table('ta_one_on_one_tutorings')->where('id', $exists->id)->update([
                        'ta_id' => $ta_id,
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
                }
            }
        }
    }
}
