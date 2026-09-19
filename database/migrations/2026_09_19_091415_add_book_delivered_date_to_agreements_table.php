<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBookDeliveredDateToAgreementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('agreements', function (Blueprint $table) {
            if (!Schema::hasColumn('agreements', 'book_delivered_date')) {
                $table->date('book_delivered_date')->nullable()->after('book_receive');
            }
        });
        Schema::table('log_agreements', function (Blueprint $table) {
            if (!Schema::hasColumn('log_agreements', 'book_delivered_date')) {
                $table->date('book_delivered_date')->nullable()->after('book_receive');
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
            $table->dropColumn('book_delivered_date');
        });
        Schema::table('log_agreements', function (Blueprint $table) {
            $table->dropColumn('book_delivered_date');
        });
    }
}
