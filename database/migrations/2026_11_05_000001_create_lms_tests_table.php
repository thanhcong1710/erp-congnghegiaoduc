<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLmsTestsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        if (!Schema::hasTable('lms_tests')) {
            Schema::create('lms_tests', function (Blueprint $table) {
                $table->id();
                $table->string('test_type', 50)->comment('UCREA, IG.BH');
                $table->string('test_cd', 50)->nullable();
                $table->string('level_cd', 50)->nullable();
                $table->string('test_seq', 50)->nullable();
                $table->string('name', 255);
                $table->string('pdf_url', 500)->nullable();
                $table->string('local_pdf_path', 500)->nullable();
                $table->tinyInteger('status')->default(1);
                $table->timestamps();

                $table->index(['test_type', 'test_seq']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('lms_tests');
    }
}

