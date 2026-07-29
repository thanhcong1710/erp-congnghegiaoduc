<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRelearnFromContractIdToContractsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->unsignedBigInteger('relearn_from_contract_id')->nullable()->after('debt_amount');
        });
        Schema::table('log_contracts', function (Blueprint $table) {
            $table->unsignedBigInteger('relearn_from_contract_id')->nullable()->after('debt_amount');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->dropColumn('relearn_from_contract_id');
        });
        Schema::table('log_contracts', function (Blueprint $table) {
            $table->dropColumn('relearn_from_contract_id');
        });
    }
}
