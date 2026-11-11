<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateHrmCompaniesAndAddCompanyIdToHrmTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 1. Tạo bảng hrm_companies
        if (!Schema::hasTable('hrm_companies')) {
            Schema::create('hrm_companies', function (Blueprint $table) {
                $table->id();
                $table->string('code', 50)->unique()->comment('CHAM, MEGA');
                $table->string('name')->comment('Tên công ty');
                $table->string('tax_code', 50)->nullable();
                $table->string('address')->nullable();
                $table->string('phone', 50)->nullable();
                $table->string('email', 100)->nullable();
                $table->string('employee_prefix', 20)->default('CHAM')->comment('Tiền tố mã nhân viên');
                $table->string('contract_prefix', 20)->default('CHAM')->comment('Tiền tố mã hợp đồng');
                $table->tinyInteger('status')->default(1)->comment('1: Hoạt động, 0: Tạm dừng');
                $table->timestamps();
            });

            // Khởi tạo 2 công ty ban đầu: CHAM (id=1) và MEGA (id=2)
            $now = now();
            DB::table('hrm_companies')->insert([
                [
                    'id' => 1,
                    'code' => 'CHAM',
                    'name' => 'Công ty Cổ phần Giáo dục CHAM',
                    'employee_prefix' => 'CHAM',
                    'contract_prefix' => 'CHAM',
                    'status' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'id' => 2,
                    'code' => 'MEGA',
                    'name' => 'Công ty Cổ phần MEGA',
                    'employee_prefix' => 'MEGA',
                    'contract_prefix' => 'MEGA',
                    'status' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            ]);
        }

        // 2. Thêm company_id vào hrm_employee_profiles
        if (Schema::hasTable('hrm_employee_profiles')) {
            Schema::table('hrm_employee_profiles', function (Blueprint $table) {
                if (!Schema::hasColumn('hrm_employee_profiles', 'company_id')) {
                    $table->unsignedBigInteger('company_id')->default(1)->after('id')->index();
                }
            });

            // Cập nhật toàn bộ nhân sự hiện tại thuộc CHAM (company_id = 1)
            DB::table('hrm_employee_profiles')
                ->whereNull('company_id')
                ->orWhere('company_id', 0)
                ->update(['company_id' => 1]);
        }

        // 3. Thêm company_id vào hrm_departments
        if (Schema::hasTable('hrm_departments')) {
            Schema::table('hrm_departments', function (Blueprint $table) {
                if (!Schema::hasColumn('hrm_departments', 'company_id')) {
                    $table->unsignedBigInteger('company_id')->default(1)->after('id')->index();
                }
            });

            // Cập nhật các phòng ban hiện tại thuộc CHAM (company_id = 1)
            DB::table('hrm_departments')
                ->whereNull('company_id')
                ->orWhere('company_id', 0)
                ->update(['company_id' => 1]);
        }

        // 4. Thêm company_id vào hrm_contracts
        if (Schema::hasTable('hrm_contracts')) {
            Schema::table('hrm_contracts', function (Blueprint $table) {
                if (!Schema::hasColumn('hrm_contracts', 'company_id')) {
                    $table->unsignedBigInteger('company_id')->default(1)->after('id')->index();
                }
            });

            DB::table('hrm_contracts')
                ->whereNull('company_id')
                ->orWhere('company_id', 0)
                ->update(['company_id' => 1]);
        }

        // 5. Thêm company_id vào hrm_shifts
        if (Schema::hasTable('hrm_shifts')) {
            Schema::table('hrm_shifts', function (Blueprint $table) {
                if (!Schema::hasColumn('hrm_shifts', 'company_id')) {
                    $table->unsignedBigInteger('company_id')->nullable()->after('id')->index();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('hrm_shifts') && Schema::hasColumn('hrm_shifts', 'company_id')) {
            Schema::table('hrm_shifts', function (Blueprint $table) {
                $table->dropColumn('company_id');
            });
        }

        if (Schema::hasTable('hrm_contracts') && Schema::hasColumn('hrm_contracts', 'company_id')) {
            Schema::table('hrm_contracts', function (Blueprint $table) {
                $table->dropColumn('company_id');
            });
        }

        if (Schema::hasTable('hrm_departments') && Schema::hasColumn('hrm_departments', 'company_id')) {
            Schema::table('hrm_departments', function (Blueprint $table) {
                $table->dropColumn('company_id');
            });
        }

        if (Schema::hasTable('hrm_employee_profiles') && Schema::hasColumn('hrm_employee_profiles', 'company_id')) {
            Schema::table('hrm_employee_profiles', function (Blueprint $table) {
                $table->dropColumn('company_id');
            });
        }

        Schema::dropIfExists('hrm_companies');
    }
}

