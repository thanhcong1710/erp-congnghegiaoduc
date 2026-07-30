<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBookInfoToContractsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contracts', function (Blueprint $table) {
            if (!Schema::hasColumn('contracts', 'book_note')) {
                $table->string('book_note')->nullable()->comment('Ghi chú phát sách');
            }
            if (!Schema::hasColumn('contracts', 'book_class_id')) {
                $table->integer('book_class_id')->nullable()->comment('Lớp tại thời điểm phát sách');
            }
        });

        Schema::table('log_contracts', function (Blueprint $table) {
            if (!Schema::hasColumn('log_contracts', 'book_note')) {
                $table->string('book_note')->nullable()->comment('Ghi chú phát sách');
            }
            if (!Schema::hasColumn('log_contracts', 'book_class_id')) {
                $table->integer('book_class_id')->nullable()->comment('Lớp tại thời điểm phát sách');
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
            if (Schema::hasColumn('contracts', 'book_note')) {
                $table->dropColumn('book_note');
            }
            if (Schema::hasColumn('contracts', 'book_class_id')) {
                $table->dropColumn('book_class_id');
            }
        });

        Schema::table('log_contracts', function (Blueprint $table) {
            if (Schema::hasColumn('log_contracts', 'book_note')) {
                $table->dropColumn('book_note');
            }
            if (Schema::hasColumn('log_contracts', 'book_class_id')) {
                $table->dropColumn('book_class_id');
            }
        });
    }
}
