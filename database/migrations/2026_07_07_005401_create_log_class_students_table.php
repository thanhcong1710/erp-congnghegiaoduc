<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLogClassStudentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('log_class_students', function (Blueprint $table) {
            $table->id();
            $table->integer('class_id')->default(0)->index();
            $table->integer('student_id')->default(0)->index();
            $table->integer('contract_id')->default(0)->index();
            $table->tinyInteger('action')->default(1)->comment('1: Xếp lớp, 0: Xóa khỏi lớp');
            $table->integer('creator_id')->default(0);
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
        Schema::dropIfExists('log_class_students');
    }
}
