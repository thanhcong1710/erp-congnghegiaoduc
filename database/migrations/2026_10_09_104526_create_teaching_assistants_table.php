<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTeachingAssistantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('teaching_assistants', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->nullable()->comment('Linked to users table');
            $table->string('full_name');
            $table->string('facebook_link')->nullable();
            $table->date('dob')->nullable();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->string('bank_account')->nullable();
            $table->string('email')->nullable();
            $table->string('lr_link')->nullable();
            $table->string('sw_link')->nullable();
            $table->string('profile_link')->nullable();
            $table->date('start_date')->nullable();
            $table->integer('status')->default(1)->comment('1: Active, 0: Inactive');
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
        Schema::dropIfExists('teaching_assistants');
    }
}
