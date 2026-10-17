<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIdCardDateToHrmEmployeeProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('hrm_employee_profiles', function (Blueprint $table) {
            if (!Schema::hasColumn('hrm_employee_profiles', 'id_card_date')) {
                $table->date('id_card_date')->nullable()->after('id_card_number')->comment('Ngày cấp CCCD/CMND');
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
        Schema::table('hrm_employee_profiles', function (Blueprint $table) {
            if (Schema::hasColumn('hrm_employee_profiles', 'id_card_date')) {
                $table->dropColumn('id_card_date');
            }
        });
    }
}
