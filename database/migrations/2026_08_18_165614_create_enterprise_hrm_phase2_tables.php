<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEnterpriseHrmPhase2Tables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hrm_shifts', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('e.g. Ca Sáng, Ca Chiều, Ca Hành chính');
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('late_grace_period')->default(0)->comment('Minutes allowed to be late');
            $table->integer('early_leave_grace_period')->default(0)->comment('Minutes allowed to leave early');
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });

        Schema::create('hrm_shift_assignments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('shift_id');
            $table->date('date');
            $table->tinyInteger('is_overtime')->default(0);
            $table->timestamps();
        });

        Schema::table('hrm_attendances', function (Blueprint $table) {
            $table->string('check_in_ip', 45)->nullable()->after('check_out');
            $table->string('check_out_ip', 45)->nullable()->after('check_in_ip');
            $table->string('check_in_location')->nullable()->after('check_out_ip');
            $table->string('check_out_location')->nullable()->after('check_in_location');
            $table->string('device_id')->nullable()->after('check_out_location');
        });

        Schema::create('hrm_overtime_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->date('date');
            $table->time('start_time');
            $table->time('end_time');
            $table->text('reason')->nullable();
            $table->tinyInteger('status')->default(0)->comment('0: Pending, 1: Approved, 2: Rejected');
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamps();
        });

        Schema::create('hrm_leave_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('e.g. Phép năm, Ốm, Nghỉ không lương');
            $table->integer('days_per_year')->default(0);
            $table->tinyInteger('is_paid')->default(1);
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });

        Schema::create('hrm_leave_balances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('leave_type_id');
            $table->integer('year');
            $table->decimal('total_days', 5, 2)->default(0);
            $table->decimal('used_days', 5, 2)->default(0);
            $table->timestamps();
        });

        Schema::table('hrm_leave_requests', function (Blueprint $table) {
            $table->unsignedBigInteger('leave_type_id')->nullable()->after('type');
            $table->unsignedBigInteger('approved_by')->nullable()->after('status');
            $table->unsignedBigInteger('hr_approved_by')->nullable()->after('approved_by');
            $table->text('rejection_reason')->nullable()->after('hr_approved_by');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('hrm_leave_requests', function (Blueprint $table) {
            $table->dropColumn(['leave_type_id', 'approved_by', 'hr_approved_by', 'rejection_reason']);
        });

        Schema::dropIfExists('hrm_leave_balances');
        Schema::dropIfExists('hrm_leave_types');
        Schema::dropIfExists('hrm_overtime_requests');

        Schema::table('hrm_attendances', function (Blueprint $table) {
            $table->dropColumn(['check_in_ip', 'check_out_ip', 'check_in_location', 'check_out_location', 'device_id', 'status']);
        });

        Schema::dropIfExists('hrm_shift_assignments');
        Schema::dropIfExists('hrm_shifts');
    }
}
