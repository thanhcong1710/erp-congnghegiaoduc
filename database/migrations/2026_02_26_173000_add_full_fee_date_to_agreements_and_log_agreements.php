<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFullFeeDateToAgreementsAndLogAgreements extends Migration
{
    public function up()
    {
        // agreements
        if (!Schema::hasColumn('agreements', 'full_fee_date')) {
            Schema::table('agreements', function (Blueprint $table) {
                $table->date('full_fee_date')->nullable()->after('debt_amount');
            });
        }
        if (!Schema::hasColumn('agreements', 'count_recharge')) {
            Schema::table('agreements', function (Blueprint $table) {
                $table->unsignedTinyInteger('count_recharge')->default(0)->after('full_fee_date')
                    ->comment('0 = lần đầu đăng ký, 1+ = lần tái ký (cùng tuition_fee)');
            });
        }

        // log_agreements
        if (!Schema::hasColumn('log_agreements', 'full_fee_date')) {
            Schema::table('log_agreements', function (Blueprint $table) {
                $table->date('full_fee_date')->nullable()->after('debt_amount');
            });
        }
        if (!Schema::hasColumn('log_agreements', 'count_recharge')) {
            Schema::table('log_agreements', function (Blueprint $table) {
                $table->unsignedTinyInteger('count_recharge')->default(0)->after('full_fee_date');
            });
        }
    }

    public function down()
    {
        Schema::table('agreements', function (Blueprint $table) {
            $table->dropColumn('full_fee_date');
        });
        Schema::table('log_agreements', function (Blueprint $table) {
            $table->dropColumn('full_fee_date');
        });
    }
}
