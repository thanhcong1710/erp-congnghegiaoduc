<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\HrmEmployeeProfile;
use App\Models\HrmDepartment;
use App\Models\HrmJobTitle;
use App\Models\HrmContract;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ImportHrmEmployees extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hrm:import-employees {--dry-run : Run in simulation mode without writing to DB}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import HRM employees list independently from users table';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $isDryRun = $this->option('dry-run');
        $jsonPath = '/Users/mac24h/.gemini/antigravity/brain/091f9e33-fb79-4b8f-8265-dbb60da92f96/scratch/parsed_employees.json';

        if (!file_exists($jsonPath)) {
            $this->error("JSON file not found at: {$jsonPath}");
            return 1;
        }

        $rawList = json_decode(file_get_contents($jsonPath), true);
        if (!is_array($rawList)) {
            $this->error("Failed to parse JSON file.");
            return 1;
        }

        $this->info("Read " . count($rawList) . " raw employee records.");

        // Deduplication by Email
        $deduped = [];
        foreach ($rawList as $item) {
            $email = strtolower(trim($item['email'] ?? ''));
            if (empty($email)) {
                $email = 'no_email_' . md5($item['name'] . ($item['id_card_number'] ?? ''));
            }

            if (!isset($deduped[$email])) {
                $deduped[$email] = $item;
            } else {
                // Merge richer info
                if (($item['doc_status'] ?? '') === 'full') {
                    $deduped[$email]['doc_status'] = 'full';
                }
                if (($item['contract_status'] ?? '') === 'signed') {
                    $deduped[$email]['contract_status'] = 'signed';
                }
                foreach (['phone', 'id_card_number', 'id_card_date', 'address_permanent', 'address_current', 'start_date', 'job_title_name'] as $k) {
                    if (empty($deduped[$email][$k]) && !empty($item[$k])) {
                        $deduped[$email][$k] = $item[$k];
                    }
                }
            }
        }

        $employees = array_values($deduped);
        $this->info("Unique employees after deduplication: " . count($employees));

        // Load existing departments and job titles
        $departments = HrmDepartment::all()->keyBy('name');
        $jobTitles = HrmJobTitle::all()->keyBy('name');

        // Department mapping dictionary
        $deptAliases = [
            'Team văn phòng' => 'Khối Văn phòng',
            'Team TG Mỹ Huyền' => 'Team Trợ giảng Huyền',
            'Team TG Minh' => 'Team Trợ giảng Minh',
            'Team TG Giang' => 'Team Trợ giảng Giang',
            'Team GV Thắng' => 'Team Giáo viên Thắng',
            'Team GV Hạ' => 'Team Giáo viên Hạ',
            'Team GV Quân' => 'Team Giáo viên Quân',
            'Team GV Hương Giang' => 'Team Giáo viên Hương Giang',
            'Thu Phương Meca' => 'Team Thu Phương Meca',
            'Diễm Quỳnh Meca' => 'Team Diễm Quỳnh Meca',
            'Thái Bảo Meca' => 'Team Thái Bảo Meca',
            'Team Hồng Vân' => 'Team Hồng Vân',
            'Team Thành Đạt' => 'Team Thành Đạt',
            'Ngọc Anh Meca' => 'Team Ngọc Anh Meca',
            'Đinh Linh Meca' => 'Team Đinh Linh Meca',
            'Hải Yến Meca' => 'Team Hải Yến Meca',
            'Nguyễn Thị Hải Yến Meca' => 'Team Hải Yến Meca',
            'Nguyễn Yến Meca' => 'Team Nguyễn Yến Meca',
            'Tùng Meca' => 'Team Tùng Meca',
            'Hà Thiệp Meca' => 'Team Hà Thiệp Meca',
            'Ngô Đức Meca' => 'Team Ngô Đức Meca',
            'Hương Trúc Meca' => 'Team Hương Trúc Meca',
            'hương Trúc Meca' => 'Team Hương Trúc Meca',
            'Xuân Đạt Meca' => 'Team Xuân Đạt Meca',
            'Hải Anh Meca' => 'Team Hải Anh Meca',
            'Thu Hà Meca' => 'Team Thu Hà Meca',
            'Thu Hiền Meca' => 'Team Thu Hiền Meca',
            'Quang Huy Meca' => 'Team Quang Huy Meca',
            'Hồng Anh Meca' => 'Team Hồng Anh Meca',
            'Team Đặng Hồng Anh Meca' => 'Team Đặng Hồng Anh Meca',
            'Vũ Thúy Meca' => 'Team Vũ Thị Thúy Meca',
            'Dương Meca' => 'Team Dương Meca',
            'Team Uyên LP' => 'Team Uyên LP',
            'Team Lê Phương' => 'Team Lê Phương',
            'Team MKT Thanh Nga' => 'Team MKT Thanh Nga',
            'Anh Thư Meca' => 'Team Anh Thư Meca',
            'Yến Nhi Meca' => 'Team Yến Nhi Meca',
            'Team Quỳnh Anh Meca' => 'Team Quỳnh Anh Meca',
            'Team Mai Chi Meca' => 'Team Mai Chi Meca',
        ];

        // Job titles mapping dictionary
        $titleAliases = [
            'Trợ giảng' => 'Trợ giảng',
            'GV online' => 'Giáo viên Online',
            'GV offline' => 'Giáo viên Offline',
            'Quản lý GV' => 'Quản lý Giáo viên',
            'Quản lý TG' => 'Quản lý Trợ giảng',
            'Nhân viên MKT' => 'Nhân viên Marketing',
            'Trưởng nhóm MKT' => 'Trưởng nhóm Marketing',
            'Kế toán' => 'Kế toán',
            'Chăm sóc khách hàng' => 'Chăm sóc khách hàng',
            'Chuyên viên kinh doanh' => 'Chuyên viên Kinh doanh',
            'Chuyên viên kinh doanh (Sale)' => 'Chuyên viên Kinh doanh',
            'Lead Sale (Meca)' => 'Lead Sale',
            'Lead Sale (Leng Keng)' => 'Lead Sale',
            'Lead Sale' => 'Lead Sale',
            'Nhân viên văn phòng' => 'Nhân viên văn phòng',
        ];

        if ($isDryRun) {
            $this->warn("=== RUNNING IN DRY-RUN MODE (NO DB CHANGES) ===");
        }

        DB::beginTransaction();

        try {
            $importedCount = 0;
            $contractCount = 0;

            foreach ($employees as $idx => $emp) {
                $codeNum = $idx + 1;
                $empCode = 'CHAM' . str_pad($codeNum, 2, '0', STR_PAD_LEFT);

                // Resolve department
                $rawDept = trim($emp['department_name'] ?? '');
                $targetDeptName = $deptAliases[$rawDept] ?? $rawDept;

                $dept = HrmDepartment::where('name', $targetDeptName)->first();
                if (!$dept && !empty($targetDeptName)) {
                    if (!$isDryRun) {
                        $dept = HrmDepartment::create([
                            'name' => $targetDeptName,
                            'status' => 1
                        ]);
                    } else {
                        $this->line("Would create department: {$targetDeptName}");
                    }
                }
                $departmentId = $dept ? $dept->id : null;

                // Resolve job title
                $rawTitle = trim($emp['job_title_name'] ?? '');
                $targetTitleName = $titleAliases[$rawTitle] ?? null;

                if (!$targetTitleName) {
                    // Fallback based on department
                    if (strpos($rawDept, 'TG') !== false) {
                        $targetTitleName = 'Trợ giảng';
                    } elseif (strpos($rawDept, 'GV') !== false) {
                        $targetTitleName = 'Giáo viên Online';
                    } elseif (strpos($rawDept, 'MKT') !== false) {
                        $targetTitleName = 'Nhân viên Marketing';
                    } else {
                        $targetTitleName = 'Chuyên viên Kinh doanh';
                    }
                }

                $jobTitle = HrmJobTitle::where('name', $targetTitleName)->first();
                $jobTitleId = $jobTitle ? $jobTitle->id : null;

                // Parse dates
                $dob = $this->parseDateString($emp['dob'] ?? null);
                $startDate = $this->parseDateString($emp['start_date'] ?? null);
                $idCardDate = $this->parseDateString($emp['id_card_date'] ?? null);

                // Address fallback
                $address = $emp['address_current'] ?: ($emp['address_permanent'] ?? null);

                // Profile status
                $profileStatus = ($emp['doc_status'] ?? '') === 'full' ? 'full' : 'missing';
                $profileReceivedDate = $profileStatus === 'full' ? ($startDate ?: date('Y-m-d')) : null;

                $profileData = [
                    'user_id' => null, // COMPLETELY INDEPENDENT FROM USERS
                    'employee_code' => $empCode,
                    'full_name' => mb_strtoupper(trim($emp['name']), 'UTF-8'),
                    'email' => trim($emp['email']),
                    'employee_type' => $emp['employee_type'] ?: 'ctv',
                    'work_status' => 'working',
                    'approval_status' => 1,
                    'created_by_user_id' => 1,
                    'department_id' => $departmentId,
                    'job_title_id' => $jobTitleId,
                    'dob' => $dob,
                    'gender' => ($emp['gender'] ?? '') === 'Nam' ? 1 : 2,
                    'start_date' => $startDate,
                    'id_card_number' => $emp['id_card_number'] ?? null,
                    'id_card_date' => $idCardDate,
                    'phone' => $emp['phone'] ?? null,
                    'address' => $address,
                    'contract_type' => $emp['employee_type'] === 'fulltime' ? 'Fulltime' : 'CTV',
                    'bank_account' => $emp['bank_account'] ?? null,
                    'bank_name' => $emp['bank_name'] ?? null,
                    'profile_status' => $profileStatus,
                    'profile_received_date' => $profileReceivedDate,
                ];

                if (!$isDryRun) {
                    $profile = HrmEmployeeProfile::create($profileData);

                    // Create contract if signed
                    if (($emp['contract_status'] ?? '') === 'signed') {
                        HrmContract::create([
                            'employee_id' => $profile->id,
                            'user_id' => null,
                            'contract_number' => 'HD-' . $empCode,
                            'contract_type' => $emp['employee_type'] === 'fulltime' ? 'Fulltime' : 'CTV',
                            'start_date' => $startDate ?: date('Y-m-d'),
                            'status' => 1,
                        ]);
                        $contractCount++;
                    }
                }

                $importedCount++;
            }

            if ($isDryRun) {
                DB::rollBack();
                $this->info("Dry-run finished. Ready to import {$importedCount} employees.");
            } else {
                DB::commit();
                $this->info("Successfully imported {$importedCount} employees and {$contractCount} signed contracts into HRM!");
            }

            return 0;
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("Error during import: " . $e->getMessage() . " at line " . $e->getLine());
            return 1;
        }
    }

    private function parseDateString($str)
    {
        if (empty($str)) return null;
        $str = trim($str);
        if (preg_match('/^(\d{1,2})\/(\d{1,2})\/(\d{4})$/', $str, $m)) {
            $day = (int)$m[1];
            $month = (int)$m[2];
            $year = (int)$m[3];
            if (checkdate($month, $day, $year)) {
                return sprintf('%04d-%02d-%02d', $year, $month, $day);
            }
        }
        return null;
    }
}

