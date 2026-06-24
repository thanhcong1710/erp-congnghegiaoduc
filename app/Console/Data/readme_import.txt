* Cần xóa file global_skipped_combined.json trước khi chạy

php artisan erp:export-students-json app/Console/Data/import_3.xlsx  app/Console/Data/students_normalized_3.json
php artisan erp:import-students-json app/Console/Data/global_skipped_combined.json
php artisan erp:import-students-json app/Console/Data/students_normalized_4.json 


"Combo GT Lv(1+2+3)+ Toeic Lv(1+2)" => Đang trả về NULL (Chưa có ID trong DB)
"Combo GT Lv(1+2)+Toeic Lv(1+2)" => Đang trả về NULL
"Combo GT Lv1+2+3" => Đang trả về NULL
"Combo GT Lv3+ Toeic Lv(1+2)" => Đang trả về NULL
"Đội sale của Ngô Đức" => Trả về NULL (Bị nhập nhầm cột trên Excel)
"Combo GT Lv(1+2+3)+ Toeic Lv1" => Đang bị map nhầm thành 39 (Vì nó túm được chữ toeiclv1 ở cuối).
"Combo GT Lv(1+2)+Toeic Lv1"