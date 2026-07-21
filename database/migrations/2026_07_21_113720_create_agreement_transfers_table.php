<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAgreementTransfersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('agreement_transfers', function (Blueprint $table) {
            $table->id();
            $table->integer('student_id')->default(0)->index();
            $table->integer('from_agreement_id')->default(0)->index();
            $table->integer('to_agreement_id')->default(0)->index();
            $table->double('amount')->default(0);
            $table->text('note')->nullable();
            $table->integer('creator_id')->default(0)->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('agreement_transfers');
    }
}
