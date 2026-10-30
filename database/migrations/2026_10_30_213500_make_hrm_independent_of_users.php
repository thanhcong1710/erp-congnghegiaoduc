<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MakeHrmIndependentOfUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 1. hrm_employee_profiles: add full_name and email
        Schema::table('hrm_employee_profiles', function (Blueprint $table) {
            if (!Schema::hasColumn('hrm_employee_profiles', 'full_name')) {
                $table->string('full_name')->nullable()->after('employee_code');
            }
            if (!Schema::hasColumn('hrm_employee_profiles', 'email')) {
                $table->string('email')->nullable()->after('full_name');
            }
        });

        // 2. hrm_attendances: add employee_id
        Schema::table('hrm_attendances', function (Blueprint $table) {
            if (!Schema::hasColumn('hrm_attendances', 'employee_id')) {
                $table->unsignedBigInteger('employee_id')->nullable()->after('id')->index();
            }
        });

        // 3. hrm_shift_assignments: add employee_id
        Schema::table('hrm_shift_assignments', function (Blueprint $table) {
            if (!Schema::hasColumn('hrm_shift_assignments', 'employee_id')) {
                $table->unsignedBigInteger('employee_id')->nullable()->after('id')->index();
            }
        });

        // 4. hrm_leave_requests: add employee_id
        Schema::table('hrm_leave_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('hrm_leave_requests', 'employee_id')) {
                $table->unsignedBigInteger('employee_id')->nullable()->after('id')->index();
            }
        });

        // 5. hrm_leave_balances: add employee_id
        Schema::table('hrm_leave_balances', function (Blueprint $table) {
            if (!Schema::hasColumn('hrm_leave_balances', 'employee_id')) {
                $table->unsignedBigInteger('employee_id')->nullable()->after('id')->index();
            }
        });

        // 6. hrm_contracts: add employee_id
        Schema::table('hrm_contracts', function (Blueprint $table) {
            if (!Schema::hasColumn('hrm_contracts', 'employee_id')) {
                $table->unsignedBigInteger('employee_id')->nullable()->after('id')->index();
            }
        });

        // 7. hrm_employee_documents: add employee_id
        Schema::table('hrm_employee_documents', function (Blueprint $table) {
            if (!Schema::hasColumn('hrm_employee_documents', 'employee_id')) {
                $table->unsignedBigInteger('employee_id')->nullable()->after('id')->index();
            }
        });

        // 8. hrm_employment_history: add employee_id
        Schema::table('hrm_employment_history', function (Blueprint $table) {
            if (!Schema::hasColumn('hrm_employment_history', 'employee_id')) {
                $table->unsignedBigInteger('employee_id')->nullable()->after('id')->index();
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('hrm_employment_history', function (Blueprint $table) {
            $table->dropColumn('employee_id');
        });
        Schema::table('hrm_employee_documents', function (Blueprint $table) {
            $table->dropColumn('employee_id');
        });
        Schema::table('hrm_contracts', function (Blueprint $table) {
            $table->dropColumn('employee_id');
        });
        Schema::table('hrm_leave_balances', function (Blueprint $table) {
            $table->dropColumn('employee_id');
        });
        Schema::table('hrm_leave_requests', function (Blueprint $table) {
            $table->dropColumn('employee_id');
        });
        Schema::table('hrm_shift_assignments', function (Blueprint $table) {
            $table->dropColumn('employee_id');
        });
        Schema::table('hrm_attendances', function (Blueprint $table) {
            $table->dropColumn('employee_id');
        });
        Schema::table('hrm_employee_profiles', function (Blueprint $table) {
            $table->dropColumn(['full_name', 'email']);
        });
    }
}

