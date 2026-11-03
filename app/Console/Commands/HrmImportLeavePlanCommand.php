<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\HrmEmployeeProfile;
use App\Models\HrmLeaveRequest;
use App\Models\HrmLeaveBalance;
use App\Models\HrmDepartment;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class HrmImportLeavePlanCommand extends Command
{
    protected $signature = 'hrm:import-leave-plan';
    protected $description = 'Import and normalize leave plan dataset for all staff';

    public function handle()
    {
        $this->info("=== BẮT ĐẦU IMPORT VÀ CHUẨN HÓA DỮ LIỆU NGHỈ PHÉP NHÂN SỰ ===");

        $dataRows = [
            ['name' => 'Bùi Thế Anh', 'days' => 2.0, 'date' => '28/04/2026', 'position' => 'Nhân viên', 'dept' => 'Sale', 'note' => ''],
            ['name' => 'Bùi Thế Anh', 'days' => 0.5, 'date' => '04/05/2026', 'position' => 'Nhân viên', 'dept' => 'Sale', 'note' => ''],
            ['name' => 'Bùi Thế Anh', 'days' => 0.5, 'date' => '03/06/2026', 'position' => 'Nhân viên', 'dept' => 'Sale', 'note' => ''],
            ['name' => 'Bùi Thế Anh', 'days' => 0.5, 'date' => '05/06/2026', 'position' => 'Nhân viên', 'dept' => 'Sale', 'note' => ''],
            ['name' => 'Bùi Thế Anh', 'days' => 0.5, 'date' => '10/07/2026', 'position' => 'Nhân viên', 'dept' => 'Sale', 'note' => ''],
            ['name' => 'Bùi Thế Anh', 'days' => 0.5, 'date' => '15/07/2026', 'position' => 'Nhân viên', 'dept' => 'Sale', 'note' => ''],
            ['name' => 'Bùi Thế Anh', 'days' => 1.0, 'date' => '24/07/2026', 'position' => 'Nhân viên', 'dept' => 'Sale', 'note' => ''],
            ['name' => 'Bùi Thế Anh', 'days' => 0.5, 'date' => '20/08/2026', 'position' => 'Nhân viên', 'dept' => 'Sale', 'note' => ''],
            ['name' => 'Bùi Thế Anh', 'days' => 0.5, 'date' => '20/08/2026', 'position' => 'Nhân viên', 'dept' => 'Sale', 'note' => ''],

            ['name' => 'Đặng Thị Anh Thư', 'days' => 0.5, 'date' => '07/04/2026', 'position' => 'Nhân viên', 'dept' => 'Sale', 'note' => ''],
            ['name' => 'Đặng Thị Anh Thư', 'days' => 0.5, 'date' => '05/05/2026', 'position' => 'Nhân viên', 'dept' => 'Đào tạo', 'note' => ''],
            ['name' => 'Đặng Thị Anh Thư', 'days' => 0.5, 'date' => '18/05/2026', 'position' => 'Trưởng phòng kinh doanh', 'dept' => 'Sale', 'note' => ''],
            ['name' => 'Đặng Thị Anh Thư', 'days' => 0.5, 'date' => '22/05/2026', 'position' => 'Nhân viên', 'dept' => 'Sale', 'note' => ''],
            ['name' => 'Đặng Thị Anh Thư', 'days' => 0.5, 'date' => '26/06/2026', 'position' => 'Trưởng phòng kinh doanh', 'dept' => 'Sale', 'note' => ''],
            ['name' => 'Đặng Thị Anh Thư', 'days' => 0.5, 'date' => '10/07/2026', 'position' => 'Nhân viên', 'dept' => 'Sale', 'note' => ''],
            ['name' => 'Đặng Thị Anh Thư', 'days' => 1.0, 'date' => '23/09/2026', 'position' => 'Trưởng phòng kinh doanh', 'dept' => 'Sale', 'note' => ''],
            ['name' => 'Đặng Thị Anh Thư', 'days' => 0.5, 'date' => '03/09/2026', 'position' => 'Nhân viên', 'dept' => 'Sale', 'note' => ''],
            ['name' => 'Đặng Thị Anh Thư', 'days' => 0.5, 'date' => '21/09/2026', 'position' => 'Trưởng phòng kinh doanh', 'dept' => 'Sale', 'note' => ''],

            ['name' => 'Đặng Thị Hồng Anh', 'days' => 0.5, 'date' => '11/02/2026', 'position' => 'Trưởng phòng kinh doanh', 'dept' => 'Sale', 'note' => ''],
            ['name' => 'Đặng Thị Hồng Anh', 'days' => 1.0, 'date' => '22/02/2026', 'position' => 'Trưởng phòng kinh doanh', 'dept' => 'Sale', 'note' => ''],
            ['name' => 'Đặng Thị Hồng Anh', 'days' => 0.5, 'date' => '17/03/2026', 'position' => 'Trưởng phòng kinh doanh', 'dept' => 'Sale', 'note' => ''],
            ['name' => 'Đặng Thị Hồng Anh', 'days' => 1.0, 'date' => '22/06/2026', 'position' => 'Trưởng phòng kinh doanh', 'dept' => 'Sale', 'note' => ''],
            ['name' => 'Đặng Thị Hồng Anh', 'days' => 0.5, 'date' => '07/08/2026', 'position' => 'Trưởng phòng kinh doanh', 'dept' => 'Sale', 'note' => ''],
            ['name' => 'Đặng Thị Hồng Anh', 'days' => 0.5, 'date' => '26/01/2026', 'position' => 'Trưởng phòng kinh doanh', 'dept' => 'Sale', 'note' => ''],
            ['name' => 'Đặng Thị Hồng Anh', 'days' => 0.5, 'date' => '23/10/2026', 'position' => 'Trưởng phòng kinh doanh', 'dept' => 'Sale', 'note' => ''],

            ['name' => 'Đỗ Minh Vũ', 'days' => 0.5, 'date' => '03/02/2026', 'position' => 'Nhân viên', 'dept' => 'Đào tạo', 'note' => ''],
            ['name' => 'Đỗ Minh Vũ', 'days' => 0.5, 'date' => '16/03/2026', 'position' => 'Nhân viên', 'dept' => 'Đào tạo', 'note' => ''],
            ['name' => 'Đỗ Minh Vũ', 'days' => 0.5, 'date' => '27/03/2026', 'position' => 'Nhân viên', 'dept' => 'Đào tạo', 'note' => ''],
            ['name' => 'Đỗ Minh Vũ', 'days' => 0.5, 'date' => '13/04/2026', 'position' => 'Nhân viên', 'dept' => 'Đào tạo', 'note' => ''],
            ['name' => 'Đỗ Minh Vũ', 'days' => 0.5, 'date' => '15/04/2026', 'position' => 'Nhân viên', 'dept' => 'Đào tạo', 'note' => ''],
            ['name' => 'Đỗ Minh Vũ', 'days' => 0.5, 'date' => '23/04/2026', 'position' => 'Nhân viên', 'dept' => 'Đào tạo', 'note' => ''],
            ['name' => 'Đỗ Minh Vũ', 'days' => 0.5, 'date' => '24/04/2026', 'position' => 'Nhân viên', 'dept' => 'Đào tạo', 'note' => ''],
            ['name' => 'Đỗ Minh Vũ', 'days' => 1.0, 'date' => '15/05/2026', 'position' => 'Nhân viên', 'dept' => 'Đào tạo', 'note' => ''],
            ['name' => 'Đỗ Minh Vũ', 'days' => 0.5, 'date' => '06/10/2026', 'position' => 'Nhân viên', 'dept' => 'Đào tạo', 'note' => ''],
            ['name' => 'Đỗ Minh Vũ', 'days' => 0.5, 'date' => '08/10/2026', 'position' => 'Nhân viên', 'dept' => 'Đào tạo', 'note' => ''],
            ['name' => 'Đỗ Minh Vũ', 'days' => 1.0, 'date' => '09/10/2026', 'position' => 'Nhân viên', 'dept' => 'Đào tạo', 'note' => ''],

            ['name' => 'Dương Thị Thái Bảo', 'days' => 1.0, 'date' => '26/06/2026', 'position' => 'Trưởng phòng kinh doanh', 'dept' => 'Sale', 'note' => ''],
            ['name' => 'Dương Thị Thái Bảo', 'days' => 1.0, 'date' => '25/08/2026', 'position' => 'Nhân viên', 'dept' => 'Sale', 'note' => ''],

            ['name' => 'Dương Thị Vân Anh', 'days' => 1.0, 'date' => '27/03/2026', 'position' => 'Nhân viên', 'dept' => 'Sale', 'note' => ''],
            ['name' => 'Dương Thị Vân Anh', 'days' => 2.0, 'date' => '08/04/2026', 'position' => 'Nhân viên', 'dept' => 'Sale', 'note' => ''],
            ['name' => 'Dương Thị Vân Anh', 'days' => 1.0, 'date' => '29/05/2026', 'position' => 'Nhân viên', 'dept' => 'Sale', 'note' => ''],
            ['name' => 'Dương Thị Vân Anh', 'days' => 1.0, 'date' => '03/06/2026', 'position' => 'Nhân viên', 'dept' => 'Sale', 'note' => ''],
            ['name' => 'Dương Thị Vân Anh', 'days' => 2.0, 'date' => '23/06/2026', 'position' => 'Nhân viên', 'dept' => 'Sale', 'note' => ''],
            ['name' => 'Dương Thị Vân Anh', 'days' => 1.5, 'date' => '30/07/2026', 'position' => 'Nhân viên', 'dept' => 'Sale', 'note' => ''],
            ['name' => 'Dương Thị Vân Anh', 'days' => 1.5, 'date' => '15/09/2026 - 16/09/2026', 'position' => 'Nhân viên', 'dept' => 'Sale', 'note' => '15,16/09'],
            ['name' => 'Dương Thị Vân Anh', 'days' => 2.0, 'date' => '08/07/2026 - 09/07/2026', 'position' => 'Nhân viên', 'dept' => 'Sale', 'note' => '08/07/2026 và 09/07/2026'],

            ['name' => 'Giáp Thanh Trúc', 'days' => 1.0, 'date' => '12/06/2026', 'position' => 'Nhân viên', 'dept' => 'Khối văn phòng', 'note' => ''],
            ['name' => 'Giáp Thanh Trúc', 'days' => 0.5, 'date' => '29/10/2026', 'position' => 'Nhân viên', 'dept' => 'Khối văn phòng', 'note' => ''],

            ['name' => 'Hà Thị Thiệp', 'days' => 1.0, 'date' => '16/04/2026', 'position' => 'Trưởng phòng kinh doanh', 'dept' => 'Marketing', 'note' => ''],
            ['name' => 'Hà Thị Thiệp', 'days' => 0.5, 'date' => '06/07/2026', 'position' => 'Trưởng phòng kinh doanh', 'dept' => 'Marketing', 'note' => ''],
            ['name' => 'Hà Thị Thiệp', 'days' => 1.0, 'date' => '28/08/2026', 'position' => 'Trưởng phòng kinh doanh', 'dept' => 'Marketing', 'note' => ''],
            ['name' => 'Hà Thị Thiệp', 'days' => 0.5, 'date' => '21/09/2026', 'position' => 'Trưởng phòng kinh doanh', 'dept' => 'Marketing', 'note' => ''],

            ['name' => 'Hà Thị Thu Hiền', 'days' => 0.5, 'date' => '16/07/2026', 'position' => 'Trưởng phòng kinh doanh', 'dept' => 'Sale', 'note' => ''],

            ['name' => 'Hoàng Thành Đạt', 'days' => 1.0, 'date' => '04/03/2026', 'position' => 'Nhân viên', 'dept' => 'Khối văn phòng', 'note' => ''],
            ['name' => 'Hoàng Thành Đạt', 'days' => 1.0, 'date' => '27/03/2026', 'position' => 'Nhân viên', 'dept' => 'Khối văn phòng', 'note' => ''],
            ['name' => 'Hoàng Thành Đạt', 'days' => 0.5, 'date' => '17/04/2026', 'position' => 'Nhân viên', 'dept' => 'Khối văn phòng', 'note' => ''],
            ['name' => 'Hoàng Thành Đạt', 'days' => 1.0, 'date' => '03/07/2026', 'position' => 'Nhân viên', 'dept' => 'Khối văn phòng', 'note' => ''],
            ['name' => 'Hoàng Thành Đạt', 'days' => 1.5, 'date' => '04/09/2026', 'position' => 'Nhân viên', 'dept' => 'Khối văn phòng', 'note' => ''],
            ['name' => 'Hoàng Thành Đạt', 'days' => 2.0, 'date' => '14/10/2026 - 15/10/2026', 'position' => 'Nhân viên', 'dept' => 'Khối văn phòng', 'note' => '14,15/10'],

            ['name' => 'Lê Thị Ngọc Anh', 'days' => 1.0, 'date' => '30/03/2026', 'position' => 'Nhân viên', 'dept' => 'Marketing', 'note' => ''],
            ['name' => 'Lê Thị Ngọc Anh', 'days' => 0.5, 'date' => '23/04/2026', 'position' => 'Nhân viên', 'dept' => 'Sale', 'note' => ''],
            ['name' => 'Lê Thị Ngọc Anh', 'days' => 1.0, 'date' => '26/06/2026', 'position' => 'Nhân viên', 'dept' => 'Sale', 'note' => ''],

            ['name' => 'Lê Thị Phương', 'days' => 0.5, 'date' => '12/03/2026', 'position' => 'Trưởng phòng kinh doanh', 'dept' => 'Sale', 'note' => ''],
            ['name' => 'Lê Thị Phương', 'days' => 0.5, 'date' => '20/03/2026', 'position' => 'Trưởng phòng kinh doanh', 'dept' => 'Sale', 'note' => ''],
            ['name' => 'Lê Thị Phương', 'days' => 1.0, 'date' => '23/03/2026', 'position' => 'Trưởng phòng kinh doanh', 'dept' => 'Sale', 'note' => ''],
            ['name' => 'Lê Thị Phương', 'days' => 0.5, 'date' => '10/04/2026', 'position' => 'Nhân viên', 'dept' => 'Khối văn phòng', 'note' => ''],
            ['name' => 'Lê Thị Phương', 'days' => 0.5, 'date' => '29/04/2026', 'position' => 'Trưởng phòng kinh doanh', 'dept' => 'Sale', 'note' => ''],
            ['name' => 'Lê Thị Phương', 'days' => 0.5, 'date' => '06/05/2026', 'position' => 'Nhân viên', 'dept' => 'Khối văn phòng', 'note' => ''],
            ['name' => 'Lê Thị Phương', 'days' => 0.5, 'date' => '28/05/2026', 'position' => 'Nhân viên', 'dept' => 'Khối văn phòng', 'note' => ''],
            ['name' => 'Lê Thị Phương', 'days' => 0.5, 'date' => '17/06/2026', 'position' => 'Nhân viên', 'dept' => 'Khối văn phòng', 'note' => ''],
            ['name' => 'Lê Thị Phương', 'days' => 0.5, 'date' => '03/07/2026', 'position' => 'Nhân viên', 'dept' => 'Khối văn phòng', 'note' => ''],
            ['name' => 'Lê Thị Phương', 'days' => 0.5, 'date' => '09/07/2026', 'position' => 'Nhân viên', 'dept' => 'Khối văn phòng', 'note' => ''],
            ['name' => 'Lê Thị Phương', 'days' => 1.5, 'date' => '16/07/2026', 'position' => 'Trưởng phòng kinh doanh', 'dept' => 'Sale', 'note' => ''],
            ['name' => 'Lê Thị Phương', 'days' => 0.5, 'date' => '21/08/2026', 'position' => 'Trưởng phòng kinh doanh', 'dept' => 'Sale', 'note' => ''],
            ['name' => 'Lê Thị Phương', 'days' => 0.5, 'date' => '15/09/2026', 'position' => 'Nhân viên', 'dept' => 'Khối văn phòng', 'note' => ''],
            ['name' => 'Lê Thị Phương', 'days' => 0.5, 'date' => '18/09/2026', 'position' => 'Nhân viên', 'dept' => 'Khối văn phòng', 'note' => ''],
            ['name' => 'Lê Thị Phương', 'days' => 0.5, 'date' => '05/10/2026', 'position' => 'Nhân viên', 'dept' => 'Khối văn phòng', 'note' => ''],
            ['name' => 'Lê Thị Phương', 'days' => 0.5, 'date' => '12/10/2026', 'position' => 'Nhân viên', 'dept' => 'Khối văn phòng', 'note' => ''],
            ['name' => 'Lê Thị Phương', 'days' => 0.5, 'date' => '16/10/2026', 'position' => 'Nhân viên', 'dept' => 'Khối văn phòng', 'note' => ''],

            ['name' => 'Lê Thu Hà', 'days' => 1.0, 'date' => '29/04/2026', 'position' => 'Trưởng phòng kinh doanh', 'dept' => 'Sale', 'note' => ''],
            ['name' => 'Lê Thu Hà', 'days' => 1.5, 'date' => '04/05/2026', 'position' => 'Trưởng phòng kinh doanh', 'dept' => 'Sale', 'note' => ''],
            ['name' => 'Lê Thu Hà', 'days' => 0.5, 'date' => '22/05/2026', 'position' => 'Nhân viên', 'dept' => 'Sale', 'note' => ''],
            ['name' => 'Lê Thu Hà', 'days' => 1.0, 'date' => '17/06/2026', 'position' => 'Nhân viên', 'dept' => 'Sale', 'note' => ''],
            ['name' => 'Lê Thu Hà', 'days' => 1.0, 'date' => '08/07/2026', 'position' => 'Nhân viên', 'dept' => 'Sale', 'note' => ''],
            ['name' => 'Lê Thu Hà', 'days' => 1.0, 'date' => '20/08/2026', 'position' => 'Nhân viên', 'dept' => 'Sale', 'note' => ''],

            ['name' => 'Lê Thùy Dương', 'days' => 0.5, 'date' => '08/07/2026', 'position' => 'Trưởng phòng kinh doanh', 'dept' => 'Sale', 'note' => ''],

            ['name' => 'Ngô Duy Khánh', 'days' => 0.5, 'date' => '26/02/2026', 'position' => 'Nhân viên', 'dept' => 'Đào tạo', 'note' => ''],
            ['name' => 'Ngô Duy Khánh', 'days' => 0.5, 'date' => '25/03/2026', 'position' => 'Nhân viên', 'dept' => 'Đào tạo', 'note' => ''],
            ['name' => 'Ngô Duy Khánh', 'days' => 0.5, 'date' => '14/04/2026', 'position' => 'Nhân viên', 'dept' => 'Đào tạo', 'note' => ''],

            ['name' => 'Ngô Thế Đức', 'days' => 1.0, 'date' => '24/04/2026', 'position' => 'Trưởng phòng kinh doanh', 'dept' => 'Sale', 'note' => ''],
            ['name' => 'Ngô Thế Đức', 'days' => 0.5, 'date' => '10/07/2026', 'position' => 'Trưởng phòng kinh doanh', 'dept' => 'Sale', 'note' => ''],
            ['name' => 'Ngô Thế Đức', 'days' => 0.5, 'date' => '14/10/2026', 'position' => 'Trưởng phòng kinh doanh', 'dept' => 'Sale', 'note' => ''],
            ['name' => 'Ngô Thế Đức', 'days' => 0.5, 'date' => '15/10/2026', 'position' => 'Trưởng phòng kinh doanh', 'dept' => 'Sale', 'note' => ''],

            ['name' => 'Ngô Thị Diệp', 'days' => 1.0, 'date' => '05/01/2026', 'position' => 'CSKH', 'dept' => 'Khối văn phòng', 'note' => ''],
            ['name' => 'Ngô Thị Diệp', 'days' => 0.5, 'date' => '06/01/2026', 'position' => 'CSKH', 'dept' => 'Khối văn phòng', 'note' => ''],
            ['name' => 'Ngô Thị Diệp', 'days' => 1.0, 'date' => '30/03/2026', 'position' => 'CSKH', 'dept' => 'Khối văn phòng', 'note' => ''],
            ['name' => 'Ngô Thị Diệp', 'days' => 0.5, 'date' => '31/03/2026', 'position' => 'CSKH', 'dept' => 'Khối văn phòng', 'note' => ''],
            ['name' => 'Ngô Thị Diệp', 'days' => 0.5, 'date' => '03/04/2026', 'position' => 'CSKH', 'dept' => 'Khối văn phòng', 'note' => ''],
            ['name' => 'Ngô Thị Diệp', 'days' => 0.5, 'date' => '26/05/2026', 'position' => 'CSKH', 'dept' => 'Khối văn phòng', 'note' => ''],
            ['name' => 'Ngô Thị Diệp', 'days' => 1.0, 'date' => '20/07/2026', 'position' => 'Nhân viên', 'dept' => 'Khối văn phòng', 'note' => ''],
            ['name' => 'Ngô Thị Diệp', 'days' => 1.0, 'date' => '31/07/2026', 'position' => 'Nhân viên', 'dept' => 'Khối văn phòng', 'note' => ''],
            ['name' => 'Ngô Thị Diệp', 'days' => 1.0, 'date' => '07/08/2026', 'position' => 'Nhân viên', 'dept' => 'Khối văn phòng', 'note' => ''],
            ['name' => 'Ngô Thị Diệp', 'days' => 0.5, 'date' => '12/08/2026', 'position' => 'CSKH', 'dept' => 'Khối văn phòng', 'note' => ''],
            ['name' => 'Ngô Thị Diệp', 'days' => 1.0, 'date' => '24/09/2026', 'position' => 'CSKH', 'dept' => 'Khối văn phòng', 'note' => ''],
            ['name' => 'Ngô Thị Diệp', 'days' => 1.0, 'date' => '16/10/2026', 'position' => 'Nhân viên', 'dept' => 'Khối văn phòng', 'note' => ''],

            ['name' => 'Nguyễn Hải Yến', 'days' => 0.5, 'date' => '28/01/2026', 'position' => 'Trưởng phòng kinh doanh', 'dept' => 'Sale', 'note' => ''],
            ['name' => 'Nguyễn Hải Yến', 'days' => 0.5, 'date' => '23/04/2026', 'position' => 'Trưởng phòng kinh doanh', 'dept' => 'Sale', 'note' => ''],
            ['name' => 'Nguyễn Hải Yến', 'days' => 0.5, 'date' => '04/06/2026', 'position' => 'Trưởng phòng kinh doanh', 'dept' => 'Sale', 'note' => ''],
            ['name' => 'Nguyễn Hải Yến', 'days' => 1.0, 'date' => '31/07/2026', 'position' => 'Trưởng phòng kinh doanh', 'dept' => 'Sale', 'note' => ''],

            ['name' => 'Nguyễn Hồng Quân', 'days' => 0.5, 'date' => '02/02/2026', 'position' => 'Nhân viên', 'dept' => 'Đào tạo', 'note' => ''],
            ['name' => 'Nguyễn Hồng Quân', 'days' => 1.0, 'date' => '06/02/2026', 'position' => 'Nhân viên', 'dept' => 'Đào tạo', 'note' => ''],
            ['name' => 'Nguyễn Hồng Quân', 'days' => 0.5, 'date' => '11/02/2026', 'position' => 'Nhân viên', 'dept' => 'Đào tạo', 'note' => ''],
            ['name' => 'Nguyễn Hồng Quân', 'days' => 0.5, 'date' => '04/03/2026', 'position' => 'Nhân viên', 'dept' => 'Đào tạo', 'note' => ''],
            ['name' => 'Nguyễn Hồng Quân', 'days' => 0.5, 'date' => '03/03/2026', 'position' => 'Nhân viên', 'dept' => 'Đào tạo', 'note' => ''],
            ['name' => 'Nguyễn Hồng Quân', 'days' => 0.5, 'date' => '05/03/2026', 'position' => 'Nhân viên', 'dept' => 'Đào tạo', 'note' => ''],
            ['name' => 'Nguyễn Hồng Quân', 'days' => 1.0, 'date' => '06/03/2026', 'position' => 'Nhân viên', 'dept' => 'Đào tạo', 'note' => ''],
            ['name' => 'Nguyễn Hồng Quân', 'days' => 0.5, 'date' => '12/03/2026', 'position' => 'Nhân viên', 'dept' => 'Đào tạo', 'note' => ''],
            ['name' => 'Nguyễn Hồng Quân', 'days' => 1.0, 'date' => '13/03/2026', 'position' => 'Nhân viên', 'dept' => 'Đào tạo', 'note' => ''],

            ['name' => 'Nguyễn Huy Hảo', 'days' => 0.5, 'date' => '04/06/2026', 'position' => 'Nhân viên', 'dept' => 'Marketing', 'note' => 'Đã nghỉ'],
            ['name' => 'Nguyễn Huy Hảo', 'days' => 1.0, 'date' => '11/06/2026', 'position' => 'Nhân viên', 'dept' => 'Marketing', 'note' => ''],
            ['name' => 'Nguyễn Huy Hảo', 'days' => 0.5, 'date' => '17/07/2026', 'position' => 'Nhân viên', 'dept' => 'Marketing', 'note' => ''],
            ['name' => 'Nguyễn Huy Hảo', 'days' => 1.5, 'date' => '12/08/2026', 'position' => 'Nhân viên', 'dept' => 'Marketing', 'note' => ''],

            ['name' => 'Nguyễn Ngọc Mai', 'days' => 1.0, 'date' => '26/06/2026', 'position' => 'Nhân viên', 'dept' => 'Khối văn phòng', 'note' => 'Đã nghỉ T9'],
            ['name' => 'Nguyễn Ngọc Mai', 'days' => 1.0, 'date' => '17/07/2026', 'position' => 'Nhân viên', 'dept' => 'Khối văn phòng', 'note' => ''],
            ['name' => 'Nguyễn Ngọc Mai', 'days' => 0.5, 'date' => '13/08/2026', 'position' => 'Nhân viên', 'dept' => 'Khối văn phòng', 'note' => ''],
            ['name' => 'Nguyễn Ngọc Mai', 'days' => 1.0, 'date' => '21/08/2026', 'position' => 'Nhân viên', 'dept' => 'Khối văn phòng', 'note' => ''],

            ['name' => 'Nguyễn Quang Huy', 'days' => 1.0, 'date' => '27/03/2026', 'position' => 'Trưởng phòng kinh doanh', 'dept' => 'Sale', 'note' => ''],
            ['name' => 'Nguyễn Quang Huy', 'days' => 0.5, 'date' => '31/07/2026', 'position' => 'Trưởng phòng kinh doanh', 'dept' => 'Sale', 'note' => ''],
            ['name' => 'Nguyễn Quang Huy', 'days' => 1.0, 'date' => '21/09/2026', 'position' => 'Trưởng phòng kinh doanh', 'dept' => 'Sale', 'note' => '21/09'],

            ['name' => 'Nguyễn Thanh Hà', 'days' => 1.0, 'date' => '13/03/2026', 'position' => 'Nhân viên', 'dept' => 'Sale', 'note' => ''],
            ['name' => 'Nguyễn Thanh Hà', 'days' => 1.5, 'date' => '15/04/2026', 'position' => 'Nhân viên', 'dept' => 'Sale', 'note' => ''],
            ['name' => 'Nguyễn Thanh Hà', 'days' => 1.0, 'date' => '02/06/2026', 'position' => 'Nhân viên', 'dept' => 'Sale', 'note' => ''],
            ['name' => 'Nguyễn Thanh Hà', 'days' => 1.0, 'date' => '06/12/2026', 'position' => 'Nhân viên', 'dept' => 'Sale', 'note' => ''],
            ['name' => 'Nguyễn Thanh Hà', 'days' => 1.0, 'date' => '23/06/2026', 'position' => 'Nhân viên', 'dept' => 'Sale', 'note' => ''],
            ['name' => 'Nguyễn Thanh Hà', 'days' => 1.0, 'date' => '24/06/2026', 'position' => 'Nhân viên', 'dept' => 'Sale', 'note' => ''],
            ['name' => 'Nguyễn Thanh Hà', 'days' => 1.0, 'date' => '30/06/2026', 'position' => 'Nhân viên', 'dept' => 'Sale', 'note' => ''],
            ['name' => 'Nguyễn Thanh Hà', 'days' => 1.0, 'date' => '03/07/2026', 'position' => 'Nhân viên', 'dept' => 'Sale', 'note' => ''],
            ['name' => 'Nguyễn Thanh Hà', 'days' => 0.5, 'date' => '28/07/2026', 'position' => 'Nhân viên', 'dept' => 'Sale', 'note' => ''],
            ['name' => 'Nguyễn Thanh Hà', 'days' => 1.0, 'date' => '07/08/2026', 'position' => 'Nhân viên', 'dept' => 'Sale', 'note' => ''],
            ['name' => 'Nguyễn Thanh Hà', 'days' => 1.0, 'date' => '10/08/2026', 'position' => 'Nhân viên', 'dept' => 'Sale', 'note' => ''],
            ['name' => 'Nguyễn Thanh Hà', 'days' => 1.0, 'date' => '11/08/2026', 'position' => 'Nhân viên', 'dept' => 'Sale', 'note' => ''],
            ['name' => 'Nguyễn Thanh Hà', 'days' => 2.0, 'date' => '19/08/2026', 'position' => 'Nhân viên', 'dept' => 'Sale', 'note' => ''],
            ['name' => 'Nguyễn Thanh Hà', 'days' => 2.0, 'date' => '24/08/2026', 'position' => 'Nhân viên', 'dept' => 'Sale', 'note' => ''],
            ['name' => 'Nguyễn Thanh Hà', 'days' => 1.0, 'date' => '26/08/2026', 'position' => 'Nhân viên', 'dept' => 'Sale', 'note' => ''],

            ['name' => 'Nguyễn Thị Hồng Vân', 'days' => 1.0, 'date' => '05/03/2026', 'position' => 'Nhân viên', 'dept' => 'Sale', 'note' => ''],
            ['name' => 'Nguyễn Thị Hồng Vân', 'days' => 1.0, 'date' => '07/04/2026', 'position' => 'Nhân viên', 'dept' => 'Sale', 'note' => ''],
            ['name' => 'Nguyễn Thị Hồng Vân', 'days' => 2.0, 'date' => '21/05/2026', 'position' => 'Nhân viên', 'dept' => 'Sale', 'note' => ''],
            ['name' => 'Nguyễn Thị Hồng Vân', 'days' => 1.0, 'date' => '17/06/2026', 'position' => 'Nhân viên', 'dept' => 'Khối văn phòng', 'note' => ''],
            ['name' => 'Nguyễn Thị Hồng Vân', 'days' => 1.0, 'date' => '03/07/2026', 'position' => 'Nhân viên', 'dept' => 'Sale', 'note' => ''],
            ['name' => 'Nguyễn Thị Hồng Vân', 'days' => 0.5, 'date' => '29/07/2026', 'position' => 'Nhân viên', 'dept' => 'Sale', 'note' => ''],
            ['name' => 'Nguyễn Thị Hồng Vân', 'days' => 1.0, 'date' => '23/09/2026', 'position' => 'Nhân viên', 'dept' => 'Sale', 'note' => ''],
            ['name' => 'Nguyễn Thị Hồng Vân', 'days' => 1.5, 'date' => '04/09/2026', 'position' => 'Nhân viên', 'dept' => 'Sale', 'note' => ''],
            ['name' => 'Nguyễn Thị Hồng Vân', 'days' => 0.5, 'date' => '09/10/2026', 'position' => 'Nhân viên', 'dept' => 'Sale', 'note' => ''],
            ['name' => 'Nguyễn Thị Hồng Vân', 'days' => 1.0, 'date' => '20/10/2026', 'position' => 'Nhân viên', 'dept' => 'Sale', 'note' => ''],

            ['name' => 'Nguyễn Thị Kim Thoa', 'days' => 1.0, 'date' => '04/05/2026', 'position' => 'Nhân viên', 'dept' => 'Khối văn phòng', 'note' => ''],
            ['name' => 'Nguyễn Thị Kim Thoa', 'days' => 0.5, 'date' => '07/05/2026', 'position' => 'Nhân viên', 'dept' => 'Khối văn phòng', 'note' => ''],
            ['name' => 'Nguyễn Thị Kim Thoa', 'days' => 2.0, 'date' => '13/05/2026', 'position' => 'Nhân viên', 'dept' => 'Khối văn phòng', 'note' => ''],
            ['name' => 'Nguyễn Thị Kim Thoa', 'days' => 0.5, 'date' => '27/05/2026', 'position' => 'Nhân viên', 'dept' => 'Khối văn phòng', 'note' => ''],
            ['name' => 'Nguyễn Thị Kim Thoa', 'days' => 1.0, 'date' => '01/06/2026', 'position' => 'Nhân viên', 'dept' => 'Khối văn phòng', 'note' => ''],
            ['name' => 'Nguyễn Thị Kim Thoa', 'days' => 0.5, 'date' => '02/06/2026', 'position' => 'Nhân viên', 'dept' => 'Khối văn phòng', 'note' => ''],
            ['name' => 'Nguyễn Thị Kim Thoa', 'days' => 1.0, 'date' => '16/07/2026', 'position' => 'Nhân viên', 'dept' => 'Khối văn phòng', 'note' => ''],
            ['name' => 'Nguyễn Thị Kim Thoa', 'days' => 0.5, 'date' => '24/07/2026', 'position' => 'Nhân viên', 'dept' => 'Khối văn phòng', 'note' => ''],
            ['name' => 'Nguyễn Thị Kim Thoa', 'days' => 1.0, 'date' => '29/07/2026', 'position' => 'Nhân viên', 'dept' => 'Khối văn phòng', 'note' => ''],
            ['name' => 'Nguyễn Thị Kim Thoa', 'days' => 0.5, 'date' => '26/08/2026', 'position' => 'Nhân viên', 'dept' => 'Khối văn phòng', 'note' => ''],

            ['name' => 'Nguyễn Thị Mai', 'days' => 1.0, 'date' => '06/04/2026', 'position' => 'Kế toán', 'dept' => 'Khối văn phòng', 'note' => ''],

            ['name' => 'Nguyễn Thị Nhài', 'days' => 2.0, 'date' => '12/01/2026', 'position' => 'CSKH', 'dept' => 'Khối văn phòng', 'note' => ''],
            ['name' => 'Nguyễn Thị Nhài', 'days' => 1.0, 'date' => '14/01/2026', 'position' => 'CSKH', 'dept' => 'Khối văn phòng', 'note' => ''],
            ['name' => 'Nguyễn Thị Nhài', 'days' => 1.0, 'date' => '29/06/2026', 'position' => 'CSKH', 'dept' => 'Khối văn phòng', 'note' => ''],

            ['name' => 'Nguyễn Thị Thu Phương', 'days' => 1.0, 'date' => '21/07/2026', 'position' => 'Trưởng phòng kinh doanh', 'dept' => 'Sale', 'note' => ''],
            ['name' => 'Nguyễn Thị Thu Phương', 'days' => 1.0, 'date' => '31/07/2026', 'position' => 'Trưởng phòng kinh doanh', 'dept' => 'Sale', 'note' => ''],

            ['name' => 'Nguyễn Văn Đức', 'days' => 2.0, 'date' => '08/07/2026', 'position' => 'Nhân viên', 'dept' => 'Sale', 'note' => ''],

            ['name' => 'Nguyễn Văn Tùng', 'days' => 1.0, 'date' => '25/03/2026', 'position' => 'Trưởng phòng kinh doanh', 'dept' => 'Sale', 'note' => ''],
            ['name' => 'Nguyễn Văn Tùng', 'days' => 0.5, 'date' => '14/04/2026', 'position' => 'Trưởng phòng kinh doanh', 'dept' => 'Sale', 'note' => ''],
            ['name' => 'Nguyễn Văn Tùng', 'days' => 1.0, 'date' => '11/05/2026', 'position' => 'Trưởng phòng kinh doanh', 'dept' => 'Sale', 'note' => ''],

            ['name' => 'Nguyễn Việt Thắng', 'days' => 1.0, 'date' => '13/03/2026', 'position' => 'Nhân viên', 'dept' => 'Đào tạo', 'note' => ''],
            ['name' => 'Nguyễn Việt Thắng', 'days' => 1.0, 'date' => '20/04/2026', 'position' => 'Nhân viên', 'dept' => 'Đào tạo', 'note' => ''],
            ['name' => 'Nguyễn Việt Thắng', 'days' => 1.0, 'date' => '29/04/2026', 'position' => 'Nhân viên', 'dept' => 'Đào tạo', 'note' => ''],
            ['name' => 'Nguyễn Việt Thắng', 'days' => 0.5, 'date' => '15/05/2026', 'position' => 'Nhân viên', 'dept' => 'Đào tạo', 'note' => ''],
            ['name' => 'Nguyễn Việt Thắng', 'days' => 1.0, 'date' => '18/06/2026', 'position' => 'Nhân viên', 'dept' => 'Đào tạo', 'note' => ''],
            ['name' => 'Nguyễn Việt Thắng', 'days' => 0.5, 'date' => '09/07/2026', 'position' => 'Nhân viên', 'dept' => 'Đào tạo', 'note' => ''],
            ['name' => 'Nguyễn Việt Thắng', 'days' => 1.0, 'date' => '24/07/2026', 'position' => 'Nhân viên', 'dept' => 'Đào tạo', 'note' => ''],
            ['name' => 'Nguyễn Việt Thắng', 'days' => 0.5, 'date' => '14/08/2026', 'position' => 'Nhân viên', 'dept' => 'Đào tạo', 'note' => ''],
            ['name' => 'Nguyễn Việt Thắng', 'days' => 0.5, 'date' => '08/09/2026', 'position' => 'Nhân viên', 'dept' => 'Đào tạo', 'note' => ''],

            ['name' => 'Nguyễn Vũ Diễm Quỳnh', 'days' => 2.0, 'date' => '29/06/2026', 'position' => 'Trưởng phòng kinh doanh', 'dept' => 'Sale', 'note' => ''],
            ['name' => 'Nguyễn Vũ Diễm Quỳnh', 'days' => 5.0, 'date' => '10/08/2026', 'position' => 'Trưởng phòng kinh doanh', 'dept' => 'Marketing', 'note' => ''],
            ['name' => 'Nguyễn Vũ Diễm Quỳnh', 'days' => 1.0, 'date' => '20/08/2026', 'position' => 'Trưởng phòng kinh doanh', 'dept' => 'Sale', 'note' => ''],
            ['name' => 'Nguyễn Vũ Diễm Quỳnh', 'days' => 1.0, 'date' => '03/09/2026', 'position' => 'Trưởng phòng kinh doanh', 'dept' => 'Marketing', 'note' => ''],

            ['name' => 'Nguyễn Xuân Đạt', 'days' => 0.5, 'date' => '06/04/2026', 'position' => 'Trưởng phòng kinh doanh', 'dept' => 'Marketing', 'note' => ''],
            ['name' => 'Nguyễn Xuân Đạt', 'days' => 1.0, 'date' => '17/04/2026', 'position' => 'Trưởng phòng kinh doanh', 'dept' => 'Marketing', 'note' => ''],
            ['name' => 'Nguyễn Xuân Đạt', 'days' => 0.5, 'date' => '05/06/2026', 'position' => 'Trưởng phòng kinh doanh', 'dept' => 'Marketing', 'note' => ''],
            ['name' => 'Nguyễn Xuân Đạt', 'days' => 1.0, 'date' => '06/08/2026', 'position' => 'Trưởng phòng kinh doanh', 'dept' => 'Marketing', 'note' => ''],
            ['name' => 'Nguyễn Xuân Đạt', 'days' => 0.5, 'date' => '18/08/2026', 'position' => 'Trưởng phòng kinh doanh', 'dept' => 'Marketing', 'note' => ''],
            ['name' => 'Nguyễn Xuân Đạt', 'days' => 0.5, 'date' => '21/09/2026', 'position' => 'Trưởng phòng kinh doanh', 'dept' => 'Marketing', 'note' => ''],

            ['name' => 'Phạm Quang Minh', 'days' => 2.0, 'date' => '24/03/2026', 'position' => 'Quản lý trợ giảng', 'dept' => 'Đào tạo', 'note' => ''],
            ['name' => 'Phạm Quang Minh', 'days' => 1.0, 'date' => '17/06/2026', 'position' => 'Quản lý trợ giảng', 'dept' => 'Đào tạo', 'note' => ''],
            // 16,17,18,21/09: 4 ngày (16, 17, 18/09 và 21/09)
            ['name' => 'Phạm Quang Minh', 'days' => 3.0, 'date' => '16/09/2026 - 18/09/2026', 'position' => 'Quản lý trợ giảng', 'dept' => 'Đào tạo', 'note' => '16,17,18/09 trong đợt 4 ngày'],
            ['name' => 'Phạm Quang Minh', 'days' => 1.0, 'date' => '21/09/2026', 'position' => 'Quản lý trợ giảng', 'dept' => 'Đào tạo', 'note' => '21/09 trong đợt 4 ngày'],
            ['name' => 'Phạm Quang Minh', 'days' => 0.5, 'date' => '11/09/2026', 'position' => 'Quản lý trợ giảng', 'dept' => 'Đào tạo', 'note' => ''],

            ['name' => 'Phạm Thị Thanh Nga', 'days' => 0.5, 'date' => '03/09/2026', 'position' => 'Nhân viên', 'dept' => 'Sale', 'note' => ''],
            ['name' => 'Phạm Thị Thanh Nga', 'days' => 0.5, 'date' => '13/03/2026', 'position' => 'Nhân viên', 'dept' => 'Sale', 'note' => ''],
            ['name' => 'Phạm Thị Thanh Nga', 'days' => 0.5, 'date' => '29/04/2026', 'position' => 'Nhân viên', 'dept' => 'Sale', 'note' => ''],
            ['name' => 'Phạm Thị Thanh Nga', 'days' => 0.5, 'date' => '28/05/2026', 'position' => 'Nhân viên', 'dept' => 'Sale', 'note' => ''],
            ['name' => 'Phạm Thị Thanh Nga', 'days' => 0.5, 'date' => '11/06/2026', 'position' => 'Nhân viên', 'dept' => 'Sale', 'note' => '11/6/2026'],
            ['name' => 'Phạm Thị Thanh Nga', 'days' => 0.5, 'date' => '08/12/2026', 'position' => 'Nhân viên', 'dept' => 'Sale', 'note' => '8/12/2026'],
            ['name' => 'Phạm Thị Thanh Nga', 'days' => 1.0, 'date' => '19/08/2026', 'position' => 'Nhân viên', 'dept' => 'Sale', 'note' => ''],
            ['name' => 'Phạm Thị Thanh Nga', 'days' => 0.5, 'date' => '11/09/2026', 'position' => 'Nhân viên', 'dept' => 'Sale', 'note' => ''],

            ['name' => 'Phùng Thùy Dương', 'days' => 0.5, 'date' => '15/01/2026', 'position' => 'Trưởng phòng kinh doanh', 'dept' => 'Sale', 'note' => ''],
            ['name' => 'Phùng Thùy Dương', 'days' => 1.0, 'date' => '20/03/2026', 'position' => 'Trưởng phòng kinh doanh', 'dept' => 'Marketing', 'note' => ''],
            ['name' => 'Phùng Thùy Dương', 'days' => 0.5, 'date' => '17/07/2026', 'position' => 'Trưởng phòng kinh doanh', 'dept' => 'Sale', 'note' => ''],
            ['name' => 'Phùng Thùy Dương', 'days' => 1.0, 'date' => '29/07/2026', 'position' => 'Trưởng phòng kinh doanh', 'dept' => 'Sale', 'note' => ''],
            ['name' => 'Phùng Thùy Dương', 'days' => 0.5, 'date' => '06/08/2026', 'position' => 'Trưởng phòng kinh doanh', 'dept' => 'Sale', 'note' => '6/8/2026'],
            ['name' => 'Phùng Thùy Dương', 'days' => 0.5, 'date' => '17/08/2026', 'position' => 'Trưởng phòng kinh doanh', 'dept' => 'Sale', 'note' => ''],
            ['name' => 'Phùng Thùy Dương', 'days' => 1.0, 'date' => '28/04/2026', 'position' => 'Trưởng phòng kinh doanh', 'dept' => 'Marketing', 'note' => ''],
            ['name' => 'Phùng Thùy Dương', 'days' => 1.0, 'date' => '16/10/2026', 'position' => 'Trưởng phòng kinh doanh', 'dept' => 'Sale', 'note' => ''],

            ['name' => 'Thiều Thị Mỹ Huyền', 'days' => 0.5, 'date' => '06/02/2026', 'position' => 'Quản lý trợ giảng', 'dept' => 'Đào tạo', 'note' => '6/2/2026'],
            ['name' => 'Thiều Thị Mỹ Huyền', 'days' => 3.0, 'date' => '25/03/2026', 'position' => 'Quản lý trợ giảng', 'dept' => 'Đào tạo', 'note' => ''],
            ['name' => 'Thiều Thị Mỹ Huyền', 'days' => 2.0, 'date' => '30/03/2026', 'position' => 'Quản lý trợ giảng', 'dept' => 'Đào tạo', 'note' => ''],
            ['name' => 'Thiều Thị Mỹ Huyền', 'days' => 1.0, 'date' => '06/05/2026', 'position' => 'Quản lý trợ giảng', 'dept' => 'Đào tạo', 'note' => '6/5/2026'],
            ['name' => 'Thiều Thị Mỹ Huyền', 'days' => 1.0, 'date' => '27/10/2026', 'position' => 'Quản lý trợ giảng', 'dept' => 'Đào tạo', 'note' => ''],

            ['name' => 'Trần Quốc Tiến', 'days' => 1.0, 'date' => '02/02/2026', 'position' => 'Nhân viên', 'dept' => 'Khối văn phòng', 'note' => '2/2/2026'],
            ['name' => 'Trần Quốc Tiến', 'days' => 1.0, 'date' => '02/03/2026', 'position' => 'Nhân viên', 'dept' => 'Khối văn phòng', 'note' => '2/3/2026'],
            ['name' => 'Trần Quốc Tiến', 'days' => 1.0, 'date' => '09/03/2026', 'position' => 'Nhân viên', 'dept' => 'Khối văn phòng', 'note' => '9/3/2026'],
            ['name' => 'Trần Quốc Tiến', 'days' => 1.0, 'date' => '24/06/2026', 'position' => 'Nhân viên', 'dept' => 'Khối văn phòng', 'note' => ''],
            ['name' => 'Trần Quốc Tiến', 'days' => 1.0, 'date' => '21/08/2026', 'position' => 'Nhân viên', 'dept' => 'Khối văn phòng', 'note' => ''],
            ['name' => 'Trần Quốc Tiến', 'days' => 2.0, 'date' => '04/09/2026', 'position' => 'Nhân viên', 'dept' => 'Khối văn phòng', 'note' => ''],
            ['name' => 'Trần Quốc Tiến', 'days' => 0.5, 'date' => '08/09/2026', 'position' => 'Nhân viên', 'dept' => 'Khối văn phòng', 'note' => 'Gửi đơn muộn có trừ lương'],

            ['name' => 'Trần Thị Thu Hạ', 'days' => 0.5, 'date' => '06/03/2026', 'position' => 'Nhân viên', 'dept' => 'Đào tạo', 'note' => '6/3/2026'],
            ['name' => 'Trần Thị Thu Hạ', 'days' => 1.0, 'date' => '25/08/2026', 'position' => 'Nhân viên', 'dept' => 'Đào tạo', 'note' => ''],
            ['name' => 'Trần Thị Thu Hạ', 'days' => 1.0, 'date' => '02/10/2026', 'position' => 'Nhân viên', 'dept' => 'Đào tạo', 'note' => ''],
            ['name' => 'Trần Thị Thu Hạ', 'days' => 0.5, 'date' => '12/10/2026', 'position' => 'Nhân viên', 'dept' => 'Đào tạo', 'note' => ''],

            ['name' => 'Vũ Minh Nguyệt', 'days' => 1.0, 'date' => '10/08/2026', 'position' => 'Nhân viên', 'dept' => 'Sale', 'note' => '10/8/2026'],
            ['name' => 'Vũ Minh Nguyệt', 'days' => 0.5, 'date' => '15/10/2026', 'position' => 'Nhân viên', 'dept' => 'Sale', 'note' => ''],

            ['name' => 'Vũ Thị Giang', 'days' => 1.0, 'date' => '05/03/2026', 'position' => 'Quản lý trợ giảng', 'dept' => 'Đào tạo', 'note' => 'Gốc gõ nhầm 2006 -> chuẩn hóa 2026'],
            ['name' => 'Vũ Thị Giang', 'days' => 1.0, 'date' => '01/01/2026', 'position' => 'Quản lý trợ giảng', 'dept' => 'Đào tạo', 'note' => 'Gốc gõ nhầm 2006 -> chuẩn hóa 2026'],
            ['name' => 'Vũ Thị Giang', 'days' => 1.0, 'date' => '30/09/2026', 'position' => 'Quản lý trợ giảng', 'dept' => 'Đào tạo', 'note' => '30/09'],
            ['name' => 'Vũ Thị Giang', 'days' => 0.5, 'date' => '01/10/2026', 'position' => 'Quản lý trợ giảng', 'dept' => 'Đào tạo', 'note' => '1/10/2026'],
            ['name' => 'Vũ Thị Giang', 'days' => 0.5, 'date' => '26/10/2026', 'position' => 'Quản lý trợ giảng', 'dept' => 'Đào tạo', 'note' => ''],

            ['name' => 'Phạm Thị Phương Nga', 'days' => 0.5, 'date' => '26/10/2026', 'position' => 'Nhân viên', 'dept' => 'Marketing', 'note' => ''],
            ['name' => 'Hoàng Xuân Phong', 'days' => 1.0, 'date' => '28/10/2026', 'position' => 'Nhân viên', 'dept' => 'Sale', 'note' => ''],
        ];

        // Mapping phòng ban
        $deptMap = [
            'Sale' => 5, // Phòng Sales
            'Đào tạo' => 7, // Khối Giáo viên
            'Marketing' => 9, // Khối Marketing
            'Khối văn phòng' => 6, // Khối Văn phòng
        ];

        DB::beginTransaction();
        try {
            // Group by employee name
            $grouped = [];
            foreach ($dataRows as $row) {
                $cleanName = \Normalizer::normalize(trim($row['name']), \Normalizer::FORM_C);
                $cleanName = mb_convert_case(preg_replace('/\s+/', ' ', $cleanName), MB_CASE_TITLE, "UTF-8");
                if (!isset($grouped[$cleanName])) {
                    $grouped[$cleanName] = [
                        'name' => $cleanName,
                        'position' => $row['position'],
                        'dept' => $row['dept'],
                        'note' => $row['note'],
                        'rows' => []
                    ];
                }
                $grouped[$cleanName]['rows'][] = $row;
            }

            $this->info("Tổng số nhân sự cần đồng bộ: " . count($grouped));

            foreach ($grouped as $empName => $empData) {
                // 1. Tìm hoặc tạo HrmEmployeeProfile
                $profile = HrmEmployeeProfile::where('full_name', $empName)
                    ->orWhere('full_name', 'like', '%' . $empName . '%')
                    ->first();

                // Tìm User tương ứng
                $user = User::where('name', $empName)
                    ->orWhere('name', 'like', '%' . $empName . '%')
                    ->first();

                $deptId = $deptMap[$empData['dept']] ?? 6;

                if (!$profile) {
                    $profile = new HrmEmployeeProfile();
                    $profile->employee_code = HrmEmployeeProfile::generateNextEmployeeCode();
                    $profile->full_name = $empName;
                    $profile->department_id = $deptId;
                    $profile->user_id = $user ? $user->id : null;
                    $profile->work_status = (strpos($empData['note'], 'Đã nghỉ') !== false) ? 'resigned' : 'working';
                    $profile->save();
                    $this->line(" [TẠO MỚI PROFILE] {$empName} (Mã: {$profile->employee_code})");
                } else {
                    if (!$profile->department_id) {
                        $profile->department_id = $deptId;
                    }
                    if (!$profile->user_id && $user) {
                        $profile->user_id = $user->id;
                    }
                    $profile->save();
                }

                $totalDays = 0;

                // 2. Tạo đơn nghỉ phép HrmLeaveRequest cho từng dòng
                foreach ($empData['rows'] as $item) {
                    $days = (float)$item['days'];
                    $totalDays += $days;

                    // Parse start_date và end_date
                    $dateStr = $item['date'];
                    if (strpos($dateStr, '-') !== false) {
                        $dParts = explode('-', $dateStr);
                        $start = Carbon::createFromFormat('d/m/Y', trim($dParts[0]))->format('Y-m-d');
                        $end = Carbon::createFromFormat('d/m/Y', trim($dParts[1]))->format('Y-m-d');
                    } else {
                        $start = Carbon::createFromFormat('d/m/Y', trim($dateStr))->format('Y-m-d');
                        $end = $start;
                    }

                    $approvalType = 'approved';
                    if (strpos($item['note'], 'Gửi đơn muộn') !== false) {
                        $approvalType = 'late_unpaid';
                    }

                    // Tránh duplicate đơn nếu đã import
                    $existing = HrmLeaveRequest::where('employee_id', $profile->id)
                        ->where('start_date', $start)
                        ->where('end_date', $end)
                        ->where('duration_days', $days)
                        ->first();

                    if (!$existing) {
                        $req = new HrmLeaveRequest();
                        $req->employee_id = $profile->id;
                        $req->user_id = $profile->user_id ?: ($user ? $user->id : 1);
                        $req->leave_type_id = 1; // Phép năm
                        $req->type = 'vacation';
                        $req->request_category = 'leave';
                        $req->start_date = $start;
                        $req->end_date = $end;
                        $req->duration_days = $days;
                        $req->reason = $item['note'] ? $item['note'] : 'Nghỉ phép theo kế hoạch năm 2026';
                        $req->status = 1; // Đã duyệt
                        $req->approval_type = $approvalType;
                        $req->approved_by = 1;
                        $req->hr_approved_by = 1;
                        $req->save();
                    }
                }

                // 3. Cập nhật HrmLeaveBalance cho năm 2026
                $balance = HrmLeaveBalance::firstOrNew([
                    'employee_id' => $profile->id,
                    'year' => 2026,
                ]);
                $balance->user_id = $profile->user_id ?: ($user ? $user->id : null);
                $balance->leave_type_id = 1;
                $balance->total_days = 12.0; // Phép tiêu chuẩn
                $balance->used_days = $totalDays;
                $balance->save();

                $this->info(" -> {$empName}: Tổng nghỉ {$totalDays} ngày | Phép năm còn lại: " . max(0, 12 - $totalDays) . " ngày");
            }

            DB::commit();
            $this->info("=== IMPORT VÀ CẬP NHẬT HOÀN TẤT THÀNH CÔNG 100%! ===");
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("LỖI IMPORT: " . $e->getMessage() . "\n" . $e->getTraceAsString());
        }
    }
}
