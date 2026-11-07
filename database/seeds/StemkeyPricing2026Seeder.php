<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StemkeyPricing2026Seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = date('Y-m-d H:i:s');

        // 1. Chỉ lấy các trung tâm thực tế hiện có trong DB (CS1, CS2, ...)
        $allBranchIds = DB::table('branches')->where('status', 1)->pluck('id')->toArray();
        $branchIdStr = implode(',', $allBranchIds);

        // 2. Seed Tuition Fees (Gói học phí 2026)
        // Products: 1: Ucrea, 2: Bright I.G, 3: Tiếng Anh, 4: Tiền Tiểu Học
        $tuitionFees = [
            // STM Toán (Áp dụng cho cả Prod 1 và Prod 2)
            ['prod' => 1, 'name' => 'Gói STM 2026 - 3 tháng (Ucrea)', 'months' => 3, 'session' => 24, 'price' => 6300000],
            ['prod' => 1, 'name' => 'Gói STM 2026 - 6 tháng (Ucrea)', 'months' => 6, 'session' => 48, 'price' => 12600000],
            ['prod' => 1, 'name' => 'Gói STM 2026 - 12 tháng (Ucrea)', 'months' => 12, 'session' => 96, 'price' => 25200000],
            ['prod' => 1, 'name' => 'Gói STM 2026 - 24 tháng (Ucrea)', 'months' => 24, 'session' => 192, 'price' => 50400000],

            ['prod' => 2, 'name' => 'Gói STM 2026 - 3 tháng (Bright IG)', 'months' => 3, 'session' => 24, 'price' => 6300000],
            ['prod' => 2, 'name' => 'Gói STM 2026 - 6 tháng (Bright IG)', 'months' => 6, 'session' => 48, 'price' => 12600000],
            ['prod' => 2, 'name' => 'Gói STM 2026 - 12 tháng (Bright IG)', 'months' => 12, 'session' => 96, 'price' => 25200000],
            ['prod' => 2, 'name' => 'Gói STM 2026 - 24 tháng (Bright IG)', 'months' => 24, 'session' => 192, 'price' => 50400000],

            // Tiền Tiểu Học CLC (Prod 4)
            ['prod' => 4, 'name' => 'Gói TTH CLC 2026 - 1 tháng', 'months' => 1, 'session' => 8, 'price' => 1200000],
            ['prod' => 4, 'name' => 'Gói TTH CLC 2026 - 6 tháng', 'months' => 6, 'session' => 48, 'price' => 7200000],
            ['prod' => 4, 'name' => 'Gói TTH CLC 2026 - 9 tháng', 'months' => 9, 'session' => 72, 'price' => 10800000],

            // Tiếng Anh 100% GVNN (Prod 3)
            ['prod' => 3, 'name' => 'Gói Tiếng Anh 2026 - 4 tháng', 'months' => 4, 'session' => 32, 'price' => 10000000],
            ['prod' => 3, 'name' => 'Gói Tiếng Anh 2026 - 8 tháng', 'months' => 8, 'session' => 64, 'price' => 20000000],
            ['prod' => 3, 'name' => 'Gói Tiếng Anh 2026 - 12 tháng', 'months' => 12, 'session' => 96, 'price' => 30000000],
            ['prod' => 3, 'name' => 'Gói Tiếng Anh 2026 - 24 tháng', 'months' => 24, 'session' => 192, 'price' => 60000000],
        ];

        foreach ($tuitionFees as $tf) {
            $existing = DB::table('tuition_fee')
                ->where('product_id', $tf['prod'])
                ->where('name', $tf['name'])
                ->first();

            $data = [
                'name' => $tf['name'],
                'product_id' => $tf['prod'],
                'session' => $tf['session'],
                'number_of_months' => $tf['months'],
                'price' => $tf['price'],
                'receivable' => $tf['price'],
                'type_contract' => 1, // Chính thức
                'type' => 0,
                'status' => 1,
                'branch_id' => $branchIdStr,
                'available_date' => date('Y-m-d'),
                'expired_date' => '2026-12-31',
                'updated_at' => $now,
            ];

            if (!$existing) {
                $data['created_at'] = $now;
                DB::table('tuition_fee')->insert($data);
            } else {
                DB::table('tuition_fee')->where('id', $existing->id)->update($data);
            }
        }

        // 3. Seed Materials (Sách và Học liệu)
        $materials = [
            // Sách Toán Ucrea
            ['prod' => 1, 'code' => 'BK_UCREA_03', 'name' => 'Bộ sách Ucrea (3 tháng)', 'months' => 3, 'price' => 609000],
            ['prod' => 1, 'code' => 'BK_UCREA_06', 'name' => 'Bộ sách Ucrea (6 tháng)', 'months' => 6, 'price' => 1218000],
            ['prod' => 1, 'code' => 'BK_UCREA_12', 'name' => 'Bộ sách Ucrea (12 tháng)', 'months' => 12, 'price' => 2436000],
            ['prod' => 1, 'code' => 'BK_UCREA_24', 'name' => 'Bộ sách Ucrea (24 tháng)', 'months' => 24, 'price' => 4872000],

            // Sách Bright IG
            ['prod' => 2, 'code' => 'BK_IG_03', 'name' => 'Bộ sách Bright IG (3 tháng)', 'months' => 3, 'price' => 1002000],
            ['prod' => 2, 'code' => 'BK_IG_06', 'name' => 'Bộ sách Bright IG (6 tháng)', 'months' => 6, 'price' => 2004000],
            ['prod' => 2, 'code' => 'BK_IG_12', 'name' => 'Bộ sách Bright IG (12 tháng)', 'months' => 12, 'price' => 4008000],
            ['prod' => 2, 'code' => 'BK_IG_24', 'name' => 'Bộ sách Bright IG (24 tháng)', 'months' => 24, 'price' => 8016000],

            // Sách Black Hole
            ['prod' => 2, 'code' => 'BK_BH_03', 'name' => 'Bộ sách Black Hole (3 tháng)', 'months' => 3, 'price' => 990000],
            ['prod' => 2, 'code' => 'BK_BH_06', 'name' => 'Bộ sách Black Hole (6 tháng)', 'months' => 6, 'price' => 1980000],
            ['prod' => 2, 'code' => 'BK_BH_12', 'name' => 'Bộ sách Black Hole (12 tháng)', 'months' => 12, 'price' => 3960000],
            ['prod' => 2, 'code' => 'BK_BH_24', 'name' => 'Bộ sách Black Hole (24 tháng)', 'months' => 24, 'price' => 7920000],

            // Sách Tiền Tiểu Học
            ['prod' => 4, 'code' => 'BK_TTH_04', 'name' => 'Bộ sách Tiền tiểu học (4 tháng)', 'months' => 4, 'price' => 417000],
            ['prod' => 4, 'code' => 'BK_TTH_06', 'name' => 'Bộ sách Tiền tiểu học (6 tháng)', 'months' => 6, 'price' => 561000],
        ];

        foreach ($materials as $m) {
            $existing = DB::table('materials')->where('code', $m['code'])->first();
            $data = [
                'product_id' => $m['prod'],
                'code' => $m['code'],
                'name' => $m['name'],
                'package_months' => $m['months'],
                'unit_price' => $m['price'],
                'status' => 1,
                'updated_at' => $now,
            ];

            if (!$existing) {
                $data['created_at'] = $now;
                DB::table('materials')->insert($data);
            } else {
                DB::table('materials')->where('id', $existing->id)->update($data);
            }
        }
    }
}
