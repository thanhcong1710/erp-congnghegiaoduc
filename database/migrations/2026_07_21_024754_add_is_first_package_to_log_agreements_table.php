<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsFirstPackageToLogAgreementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('log_agreements', function (Blueprint $table) {
            if (!Schema::hasColumn('log_agreements', 'is_first_package')) {
                $table->tinyInteger('is_first_package')->default(0)->comment('1: Gói đầu tiên, 0: Gói tái phí');
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
        Schema::table('log_agreements', function (Blueprint $table) {
            $table->dropColumn('is_first_package');
        });
    }
}
