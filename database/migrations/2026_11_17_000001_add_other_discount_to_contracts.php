<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOtherDiscountToContracts extends Migration
{
    /**
     * Giảm trừ khác (nhập tay) để khớp số tiền thu thực tế khi học sinh áp chính sách riêng.
     *
     * @return void
     */
    public function up()
    {
        foreach (['contracts', 'log_contracts'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (!Schema::hasColumn($tableName, 'other_discount_amount')) {
                    $table->decimal('other_discount_amount', 15, 2)->nullable()->default(0)->after('resident_percent')
                        ->comment('Giảm trừ khác (nhập tay)');
                }
                if (!Schema::hasColumn($tableName, 'other_discount_note')) {
                    $table->string('other_discount_note', 500)->nullable()->after('other_discount_amount')
                        ->comment('Lý do giảm trừ khác');
                }
            });
        }
    }

    /**
     * @return void
     */
    public function down()
    {
        foreach (['contracts', 'log_contracts'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                foreach (['other_discount_note', 'other_discount_amount'] as $col) {
                    if (Schema::hasColumn($tableName, $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }
    }
}
