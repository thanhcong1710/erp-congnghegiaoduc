<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class ChangeLastPayDateToDateInAgreementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE agreements MODIFY last_pay_date DATE');
        DB::statement('ALTER TABLE log_agreements MODIFY last_pay_date DATE');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('ALTER TABLE agreements MODIFY last_pay_date DATETIME');
        DB::statement('ALTER TABLE log_agreements MODIFY last_pay_date DATETIME');
    }
}
