<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTaTutoringsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ta_group_tutorings', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('ta_id')->unsigned()->nullable();
            $table->integer('product_id')->unsigned()->nullable();
            $table->integer('class_id')->unsigned()->nullable();
            $table->string('group_name', 255)->nullable();
            $table->integer('session_index')->unsigned()->nullable();
            $table->date('expected_date')->nullable();
            $table->string('expected_time', 255)->nullable();
            $table->text('support_request')->nullable();
            $table->string('lp_check_status', 255)->nullable();
            $table->string('record_link', 500)->nullable();
            $table->text('record_note')->nullable();
            $table->tinyInteger('is_inspected')->default(0);
            $table->tinyInteger('is_note_read')->default(0);
            $table->timestamps();
        });

        Schema::create('ta_one_on_one_tutorings', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('ta_id')->unsigned()->nullable();
            $table->integer('product_id')->unsigned()->nullable();
            $table->integer('class_id')->unsigned()->nullable();
            $table->integer('student_id')->unsigned()->nullable();
            $table->integer('session_index')->unsigned()->nullable();
            $table->date('expected_date')->nullable();
            $table->string('expected_time', 255)->nullable();
            $table->text('support_request')->nullable();
            $table->string('lp_check_status', 255)->nullable();
            $table->string('record_link', 500)->nullable();
            $table->text('record_note')->nullable();
            $table->tinyInteger('is_inspected')->default(0);
            $table->tinyInteger('is_note_read')->default(0);
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
        Schema::dropIfExists('ta_group_tutorings');
        Schema::dropIfExists('ta_one_on_one_tutorings');
    }
}
