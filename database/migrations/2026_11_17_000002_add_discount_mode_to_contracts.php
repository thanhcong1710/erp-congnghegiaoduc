<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDiscountModeToContracts extends Migration
{
    /**
     * Cách tính ưu đãi STEMKey: cascade = Giảm dần (mặc định), base = Theo giá gốc.
     *
     * @return void
     */
    public function up()
    {
        foreach (['contracts', 'log_contracts'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (!Schema::hasColumn($tableName, 'discount_mode')) {
                    $table->string('discount_mode', 20)->nullable()->default('cascade')->after('resident_percent')
                        ->comment('Cách tính ưu đãi: cascade = Giảm dần, base = Theo giá gốc');
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
                if (Schema::hasColumn($tableName, 'discount_mode')) {
                    $table->dropColumn('discount_mode');
                }
            });
        }
    }
}
