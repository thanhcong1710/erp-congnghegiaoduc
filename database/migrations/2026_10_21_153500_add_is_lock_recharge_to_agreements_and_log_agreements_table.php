<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddIsLockRechargeToAgreementsAndLogAgreementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('agreements', 'is_lock_recharge')) {
            Schema::table('agreements', function (Blueprint $table) {
                $table->tinyInteger('is_lock_recharge')->default(0)->after('count_recharge')
                    ->comment('0: Cho phép chạy lại count_recharge, 1: Đã khóa theo chốt lương');
            });
        }

        if (!Schema::hasColumn('log_agreements', 'is_lock_recharge')) {
            Schema::table('log_agreements', function (Blueprint $table) {
                $table->tinyInteger('is_lock_recharge')->default(0)->after('count_recharge')
                    ->comment('0: Cho phép chạy lại count_recharge, 1: Đã khóa theo chốt lương');
            });
        }

        // Cập nhật các bản ghi đã chốt tính lương trước đó
        $day = (int) date('d');
        $month_diff = $day < 5 ? 2 : 1;
        $closing_month = date('Y-m', strtotime(date('Y-m-01') . " -$month_diff month"));

        DB::statement("
            UPDATE agreements 
            SET is_lock_recharge = 1 
            WHERE salary_month IS NOT NULL 
              AND salary_month != '' 
              AND salary_month <= ?
        ", [$closing_month]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('agreements', 'is_lock_recharge')) {
            Schema::table('agreements', function (Blueprint $table) {
                $table->dropColumn('is_lock_recharge');
            });
        }

        if (Schema::hasColumn('log_agreements', 'is_lock_recharge')) {
            Schema::table('log_agreements', function (Blueprint $table) {
                $table->dropColumn('is_lock_recharge');
            });
        }
    }
}
