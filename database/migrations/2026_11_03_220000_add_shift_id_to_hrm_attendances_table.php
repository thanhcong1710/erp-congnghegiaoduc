<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddShiftIdToHrmAttendancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('hrm_attendances', function (Blueprint $table) {
            if (!Schema::hasColumn('hrm_attendances', 'shift_id')) {
                $table->unsignedBigInteger('shift_id')->nullable()->after('employee_id')->index();
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
        Schema::table('hrm_attendances', function (Blueprint $table) {
            if (Schema::hasColumn('hrm_attendances', 'shift_id')) {
                $table->dropColumn('shift_id');
            }
        });
    }
}

