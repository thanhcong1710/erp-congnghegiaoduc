<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class LessonReportsWeeklyAndPlans extends Migration
{
    /**
     * Nhận xét chuyển sang theo TUẦN (1 phiếu / lớp / tuần, gắn vào buổi cuối tuần – lesson_reports.schedule_id).
     * - lesson_reports.week_start / week_no: tuần (thứ 2) & tuần thứ mấy của lớp.
     * - lesson_report_students.share_token: link riêng từng học sinh (PH chỉ xem nhận xét của con mình).
     * - lesson_plans: nội dung bài học cố định theo lộ trình (khóa học × tuần thứ N).
     */
    public function up()
    {
        if (Schema::hasTable('lesson_reports') && !Schema::hasColumn('lesson_reports', 'week_start')) {
            Schema::table('lesson_reports', function (Blueprint $table) {
                $table->date('week_start')->nullable()->index()->after('class_date')->comment('Thứ 2 của tuần');
                $table->integer('week_no')->nullable()->after('week_start')->comment('Tuần thứ mấy của lớp');
            });
        }
        if (Schema::hasTable('lesson_report_students') && !Schema::hasColumn('lesson_report_students', 'share_token')) {
            Schema::table('lesson_report_students', function (Blueprint $table) {
                $table->string('share_token', 40)->nullable()->unique()->after('comment')->comment('Link riêng gửi phụ huynh');
            });
        }
        if (!Schema::hasTable('lesson_plans')) {
            Schema::create('lesson_plans', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('product_id')->comment('products.id');
                $table->integer('week_no')->comment('Tuần thứ N của khóa');
                $table->string('lesson_title', 255)->nullable();
                $table->mediumText('content')->nullable()->comment('HTML: nội dung bài học + BTVN');
                $table->string('homework_deadline', 255)->nullable();
                $table->integer('creator_id')->nullable();
                $table->integer('updator_id')->nullable();
                $table->timestamps();
                $table->unique(['product_id', 'week_no'], 'lesson_plans_product_week_unique');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('lesson_plans');
        if (Schema::hasColumn('lesson_report_students', 'share_token')) {
            Schema::table('lesson_report_students', function (Blueprint $table) {
                $table->dropUnique(['share_token']);
                $table->dropColumn('share_token');
            });
        }
        if (Schema::hasColumn('lesson_reports', 'week_start')) {
            Schema::table('lesson_reports', function (Blueprint $table) {
                $table->dropIndex(['week_start']);
                $table->dropColumn(['week_start', 'week_no']);
            });
        }
    }
}
