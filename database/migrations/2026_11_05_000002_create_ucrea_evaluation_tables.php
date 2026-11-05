<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUcreaEvaluationTables extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        // 1. ucrea_tests: Define test types and Levels
        if (!Schema::hasTable('ucrea_tests')) {
            Schema::create('ucrea_tests', function (Blueprint $table) {
                $table->id();
                $table->string('test_cd', 50)->comment('Mã bài kiểm tra (TT001, TT002...)');
                $table->string('test_nm', 255)->comment('Tên bài kiểm tra');
                $table->string('level_cd', 50)->comment('Level (L1, L2, L3, L4...)');
                $table->string('level_cd_nm', 255)->nullable()->comment('Tên Level (3 y/o, 4 y/o...)');
                $table->integer('test_seq')->comment('Seq của bài kiểm tra gốc');
                $table->timestamps();

                $table->index(['test_cd', 'level_cd', 'test_seq']);
            });
        }

        // 2. ucrea_student_results: Store student results with direct links to ERP entities
        if (!Schema::hasTable('ucrea_student_results')) {
            Schema::create('ucrea_student_results', function (Blueprint $table) {
                $table->id();
                $table->string('test_cd', 50);
                $table->string('level_cd', 50);
                $table->integer('test_seq');
                $table->integer('result_seq')->unique()->comment('Seq của kết quả gốc');

                // ERP Relationships
                $table->integer('student_id')->unsigned()->nullable()->index()->comment('FK trỏ tới students.id');
                $table->integer('class_id')->unsigned()->nullable()->index()->comment('FK trỏ tới classes.id');
                $table->integer('contract_id')->unsigned()->nullable()->index()->comment('FK trỏ tới contracts.id');
                $table->integer('branch_id')->unsigned()->nullable()->index()->comment('FK trỏ tới branches.id');
                $table->unsignedBigInteger('teacher_id')->nullable()->index()->comment('FK trỏ tới users.id');

                // Raw meta from CMS/LMS
                $table->string('memb_nm', 255)->nullable()->comment('Tên giáo viên');
                $table->string('stu_nm', 255)->comment('Tên học sinh');
                $table->string('class_nm', 255)->nullable()->comment('Tên lớp');
                $table->date('eval_dt')->nullable()->comment('Ngày kiểm tra');

                $table->string('result_cd', 50)->comment('Trạng thái (IS002, IS004...)');
                $table->string('result_cd_nm', 255)->nullable()->comment('Tên trạng thái');

                // Radar chart JSON & Skill Grades JSON
                $table->json('report_data')->nullable()->comment('Dữ liệu biểu đồ % các kỹ năng');
                $table->json('skills_grade')->nullable()->comment('Dữ liệu đánh giá chữ (A, B, C...)');

                $table->timestamps();
            });
        }

        // 3. ucrea_student_result_details: Detailed chosen rubric scores
        if (!Schema::hasTable('ucrea_student_result_details')) {
            Schema::create('ucrea_student_result_details', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('ucrea_student_result_id');
                $table->string('question_no', 50)->comment('Số thứ tự / vấn đề số');
                $table->string('main_category', 255)->comment('Lĩnh vực kiểm tra');
                $table->string('sub_category', 255)->comment('Lĩnh vực chi tiết');
                $table->string('rubric_name', 255)->comment('Nội dung đánh giá / Tên Rubric');
                $table->string('assigned_score', 100)->nullable()->comment('Điểm giáo viên chọn');
                $table->timestamps();

                $table->foreign('ucrea_student_result_id')
                    ->references('id')
                    ->on('ucrea_student_results')
                    ->onDelete('cascade');
                $table->index('ucrea_student_result_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('ucrea_student_result_details');
        Schema::dropIfExists('ucrea_student_results');
        Schema::dropIfExists('ucrea_tests');
    }
}

