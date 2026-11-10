<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCodeToTmpPaymentsAndPayments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('tmp_payments', 'code')) {
            Schema::table('tmp_payments', function (Blueprint $table) {
                $table->string('code', 50)->nullable()->after('id');
            });
        }

        if (!Schema::hasColumn('payments', 'code')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->string('code', 50)->nullable()->after('id');
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
        if (Schema::hasColumn('tmp_payments', 'code')) {
            Schema::table('tmp_payments', function (Blueprint $table) {
                $table->dropColumn('code');
            });
        }

        if (Schema::hasColumn('payments', 'code')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->dropColumn('code');
            });
        }
    }
}

