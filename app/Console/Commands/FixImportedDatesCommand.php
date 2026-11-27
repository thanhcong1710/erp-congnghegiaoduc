<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Dữ liệu import (CS1 An Khánh, CS2) đang mang thời gian tạo / thu phí = thời điểm chạy import.
 * Command này đưa về theo NGÀY BẮT ĐẦU HỌC (contracts.start_date):
 *  - contracts.created_at                       = start_date (giữ giờ cũ)
 *  - payments / tmp_payments do import tạo       : charge_date = start_date, created_at (+approved_at) = start_date
 *  - students, term_student_user, crm_students,
 *    crm_parents (+ last_assign_date nếu có)     = start_date sớm nhất của các HĐ import (chỉ lùi về trước, không đẩy lên sau)
 * Phiếu thu do Sale/Kế toán tự lên trên hệ thống (không gắn tag import) KHÔNG bị sửa.
 *
 *   php artisan import:fixDates            # xem trước (không ghi DB)
 *   php artisan import:fixDates --run      # ghi DB, lưu giá trị cũ để hoàn tác
 *   php artisan import:fixDates --rollback # trả lại giá trị cũ
 */
class FixImportedDatesCommand extends Command
{
    protected $signature = 'import:fixDates {--run : Ghi thay đổi vào DB} {--rollback : Hoàn tác lần chạy --run gần nhất}';
    protected $description = 'Đưa thời gian tạo / thu phí của dữ liệu import về ngày bắt đầu học';

    const BACKUP_FILE = 'import_batches/fix_dates_backup.json';
    const CONTRACT_TAG = "(c.note LIKE '%[Import: BATCH_ANKHANH_202611]%' OR c.note LIKE '%[Import CS2 202611]%')";
    const PAYMENT_TAG = "%[Import CS2 202611]%";

    private $changes = [];

    public function handle()
    {
        if ($this->option('rollback')) return $this->rollback();

        $contracts = DB::select("SELECT c.id, c.code, c.student_id, c.branch_id, c.start_date, c.created_at
            FROM contracts AS c WHERE " . self::CONTRACT_TAG . " AND c.start_date IS NOT NULL ORDER BY c.id");
        if (!$contracts) {
            $this->info('Không có hợp đồng import nào.');
            return 0;
        }

        $studentStart = [];
        foreach ($contracts as $c) {
            $this->plan('contracts', $c->id, ['created_at' => $c->created_at], ['created_at' => $this->at($c->start_date, $c->created_at)]);
            $sid = (int)$c->student_id;
            if (!isset($studentStart[$sid]) || $c->start_date < $studentStart[$sid]) $studentStart[$sid] = $c->start_date;
        }

        // Phiếu thu do import tạo
        $ids = implode(',', array_map(function ($c) { return (int)$c->id; }, $contracts));
        foreach (DB::select("SELECT tp.id, tp.charge_date, tp.created_at, tp.approved_at, c.start_date
                FROM tmp_payments AS tp JOIN contracts AS c ON c.id = tp.contract_id
                WHERE tp.contract_id IN ($ids) AND tp.note LIKE ?", [self::PAYMENT_TAG]) as $p) {
            $new = ['charge_date' => $p->start_date, 'created_at' => $this->at($p->start_date, $p->created_at)];
            if ($p->approved_at) $new['approved_at'] = $this->at($p->start_date, $p->approved_at);
            $this->plan('tmp_payments', $p->id, (array)$p, $new);
        }
        foreach (DB::select("SELECT p.id, p.charge_date, p.created_at, c.start_date
                FROM payments AS p JOIN contracts AS c ON c.id = p.contract_id
                WHERE p.contract_id IN ($ids) AND p.note LIKE ?", [self::PAYMENT_TAG]) as $p) {
            $this->plan('payments', $p->id, (array)$p, ['charge_date' => $p->start_date, 'created_at' => $this->at($p->start_date, $p->created_at)]);
        }

        // Học sinh / phụ huynh: lùi về ngày bắt đầu học sớm nhất
        $parentStart = [];
        foreach ($studentStart as $sid => $start) {
            $s = DB::selectOne("SELECT id, created_at, gud_mobile1 FROM students WHERE id = ?", [$sid]);
            if ($s) $this->planEarlier('students', $s->id, $s->created_at, $start);
            foreach (DB::select("SELECT id, created_at FROM term_student_user WHERE student_id = ?", [$sid]) as $t) {
                $this->planEarlier('term_student_user', $t->id, $t->created_at, $start);
            }
            $crm = DB::select("SELECT id, parent_id, created_at FROM crm_students WHERE lms_id = ?", [$sid]);
            foreach ($crm as $cs) {
                $this->planEarlier('crm_students', $cs->id, $cs->created_at, $start);
                if ($cs->parent_id) $this->minDate($parentStart, (int)$cs->parent_id, $start);
            }
            if (!$crm && $s && $s->gud_mobile1) {
                $p = DB::selectOne("SELECT id FROM crm_parents WHERE mobile_1 = ? LIMIT 1", [$s->gud_mobile1]);
                if ($p) $this->minDate($parentStart, (int)$p->id, $start);
            }
        }
        foreach ($parentStart as $pid => $start) {
            $p = DB::selectOne("SELECT id, created_at, last_assign_date FROM crm_parents WHERE id = ?", [$pid]);
            if (!$p) continue;
            $old = [];
            $new = [];
            if ($p->created_at && substr($p->created_at, 0, 10) > $start) {
                $old['created_at'] = $p->created_at;
                $new['created_at'] = $this->at($start, $p->created_at);
            }
            if ($p->last_assign_date && substr($p->last_assign_date, 0, 10) > $start) {
                $old['last_assign_date'] = $p->last_assign_date;
                $new['last_assign_date'] = $this->at($start, $p->last_assign_date);
            }
            if ($new) $this->plan('crm_parents', $p->id, $old, $new);
        }

        $this->summary();

        if (!$this->option('run')) {
            $this->warn('Chế độ xem trước – chưa ghi DB. Chạy lại với --run để cập nhật.');
            return 0;
        }

        $backup = ['run_at' => date('Y-m-d H:i:s'), 'rows' => []];
        DB::beginTransaction();
        try {
            foreach ($this->changes as $table => $rows) {
                foreach ($rows as $id => $ch) {
                    DB::table($table)->where('id', $id)->update($ch['new']);
                    $backup['rows'][] = ['table' => $table, 'id' => $id, 'old' => $ch['old']];
                }
            }
            $path = storage_path('app/' . self::BACKUP_FILE);
            @mkdir(dirname($path), 0777, true);
            file_put_contents($path, json_encode($backup, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->error('Lỗi, đã huỷ toàn bộ thay đổi: ' . $e->getMessage());
            return 1;
        }
        $this->info('Đã cập nhật xong. Backup: storage/app/' . self::BACKUP_FILE . ' (dùng --rollback để hoàn tác)');
        return 0;
    }

    private function rollback()
    {
        $path = storage_path('app/' . self::BACKUP_FILE);
        if (!file_exists($path)) {
            $this->error('Không có file backup.');
            return 1;
        }
        $backup = json_decode(file_get_contents($path), true);
        DB::transaction(function () use ($backup) {
            foreach ($backup['rows'] as $r) DB::table($r['table'])->where('id', $r['id'])->update($r['old']);
        });
        rename($path, $path . '.rolledback_' . date('YmdHis'));
        $this->info('Đã hoàn tác ' . count($backup['rows']) . ' bản ghi.');
        return 0;
    }

    /** Ngày $date + giờ của $oldDateTime (mặc định 08:00:00) */
    private function at($date, $oldDateTime)
    {
        $time = $oldDateTime && strlen($oldDateTime) >= 19 ? substr($oldDateTime, 11, 8) : '08:00:00';
        return substr($date, 0, 10) . ' ' . $time;
    }

    private function minDate(array &$arr, $key, $date)
    {
        if (!isset($arr[$key]) || $date < $arr[$key]) $arr[$key] = $date;
    }

    private function planEarlier($table, $id, $createdAt, $start)
    {
        if ($createdAt && substr($createdAt, 0, 10) > $start) {
            $this->plan($table, $id, ['created_at' => $createdAt], ['created_at' => $this->at($start, $createdAt)]);
        }
    }

    private function plan($table, $id, array $oldRow, array $new)
    {
        $old = [];
        foreach ($new as $k => $v) $old[$k] = $oldRow[$k] ?? null;
        if ($old == $new) return;
        $this->changes[$table][$id] = ['old' => $old, 'new' => $new];
    }

    private function summary()
    {
        $rows = [];
        foreach ($this->changes as $table => $list) {
            $fields = [];
            foreach ($list as $ch) foreach ($ch['new'] as $k => $v) if ($ch['old'][$k] != $v) $fields[$k] = true;
            $rows[] = [$table, count($list), implode(', ', array_keys($fields))];
        }
        $this->table(['Bảng', 'Số bản ghi sửa', 'Cột'], $rows);

        if (!empty($this->changes['contracts'])) {
            $this->line('Ví dụ hợp đồng:');
            $sample = array_slice($this->changes['contracts'], 0, 8, true);
            $this->table(['ID', 'created_at cũ', 'created_at mới'], array_map(function ($id, $ch) {
                return [$id, $ch['old']['created_at'], $ch['new']['created_at']];
            }, array_keys($sample), $sample));
        }
        foreach (['payments', 'tmp_payments'] as $t) {
            if (empty($this->changes[$t])) continue;
            $this->line("Phiếu thu ($t):");
            $this->table(['ID', 'charge_date cũ', 'charge_date mới', 'created_at mới'], array_map(function ($id, $ch) {
                return [$id, $ch['old']['charge_date'], $ch['new']['charge_date'], $ch['new']['created_at']];
            }, array_keys($this->changes[$t]), $this->changes[$t]));
        }
    }
}
