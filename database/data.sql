/*
 Navicat Premium Data Transfer

 Source Server         : DB - google 34.143.225.66
 Source Server Type    : MySQL
 Source Server Version : 80040
 Source Host           : 34.143.225.66:3306
 Source Schema         : xgenius

 Target Server Type    : MySQL
 Target Server Version : 80040
 File Encoding         : 65001

 Date: 28/11/2024 17:16:15
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for assessments
-- ----------------------------
DROP TABLE IF EXISTS `assessments`;
CREATE TABLE `assessments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `branch_id` int DEFAULT NULL,
  `student_id` int DEFAULT NULL,
  `title` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `description` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `created_at` datetime DEFAULT NULL,
  `creator_id` int DEFAULT NULL,
  `updator_id` int DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `meta_data` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `status` tinyint DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `student_id` (`student_id`) USING BTREE,
  KEY `branch_id` (`branch_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- ----------------------------
-- Table structure for branch_has_user
-- ----------------------------
DROP TABLE IF EXISTS `branch_has_user`;
CREATE TABLE `branch_has_user` (
  `branch_id` int NOT NULL,
  `user_id` int NOT NULL,
  PRIMARY KEY (`branch_id`,`user_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- ----------------------------
-- Table structure for branch_transfer
-- ----------------------------
DROP TABLE IF EXISTS `branch_transfer`;
CREATE TABLE `branch_transfer` (
  `id` int NOT NULL AUTO_INCREMENT,
  `student_id` int DEFAULT NULL,
  `list_contract_id` int DEFAULT NULL,
  `from_class_id` int DEFAULT NULL,
  `from_branch_id` int DEFAULT NULL,
  `from_product_id` int DEFAULT NULL,
  `from_program_id` int DEFAULT NULL,
  `to_class_id` int DEFAULT NULL,
  `to_branch_id` int DEFAULT NULL,
  `to_product_id` int DEFAULT NULL,
  `to_program_id` int DEFAULT NULL,
  `meta_data` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `creator_id` int DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updator_id` int DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `attached_file` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `status` tinyint DEFAULT NULL COMMENT '0 xóa, 1 chờ duyệt di, 2 từ chối duyệt đi, 3 chờ duyệt đến, 4 từ chối duyệt đến, 5 đã duyệt đến, 6 đã xử lý',
  `note` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `transfer_date` date DEFAULT NULL,
  `amount_transferred` int DEFAULT NULL,
  `session_transferred` int DEFAULT NULL,
  `session_exchange` int DEFAULT NULL,
  `amount_exchange` int DEFAULT NULL,
  `from_approver_id` int DEFAULT NULL,
  `from_approved_at` datetime DEFAULT NULL,
  `from_approve_comment` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `to_approver_id` int DEFAULT NULL,
  `to_approved_at` datetime DEFAULT NULL,
  `to_approve_comment` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `final_approver_id` int DEFAULT NULL,
  `final_approved_at` datetime DEFAULT NULL,
  `final_approve_comment` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `student_id` (`student_id`) USING BTREE,
  KEY `from_class_id` (`from_class_id`) USING BTREE,
  KEY `to_class_id` (`to_class_id`) USING BTREE,
  KEY `creator_id` (`creator_id`) USING BTREE,
  KEY `to_branch_id` (`to_branch_id`) USING BTREE,
  KEY `from_branch_id` (`from_branch_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Table structure for branches
-- ----------------------------
DROP TABLE IF EXISTS `branches`;
CREATE TABLE `branches` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'branch id lms',
  `accounting_id` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hrm_id` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `area_id` int DEFAULT NULL COMMENT 'Mã phân khu vùng miền: 1 - Hà Nội, 2 - TPHCM, 3 - Các Tỉnh Khác',
  `zone_id` int DEFAULT NULL,
  `region_id` int DEFAULT NULL,
  `opened_date` date DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL,
  `creator_id` int DEFAULT NULL,
  `updator_id` int DEFAULT NULL,
  `status` tinyint(1) DEFAULT '1',
  `branch_code` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_last_date` datetime DEFAULT NULL,
  `hotline` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_lms` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `placeID` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `index_branch` (`id`) USING BTREE,
  KEY `zone_id` (`zone_id`) USING BTREE,
  KEY `region_id` (`region_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Table structure for class_transfer
-- ----------------------------
DROP TABLE IF EXISTS `class_transfer`;
CREATE TABLE `class_transfer` (
  `id` int NOT NULL AUTO_INCREMENT,
  `student_id` int DEFAULT NULL,
  `contract_id` int DEFAULT NULL,
  `from_class_id` int DEFAULT NULL,
  `from_branch_id` int DEFAULT NULL,
  `from_product_id` int DEFAULT NULL,
  `from_program_id` int DEFAULT NULL,
  `to_class_id` int DEFAULT NULL,
  `to_branch_id` int DEFAULT NULL,
  `to_product_id` int DEFAULT NULL,
  `to_program_id` int DEFAULT NULL,
  `meta_data` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `creator_id` int DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updator_id` int DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `attached_file` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `status` tinyint DEFAULT NULL,
  `note` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `transfer_date` date DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `student_id` (`student_id`) USING BTREE,
  KEY `from_class_id` (`from_class_id`) USING BTREE,
  KEY `to_class_id` (`to_class_id`) USING BTREE,
  KEY `creator_id` (`creator_id`) USING BTREE,
  KEY `to_branch_id` (`to_branch_id`) USING BTREE,
  KEY `from_branch_id` (`from_branch_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Table structure for classes
-- ----------------------------
DROP TABLE IF EXISTS `classes`;
CREATE TABLE `classes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `teacher_id` int DEFAULT NULL COMMENT 'teacher user id',
  `cls_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'tên lớp trên lms',
  `product_id` int DEFAULT NULL,
  `program_id` int DEFAULT NULL COMMENT 'program id crm (cấp chia nhỏ nhất) VD: Seed 1, Seed 2, Seedbed 1, Seedbed 2',
  `cm_id` int DEFAULT NULL,
  `cls_startdate` date DEFAULT NULL COMMENT 'ngày bắt đầu lớp trên lms',
  `cls_enddate` date DEFAULT NULL COMMENT 'ngày kết thúc lớp trên lms',
  `level_id` int DEFAULT NULL COMMENT 'program level id lms',
  `class_day` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'dạng json',
  `max_students` int DEFAULT '16',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `creator_id` int DEFAULT NULL,
  `updator_id` int DEFAULT NULL,
  `branch_id` int DEFAULT NULL,
  `status` tinyint(1) DEFAULT NULL COMMENT 'Trạng thái sử dụng của lớp:\r\n0 - Lớp đang available mọi chức năng\r\n1 - Lớp không được phép sử dụng để thêm học sinh',
  `total_cycles` tinyint DEFAULT '1',
  `type` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `program_id` (`program_id`) USING BTREE,
  KEY `product_id` (`product_id`) USING BTREE,
  KEY `branch_id` (`branch_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Table structure for contracts
-- ----------------------------
DROP TABLE IF EXISTS `contracts`;
CREATE TABLE `contracts` (
  `id` int NOT NULL AUTO_INCREMENT COMMENT 'ID hợp đồng',
  `type` tinyint DEFAULT '0',
  `code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Mã hợp đồng trên phần mềm kế toán',
  `student_id` int DEFAULT NULL COMMENT 'Mã ID của học sinh',
  `reference_contract_id` int DEFAULT NULL COMMENT 'Mã ID hợp đồng có liên quan',
  `transfer_to_contract_id` int DEFAULT NULL,
  `branch_id` int DEFAULT NULL COMMENT 'Mã ID trung tâm',
  `ceo_branch_id` int DEFAULT NULL,
  `ec_id` int DEFAULT NULL COMMENT 'Mã User ID của EC trung tâm tại thời điểm hợp đồng có hiệu lực',
  `ec_leader_id` int DEFAULT NULL COMMENT 'Mã User ID của EC Leader trung tâm tại thời điểm hợp đồng có hiệu lực',
  `cm_id` int DEFAULT NULL COMMENT 'Mã User ID của CM trung tâm tại thời điểm hợp đồng có hiệu lực',
  `cm_leader_id` int DEFAULT NULL COMMENT 'Mã User ID của OM (CM Leader) trung tâm tại thời điểm hợp đồng có hiệu lực',
  `product_id` int DEFAULT NULL COMMENT 'Mã ID sản phẩm',
  `program_id` int DEFAULT NULL COMMENT 'Mã ID chương trình học',
  `tuition_fee_id` int DEFAULT NULL COMMENT 'Mã ID gói phí',
  `init_tuition_fee_id` int DEFAULT NULL COMMENT 'Mã ID gói phí lúc khởi tạo',
  `init_tuition_fee_amount` int DEFAULT NULL,
  `init_tuition_fee_receivable` int DEFAULT NULL COMMENT 'Số tiền sau giảm trừ trong giá gốc theo thông tin gói phí',
  `init_tuition_fee_session` int DEFAULT NULL,
  `init_total_charge` int DEFAULT NULL COMMENT 'Số tiền đã đóng lúc đầu',
  `must_charge` int DEFAULT NULL COMMENT 'Tổng số tiền còn lại phải đóng sau khi đã trừ hết các khoản khuyến mại và chiết khấu',
  `total_charged` int DEFAULT NULL COMMENT 'Tổng số tiền đã đóng',
  `debt_amount` int DEFAULT NULL COMMENT 'Số tiền công nợ của hợp đồng',
  `total_discount` int DEFAULT NULL,
  `discount_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Mã chiết khấu',
  `discount_code_id` int DEFAULT NULL,
  `discount_code_amount` int DEFAULT '0' COMMENT 'Tổng số tiền giảm trừ bao gồm các hình thức chiết khấu, voucher và hình thức khác',
  `discount_code_percent` int DEFAULT NULL,
  `coupon_code` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Mã giảm giá',
  `coupon_amount` int DEFAULT NULL,
  `coupon_session` int DEFAULT NULL,
  `total_sessions` int DEFAULT NULL COMMENT 'Tổng số buổi học đã mua theo gói phí nhập học và lựa chọn có bỏ qua học thử hay không (bỏ qua được cộng 3 buổi chỉ cho lần đầu tiên)',
  `real_sessions` int DEFAULT NULL COMMENT 'Số buổi học thực tế được tính từ số tiền thực đóng',
  `bonus_sessions` int DEFAULT '0',
  `summary_sessions` int DEFAULT NULL,
  `reserved_sessions` int DEFAULT '0' COMMENT 'Số buổi đã bảo lưu',
  `reservable_sessions` int DEFAULT '0' COMMENT 'Số buổi được bảo lưu bình thường',
  `done_sessions` int DEFAULT '0',
  `left_sessions` int DEFAULT '0',
  `last_done_sessions` int DEFAULT '0',
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'Thông tin chi tiết giảm trừ',
  `bill_info` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Thông tin mô tả giảm trừ',
  `start_date` date DEFAULT NULL COMMENT 'Ngày bắt đầu nhập học',
  `end_date` date DEFAULT NULL COMMENT 'Ngày dự tính kết thúc học',
  `class_id` int DEFAULT NULL COMMENT 'Mã ID lớp học được xếp vào học',
  `enrolment_start_date` date DEFAULT NULL COMMENT 'Ngày bắt đầu vào học',
  `enrolment_last_date` date DEFAULT NULL COMMENT 'Ngày kết thúc học theo số tiền thực đóng',
  `status` tinyint(1) DEFAULT '0' COMMENT '0 - Đã xóa, \r\n1 - Đã active nhưng chưa đóng phí, \r\n2 - Đã active và đặt cọc nhưng chưa thu đủ phí hoặc đang chờ nhận chuyển phí, \r\n3 - Đã active và đã thu đủ phí nhưng chưa được xếp lớp, \r\n4 - Đang bảo lưu không giữ chỗ hoặc pending, \r\n5 - Đang được nhận học bổng hoặc VIP hoặc chuyển phí \r\n6 - Đã được xếp lớp và đang đi học, \r\n7 - Đã bị withdraw, \r\n8 - Đã bỏ cọc',
  `created_at` datetime DEFAULT NULL COMMENT 'Thời gian khởi tạo bản ghi, thành lập hợp đồng',
  `creator_id` int DEFAULT NULL COMMENT 'Mã User ID người lập hợp đồng',
  `updated_at` datetime DEFAULT NULL COMMENT 'Thời gian cập nhật thông tin thay đổi nội dung hợp đồng',
  `updator_id` int DEFAULT NULL COMMENT 'Người thực hiện cập nhật thông tin thay đổi nội dung hợp đồng',
  `note` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Các ghi chú, lưu ý',
  `action` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Ghi chú thao tác cập nhật',
  `count_recharge` tinyint DEFAULT '0',
  `type_withdraw` tinyint DEFAULT NULL COMMENT '1 withdraw hết phí,2 withdraw do chuyển phí',
  `completed_date` date DEFAULT NULL COMMENT 'Ngày hợp đồng được đóng đủ phí',
  `renewed_date` date DEFAULT NULL COMMENT 'Ngày tới hạn tái phí',
  `success_renewed_date` date DEFAULT NULL COMMENT 'Ngày tái phí thành công của contract',
  `renew_runtime` date DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `ec_id` (`ec_id`) USING BTREE,
  KEY `tuition_fee_id` (`tuition_fee_id`) USING BTREE,
  KEY `creator_id` (`creator_id`) USING BTREE,
  KEY `cm_id` (`cm_id`) USING BTREE,
  KEY `student_id` (`student_id`) USING BTREE,
  KEY `branch_id` (`branch_id`) USING BTREE,
  KEY `ceo_branch_id` (`ceo_branch_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Table structure for coupon_logs
-- ----------------------------
DROP TABLE IF EXISTS `coupon_logs`;
CREATE TABLE `coupon_logs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `coupon_id` int DEFAULT NULL,
  `contract_id` int DEFAULT NULL,
  `type` tinyint DEFAULT '0',
  `created_at` datetime DEFAULT NULL,
  `creator_id` int DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1003 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for coupons
-- ----------------------------
DROP TABLE IF EXISTS `coupons`;
CREATE TABLE `coupons` (
  `id` int NOT NULL AUTO_INCREMENT COMMENT 'Mã ID phiếu ưu đãi',
  `code` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL COMMENT 'Mã xác nhận phiếu ưu đãi, vd: 105100100000123',
  `alias` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL COMMENT 'Mã định danh của phiếu, vd: 00000123',
  `type` tinyint(1) DEFAULT '1' COMMENT 'Loại phiếu ưu đãi với: \r\n 0 - Mặc định là unlimited tức không có giới hạn lượng sử dụng và không có mã định danh riêng biệt.\r\n 1 - Là có mã định danh riêng biệt cho từng coupon và số lần được sử dụng cho mỗi coupon là 1.\r\n 2 - Là không có mã định danh riêng biệt nhưng có giới hạn về số lần được sử dụng.',
  `quota` int DEFAULT '1' COMMENT 'Số lần được sử dụng tối đa với coupon',
  `used` int DEFAULT '0' COMMENT 'Số lần đã được sử dụng với coupon',
  `partner_id` int DEFAULT NULL COMMENT 'Mã đối tác sở hữu',
  `student_id` int DEFAULT NULL COMMENT 'Mã ID học sinh sử dụng phiếu',
  `branch_id` int DEFAULT '0' COMMENT 'Mã trung tâm tiếp nhận phiếu',
  `coupon_amount` int DEFAULT '0' COMMENT 'Giá trị của phiếu ưu đãi',
  `coupon_session` int DEFAULT '0',
  `bonus_amount` int DEFAULT NULL COMMENT 'Số tiền tương ứng với số buổi học bổng',
  `start_date` date DEFAULT NULL COMMENT 'Ngày bắt đầu có hiệu lực',
  `end_date` date DEFAULT NULL COMMENT 'Ngày hết hiệu lực',
  `checked_date` date DEFAULT NULL COMMENT 'Ngày sử dụng',
  `note` text CHARACTER SET latin1 COLLATE latin1_swedish_ci COMMENT 'Ghi chú về phiếu ưu đãi',
  `status` tinyint(1) DEFAULT NULL COMMENT 'Trạng thái của phiếu với 0 là deactive, 1 là đang active và 2 là đã sử dụng',
  `created_at` datetime DEFAULT NULL COMMENT 'Thời điểm khởi tạo bản ghi',
  `creator_id` int DEFAULT NULL COMMENT 'Mã ID của user khởi tạo bản ghi',
  `updated_at` datetime DEFAULT NULL,
  `updator_id` int DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `code` (`code`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=287 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for crm_contact_methods
-- ----------------------------
DROP TABLE IF EXISTS `crm_contact_methods`;
CREATE TABLE `crm_contact_methods` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint NOT NULL COMMENT '0 - inactive, 1 - active',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Table structure for crm_customer_care
-- ----------------------------
DROP TABLE IF EXISTS `crm_customer_care`;
CREATE TABLE `crm_customer_care` (
  `id` int NOT NULL AUTO_INCREMENT,
  `parent_id` int DEFAULT NULL,
  `note` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `created_at` datetime DEFAULT NULL,
  `creator_id` int DEFAULT NULL,
  `method_id` int DEFAULT NULL,
  `care_date` datetime DEFAULT NULL,
  `data_id` int DEFAULT NULL,
  `data_state` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `phone` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `branch_id` int DEFAULT NULL,
  `status` tinyint DEFAULT '1',
  `attached_file` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `next_care_date` datetime DEFAULT NULL,
  `call_status` int DEFAULT NULL,
  `call_status_sub` int DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `parent_id` (`parent_id`) USING BTREE,
  KEY `branch_id` (`branch_id`) USING BTREE,
  KEY `data_id` (`data_id`) USING BTREE,
  KEY `creator_id` (`creator_id`) USING BTREE,
  KEY `care_date` (`care_date`) USING BTREE,
  KEY `method_id` (`method_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- ----------------------------
-- Table structure for crm_import_parents
-- ----------------------------
DROP TABLE IF EXISTS `crm_import_parents`;
CREATE TABLE `crm_import_parents` (
  `id` int NOT NULL AUTO_INCREMENT,
  `import_id` int DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `gud_mobile1` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `gud_mobile2` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `address` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `note` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `status` tinyint DEFAULT '0' COMMENT '0: là chưa xử lý, 1: ok, 2: dữ liệu  đầu vào lỗi, 3: trùng lặp dữ liệu file excel, 4: trùng lặp dữ liệu khách hàng đang chăm sóc, 6: đã import thành công',
  `created_at` datetime DEFAULT NULL,
  `creator_id` int DEFAULT NULL,
  `error_message` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `student_name_1` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `student_name_2` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `student_birthday_1` date DEFAULT NULL,
  `student_birthday_2` date DEFAULT NULL,
  `owner_hrm` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `owner_id` int DEFAULT NULL,
  `checkin_at` datetime DEFAULT NULL,
  `checkin_branch_accounting_id` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `is_lock` tinyint DEFAULT '1',
  `parent_id` int DEFAULT NULL,
  `province_id` int DEFAULT NULL,
  `province_name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `district_id` int DEFAULT NULL,
  `district_name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `import_id` (`import_id`) USING BTREE,
  KEY `gud_mobile1` (`gud_mobile1`) USING BTREE,
  KEY `gud_mobile2` (`gud_mobile2`) USING BTREE,
  KEY `parent_id` (`parent_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- ----------------------------
-- Table structure for crm_imports
-- ----------------------------
DROP TABLE IF EXISTS `crm_imports`;
CREATE TABLE `crm_imports` (
  `id` int NOT NULL AUTO_INCREMENT,
  `file_name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `file_link` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `status` tinyint DEFAULT '0',
  `created_at` datetime DEFAULT NULL,
  `creator_id` int DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updator_id` int DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=10125 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- ----------------------------
-- Table structure for crm_parent_logs
-- ----------------------------
DROP TABLE IF EXISTS `crm_parent_logs`;
CREATE TABLE `crm_parent_logs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `parent_id` int DEFAULT NULL,
  `content` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `created_at` datetime DEFAULT NULL,
  `creator_id` int DEFAULT NULL,
  `status` tinyint DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `parent_id` (`parent_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- ----------------------------
-- Table structure for crm_parent_overwrite
-- ----------------------------
DROP TABLE IF EXISTS `crm_parent_overwrite`;
CREATE TABLE `crm_parent_overwrite` (
  `id` int NOT NULL AUTO_INCREMENT,
  `parent_id` int DEFAULT NULL,
  `last_branch_id` int DEFAULT NULL,
  `last_owner_id` int DEFAULT NULL,
  `branch_id` int DEFAULT NULL,
  `owner_id` int DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `creator_id` int DEFAULT NULL,
  `last_care_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `parent_id` (`parent_id`) USING BTREE,
  KEY `owner_id` (`owner_id`) USING BTREE,
  KEY `last_owner_id` (`last_owner_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- ----------------------------
-- Table structure for crm_parents
-- ----------------------------
DROP TABLE IF EXISTS `crm_parents`;
CREATE TABLE `crm_parents` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `mobile_1` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `mobile_2` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `address` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `province_id` int DEFAULT NULL,
  `district_id` int DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `creator_id` int DEFAULT NULL,
  `gender` char(2) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `birthday` date DEFAULT NULL,
  `job_id` int DEFAULT NULL,
  `source_detail_id` int DEFAULT NULL,
  `source_id` int DEFAULT NULL,
  `note` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `status` tinyint DEFAULT '0',
  `last_assign_date` datetime DEFAULT NULL,
  `owner_id` int DEFAULT NULL,
  `branch_id` int DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updator_id` int DEFAULT NULL,
  `is_lock` tinyint DEFAULT '1',
  `care_date` datetime DEFAULT NULL,
  `next_care_date` datetime DEFAULT NULL,
  `c2c_mobile` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `tmp_branch_id` int DEFAULT NULL,
  `is_update` tinyint DEFAULT '0',
  `old_status` tinyint DEFAULT NULL,
  `last_ticket_date` datetime DEFAULT NULL,
  `level` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `mobile_1` (`mobile_1`) USING BTREE,
  KEY `mobile_2` (`mobile_2`) USING BTREE,
  KEY `owner_id` (`owner_id`) USING BTREE,
  KEY `source_id` (`source_id`) USING BTREE,
  KEY `tmp_branch_id` (`tmp_branch_id`) USING BTREE,
  KEY `creator_id` (`creator_id`) USING BTREE,
  KEY `last_assign_date` (`last_assign_date`) USING BTREE,
  KEY `status` (`status`) USING BTREE,
  KEY `source_detail_id` (`source_detail_id`) USING BTREE,
  KEY `level` (`level`) USING BTREE,
  KEY `branch_id` (`branch_id`) USING BTREE,
  KEY `care_date` (`care_date`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- ----------------------------
-- Table structure for crm_students
-- ----------------------------
DROP TABLE IF EXISTS `crm_students`;
CREATE TABLE `crm_students` (
  `id` int NOT NULL AUTO_INCREMENT,
  `parent_id` int DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `gender` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `school_level` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `school` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `birthday` date DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `creator_id` int DEFAULT NULL,
  `status` tinyint DEFAULT '0' COMMENT '0: mới tạo, 1: tạo checkin, 2: đã đến checkin, 3: lên chính thức',
  `note` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `gud_mobile_1` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updator_id` int DEFAULT NULL,
  `checkin_owner_id` int DEFAULT NULL,
  `checkin_branch_id` int DEFAULT NULL,
  `checkin_at` datetime DEFAULT NULL,
  `checkined_at` datetime DEFAULT NULL,
  `checkined_by` int DEFAULT NULL,
  `checkined_note` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `lms_id` int DEFAULT NULL,
  `checkin_branch_accounting_id` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `type_product` tinyint DEFAULT '0',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `parent_id` (`parent_id`) USING BTREE,
  KEY `checkin_branch_id` (`checkin_branch_id`) USING BTREE,
  KEY `lms_id` (`lms_id`) USING BTREE,
  KEY `checkin_owner_id` (`checkin_owner_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- ----------------------------
-- Table structure for crm_tickets
-- ----------------------------
DROP TABLE IF EXISTS `crm_tickets`;
CREATE TABLE `crm_tickets` (
  `id` int NOT NULL AUTO_INCREMENT,
  `parent_id` int DEFAULT NULL,
  `type` tinyint DEFAULT NULL COMMENT '1: yêu cầu dịch vụ, 2 hỗ trợ, 3 khiếu lại, 4 báo lỗi',
  `description` tinytext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `note` tinytext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `created_at` datetime DEFAULT NULL,
  `creator_id` int DEFAULT NULL,
  `status` tinyint DEFAULT NULL COMMENT '1- Mới , 2 đã tiếp nhận, 3 chờ xử lý, 4 hoàn thành, 5 hủy',
  `updator_id` int DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `parent_id` (`parent_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- ----------------------------
-- Table structure for discount_codes
-- ----------------------------
DROP TABLE IF EXISTS `discount_codes`;
CREATE TABLE `discount_codes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `code` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL COMMENT 'Mã định danh cho mã chiết khấu giảm giá',
  `name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL COMMENT 'Tên mã chiết khấu',
  `percent` float(11,2) NOT NULL COMMENT 'Phần trăm giảm giá của mã chiết khấu',
  `price` float DEFAULT NULL COMMENT 'Giá gốc',
  `discount` double DEFAULT NULL COMMENT 'Số tiền chiết khấu',
  `bonus_sessions` int DEFAULT NULL COMMENT 'Số buổi học bổng đi kèm mã triết khấu',
  `start_date` date NOT NULL COMMENT 'Ngày bắt đầu có hiệu lực',
  `end_date` date NOT NULL COMMENT 'Ngày hết hạn hiệu lực',
  `created_at` datetime DEFAULT NULL,
  `creator_id` int DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updator_id` int DEFAULT NULL,
  `status` tinyint DEFAULT NULL COMMENT '0 = không hoạt động, 1 = hoạt động',
  `fee_ids` varchar(500) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `code` (`code`) USING BTREE,
  UNIQUE KEY `code_2` (`code`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- ----------------------------
-- Table structure for districts
-- ----------------------------
DROP TABLE IF EXISTS `districts`;
CREATE TABLE `districts` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `province_id` int DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `accounting_id` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=976 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Table structure for email_queues
-- ----------------------------
DROP TABLE IF EXISTS `email_queues`;
CREATE TABLE `email_queues` (
  `id` int NOT NULL AUTO_INCREMENT,
  `lock` tinyint DEFAULT '0',
  `status` tinyint DEFAULT '0',
  `email_from` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `email_to` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `email_subject` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `email_body` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `email_cc` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `email_attack` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `created_at` datetime DEFAULT NULL,
  `add_queue_at` datetime DEFAULT NULL,
  `send_at` datetime DEFAULT NULL,
  `send_code` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for exchanges
-- ----------------------------
DROP TABLE IF EXISTS `exchanges`;
CREATE TABLE `exchanges` (
  `id` int NOT NULL AUTO_INCREMENT,
  `contract_id` int DEFAULT NULL,
  `branch_id` int DEFAULT NULL,
  `student_id` int DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `creator_id` int DEFAULT NULL,
  `from_product_id` int DEFAULT NULL,
  `to_product_id` int DEFAULT NULL,
  `from_tuition_fee_id` int DEFAULT NULL,
  `to_tuition_fee_id` int DEFAULT NULL,
  `from_left_sessions` int DEFAULT NULL,
  `to_left_sessions` int DEFAULT NULL,
  `amount` int DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `student_id` (`student_id`) USING BTREE,
  KEY `contract_id` (`contract_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Table structure for jobs
-- ----------------------------
DROP TABLE IF EXISTS `jobs`;
CREATE TABLE `jobs` (
  `id` int NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `status` tinyint DEFAULT '0',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- ----------------------------
-- Table structure for log_contracts
-- ----------------------------
DROP TABLE IF EXISTS `log_contracts`;
CREATE TABLE `log_contracts` (
  `id` int NOT NULL AUTO_INCREMENT COMMENT 'ID hợp đồng',
  `contract_id` int DEFAULT NULL,
  `type` tinyint DEFAULT '0',
  `code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Mã hợp đồng trên phần mềm kế toán',
  `student_id` int DEFAULT NULL COMMENT 'Mã ID của học sinh',
  `reference_contract_id` int DEFAULT NULL COMMENT 'Mã ID hợp đồng có liên quan',
  `transfer_to_contract_id` int DEFAULT NULL,
  `branch_id` int DEFAULT NULL COMMENT 'Mã ID trung tâm',
  `ceo_branch_id` int DEFAULT NULL,
  `ec_id` int DEFAULT NULL COMMENT 'Mã User ID của EC trung tâm tại thời điểm hợp đồng có hiệu lực',
  `ec_leader_id` int DEFAULT NULL COMMENT 'Mã User ID của EC Leader trung tâm tại thời điểm hợp đồng có hiệu lực',
  `cm_id` int DEFAULT NULL COMMENT 'Mã User ID của CM trung tâm tại thời điểm hợp đồng có hiệu lực',
  `cm_leader_id` int DEFAULT NULL COMMENT 'Mã User ID của OM (CM Leader) trung tâm tại thời điểm hợp đồng có hiệu lực',
  `product_id` int DEFAULT NULL COMMENT 'Mã ID sản phẩm',
  `program_id` int DEFAULT NULL COMMENT 'Mã ID chương trình học',
  `tuition_fee_id` int DEFAULT NULL COMMENT 'Mã ID gói phí',
  `init_tuition_fee_id` int DEFAULT NULL,
  `init_tuition_fee_amount` int DEFAULT NULL,
  `init_tuition_fee_receivable` int DEFAULT NULL COMMENT 'Số tiền sau giảm trừ trong giá gốc theo thông tin gói phí',
  `init_tuition_fee_session` int DEFAULT NULL,
  `init_total_charge` int DEFAULT NULL,
  `must_charge` int DEFAULT NULL COMMENT 'Tổng số tiền còn lại phải đóng sau khi đã trừ hết các khoản khuyến mại và chiết khấu',
  `total_charged` int DEFAULT NULL COMMENT 'Tổng số tiền đã đóng',
  `debt_amount` int DEFAULT NULL COMMENT 'Số tiền công nợ của hợp đồng',
  `total_discount` int DEFAULT NULL,
  `discount_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Mã chiết khấu',
  `discount_code_id` int DEFAULT NULL,
  `discount_code_amount` int DEFAULT '0' COMMENT 'Tổng số tiền giảm trừ bao gồm các hình thức chiết khấu, voucher và hình thức khác',
  `discount_code_percent` int DEFAULT NULL,
  `coupon_code` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Mã giảm giá',
  `coupon_amount` int DEFAULT NULL,
  `coupon_session` int DEFAULT NULL,
  `total_sessions` int DEFAULT NULL COMMENT 'Tổng số buổi học đã mua theo gói phí nhập học và lựa chọn có bỏ qua học thử hay không (bỏ qua được cộng 3 buổi chỉ cho lần đầu tiên)',
  `real_sessions` int DEFAULT NULL COMMENT 'Số buổi học thực tế được tính từ số tiền thực đóng',
  `bonus_sessions` int DEFAULT '0',
  `summary_sessions` int DEFAULT NULL,
  `reserved_sessions` int DEFAULT '0' COMMENT 'Số buổi đã bảo lưu',
  `reservable_sessions` int DEFAULT '0' COMMENT 'Số buổi được bảo lưu bình thường',
  `done_sessions` int DEFAULT '0',
  `left_sessions` int DEFAULT NULL,
  `last_done_sessions` int DEFAULT '0',
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'Thông tin chi tiết giảm trừ',
  `bill_info` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Thông tin mô tả giảm trừ',
  `start_date` date DEFAULT NULL COMMENT 'Ngày bắt đầu nhập học',
  `end_date` date DEFAULT NULL COMMENT 'Ngày dự tính kết thúc học',
  `class_id` int DEFAULT NULL COMMENT 'Mã ID lớp học được xếp vào học',
  `enrolment_start_date` date DEFAULT NULL COMMENT 'Ngày bắt đầu vào học',
  `enrolment_last_date` date DEFAULT NULL COMMENT 'Ngày kết thúc học theo số tiền thực đóng',
  `status` tinyint(1) DEFAULT '0' COMMENT '0 - Đã xóa, \r\n1 - Đã active nhưng chưa đóng phí, \r\n2 - Đã active và đặt cọc nhưng chưa thu đủ phí hoặc đang chờ nhận chuyển phí, \r\n3 - Đã active và đã thu đủ phí nhưng chưa được xếp lớp, \r\n4 - Đang bảo lưu không giữ chỗ hoặc pending, \r\n5 - Đang được nhận học bổng hoặc VIP, \r\n6 - Đã được xếp lớp và đang đi học, \r\n7 - Đã bị withdraw, \r\n8 - Đã bỏ cọc',
  `created_at` datetime DEFAULT NULL COMMENT 'Thời gian khởi tạo bản ghi, thành lập hợp đồng',
  `creator_id` int DEFAULT NULL COMMENT 'Mã User ID người lập hợp đồng',
  `updated_at` datetime DEFAULT NULL COMMENT 'Thời gian cập nhật thông tin thay đổi nội dung hợp đồng',
  `updator_id` int DEFAULT NULL COMMENT 'Người thực hiện cập nhật thông tin thay đổi nội dung hợp đồng',
  `note` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Các ghi chú, lưu ý',
  `action` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Ghi chú thao tác cập nhật',
  `count_recharge` tinyint DEFAULT NULL,
  `type_withdraw` tinyint DEFAULT NULL COMMENT '1 withdraw hết phí,2 withdraw do chuyển phí',
  `log_time` datetime DEFAULT NULL,
  `completed_date` date DEFAULT NULL,
  `renewed_date` date DEFAULT NULL,
  `success_renewed_date` date DEFAULT NULL,
  `renew_runtime` date DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `ec_id` (`ec_id`) USING BTREE,
  KEY `tuition_fee_id` (`tuition_fee_id`) USING BTREE,
  KEY `creator_id` (`creator_id`) USING BTREE,
  KEY `cm_id` (`cm_id`) USING BTREE,
  KEY `student_id` (`student_id`) USING BTREE,
  KEY `branch_id` (`branch_id`) USING BTREE,
  KEY `ceo_branch_id` (`ceo_branch_id`) USING BTREE,
  KEY `contract_id` (`contract_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=44115 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Table structure for log_jobs
-- ----------------------------
DROP TABLE IF EXISTS `log_jobs`;
CREATE TABLE `log_jobs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `action` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `data` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1366 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for make_up_sessions
-- ----------------------------
DROP TABLE IF EXISTS `make_up_sessions`;
CREATE TABLE `make_up_sessions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `student_id` int DEFAULT NULL,
  `schedule_student_id` int DEFAULT NULL,
  `from_class_id` int DEFAULT NULL,
  `from_class_date` date DEFAULT NULL,
  `to_class_id` int DEFAULT NULL,
  `to_class_date` date DEFAULT NULL,
  `status` tinyint DEFAULT NULL COMMENT '0 :xóa, 1: đăng ký học bù, 2 đã học bù, 3 không đến học bù',
  `created_at` datetime DEFAULT NULL,
  `creator_id` int DEFAULT NULL,
  `updator_id` int DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- ----------------------------
-- Table structure for payments
-- ----------------------------
DROP TABLE IF EXISTS `payments`;
CREATE TABLE `payments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `contract_id` int DEFAULT NULL,
  `accounting_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `method` tinyint DEFAULT '0' COMMENT '0 - tiền mặt, 1 - chuyển khoản 2- Thanh toán thẻ tín dụng',
  `payload` tinyint DEFAULT '0' COMMENT '0 - 1 lần, 1 - nhiều lần',
  `must_charge` int DEFAULT NULL,
  `amount` int DEFAULT NULL COMMENT 'số tiền thu',
  `total` int DEFAULT NULL,
  `debt` int DEFAULT NULL COMMENT 'công nợ',
  `charge_date` date DEFAULT NULL COMMENT 'ngày đóng phí',
  `created_at` datetime DEFAULT NULL,
  `creator_id` int DEFAULT NULL,
  `hash_key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `count` int NOT NULL,
  `type` tinyint(1) DEFAULT NULL COMMENT '0 - bỏ cọc, 1 - lần đầu, 2 - tái phí',
  `student_id` int DEFAULT NULL,
  `note` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `contract_id` (`contract_id`) USING BTREE,
  KEY `creator_id` (`creator_id`) USING BTREE,
  KEY `student_id` (`student_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=37096 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Table structure for permission_groups
-- ----------------------------
DROP TABLE IF EXISTS `permission_groups`;
CREATE TABLE `permission_groups` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `description` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `status` tinyint DEFAULT NULL,
  `display_order` tinyint DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- ----------------------------
-- Table structure for permission_has_role
-- ----------------------------
DROP TABLE IF EXISTS `permission_has_role`;
CREATE TABLE `permission_has_role` (
  `role_id` int NOT NULL,
  `permission_id` int NOT NULL,
  PRIMARY KEY (`role_id`,`permission_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- ----------------------------
-- Table structure for permissions
-- ----------------------------
DROP TABLE IF EXISTS `permissions`;
CREATE TABLE `permissions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `description` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `group_id` int DEFAULT NULL,
  `display_order` tinyint DEFAULT NULL,
  `status` tinyint DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- ----------------------------
-- Table structure for products
-- ----------------------------
DROP TABLE IF EXISTS `products`;
CREATE TABLE `products` (
  `id` int NOT NULL AUTO_INCREMENT,
  `accounting_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'product id lsm',
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `creator_id` int DEFAULT NULL,
  `updator_id` int DEFAULT NULL,
  `status` tinyint DEFAULT '1',
  `max_student` int DEFAULT NULL COMMENT 'số học sinh tối đa',
  `min_number_of_days_in_week` tinyint DEFAULT NULL COMMENT 'Số ngày tối thiểu được học trong một tuần',
  `max_number_of_days_in_week` tinyint DEFAULT NULL COMMENT 'Số ngày tối đa được học trong một tuần',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `proc_id` (`code`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Table structure for programs
-- ----------------------------
DROP TABLE IF EXISTS `programs`;
CREATE TABLE `programs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `accounting_id` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Mã gói học tương ứng bên phần mềm kế toán',
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `code` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'Ghi chú tương đương với trường tên gói học bên phần mềm kế toán',
  `created_at` datetime DEFAULT NULL,
  `creator_id` int DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updator_id` int DEFAULT NULL,
  `status` tinyint DEFAULT '1',
  `parent_id` int DEFAULT '0',
  `product_id` int DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `product_id` (`product_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Table structure for provinces
-- ----------------------------
DROP TABLE IF EXISTS `provinces`;
CREATE TABLE `provinces` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `accounting_id` varchar(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `caresoft_id` int DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=97 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Table structure for public_holiday
-- ----------------------------
DROP TABLE IF EXISTS `public_holiday`;
CREATE TABLE `public_holiday` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `zone_id` int DEFAULT NULL,
  `products` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'ID của các products được áp dụng, vd: [1,2,3]',
  `branch_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `creator_id` int DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updator_id` int DEFAULT NULL,
  `status` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=131 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Table structure for report_full_fee_active
-- ----------------------------
DROP TABLE IF EXISTS `report_full_fee_active`;
CREATE TABLE `report_full_fee_active` (
  `id` int NOT NULL AUTO_INCREMENT COMMENT 'Mã ID bản ghi',
  `student_id` int DEFAULT '0' COMMENT 'ID học sinh trên CRM',
  `contract_id` int DEFAULT NULL COMMENT 'Mã ID contract gói phí',
  `class_id` int DEFAULT NULL COMMENT 'ID lớp học',
  `product_id` int DEFAULT '0' COMMENT 'ID sản phẩm',
  `init_tuition_fee_id` int DEFAULT NULL,
  `tuition_fee_id` int DEFAULT NULL,
  `cm_id` int DEFAULT '0' COMMENT 'ID CM',
  `report_month` varchar(255) DEFAULT NULL COMMENT 'Tháng báo cáo',
  `type` tinyint(1) DEFAULT '0' COMMENT 'Phân loại bản ghi',
  `branch_id` int DEFAULT '0' COMMENT 'ID trung tâm',
  `note` text COMMENT 'Ghi chú',
  `meta` text COMMENT 'Dữ liệu mở rộng',
  `force` tinyint(1) DEFAULT '0' COMMENT 'Đánh dấu bản ghi bắt buộc',
  `fixed` tinyint(1) DEFAULT '0' COMMENT 'Đánh dấu bản ghi cố định dữ liệu',
  `created_at` datetime DEFAULT NULL COMMENT 'Thời điểm tạo bản ghi',
  `creator_id` int DEFAULT '0' COMMENT 'ID người tạo bản ghi',
  `end_date` date DEFAULT NULL COMMENT 'Ngày hết phí',
  `start_date` date DEFAULT NULL COMMENT 'Ngày bắt đầu học (trừ học thử)',
  `done_sessions` int DEFAULT '0',
  `last_done_sessions` int DEFAULT NULL,
  `summary_sessions` int DEFAULT '0',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `student_id` (`student_id`) USING BTREE,
  KEY `branch_id` (`branch_id`) USING BTREE,
  KEY `cm_id` (`cm_id`) USING BTREE,
  KEY `report_month` (`report_month`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb3;

-- ----------------------------
-- Table structure for report_renews
-- ----------------------------
DROP TABLE IF EXISTS `report_renews`;
CREATE TABLE `report_renews` (
  `id` int NOT NULL AUTO_INCREMENT COMMENT 'Mã ID bản ghi kết quả renew',
  `student_id` int DEFAULT NULL,
  `contract_id` int DEFAULT '0' COMMENT 'Mã IID của bản ghi contract được renew hoặc tới hạn tái phí',
  `branch_id` int DEFAULT '0' COMMENT 'Mã ID trung tâm mà học sinh thực hiện renew',
  `product_id` int DEFAULT '0' COMMENT 'Mã ID sản phẩm của contract được tái phí hoặc tới hạn tái phí',
  `program_id` int DEFAULT '0' COMMENT 'Mã ID chương trình của contract được tái phí hoặc tới hạn tái phí',
  `class_id` int DEFAULT '0' COMMENT 'Mã ID lớp học của contract được tái phí hoặc tới hạn tái phí',
  `init_tuition_fee_id` int DEFAULT NULL,
  `init_renew_tuition_fee_id` int DEFAULT '0' COMMENT 'Mã ID gói phí của contract nối tiếp đóng full phí hoặc của contract tới hạn tái phí',
  `ec_id` int DEFAULT '0' COMMENT 'Mã ID nhân viên EC của học sinh',
  `cm_id` int DEFAULT '0' COMMENT 'Mã ID nhân viên CM của học sinh',
  `renewed_cm_id` int DEFAULT NULL COMMENT 'Mã ID nhân viên CM của contract tái phí cho contract này',
  `ec_leader_id` int DEFAULT '0' COMMENT 'Mã ID nhân viên EC của học sinh',
  `cm_leader_id` int DEFAULT '0' COMMENT 'Mã ID nhân viên OM của học sinh',
  `ceo_id` int DEFAULT '0' COMMENT 'Mã ID GĐ của trung tâm của học sinh',
  `renew_amount` int DEFAULT '0' COMMENT 'Số tiền tái phí của contract vừa đóng full phí để tái phí thành công cho contract này',
  `status` tinyint DEFAULT '0' COMMENT 'Trạng thái renew với: 0 là không lên báo cáo 1 là thành công và 2 là thất bại',
  `renewed_month` varchar(255) DEFAULT NULL COMMENT 'Thời điểm thực hiện renew tính theo Năm-Tháng với dữ liệu được định dạng là YYYY-MM ví dụ: 2019-05',
  `last_date` date DEFAULT NULL COMMENT 'Ngày đến hạn tái phí của contract',
  `note` text COMMENT 'Thông tin ghi chú về bản ghi',
  `meta_data` text COMMENT 'Dữ liệu tham chiếu mở rộng của bản ghi',
  `created_at` datetime DEFAULT NULL COMMENT 'Thời điểm khởi tạo bản ghi',
  `created_by` int DEFAULT '0' COMMENT 'Mã ID người tạo bản ghi',
  `disabled` tinyint(1) DEFAULT '0' COMMENT 'Có tính vào báo cáo hay không với 0 là có và 1 là không tính vào',
  `fixed` tinyint(1) DEFAULT '0' COMMENT 'Đánh dấu bản ghi đã được fix giá trị, không được phép cập nhật lại',
  PRIMARY KEY (`id`),
  KEY `cm_id` (`cm_id`),
  KEY `branch_id` (`branch_id`) USING BTREE,
  KEY `renewed_month` (`renewed_month`) USING BTREE,
  KEY `contract_id` (`contract_id`) USING BTREE,
  KEY `student_id` (`student_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb3;

-- ----------------------------
-- Table structure for reserves
-- ----------------------------
DROP TABLE IF EXISTS `reserves`;
CREATE TABLE `reserves` (
  `id` int NOT NULL AUTO_INCREMENT,
  `student_id` int DEFAULT NULL,
  `type` tinyint DEFAULT '0' COMMENT '0 - bình thường, 1 - đặc biệt',
  `start_date` date DEFAULT NULL,
  `session` int DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `status` tinyint DEFAULT '0' COMMENT '0 - đã xóa, 1 - chờ duyệt, 2 - đã duyệt, 3 - từ chối, 4 - đã xử lý',
  `creator_id` int DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `approver_id` int DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `comment` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `meta_data` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `is_reserved` tinyint DEFAULT '0' COMMENT '0 - không giữ chỗ, 1 - giữ chỗ',
  `attached_file` varchar(2550) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contract_id` int DEFAULT NULL,
  `branch_id` int DEFAULT NULL,
  `product_id` int DEFAULT NULL,
  `program_id` int DEFAULT NULL,
  `class_id` int DEFAULT NULL,
  `note` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `updated_at` datetime DEFAULT NULL,
  `updator_id` int DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `student_id` (`student_id`) USING BTREE,
  KEY `creator_id` (`creator_id`) USING BTREE,
  KEY `approver_id` (`approver_id`) USING BTREE,
  KEY `contract_id` (`contract_id`) USING BTREE,
  KEY `branch_id` (`branch_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Table structure for role_has_user
-- ----------------------------
DROP TABLE IF EXISTS `role_has_user`;
CREATE TABLE `role_has_user` (
  `user_id` int NOT NULL,
  `role_id` int NOT NULL,
  PRIMARY KEY (`user_id`,`role_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- ----------------------------
-- Table structure for roles
-- ----------------------------
DROP TABLE IF EXISTS `roles`;
CREATE TABLE `roles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `guard_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `creator_id` int DEFAULT NULL,
  `updator_id` int DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `code` (`code`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1000000 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Table structure for rooms
-- ----------------------------
DROP TABLE IF EXISTS `rooms`;
CREATE TABLE `rooms` (
  `id` int NOT NULL AUTO_INCREMENT,
  `code` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'room id lms',
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'room name lms',
  `branch_id` int DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `creator_id` int DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updator_id` int DEFAULT NULL,
  `status` tinyint DEFAULT '0' COMMENT '0 - inactive, 1 - active',
  `type` tinyint DEFAULT NULL COMMENT '1 - manualy, 0 - auto',
  `product_id` int DEFAULT NULL COMMENT 'Theo hiệu suất chương trình',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `branch_id` (`branch_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Table structure for schedule_has_student
-- ----------------------------
DROP TABLE IF EXISTS `schedule_has_student`;
CREATE TABLE `schedule_has_student` (
  `id` int NOT NULL AUTO_INCREMENT,
  `student_id` int DEFAULT NULL,
  `branch_id` int DEFAULT NULL,
  `class_id` int DEFAULT NULL,
  `contract_id` int DEFAULT NULL,
  `product_id` int DEFAULT NULL,
  `program_id` int DEFAULT NULL,
  `subject_id` int DEFAULT NULL,
  `subject_stt` tinyint DEFAULT NULL,
  `class_date` date DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updator_id` int DEFAULT NULL,
  `note` varchar(500) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `status` tinyint DEFAULT '0' COMMENT ' 1 - tính phí, 0 ko tính phí,  2- bảo lưu',
  `attendance_status` tinyint DEFAULT '0' COMMENT '0 chưa điểm danh, 1 đi học , 2 đăng ký học bù, 3  đã học bù',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `student_id` (`student_id`) USING BTREE,
  KEY `branch_id` (`branch_id`) USING BTREE,
  KEY `class_id` (`class_id`) USING BTREE,
  KEY `contract_id` (`contract_id`) USING BTREE,
  KEY `class_date` (`class_date`) USING BTREE,
  KEY `subject_id` (`subject_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- ----------------------------
-- Table structure for schedules
-- ----------------------------
DROP TABLE IF EXISTS `schedules`;
CREATE TABLE `schedules` (
  `id` int NOT NULL AUTO_INCREMENT,
  `class_date` date NOT NULL,
  `status` tinyint DEFAULT '0' COMMENT '0 - inactive, 1 - active',
  `class_id` int DEFAULT NULL COMMENT 'class id crm',
  `subject_id` int DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `creator_id` int DEFAULT NULL,
  `updator_id` int DEFAULT NULL,
  `class_stt` int DEFAULT NULL,
  `subject_stt` int DEFAULT NULL,
  `teacher_id` int DEFAULT NULL,
  `cm_id` int DEFAULT NULL,
  `branch_id` int DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `class_id` (`class_id`) USING BTREE,
  KEY `class_date` (`class_date`) USING BTREE,
  KEY `subject_id` (`subject_id`) USING BTREE,
  KEY `teacher_id` (`teacher_id`) USING BTREE,
  KEY `cm_id` (`cm_id`) USING BTREE,
  KEY `branch_id` (`branch_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1211 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Table structure for sessions
-- ----------------------------
DROP TABLE IF EXISTS `sessions`;
CREATE TABLE `sessions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `class_id` int DEFAULT NULL,
  `shift_id` int DEFAULT NULL,
  `room_id` int DEFAULT NULL COMMENT 'room id crm',
  `teacher_id` int DEFAULT NULL COMMENT 'Đây là user_id trong bảng users của giáo viên chứ không phải teacher_id trong bảng teachers',
  `class_day` int DEFAULT NULL COMMENT '0 - Chủ nhật,\r\n1 - Thứ 2, \r\n2 - Thứ 3, \r\n3 - Thứ 4, \r\n4 - Thứ 5, \r\n5 - Thứ 6, \r\n6 - Thứ 7 ',
  `status` tinyint DEFAULT '1' COMMENT '0 - inactive, 1 - active',
  `created_at` datetime DEFAULT NULL,
  `creator_id` int DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updator_id` int DEFAULT NULL,
  `branch_id` int DEFAULT '0',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `class_id` (`class_id`) USING BTREE,
  KEY `shift_id` (`shift_id`) USING BTREE,
  KEY `teacher_id` (`teacher_id`) USING BTREE,
  KEY `room_id` (`room_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=105 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Table structure for shifts
-- ----------------------------
DROP TABLE IF EXISTS `shifts`;
CREATE TABLE `shifts` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `status` tinyint DEFAULT '0' COMMENT '0 - inactive, 1- active',
  `zone_id` tinyint DEFAULT NULL,
  `creator_id` int DEFAULT NULL,
  `updator_id` int DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Table structure for source_detail
-- ----------------------------
DROP TABLE IF EXISTS `source_detail`;
CREATE TABLE `source_detail` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint DEFAULT '0',
  `created_at` datetime DEFAULT NULL,
  `creator_id` int DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updator_id` int DEFAULT NULL,
  `branch_id` int DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `branch_id` (`branch_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=461 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Table structure for sources
-- ----------------------------
DROP TABLE IF EXISTS `sources`;
CREATE TABLE `sources` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT '0' COMMENT '0 - inactive, 1 - active',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Table structure for student_logs
-- ----------------------------
DROP TABLE IF EXISTS `student_logs`;
CREATE TABLE `student_logs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `student_id` int DEFAULT NULL,
  `content` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `created_at` datetime DEFAULT NULL,
  `creator_id` int DEFAULT NULL,
  `status` tinyint DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `student_id` (`student_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- ----------------------------
-- Table structure for student_waitting_process
-- ----------------------------
DROP TABLE IF EXISTS `student_waitting_process`;
CREATE TABLE `student_waitting_process` (
  `id` int NOT NULL AUTO_INCREMENT,
  `student_id` int DEFAULT NULL,
  `data_id` int DEFAULT NULL,
  `type` tinyint DEFAULT NULL COMMENT '1 bảo lưu, 2 chuyển lớp, 3 chuyển trung  tâm, 4 chuyển phí',
  `status` tinyint DEFAULT NULL COMMENT '0 hủy, 1 chờ xử lý, 2 đã xử lý',
  `created_at` datetime DEFAULT NULL,
  `processed_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updator_id` int DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `student_id` (`student_id`) USING BTREE,
  KEY `data_id` (`data_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- ----------------------------
-- Table structure for students
-- ----------------------------
DROP TABLE IF EXISTS `students`;
CREATE TABLE `students` (
  `id` int NOT NULL AUTO_INCREMENT COMMENT 'Mã ID của học sinh',
  `lms_code` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Mã CRM: ''CRM''+id',
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Họ tên học sinh viết in hoa',
  `firstname` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `midname` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lastname` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nick` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Nick name trong lớp của học sinh',
  `gender` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Giới tính học sinh: M - nam, F - nữ',
  `type` tinyint DEFAULT '0' COMMENT '0 - Bình thường, 1 - VIP',
  `date_of_birth` date DEFAULT NULL COMMENT 'định dạng: YYYY-mm-dd',
  `gud_mobile1` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Số di động của phụ huynh 1 (bố)',
  `gud_name1` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Tên phụ huynh 1 (bố)',
  `gud_email1` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Email phụ huynh 1 (bố)',
  `gud_mobile2` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Số di động của phụ huynh 2 (mẹ)',
  `gud_name2` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Tên phụ huynh 2 (mẹ)',
  `gud_email2` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Email phụ huynh 2 (mẹ)',
  `address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Địa chỉ thường trú',
  `province_id` int DEFAULT NULL COMMENT 'ID Tỉnh / Thành phố',
  `district_id` int DEFAULT NULL COMMENT 'ID Quận / Huyện',
  `school` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Trường học',
  `school_level` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `school_grade` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Mã School Grade (xem API 16 LMS)',
  `created_at` datetime DEFAULT NULL COMMENT 'định dạng: YYYY-mm-dd H:m:i',
  `creator_id` int DEFAULT NULL COMMENT 'User ID người tạo học sinh',
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP COMMENT 'định dạng: YYYY-mm-dd H:m:i',
  `updator_id` int DEFAULT NULL COMMENT 'User ID người cập nhật thông tin hồ sơ học sinh',
  `note` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'Các ghi chú khác',
  `aspiration` text COLLATE utf8mb4_unicode_ci COMMENT 'Nguyện vọng của phụ huynh',
  `avatar` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'Nguồn thu thập dữ liệu học sinh',
  `branch_id` int DEFAULT NULL COMMENT 'Mã trung tâm',
  `attached_file` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL COMMENT 'Tệp tin bổ sung đính kèm',
  `gud_card1` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Số CCCD của phụ huynh 1 (bố)',
  `gud_card2` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Số CCCD của phụ huynh 2 (mẹ)',
  `gud_birth_day1` date DEFAULT NULL COMMENT 'Ngày sinh nhật của phụ huynh 1 (bố)',
  `gud_birth_day2` date DEFAULT NULL COMMENT 'Ngày sinh nhật của phụ huynh 1 (mẹ)',
  `gud_gender1` char(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gud_gender2` char(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gud_job1` int DEFAULT NULL,
  `gud_job2` int DEFAULT NULL,
  `status` tinyint(1) unsigned zerofill DEFAULT '1',
  `source_detail_id` int DEFAULT NULL,
  `source_id` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `index_student` (`lms_code`) USING BTREE,
  KEY `province_id` (`province_id`) USING BTREE,
  KEY `district_id` (`district_id`) USING BTREE,
  KEY `gud_mobile1` (`gud_mobile1`) USING BTREE,
  KEY `gud_mobile2` (`gud_mobile2`) USING BTREE,
  KEY `branch_id` (`branch_id`) USING BTREE,
  KEY `status` (`status`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Table structure for subject_has_class
-- ----------------------------
DROP TABLE IF EXISTS `subject_has_class`;
CREATE TABLE `subject_has_class` (
  `id` int NOT NULL AUTO_INCREMENT,
  `subject_id` int DEFAULT NULL,
  `class_id` int DEFAULT NULL,
  `session` int DEFAULT NULL,
  `stt` tinyint DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `subject_id` (`subject_id`) USING BTREE,
  KEY `class_id` (`class_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=219 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- ----------------------------
-- Table structure for subjects
-- ----------------------------
DROP TABLE IF EXISTS `subjects`;
CREATE TABLE `subjects` (
  `id` int NOT NULL AUTO_INCREMENT,
  `code` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `description` tinytext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `status` tinyint DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `creator_id` int DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updator_id` int DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- ----------------------------
-- Table structure for term_student_user
-- ----------------------------
DROP TABLE IF EXISTS `term_student_user`;
CREATE TABLE `term_student_user` (
  `id` int NOT NULL AUTO_INCREMENT,
  `student_id` int NOT NULL,
  `ec_id` int DEFAULT NULL,
  `ec_leader_id` int DEFAULT NULL,
  `cm_id` int DEFAULT NULL,
  `cm_leader_id` int DEFAULT NULL,
  `ceo_branch_id` int DEFAULT NULL,
  `teacher_id` int DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `status` tinyint(1) DEFAULT '0',
  `branch_id` int DEFAULT NULL,
  `creator_id` int DEFAULT NULL,
  `updator_id` int DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `index_term_student_user` (`id`,`student_id`,`ec_id`,`cm_id`) USING BTREE,
  KEY `ec_id` (`ec_id`) USING BTREE,
  KEY `cm_id` (`cm_id`) USING BTREE,
  KEY `student_id` (`student_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Table structure for tuition_fee
-- ----------------------------
DROP TABLE IF EXISTS `tuition_fee`;
CREATE TABLE `tuition_fee` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `accounting_id` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `product_id` int DEFAULT NULL,
  `zone_id` int DEFAULT NULL,
  `session` int DEFAULT NULL,
  `price` int DEFAULT NULL,
  `discount` int DEFAULT NULL,
  `tax` int DEFAULT NULL COMMENT 'Thuế giá trị gia tăng',
  `receivable` int DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `creator_id` int DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updator_id` int DEFAULT NULL,
  `available_date` date DEFAULT NULL,
  `expired_date` date DEFAULT NULL,
  `hash_key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `changed_fields` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(1) unsigned zerofill DEFAULT '1',
  `type` tinyint DEFAULT '0' COMMENT '0 - 1 lần, 1 - nhiều lần',
  `branch_id` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `meta_data` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `number_of_months` tinyint DEFAULT NULL COMMENT 'số tháng đào tạo của gói phí',
  `type_contract` tinyint DEFAULT '1' COMMENT '1- chính thức, 0 - học thử',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `product_id` (`product_id`) USING BTREE,
  KEY `zone_id` (`zone_id`) USING BTREE,
  KEY `creator_id` (`creator_id`) USING BTREE,
  KEY `editor_id` (`updator_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Table structure for tuition_fee_relation
-- ----------------------------
DROP TABLE IF EXISTS `tuition_fee_relation`;
CREATE TABLE `tuition_fee_relation` (
  `id` int NOT NULL AUTO_INCREMENT,
  `tuition_fee_id` int DEFAULT NULL,
  `exchange_tuition_fee_id` int DEFAULT NULL,
  `status` tinyint DEFAULT '0' COMMENT '0 - inactive, 1 - active',
  `tuition_fee_name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `exchange_tuition_fee_name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `tuition_id` (`tuition_fee_id`) USING BTREE,
  KEY `exchange_tuition_id` (`exchange_tuition_fee_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb3;

-- ----------------------------
-- Table structure for tuition_transfer
-- ----------------------------
DROP TABLE IF EXISTS `tuition_transfer`;
CREATE TABLE `tuition_transfer` (
  `id` int NOT NULL AUTO_INCREMENT COMMENT 'Mã ID của bản ghi chuyển phí',
  `from_student_id` int DEFAULT NULL COMMENT 'Mã ID học sinh chuyển phí',
  `to_student_id` int DEFAULT NULL COMMENT 'Mã ID học sinh nhận phí',
  `note` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Ghi chú lý do chuyển phí.',
  `transfer_date` date DEFAULT NULL COMMENT 'Ngày thực hiện chuyển phí',
  `status` tinyint DEFAULT NULL COMMENT 'Trạng thái bản ghi chuyển phí với: \r\n0 - Đã bị hủy\r\n1 - Chờ duyệt\r\n2 - Giám đốc đã từ chối \n3 - Kế toán HO đã từ chối\r\n4 - Giám đốc duyệt \n5 - Kế toán HO đã duyệt  \r\n6 -  Đã được duyệt thành công',
  `creator_id` int DEFAULT NULL COMMENT 'Mã ID user khởi tạo bản ghi',
  `created_at` datetime DEFAULT NULL COMMENT 'Thời điểm khởi tạo bản ghi',
  `transferred_amount` int DEFAULT NULL COMMENT 'Số tiền được chuyển',
  `received_amount` int DEFAULT NULL COMMENT 'Số tiền nhận được',
  `meta_data` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'Dữ liệu các contract chuyển',
  `from_branch_id` int DEFAULT NULL COMMENT 'Trung tâm chuyển phí',
  `to_branch_id` int DEFAULT NULL COMMENT 'Trung tâm nhận phí',
  `from_product_id` int DEFAULT NULL COMMENT 'Sản phẩm chuyển phí',
  `to_product_id` int DEFAULT NULL COMMENT 'Sản phẩm nhận phí',
  `updator_id` int DEFAULT NULL COMMENT 'Mã ID user sửa bản ghi',
  `ceo_approver_id` int DEFAULT NULL COMMENT 'Mã ID user giám đốc phê duyệt chuyển phí',
  `ceo_approved_at` datetime DEFAULT NULL COMMENT 'Thời điểm giám đốc phê duyệt chuyển phí',
  `ceo_comment` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'Bình luận của giám đốc duyệt',
  `accounting_approver_id` int DEFAULT NULL COMMENT 'Mã ID user kế toán phê duyệt chuyển phí',
  `accounting_approved_at` datetime DEFAULT NULL COMMENT 'Thời điểm kế toán phê duyệt chuyển phí',
  `accounting_comment` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'Bình luận của kế toán duyệt',
  `updated_at` datetime DEFAULT NULL COMMENT 'Thời điểm chỉnh sửa bản ghi',
  `attached_file` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Đường dẫn tệp tin đính kèm',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `to_student_id` (`to_student_id`) USING BTREE,
  KEY `creator_id` (`creator_id`) USING BTREE,
  KEY `from_student_id` (`from_student_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Table structure for users
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `api_token` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `menuroles` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint NOT NULL DEFAULT '0',
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `creator_id` int DEFAULT NULL,
  `updator_id` int DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `branch_id` int DEFAULT NULL,
  `branch_name` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `hrm_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `manager_hrm_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `manager_id` int DEFAULT '0',
  `change_password` tinyint DEFAULT '0',
  `role_name` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `sip_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `manager_id` (`manager_id`) USING BTREE,
  KEY `hrm_id` (`hrm_id`) USING BTREE,
  KEY `manager_hrm_id` (`manager_hrm_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
