<?php

namespace App\Console\Commands;

use App\Providers\UtilityServiceProvider as u;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Xử lý bù: hợp đồng đang "Đặt cọc" (status=2) nhưng đã thu >= deposit_min_rate (30%) must_charge
 * => chuyển "Chờ xếp lớp chính thức" (status=3) để được xếp lớp.
 * Mặc định chỉ liệt kê (dry-run), thêm --run để cập nhật.
 */
class RecalcDepositStatus extends Command
{
    protected $signature = 'contracts:recalcDepositStatus {--run : Cập nhật thật (mặc định chỉ liệt kê)}';

    protected $description = 'Chuyển hợp đồng Đặt cọc đã thu >= 30% học phí sang Chờ xếp lớp chính thức';

    public function handle()
    {
        $rate = (float)config('app.deposit_min_rate', 0.3);
        $rows = DB::select("SELECT c.id, c.code, c.student_id, c.must_charge, c.total_charged, c.status, s.name AS student_name
            FROM contracts AS c LEFT JOIN students AS s ON s.id = c.student_id
            WHERE c.status = 2 AND c.total_charged > 0 AND (c.must_charge <= 0 OR c.total_charged >= c.must_charge * ?)", [$rate]);

        if (!$rows) {
            $this->info('Không có hợp đồng nào cần cập nhật.');
            return 0;
        }
        $this->table(['ID', 'Mã HĐ', 'Học sinh', 'Phải thu', 'Đã thu', '%'], array_map(function ($r) {
            $pct = $r->must_charge > 0 ? round($r->total_charged * 100 / $r->must_charge, 1) : 100;
            return [$r->id, $r->code, $r->student_name, number_format($r->must_charge), number_format($r->total_charged), $pct];
        }, $rows));

        if (!$this->option('run')) {
            $this->warn(count($rows) . ' hợp đồng sẽ được chuyển sang "Chờ xếp lớp chính thức". Chạy lại với --run để cập nhật.');
            return 0;
        }
        foreach ($rows as $r) {
            DB::update("UPDATE contracts SET status = 3, updated_at = ? WHERE id = ? AND status = 2", [date('Y-m-d H:i:s'), $r->id]);
            u::addLogContracts($r->id);
        }
        $this->info('Đã cập nhật ' . count($rows) . ' hợp đồng.');
        return 0;
    }
}
