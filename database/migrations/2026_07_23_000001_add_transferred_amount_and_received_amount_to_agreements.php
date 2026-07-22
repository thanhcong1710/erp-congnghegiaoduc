<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTransferredAmountAndReceivedAmountToAgreements extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('agreements', function (Blueprint $table) {
            if (!Schema::hasColumn('agreements', 'transferred_amount')) {
                $table->bigInteger('transferred_amount')->default(0)->after('total_charged');
            }
            if (!Schema::hasColumn('agreements', 'received_amount')) {
                $table->bigInteger('received_amount')->default(0)->after('transferred_amount');
            }
        });

        Schema::table('log_agreements', function (Blueprint $table) {
            if (!Schema::hasColumn('log_agreements', 'transferred_amount')) {
                $table->bigInteger('transferred_amount')->default(0)->after('total_charged');
            }
            if (!Schema::hasColumn('log_agreements', 'received_amount')) {
                $table->bigInteger('received_amount')->default(0)->after('transferred_amount');
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
        Schema::table('agreements', function (Blueprint $table) {
            if (Schema::hasColumn('agreements', 'transferred_amount')) {
                $table->dropColumn('transferred_amount');
            }
            if (Schema::hasColumn('agreements', 'received_amount')) {
                $table->dropColumn('received_amount');
            }
        });

        Schema::table('log_agreements', function (Blueprint $table) {
            if (Schema::hasColumn('log_agreements', 'transferred_amount')) {
                $table->dropColumn('transferred_amount');
            }
            if (Schema::hasColumn('log_agreements', 'received_amount')) {
                $table->dropColumn('received_amount');
            }
        });
    }
}
