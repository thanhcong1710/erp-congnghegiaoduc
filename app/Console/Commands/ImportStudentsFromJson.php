<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Import học sinh / lớp / hợp đồng từ JSON chuẩn hóa vào ERP.
 *
 * Mỗi dòng JSON = 1 bản ghi contracts (contract).
 * 1 học sinh có thể có nhiều contracts (gói lẻ trong 1 combo).
 * 1 học sinh = 1 Agreement (tạo 1 lần, sau đó dùng lại).
 *
 * CHỐNG DUPLICATE:
 *   - Mỗi contract row có trường `dedup_key` (MD5 hash của phone+class+date+price+course).
 *   - Khi insert, lưu dedup_key vào contracts.note (prefix: [import:XXXX]).
 *   - Trước khi insert, check xem note đã chứa dedup_key chưa.
 *
 * CÁCH DÙNG:
 *   php artisan erp:import-students-json students_normalized.json
 *   php artisan erp:import-students-json students_normalized.json --dry-run
 *   php artisan erp:import-students-json students_normalized.json --limit=20
 *   php artisan erp:import-students-json students_normalized.json --limit=20 --offset=0
 *   php artisan erp:import-students-json students_normalized.json --limit=20 --offset=20
 */
class ImportStudentsFromJson extends Command
{
    protected $signature = 'erp:import-students-json
        {json        : Path to students_normalized.json}
        {--dry-run   : Preview – no DB writes}
        {--limit=0   : Max records to process (0 = all)}
        {--offset=0  : Skip first N records}';

    protected $description = 'Import student/class/agreement/contract data from JSON. Each row = 1 contract.';

    private bool $dry = false;

    // ── In-memory lookup maps (built once from DB) ───────────────────────────
    private array $studentsByPhone  = []; // normalizedPhone → student_id
    private array $classesByCode    = []; // classCode       → {id, cls_startdate}
    private array $agreementsByStud = []; // student_id      → agreement_id
    private array $dedupKeys        = []; // dedup_key       → true  (already imported)
    private array $productsById     = []; // product_id      → num_sessions
    private array $tuitionFeeProductId = []; // tuition_fee_id → product_id

    // ── Pending flush buffers ────────────────────────────────────────────────
    private array $newStudents   = [];
    private array $newCrmParents = [];
    private array $newClasses    = [];
    private array $newAgreements = [];
    private array $newContracts  = [];
    private array $updContracts  = []; // [{id, data}]
    private array $updClasses    = []; // [{id, cls_startdate}]

    // ── Counters ─────────────────────────────────────────────────────────────
    private int $cntStudNew  = 0;
    private int $cntClsNew   = 0;
    private int $cntAgreNew  = 0;
    private int $cntContNew  = 0;
    private int $cntContUpd  = 0;
    private int $cntSkipDup  = 0;
    private int $cntSkipInv  = 0;

    // ─────────────────────────────────────────────────────────────────────────
    public function handle(): int
    {
        $jsonPath = $this->argument('json');
        $this->dry = (bool) $this->option('dry-run');
        $limit     = (int) $this->option('limit');
        $offset    = (int) $this->option('offset');

        if (!file_exists($jsonPath)) {
            $this->error("File not found: $jsonPath");
            return 1;
        }

        $all = json_decode(file_get_contents($jsonPath), true);
        if (!is_array($all)) {
            $this->error('Invalid JSON');
            return 1;
        }

        // Apply offset + limit
        $records = $offset > 0 ? array_slice($all, $offset) : $all;
        if ($limit > 0) {
            $records = array_slice($records, 0, $limit);
        }

        $this->info(sprintf(
            '📦 Total in file: %d | Processing: %d (offset=%d, limit=%s)%s',
            count($all),
            count($records),
            $offset,
            $limit > 0 ? $limit : 'all',
            $this->dry ? ' [DRY-RUN]' : ''
        ));

        $this->info('🔄 Pre-loading existing DB data...');
        $this->preload();

        $this->info('⚙️  Processing rows...');
        $processedCount = 0;

        foreach ($records as $row) {
            $this->processRow($row);
            $processedCount++;
            if ($processedCount % 50 === 0) {
                $this->info("Đã xử lý đến bản ghi thứ: {$processedCount} (Excel row: " . ($row['excel_row'] ?? '?') . ")");
            }
        }

        $this->line('');
        $this->line('');

        if (!$this->dry) {
            $this->info('💾 Flushing to database (transaction)...');
            $this->flush();
        }

        $this->table(['Action', 'Count'], [
            ['Students created',      $this->cntStudNew],
            ['Classes created',       $this->cntClsNew],
            ['Agreements created',    $this->cntAgreNew],
            ['Contracts created',     $this->cntContNew],
            ['Contracts updated',     $this->cntContUpd],
            ['Skipped (duplicate)',   $this->cntSkipDup],
            ['Skipped (invalid row)', $this->cntSkipInv],
        ]);

        $this->info($this->dry ? '✅ Dry-run done – nothing written.' : '✅ Import done.');
        return 0;
    }

    // ── Pre-load ──────────────────────────────────────────────────────────────
    private function preload(): void
    {
        // Students: normalizedPhone → id
        DB::table('students')
            ->whereNotNull('gud_mobile1')
            ->select('id', 'gud_mobile1')
            ->orderBy('id')
            ->chunk(5000, function ($rows) {
                foreach ($rows as $r) {
                    $n = $this->normalizePhone($r->gud_mobile1);
                    if ($n && !isset($this->studentsByPhone[$n])) {
                        $this->studentsByPhone[$n] = $r->id;
                    }
                }
            });

        // Classes: code → {id, cls_startdate, cls_enddate}
        DB::table('classes')->select('id', 'code', 'cls_startdate', 'cls_enddate')->get()->each(function ($c) {
            if ($c->code) {
                $this->classesByCode[strtoupper($c->code)] = [
                    'id'            => $c->id,
                    'cls_startdate' => $c->cls_startdate,
                    'cls_enddate'   => $c->cls_enddate,
                ];
            }
        });

        // Agreements: student_id → agreement_id (first/lowest per student)
        DB::table('agreements')
            ->select('id', 'student_id')
            ->orderBy('id')
            ->chunk(5000, function ($rows) {
                foreach ($rows as $r) {
                    if (!isset($this->agreementsByStud[$r->student_id])) {
                        $this->agreementsByStud[$r->student_id] = $r->id;
                    }
                }
            });

        // Dedup keys: load note field from existing contracts that were imported
        DB::table('contracts')
            ->whereNotNull('note')
            ->where('note', 'like', 'import_key:%')
            ->select('id', 'note')
            ->orderBy('id')
            ->chunk(5000, function ($rows) {
                foreach ($rows as $r) {
                    if (preg_match('/^import_key:([a-f0-9]+)/', $r->note, $m)) {
                        $this->dedupKeys[$m[1]] = true;
                    }
                }
            });

        // Products: load num_sessions
        DB::table('products')->select('id', 'num_sessions')->get()->each(function ($p) {
            $this->productsById[$p->id] = (int) $p->num_sessions;
        });

        // Tuition Fee mapping
        DB::table('tuition_fee')->select('id', 'product_id')->get()->each(function ($t) {
            $this->tuitionFeeProductId[$t->id] = $t->product_id;
        });

        $this->info(sprintf(
            '   ↳ %d students | %d classes | %d agreements | %d dedup-keys loaded | %d products loaded',
            count($this->studentsByPhone),
            count($this->classesByCode),
            count($this->agreementsByStud),
            count($this->dedupKeys),
            count($this->productsById)
        ));
    }

    // ── Process single row (1 row = 1 contract) ───────────────────────────────
    private function processRow(array $row): void
    {
        $dedupKey  = $row['dedup_key']  ?? '';
        $phone     = $this->normalizePhone($row['phone'] ?? '');
        $name      = trim($row['name']       ?? '');
        $address   = trim($row['address']    ?? '');
        $classCode = strtoupper(trim($row['class_code'] ?? ''));
        $startDate = $row['start_date'] ?? null;
        $rawPrice  = (int) ($row['price'] ?? 0);
        // Sanity check: prevent integer overflow if Excel has junk data (e.g. 6962400000000001)
        if ($rawPrice > 1000000) {
            $rawPrice = 0;
        }
        $price     = $rawPrice * 1000;
        $rawStatus = $row['raw_status']      ?? '4';
        $excelRow  = $row['excel_row']       ?? 0;
        $course    = $row['course']          ?? '';

        $linkFb    = trim($row['link_fb'] ?? '');
        $email     = trim($row['email'] ?? '');
        $saleTeam  = trim($row['sale_team'] ?? '');
        $saleMem   = trim($row['sale_member'] ?? '');
        $shipNote  = trim($row['shipping_note'] ?? '');

        // ── Validate ─────────────────────────────────────────────────────────
        if (!$phone || !$name) {
            $this->cntSkipInv++;
            return;
        }

        // ── Dedup check ──────────────────────────────────────────────────────
        if ($dedupKey && isset($this->dedupKeys[$dedupKey])) {
            $this->cntSkipDup++;
            return;
        }

        // ── 1. Student ───────────────────────────────────────────────────────
        if (!isset($this->studentsByPhone[$phone])) {
            $tempId    = 'new_s_' . count($this->newStudents);
            $nameParts = $this->splitName($name);
            $this->newStudents[] = [
                '__temp_id'   => $tempId,
                'lms_code'    => '',           // sẽ được cập nhật sau khi insert
                'name'        => mb_substr($name, 0, 50),
                'firstname'   => mb_substr($nameParts['firstname'], 0, 255),
                'midname'     => mb_substr($nameParts['midname'], 0, 255),
                'lastname'    => mb_substr($nameParts['lastname'], 0, 255),
                'gud_mobile1' => $phone,
                'gud_name1'   => mb_substr($name, 0, 50),
                'gud_email1'  => mb_substr($email, 0, 100),
                'address'     => mb_substr($address, 0, 255),
                'branch_id'   => 9,
                'status'      => 1,
                'created_at'  => now()->toDateTimeString(),
                'updated_at'  => now()->toDateTimeString(),
            ];
            
            $this->newCrmParents[] = [
                '__student_key' => $tempId,
                'name'          => mb_substr($name, 0, 50),
                'mobile_1'      => $phone,
                'email'         => mb_substr($email, 0, 100),
                'link_facebook' => mb_substr($linkFb, 0, 255),
                'branch_id'     => 9,
                'status'        => 3, // based on standard crm_parents status
                'created_at'    => now()->toDateTimeString(),
                'updated_at'    => now()->toDateTimeString(),
            ];
            
            $this->studentsByPhone[$phone] = $tempId;
            $this->cntStudNew++;
        }
        $studentId = $this->studentsByPhone[$phone];

        // ── 2. Class ─────────────────────────────────────────────────────────
        $classId = null;
        if ($classCode && $rawStatus !== '3') {
            if (!isset($this->classesByCode[$classCode])) {
                $tempId = 'new_c_' . count($this->newClasses);
                $this->newClasses[] = [
                    '__temp_id'     => $tempId,
                    'code'          => $classCode,
                    'cls_name'      => $classCode,
                    'cls_startdate' => $startDate,
                    'product_id'    => $this->getProductIdFromClassCode($classCode),
                    'branch_id'     => 9,
                    'status'        => 1,
                    'created_at'    => now()->toDateTimeString(),
                    'updated_at'    => now()->toDateTimeString(),
                ];
                $this->classesByCode[$classCode] = ['id' => $tempId, 'cls_startdate' => $startDate];
                $this->cntClsNew++;
            } elseif (!$this->classesByCode[$classCode]['cls_startdate'] && $startDate) {
                $this->updClasses[] = ['id' => $this->classesByCode[$classCode]['id'], 'cls_startdate' => $startDate];
                $this->classesByCode[$classCode]['cls_startdate'] = $startDate;
            }
            $classId = $this->classesByCode[$classCode]['id'];
        }

        // ── 3. Agreement (1 per Student) ──────────────────────────────────────
        if (!isset($this->agreementsByStud[$studentId])) {
            $tempId = 'new_a_' . count($this->newAgreements);
            
            $agrTuitionFeeId = $this->getTuitionFeeIdFromCourse($course);
            $agrProductId    = $agrTuitionFeeId ? ($this->tuitionFeeProductId[$agrTuitionFeeId] ?? null) : null;

            $this->newAgreements[] = [
                '__temp_id'     => $tempId,
                '__student_key' => $studentId,
                'tuition_fee_id'=> $agrTuitionFeeId,
                'product_id'    => $agrProductId,
                'must_charge'   => $price,
                'total_charged' => $price,
                'debt_amount'   => 0,
                'branch_id'     => 9,
                'status'        => 1,
                'created_at'    => now()->toDateTimeString(),
                'updated_at'    => now()->toDateTimeString(),
            ];
            $this->agreementsByStud[$studentId] = $tempId;
            $this->cntAgreNew++;
        }
        $agreementId = $this->agreementsByStud[$studentId];

        // ── 4. Contract status & sessions ─────────────────────────────────────
        $productId    = $this->getProductIdFromClassCode($classCode);
        $tuitionFeeId = $this->getTuitionFeeIdFromProductId($productId);
        $numSessions  = $productId ? ($this->productsById[$productId] ?? 0) : 0;

        $totalSes = $realSes = $sumSes = $doneSes = 0;

        $clsEnddate = null;
        if ($classCode && isset($this->classesByCode[$classCode])) {
            $clsEnddate = $this->classesByCode[$classCode]['cls_enddate'] ?? null;
        }

        $nowDate = date('Y-m-d');
        if ($clsEnddate && $clsEnddate > $nowDate) {
            $finalStatus    = 6;
            $finalStartDate = $startDate;
            $finalClassId   = $classId;
            $totalSes = $realSes = $sumSes = $doneSes = $numSessions;
        } else {
            if ($rawStatus === '3') {
                $finalStatus    = 3;
                $finalStartDate = null;
                $finalClassId   = null;
            } elseif ($startDate) {
                $finalStatus    = 7;
                $finalStartDate = $startDate;
                $finalClassId   = $classId;
                $totalSes = $realSes = $sumSes = $doneSes = $numSessions;
            } else {
                $finalStatus    = 4;
                $finalStartDate = null;
                $finalClassId   = $classId;
                $totalSes = $realSes = $sumSes = $numSessions;
                $doneSes = 0;
            }
        }

        $extraNote = sprintf("Team: %s | Sale: %s | Ghi chú: %s",
            $saleTeam, $saleMem, $shipNote
        );

        // ── 5. Contract (1 per Excel row) ─────────────────────────────────────
        $this->newContracts[] = [
            '__agreement_key' => $agreementId,
            '__student_key'   => $studentId,
            '__class_key'     => $finalClassId,
            '__dedup_key'     => $dedupKey,
            '__excel_row'     => $excelRow,
            '__extra_note'    => $extraNote,
            'product_id'      => $productId,
            'tuition_fee_id'  => $tuitionFeeId,
            'must_charge'     => $price,
            'total_charged'   => $price,
            'debt_amount'     => 0,
            'total_sessions'  => $totalSes,
            'real_sessions'   => $realSes,
            'summary_sessions'=> $sumSes,
            'done_sessions'   => $doneSes,
            'start_date'      => $finalStartDate,
            'status'          => $finalStatus,
            'branch_id'       => 9,
            'created_at'      => now()->toDateTimeString(),
            'updated_at'      => now()->toDateTimeString(),
        ];
        // Mark dedup key as used in-memory (prevent duplicate in same run)
        if ($dedupKey) {
            $this->dedupKeys[$dedupKey] = true;
        }
        $this->cntContNew++;
    }

    // ── Flush to DB (single transaction) ─────────────────────────────────────
    private function flush(): void
    {
        DB::transaction(function () {
            // 1. Students – batch insert
            $studBatch   = [];
            $phones      = [];
            foreach ($this->newStudents as $s) {
                unset($s['__temp_id'], $s['lms_code']);
                $studBatch[] = $s;
                $phones[]    = $s['gud_mobile1'];
            }
            
            $studTempMap = [];
            if ($studBatch) {
                foreach (array_chunk($studBatch, 500) as $chunk) {
                    DB::table('students')->insert($chunk);
                }
                
                $realStudents = DB::table('students')->whereIn('gud_mobile1', $phones)->pluck('id', 'gud_mobile1');
                
                foreach ($this->newStudents as $s) {
                    $phone = $s['gud_mobile1'];
                    if (isset($realStudents[$phone])) {
                        $studTempMap[$s['__temp_id']] = $realStudents[$phone];
                    }
                }
                
                $ids = $realStudents->values()->toArray();
                if ($ids) {
                    foreach (array_chunk($ids, 1000) as $idChunk) {
                        $idStr = implode(',', $idChunk);
                        $lmsPrefix = config('app.prefix_student_code', 'LAPO');
                        DB::update("UPDATE students SET lms_code = CONCAT(?, LPAD(id, 6, '0')) WHERE id IN ($idStr)", [$lmsPrefix]);
                    }
                }
            }
            $this->info(sprintf('   ✔ Students inserted: %d (lms_code generated)', count($this->newStudents)));

            // 1b. CRM Parents
            $crmBatch = [];
            foreach ($this->newCrmParents as $cp) {
                $studentId = $studTempMap[$cp['__student_key']] ?? $cp['__student_key'];
                unset($cp['__student_key']);
                if ($studentId && is_numeric($studentId)) {
                    $cp['student_id'] = $studentId;
                    $crmBatch[] = $cp;
                }
            }
            if ($crmBatch) {
                foreach (array_chunk($crmBatch, 500) as $chunk) {
                    DB::table('crm_parents')->insert($chunk);
                }
            }
            $this->info(sprintf('   ✔ CRM Parents inserted: %d', count($crmBatch)));

            // 2. Classes
            $clsBatch = [];
            $clsCodes = [];
            foreach ($this->newClasses as $c) {
                unset($c['__temp_id']);
                $clsBatch[] = $c;
                $clsCodes[] = $c['code'];
            }
            $clsTempMap = [];
            if ($clsBatch) {
                foreach (array_chunk($clsBatch, 500) as $chunk) {
                    DB::table('classes')->insert($chunk);
                }
                $realClasses = DB::table('classes')->whereIn('code', $clsCodes)->pluck('id', 'code');
                foreach ($this->newClasses as $c) {
                    $code = $c['code'];
                    if (isset($realClasses[$code])) {
                        $clsTempMap[$c['__temp_id']] = $realClasses[$code];
                    }
                }
            }
            $this->info(sprintf('   ✔ Classes inserted: %d', count($this->newClasses)));

            foreach ($this->updClasses as $u) {
                if (!is_int($u['id'])) continue;
                DB::table('classes')->where('id', $u['id'])
                    ->update(['cls_startdate' => $u['cls_startdate'], 'updated_at' => now()]);
            }

            // Helper: resolve temp keys
            $resStud  = fn($k) => str_starts_with((string)$k, 'new_s_') ? ($studTempMap[$k]  ?? null) : $k;
            $resCls   = fn($k) => str_starts_with((string)$k, 'new_c_') ? ($clsTempMap[$k]   ?? null) : $k;

            // 3. Agreements
            $agreBatch = [];
            $agreStuIds = [];
            foreach ($this->newAgreements as $a) {
                $studentId = $studTempMap[$a['__student_key']] ?? $a['__student_key'];
                unset($a['__temp_id'], $a['__student_key']);
                if ($studentId && is_numeric($studentId)) {
                    $a['student_id'] = $studentId;
                    $agreBatch[] = $a;
                    $agreStuIds[] = $studentId;
                }
            }
            $agreTempMap = [];
            if ($agreBatch) {
                foreach (array_chunk($agreBatch, 500) as $chunk) {
                    DB::table('agreements')->insert($chunk);
                }
                
                $realAgreements = DB::table('agreements')->whereIn('student_id', $agreStuIds)->pluck('id', 'student_id');
                foreach ($this->newAgreements as $a) {
                    $studentId = $studTempMap[$a['__student_key']] ?? $a['__student_key'];
                    if (isset($realAgreements[$studentId])) {
                        $agreTempMap[$a['__temp_id']] = $realAgreements[$studentId];
                        $this->agreementsByStud[$studentId] = $realAgreements[$studentId];
                    }
                }
                
                $ids = $realAgreements->values()->toArray();
                if ($ids) {
                    foreach (array_chunk($ids, 1000) as $idChunk) {
                        $idStr = implode(',', $idChunk);
                        DB::update("UPDATE agreements SET code = LPAD(id, 6, '0') WHERE id IN ($idStr)");
                    }
                }
            }
            $this->info(sprintf('   ✔ Agreements inserted: %d (code generated)', count($agreBatch)));

            $resAgre  = fn($k) => str_starts_with((string)$k, 'new_a_') ? ($agreTempMap[$k]  ?? null) : $k;

            // 4. Contracts
            $contBatch = [];
            $contStuIds = [];
            foreach ($this->newContracts as $ct) {
                $agreementId = $resAgre($ct['__agreement_key']);
                $studentId   = $resStud($ct['__student_key']);
                $classId     = $resCls($ct['__class_key']);
                $dedupKey    = $ct['__dedup_key'];
                $excelRow    = $ct['__excel_row'];
                $extraNote   = $ct['__extra_note'];

                unset(
                    $ct['__agreement_key'], $ct['__student_key'],
                    $ct['__class_key'],     $ct['__dedup_key'],
                    $ct['__excel_row'],     $ct['__extra_note']
                );

                if (!$agreementId || !$studentId) continue;

                $ct['agreement_id'] = $agreementId;
                $ct['student_id']   = $studentId;
                $ct['class_id']     = $classId;
                
                $prefix = $dedupKey ? "import_key:{$dedupKey} excel_row:{$excelRow}" : "excel_row:{$excelRow}";
                $ct['note'] = $prefix . " | " . $extraNote;

                $contBatch[] = $ct;
                $contStuIds[] = $studentId;
            }
            
            if ($contBatch) {
                foreach (array_chunk($contBatch, 500) as $chunk) {
                    DB::table('contracts')->insert($chunk);
                }
                
                $contStuIds = array_unique($contStuIds);
                if ($contStuIds) {
                    foreach (array_chunk($contStuIds, 500) as $stuChunk) {
                        $stuIdsStr = implode(',', $stuChunk);
                        DB::update("UPDATE contracts SET code = CONCAT('C', LPAD(id, 6, '0')) WHERE code IS NULL AND student_id IN ($stuIdsStr)");
                    }
                }
            }
            $this->info(sprintf('   ✔ Contracts inserted: %d (code generated)', count($contBatch)));

            // 5. Contract updates (status 4→7 etc.)
            foreach ($this->updContracts as $u) {
                if (!is_int($u['id'])) continue;
                DB::table('contracts')->where('id', $u['id'])->update($u['data']);
            }
            if (count($this->updContracts)) {
                $this->info(sprintf('   ✔ Contracts updated: %d', count($this->updContracts)));
            }
        });
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function getTuitionFeeIdFromProductId(?int $productId): ?int
    {
        $map = [
            25 => 38,
            26 => 39,
            27 => 40,
            28 => 41,
            29 => 42,
        ];
        return $productId && isset($map[$productId]) ? $map[$productId] : null;
    }

    private function getTuitionFeeIdFromCourse(string $course): ?int
    {
        $c = strtolower(trim($course));
        if (str_contains($c, 'combo pre_toeic + toeic lv(1+2) + toeic (s + w)')) return 50;
        if (str_contains($c, 'combo pre_toeic + toeic lv(1+2) + toeic s')) return 46;
        if (str_contains($c, 'combo pre_toeic + toeic lv(1+2) + toeic w')) return 53;
        if (str_contains($c, 'combo pre_toeic + toeic lv(1+2)')) return 44;
        
        if (str_contains($c, 'combo toeic lv(1+2) + toeic (s + w)')) return 49;
        if (str_contains($c, 'combo toeic lv(1+2) + toeic s')) return 54;
        if (str_contains($c, 'combo toeic lv1+2')) return 45;
        
        if (str_contains($c, 'combo pre_toeic + toeic lv1 + toeic (s+w)')) return 51;
        if (str_contains($c, 'combo pre_toeic + toeic lv1')) return 43;

        if (str_contains($c, 'combo toeic lv1 + toeic (s+w)')) return 52;
        
        if (str_contains($c, 'combo toeic lv2 + toeic (s+ w)')) return 48;
        if (str_contains($c, 'combo toeic lv2 + toeic s')) return 55;
        if (str_contains($c, 'combo toeic (s+ w)')) return 47;
        
        if (str_contains($c, 'pre_toeic')) return 38;
        if (str_contains($c, 'toeic lv1')) return 39;
        if (str_contains($c, 'toeic lv2')) return 40;
        if (str_contains($c, 'toeic speaking')) return 41;
        if (str_contains($c, 'toeic writing')) return 42;
        
        return null;
    }

    private function getProductIdFromClassCode(string $code): ?int
    {
        if (str_starts_with($code, 'PT')) return 25;
        if (str_starts_with($code, 'T')) return 26;
        if (str_starts_with($code, 'V')) return 27;
        if (str_starts_with($code, 'W')) return 29;
        if (str_starts_with($code, 'S')) return 28;
        return null;
    }

    private function normalizePhone(string $raw): ?string
    {
        if (stripos($raw, 'e+') !== false) {
            $raw = number_format((float) $raw, 0, '.', '');
        }
        $digits = preg_replace('/\D/', '', $raw);
        if (!$digits) return null;
        if (str_starts_with($digits, '84') && strlen($digits) >= 11) {
            $digits = '0' . substr($digits, 2);
        }
        if (!str_starts_with($digits, '0')) {
            $digits = '0' . $digits;
        }
        $digits = substr($digits, 0, 10);
        return strlen($digits) >= 9 ? $digits : null;
    }

    private function splitName(string $full): array
    {
        $parts = array_values(array_filter(explode(' ', trim($full))));
        $n     = count($parts);
        if ($n === 0) return ['firstname' => '', 'midname' => '', 'lastname' => ''];
        if ($n === 1) return ['firstname' => $parts[0], 'midname' => '', 'lastname' => ''];
        if ($n === 2) return ['firstname' => $parts[1], 'midname' => '', 'lastname' => $parts[0]];
        return [
            'lastname'  => $parts[0],
            'midname'   => implode(' ', array_slice($parts, 1, $n - 2)),
            'firstname' => $parts[$n - 1],
        ];
    }
}
