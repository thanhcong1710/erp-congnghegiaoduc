<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;

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
    private array $agreementsByStud = []; // student_id_tuitionFeeId → agreement_id
    private array $contractsByAgrProd = []; // agreement_id_productId → contract_id
    private array $dedupKeys        = []; // dedup_key       → true  (already imported)
    private array $productsById     = []; // product_id      → num_sessions
    private array $tuitionFeeProductId = []; // tuition_fee_id → product_id
    private array $tuitionFeePrice  = []; // tuition_fee_id → price
    private array $tuitionFeeSession= []; // tuition_fee_id → session
    private array $usersByTmpName   = []; // tmp_name      → {id, manager_id}
    private array $importedPaymentsAgreements = []; // agreement_id → true
    private array $newContractsKeys = []; // agreementId_productId → true (for in-memory deduplication)
    private array $tuitionFeeRelations = []; // tuition_fee_id(combo) → [product_id → exchange_tuition_fee_id]

    // ── Pending flush buffers ────────────────────────────────────────────────
    private array $newStudents   = [];
    private array $newCrmParents = [];
    private array $newClasses    = [];
    private array $newAgreements = [];
    private array $newContracts  = [];
    private array $newPayments   = [];
    private array $updContracts  = []; // [{id, data}]
    private array $updClasses    = []; // [{id, cls_startdate}]
    private array $updStudents   = []; 
    private array $updCrmParents = [];
    private array $updAgreements = [];

    // ── Counters ─────────────────────────────────────────────────────────────
    private int $cntStudNew  = 0;
    private int $cntClsNew   = 0;
    private int $cntAgreNew  = 0;
    private int $cntContNew  = 0;
    private int $cntPayNew   = 0;
    private int $cntContUpd  = 0;
    private int $cntSkipDup  = 0;
    private int $cntSkipInv  = 0;

    // ─────────────────────────────────────────────────────────────────────────
    public function handle(): int
    {
        ini_set('memory_limit', '-1');
        
        $this->info('🗑  Resetting system cache...');
        Artisan::call('cache:clear');
        
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
            ['Payments created',      $this->cntPayNew],
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
        DB::table('classes')->select('id', 'code', 'cls_startdate', 'cls_enddate')
            ->orderBy('id')
            ->chunk(5000, function ($rows) {
                foreach ($rows as $c) {
                    if ($c->code) {
                        $this->classesByCode[strtoupper($c->code)] = [
                            'id'            => $c->id,
                            'cls_startdate' => $c->cls_startdate,
                            'cls_enddate'   => $c->cls_enddate,
                        ];
                    }
                }
            });

        // Agreements: student_id_tuitionFeeId → agreement_id
        DB::table('agreements')
            ->select('id', 'student_id', 'tuition_fee_id')
            ->orderBy('id')
            ->chunk(5000, function ($rows) {
                foreach ($rows as $r) {
                    $key = $r->student_id . '_' . $r->tuition_fee_id;
                    if (!isset($this->agreementsByStud[$key])) {
                        $this->agreementsByStud[$key] = $r->id;
                    }
                }
            });

        // Contracts: agreementId_productId → contract_id
        DB::table('contracts')
            ->select('id', 'agreement_id', 'product_id')
            ->orderBy('id')
            ->chunk(5000, function ($rows) {
                foreach ($rows as $r) {
                    if ($r->product_id) {
                        $key = $r->agreement_id . '_' . $r->product_id;
                        $this->contractsByAgrProd[$key] = $r->id;
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
        DB::table('tuition_fee')->select('id', 'product_id', 'price', 'session')->get()->each(function ($t) {
            $this->tuitionFeeProductId[$t->id] = $t->product_id;
            $this->tuitionFeePrice[$t->id] = (float) $t->price;
            $this->tuitionFeeSession[$t->id] = (int) $t->session;
        });

        // Tuition Fee Relation mapping (combo -> individual packages)
        DB::table('tuition_fee_relation')
            ->where('status', 1)
            ->select('tuition_fee_id', 'exchange_tuition_fee_id')
            ->get()
            ->each(function($r) {
                $comboId = $r->tuition_fee_id;
                $indivId = $r->exchange_tuition_fee_id;
                $prodId = $this->tuitionFeeProductId[$indivId] ?? null;
                if ($prodId) {
                    $this->tuitionFeeRelations[$comboId][$prodId] = $indivId;
                }
            });

        // Users mapping
        DB::table('users')->whereNotNull('tmp_name')->select('id', 'manager_id', 'tmp_name')->get()->each(function ($u) {
            $this->usersByTmpName[trim($u->tmp_name)] = [
                'id' => $u->id,
                'manager_id' => $u->manager_id,
            ];
        });

        // Imported Payments Agreements
        DB::table('payments')
            ->where('is_import', 1)
            ->select('agreement_id')
            ->distinct()
            ->orderBy('agreement_id')
            ->chunk(5000, function ($rows) {
                foreach ($rows as $p) {
                    $this->importedPaymentsAgreements[$p->agreement_id] = true;
                }
            });

        $this->info(sprintf(
            '   ↳ %d students | %d classes | %d agreements | %d dedup-keys | %d products | %d users | %d imported payments',
            count($this->studentsByPhone),
            count($this->classesByCode),
            count($this->agreementsByStud),
            count($this->dedupKeys),
            count($this->productsById),
            count($this->usersByTmpName),
            count($this->importedPaymentsAgreements)
        ));
    }

    // ── Process single row (1 row = 1 contract) ───────────────────────────────
    private function processRow(array $row): void
    {
        $dedupKey  = $row['dedup_key']  ?? '';
        $phone     = $this->normalizePhone($row['phone'] ?? '');
        $name      = trim($row['name']       ?? '');
        $address   = trim($row['address']    ?? '');
        $classCode = strtoupper(trim($row['class_name'] ?? ($row['class_code'] ?? '')));
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
        $gender    = $row['gender']          ?? 'M';

        $linkFb    = trim($row['link_fb'] ?? '');
        $email     = trim($row['email'] ?? '');
        $saleTeam  = trim($row['sale_team'] ?? '');
        $saleMem   = trim($row['sale_member'] ?? '');
        $shipNote  = trim($row['shipping_note'] ?? '');
        
        $teamKinhDoanh = trim($row['team_kinh_doanh'] ?? '');
        $pay1Amount = (float)($row['payment_1_amount'] ?? 0) * 1000;
        $pay1Date   = $row['payment_1_date'] ?? null;
        $pay2Amount = (float)($row['payment_2_amount'] ?? 0) * 1000;
        $pay2Date   = $row['payment_2_date'] ?? null;
        $discountAmount = (float)($row['discount_amount'] ?? 0) * 1000;
        $debtAmountRaw  = (float)($row['debt_amount_raw'] ?? 0) * 1000;

        $createdAt  = $pay1Date ? ($pay1Date . ' 00:00:00') : now()->toDateTimeString();

        $finalTotalCharged = $price;
        $finalDebtAmount = 0;
        if ($debtAmountRaw > 0) {
            $finalDebtAmount = $debtAmountRaw;
            $finalTotalCharged = $pay1Amount + $pay2Amount;
        }

        $ecId = null;
        $ecLeaderId = null;
        if ($teamKinhDoanh && isset($this->usersByTmpName[$teamKinhDoanh])) {
            $ecId = $this->usersByTmpName[$teamKinhDoanh]['id'];
            $ecLeaderId = $this->usersByTmpName[$teamKinhDoanh]['manager_id'];
        }

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
                'gender'      => $gender ?: 'M',
                'branch_id'   => 9,
                'status'      => 1,
                'created_at'  => $createdAt,
                'updated_at'  => $createdAt,
            ];
            
            $this->newCrmParents[] = [
                '__student_key' => $tempId,
                'name'          => mb_substr($name, 0, 50),
                'mobile_1'      => $phone,
                'email'         => mb_substr($email, 0, 100),
                'link_facebook' => mb_substr($linkFb, 0, 255),
                'branch_id'     => 9,
                'status'        => 3, // based on standard crm_parents status
                'created_at'    => $createdAt,
                'updated_at'    => $createdAt,
            ];
            
            $this->studentsByPhone[$phone] = $tempId;
            $this->cntStudNew++;
        } else {
            $studentId = $this->studentsByPhone[$phone];
            if (is_numeric($studentId)) {
                if ($email) {
                    $this->updStudents[$studentId] = ['gud_email1' => mb_substr($email, 0, 100), 'updated_at' => now()->toDateTimeString()];
                    if (!isset($this->updCrmParents[$studentId])) $this->updCrmParents[$studentId] = ['updated_at' => now()->toDateTimeString()];
                    $this->updCrmParents[$studentId]['email'] = mb_substr($email, 0, 100);
                }
                if ($linkFb) {
                    if (!isset($this->updCrmParents[$studentId])) $this->updCrmParents[$studentId] = ['updated_at' => now()->toDateTimeString()];
                    $this->updCrmParents[$studentId]['link_facebook'] = mb_substr($linkFb, 0, 255);
                }
            }
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
                    'created_at'    => $createdAt,
                    'updated_at'    => $createdAt,
                ];
                $this->classesByCode[$classCode] = ['id' => $tempId, 'cls_startdate' => $startDate];
                $this->cntClsNew++;
            } elseif (!$this->classesByCode[$classCode]['cls_startdate'] && $startDate) {
                $this->updClasses[] = ['id' => $this->classesByCode[$classCode]['id'], 'cls_startdate' => $startDate];
                $this->classesByCode[$classCode]['cls_startdate'] = $startDate;
            }
            $classId = $this->classesByCode[$classCode]['id'];
        }

        $extraNote = sprintf("Team: %s | Sale: %s | Ghi chú: %s",
            $saleTeam, $saleMem, $shipNote
        );

        // ── 3. Agreement ──────────────────────────────────────────────────────
        $agrTuitionFeeId = $this->getTuitionFeeIdFromCourse($course);
        $agreementId = null;
        $isAgreementNew = false;

        if ($agrTuitionFeeId !== null) {
            $agrKey = $studentId . '_' . $agrTuitionFeeId;
            if (!isset($this->agreementsByStud[$agrKey])) {
                $tempId = 'new_a_' . count($this->newAgreements);
                
                $agrProductId    = $agrTuitionFeeId ? ($this->tuitionFeeProductId[$agrTuitionFeeId] ?? null) : null;

                $this->newAgreements[] = [
                    '__temp_id'     => $tempId,
                    '__student_key' => $studentId,
                    'tuition_fee_id'=> $agrTuitionFeeId,
                    'product_id'    => $agrProductId,
                    'ec_id'         => $ecId,
                    'ec_leader_id'  => $ecLeaderId,
                    'must_charge'   => $price,
                    'discount_amount'=> $discountAmount,
                    'total_charged' => $finalTotalCharged,
                    'debt_amount'   => $finalDebtAmount,
                    'note'          => $extraNote,
                    'branch_id'     => 9,
                    'status'        => 1,
                    'created_at'    => $createdAt,
                    'updated_at'    => $createdAt,
                ];
                $this->agreementsByStud[$agrKey] = $tempId;
                $this->cntAgreNew++;
                $isAgreementNew = true;
            } else {
                $agreementId = $this->agreementsByStud[$agrKey];
                if (is_numeric($agreementId)) {
                    $this->updAgreements[$agreementId] = ['note' => $extraNote, 'updated_at' => now()->toDateTimeString()];
                }
            }
            $agreementId = $this->agreementsByStud[$agrKey];
        }

        // ── 4. Contract status & sessions ─────────────────────────────────────
        $productId    = $this->getProductIdFromClassCode($classCode);
        
        $tuitionFeeId = null;
        if ($agrTuitionFeeId !== null) {
            if (isset($this->tuitionFeeRelations[$agrTuitionFeeId][$productId])) {
                $tuitionFeeId = $this->tuitionFeeRelations[$agrTuitionFeeId][$productId];
            } elseif (($this->tuitionFeeProductId[$agrTuitionFeeId] ?? null) == $productId) {
                $tuitionFeeId = $agrTuitionFeeId;
            }
        }
        if ($tuitionFeeId === null) {
            $tuitionFeeId = $this->getTuitionFeeIdFromProductId($productId);
        }

        $numSessions  = $productId ? ($this->productsById[$productId] ?? 0) : 0;

        $totalSes = $realSes = $sumSes = $doneSes = 0;

        $clsEnddate = null;
        if ($classCode && isset($this->classesByCode[$classCode])) {
            $clsEnddate = $this->classesByCode[$classCode]['cls_enddate'] ?? null;
        }

        $nowDate = date('Y-m-d');
        
        if ($rawStatus === '3') {
            $finalStatus    = 3;
            $finalStartDate = null;
            $finalClassId   = null;
            $totalSes = $realSes = $sumSes = $numSessions;
            $doneSes = 0;
        } elseif ($startDate && $startDate > $nowDate) {
            $finalStatus    = 6;
            $finalStartDate = $startDate;
            $finalClassId   = $classId;
            $totalSes = $realSes = $sumSes = $numSessions;
            $doneSes = 0;
        } elseif ($rawStatus === '7') {
            $finalStatus    = 7;
            $finalStartDate = $startDate;
            $finalClassId   = $classId;
            $totalSes = $realSes = $sumSes = $doneSes = $numSessions;
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

        // ── 5. Contract (1 per Excel row) ─────────────────────────────────────
        if ($agrTuitionFeeId !== null && $agreementId !== null) {
            
            $contractKey = $agreementId . '_' . $productId;
            $contractExistsInDb = false;
            $existingContractId = null;
            
            if (is_numeric($agreementId) && isset($this->contractsByAgrProd[$contractKey])) {
                $contractExistsInDb = true;
                $existingContractId = $this->contractsByAgrProd[$contractKey];
            }

            if ($contractExistsInDb) {
                // Update existing contract with class_id, status, dates
                $this->updContracts[] = [
                    'id'   => $existingContractId,
                    'data' => [
                        'class_id'             => $finalClassId,
                        'start_date'           => $finalStartDate,
                        'enrolment_start_date' => $finalStartDate,
                        'status'               => $finalStatus,
                        'total_sessions'       => $totalSes,
                        'real_sessions'   => $realSes,
                        'summary_sessions'=> $sumSes,
                        'done_sessions'   => $doneSes,
                        'updated_at'      => now()->toDateTimeString(),
                    ]
                ];
            } else {
                // Prevent creating duplicates in the same run if multiple rows map to the same product under this agreement
                if (!isset($this->newContractsKeys[$contractKey])) {
                    $isCombo = isset($this->tuitionFeeRelations[$agrTuitionFeeId]);
                    if ($productId !== null || !$isCombo) {
                        $this->newContracts[] = [
                            '__agreement_key' => $agreementId,
                            '__student_key'   => $studentId,
                            '__class_key'     => $finalClassId,
                            '__dedup_key'     => $dedupKey,
                            '__excel_row'     => $excelRow,
                            '__extra_note'    => $extraNote,
                            '__agr_total_charged' => $finalTotalCharged,
                            'product_id'      => $productId,
                            'tuition_fee_id'  => $tuitionFeeId,
                            'ec_id'           => $ecId,
                            'ec_leader_id'    => $ecLeaderId,
                            'must_charge'     => $tuitionFeeId ? ($this->tuitionFeePrice[$tuitionFeeId] ?? $price) : $price,
                            'discount_amount' => $discountAmount,
                            'total_charged'   => 0, // Will be calculated by processContractsByAgreement
                            'debt_amount'     => 0, // Will be calculated by processContractsByAgreement
                            'total_sessions'  => $totalSes,
                            'real_sessions'   => $realSes,
                            'summary_sessions'    => $sumSes,
                            'done_sessions'       => $doneSes,
                            'start_date'          => $finalStartDate,
                            'enrolment_start_date'=> $finalStartDate,
                            'status'              => $finalStatus,
                            'branch_id'       => 9,
                            'created_at'      => $createdAt,
                            'updated_at'      => $createdAt,
                        ];
                        $this->newContractsKeys[$contractKey] = count($this->newContracts) - 1;
                        $this->cntContNew++;
                    }
                } else {
                    // Update in-memory contract (e.g. auto-filled previously)
                    $idx = $this->newContractsKeys[$contractKey];
                    if (is_int($idx) && isset($this->newContracts[$idx])) {
                        $this->newContracts[$idx]['__class_key']          = $finalClassId;
                        $this->newContracts[$idx]['start_date']           = $finalStartDate;
                        $this->newContracts[$idx]['enrolment_start_date'] = $finalStartDate;
                        $this->newContracts[$idx]['status']               = $finalStatus;
                        // For auto-filled contracts, __dedup_key might be empty, so update it if we have one
                        if (empty($this->newContracts[$idx]['__dedup_key'])) {
                            $this->newContracts[$idx]['__dedup_key'] = $dedupKey;
                        }
                    }
                }
            }

            // Auto-fill missing contracts for this combo (if any)
            if (isset($this->tuitionFeeRelations[$agrTuitionFeeId])) {
                foreach ($this->tuitionFeeRelations[$agrTuitionFeeId] as $compProdId => $compTfId) {
                    $compContractKey = $agreementId . '_' . $compProdId;
                    
                    $compExistsInDb = false;
                    if (is_numeric($agreementId) && isset($this->contractsByAgrProd[$compContractKey])) {
                        $compExistsInDb = true;
                    }
                    
                    if (!$compExistsInDb && !isset($this->newContractsKeys[$compContractKey])) {
                        $tfPrice = $this->tuitionFeePrice[$compTfId] ?? 0;
                        $tfSession = $this->tuitionFeeSession[$compTfId] ?? 0;
                        
                        $this->newContracts[] = [
                            '__agreement_key' => $agreementId,
                            '__student_key'   => $studentId,
                            '__class_key'     => null,
                            '__dedup_key'     => '',
                            '__excel_row'     => $excelRow,
                            '__extra_note'    => "Auto-generated from combo",
                            '__agr_total_charged' => $finalTotalCharged,
                            'product_id'      => $compProdId,
                            'tuition_fee_id'  => $compTfId,
                            'ec_id'           => $ecId,
                            'ec_leader_id'    => $ecLeaderId,
                            'must_charge'     => $tfPrice,
                            'discount_amount' => 0,
                            'total_charged'   => 0,
                            'debt_amount'     => 0,
                            'total_sessions'  => $tfSession,
                            'real_sessions'   => $tfSession,
                            'summary_sessions'    => $tfSession,
                            'done_sessions'       => 0,
                            'start_date'          => null,
                            'enrolment_start_date'=> null,
                            'status'              => 4, // Pending
                            'branch_id'       => 9,
                            'created_at'      => $createdAt,
                            'updated_at'      => $createdAt,
                        ];
                        $this->newContractsKeys[$compContractKey] = count($this->newContracts) - 1;
                        $this->cntContNew++;
                    }
                }
            }

            // Mark dedup key as used in-memory (prevent duplicate in same run)
            if ($dedupKey) {
                $this->dedupKeys[$dedupKey] = true;
            }
    
            // ── 6. Payments (Phân đợt) ───────────────────────────────────────────
            // Chỉ tạo payment nếu đây là agreement mới tạo. Nếu agreement đã tồn tại ở DB thì payments cũng đã tồn tại.
            if ($isAgreementNew) {
                if ($pay1Amount > 0) {
                    $this->newPayments[] = [
                        '__agreement_key' => $agreementId,
                        '__student_key'   => $studentId,
                        '__phase'         => 1,
                        'amount'          => $pay1Amount,
                        'must_charge'     => $price,
                        'total'           => $pay1Amount,
                        'debt'            => max(0, $price - $pay1Amount),
                        'count'           => 1,
                        'type'            => 1,
                        'charge_date'     => $pay1Date ?: now()->format('Y-m-d'),
                        'method'          => 2, // Chuyển khoản (default guess)
                        'is_import'       => 1,
                        'note'            => 'Import đợt 1',
                        'branch_id'       => 9,
                        'created_at'      => $createdAt,
                    ];
                }
                if ($pay2Amount > 0) {
                    $this->newPayments[] = [
                        '__agreement_key' => $agreementId,
                        '__student_key'   => $studentId,
                        '__phase'         => 2,
                        'amount'          => $pay2Amount,
                        'must_charge'     => $price,
                        'total'           => $pay1Amount + $pay2Amount,
                        'debt'            => max(0, $price - ($pay1Amount + $pay2Amount)),
                        'count'           => 2,
                        'type'            => 1,
                        'charge_date'     => $pay2Date ?: now()->format('Y-m-d'),
                        'method'          => 2,
                        'is_import'       => 1,
                        'note'            => 'Import đợt 2',
                        'branch_id'       => 9,
                        'created_at'      => $createdAt,
                    ];
                }
            }
        } else {
            // Unmapped course, skip financials
            // Option to log or add tracking here if needed
        }
    }

    // ── Flush to DB (single transaction) ─────────────────────────────────────
    private function flush(): void
    {
        $processedAgreementIds = [];

        DB::transaction(function () use (&$processedAgreementIds) {
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

                $agrTotal = $ct['__agr_total_charged'];
                unset(
                    $ct['__agreement_key'], $ct['__student_key'],
                    $ct['__class_key'],     $ct['__dedup_key'],
                    $ct['__excel_row'],     $ct['__extra_note'],
                    $ct['__agr_total_charged']
                );

                if (!$agreementId || !$studentId) continue;

                $ct['agreement_id'] = $agreementId;
                $ct['student_id']   = $studentId;
                $ct['class_id']     = $classId;
                
                $prefix = $dedupKey ? "import_key:{$dedupKey} excel_row:{$excelRow}" : "excel_row:{$excelRow}";
                $ct['note'] = $prefix . " | " . $extraNote;

                if (!isset($contractsByAgr[$agreementId])) {
                    $contractsByAgr[$agreementId] = [
                        'remain' => $agrTotal,
                        'contracts' => []
                    ];
                }
                $contractsByAgr[$agreementId]['contracts'][] = $ct;
            }

            foreach ($contractsByAgr as $agrId => $grp) {
                $remain = $grp['remain'];
                $cts = &$grp['contracts'];
                // Not strictly sorting by count_recharge since they are all new (count_recharge=0)
                
                foreach ($cts as &$c) {
                    $mustCharge = $c['must_charge'];
                    $paid = ($remain <= 0) ? 0 : min($mustCharge, $remain);
                    $remain -= $paid;
                    
                    $c['total_charged'] = $paid;
                    $c['debt_amount'] = $mustCharge - $paid;
                    $c['init_total_charged'] = $paid;
                    
                    $tfSession = $this->tuitionFeeSession[$c['tuition_fee_id']] ?? $c['total_sessions'];
                    $availableSession = 0;
                    if ($tfSession > 0 && $mustCharge > 0) {
                        $availableSession = round($paid / ($mustCharge / $tfSession));
                    }
                    
                    $c['real_sessions'] = $availableSession;
                    $c['summary_sessions'] = $availableSession;
                    $c['left_sessions'] = $availableSession - ($c['done_sessions'] ?? 0);
                    
                    if ($c['class_id'] && $availableSession > 0) {
                        if (!in_array($c['status'], [6, 7])) {
                            $c['status'] = 6;
                        }
                    } else {
                        $c['status'] = ($paid >= $mustCharge) ? (($c['status'] > 3) ? $c['status'] : 3) : 2;
                    }
                    
                    $contBatch[] = $c;
                    $contStuIds[] = $c['student_id'];
                }
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
                
                $clsKey = $u['data']['class_id'] ?? null;
                if ($clsKey && str_starts_with((string)$clsKey, 'new_c_')) {
                    $u['data']['class_id'] = $clsTempMap[$clsKey] ?? null;
                }
                
                DB::table('contracts')->where('id', $u['id'])->update($u['data']);
            }
            if (count($this->updContracts)) {
                $this->info(sprintf('   ✔ Contracts updated: %d', count($this->updContracts)));
            }

            // Updates for existing records (students, parents, agreements) - Optimized with CASE WHEN
            $this->info("   ⏳ Bulk updating existing records (Students, Crm Parents, Agreements)...");
            
            $this->bulkUpdate('students', 'id', $this->updStudents);
            $this->bulkUpdate('crm_parents', 'student_id', $this->updCrmParents);
            $this->bulkUpdate('agreements', 'id', $this->updAgreements);

            if (count($this->updStudents) > 0) $this->info(sprintf('   ✔ Existing students updated (email): %d', count($this->updStudents)));
            if (count($this->updCrmParents) > 0) $this->info(sprintf('   ✔ Existing crm_parents updated (fb/email): %d', count($this->updCrmParents)));
            if (count($this->updAgreements) > 0) $this->info(sprintf('   ✔ Existing agreements updated (note): %d', count($this->updAgreements)));

            // 6. Payments
            $payBatch = [];
            $inRunPaymentCheck = []; // agreementId_phase -> true
            foreach ($this->newPayments as $p) {
                $agreementId = $resAgre($p['__agreement_key']);
                $studentId   = $resStud($p['__student_key']);
                $phase       = $p['__phase'];

                unset($p['__agreement_key'], $p['__student_key'], $p['__phase']);

                if (!$agreementId || !$studentId) continue;
                if (isset($this->importedPaymentsAgreements[$agreementId])) continue; // DB already has imports for this agreement
                
                $checkKey = "{$agreementId}_{$phase}";
                if (isset($inRunPaymentCheck[$checkKey])) continue; // Deduplicate in same run
                $inRunPaymentCheck[$checkKey] = true;

                $p['agreement_id'] = $agreementId;
                $p['student_id']   = $studentId;
                $payBatch[] = $p;
            }

            if ($payBatch) {
                foreach (array_chunk($payBatch, 500) as $chunk) {
                    DB::table('payments')->insert($chunk);
                }
                $this->cntPayNew = count($payBatch);
                $this->info(sprintf('   ✔ Payments inserted: %d', count($payBatch)));
            }

            // No post-processing needed anymore, calculated in memory.
        });
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    /**
     * Executes an optimized bulk UPDATE using CASE WHEN statements.
     * Dramatically faster than executing thousands of single updates in a loop.
     */
    private function bulkUpdate(string $table, string $idColumn, array $updates): void
    {
        if (empty($updates)) return;

        // Ensure all items in the array have the exact same keys
        $allKeys = [];
        foreach ($updates as $data) {
            foreach (array_keys($data) as $k) {
                $allKeys[$k] = true;
            }
        }
        $allKeys = array_keys($allKeys);

        foreach (array_chunk($updates, 1000, true) as $chunk) {
            $ids = array_keys($chunk);
            $bindings = [];
            $sql = "UPDATE {$table} SET ";
            
            foreach ($allKeys as $field) {
                $sql .= "`{$field}` = CASE `{$idColumn}` ";
                foreach ($chunk as $id => $data) {
                    if (array_key_exists($field, $data)) {
                        $sql .= "WHEN ? THEN ? ";
                        $bindings[] = $id;
                        $bindings[] = $data[$field];
                    }
                }
                $sql .= "ELSE `{$field}` END, ";
            }
            
            $sql = rtrim($sql, ", ");
            $sql .= " WHERE `{$idColumn}` IN (" . implode(',', array_fill(0, count($ids), '?')) . ")";
            
            foreach ($ids as $id) {
                $bindings[] = $id;
            }
            
            DB::update($sql, $bindings);
        }
    }

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
        // Loại bỏ toàn bộ khoảng trắng và đưa về viết thường để chống sai sót do gõ thừa/thiếu phím space
        $c = str_replace(' ', '', mb_strtolower(trim($course), 'UTF-8'));
        
        // Map với các gói có độ chính xác cao nhất (nhiều thành phần nhất) trước
        if (str_contains($c, 'combopre_toeic+toeiclv(1+2)+toeic(s+w)')) return 50;
        if (str_contains($c, 'combopre_toeic+toeiclv(1+2)+ônthict(l&r)+toeicw')) return 53; // Ôn thi CT + Toeic W -> tương đương combo có W
        if (str_contains($c, 'combopre_toeic+toeiclv(1+2)+ônthict(l&r)')) return 44; // Ôn thi CT -> tương đương combo 1+2
        if (str_contains($c, 'combopre_toeic+toeiclv(1+2)+toeics')) return 46;
        if (str_contains($c, 'combopre_toeic+toeiclv(1+2)+toeicw')) return 53;
        if (str_contains($c, 'combopre_toeic+toeiclv(1+2)')) return 44;
        
        if (str_contains($c, 'combotoeiclv(1+2)+toeic(s+w)')) return 49;
        if (str_contains($c, 'toeiclv(1+2)+toeic(s+w)')) return 49;
        if (str_contains($c, 'combotoeiclv(1+2)+toeics')) return 54;
        if (str_contains($c, 'toeiclv(1+2)+toeics')) return 54;
        if (str_contains($c, 'combotoeiclv1+2')) return 45;
        
        if (str_contains($c, 'combopre_toeic+toeiclv1+toeic(s+w)')) return 51;
        if (str_contains($c, 'combopre_toeic+toeiclv1')) return 43;

        if (str_contains($c, 'combotoeiclv1+toeic(s+w)')) return 52;
        
        if (str_contains($c, 'combotoeiclv2+toeic(s+w)')) return 48;
        if (str_contains($c, 'combotoeiclv2+toeics')) return 55;
        if (str_contains($c, 'combotoeic(s+w)')) return 47;
        if (str_contains($c, 'toeic(s+w)')) return 47;
        
        if (str_contains($c, 'pre_toeic')) return 38;
        if (str_contains($c, 'toeiclv1')) return 39;
        if (str_contains($c, 'toeiclv2')) return 40;
        if (str_contains($c, 'toeicspeaking')) return 41;
        if (str_contains($c, 'toeicwriting')) return 42;
        
        return null;
    }

    private function getProductIdFromClassCode(string $code): ?int
    {
        if (str_starts_with($code, 'O')) {
            $code = substr($code, 1); // Bỏ tiền tố O (Online)
        }
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
