<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHrmTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hrm_departments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable();
            $table->unsignedBigInteger('manager_id')->nullable()->comment('User ID of the department manager');
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });

        Schema::create('hrm_positions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('department_id')->nullable();
            $table->string('description')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });

        Schema::create('hrm_employee_profiles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->unique();
            $table->unsignedBigInteger('department_id')->nullable();
            $table->unsignedBigInteger('position_id')->nullable();
            $table->string('id_card_number')->nullable();
            $table->string('address')->nullable();
            $table->date('dob')->nullable();
            $table->tinyInteger('gender')->nullable()->comment('1: Male, 2: Female, 3: Other');
            $table->date('start_date')->nullable();
            $table->decimal('base_salary', 15, 2)->nullable();
            $table->string('bank_account')->nullable();
            $table->string('bank_name')->nullable();
            $table->timestamps();
        });

        Schema::create('hrm_leave_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('type')->comment('sick, vacation, unpaid, maternity');
            $table->date('start_date');
            $table->date('end_date');
            $table->text('reason')->nullable();
            $table->tinyInteger('status')->default(0)->comment('0: Pending, 1: Approved, 2: Rejected');
            $table->unsignedBigInteger('approver_id')->nullable();
            $table->timestamps();
        });

        Schema::create('hrm_attendances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->date('date');
            $table->dateTime('check_in')->nullable();
            $table->dateTime('check_out')->nullable();
            $table->string('note')->nullable();
            $table->tinyInteger('status')->default(1)->comment('1: Present, 2: Late, 3: Absent, 4: Half-day');
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
        Schema::dropIfExists('hrm_attendances');
        Schema::dropIfExists('hrm_leave_requests');
        Schema::dropIfExists('hrm_employee_profiles');
        Schema::dropIfExists('hrm_positions');
        Schema::dropIfExists('hrm_departments');
    }
}
