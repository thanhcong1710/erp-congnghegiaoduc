<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTeacherSalariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('teacher_salaries', function (Blueprint $table) {
            $table->id();
            $table->integer('teacher_id')->index();
            $table->string('salary_month', 10)->index()->comment('YYYY-MM');
            $table->integer('total_lessons')->default(0)->comment('Tổng số buổi dạy');
            $table->decimal('lesson_price', 15, 2)->default(0)->comment('Đơn giá/buổi');
            $table->decimal('base_salary', 15, 2)->default(0)->comment('Lương cơ bản');
            $table->decimal('bonus', 15, 2)->default(0)->comment('Thưởng');
            $table->decimal('penalty', 15, 2)->default(0)->comment('Phạt');
            $table->decimal('total_salary', 15, 2)->default(0)->comment('Tổng lương');
            $table->tinyInteger('status')->default(0)->comment('0: Nháp, 1: Đã chốt');
            $table->text('note')->nullable()->comment('Ghi chú');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('teacher_salaries');
    }
}
