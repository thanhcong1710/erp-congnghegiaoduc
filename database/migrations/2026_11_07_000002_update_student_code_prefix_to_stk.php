<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class UpdateStudentCodePrefixToStk extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 1. Chuyển đổi mã học sinh từ tiền tố ANT sang STK
        DB::statement("UPDATE students SET lms_code = CONCAT('STK', SUBSTRING(lms_code, 4)) WHERE lms_code LIKE 'ANT%'");

        // 2. Cập nhật snapshot meta_data JSON trong các bảng giao dịch để đồng bộ
        DB::statement("UPDATE reserves SET meta_data = REPLACE(meta_data, 'ANT00', 'STK00') WHERE meta_data LIKE '%ANT00%'");
        DB::statement("UPDATE class_transfer SET meta_data = REPLACE(meta_data, 'ANT00', 'STK00') WHERE meta_data LIKE '%ANT00%'");
        DB::statement("UPDATE tuition_transfer SET meta_data = REPLACE(meta_data, 'ANT00', 'STK00') WHERE meta_data LIKE '%ANT00%'");
        DB::statement("UPDATE branch_transfer SET meta_data = REPLACE(meta_data, 'ANT00', 'STK00') WHERE meta_data LIKE '%ANT00%'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("UPDATE students SET lms_code = CONCAT('ANT', SUBSTRING(lms_code, 4)) WHERE lms_code LIKE 'STK%'");
        DB::statement("UPDATE reserves SET meta_data = REPLACE(meta_data, 'STK00', 'ANT00') WHERE meta_data LIKE '%STK00%'");
        DB::statement("UPDATE class_transfer SET meta_data = REPLACE(meta_data, 'STK00', 'ANT00') WHERE meta_data LIKE '%STK00%'");
        DB::statement("UPDATE tuition_transfer SET meta_data = REPLACE(meta_data, 'STK00', 'ANT00') WHERE meta_data LIKE '%STK00%'");
        DB::statement("UPDATE branch_transfer SET meta_data = REPLACE(meta_data, 'STK00', 'ANT00') WHERE meta_data LIKE '%STK00%'");
    }
}

