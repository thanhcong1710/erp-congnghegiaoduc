<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEnrolmentNoteToContractsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contracts', function (Blueprint $table) {
            if (!Schema::hasColumn('contracts', 'enrolment_note')) {
                $table->text('enrolment_note')->nullable()->comment('Ghi chú xếp lớp');
            }
        });

        Schema::table('log_contracts', function (Blueprint $table) {
            if (!Schema::hasColumn('log_contracts', 'enrolment_note')) {
                $table->text('enrolment_note')->nullable()->comment('Ghi chú xếp lớp');
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
        Schema::table('contracts', function (Blueprint $table) {
            if (Schema::hasColumn('contracts', 'enrolment_note')) {
                $table->dropColumn('enrolment_note');
            }
        });

        Schema::table('log_contracts', function (Blueprint $table) {
            if (Schema::hasColumn('log_contracts', 'enrolment_note')) {
                $table->dropColumn('enrolment_note');
            }
        });
    }
}
