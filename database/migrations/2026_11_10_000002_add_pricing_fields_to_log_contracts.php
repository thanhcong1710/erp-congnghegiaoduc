<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPricingFieldsToLogContracts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('log_contracts', function (Blueprint $table) {
            if (!Schema::hasColumn('log_contracts', 'tuition_receivable')) {
                $table->decimal('tuition_receivable', 15, 2)->nullable()->default(0)->after('init_tuition_fee_receivable');
            }
            if (!Schema::hasColumn('log_contracts', 'book_receivable')) {
                $table->decimal('book_receivable', 15, 2)->nullable()->default(0)->after('tuition_receivable');
            }
            if (!Schema::hasColumn('log_contracts', 'scholarship_percent')) {
                $table->float('scholarship_percent')->nullable()->default(0)->after('book_receivable');
            }
            if (!Schema::hasColumn('log_contracts', 'promo_extra_percent')) {
                $table->float('promo_extra_percent')->nullable()->default(0)->after('scholarship_percent');
            }
            if (!Schema::hasColumn('log_contracts', 'checkin_group_percent')) {
                $table->float('checkin_group_percent')->nullable()->default(0)->after('promo_extra_percent');
            }
            if (!Schema::hasColumn('log_contracts', 'resident_percent')) {
                $table->float('resident_percent')->nullable()->default(0)->after('checkin_group_percent');
            }
            if (!Schema::hasColumn('log_contracts', 'gift_note')) {
                $table->string('gift_note', 500)->nullable()->after('note');
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
        Schema::table('log_contracts', function (Blueprint $table) {
            $cols = [
                'tuition_receivable',
                'book_receivable',
                'scholarship_percent',
                'promo_extra_percent',
                'checkin_group_percent',
                'resident_percent',
                'gift_note'
            ];
            foreach ($cols as $col) {
                if (Schema::hasColumn('log_contracts', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
}

