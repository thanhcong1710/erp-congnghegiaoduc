<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Cho phép học sinh chỉ có NĂM SINH (không rõ ngày/tháng).
 * Cột ngày giữ nguyên kiểu DATE, năm sinh lưu dạng YYYY-01-01 + cờ *_year_only = 1
 * => các chỗ đang dùng YEAR()/tính tuổi/sắp xếp không phải sửa.
 */
class AddBirthdayYearOnlyToStudents extends Migration
{
    private $columns = [
        'crm_students' => ['birthday_year_only' => 'birthday'],
        'students' => ['birthday_year_only' => 'date_of_birth'],
        'crm_import_parents' => [
            'student_birthday_year_only_1' => 'student_birthday_1',
            'student_birthday_year_only_2' => 'student_birthday_2',
        ],
    ];

    public function up()
    {
        foreach ($this->columns as $table => $cols) {
            Schema::table($table, function (Blueprint $t) use ($table, $cols) {
                foreach ($cols as $col => $after) {
                    if (!Schema::hasColumn($table, $col)) {
                        $t->tinyInteger($col)->default(0)->after($after);
                    }
                }
            });
        }
    }

    public function down()
    {
        foreach ($this->columns as $table => $cols) {
            Schema::table($table, function (Blueprint $t) use ($table, $cols) {
                foreach (array_keys($cols) as $col) {
                    if (Schema::hasColumn($table, $col)) {
                        $t->dropColumn($col);
                    }
                }
            });
        }
    }
}
