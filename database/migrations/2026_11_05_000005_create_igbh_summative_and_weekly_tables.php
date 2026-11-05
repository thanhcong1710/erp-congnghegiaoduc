<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIgbhSummativeAndWeeklyTables extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        // 1. igbh_summative_themes
        if (!Schema::hasTable('igbh_summative_themes')) {
            Schema::create('igbh_summative_themes', function (Blueprint $table) {
                $table->id();
                $table->integer('test_seq')->comment('Seq bài kiểm tra summative');
                $table->integer('sort_no')->comment('Thứ tự câu (1-12)');
                $table->integer('theme_seq')->nullable()->comment('theme_seq gốc từ CMS');
                $table->string('theme_desc', 255)->comment('Nội dung đánh giá');
                $table->integer('theme_point')->default(0)->comment('Điểm chuẩn');
                $table->timestamps();

                $table->index(['test_seq', 'sort_no']);
            });
        }

        // 2. igbh_weekly_evals (Đánh giá hàng tuần theo Lớp)
        if (!Schema::hasTable('igbh_weekly_evals')) {
            Schema::create('igbh_weekly_evals', function (Blueprint $table) {
                $table->id();
                $table->integer('test_seq')->comment('Bài kiểm tra summative');

                // ERP Relationships
                $table->integer('class_id')->unsigned()->nullable()->index()->comment('FK trỏ tới classes.id');
                $table->integer('branch_id')->unsigned()->nullable()->index()->comment('FK trỏ tới branches.id');
                $table->unsignedBigInteger('teacher_id')->nullable()->index()->comment('FK trỏ tới users.id');

                // Metadata
                $table->integer('class_seq')->comment('Mã lớp CMS/LMS');
                $table->string('class_nm', 100)->nullable()->comment('Tên lớp');
                $table->string('each_cd', 20)->comment('Mã tuần (EC001..EC012)');
                $table->string('each_cd_nm', 50)->nullable()->comment('Tên tuần');
                $table->string('teacher_nm', 100)->nullable()->comment('Tên giáo viên');
                $table->date('eval_ymd')->nullable()->comment('Ngày kiểm tra');
                $table->string('status', 20)->default('Draft')->comment('Draft, Completed');
                $table->timestamps();

                $table->unique(['test_seq', 'class_seq', 'each_cd']);
            });
        }

        // 3. igbh_weekly_eval_details
        if (!Schema::hasTable('igbh_weekly_eval_details')) {
            Schema::create('igbh_weekly_eval_details', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('weekly_eval_id');

                // ERP Relationship
                $table->integer('student_id')->unsigned()->nullable()->index()->comment('FK trỏ tới students.id');

                // Metadata
                $table->string('stu_seq', 50)->comment('Mã học sinh gốc');
                $table->string('stu_nm', 100)->nullable()->comment('Tên học sinh');
                $table->integer('workbook')->default(0)->comment('Sách bài tập (1~20)');
                $table->integer('attd_listen')->default(5)->comment('Khả năng lắng nghe (1~5)');
                $table->integer('attd_join')->default(5)->comment('Tham gia bài học (1~5)');
                $table->integer('attd_express')->default(5)->comment('Khả năng thể hiện (1~5)');
                $table->integer('attd_coop')->default(5)->comment('Sự hợp tác (1~5)');
                $table->integer('detect_normal')->default(5)->comment('Kỹ năng cơ bản (1~5)');
                $table->integer('detect_leadersh')->default(5)->comment('Khả năng lãnh đạo (1~5)');
                $table->integer('detect_math')->default(5)->comment('Khả năng toán học (1~5)');
                $table->integer('detect_creative')->default(5)->comment('Tính sáng tạo (1~5)');
                $table->timestamps();

                $table->foreign('weekly_eval_id')
                    ->references('id')
                    ->on('igbh_weekly_evals')
                    ->onDelete('cascade');
                $table->index(['weekly_eval_id', 'student_id']);
            });
        }

        // 4. igbh_class_summative_evals (Đánh giá tổng kết theo Lớp)
        if (!Schema::hasTable('igbh_class_summative_evals')) {
            Schema::create('igbh_class_summative_evals', function (Blueprint $table) {
                $table->id();
                $table->integer('test_seq')->comment('Bài kiểm tra summative');

                // ERP Relationships
                $table->integer('class_id')->unsigned()->nullable()->index()->comment('FK trỏ tới classes.id');
                $table->integer('branch_id')->unsigned()->nullable()->index()->comment('FK trỏ tới branches.id');
                $table->unsignedBigInteger('teacher_id')->nullable()->index()->comment('FK trỏ tới users.id');

                // Metadata
                $table->integer('class_seq')->comment('Lớp gốc CMS');
                $table->string('class_nm', 100)->nullable()->comment('Tên lớp');
                $table->string('teacher_nm', 100)->nullable()->comment('Tên giáo viên');
                $table->date('eval_ymd')->nullable()->comment('Ngày đánh giá');
                $table->string('status', 20)->default('draft')->comment('Trạng thái: draft, completed');
                $table->timestamps();

                $table->unique(['test_seq', 'class_seq']);
            });
        }

        // 5. igbh_summative_results (Tóm tắt kết quả tổng kết từng học sinh)
        if (!Schema::hasTable('igbh_summative_results')) {
            Schema::create('igbh_summative_results', function (Blueprint $table) {
                $table->id();
                $table->integer('test_seq')->comment('Bài kiểm tra summative');

                // ERP Relationships
                $table->integer('student_id')->unsigned()->nullable()->index()->comment('FK trỏ tới students.id');
                $table->integer('class_id')->unsigned()->nullable()->index()->comment('FK trỏ tới classes.id');
                $table->integer('contract_id')->unsigned()->nullable()->index()->comment('FK trỏ tới contracts.id');
                $table->integer('branch_id')->unsigned()->nullable()->index()->comment('FK trỏ tới branches.id');
                $table->unsignedBigInteger('teacher_id')->nullable()->index()->comment('FK trỏ tới users.id');

                // Metadata
                $table->string('stu_seq', 50)->comment('Mã học sinh gốc');
                $table->string('stu_nm', 100)->nullable()->comment('Tên học sinh');
                $table->integer('class_seq')->nullable()->comment('Lớp');
                $table->string('class_nm', 100)->nullable()->comment('Tên lớp');
                $table->string('teacher_nm', 100)->nullable()->comment('Tên giáo viên');
                $table->integer('total_score')->default(0)->comment('Tổng điểm');
                $table->date('eval_dt')->nullable()->comment('Ngày đánh giá');
                $table->string('status', 20)->nullable()->comment('Trạng thái');
                $table->json('subjective_analysis')->nullable()->comment('Phân tích định tính');
                $table->timestamps();

                $table->unique(['test_seq', 'stu_seq']);
            });
        }

        // 6. igbh_summative_result_details
        if (!Schema::hasTable('igbh_summative_result_details')) {
            Schema::create('igbh_summative_result_details', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('summative_result_id');
                $table->integer('sort_no')->comment('Số thứ tự câu');
                $table->float('max_score')->nullable();
                $table->float('score')->default(0)->comment('Điểm đạt được');
                $table->float('concept')->nullable();
                $table->float('strategy')->nullable();
                $table->float('calculation')->nullable();
                $table->float('expression')->nullable();
                $table->timestamps();

                $table->foreign('summative_result_id')
                    ->references('id')
                    ->on('igbh_summative_results')
                    ->onDelete('cascade');
                $table->index('summative_result_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('igbh_summative_result_details');
        Schema::dropIfExists('igbh_summative_results');
        Schema::dropIfExists('igbh_class_summative_evals');
        Schema::dropIfExists('igbh_weekly_eval_details');
        Schema::dropIfExists('igbh_weekly_evals');
        Schema::dropIfExists('igbh_summative_themes');
    }
}

