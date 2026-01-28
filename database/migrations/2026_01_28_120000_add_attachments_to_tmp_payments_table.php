<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAttachmentsToTmpPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tmp_payments', function (Blueprint $table) {
            $table->text('attachments')->nullable()->after('meta_data')->comment('JSON array of file paths');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tmp_payments', function (Blueprint $table) {
            $table->dropColumn('attachments');
        });
    }
}
