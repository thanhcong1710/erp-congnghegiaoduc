<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSalaryMonthToAgreementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('agreements', function (Blueprint $table) {
            $table->string('salary_month', 10)->nullable()->comment('Tháng tính lương format YYYY-MM');
        });
        Schema::table('log_agreements', function (Blueprint $table) {
            $table->string('salary_month', 10)->nullable()->comment('Tháng tính lương format YYYY-MM');
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
            $table->dropColumn('salary_month');
        });
        Schema::table('log_agreements', function (Blueprint $table) {
            $table->dropColumn('salary_month');
        });
    }
}
