<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

$data = [
    ['An Thùy', null],
    ['Anh Thư Meca', 'CT029'],
    ['Bắc Từ Liêm', null],
    ['Cầu Giấy 1', null],
    ['Cầu Giấy 2', null],
    ['Diễm Quỳnh Meca', 'CT041'],
    ['Dương', 'CT036'],
    ['Dương Meca', 'CT036'],
    ['Hà Nguyễn Meca', null],
    ['Hà Thiệp Meca', 'CT043'],
    ['Hương Trúc Meca', 'CT044'],
    ['Hải Anh Meca', 'CT042'],
    ['Hải Yến Meca', 'CT035'],
    ['Hồng Anh Meca', 'CT023'],
    ['Hồng Vân', 'CT026'],
    ['Lê Linh', null],
    ['Minh TD', 'CT030'],
    ['Mỹ Linh LP', 'CT047'],
    ['Nguyễn Yến Meca', 'CT052'],
    ['Nguyễn Ánh Meca', null],
    ['Nguyệt Hằng Meca', null],
    ['Nguyệt LP', 'CT048'],
    ['Nguồn Ngoài', 'CT033'],
    ['Nguồn ngoài', 'CT033'],
    ['Ngô Đức Meca', 'CT034'],
    ['Ngọc Anh Meca', 'CT050'],
    ['Ngọc Diệp Meca', null],
    ['Nhài xếp lớp. Phương báo kiểm tra', null],
    ['PAGE', null],
    ['Page', null],
    ['Quang Huy Meca', 'CT020'],
    ['Team Hiền', null],
    ['Team Hoàng', null],
    ['Team Huy Meca', null],
    ['Team Huyền', null],
    ['Team Hà Hiền', 'CT027'],
    ['Team Năm', null],
    ['Team P. Anh', null],
    ['Team Phương', 'CT031'],
    ['Team Toeic', null],
    ['Team Trung', null],
    ['Team Tú', null],
    ['Team Yến', null],
    ['Team Đinh Oanh', null],
    ['Team Đạt', 'CT022'],
    ['Team Đức MKT', null],
    ['Thanh Nga Meca', 'CT038'],
    ['Thu Hiền Meca', 'CT045'],
    ['Thu Hà Meca', 'CT037'],
    ['Thu Phương Meca', 'CT021'],
    ['Thuỳ Linh', null],
    ['Thái Bảo Meca', 'CT025'],
    ['Thùy Trang Meca', null],
    ['Thế Anh Meca', 'CT040'],
    ['Tiến Dũng Meca', 'CT028'],
    ['Tùng Meca', 'CT039'],
    ['Uyên LP', 'CT046'],
    ['Xuân Đạt Meca', 'CT024'],
    ['Xuân Đức Meca', null],
    ['Yến Nhi Meca', 'CT049'],
    ['Đinh Linh Meca', 'CT051'],
];

// Get max CT code
$max_ct = DB::table('users')->where('hrm_id', 'like', 'CT%')->select('hrm_id')->get()->map(function($user) {
    return (int) str_replace('CT', '', $user->hrm_id);
})->max();

if (!$max_ct) $max_ct = 0;

foreach ($data as $row) {
    if ($row[1] && strpos($row[1], 'CT') === 0) {
        $num = (int) str_replace('CT', '', $row[1]);
        if ($num > $max_ct) {
            $max_ct = $num;
        }
    }
}

foreach ($data as $row) {
    $name = $row[0];
    $code = $row[1];

    if ($code) {
        // Cập nhật tên vào tmp_name
        DB::table('users')->where('hrm_id', $code)->update([
            'tmp_name' => $name
        ]);
        echo "Updated $code with tmp_name $name\n";
    } else {
        // Thêm mới nhân viên
        $max_ct++;
        $new_code = 'CT' . str_pad($max_ct, 3, '0', STR_PAD_LEFT);
        
        $email = Str::slug($name) . '_' . $new_code . '@example.com';
        
        $user_id = DB::table('users')->insertGetId([
            'name' => $name,
            'tmp_name' => $name,
            'hrm_id' => $new_code,
            'email' => $email,
            'password' => bcrypt('123456'),
            'status' => 1,
            'menuroles' => 'user',
            'phone' => null,
            'manager_hrm_id' => null,
            'manager_id' => null,
            'sip_id' => null,
            'role_name' => 'Trưởng nhóm Kinh doanh',
            'branch_id' => 9,
            'branch_name' => 'CHAM TOEIC',
            'created_at' => now(),
            'updated_at' => now(),
            'creator_id' => 1,
        ]);
        
        // Insert role
        DB::table('role_has_user')->insert([
            'role_id' => 69,
            'user_id' => $user_id
        ]);
        
        // Insert branch_has_user
        DB::table('branch_has_user')->insert([
            'branch_id' => 9,
            'user_id' => $user_id
        ]);
        
        echo "Created new user $name with code $new_code\n";
    }
}

echo "Done\n";
