<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportPaymentsFromJson extends Command
{
    protected $signature = 'erp:import-payments-json
        {files* : Path to one or more json files}';

    protected $description = 'Import payments from json files';

    private array $studentsByPhone = [];
    private array $agreementsByStud = [];
    private array $paymentsSumByAgreement = [];

    private array $newPayments = [];
    private int $totalInserted = 0;

    public function handle(): int
    {
        ini_set('memory_limit', '-1');

        $files = $this->argument('files');
        foreach ($files as $f) {
            if (!file_exists($f)) {
                $this->error("File not found: $f");
                return 1;
            }
        }

        $this->info('Preloading data...');
        $this->preload();

        foreach ($files as $file) {
            $this->info("Processing $file ...");
            $data = json_decode(file_get_contents($file), true);
            if (!is_array($data)) continue;

            $totalRows = count($data);
            $this->info("Total rows in $file: $totalRows");
            
            foreach ($data as $index => $row) {
                $this->processRow($row);
                
                if (count($this->newPayments) >= 500) {
                    $this->flush();
                }
                
                if (($index + 1) % 1000 === 0) {
                    $this->info("Processed " . ($index + 1) . " / $totalRows rows...");
                }
            }
        }

        $this->info("Flushing remaining to DB...");
        $this->flush();

        $this->info("Done! Inserted " . $this->totalInserted . " payments.");
        return 0;
    }

    private function preload(): void
    {
        DB::table('students')->whereNotNull('gud_mobile1')->select('id', 'gud_mobile1')->orderBy('id')->chunk(5000, function ($rows) {
            foreach ($rows as $r) {
                $n = $this->normalizePhone($r->gud_mobile1);
                if ($n && !isset($this->studentsByPhone[$n])) {
                    $this->studentsByPhone[$n] = $r->id;
                }
            }
        });

        DB::table('agreements')->select('id', 'student_id', 'tuition_fee_id', 'created_at')->orderBy('id')->chunk(5000, function ($rows) {
            foreach ($rows as $r) {
                $key = $r->student_id . '_' . $r->tuition_fee_id;
                if (!isset($this->agreementsByStud[$key])) {
                    $this->agreementsByStud[$key] = [
                        'id' => $r->id,
                        'created_at' => $r->created_at,
                    ];
                }
            }
        });

        DB::table('payments')->select('agreement_id', 'amount')->orderBy('id')->chunk(5000, function ($rows) {
            foreach ($rows as $r) {
                if (!isset($this->paymentsSumByAgreement[$r->agreement_id])) {
                    $this->paymentsSumByAgreement[$r->agreement_id] = 0;
                }
                $this->paymentsSumByAgreement[$r->agreement_id] += (float)$r->amount;
            }
        });
    }

    private function processRow(array $row): void
    {
        $phone = $this->normalizePhone($row['phone'] ?? '');
        $course = $row['course'] ?? '';
        
        if (!$phone || !$course) return;

        $studentId = $this->studentsByPhone[$phone] ?? null;
        if (!$studentId) return;

        $tuitionFeeId = $this->getTuitionFeeIdFromCourse($course);
        if (!$tuitionFeeId) return;

        $agrKey = $studentId . '_' . $tuitionFeeId;
        $agrInfo = $this->agreementsByStud[$agrKey] ?? null;
        if (!$agrInfo) return;
        
        $agreementId = $agrInfo['id'];
        $agreementCreatedAt = $agrInfo['created_at'] ?? now()->toDateTimeString();

        $sumAmount = $this->paymentsSumByAgreement[$agreementId] ?? 0;
        
        // "lưu ý *1000 vì đơn vị là nghìn"
        $multiplier = 1000;
        $pay1Amount = (float)($row['payment_1_amount'] ?? 0) * $multiplier;
        $pay2Amount = (float)($row['payment_2_amount'] ?? 0) * $multiplier;
        
        $pay1Date = $this->parsePaymentDate($row['payment_1_date'] ?? null, $agreementCreatedAt);
        $pay2Date = $this->parsePaymentDate($row['payment_2_date'] ?? null, $agreementCreatedAt);
        $price = (float)($row['price'] ?? 0) * $multiplier;

        if ($pay1Amount > 0) {
            if ($sumAmount >= $pay1Amount) {
                $sumAmount -= $pay1Amount;
            } else {
                $amountToInsert1 = $pay1Amount - $sumAmount;
                $this->newPayments[] = [
                    'agreement_id' => $agreementId,
                    'student_id'   => $studentId,
                    'amount'       => $amountToInsert1,
                    'must_charge'  => $price,
                    'total'        => $pay1Amount,
                    'debt'         => max(0, $price - $pay1Amount),
                    'count'        => 1,
                    'type'         => 1,
                    'charge_date'  => $pay1Date,
                    'method'       => 2,
                    'is_import'    => 2,
                    'note'         => 'Import payment 1',
                    'branch_id'    => 9,
                    'created_at'   => $pay1Date . ' 00:00:00',
                ];
                $sumAmount = 0;
            }
        }

        if ($pay2Amount > 0) {
            if ($sumAmount >= $pay2Amount) {
                $sumAmount -= $pay2Amount;
            } else {
                $amountToInsert2 = $pay2Amount - $sumAmount;
                $this->newPayments[] = [
                    'agreement_id' => $agreementId,
                    'student_id'   => $studentId,
                    'amount'       => $amountToInsert2,
                    'must_charge'  => $price,
                    'total'        => $pay1Amount + $pay2Amount,
                    'debt'         => max(0, $price - ($pay1Amount + $pay2Amount)),
                    'count'        => 2,
                    'type'         => 1,
                    'charge_date'  => $pay2Date,
                    'method'       => 2,
                    'is_import'    => 2,
                    'note'         => 'Import payment 2',
                    'branch_id'    => 9,
                    'created_at'   => $pay2Date . ' 00:00:00',
                ];
                $sumAmount = 0;
            }
        }
    }

    private function flush(): void
    {
        if (empty($this->newPayments)) return;
        
        DB::transaction(function () {
            foreach (array_chunk($this->newPayments, 500) as $chunk) {
                DB::table('payments')->insert($chunk);
                $this->totalInserted += count($chunk);
            }
        });
        
        $this->newPayments = [];
    }

    private function getTuitionFeeIdFromCourse(string $course): ?int
    {
        $c = str_replace(' ', '', mb_strtolower(trim($course), 'UTF-8'));
        
        if (str_contains($c, 'combopre_toeic+toeiclv(1+2)+toeic(s+w)')) return 50;
        if (str_contains($c, 'combopre_toeic+toeiclv(1+2)+ônthict(l&r)+toeicw')) return 53;
        if (str_contains($c, 'combopre_toeic+toeiclv(1+2)+ônthict(l&r)')) return 44;
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

    private function parsePaymentDate(?string $rawDate, string $defaultDate): string
    {
        if (empty($rawDate) || trim($rawDate) === '') {
            return date('Y-m-d', strtotime($defaultDate));
        }

        $parts = preg_split('/(\s+và\s+|,|\s+&\s+|-|\s+)/iu', trim($rawDate));
        // Some might be "19/08/2024 và 23/08/2024", we split by "và", ",", "&", "-", or whitespace
        // Wait, whitespace might split the date itself if it's badly formatted. 
        // Just split by 'và' or ',' or '&'
        $parts = preg_split('/(\s+và\s+|,|\s+&\s+)/iu', trim($rawDate));
        $lastPart = trim(end($parts));

        if (str_contains($lastPart, '/')) {
            $d = \DateTime::createFromFormat('d/m/Y', $lastPart);
            if (!$d) {
                $d = \DateTime::createFromFormat('d/m/y', $lastPart);
            }
            if ($d) return $d->format('Y-m-d');
        }

        $ts = strtotime($lastPart);
        if ($ts) {
            return date('Y-m-d', $ts);
        }

        return date('Y-m-d', strtotime($defaultDate));
    }
}
