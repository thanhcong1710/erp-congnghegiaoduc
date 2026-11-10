<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNoteAndUpdatedByToHrmLeaveBalancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('hrm_leave_balances', function (Blueprint $table) {
            if (!Schema::hasColumn('hrm_leave_balances', 'note')) {
                $table->string('note', 255)->nullable()->after('used_days')->comment('Lý do hoặc ghi chú điều chỉnh phép');
            }
            if (!Schema::hasColumn('hrm_leave_balances', 'updated_by')) {
                $table->unsignedBigInteger('updated_by')->nullable()->after('note')->comment('ID người điều chỉnh');
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
        Schema::table('hrm_leave_balances', function (Blueprint $table) {
            $cols = [];
            if (Schema::hasColumn('hrm_leave_balances', 'note')) {
                $cols[] = 'note';
            }
            if (Schema::hasColumn('hrm_leave_balances', 'updated_by')) {
                $cols[] = 'updated_by';
            }
            if (!empty($cols)) {
                $table->dropColumn($cols);
            }
        });
    }
}

