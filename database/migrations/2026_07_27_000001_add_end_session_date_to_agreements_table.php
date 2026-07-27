<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEndSessionDateToAgreementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('agreements', function (Blueprint $table) {
            if (!Schema::hasColumn('agreements', 'end_session_date')) {
                $table->date('end_session_date')->nullable()->comment('Ngày buổi học cuối cùng của contract đầu tiên được xếp lớp');
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
            $table->dropColumn('end_session_date');
        });
    }
}
