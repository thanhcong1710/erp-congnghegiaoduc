<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTuitionFeeIdAndRevenueAfterSplitToAgreementsRevenueHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('agreements_revenue_histories', function (Blueprint $table) {
            $table->unsignedBigInteger('tuition_fee_id')->nullable()->after('revenue_amount');
            $table->decimal('separated_sales', 15, 2)->default(0)->after('tuition_fee_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('agreements_revenue_histories', function (Blueprint $table) {
            $table->dropColumn(['tuition_fee_id', 'separated_sales']);
        });
    }
}
