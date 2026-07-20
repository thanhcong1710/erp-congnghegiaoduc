<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLastPayDateAnd8thSessionDateToLogAgreementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('log_agreements', function (Blueprint $table) {
            if (!Schema::hasColumn('log_agreements', 'last_pay_date')) {
                $table->dateTime('last_pay_date')->nullable();
            }
            if (!Schema::hasColumn('log_agreements', 'first_8th_session_date')) {
                $table->date('first_8th_session_date')->nullable();
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
            $table->dropColumn('last_pay_date');
            $table->dropColumn('first_8th_session_date');
        });
    }
}
