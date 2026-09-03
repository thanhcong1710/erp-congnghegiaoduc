<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAgreementsRevenueHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('agreements_revenue_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('agreement_id')->index();
            $table->string('salary_month', 15)->index();
            $table->decimal('must_charge', 15, 2)->default(0);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('revenue_amount', 15, 2)->default(0);
            $table->timestamps();

            $table->unique(['agreement_id', 'salary_month']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('agreements_revenue_histories');
    }
}

