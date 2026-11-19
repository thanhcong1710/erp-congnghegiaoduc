<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDayPartToHrmLeaveRequests extends Migration
{
    /**
     * Thêm cột buổi nghỉ (cả ngày / sáng / chiều) cho đơn xin nghỉ.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('hrm_leave_requests', 'day_part')) {
            return;
        }
        Schema::table('hrm_leave_requests', function (Blueprint $table) {
            $table->string('day_part', 10)->default('full')->after('duration_days')
                ->comment('full: cả ngày, morning: nghỉ sáng, afternoon: nghỉ chiều');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('hrm_leave_requests', function (Blueprint $table) {
            $table->dropColumn('day_part');
        });
    }
}
