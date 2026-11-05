<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIgbhEvaluationTables extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        // 1. igbh_student_results (Diagnostic Evaluation)
        if (!Schema::hasTable('igbh_student_results')) {
            Schema::create('igbh_student_results', function (Blueprint $table) {
                $table->id();
                $table->integer('result_seq')->unique()->comment('Seq của kết quả gốc');
                $table->integer('test_seq');
                $table->string('stu_seq', 50)->nullable()->comment('Seq học sinh gốc LMS');

                // ERP Relationships
                $table->integer('student_id')->unsigned()->nullable()->index()->comment('FK trỏ tới students.id');
                $table->integer('class_id')->unsigned()->nullable()->index()->comment('FK trỏ tới classes.id');
                $table->integer('contract_id')->unsigned()->nullable()->index()->comment('FK trỏ tới contracts.id');
                $table->integer('branch_id')->unsigned()->nullable()->index()->comment('FK trỏ tới branches.id');
                $table->unsignedBigInteger('teacher_id')->nullable()->index()->comment('FK trỏ tới users.id');

                // Metadata
                $table->string('stu_nm', 255)->comment('Tên học sinh');
                $table->string('stu_birth_dt', 50)->nullable()->comment('Ngày sinh học sinh');
                $table->string('reg_name', 255)->nullable()->comment('Người đăng ký / Giáo viên');
                $table->date('eval_dt')->nullable()->comment('Ngày thi');
                $table->timestamp('reg_date')->nullable()->comment('Ngày đăng ký');

                $table->integer('total_score')->default(0);
                $table->integer('subject_total')->default(0);
                $table->integer('thinking_total')->default(0);

                $table->string('assigned_level', 50)->nullable()->comment('Level bài tập được gán');
                $table->string('quarter_cd', 50)->nullable();
                $table->string('quarter_cd_nm', 255)->nullable();
                $table->string('class_type_cd', 50)->nullable();

                $table->timestamps();

                $table->index(['test_seq', 'student_id']);
            });
        }

        // 2. igbh_student_result_details
        if (!Schema::hasTable('igbh_student_result_details')) {
            Schema::create('igbh_student_result_details', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('igbh_student_result_id');
                $table->string('question_no', 50)->comment('Số thứ tự câu hỏi');
                $table->string('question_type', 50)->comment('curriculum | thinking');
                $table->integer('seq_id')->nullable()->comment('saSeq hoặc taSeq');

                $table->string('assigned_score', 50)->nullable()->comment('Đáp án chọn hoặc Điểm');
                $table->string('unit', 50)->nullable()->comment('Đơn vị phân loại (A, B, C...)');
                $table->string('is_correct', 10)->nullable()->comment('Đánh giá O hoặc X');
                $table->integer('max_score')->nullable()->comment('Điểm tối đa của câu hỏi');

                $table->timestamps();

                $table->foreign('igbh_student_result_id')
                    ->references('id')
                    ->on('igbh_student_results')
                    ->onDelete('cascade');
                $table->index('igbh_student_result_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('igbh_student_result_details');
        Schema::dropIfExists('igbh_student_results');
    }
}

