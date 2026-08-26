<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTimekeepingInSchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->dropColumn(['lesson_code', 'attendance_info']);
            $table->text('note')->nullable()->comment('Ghi chú buổi học');
            $table->string('link_record')->nullable()->comment('Link record buổi học');
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
            $table->string('lesson_code', 50)->nullable();
            $table->string('attendance_info', 50)->nullable();
            $table->dropColumn(['note', 'link_record']);
        });
    }
}
