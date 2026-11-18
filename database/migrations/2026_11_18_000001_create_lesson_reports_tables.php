<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLessonReportsTables extends Migration
{
    /**
     * Nhận xét buổi học (theo lớp - buổi) gửi phụ huynh.
     * - lesson_reports: 1 dòng / 1 buổi (schedules.id) – nhận xét chung, nội dung bài, BTVN.
     * - lesson_report_students: nhận xét từng học sinh trong buổi (điểm danh đọc từ schedule_has_student).
     */
    public function up()
    {
        if (!Schema::hasTable('lesson_reports')) {
            Schema::create('lesson_reports', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('schedule_id')->unique()->comment('schedules.id');
                $table->integer('class_id')->index();
                $table->integer('branch_id')->nullable()->index();
                $table->date('class_date')->index();
                $table->integer('subject_stt')->nullable()->comment('Buổi thứ');
                $table->string('lesson_title', 255)->nullable()->comment('VD: Unit 2 - Lesson 4 - Letters and sounds');
                $table->string('start_time', 5)->nullable();
                $table->string('end_time', 5)->nullable();
                $table->mediumText('general_comment')->nullable()->comment('HTML: nội dung bài học + bài tập về nhà');
                $table->string('homework_deadline', 255)->nullable();
                $table->integer('teacher_id')->nullable();
                $table->integer('ta_id')->nullable();
                $table->integer('cm_id')->nullable();
                $table->tinyInteger('status')->default(0)->comment('0 nháp, 1 hoàn thành, 2 đã gửi phụ huynh');
                $table->string('share_token', 40)->nullable()->unique();
                $table->dateTime('sent_at')->nullable();
                $table->integer('sent_by')->nullable();
                $table->integer('creator_id')->nullable();
                $table->integer('updator_id')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('lesson_report_students')) {
            Schema::create('lesson_report_students', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('lesson_report_id')->index();
                $table->integer('student_id');
                $table->integer('schedule_has_student_id')->nullable();
                $table->integer('make_up_session_id')->nullable();
                $table->tinyInteger('is_makeup')->default(0);
                $table->tinyInteger('homework_done')->nullable()->comment('1 đã làm BTVN, 0 chưa làm');
                $table->decimal('homework_score', 4, 1)->nullable()->comment('Thang 0-10, để trống được');
                $table->text('comment')->nullable();
                $table->integer('updator_id')->nullable();
                $table->timestamps();
                $table->unique(['lesson_report_id', 'student_id'], 'lrs_report_student_unique');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('lesson_report_students');
        Schema::dropIfExists('lesson_reports');
    }
}
