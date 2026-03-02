<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDiscountToAgreementsAndContracts extends Migration
{
    public function up()
    {
        // agreements
        Schema::table('agreements', function (Blueprint $table) {
            $table->bigInteger('discount_amount')->default(0)->after('debt_amount')->comment('Số tiền giảm trừ do KT phê duyệt');
            $table->text('discount_note')->nullable()->after('discount_amount')->comment('Lý do giảm trừ');
        });

        // log_agreements
        Schema::table('log_agreements', function (Blueprint $table) {
            $table->bigInteger('discount_amount')->default(0)->after('debt_amount');
            $table->text('discount_note')->nullable()->after('discount_amount');
        });

        // contracts
        Schema::table('contracts', function (Blueprint $table) {
            $table->bigInteger('discount_amount')->default(0)->after('debt_amount')->comment('Giảm trừ phân bổ từ agreement');
            $table->text('discount_note')->nullable()->after('discount_amount');
        });

        // log_contracts
        Schema::table('log_contracts', function (Blueprint $table) {
            $table->bigInteger('discount_amount')->default(0)->after('debt_amount');
            $table->text('discount_note')->nullable()->after('discount_amount');
        });
    }

    public function down()
    {
        Schema::table('agreements', function (Blueprint $table) {
            $table->dropColumn(['discount_amount', 'discount_note']);
        });
        Schema::table('log_agreements', function (Blueprint $table) {
            $table->dropColumn(['discount_amount', 'discount_note']);
        });
        Schema::table('contracts', function (Blueprint $table) {
            $table->dropColumn(['discount_amount', 'discount_note']);
        });
        Schema::table('log_contracts', function (Blueprint $table) {
            $table->dropColumn(['discount_amount', 'discount_note']);
        });
    }
}
