<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEnterpriseHrmPhase1Tables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hrm_job_levels', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('e.g. Intern, Fresher, Junior, Middle, Senior, Lead');
            $table->text('description')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });

        Schema::create('hrm_job_titles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('e.g. Developer, QA, Business Analyst, Sales Executive');
            $table->unsignedBigInteger('department_id')->nullable();
            $table->text('description')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });

        Schema::table('hrm_departments', function (Blueprint $table) {
            $table->unsignedBigInteger('parent_id')->nullable()->after('id')->comment('For nested organization chart');
            $table->unsignedBigInteger('branch_id')->nullable()->after('parent_id');
        });

        Schema::table('hrm_employee_profiles', function (Blueprint $table) {
            $table->string('employee_code', 50)->nullable()->after('user_id')->unique();
            $table->string('phone', 20)->nullable()->after('address');
            $table->string('emergency_contact_name')->nullable()->after('phone');
            $table->string('emergency_contact_phone', 20)->nullable()->after('emergency_contact_name');
            $table->string('emergency_contact_relation')->nullable()->after('emergency_contact_phone');
            $table->string('contract_type')->nullable()->after('emergency_contact_relation');
            $table->date('end_date')->nullable()->after('start_date');
            $table->unsignedBigInteger('job_level_id')->nullable()->after('position_id');
            $table->unsignedBigInteger('job_title_id')->nullable()->after('job_level_id');
        });

        Schema::create('hrm_employment_history', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('type')->comment('Transfer, Promotion, Salary Change, Disciplinary, Reward');
            $table->unsignedBigInteger('from_department_id')->nullable();
            $table->unsignedBigInteger('to_department_id')->nullable();
            $table->unsignedBigInteger('from_position_id')->nullable();
            $table->unsignedBigInteger('to_position_id')->nullable();
            $table->decimal('from_salary', 15, 2)->nullable();
            $table->decimal('to_salary', 15, 2)->nullable();
            $table->date('effective_date');
            $table->text('note')->nullable();
            $table->timestamps();
        });

        Schema::create('hrm_employee_documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('document_type')->comment('ID Card, Certificate, Other');
            $table->string('title');
            $table->string('file_path');
            $table->date('expiry_date')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
        });

        Schema::create('hrm_contracts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('contract_number');
            $table->string('contract_type')->comment('Probation, 1 Year, Unlimited, etc');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->decimal('basic_salary', 15, 2)->nullable();
            $table->string('file_path')->nullable();
            $table->tinyInteger('status')->default(1)->comment('1: Active, 0: Expired/Terminated');
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
        Schema::dropIfExists('hrm_contracts');
        Schema::dropIfExists('hrm_employee_documents');
        Schema::dropIfExists('hrm_employment_history');
        
        Schema::table('hrm_employee_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'employee_code', 'phone', 'emergency_contact_name', 
                'emergency_contact_phone', 'emergency_contact_relation', 
                'contract_type', 'end_date', 'job_level_id', 'job_title_id'
            ]);
        });

        Schema::table('hrm_departments', function (Blueprint $table) {
            $table->dropColumn(['parent_id', 'branch_id']);
        });

        Schema::dropIfExists('hrm_job_titles');
        Schema::dropIfExists('hrm_job_levels');
    }
}
