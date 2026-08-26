<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTimekeepingToSchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->string('lesson_code', 50)->nullable()->comment('Mã buổi dạy hoặc link record');
            $table->string('attendance_info', 50)->nullable()->comment('Sĩ số, vd: 8/9');
            $table->timestamp('timekeeping_updated_at')->nullable()->comment('Thời gian giáo viên chấm công');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->dropColumn(['lesson_code', 'attendance_info', 'timekeeping_updated_at']);
        });
    }
}
