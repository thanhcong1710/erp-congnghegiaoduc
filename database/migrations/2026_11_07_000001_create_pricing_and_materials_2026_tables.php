<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePricingAndMaterials2026Tables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('materials')) {
            Schema::create('materials', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('product_id')->nullable()->comment('Gắn với sản phẩm học');
                $table->string('code', 50)->unique();
                $table->string('name', 255);
                $table->decimal('unit_price', 15, 2)->default(0)->comment('Đơn giá niêm yết');
                $table->integer('package_months')->nullable()->comment('Số tháng gói tương ứng nếu có');
                $table->tinyInteger('status')->default(1);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('contract_materials')) {
            Schema::create('contract_materials', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('contract_id');
                $table->integer('material_id');
                $table->integer('quantity')->default(1);
                $table->decimal('original_price', 15, 2)->default(0);
                $table->float('discount_percent')->default(0);
                $table->decimal('final_price', 15, 2)->default(0);
                $table->timestamps();

                $table->index('contract_id');
                $table->index('material_id');
            });
        }

        if (!Schema::hasTable('branch_targets')) {
            Schema::create('branch_targets', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('branch_id');
                $table->integer('year')->default(2026);
                $table->tinyInteger('month')->nullable();
                $table->integer('target_enrolment')->default(0);
                $table->decimal('target_collection', 15, 2)->default(0);
                $table->timestamps();

                $table->unique(['branch_id', 'year', 'month'], 'branch_year_month_unique');
            });
        }

        Schema::table('contracts', function (Blueprint $table) {
            if (!Schema::hasColumn('contracts', 'tuition_receivable')) {
                $table->decimal('tuition_receivable', 15, 2)->nullable()->default(0)->after('init_tuition_fee_receivable');
            }
            if (!Schema::hasColumn('contracts', 'book_receivable')) {
                $table->decimal('book_receivable', 15, 2)->nullable()->default(0)->after('tuition_receivable');
            }
            if (!Schema::hasColumn('contracts', 'scholarship_percent')) {
                $table->float('scholarship_percent')->nullable()->default(0)->after('book_receivable');
            }
            if (!Schema::hasColumn('contracts', 'promo_extra_percent')) {
                $table->float('promo_extra_percent')->nullable()->default(0)->after('scholarship_percent');
            }
            if (!Schema::hasColumn('contracts', 'checkin_group_percent')) {
                $table->float('checkin_group_percent')->nullable()->default(0)->after('promo_extra_percent');
            }
            if (!Schema::hasColumn('contracts', 'resident_percent')) {
                $table->float('resident_percent')->nullable()->default(0)->after('checkin_group_percent');
            }
            if (!Schema::hasColumn('contracts', 'gift_note')) {
                $table->string('gift_note', 500)->nullable()->after('note');
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
        Schema::dropIfExists('contract_materials');
        Schema::dropIfExists('materials');
        Schema::dropIfExists('branch_targets');

        Schema::table('contracts', function (Blueprint $table) {
            $cols = [
                'tuition_receivable',
                'book_receivable',
                'scholarship_percent',
                'promo_extra_percent',
                'checkin_group_percent',
                'resident_percent',
                'gift_note'
            ];
            foreach ($cols as $col) {
                if (Schema::hasColumn('contracts', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
}

