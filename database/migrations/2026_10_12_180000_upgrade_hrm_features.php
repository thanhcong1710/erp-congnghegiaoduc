<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpgradeHrmFeatures extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('hrm_employee_profiles', function (Blueprint $table) {
            $table->string('employee_type', 30)->default('fulltime')->after('employee_code')->comment('fulltime, ctv, probation');
            $table->date('probation_start_date')->nullable()->after('employee_type');
            $table->date('probation_end_date')->nullable()->after('probation_start_date');
            $table->string('work_status', 30)->default('working')->after('probation_end_date')->comment('working, resigned');
            $table->date('resigned_date')->nullable()->after('work_status');
            $table->text('resigned_reason')->nullable()->after('resigned_date');
            $table->tinyInteger('approval_status')->default(1)->after('resigned_reason')->comment('1: approved, 0: pending_hr');
            $table->unsignedBigInteger('created_by_user_id')->nullable()->after('approval_status');
            
            // Hồ sơ lưu trữ checklist
            $table->date('profile_received_date')->nullable()->after('created_by_user_id');
            $table->string('profile_status', 30)->default('missing')->after('profile_received_date')->comment('full, missing');
            $table->json('missing_documents')->nullable()->after('profile_status');
            $table->text('profile_note')->nullable()->after('missing_documents');
        });

        Schema::table('hrm_attendances', function (Blueprint $table) {
            $table->string('attendance_status', 50)->default('present')->after('status')->comment('present, leave_approved, unexcused_absent, late_fined, late_submitted');
            $table->decimal('penalty_amount', 15, 2)->default(0)->after('attendance_status');
        });

        Schema::table('hrm_leave_requests', function (Blueprint $table) {
            $table->string('request_category', 30)->default('leave')->after('type')->comment('leave, shift_change, off_to_onl');
            $table->string('approval_type', 30)->nullable()->after('status')->comment('approved, half_salary, late_unpaid, rejected');
            $table->decimal('duration_days', 4, 1)->default(1.0)->after('end_date')->comment('Number of days');
            $table->string('target_shift_info')->nullable()->after('duration_days')->comment('For shift change');
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
            $table->dropColumn(['request_category', 'approval_type', 'duration_days', 'target_shift_info']);
        });

        Schema::table('hrm_attendances', function (Blueprint $table) {
            $table->dropColumn(['attendance_status', 'penalty_amount']);
        });

        Schema::table('hrm_employee_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'employee_type',
                'probation_start_date',
                'probation_end_date',
                'work_status',
                'resigned_date',
                'resigned_reason',
                'approval_status',
                'created_by_user_id',
                'profile_received_date',
                'profile_status',
                'missing_documents',
                'profile_note'
            ]);
        });
    }
}
