/*
 Navicat Premium Dump SQL

 Source Server         : Ant - DB
 Source Server Type    : MySQL
 Source Server Version : 80044 (8.0.44-0ubuntu0.22.04.1)
 Source Host           : 14.225.71.15:3306
 Source Schema         : dev-lapo

 Target Server Type    : MySQL
 Target Server Version : 80044 (8.0.44-0ubuntu0.22.04.1)
 File Encoding         : 65001

 Date: 15/01/2026 00:09:51
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for agreements
-- ----------------------------
DROP TABLE IF EXISTS `agreements`;
CREATE TABLE `agreements` (
  `id` int NOT NULL AUTO_INCREMENT,
  `student_id` int DEFAULT NULL,
  `branch_id` int DEFAULT NULL,
  `ec_id` int DEFAULT NULL,
  `ec_leader_id` int DEFAULT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `product_id` int DEFAULT NULL,
  `type_fee` tinyint DEFAULT NULL,
  `tuition_fee_id` int DEFAULT NULL,
  `must_charge` int DEFAULT '0',
  `total_charged` int DEFAULT '0',
  `debt_amount` int DEFAULT '0',
  `start_date` date DEFAULT NULL,
  `note` text COLLATE utf8mb4_unicode_ci,
  `status` tinyint DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `creator_id` int DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updator_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `student_id` (`student_id`) USING BTREE,
  KEY `branch_id` (`branch_id`) USING BTREE,
  KEY `product_id` (`product_id`) USING BTREE,
  KEY `ec_id` (`ec_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of agreements
-- ----------------------------
BEGIN;
INSERT INTO `agreements` (`id`, `student_id`, `branch_id`, `ec_id`, `ec_leader_id`, `code`, `product_id`, `type_fee`, `tuition_fee_id`, `must_charge`, `total_charged`, `debt_amount`, `start_date`, `note`, `status`, `created_at`, `creator_id`, `updated_at`, `updator_id`) VALUES (7, 541, 9, 1, NULL, '000007', NULL, 2, 43, 5936000, 5936000, 0, '2026-01-01', NULL, 3, '2026-01-14 22:15:22', 1, '2026-01-14 22:17:18', 1);
COMMIT;

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
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- ----------------------------
-- Records of assessments
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for b2b_campaigns
-- ----------------------------
DROP TABLE IF EXISTS `b2b_campaigns`;
CREATE TABLE `b2b_campaigns` (
  `id` int NOT NULL AUTO_INCREMENT,
  `b2b_source_id` int DEFAULT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `note` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `creator_id` int DEFAULT NULL,
  `updator_id` int DEFAULT NULL,
  `status` tinyint DEFAULT NULL,
  `list_tuition_fee` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meta_data` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `b2b_source_id` (`b2b_source_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of b2b_campaigns
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for b2b_sources
-- ----------------------------
DROP TABLE IF EXISTS `b2b_sources`;
CREATE TABLE `b2b_sources` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `note` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `status` tinyint DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `creator_id` int DEFAULT NULL,
  `updator_id` int DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of b2b_sources
-- ----------------------------
BEGIN;
COMMIT;

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
-- Records of branch_has_user
-- ----------------------------
BEGIN;
INSERT INTO `branch_has_user` (`branch_id`, `user_id`) VALUES (9, 1);
INSERT INTO `branch_has_user` (`branch_id`, `user_id`) VALUES (10, 1);
INSERT INTO `branch_has_user` (`branch_id`, `user_id`) VALUES (11, 1);
INSERT INTO `branch_has_user` (`branch_id`, `user_id`) VALUES (12, 1);
COMMIT;

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
-- Records of branch_transfer
-- ----------------------------
BEGIN;
COMMIT;

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
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of branches
-- ----------------------------
BEGIN;
INSERT INTO `branches` (`id`, `name`, `code`, `accounting_id`, `address`, `hrm_id`, `area_id`, `zone_id`, `region_id`, `opened_date`, `created_at`, `updated_at`, `creator_id`, `updator_id`, `status`, `branch_code`, `updated_last_date`, `hotline`, `id_lms`, `email`, `placeID`) VALUES (9, 'Cơ sở Hà Đông', 'HĐ', NULL, NULL, NULL, NULL, NULL, NULL, '2025-02-17', '2025-02-17 15:01:04', '2026-01-14 14:34:09', 1, 1, 1, NULL, NULL, NULL, NULL, 'loghanam@logiclab.vn', NULL);
INSERT INTO `branches` (`id`, `name`, `code`, `accounting_id`, `address`, `hrm_id`, `area_id`, `zone_id`, `region_id`, `opened_date`, `created_at`, `updated_at`, `creator_id`, `updator_id`, `status`, `branch_code`, `updated_last_date`, `hotline`, `id_lms`, `email`, `placeID`) VALUES (10, 'Cơ sở Thương Mại', 'TM', NULL, NULL, NULL, NULL, NULL, NULL, '2025-03-04', '2025-03-26 18:47:31', '2026-01-14 14:35:49', 1, 1, 1, NULL, NULL, NULL, NULL, 'loghanam@logiclab.vn', NULL);
INSERT INTO `branches` (`id`, `name`, `code`, `accounting_id`, `address`, `hrm_id`, `area_id`, `zone_id`, `region_id`, `opened_date`, `created_at`, `updated_at`, `creator_id`, `updator_id`, `status`, `branch_code`, `updated_last_date`, `hotline`, `id_lms`, `email`, `placeID`) VALUES (11, 'Cơ sở Đống Đa 1', 'DĐ1', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-03-26 18:48:16', '2026-01-14 14:36:35', 1, 1, 1, NULL, NULL, NULL, NULL, '123@gmail.com', NULL);
INSERT INTO `branches` (`id`, `name`, `code`, `accounting_id`, `address`, `hrm_id`, `area_id`, `zone_id`, `region_id`, `opened_date`, `created_at`, `updated_at`, `creator_id`, `updator_id`, `status`, `branch_code`, `updated_last_date`, `hotline`, `id_lms`, `email`, `placeID`) VALUES (12, 'Cơ sở Trần Quốc Hoàn', 'TQH', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-26 16:39:11', '2026-01-14 14:37:04', 1, 1, 1, NULL, NULL, NULL, NULL, '123@gmail.com', NULL);
COMMIT;

-- ----------------------------
-- Table structure for c2c_campaigns
-- ----------------------------
DROP TABLE IF EXISTS `c2c_campaigns`;
CREATE TABLE `c2c_campaigns` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `note` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `creator_id` int DEFAULT NULL,
  `updator_id` int DEFAULT NULL,
  `status` tinyint DEFAULT NULL,
  `list_tuition_fee` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meta_data` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of c2c_campaigns
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for campaigns
-- ----------------------------
DROP TABLE IF EXISTS `campaigns`;
CREATE TABLE `campaigns` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` int DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `amount` int DEFAULT NULL,
  `voucher_amount` int DEFAULT NULL,
  `voucher_num` int DEFAULT NULL,
  `voucher_bonus_sessions` int DEFAULT NULL,
  `note` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `creator_id` int DEFAULT NULL,
  `updator_id` int DEFAULT NULL,
  `status` tinyint DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of campaigns
-- ----------------------------
BEGIN;
COMMIT;

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
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of class_transfer
-- ----------------------------
BEGIN;
COMMIT;

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
  `ta_id` int DEFAULT NULL,
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
  `type_fee` int DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `program_id` (`program_id`) USING BTREE,
  KEY `product_id` (`product_id`) USING BTREE,
  KEY `branch_id` (`branch_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=42 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of classes
-- ----------------------------
BEGIN;
COMMIT;

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
  `init_total_charged` int DEFAULT NULL COMMENT 'Số tiền đã đóng lúc đầu',
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
  `b2b_campaign_id` int DEFAULT NULL,
  `b2b_amount` int DEFAULT NULL,
  `b2b_bonus_session` int DEFAULT NULL,
  `agreement_id` int DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `ec_id` (`ec_id`) USING BTREE,
  KEY `tuition_fee_id` (`tuition_fee_id`) USING BTREE,
  KEY `creator_id` (`creator_id`) USING BTREE,
  KEY `cm_id` (`cm_id`) USING BTREE,
  KEY `student_id` (`student_id`) USING BTREE,
  KEY `branch_id` (`branch_id`) USING BTREE,
  KEY `ceo_branch_id` (`ceo_branch_id`) USING BTREE,
  KEY `agreement_id` (`agreement_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=565 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of contracts
-- ----------------------------
BEGIN;
INSERT INTO `contracts` (`id`, `type`, `code`, `student_id`, `reference_contract_id`, `transfer_to_contract_id`, `branch_id`, `ceo_branch_id`, `ec_id`, `ec_leader_id`, `cm_id`, `cm_leader_id`, `product_id`, `program_id`, `tuition_fee_id`, `init_tuition_fee_id`, `init_tuition_fee_amount`, `init_tuition_fee_receivable`, `init_tuition_fee_session`, `init_total_charged`, `must_charge`, `total_charged`, `debt_amount`, `total_discount`, `discount_code`, `discount_code_id`, `discount_code_amount`, `discount_code_percent`, `coupon_code`, `coupon_amount`, `coupon_session`, `total_sessions`, `real_sessions`, `bonus_sessions`, `summary_sessions`, `reserved_sessions`, `reservable_sessions`, `done_sessions`, `left_sessions`, `last_done_sessions`, `description`, `bill_info`, `start_date`, `end_date`, `class_id`, `enrolment_start_date`, `enrolment_last_date`, `status`, `created_at`, `creator_id`, `updated_at`, `updator_id`, `note`, `action`, `count_recharge`, `type_withdraw`, `completed_date`, `renewed_date`, `success_renewed_date`, `renew_runtime`, `b2b_campaign_id`, `b2b_amount`, `b2b_bonus_session`, `agreement_id`) VALUES (563, 1, 'C000563', 541, NULL, NULL, 9, NULL, 1, NULL, NULL, NULL, 25, NULL, 38, 38, 2218000, NULL, 14, 2218000, 2218000, 2218000, 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, 14, 14, 0, 14, 0, 0, 0, 14, 0, NULL, NULL, '2026-01-01', NULL, NULL, NULL, NULL, 3, '2026-01-14 22:15:22', 1, '2026-01-14 22:28:45', 0, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 7);
INSERT INTO `contracts` (`id`, `type`, `code`, `student_id`, `reference_contract_id`, `transfer_to_contract_id`, `branch_id`, `ceo_branch_id`, `ec_id`, `ec_leader_id`, `cm_id`, `cm_leader_id`, `product_id`, `program_id`, `tuition_fee_id`, `init_tuition_fee_id`, `init_tuition_fee_amount`, `init_tuition_fee_receivable`, `init_tuition_fee_session`, `init_total_charged`, `must_charge`, `total_charged`, `debt_amount`, `total_discount`, `discount_code`, `discount_code_id`, `discount_code_amount`, `discount_code_percent`, `coupon_code`, `coupon_amount`, `coupon_session`, `total_sessions`, `real_sessions`, `bonus_sessions`, `summary_sessions`, `reserved_sessions`, `reservable_sessions`, `done_sessions`, `left_sessions`, `last_done_sessions`, `description`, `bill_info`, `start_date`, `end_date`, `class_id`, `enrolment_start_date`, `enrolment_last_date`, `status`, `created_at`, `creator_id`, `updated_at`, `updator_id`, `note`, `action`, `count_recharge`, `type_withdraw`, `completed_date`, `renewed_date`, `success_renewed_date`, `renew_runtime`, `b2b_campaign_id`, `b2b_amount`, `b2b_bonus_session`, `agreement_id`) VALUES (564, 1, 'C000564', 541, NULL, NULL, 9, NULL, 1, NULL, NULL, NULL, 26, NULL, 39, 39, 3718000, NULL, 25, 3718000, 3718000, 3718000, 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, 25, 25, 0, 25, 0, 0, 0, 25, 0, NULL, NULL, '2026-01-01', NULL, NULL, NULL, NULL, 3, '2026-01-14 22:15:22', 1, '2026-01-14 22:28:46', 0, NULL, NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 7);
COMMIT;

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
-- Records of coupon_logs
-- ----------------------------
BEGIN;
COMMIT;

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
  `campaign_id` int DEFAULT NULL,
  `source_id` int DEFAULT NULL,
  `c2c_mobile` varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `c2c_contract_id` int DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `code` (`code`) USING BTREE,
  KEY `campaign_id` (`campaign_id`) USING BTREE,
  KEY `c2c_mobile` (`c2c_mobile`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=344 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of coupons
-- ----------------------------
BEGIN;
COMMIT;

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
-- Records of crm_contact_methods
-- ----------------------------
BEGIN;
COMMIT;

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
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- ----------------------------
-- Records of crm_customer_care
-- ----------------------------
BEGIN;
COMMIT;

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
  `link_facebook` varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `import_id` (`import_id`) USING BTREE,
  KEY `gud_mobile1` (`gud_mobile1`) USING BTREE,
  KEY `gud_mobile2` (`gud_mobile2`) USING BTREE,
  KEY `parent_id` (`parent_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=47 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- ----------------------------
-- Records of crm_import_parents
-- ----------------------------
BEGIN;
COMMIT;

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
) ENGINE=InnoDB AUTO_INCREMENT=10132 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- ----------------------------
-- Records of crm_imports
-- ----------------------------
BEGIN;
COMMIT;

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
) ENGINE=InnoDB AUTO_INCREMENT=133 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- ----------------------------
-- Records of crm_parent_logs
-- ----------------------------
BEGIN;
INSERT INTO `crm_parent_logs` (`id`, `parent_id`, `content`, `created_at`, `creator_id`, `status`) VALUES (132, 540, 'Khởi tạo khách hàng thủ công', '2026-01-14 22:14:53', 1, 1);
COMMIT;

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
-- Records of crm_parent_overwrite
-- ----------------------------
BEGIN;
COMMIT;

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
  `avatar_url` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `link_facebook` varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `student_id` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`,`student_id`) USING BTREE,
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
  KEY `care_date` (`care_date`) USING BTREE,
  KEY `student_id` (`student_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=541 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- ----------------------------
-- Records of crm_parents
-- ----------------------------
BEGIN;
INSERT INTO `crm_parents` (`id`, `name`, `email`, `mobile_1`, `mobile_2`, `address`, `province_id`, `district_id`, `created_at`, `creator_id`, `gender`, `birthday`, `job_id`, `source_detail_id`, `source_id`, `note`, `status`, `last_assign_date`, `owner_id`, `branch_id`, `updated_at`, `updator_id`, `is_lock`, `care_date`, `next_care_date`, `c2c_mobile`, `tmp_branch_id`, `is_update`, `old_status`, `last_ticket_date`, `level`, `avatar_url`, `link_facebook`, `student_id`) VALUES (540, 'Nguyễn Văn A', NULL, '0389941902', NULL, NULL, 12, 999, '2026-01-14 22:14:53', 1, 'M', NULL, NULL, NULL, 4, NULL, 0, '2026-01-14 22:14:53', 1, 9, NULL, NULL, 1, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 541);
COMMIT;

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
) ENGINE=InnoDB AUTO_INCREMENT=542 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- ----------------------------
-- Records of crm_students
-- ----------------------------
BEGIN;
COMMIT;

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
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- ----------------------------
-- Records of crm_tickets
-- ----------------------------
BEGIN;
COMMIT;

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
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- ----------------------------
-- Records of discount_codes
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for districts
-- ----------------------------
DROP TABLE IF EXISTS `districts`;
CREATE TABLE `districts` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `province_id` int DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `accounting_id` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `province_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=3322 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of districts
-- ----------------------------
BEGIN;
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1, 'An Biên', 1, NULL, NULL, NULL, 'An Giang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2, 'An Châu', 1, NULL, NULL, NULL, 'An Giang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3, 'An Cư', 1, NULL, NULL, NULL, 'An Giang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (4, 'An Minh', 1, NULL, NULL, NULL, 'An Giang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (5, 'An Phú', 1, NULL, NULL, NULL, 'An Giang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (6, 'Ba Chúc', 1, NULL, NULL, NULL, 'An Giang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (7, 'Bình An', 1, NULL, NULL, NULL, 'An Giang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (8, 'Bình Đức', 1, NULL, NULL, NULL, 'An Giang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (9, 'Bình Giang', 1, NULL, NULL, NULL, 'An Giang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (10, 'Bình Hòa', 1, NULL, NULL, NULL, 'An Giang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (11, 'Bình Mỹ', 1, NULL, NULL, NULL, 'An Giang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (12, 'Bình Sơn', 1, NULL, NULL, NULL, 'An Giang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (13, 'Bình Thạnh Đông', 1, NULL, NULL, NULL, 'An Giang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (14, 'Cần Đăng', 1, NULL, NULL, NULL, 'An Giang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (15, 'Châu Đốc', 1, NULL, NULL, NULL, 'An Giang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (16, 'Châu Phong', 1, NULL, NULL, NULL, 'An Giang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (17, 'Châu Phú', 1, NULL, NULL, NULL, 'An Giang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (18, 'Châu Thành', 1, NULL, NULL, NULL, 'An Giang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (19, 'Chi Lăng', 1, NULL, NULL, NULL, 'An Giang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (20, 'Chợ Mới', 1, NULL, NULL, NULL, 'An Giang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (21, 'Chợ Vàm', 1, NULL, NULL, NULL, 'An Giang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (22, 'Cô Tô', 1, NULL, NULL, NULL, 'An Giang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (23, 'Cù Lao Giêng', 1, NULL, NULL, NULL, 'An Giang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (24, 'Định Hòa', 1, NULL, NULL, NULL, 'An Giang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (25, 'Định Mỹ', 1, NULL, NULL, NULL, 'An Giang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (26, 'Đông Hòa', 1, NULL, NULL, NULL, 'An Giang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (27, 'Đông Hưng', 1, NULL, NULL, NULL, 'An Giang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (28, 'Đông Thái', 1, NULL, NULL, NULL, 'An Giang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (29, 'Giang Thành', 1, NULL, NULL, NULL, 'An Giang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (30, 'Giồng Riềng', 1, NULL, NULL, NULL, 'An Giang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (31, 'Gò Quao', 1, NULL, NULL, NULL, 'An Giang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (32, 'Hà Tiên', 1, NULL, NULL, NULL, 'An Giang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (33, 'Hòa Điền', 1, NULL, NULL, NULL, 'An Giang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (34, 'Hòa Hưng', 1, NULL, NULL, NULL, 'An Giang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (35, 'Hòa Lạc', 1, NULL, NULL, NULL, 'An Giang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (36, 'Hòa Thuận', 1, NULL, NULL, NULL, 'An Giang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (37, 'Hòn Đất', 1, NULL, NULL, NULL, 'An Giang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (38, 'Hòn Nghệ', 1, NULL, NULL, NULL, 'An Giang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (39, 'Hội An', 1, NULL, NULL, NULL, 'An Giang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (40, 'Khánh Bình', 1, NULL, NULL, NULL, 'An Giang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (41, 'Kiên Hải', 1, NULL, NULL, NULL, 'An Giang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (42, 'Kiên Lương', 1, NULL, NULL, NULL, 'An Giang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (43, 'Long Điền', 1, NULL, NULL, NULL, 'An Giang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (44, 'Long Kiến', 1, NULL, NULL, NULL, 'An Giang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (45, 'Long Phú', 1, NULL, NULL, NULL, 'An Giang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (46, 'Long Thạnh', 1, NULL, NULL, NULL, 'An Giang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (47, 'Long Xuyên', 1, NULL, NULL, NULL, 'An Giang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (48, 'Mỹ Đức', 1, NULL, NULL, NULL, 'An Giang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (49, 'Mỹ Hòa Hưng', 1, NULL, NULL, NULL, 'An Giang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (50, 'Mỹ Thới', 1, NULL, NULL, NULL, 'An Giang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (51, 'Mỹ Thuận', 1, NULL, NULL, NULL, 'An Giang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (52, 'Ngọc Chúc', 1, NULL, NULL, NULL, 'An Giang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (53, 'Nhơn Hội', 1, NULL, NULL, NULL, 'An Giang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (54, 'Nhơn Mỹ', 1, NULL, NULL, NULL, 'An Giang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (55, 'Núi Cấm', 1, NULL, NULL, NULL, 'An Giang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (56, 'Óc Eo', 1, NULL, NULL, NULL, 'An Giang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (57, 'Ô Lâm', 1, NULL, NULL, NULL, 'An Giang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (58, 'Phú An', 1, NULL, NULL, NULL, 'An Giang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (59, 'Phú Hòa', 1, NULL, NULL, NULL, 'An Giang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (60, 'Phú Hữu', 1, NULL, NULL, NULL, 'An Giang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (61, 'Phú Lâm', 1, NULL, NULL, NULL, 'An Giang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (62, 'Phú Quốc', 1, NULL, NULL, NULL, 'An Giang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (63, 'Phú Tân', 1, NULL, NULL, NULL, 'An Giang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (64, 'Rạch Giá', 1, NULL, NULL, NULL, 'An Giang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (65, 'Sơn Hải', 1, NULL, NULL, NULL, 'An Giang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (66, 'Sơn Kiên', 1, NULL, NULL, NULL, 'An Giang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (67, 'Tân An', 1, NULL, NULL, NULL, 'An Giang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (68, 'Tân Châu', 1, NULL, NULL, NULL, 'An Giang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (69, 'Tân Hiệp', 1, NULL, NULL, NULL, 'An Giang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (70, 'Tân Hội', 1, NULL, NULL, NULL, 'An Giang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (71, 'Tân Thạnh', 1, NULL, NULL, NULL, 'An Giang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (72, 'Tây Phú', 1, NULL, NULL, NULL, 'An Giang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (73, 'Tây Yên', 1, NULL, NULL, NULL, 'An Giang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (74, 'Thạnh Đông', 1, NULL, NULL, NULL, 'An Giang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (75, 'Thạnh Hưng', 1, NULL, NULL, NULL, 'An Giang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (76, 'Thạnh Lộc', 1, NULL, NULL, NULL, 'An Giang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (77, 'Thạnh Mỹ Tây', 1, NULL, NULL, NULL, 'An Giang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (78, 'Thoại Sơn', 1, NULL, NULL, NULL, 'An Giang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (79, 'Thổ Châu', 1, NULL, NULL, NULL, 'An Giang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (80, 'Thới Sơn', 1, NULL, NULL, NULL, 'An Giang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (81, 'Tiên Hải', 1, NULL, NULL, NULL, 'An Giang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (82, 'Tịnh Biên', 1, NULL, NULL, NULL, 'An Giang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (83, 'Tô Châu', 1, NULL, NULL, NULL, 'An Giang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (84, 'Tri Tôn', 1, NULL, NULL, NULL, 'An Giang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (85, 'U Minh Thượng', 1, NULL, NULL, NULL, 'An Giang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (86, 'Vân Khánh', 1, NULL, NULL, NULL, 'An Giang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (87, 'Vĩnh An', 1, NULL, NULL, NULL, 'An Giang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (88, 'Vĩnh Bình', 1, NULL, NULL, NULL, 'An Giang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (89, 'Vĩnh Điều', 1, NULL, NULL, NULL, 'An Giang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (90, 'Vĩnh Gia', 1, NULL, NULL, NULL, 'An Giang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (91, 'Vĩnh Hanh', 1, NULL, NULL, NULL, 'An Giang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (92, 'Vĩnh Hậu', 1, NULL, NULL, NULL, 'An Giang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (93, 'Vĩnh Hòa', 1, NULL, NULL, NULL, 'An Giang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (94, 'Vĩnh Hòa Hưng', 1, NULL, NULL, NULL, 'An Giang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (95, 'Vĩnh Phong', 1, NULL, NULL, NULL, 'An Giang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (96, 'Vĩnh Tế', 1, NULL, NULL, NULL, 'An Giang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (97, 'Vĩnh Thạnh Trung', 1, NULL, NULL, NULL, 'An Giang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (98, 'Vĩnh Thông', 1, NULL, NULL, NULL, 'An Giang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (99, 'Vĩnh Thuận', 1, NULL, NULL, NULL, 'An Giang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (100, 'Vĩnh Trạch', 1, NULL, NULL, NULL, 'An Giang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (101, 'Vĩnh Tuy', 1, NULL, NULL, NULL, 'An Giang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (102, 'Vĩnh Xương', 1, NULL, NULL, NULL, 'An Giang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (103, 'An Lạc', 2, NULL, NULL, NULL, 'Bắc Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (104, 'Bảo Đài', 2, NULL, NULL, NULL, 'Bắc Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (105, 'Bắc Giang', 2, NULL, NULL, NULL, 'Bắc Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (106, 'Bắc Lũng', 2, NULL, NULL, NULL, 'Bắc Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (107, 'Biển Động', 2, NULL, NULL, NULL, 'Bắc Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (108, 'Biên Sơn', 2, NULL, NULL, NULL, 'Bắc Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (109, 'Bố Hạ', 2, NULL, NULL, NULL, 'Bắc Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (110, 'Bồng Lai', 2, NULL, NULL, NULL, 'Bắc Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (111, 'Cảnh Thụy', 2, NULL, NULL, NULL, 'Bắc Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (112, 'Cao Đức', 2, NULL, NULL, NULL, 'Bắc Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (113, 'Cẩm Lý', 2, NULL, NULL, NULL, 'Bắc Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (114, 'Chi Lăng', 2, NULL, NULL, NULL, 'Bắc Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (115, 'Chũ', 2, NULL, NULL, NULL, 'Bắc Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (116, 'Dương Hưu', 2, NULL, NULL, NULL, 'Bắc Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (117, 'Đa Mai', 2, NULL, NULL, NULL, 'Bắc Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (118, 'Đại Đồng', 2, NULL, NULL, NULL, 'Bắc Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (119, 'Đại Lai', 2, NULL, NULL, NULL, 'Bắc Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (120, 'Đại Sơn', 2, NULL, NULL, NULL, 'Bắc Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (121, 'Đào Viên', 2, NULL, NULL, NULL, 'Bắc Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (122, 'Đèo Gia', 2, NULL, NULL, NULL, 'Bắc Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (123, 'Đông Cứu', 2, NULL, NULL, NULL, 'Bắc Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (124, 'Đồng Kỳ', 2, NULL, NULL, NULL, 'Bắc Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (125, 'Đồng Nguyên', 2, NULL, NULL, NULL, 'Bắc Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (126, 'Đông Phú', 2, NULL, NULL, NULL, 'Bắc Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (127, 'Đồng Việt', 2, NULL, NULL, NULL, 'Bắc Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (128, 'Gia Bình', 2, NULL, NULL, NULL, 'Bắc Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (129, 'Hạp Lĩnh', 2, NULL, NULL, NULL, 'Bắc Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (130, 'Hiệp Hòa', 2, NULL, NULL, NULL, 'Bắc Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (131, 'Hoàng Vân', 2, NULL, NULL, NULL, 'Bắc Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (132, 'Hợp Thịnh', 2, NULL, NULL, NULL, 'Bắc Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (133, 'Kép', 2, NULL, NULL, NULL, 'Bắc Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (134, 'Kiên Lao', 2, NULL, NULL, NULL, 'Bắc Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (135, 'Kinh Bắc', 2, NULL, NULL, NULL, 'Bắc Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (136, 'Lạng Giang', 2, NULL, NULL, NULL, 'Bắc Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (137, 'Lâm Thao', 2, NULL, NULL, NULL, 'Bắc Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (138, 'Liên Bão', 2, NULL, NULL, NULL, 'Bắc Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (139, 'Lục Nam', 2, NULL, NULL, NULL, 'Bắc Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (140, 'Lục Ngạn', 2, NULL, NULL, NULL, 'Bắc Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (141, 'Lục Sơn', 2, NULL, NULL, NULL, 'Bắc Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (142, 'Lương Tài', 2, NULL, NULL, NULL, 'Bắc Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (143, 'Mão Điền', 2, NULL, NULL, NULL, 'Bắc Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (144, 'Mỹ Thái', 2, NULL, NULL, NULL, 'Bắc Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (145, 'Nam Dương', 2, NULL, NULL, NULL, 'Bắc Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (146, 'Nam Sơn', 2, NULL, NULL, NULL, 'Bắc Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (147, 'Nếnh', 2, NULL, NULL, NULL, 'Bắc Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (148, 'Nghĩa Phương', 2, NULL, NULL, NULL, 'Bắc Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (149, 'Ngọc Thiện', 2, NULL, NULL, NULL, 'Bắc Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (150, 'Nhã Nam', 2, NULL, NULL, NULL, 'Bắc Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (151, 'Nhân Hòa', 2, NULL, NULL, NULL, 'Bắc Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (152, 'Nhân Thắng', 2, NULL, NULL, NULL, 'Bắc Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (153, 'Ninh Xá', 2, NULL, NULL, NULL, 'Bắc Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (154, 'Phật Tích', 2, NULL, NULL, NULL, 'Bắc Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (155, 'Phù Khê', 2, NULL, NULL, NULL, 'Bắc Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (156, 'Phù Lãng', 2, NULL, NULL, NULL, 'Bắc Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (157, 'Phúc Hoà', 2, NULL, NULL, NULL, 'Bắc Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (158, 'Phương Liễu', 2, NULL, NULL, NULL, 'Bắc Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (159, 'Phượng Sơn', 2, NULL, NULL, NULL, 'Bắc Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (160, 'Quang Trung', 2, NULL, NULL, NULL, 'Bắc Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (161, 'Quế Võ', 2, NULL, NULL, NULL, 'Bắc Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (162, 'Sa Lý', 2, NULL, NULL, NULL, 'Bắc Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (163, 'Song Liễu', 2, NULL, NULL, NULL, 'Bắc Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (164, 'Sơn Động', 2, NULL, NULL, NULL, 'Bắc Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (165, 'Sơn Hải', 2, NULL, NULL, NULL, 'Bắc Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (166, 'Tam Đa', 2, NULL, NULL, NULL, 'Bắc Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (167, 'Tam Giang', 2, NULL, NULL, NULL, 'Bắc Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (168, 'Tam Sơn', 2, NULL, NULL, NULL, 'Bắc Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (169, 'Tam Tiến', 2, NULL, NULL, NULL, 'Bắc Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (170, 'Tân An', 2, NULL, NULL, NULL, 'Bắc Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (171, 'Tân Chi', 2, NULL, NULL, NULL, 'Bắc Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (172, 'Tân Dĩnh', 2, NULL, NULL, NULL, 'Bắc Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (173, 'Tân Sơn', 2, NULL, NULL, NULL, 'Bắc Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (174, 'Tân Tiến', 2, NULL, NULL, NULL, 'Bắc Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (175, 'Tân Yên', 2, NULL, NULL, NULL, 'Bắc Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (176, 'Tây Yên Tử', 2, NULL, NULL, NULL, 'Bắc Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (177, 'Thuận Thành', 2, NULL, NULL, NULL, 'Bắc Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (178, 'Tiên Du', 2, NULL, NULL, NULL, 'Bắc Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (179, 'Tiên Lục', 2, NULL, NULL, NULL, 'Bắc Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (180, 'Tiền Phong', 2, NULL, NULL, NULL, 'Bắc Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (181, 'Trạm Lộ', 2, NULL, NULL, NULL, 'Bắc Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (182, 'Trí Quả', 2, NULL, NULL, NULL, 'Bắc Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (183, 'Trung Chính', 2, NULL, NULL, NULL, 'Bắc Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (184, 'Trung Kênh', 2, NULL, NULL, NULL, 'Bắc Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (185, 'Trường Sơn', 2, NULL, NULL, NULL, 'Bắc Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (186, 'Tuấn Đạo', 2, NULL, NULL, NULL, 'Bắc Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (187, 'Tự Lạn', 2, NULL, NULL, NULL, 'Bắc Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (188, 'Từ Sơn', 2, NULL, NULL, NULL, 'Bắc Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (189, 'Văn Môn', 2, NULL, NULL, NULL, 'Bắc Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (190, 'Vân Hà', 2, NULL, NULL, NULL, 'Bắc Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (191, 'Vân Sơn', 2, NULL, NULL, NULL, 'Bắc Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (192, 'Việt Yên', 2, NULL, NULL, NULL, 'Bắc Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (193, 'Võ Cường', 2, NULL, NULL, NULL, 'Bắc Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (194, 'Vũ Ninh', 2, NULL, NULL, NULL, 'Bắc Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (195, 'Xuân Cẩm', 2, NULL, NULL, NULL, 'Bắc Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (196, 'Xuân Lương', 2, NULL, NULL, NULL, 'Bắc Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (197, 'Yên Dũng', 2, NULL, NULL, NULL, 'Bắc Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (198, 'Yên Định', 2, NULL, NULL, NULL, 'Bắc Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (199, 'Yên Phong', 2, NULL, NULL, NULL, 'Bắc Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (200, 'Yên Thế', 2, NULL, NULL, NULL, 'Bắc Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (201, 'Yên Trung', 2, NULL, NULL, NULL, 'Bắc Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (202, 'An Trạch', 3, NULL, NULL, NULL, 'Cà Mau');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (203, 'An Xuyên', 3, NULL, NULL, NULL, 'Cà Mau');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (204, 'Bạc Liêu', 3, NULL, NULL, NULL, 'Cà Mau');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (205, 'Biển Bạch', 3, NULL, NULL, NULL, 'Cà Mau');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (206, 'Cái Đôi Vàm', 3, NULL, NULL, NULL, 'Cà Mau');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (207, 'Cái Nước', 3, NULL, NULL, NULL, 'Cà Mau');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (208, 'Châu Thới', 3, NULL, NULL, NULL, 'Cà Mau');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (209, 'Đá Bạc', 3, NULL, NULL, NULL, 'Cà Mau');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (210, 'Đầm Dơi', 3, NULL, NULL, NULL, 'Cà Mau');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (211, 'Đất Mới', 3, NULL, NULL, NULL, 'Cà Mau');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (212, 'Đất Mũi', 3, NULL, NULL, NULL, 'Cà Mau');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (213, 'Định Thành', 3, NULL, NULL, NULL, 'Cà Mau');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (214, 'Đông Hải', 3, NULL, NULL, NULL, 'Cà Mau');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (215, 'Gành Hào', 3, NULL, NULL, NULL, 'Cà Mau');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (216, 'Giá Rai', 3, NULL, NULL, NULL, 'Cà Mau');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (217, 'Hiệp Thành', 3, NULL, NULL, NULL, 'Cà Mau');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (218, 'Hoà Bình', 3, NULL, NULL, NULL, 'Cà Mau');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (219, 'Hoà Thành', 3, NULL, NULL, NULL, 'Cà Mau');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (220, 'Hồ Thị Kỷ', 3, NULL, NULL, NULL, 'Cà Mau');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (221, 'Hồng Dân', 3, NULL, NULL, NULL, 'Cà Mau');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (222, 'Hưng Hội', 3, NULL, NULL, NULL, 'Cà Mau');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (223, 'Hưng Mỹ', 3, NULL, NULL, NULL, 'Cà Mau');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (224, 'Khánh An', 3, NULL, NULL, NULL, 'Cà Mau');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (225, 'Khánh Bình', 3, NULL, NULL, NULL, 'Cà Mau');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (226, 'Khánh Hưng', 3, NULL, NULL, NULL, 'Cà Mau');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (227, 'Khánh Lâm', 3, NULL, NULL, NULL, 'Cà Mau');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (228, 'Láng Tròn', 3, NULL, NULL, NULL, 'Cà Mau');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (229, 'Long Điền', 3, NULL, NULL, NULL, 'Cà Mau');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (230, 'Lương Thế Trân', 3, NULL, NULL, NULL, 'Cà Mau');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (231, 'Lý Văn Lâm', 3, NULL, NULL, NULL, 'Cà Mau');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (232, 'Năm Căn', 3, NULL, NULL, NULL, 'Cà Mau');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (233, 'Nguyễn Phích', 3, NULL, NULL, NULL, 'Cà Mau');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (234, 'Nguyễn Việt Khái', 3, NULL, NULL, NULL, 'Cà Mau');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (235, 'Ninh Quới', 3, NULL, NULL, NULL, 'Cà Mau');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (236, 'Ninh Thạnh Lợi', 3, NULL, NULL, NULL, 'Cà Mau');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (237, 'Phan Ngọc Hiển', 3, NULL, NULL, NULL, 'Cà Mau');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (238, 'Phong Hiệp', 3, NULL, NULL, NULL, 'Cà Mau');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (239, 'Phong Thạnh', 3, NULL, NULL, NULL, 'Cà Mau');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (240, 'Phú Mỹ', 3, NULL, NULL, NULL, 'Cà Mau');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (241, 'Phú Tân', 3, NULL, NULL, NULL, 'Cà Mau');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (242, 'Phước Long', 3, NULL, NULL, NULL, 'Cà Mau');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (243, 'Quách Phẩm', 3, NULL, NULL, NULL, 'Cà Mau');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (244, 'Sông Đốc', 3, NULL, NULL, NULL, 'Cà Mau');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (245, 'Tạ An Khương', 3, NULL, NULL, NULL, 'Cà Mau');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (246, 'Tam Giang', 3, NULL, NULL, NULL, 'Cà Mau');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (247, 'Tân Ân', 3, NULL, NULL, NULL, 'Cà Mau');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (248, 'Tân Hưng', 3, NULL, NULL, NULL, 'Cà Mau');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (249, 'Tân Lộc', 3, NULL, NULL, NULL, 'Cà Mau');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (250, 'Tân Thành', 3, NULL, NULL, NULL, 'Cà Mau');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (251, 'Tân Thuận', 3, NULL, NULL, NULL, 'Cà Mau');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (252, 'Tân Tiến', 3, NULL, NULL, NULL, 'Cà Mau');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (253, 'Thanh Tùng', 3, NULL, NULL, NULL, 'Cà Mau');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (254, 'Thới Bình', 3, NULL, NULL, NULL, 'Cà Mau');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (255, 'Trần Phán', 3, NULL, NULL, NULL, 'Cà Mau');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (256, 'Trần Văn Thời', 3, NULL, NULL, NULL, 'Cà Mau');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (257, 'Trí Phải', 3, NULL, NULL, NULL, 'Cà Mau');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (258, 'U Minh', 3, NULL, NULL, NULL, 'Cà Mau');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (259, 'Vĩnh Hậu', 3, NULL, NULL, NULL, 'Cà Mau');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (260, 'Vĩnh Lộc', 3, NULL, NULL, NULL, 'Cà Mau');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (261, 'Vĩnh Lợi', 3, NULL, NULL, NULL, 'Cà Mau');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (262, 'Vĩnh Mỹ', 3, NULL, NULL, NULL, 'Cà Mau');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (263, 'Vĩnh Phước', 3, NULL, NULL, NULL, 'Cà Mau');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (264, 'Vĩnh Thanh', 3, NULL, NULL, NULL, 'Cà Mau');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (265, 'Vĩnh Trạch', 3, NULL, NULL, NULL, 'Cà Mau');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (266, 'Bạch Đằng', 4, NULL, NULL, NULL, 'Cao Bằng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (267, 'Bảo Lạc', 4, NULL, NULL, NULL, 'Cao Bằng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (268, 'Bảo Lâm', 4, NULL, NULL, NULL, 'Cao Bằng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (269, 'Bế Văn Đàn', 4, NULL, NULL, NULL, 'Cao Bằng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (270, 'Ca Thành', 4, NULL, NULL, NULL, 'Cao Bằng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (271, 'Canh Tân', 4, NULL, NULL, NULL, 'Cao Bằng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (272, 'Cần Yên', 4, NULL, NULL, NULL, 'Cao Bằng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (273, 'Cô Ba', 4, NULL, NULL, NULL, 'Cao Bằng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (274, 'Cốc Pàng', 4, NULL, NULL, NULL, 'Cao Bằng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (275, 'Đàm Thủy', 4, NULL, NULL, NULL, 'Cao Bằng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (276, 'Đình Phong', 4, NULL, NULL, NULL, 'Cao Bằng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (277, 'Đoài Dương', 4, NULL, NULL, NULL, 'Cao Bằng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (278, 'Độc Lập', 4, NULL, NULL, NULL, 'Cao Bằng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (279, 'Đông Khê', 4, NULL, NULL, NULL, 'Cao Bằng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (280, 'Đức Long', 4, NULL, NULL, NULL, 'Cao Bằng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (281, 'Hạ Lang', 4, NULL, NULL, NULL, 'Cao Bằng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (282, 'Hà Quảng', 4, NULL, NULL, NULL, 'Cao Bằng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (283, 'Hạnh Phúc', 4, NULL, NULL, NULL, 'Cao Bằng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (284, 'Hòa An', 4, NULL, NULL, NULL, 'Cao Bằng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (285, 'Huy Giáp', 4, NULL, NULL, NULL, 'Cao Bằng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (286, 'Hưng Đạo', 4, NULL, NULL, NULL, 'Cao Bằng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (287, 'Khánh Xuân', 4, NULL, NULL, NULL, 'Cao Bằng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (288, 'Kim Đồng', 4, NULL, NULL, NULL, 'Cao Bằng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (289, 'Lũng Nặm', 4, NULL, NULL, NULL, 'Cao Bằng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (290, 'Lý Bôn', 4, NULL, NULL, NULL, 'Cao Bằng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (291, 'Lý Quốc', 4, NULL, NULL, NULL, 'Cao Bằng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (292, 'Minh Khai', 4, NULL, NULL, NULL, 'Cao Bằng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (293, 'Minh Tâm', 4, NULL, NULL, NULL, 'Cao Bằng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (294, 'Nam Quang', 4, NULL, NULL, NULL, 'Cao Bằng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (295, 'Nam Tuấn', 4, NULL, NULL, NULL, 'Cao Bằng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (296, 'Nguyên Bình', 4, NULL, NULL, NULL, 'Cao Bằng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (297, 'Nguyễn Huệ', 4, NULL, NULL, NULL, 'Cao Bằng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (298, 'Nùng Trí Cao', 4, NULL, NULL, NULL, 'Cao Bằng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (299, 'Phan Thanh', 4, NULL, NULL, NULL, 'Cao Bằng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (300, 'Phục Hòa', 4, NULL, NULL, NULL, 'Cao Bằng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (301, 'Quang Hán', 4, NULL, NULL, NULL, 'Cao Bằng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (302, 'Quảng Lâm', 4, NULL, NULL, NULL, 'Cao Bằng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (303, 'Quang Long', 4, NULL, NULL, NULL, 'Cao Bằng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (304, 'Quang Trung', 4, NULL, NULL, NULL, 'Cao Bằng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (305, 'Quảng Uyên', 4, NULL, NULL, NULL, 'Cao Bằng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (306, 'Sơn Lộ', 4, NULL, NULL, NULL, 'Cao Bằng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (307, 'Tam Kim', 4, NULL, NULL, NULL, 'Cao Bằng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (308, 'Tân Giang', 4, NULL, NULL, NULL, 'Cao Bằng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (309, 'Thạch An', 4, NULL, NULL, NULL, 'Cao Bằng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (310, 'Thành Công', 4, NULL, NULL, NULL, 'Cao Bằng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (311, 'Thanh Long', 4, NULL, NULL, NULL, 'Cao Bằng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (312, 'Thông Nông', 4, NULL, NULL, NULL, 'Cao Bằng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (313, 'Thục Phán', 4, NULL, NULL, NULL, 'Cao Bằng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (314, 'Tĩnh Túc', 4, NULL, NULL, NULL, 'Cao Bằng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (315, 'Tổng Cọt', 4, NULL, NULL, NULL, 'Cao Bằng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (316, 'Trà Lĩnh', 4, NULL, NULL, NULL, 'Cao Bằng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (317, 'Trùng Khánh', 4, NULL, NULL, NULL, 'Cao Bằng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (318, 'Trường Hà', 4, NULL, NULL, NULL, 'Cao Bằng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (319, 'Vinh Quý', 4, NULL, NULL, NULL, 'Cao Bằng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (320, 'Xuân Trường', 4, NULL, NULL, NULL, 'Cao Bằng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (321, 'Yên Thổ', 4, NULL, NULL, NULL, 'Cao Bằng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (322, 'An Bình', 5, NULL, NULL, NULL, 'Cần Thơ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (323, 'An Lạc Thôn', 5, NULL, NULL, NULL, 'Cần Thơ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (324, 'An Ninh', 5, NULL, NULL, NULL, 'Cần Thơ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (325, 'An Thạnh', 5, NULL, NULL, NULL, 'Cần Thơ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (326, 'Bình Thủy', 5, NULL, NULL, NULL, 'Cần Thơ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (327, 'Cái Khế', 5, NULL, NULL, NULL, 'Cần Thơ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (328, 'Cái Răng', 5, NULL, NULL, NULL, 'Cần Thơ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (329, 'Châu Thành', 5, NULL, NULL, NULL, 'Cần Thơ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (330, 'Cờ Đỏ', 5, NULL, NULL, NULL, 'Cần Thơ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (331, 'Cù Lao Dung', 5, NULL, NULL, NULL, 'Cần Thơ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (332, 'Đại Hải', 5, NULL, NULL, NULL, 'Cần Thơ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (333, 'Đại Ngãi', 5, NULL, NULL, NULL, 'Cần Thơ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (334, 'Đại Thành', 5, NULL, NULL, NULL, 'Cần Thơ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (335, 'Đông Hiệp', 5, NULL, NULL, NULL, 'Cần Thơ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (336, 'Đông Phước', 5, NULL, NULL, NULL, 'Cần Thơ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (337, 'Đông Thuận', 5, NULL, NULL, NULL, 'Cần Thơ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (338, 'Gia Hòa', 5, NULL, NULL, NULL, 'Cần Thơ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (339, 'Hiệp Hưng', 5, NULL, NULL, NULL, 'Cần Thơ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (340, 'Hòa An', 5, NULL, NULL, NULL, 'Cần Thơ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (341, 'Hỏa Lựu', 5, NULL, NULL, NULL, 'Cần Thơ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (342, 'Hòa Tú', 5, NULL, NULL, NULL, 'Cần Thơ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (343, 'Hồ Đắc Kiện', 5, NULL, NULL, NULL, 'Cần Thơ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (344, 'Hưng Phú', 5, NULL, NULL, NULL, 'Cần Thơ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (345, 'Kế Sách', 5, NULL, NULL, NULL, 'Cần Thơ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (346, 'Khánh Hòa', 5, NULL, NULL, NULL, 'Cần Thơ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (347, 'Lai Hòa', 5, NULL, NULL, NULL, 'Cần Thơ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (348, 'Lâm Tân', 5, NULL, NULL, NULL, 'Cần Thơ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (349, 'Lịch Hội Thượng', 5, NULL, NULL, NULL, 'Cần Thơ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (350, 'Liêu Tú', 5, NULL, NULL, NULL, 'Cần Thơ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (351, 'Long Bình', 5, NULL, NULL, NULL, 'Cần Thơ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (352, 'Long Hưng', 5, NULL, NULL, NULL, 'Cần Thơ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (353, 'Long Mỹ', 5, NULL, NULL, NULL, 'Cần Thơ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (354, 'Long Phú', 5, NULL, NULL, NULL, 'Cần Thơ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (355, 'Long Phú 1', 5, NULL, NULL, NULL, 'Cần Thơ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (356, 'Long Tuyền', 5, NULL, NULL, NULL, 'Cần Thơ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (357, 'Lương Tâm', 5, NULL, NULL, NULL, 'Cần Thơ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (358, 'Mỹ Hương', 5, NULL, NULL, NULL, 'Cần Thơ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (359, 'Mỹ Phước', 5, NULL, NULL, NULL, 'Cần Thơ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (360, 'Mỹ Quới', 5, NULL, NULL, NULL, 'Cần Thơ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (361, 'Mỹ Tú', 5, NULL, NULL, NULL, 'Cần Thơ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (362, 'Mỹ Xuyên', 5, NULL, NULL, NULL, 'Cần Thơ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (363, 'Ngã Bảy', 5, NULL, NULL, NULL, 'Cần Thơ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (364, 'Ngã Năm', 5, NULL, NULL, NULL, 'Cần Thơ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (365, 'Ngọc Tố', 5, NULL, NULL, NULL, 'Cần Thơ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (366, 'Nhơn Ái', 5, NULL, NULL, NULL, 'Cần Thơ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (367, 'Nhơn Mỹ', 5, NULL, NULL, NULL, 'Cần Thơ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (368, 'Nhu Gia', 5, NULL, NULL, NULL, 'Cần Thơ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (369, 'Ninh Kiều', 5, NULL, NULL, NULL, 'Cần Thơ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (370, 'Ô Môn', 5, NULL, NULL, NULL, 'Cần Thơ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (371, 'Phong Điền', 5, NULL, NULL, NULL, 'Cần Thơ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (372, 'Phong Nẫm', 5, NULL, NULL, NULL, 'Cần Thơ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (373, 'Phú Hữu', 5, NULL, NULL, NULL, 'Cần Thơ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (374, 'Phú Lộc', 5, NULL, NULL, NULL, 'Cần Thơ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (375, 'Phú Lợi', 5, NULL, NULL, NULL, 'Cần Thơ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (376, 'Phú Tâm', 5, NULL, NULL, NULL, 'Cần Thơ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (377, 'Phụng Hiệp', 5, NULL, NULL, NULL, 'Cần Thơ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (378, 'Phước Thới', 5, NULL, NULL, NULL, 'Cần Thơ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (379, 'Phương Bình', 5, NULL, NULL, NULL, 'Cần Thơ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (380, 'Sóc Trăng', 5, NULL, NULL, NULL, 'Cần Thơ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (381, 'Tài Văn', 5, NULL, NULL, NULL, 'Cần Thơ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (382, 'Tân An', 5, NULL, NULL, NULL, 'Cần Thơ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (383, 'Tân Bình', 5, NULL, NULL, NULL, 'Cần Thơ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (384, 'Tân Hòa', 5, NULL, NULL, NULL, 'Cần Thơ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (385, 'Tân Long', 5, NULL, NULL, NULL, 'Cần Thơ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (386, 'Tân Lộc', 5, NULL, NULL, NULL, 'Cần Thơ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (387, 'Tân Phước Hưng', 5, NULL, NULL, NULL, 'Cần Thơ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (388, 'Tân Thạnh', 5, NULL, NULL, NULL, 'Cần Thơ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (389, 'Thạnh An', 5, NULL, NULL, NULL, 'Cần Thơ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (390, 'Thạnh Hòa', 5, NULL, NULL, NULL, 'Cần Thơ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (391, 'Thạnh Phú', 5, NULL, NULL, NULL, 'Cần Thơ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (392, 'Thạnh Quới', 5, NULL, NULL, NULL, 'Cần Thơ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (393, 'Thạnh Thới An', 5, NULL, NULL, NULL, 'Cần Thơ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (394, 'Thạnh Xuân', 5, NULL, NULL, NULL, 'Cần Thơ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (395, 'Thốt Nốt', 5, NULL, NULL, NULL, 'Cần Thơ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (396, 'Thới An Đông', 5, NULL, NULL, NULL, 'Cần Thơ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (397, 'Thới An Hội', 5, NULL, NULL, NULL, 'Cần Thơ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (398, 'Thới Hưng', 5, NULL, NULL, NULL, 'Cần Thơ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (399, 'Thới Lai', 5, NULL, NULL, NULL, 'Cần Thơ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (400, 'Thới Long', 5, NULL, NULL, NULL, 'Cần Thơ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (401, 'Thuận Hòa', 5, NULL, NULL, NULL, 'Cần Thơ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (402, 'Thuận Hưng', 5, NULL, NULL, NULL, 'Cần Thơ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (403, 'Trần Đề', 5, NULL, NULL, NULL, 'Cần Thơ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (404, 'Trung Hưng', 5, NULL, NULL, NULL, 'Cần Thơ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (405, 'Trung Nhứt', 5, NULL, NULL, NULL, 'Cần Thơ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (406, 'Trường Khánh', 5, NULL, NULL, NULL, 'Cần Thơ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (407, 'Trường Long', 5, NULL, NULL, NULL, 'Cần Thơ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (408, 'Trường Long Tây', 5, NULL, NULL, NULL, 'Cần Thơ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (409, 'Trường Thành', 5, NULL, NULL, NULL, 'Cần Thơ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (410, 'Trường Xuân', 5, NULL, NULL, NULL, 'Cần Thơ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (411, 'Vị Tân', 5, NULL, NULL, NULL, 'Cần Thơ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (412, 'Vị Thanh', 5, NULL, NULL, NULL, 'Cần Thơ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (413, 'Vị Thanh 1', 5, NULL, NULL, NULL, 'Cần Thơ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (414, 'Vị Thủy', 5, NULL, NULL, NULL, 'Cần Thơ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (415, 'Vĩnh Châu', 5, NULL, NULL, NULL, 'Cần Thơ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (416, 'Vĩnh Hải', 5, NULL, NULL, NULL, 'Cần Thơ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (417, 'Vĩnh Lợi', 5, NULL, NULL, NULL, 'Cần Thơ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (418, 'Vĩnh Phước', 5, NULL, NULL, NULL, 'Cần Thơ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (419, 'Vĩnh Thạnh', 5, NULL, NULL, NULL, 'Cần Thơ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (420, 'Vĩnh Thuận Đông', 5, NULL, NULL, NULL, 'Cần Thơ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (421, 'Vĩnh Trinh', 5, NULL, NULL, NULL, 'Cần Thơ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (422, 'Vĩnh Tường', 5, NULL, NULL, NULL, 'Cần Thơ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (423, 'Vĩnh Viễn', 5, NULL, NULL, NULL, 'Cần Thơ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (424, 'Xà Phiên', 5, NULL, NULL, NULL, 'Cần Thơ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (425, 'An Hải', 6, NULL, NULL, NULL, 'Đà Nẵng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (426, 'An Khê', 6, NULL, NULL, NULL, 'Đà Nẵng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (427, 'An Thắng', 6, NULL, NULL, NULL, 'Đà Nẵng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (428, 'Avương', 6, NULL, NULL, NULL, 'Đà Nẵng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (429, 'Bà Nà', 6, NULL, NULL, NULL, 'Đà Nẵng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (430, 'Bàn Thạch', 6, NULL, NULL, NULL, 'Đà Nẵng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (431, 'Bến Giằng', 6, NULL, NULL, NULL, 'Đà Nẵng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (432, 'Bến Hiên', 6, NULL, NULL, NULL, 'Đà Nẵng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (433, 'Cẩm Lệ', 6, NULL, NULL, NULL, 'Đà Nẵng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (434, 'Chiên Đàn', 6, NULL, NULL, NULL, 'Đà Nẵng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (435, 'Duy Nghĩa', 6, NULL, NULL, NULL, 'Đà Nẵng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (436, 'Duy Xuyên', 6, NULL, NULL, NULL, 'Đà Nẵng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (437, 'Đại Lộc', 6, NULL, NULL, NULL, 'Đà Nẵng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (438, 'Đắc Pring', 6, NULL, NULL, NULL, 'Đà Nẵng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (439, 'Điện Bàn', 6, NULL, NULL, NULL, 'Đà Nẵng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (440, 'Điện Bàn Bắc', 6, NULL, NULL, NULL, 'Đà Nẵng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (441, 'Điện Bàn Đông', 6, NULL, NULL, NULL, 'Đà Nẵng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (442, 'Điện Bàn Tây', 6, NULL, NULL, NULL, 'Đà Nẵng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (443, 'Đồng Dương', 6, NULL, NULL, NULL, 'Đà Nẵng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (444, 'Đông Giang', 6, NULL, NULL, NULL, 'Đà Nẵng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (445, 'Đức Phú', 6, NULL, NULL, NULL, 'Đà Nẵng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (446, 'Gò Nổi', 6, NULL, NULL, NULL, 'Đà Nẵng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (447, 'Hà Nha', 6, NULL, NULL, NULL, 'Đà Nẵng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (448, 'Hải Châu', 6, NULL, NULL, NULL, 'Đà Nẵng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (449, 'Hải Vân', 6, NULL, NULL, NULL, 'Đà Nẵng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (450, 'Hiệp Đức', 6, NULL, NULL, NULL, 'Đà Nẵng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (451, 'Hòa Cường', 6, NULL, NULL, NULL, 'Đà Nẵng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (452, 'Hòa Khánh', 6, NULL, NULL, NULL, 'Đà Nẵng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (453, 'Hòa Tiến', 6, NULL, NULL, NULL, 'Đà Nẵng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (454, 'Hòa Vang', 6, NULL, NULL, NULL, 'Đà Nẵng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (455, 'Hòa Xuân', 6, NULL, NULL, NULL, 'Đà Nẵng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (456, 'Hoàng Sa', 6, NULL, NULL, NULL, 'Đà Nẵng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (457, 'Hội An', 6, NULL, NULL, NULL, 'Đà Nẵng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (458, 'Hội An Đông', 6, NULL, NULL, NULL, 'Đà Nẵng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (459, 'Hội An Tây', 6, NULL, NULL, NULL, 'Đà Nẵng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (460, 'Hùng Sơn', 6, NULL, NULL, NULL, 'Đà Nẵng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (461, 'Hương Trà', 6, NULL, NULL, NULL, 'Đà Nẵng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (462, 'Khâm Đức', 6, NULL, NULL, NULL, 'Đà Nẵng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (463, 'La Dêê', 6, NULL, NULL, NULL, 'Đà Nẵng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (464, 'La Êê', 6, NULL, NULL, NULL, 'Đà Nẵng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (465, 'Lãnh Ngọc', 6, NULL, NULL, NULL, 'Đà Nẵng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (466, 'Liên Chiểu', 6, NULL, NULL, NULL, 'Đà Nẵng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (467, 'Nam Giang', 6, NULL, NULL, NULL, 'Đà Nẵng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (468, 'Nam Phước', 6, NULL, NULL, NULL, 'Đà Nẵng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (469, 'Nam Trà My', 6, NULL, NULL, NULL, 'Đà Nẵng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (470, 'Ngũ Hành Sơn', 6, NULL, NULL, NULL, 'Đà Nẵng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (471, 'Nông Sơn', 6, NULL, NULL, NULL, 'Đà Nẵng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (472, 'Núi Thành', 6, NULL, NULL, NULL, 'Đà Nẵng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (473, 'Phú Ninh', 6, NULL, NULL, NULL, 'Đà Nẵng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (474, 'Phú Thuận', 6, NULL, NULL, NULL, 'Đà Nẵng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (475, 'Phước Chánh', 6, NULL, NULL, NULL, 'Đà Nẵng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (476, 'Phước Hiệp', 6, NULL, NULL, NULL, 'Đà Nẵng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (477, 'Phước Năng', 6, NULL, NULL, NULL, 'Đà Nẵng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (478, 'Phước Thành', 6, NULL, NULL, NULL, 'Đà Nẵng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (479, 'Phước Trà', 6, NULL, NULL, NULL, 'Đà Nẵng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (480, 'Quảng Phú', 6, NULL, NULL, NULL, 'Đà Nẵng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (481, 'Quế Phước', 6, NULL, NULL, NULL, 'Đà Nẵng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (482, 'Quế Sơn', 6, NULL, NULL, NULL, 'Đà Nẵng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (483, 'Quế Sơn Trung', 6, NULL, NULL, NULL, 'Đà Nẵng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (484, 'Sông Kôn', 6, NULL, NULL, NULL, 'Đà Nẵng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (485, 'Sông Vàng', 6, NULL, NULL, NULL, 'Đà Nẵng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (486, 'Sơn Cẩm Hà', 6, NULL, NULL, NULL, 'Đà Nẵng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (487, 'Sơn Trà', 6, NULL, NULL, NULL, 'Đà Nẵng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (488, 'Tam Anh', 6, NULL, NULL, NULL, 'Đà Nẵng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (489, 'Tam Hải', 6, NULL, NULL, NULL, 'Đà Nẵng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (490, 'Tam Kỳ', 6, NULL, NULL, NULL, 'Đà Nẵng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (491, 'Tam Mỹ', 6, NULL, NULL, NULL, 'Đà Nẵng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (492, 'Tam Xuân', 6, NULL, NULL, NULL, 'Đà Nẵng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (493, 'Tân Hiệp', 6, NULL, NULL, NULL, 'Đà Nẵng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (494, 'Tây Giang', 6, NULL, NULL, NULL, 'Đà Nẵng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (495, 'Tây Hồ', 6, NULL, NULL, NULL, 'Đà Nẵng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (496, 'Thạnh Bình', 6, NULL, NULL, NULL, 'Đà Nẵng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (497, 'Thanh Khê', 6, NULL, NULL, NULL, 'Đà Nẵng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (498, 'Thạnh Mỹ', 6, NULL, NULL, NULL, 'Đà Nẵng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (499, 'Thăng An', 6, NULL, NULL, NULL, 'Đà Nẵng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (500, 'Thăng Bình', 6, NULL, NULL, NULL, 'Đà Nẵng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (501, 'Thăng Điền', 6, NULL, NULL, NULL, 'Đà Nẵng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (502, 'Thăng Phú', 6, NULL, NULL, NULL, 'Đà Nẵng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (503, 'Thăng Trường', 6, NULL, NULL, NULL, 'Đà Nẵng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (504, 'Thu Bồn', 6, NULL, NULL, NULL, 'Đà Nẵng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (505, 'Thượng Đức', 6, NULL, NULL, NULL, 'Đà Nẵng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (506, 'Tiên Phước', 6, NULL, NULL, NULL, 'Đà Nẵng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (507, 'Trà Đốc', 6, NULL, NULL, NULL, 'Đà Nẵng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (508, 'Trà Giáp', 6, NULL, NULL, NULL, 'Đà Nẵng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (509, 'Trà Leng', 6, NULL, NULL, NULL, 'Đà Nẵng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (510, 'Trà Liên', 6, NULL, NULL, NULL, 'Đà Nẵng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (511, 'Trà Linh', 6, NULL, NULL, NULL, 'Đà Nẵng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (512, 'Trà My', 6, NULL, NULL, NULL, 'Đà Nẵng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (513, 'Trà Tân', 6, NULL, NULL, NULL, 'Đà Nẵng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (514, 'Trà Tập', 6, NULL, NULL, NULL, 'Đà Nẵng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (515, 'Trà Vân', 6, NULL, NULL, NULL, 'Đà Nẵng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (516, 'Việt An', 6, NULL, NULL, NULL, 'Đà Nẵng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (517, 'Vu Gia', 6, NULL, NULL, NULL, 'Đà Nẵng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (518, 'Xuân Phú', 6, NULL, NULL, NULL, 'Đà Nẵng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (519, 'Bình Kiến', 7, NULL, NULL, NULL, 'Đắk Lắk');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (520, 'Buôn Đôn', 7, NULL, NULL, NULL, 'Đắk Lắk');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (521, 'Buôn Hồ', 7, NULL, NULL, NULL, 'Đắk Lắk');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (522, 'Buôn Ma Thuột', 7, NULL, NULL, NULL, 'Đắk Lắk');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (523, 'Cuôr Đăng', 7, NULL, NULL, NULL, 'Đắk Lắk');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (524, 'Cư Bao', 7, NULL, NULL, NULL, 'Đắk Lắk');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (525, 'Cư M’gar', 7, NULL, NULL, NULL, 'Đắk Lắk');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (526, 'Cư M’ta', 7, NULL, NULL, NULL, 'Đắk Lắk');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (527, 'Cư Pơng', 7, NULL, NULL, NULL, 'Đắk Lắk');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (528, 'Cư Prao', 7, NULL, NULL, NULL, 'Đắk Lắk');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (529, 'Cư Pui', 7, NULL, NULL, NULL, 'Đắk Lắk');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (530, 'Cư Yang', 7, NULL, NULL, NULL, 'Đắk Lắk');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (531, 'Dang Kang', 7, NULL, NULL, NULL, 'Đắk Lắk');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (532, 'Dliê Ya', 7, NULL, NULL, NULL, 'Đắk Lắk');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (533, 'Dray Bhăng', 7, NULL, NULL, NULL, 'Đắk Lắk');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (534, 'Dur Kmăl', 7, NULL, NULL, NULL, 'Đắk Lắk');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (535, 'Đắk Liêng', 7, NULL, NULL, NULL, 'Đắk Lắk');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (536, 'Đắk Phơi', 7, NULL, NULL, NULL, 'Đắk Lắk');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (537, 'Đông Hòa', 7, NULL, NULL, NULL, 'Đắk Lắk');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (538, 'Đồng Xuân', 7, NULL, NULL, NULL, 'Đắk Lắk');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (539, 'Đức Bình', 7, NULL, NULL, NULL, 'Đắk Lắk');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (540, 'Ea Bá', 7, NULL, NULL, NULL, 'Đắk Lắk');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (541, 'Ea Bung', 7, NULL, NULL, NULL, 'Đắk Lắk');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (542, 'Ea Drăng', 7, NULL, NULL, NULL, 'Đắk Lắk');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (543, 'Ea Drông', 7, NULL, NULL, NULL, 'Đắk Lắk');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (544, 'Ea H’Leo', 7, NULL, NULL, NULL, 'Đắk Lắk');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (545, 'Ea Hiao', 7, NULL, NULL, NULL, 'Đắk Lắk');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (546, 'Ea Kao', 7, NULL, NULL, NULL, 'Đắk Lắk');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (547, 'Ea Kar', 7, NULL, NULL, NULL, 'Đắk Lắk');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (548, 'Ea Khăl', 7, NULL, NULL, NULL, 'Đắk Lắk');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (549, 'Ea Kiết', 7, NULL, NULL, NULL, 'Đắk Lắk');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (550, 'Ea Kly', 7, NULL, NULL, NULL, 'Đắk Lắk');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (551, 'Ea Knốp', 7, NULL, NULL, NULL, 'Đắk Lắk');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (552, 'Ea Knuếc', 7, NULL, NULL, NULL, 'Đắk Lắk');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (553, 'Ea Ktur', 7, NULL, NULL, NULL, 'Đắk Lắk');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (554, 'Ea Ly', 7, NULL, NULL, NULL, 'Đắk Lắk');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (555, 'Ea M’Droh', 7, NULL, NULL, NULL, 'Đắk Lắk');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (556, 'Ea Na', 7, NULL, NULL, NULL, 'Đắk Lắk');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (557, 'Ea Ning', 7, NULL, NULL, NULL, 'Đắk Lắk');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (558, 'Ea Nuôl', 7, NULL, NULL, NULL, 'Đắk Lắk');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (559, 'Ea Ô', 7, NULL, NULL, NULL, 'Đắk Lắk');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (560, 'Ea Păl', 7, NULL, NULL, NULL, 'Đắk Lắk');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (561, 'Ea Phê', 7, NULL, NULL, NULL, 'Đắk Lắk');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (562, 'Ea Riêng', 7, NULL, NULL, NULL, 'Đắk Lắk');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (563, 'Ea Rốk', 7, NULL, NULL, NULL, 'Đắk Lắk');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (564, 'Ea Súp', 7, NULL, NULL, NULL, 'Đắk Lắk');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (565, 'Ea Trang', 7, NULL, NULL, NULL, 'Đắk Lắk');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (566, 'Ea Tul', 7, NULL, NULL, NULL, 'Đắk Lắk');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (567, 'Ea Wer', 7, NULL, NULL, NULL, 'Đắk Lắk');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (568, 'Ea Wy', 7, NULL, NULL, NULL, 'Đắk Lắk');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (569, 'Hòa Hiệp', 7, NULL, NULL, NULL, 'Đắk Lắk');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (570, 'Hòa Mỹ', 7, NULL, NULL, NULL, 'Đắk Lắk');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (571, 'Hòa Phú', 7, NULL, NULL, NULL, 'Đắk Lắk');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (572, 'Hòa Sơn', 7, NULL, NULL, NULL, 'Đắk Lắk');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (573, 'Hòa Thịnh', 7, NULL, NULL, NULL, 'Đắk Lắk');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (574, 'Hòa Xuân', 7, NULL, NULL, NULL, 'Đắk Lắk');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (575, 'Ia Lốp', 7, NULL, NULL, NULL, 'Đắk Lắk');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (576, 'Ia Rvê', 7, NULL, NULL, NULL, 'Đắk Lắk');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (577, 'Krông Á', 7, NULL, NULL, NULL, 'Đắk Lắk');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (578, 'Krông Ana', 7, NULL, NULL, NULL, 'Đắk Lắk');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (579, 'Krông Bông', 7, NULL, NULL, NULL, 'Đắk Lắk');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (580, 'Krông Búk', 7, NULL, NULL, NULL, 'Đắk Lắk');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (581, 'Krông Năng', 7, NULL, NULL, NULL, 'Đắk Lắk');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (582, 'Krông Nô', 7, NULL, NULL, NULL, 'Đắk Lắk');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (583, 'Krông Pắc', 7, NULL, NULL, NULL, 'Đắk Lắk');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (584, 'Liên Sơn Lắk', 7, NULL, NULL, NULL, 'Đắk Lắk');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (585, 'M’Drắk', 7, NULL, NULL, NULL, 'Đắk Lắk');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (586, 'Nam Ka', 7, NULL, NULL, NULL, 'Đắk Lắk');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (587, 'Ô Loan', 7, NULL, NULL, NULL, 'Đắk Lắk');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (588, 'Phú Hòa 1', 7, NULL, NULL, NULL, 'Đắk Lắk');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (589, 'Phú Hòa 2', 7, NULL, NULL, NULL, 'Đắk Lắk');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (590, 'Phú Mỡ', 7, NULL, NULL, NULL, 'Đắk Lắk');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (591, 'Phú Xuân', 7, NULL, NULL, NULL, 'Đắk Lắk');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (592, 'Phú Yên', 7, NULL, NULL, NULL, 'Đắk Lắk');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (593, 'Pơng Drang', 7, NULL, NULL, NULL, 'Đắk Lắk');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (594, 'Quảng Phú', 7, NULL, NULL, NULL, 'Đắk Lắk');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (595, 'Sông Cầu', 7, NULL, NULL, NULL, 'Đắk Lắk');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (596, 'Sông Hinh', 7, NULL, NULL, NULL, 'Đắk Lắk');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (597, 'Sơn Hòa', 7, NULL, NULL, NULL, 'Đắk Lắk');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (598, 'Sơn Thành', 7, NULL, NULL, NULL, 'Đắk Lắk');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (599, 'Suối Trai', 7, NULL, NULL, NULL, 'Đắk Lắk');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (600, 'Tam Giang', 7, NULL, NULL, NULL, 'Đắk Lắk');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (601, 'Tân An', 7, NULL, NULL, NULL, 'Đắk Lắk');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (602, 'Tân Lập', 7, NULL, NULL, NULL, 'Đắk Lắk');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (603, 'Tân Tiến', 7, NULL, NULL, NULL, 'Đắk Lắk');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (604, 'Tây Hòa', 7, NULL, NULL, NULL, 'Đắk Lắk');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (605, 'Tây Sơn', 7, NULL, NULL, NULL, 'Đắk Lắk');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (606, 'Thành Nhất', 7, NULL, NULL, NULL, 'Đắk Lắk');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (607, 'Tuy An Bắc', 7, NULL, NULL, NULL, 'Đắk Lắk');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (608, 'Tuy An Đông', 7, NULL, NULL, NULL, 'Đắk Lắk');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (609, 'Tuy An Nam', 7, NULL, NULL, NULL, 'Đắk Lắk');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (610, 'Tuy An Tây', 7, NULL, NULL, NULL, 'Đắk Lắk');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (611, 'Tuy Hòa', 7, NULL, NULL, NULL, 'Đắk Lắk');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (612, 'Vân Hòa', 7, NULL, NULL, NULL, 'Đắk Lắk');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (613, 'Vụ Bổn', 7, NULL, NULL, NULL, 'Đắk Lắk');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (614, 'Xuân Cảnh', 7, NULL, NULL, NULL, 'Đắk Lắk');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (615, 'Xuân Đài', 7, NULL, NULL, NULL, 'Đắk Lắk');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (616, 'Xuân Lãnh', 7, NULL, NULL, NULL, 'Đắk Lắk');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (617, 'Xuân Lộc', 7, NULL, NULL, NULL, 'Đắk Lắk');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (618, 'Xuân Phước', 7, NULL, NULL, NULL, 'Đắk Lắk');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (619, 'Xuân Thọ', 7, NULL, NULL, NULL, 'Đắk Lắk');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (620, 'Yang Mao', 7, NULL, NULL, NULL, 'Đắk Lắk');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (621, 'Búng Lao', 8, NULL, NULL, NULL, 'Điện Biên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (622, 'Chà Tở', 8, NULL, NULL, NULL, 'Điện Biên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (623, 'Chiềng Sinh', 8, NULL, NULL, NULL, 'Điện Biên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (624, 'Điện Biên Phủ', 8, NULL, NULL, NULL, 'Điện Biên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (625, 'Mường Ảng', 8, NULL, NULL, NULL, 'Điện Biên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (626, 'Mường Chà', 8, NULL, NULL, NULL, 'Điện Biên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (627, 'Mường Lạn', 8, NULL, NULL, NULL, 'Điện Biên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (628, 'Mường Lay', 8, NULL, NULL, NULL, 'Điện Biên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (629, 'Mường Luân', 8, NULL, NULL, NULL, 'Điện Biên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (630, 'Mường Mùn', 8, NULL, NULL, NULL, 'Điện Biên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (631, 'Mường Nhà', 8, NULL, NULL, NULL, 'Điện Biên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (632, 'Mường Nhé', 8, NULL, NULL, NULL, 'Điện Biên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (633, 'Mường Phăng', 8, NULL, NULL, NULL, 'Điện Biên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (634, 'Mường Pồn', 8, NULL, NULL, NULL, 'Điện Biên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (635, 'Mường Thanh', 8, NULL, NULL, NULL, 'Điện Biên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (636, 'Mường Toong', 8, NULL, NULL, NULL, 'Điện Biên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (637, 'Mường Tùng', 8, NULL, NULL, NULL, 'Điện Biên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (638, 'Nà Bủng', 8, NULL, NULL, NULL, 'Điện Biên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (639, 'Nà Hỳ', 8, NULL, NULL, NULL, 'Điện Biên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (640, 'Na Sang', 8, NULL, NULL, NULL, 'Điện Biên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (641, 'Na Son', 8, NULL, NULL, NULL, 'Điện Biên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (642, 'Nà Tấu', 8, NULL, NULL, NULL, 'Điện Biên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (643, 'Nậm Kè', 8, NULL, NULL, NULL, 'Điện Biên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (644, 'Nậm Nèn', 8, NULL, NULL, NULL, 'Điện Biên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (645, 'Núa Ngam', 8, NULL, NULL, NULL, 'Điện Biên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (646, 'Pa Ham', 8, NULL, NULL, NULL, 'Điện Biên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (647, 'Phình Giàng', 8, NULL, NULL, NULL, 'Điện Biên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (648, 'Pu Nhi', 8, NULL, NULL, NULL, 'Điện Biên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (649, 'Pú Nhung', 8, NULL, NULL, NULL, 'Điện Biên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (650, 'Quài Tở', 8, NULL, NULL, NULL, 'Điện Biên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (651, 'Quảng Lâm', 8, NULL, NULL, NULL, 'Điện Biên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (652, 'Sam Mứn', 8, NULL, NULL, NULL, 'Điện Biên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (653, 'Sáng Nhè', 8, NULL, NULL, NULL, 'Điện Biên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (654, 'Si Pa Phìn', 8, NULL, NULL, NULL, 'Điện Biên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (655, 'Sín Chải', 8, NULL, NULL, NULL, 'Điện Biên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (656, 'Sín Thầu', 8, NULL, NULL, NULL, 'Điện Biên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (657, 'Sính Phình', 8, NULL, NULL, NULL, 'Điện Biên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (658, 'Thanh An', 8, NULL, NULL, NULL, 'Điện Biên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (659, 'Thanh Nưa', 8, NULL, NULL, NULL, 'Điện Biên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (660, 'Thanh Yên', 8, NULL, NULL, NULL, 'Điện Biên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (661, 'Tìa Dình', 8, NULL, NULL, NULL, 'Điện Biên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (662, 'Tủa Chùa', 8, NULL, NULL, NULL, 'Điện Biên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (663, 'Tủa Thàng', 8, NULL, NULL, NULL, 'Điện Biên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (664, 'Tuần Giáo', 8, NULL, NULL, NULL, 'Điện Biên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (665, 'Xa Dung', 8, NULL, NULL, NULL, 'Điện Biên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (666, 'An Lộc', 9, NULL, NULL, NULL, 'Đồng Nai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (667, 'An Phước', 9, NULL, NULL, NULL, 'Đồng Nai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (668, 'An Viễn', 9, NULL, NULL, NULL, 'Đồng Nai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (669, 'Bảo Vinh', 9, NULL, NULL, NULL, 'Đồng Nai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (670, 'Bàu Hàm', 9, NULL, NULL, NULL, 'Đồng Nai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (671, 'Biên Hòa', 9, NULL, NULL, NULL, 'Đồng Nai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (672, 'Bình An', 9, NULL, NULL, NULL, 'Đồng Nai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (673, 'Bình Long', 9, NULL, NULL, NULL, 'Đồng Nai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (674, 'Bình Lộc', 9, NULL, NULL, NULL, 'Đồng Nai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (675, 'Bình Minh', 9, NULL, NULL, NULL, 'Đồng Nai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (676, 'Bình Phước', 9, NULL, NULL, NULL, 'Đồng Nai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (677, 'Bình Tân', 9, NULL, NULL, NULL, 'Đồng Nai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (678, 'Bom Bo', 9, NULL, NULL, NULL, 'Đồng Nai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (679, 'Bù Đăng', 9, NULL, NULL, NULL, 'Đồng Nai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (680, 'Bù Gia Mập', 9, NULL, NULL, NULL, 'Đồng Nai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (681, 'Cẩm Mỹ', 9, NULL, NULL, NULL, 'Đồng Nai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (682, 'Chơn Thành', 9, NULL, NULL, NULL, 'Đồng Nai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (683, 'Dầu Giây', 9, NULL, NULL, NULL, 'Đồng Nai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (684, 'Đa Kia', 9, NULL, NULL, NULL, 'Đồng Nai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (685, 'Đại Phước', 9, NULL, NULL, NULL, 'Đồng Nai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (686, 'Đak Lua', 9, NULL, NULL, NULL, 'Đồng Nai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (687, 'Đak Nhau', 9, NULL, NULL, NULL, 'Đồng Nai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (688, 'Đăk Ơ', 9, NULL, NULL, NULL, 'Đồng Nai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (689, 'Định Quán', 9, NULL, NULL, NULL, 'Đồng Nai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (690, 'Đồng Phú', 9, NULL, NULL, NULL, 'Đồng Nai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (691, 'Đồng Tâm', 9, NULL, NULL, NULL, 'Đồng Nai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (692, 'Đồng Xoài', 9, NULL, NULL, NULL, 'Đồng Nai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (693, 'Gia Kiệm', 9, NULL, NULL, NULL, 'Đồng Nai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (694, 'Hàng Gòn', 9, NULL, NULL, NULL, 'Đồng Nai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (695, 'Hố Nai', 9, NULL, NULL, NULL, 'Đồng Nai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (696, 'Hưng Phước', 9, NULL, NULL, NULL, 'Đồng Nai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (697, 'Hưng Thịnh', 9, NULL, NULL, NULL, 'Đồng Nai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (698, 'La Ngà', 9, NULL, NULL, NULL, 'Đồng Nai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (699, 'Long Bình', 9, NULL, NULL, NULL, 'Đồng Nai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (700, 'Long Hà', 9, NULL, NULL, NULL, 'Đồng Nai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (701, 'Long Hưng', 9, NULL, NULL, NULL, 'Đồng Nai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (702, 'Long Khánh', 9, NULL, NULL, NULL, 'Đồng Nai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (703, 'Long Phước', 9, NULL, NULL, NULL, 'Đồng Nai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (704, 'Long Thành', 9, NULL, NULL, NULL, 'Đồng Nai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (705, 'Lộc Hưng', 9, NULL, NULL, NULL, 'Đồng Nai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (706, 'Lộc Ninh', 9, NULL, NULL, NULL, 'Đồng Nai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (707, 'Lộc Quang', 9, NULL, NULL, NULL, 'Đồng Nai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (708, 'Lộc Tấn', 9, NULL, NULL, NULL, 'Đồng Nai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (709, 'Lộc Thành', 9, NULL, NULL, NULL, 'Đồng Nai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (710, 'Lộc Thạnh', 9, NULL, NULL, NULL, 'Đồng Nai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (711, 'Minh Đức', 9, NULL, NULL, NULL, 'Đồng Nai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (712, 'Minh Hưng', 9, NULL, NULL, NULL, 'Đồng Nai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (713, 'Nam Cát Tiên', 9, NULL, NULL, NULL, 'Đồng Nai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (714, 'Nghĩa Trung', 9, NULL, NULL, NULL, 'Đồng Nai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (715, 'Nha Bích', 9, NULL, NULL, NULL, 'Đồng Nai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (716, 'Nhơn Trạch', 9, NULL, NULL, NULL, 'Đồng Nai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (717, 'Phú Hòa', 9, NULL, NULL, NULL, 'Đồng Nai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (718, 'Phú Lâm', 9, NULL, NULL, NULL, 'Đồng Nai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (719, 'Phú Lý', 9, NULL, NULL, NULL, 'Đồng Nai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (720, 'Phú Nghĩa', 9, NULL, NULL, NULL, 'Đồng Nai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (721, 'Phú Riềng', 9, NULL, NULL, NULL, 'Đồng Nai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (722, 'Phú Trung', 9, NULL, NULL, NULL, 'Đồng Nai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (723, 'Phú Vinh', 9, NULL, NULL, NULL, 'Đồng Nai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (724, 'Phước An', 9, NULL, NULL, NULL, 'Đồng Nai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (725, 'Phước Bình', 9, NULL, NULL, NULL, 'Đồng Nai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (726, 'Phước Long', 9, NULL, NULL, NULL, 'Đồng Nai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (727, 'Phước Sơn', 9, NULL, NULL, NULL, 'Đồng Nai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (728, 'Phước Tân', 9, NULL, NULL, NULL, 'Đồng Nai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (729, 'Phước Thái', 9, NULL, NULL, NULL, 'Đồng Nai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (730, 'Sông Ray', 9, NULL, NULL, NULL, 'Đồng Nai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (731, 'Tà Lài', 9, NULL, NULL, NULL, 'Đồng Nai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (732, 'Tam Hiệp', 9, NULL, NULL, NULL, 'Đồng Nai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (733, 'Tam Phước', 9, NULL, NULL, NULL, 'Đồng Nai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (734, 'Tân An', 9, NULL, NULL, NULL, 'Đồng Nai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (735, 'Tân Hưng', 9, NULL, NULL, NULL, 'Đồng Nai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (736, 'Tân Khai', 9, NULL, NULL, NULL, 'Đồng Nai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (737, 'Tân Lợi', 9, NULL, NULL, NULL, 'Đồng Nai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (738, 'Tân Phú', 9, NULL, NULL, NULL, 'Đồng Nai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (739, 'Tân Quan', 9, NULL, NULL, NULL, 'Đồng Nai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (740, 'Tân Tiến', 9, NULL, NULL, NULL, 'Đồng Nai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (741, 'Tân Triều', 9, NULL, NULL, NULL, 'Đồng Nai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (742, 'Thanh Sơn', 9, NULL, NULL, NULL, 'Đồng Nai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (743, 'Thiện Hưng', 9, NULL, NULL, NULL, 'Đồng Nai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (744, 'Thọ Sơn', 9, NULL, NULL, NULL, 'Đồng Nai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (745, 'Thống Nhất', 9, NULL, NULL, NULL, 'Đồng Nai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (746, 'Thuận Lợi', 9, NULL, NULL, NULL, 'Đồng Nai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (747, 'Trảng Bom', 9, NULL, NULL, NULL, 'Đồng Nai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (748, 'Trảng Dài', 9, NULL, NULL, NULL, 'Đồng Nai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (749, 'Trấn Biên', 9, NULL, NULL, NULL, 'Đồng Nai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (750, 'Trị An', 9, NULL, NULL, NULL, 'Đồng Nai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (751, 'Xuân Bắc', 9, NULL, NULL, NULL, 'Đồng Nai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (752, 'Xuân Định', 9, NULL, NULL, NULL, 'Đồng Nai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (753, 'Xuân Đông', 9, NULL, NULL, NULL, 'Đồng Nai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (754, 'Xuân Đường', 9, NULL, NULL, NULL, 'Đồng Nai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (755, 'Xuân Hòa', 9, NULL, NULL, NULL, 'Đồng Nai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (756, 'Xuân Lập', 9, NULL, NULL, NULL, 'Đồng Nai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (757, 'Xuân Lộc', 9, NULL, NULL, NULL, 'Đồng Nai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (758, 'Xuân Phú', 9, NULL, NULL, NULL, 'Đồng Nai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (759, 'Xuân Quế', 9, NULL, NULL, NULL, 'Đồng Nai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (760, 'Xuân Thành', 9, NULL, NULL, NULL, 'Đồng Nai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (761, 'An Bình', 10, NULL, NULL, NULL, 'Đồng Tháp');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (762, 'An Hòa', 10, NULL, NULL, NULL, 'Đồng Tháp');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (763, 'An Hữu', 10, NULL, NULL, NULL, 'Đồng Tháp');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (764, 'An Long', 10, NULL, NULL, NULL, 'Đồng Tháp');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (765, 'An Phước', 10, NULL, NULL, NULL, 'Đồng Tháp');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (766, 'An Thạnh Thủy', 10, NULL, NULL, NULL, 'Đồng Tháp');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (767, 'Ba Sao', 10, NULL, NULL, NULL, 'Đồng Tháp');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (768, 'Bình Hàng Trung', 10, NULL, NULL, NULL, 'Đồng Tháp');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (769, 'Bình Ninh', 10, NULL, NULL, NULL, 'Đồng Tháp');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (770, 'Bình Phú', 10, NULL, NULL, NULL, 'Đồng Tháp');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (771, 'Bình Thành', 10, NULL, NULL, NULL, 'Đồng Tháp');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (772, 'Bình Trưng', 10, NULL, NULL, NULL, 'Đồng Tháp');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (773, 'Bình Xuân', 10, NULL, NULL, NULL, 'Đồng Tháp');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (774, 'Cái Bè', 10, NULL, NULL, NULL, 'Đồng Tháp');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (775, 'Cai Lậy', 10, NULL, NULL, NULL, 'Đồng Tháp');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (776, 'Cao Lãnh', 10, NULL, NULL, NULL, 'Đồng Tháp');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (777, 'Châu Thành', 10, NULL, NULL, NULL, 'Đồng Tháp');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (778, 'Chợ Gạo', 10, NULL, NULL, NULL, 'Đồng Tháp');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (779, 'Đạo Thạnh', 10, NULL, NULL, NULL, 'Đồng Tháp');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (780, 'Đốc Binh Kiều', 10, NULL, NULL, NULL, 'Đồng Tháp');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (781, 'Đồng Sơn', 10, NULL, NULL, NULL, 'Đồng Tháp');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (782, 'Gia Thuận', 10, NULL, NULL, NULL, 'Đồng Tháp');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (783, 'Gò Công', 10, NULL, NULL, NULL, 'Đồng Tháp');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (784, 'Gò Công Đông', 10, NULL, NULL, NULL, 'Đồng Tháp');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (785, 'Hậu Mỹ', 10, NULL, NULL, NULL, 'Đồng Tháp');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (786, 'Hiệp Đức', 10, NULL, NULL, NULL, 'Đồng Tháp');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (787, 'Hòa Long', 10, NULL, NULL, NULL, 'Đồng Tháp');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (788, 'Hội Cư', 10, NULL, NULL, NULL, 'Đồng Tháp');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (789, 'Hồng Ngự', 10, NULL, NULL, NULL, 'Đồng Tháp');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (790, 'Hưng Thạnh', 10, NULL, NULL, NULL, 'Đồng Tháp');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (791, 'Kim Sơn', 10, NULL, NULL, NULL, 'Đồng Tháp');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (792, 'Lai Vung', 10, NULL, NULL, NULL, 'Đồng Tháp');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (793, 'Lấp Vò', 10, NULL, NULL, NULL, 'Đồng Tháp');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (794, 'Long Bình', 10, NULL, NULL, NULL, 'Đồng Tháp');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (795, 'Long Định', 10, NULL, NULL, NULL, 'Đồng Tháp');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (796, 'Long Hưng', 10, NULL, NULL, NULL, 'Đồng Tháp');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (797, 'Long Khánh', 10, NULL, NULL, NULL, 'Đồng Tháp');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (798, 'Long Phú Thuận', 10, NULL, NULL, NULL, 'Đồng Tháp');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (799, 'Long Thuận', 10, NULL, NULL, NULL, 'Đồng Tháp');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (800, 'Long Tiên', 10, NULL, NULL, NULL, 'Đồng Tháp');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (801, 'Lương Hòa Lạc', 10, NULL, NULL, NULL, 'Đồng Tháp');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (802, 'Mỹ An Hưng', 10, NULL, NULL, NULL, 'Đồng Tháp');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (803, 'Mỹ Đức Tây', 10, NULL, NULL, NULL, 'Đồng Tháp');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (804, 'Mỹ Hiệp', 10, NULL, NULL, NULL, 'Đồng Tháp');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (805, 'Mỹ Lợi', 10, NULL, NULL, NULL, 'Đồng Tháp');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (806, 'Mỹ Ngãi', 10, NULL, NULL, NULL, 'Đồng Tháp');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (807, 'Mỹ Phong', 10, NULL, NULL, NULL, 'Đồng Tháp');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (808, 'Mỹ Phước Tây', 10, NULL, NULL, NULL, 'Đồng Tháp');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (809, 'Mỹ Quí', 10, NULL, NULL, NULL, 'Đồng Tháp');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (810, 'Mỹ Thành', 10, NULL, NULL, NULL, 'Đồng Tháp');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (811, 'Mỹ Thiện', 10, NULL, NULL, NULL, 'Đồng Tháp');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (812, 'Mỹ Tho', 10, NULL, NULL, NULL, 'Đồng Tháp');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (813, 'Mỹ Thọ', 10, NULL, NULL, NULL, 'Đồng Tháp');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (814, 'Mỹ Tịnh An', 10, NULL, NULL, NULL, 'Đồng Tháp');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (815, 'Mỹ Trà', 10, NULL, NULL, NULL, 'Đồng Tháp');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (816, 'Ngũ Hiệp', 10, NULL, NULL, NULL, 'Đồng Tháp');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (817, 'Nhị Quý', 10, NULL, NULL, NULL, 'Đồng Tháp');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (818, 'Phong Hòa', 10, NULL, NULL, NULL, 'Đồng Tháp');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (819, 'Phong Mỹ', 10, NULL, NULL, NULL, 'Đồng Tháp');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (820, 'Phú Cường', 10, NULL, NULL, NULL, 'Đồng Tháp');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (821, 'Phú Hựu', 10, NULL, NULL, NULL, 'Đồng Tháp');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (822, 'Phú Thành', 10, NULL, NULL, NULL, 'Đồng Tháp');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (823, 'Phú Thọ', 10, NULL, NULL, NULL, 'Đồng Tháp');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (824, 'Phương Thịnh', 10, NULL, NULL, NULL, 'Đồng Tháp');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (825, 'Sa Đéc', 10, NULL, NULL, NULL, 'Đồng Tháp');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (826, 'Sơn Qui', 10, NULL, NULL, NULL, 'Đồng Tháp');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (827, 'Tam Nông', 10, NULL, NULL, NULL, 'Đồng Tháp');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (828, 'Tân Dương', 10, NULL, NULL, NULL, 'Đồng Tháp');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (829, 'Tân Điền', 10, NULL, NULL, NULL, 'Đồng Tháp');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (830, 'Tân Đông', 10, NULL, NULL, NULL, 'Đồng Tháp');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (831, 'Tân Hòa', 10, NULL, NULL, NULL, 'Đồng Tháp');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (832, 'Tân Hộ Cơ', 10, NULL, NULL, NULL, 'Đồng Tháp');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (833, 'Tân Hồng', 10, NULL, NULL, NULL, 'Đồng Tháp');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (834, 'Tân Hương', 10, NULL, NULL, NULL, 'Đồng Tháp');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (835, 'Tân Khánh Trung', 10, NULL, NULL, NULL, 'Đồng Tháp');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (836, 'Tân Long', 10, NULL, NULL, NULL, 'Đồng Tháp');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (837, 'Tân Nhuận Đông', 10, NULL, NULL, NULL, 'Đồng Tháp');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (838, 'Tân Phú', 10, NULL, NULL, NULL, 'Đồng Tháp');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (839, 'Tân Phú Đông', 10, NULL, NULL, NULL, 'Đồng Tháp');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (840, 'Tân Phú Trung', 10, NULL, NULL, NULL, 'Đồng Tháp');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (841, 'Tân Phước 1', 10, NULL, NULL, NULL, 'Đồng Tháp');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (842, 'Tân Phước 2', 10, NULL, NULL, NULL, 'Đồng Tháp');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (843, 'Tân Phước 3', 10, NULL, NULL, NULL, 'Đồng Tháp');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (844, 'Tân Thành', 10, NULL, NULL, NULL, 'Đồng Tháp');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (845, 'Tân Thạnh', 10, NULL, NULL, NULL, 'Đồng Tháp');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (846, 'Tân Thới', 10, NULL, NULL, NULL, 'Đồng Tháp');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (847, 'Tân Thuận Bình', 10, NULL, NULL, NULL, 'Đồng Tháp');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (848, 'Thanh Bình', 10, NULL, NULL, NULL, 'Đồng Tháp');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (849, 'Thanh Hòa', 10, NULL, NULL, NULL, 'Đồng Tháp');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (850, 'Thanh Hưng', 10, NULL, NULL, NULL, 'Đồng Tháp');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (851, 'Thanh Mỹ', 10, NULL, NULL, NULL, 'Đồng Tháp');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (852, 'Thạnh Phú', 10, NULL, NULL, NULL, 'Đồng Tháp');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (853, 'Tháp Mười', 10, NULL, NULL, NULL, 'Đồng Tháp');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (854, 'Thới Sơn', 10, NULL, NULL, NULL, 'Đồng Tháp');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (855, 'Thường Lạc', 10, NULL, NULL, NULL, 'Đồng Tháp');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (856, 'Thường Phước', 10, NULL, NULL, NULL, 'Đồng Tháp');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (857, 'Tràm Chim', 10, NULL, NULL, NULL, 'Đồng Tháp');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (858, 'Trung An', 10, NULL, NULL, NULL, 'Đồng Tháp');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (859, 'Trường Xuân', 10, NULL, NULL, NULL, 'Đồng Tháp');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (860, 'Vĩnh Bình', 10, NULL, NULL, NULL, 'Đồng Tháp');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (861, 'Vĩnh Hựu', 10, NULL, NULL, NULL, 'Đồng Tháp');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (862, 'Vĩnh Kim', 10, NULL, NULL, NULL, 'Đồng Tháp');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (863, 'Al Bá', 11, NULL, NULL, NULL, 'Gia Lai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (864, 'An Bình', 11, NULL, NULL, NULL, 'Gia Lai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (865, 'An Hòa', 11, NULL, NULL, NULL, 'Gia Lai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (866, 'An Khê', 11, NULL, NULL, NULL, 'Gia Lai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (867, 'An Lão', 11, NULL, NULL, NULL, 'Gia Lai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (868, 'An Lương', 11, NULL, NULL, NULL, 'Gia Lai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (869, 'An Nhơn', 11, NULL, NULL, NULL, 'Gia Lai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (870, 'An Nhơn Bắc', 11, NULL, NULL, NULL, 'Gia Lai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (871, 'An Nhơn Đông', 11, NULL, NULL, NULL, 'Gia Lai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (872, 'An Nhơn Nam', 11, NULL, NULL, NULL, 'Gia Lai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (873, 'An Nhơn Tây', 11, NULL, NULL, NULL, 'Gia Lai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (874, 'An Phú', 11, NULL, NULL, NULL, 'Gia Lai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (875, 'An Toàn', 11, NULL, NULL, NULL, 'Gia Lai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (876, 'An Vinh', 11, NULL, NULL, NULL, 'Gia Lai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (877, 'Ayun', 11, NULL, NULL, NULL, 'Gia Lai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (878, 'Ayun Pa', 11, NULL, NULL, NULL, 'Gia Lai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (879, 'Ân Hảo', 11, NULL, NULL, NULL, 'Gia Lai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (880, 'Ân Tường', 11, NULL, NULL, NULL, 'Gia Lai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (881, 'Bàu Cạn', 11, NULL, NULL, NULL, 'Gia Lai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (882, 'Biển Hồ', 11, NULL, NULL, NULL, 'Gia Lai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (883, 'Bình An', 11, NULL, NULL, NULL, 'Gia Lai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (884, 'Bình Dương', 11, NULL, NULL, NULL, 'Gia Lai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (885, 'Bình Định', 11, NULL, NULL, NULL, 'Gia Lai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (886, 'Bình Hiệp', 11, NULL, NULL, NULL, 'Gia Lai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (887, 'Bình Khê', 11, NULL, NULL, NULL, 'Gia Lai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (888, 'Bình Phú', 11, NULL, NULL, NULL, 'Gia Lai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (889, 'Bồng Sơn', 11, NULL, NULL, NULL, 'Gia Lai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (890, 'Bờ Ngoong', 11, NULL, NULL, NULL, 'Gia Lai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (891, 'Canh Liên', 11, NULL, NULL, NULL, 'Gia Lai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (892, 'Canh Vinh', 11, NULL, NULL, NULL, 'Gia Lai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (893, 'Cát Tiến', 11, NULL, NULL, NULL, 'Gia Lai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (894, 'Chơ Long', 11, NULL, NULL, NULL, 'Gia Lai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (895, 'Chư A Thai', 11, NULL, NULL, NULL, 'Gia Lai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (896, 'Chư Krey', 11, NULL, NULL, NULL, 'Gia Lai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (897, 'Chư Păh', 11, NULL, NULL, NULL, 'Gia Lai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (898, 'Chư Prông', 11, NULL, NULL, NULL, 'Gia Lai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (899, 'Chư Pưh', 11, NULL, NULL, NULL, 'Gia Lai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (900, 'Chư Sê', 11, NULL, NULL, NULL, 'Gia Lai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (901, 'Cửu An', 11, NULL, NULL, NULL, 'Gia Lai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (902, 'Diên Hồng', 11, NULL, NULL, NULL, 'Gia Lai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (903, 'Đak Đoa', 11, NULL, NULL, NULL, 'Gia Lai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (904, 'Đak Pơ', 11, NULL, NULL, NULL, 'Gia Lai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (905, 'Đak Rong', 11, NULL, NULL, NULL, 'Gia Lai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (906, 'Đak Sơmei', 11, NULL, NULL, NULL, 'Gia Lai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (907, 'Đăk Song', 11, NULL, NULL, NULL, 'Gia Lai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (908, 'Đề Gi', 11, NULL, NULL, NULL, 'Gia Lai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (909, 'Đức Cơ', 11, NULL, NULL, NULL, 'Gia Lai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (910, 'Gào', 11, NULL, NULL, NULL, 'Gia Lai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (911, 'Hòa Hội', 11, NULL, NULL, NULL, 'Gia Lai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (912, 'Hoài Ân', 11, NULL, NULL, NULL, 'Gia Lai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (913, 'Hoài Nhơn', 11, NULL, NULL, NULL, 'Gia Lai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (914, 'Hoài Nhơn Bắc', 11, NULL, NULL, NULL, 'Gia Lai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (915, 'Hoài Nhơn Đông', 11, NULL, NULL, NULL, 'Gia Lai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (916, 'Hoài Nhơn Nam', 11, NULL, NULL, NULL, 'Gia Lai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (917, 'Hoài Nhơn Tây', 11, NULL, NULL, NULL, 'Gia Lai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (918, 'Hội Phú', 11, NULL, NULL, NULL, 'Gia Lai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (919, 'Hội Sơn', 11, NULL, NULL, NULL, 'Gia Lai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (920, 'Hra', 11, NULL, NULL, NULL, 'Gia Lai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (921, 'Ia Băng', 11, NULL, NULL, NULL, 'Gia Lai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (922, 'Ia Boòng', 11, NULL, NULL, NULL, 'Gia Lai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (923, 'Ia Chia', 11, NULL, NULL, NULL, 'Gia Lai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (924, 'Ia Dom', 11, NULL, NULL, NULL, 'Gia Lai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (925, 'Ia Dơk', 11, NULL, NULL, NULL, 'Gia Lai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (926, 'Ia Dreh', 11, NULL, NULL, NULL, 'Gia Lai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (927, 'Ia Grai', 11, NULL, NULL, NULL, 'Gia Lai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (928, 'Ia Hiao', 11, NULL, NULL, NULL, 'Gia Lai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (929, 'Ia Hrú', 11, NULL, NULL, NULL, 'Gia Lai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (930, 'Ia Hrung', 11, NULL, NULL, NULL, 'Gia Lai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (931, 'Ia Khươl', 11, NULL, NULL, NULL, 'Gia Lai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (932, 'Ia Ko', 11, NULL, NULL, NULL, 'Gia Lai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (933, 'Ia Krái', 11, NULL, NULL, NULL, 'Gia Lai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (934, 'Ia Krêl', 11, NULL, NULL, NULL, 'Gia Lai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (935, 'Ia Lâu', 11, NULL, NULL, NULL, 'Gia Lai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (936, 'Ia Le', 11, NULL, NULL, NULL, 'Gia Lai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (937, 'Ia Ly', 11, NULL, NULL, NULL, 'Gia Lai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (938, 'Ia Mơ', 11, NULL, NULL, NULL, 'Gia Lai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (939, 'Ia Nan', 11, NULL, NULL, NULL, 'Gia Lai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (940, 'Ia O', 11, NULL, NULL, NULL, 'Gia Lai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (941, 'Ia Pa', 11, NULL, NULL, NULL, 'Gia Lai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (942, 'Ia Phí', 11, NULL, NULL, NULL, 'Gia Lai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (943, 'Ia Pia', 11, NULL, NULL, NULL, 'Gia Lai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (944, 'Ia Pnôn', 11, NULL, NULL, NULL, 'Gia Lai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (945, 'Ia Púch', 11, NULL, NULL, NULL, 'Gia Lai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (946, 'Ia Rbol', 11, NULL, NULL, NULL, 'Gia Lai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (947, 'Ia Rsai', 11, NULL, NULL, NULL, 'Gia Lai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (948, 'Ia Sao', 11, NULL, NULL, NULL, 'Gia Lai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (949, 'Ia Tôr', 11, NULL, NULL, NULL, 'Gia Lai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (950, 'Ia Tul', 11, NULL, NULL, NULL, 'Gia Lai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (951, 'Kbang', 11, NULL, NULL, NULL, 'Gia Lai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (952, 'KDang', 11, NULL, NULL, NULL, 'Gia Lai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (953, 'Kim Sơn', 11, NULL, NULL, NULL, 'Gia Lai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (954, 'Kon Chiêng', 11, NULL, NULL, NULL, 'Gia Lai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (955, 'Kon Gang', 11, NULL, NULL, NULL, 'Gia Lai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (956, 'Kông Bơ La', 11, NULL, NULL, NULL, 'Gia Lai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (957, 'Kông Chro', 11, NULL, NULL, NULL, 'Gia Lai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (958, 'Krong', 11, NULL, NULL, NULL, 'Gia Lai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (959, 'Lơ Pang', 11, NULL, NULL, NULL, 'Gia Lai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (960, 'Mang Yang', 11, NULL, NULL, NULL, 'Gia Lai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (961, 'Ngô Mây', 11, NULL, NULL, NULL, 'Gia Lai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (962, 'Nhơn Châu', 11, NULL, NULL, NULL, 'Gia Lai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (963, 'Phù Cát', 11, NULL, NULL, NULL, 'Gia Lai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (964, 'Phù Mỹ', 11, NULL, NULL, NULL, 'Gia Lai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (965, 'Phù Mỹ Bắc', 11, NULL, NULL, NULL, 'Gia Lai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (966, 'Phù Mỹ Đông', 11, NULL, NULL, NULL, 'Gia Lai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (967, 'Phù Mỹ Nam', 11, NULL, NULL, NULL, 'Gia Lai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (968, 'Phù Mỹ Tây', 11, NULL, NULL, NULL, 'Gia Lai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (969, 'Phú Thiện', 11, NULL, NULL, NULL, 'Gia Lai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (970, 'Phú Túc', 11, NULL, NULL, NULL, 'Gia Lai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (971, 'Pleiku', 11, NULL, NULL, NULL, 'Gia Lai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (972, 'Pờ Tó', 11, NULL, NULL, NULL, 'Gia Lai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (973, 'Quy Nhơn', 11, NULL, NULL, NULL, 'Gia Lai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (974, 'Quy Nhơn Bắc', 11, NULL, NULL, NULL, 'Gia Lai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (975, 'Quy Nhơn Đông', 11, NULL, NULL, NULL, 'Gia Lai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (976, 'Quy Nhơn Nam', 11, NULL, NULL, NULL, 'Gia Lai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (977, 'Quy Nhơn Tây', 11, NULL, NULL, NULL, 'Gia Lai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (978, 'Sơn Lang', 11, NULL, NULL, NULL, 'Gia Lai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (979, 'SRó', 11, NULL, NULL, NULL, 'Gia Lai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (980, 'Tam Quan', 11, NULL, NULL, NULL, 'Gia Lai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (981, 'Tây Sơn', 11, NULL, NULL, NULL, 'Gia Lai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (982, 'Thống Nhất', 11, NULL, NULL, NULL, 'Gia Lai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (983, 'Tơ Tung', 11, NULL, NULL, NULL, 'Gia Lai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (984, 'Tuy Phước', 11, NULL, NULL, NULL, 'Gia Lai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (985, 'Tuy Phước Bắc', 11, NULL, NULL, NULL, 'Gia Lai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (986, 'Tuy Phước Đông', 11, NULL, NULL, NULL, 'Gia Lai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (987, 'Tuy Phước Tây', 11, NULL, NULL, NULL, 'Gia Lai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (988, 'Uar', 11, NULL, NULL, NULL, 'Gia Lai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (989, 'Vạn Đức', 11, NULL, NULL, NULL, 'Gia Lai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (990, 'Vân Canh', 11, NULL, NULL, NULL, 'Gia Lai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (991, 'Vĩnh Quang', 11, NULL, NULL, NULL, 'Gia Lai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (992, 'Vĩnh Sơn', 11, NULL, NULL, NULL, 'Gia Lai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (993, 'Vĩnh Thạnh', 11, NULL, NULL, NULL, 'Gia Lai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (994, 'Vĩnh Thịnh', 11, NULL, NULL, NULL, 'Gia Lai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (995, 'Xuân An', 11, NULL, NULL, NULL, 'Gia Lai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (996, 'Ya Hội', 11, NULL, NULL, NULL, 'Gia Lai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (997, 'Ya Ma', 11, NULL, NULL, NULL, 'Gia Lai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (998, 'An Khánh', 12, NULL, NULL, NULL, 'Hà Nội');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (999, 'Ba Đình', 12, NULL, NULL, NULL, 'Hà Nội');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1000, 'Ba Vì', 12, NULL, NULL, NULL, 'Hà Nội');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1001, 'Bạch Mai', 12, NULL, NULL, NULL, 'Hà Nội');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1002, 'Bát Tràng', 12, NULL, NULL, NULL, 'Hà Nội');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1003, 'Bất Bạt', 12, NULL, NULL, NULL, 'Hà Nội');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1004, 'Bình Minh', 12, NULL, NULL, NULL, 'Hà Nội');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1005, 'Bồ Đề', 12, NULL, NULL, NULL, 'Hà Nội');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1006, 'Cầu Giấy', 12, NULL, NULL, NULL, 'Hà Nội');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1007, 'Chuyên Mỹ', 12, NULL, NULL, NULL, 'Hà Nội');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1008, 'Chương Dương', 12, NULL, NULL, NULL, 'Hà Nội');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1009, 'Chương Mỹ', 12, NULL, NULL, NULL, 'Hà Nội');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1010, 'Cổ Đô', 12, NULL, NULL, NULL, 'Hà Nội');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1011, 'Cửa Nam', 12, NULL, NULL, NULL, 'Hà Nội');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1012, 'Dân Hòa', 12, NULL, NULL, NULL, 'Hà Nội');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1013, 'Dương Hòa', 12, NULL, NULL, NULL, 'Hà Nội');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1014, 'Dương Nội', 12, NULL, NULL, NULL, 'Hà Nội');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1015, 'Đa Phúc', 12, NULL, NULL, NULL, 'Hà Nội');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1016, 'Đại Mỗ', 12, NULL, NULL, NULL, 'Hà Nội');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1017, 'Đại Thanh', 12, NULL, NULL, NULL, 'Hà Nội');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1018, 'Đại Xuyên', 12, NULL, NULL, NULL, 'Hà Nội');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1019, 'Đan Phượng', 12, NULL, NULL, NULL, 'Hà Nội');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1020, 'Định Công', 12, NULL, NULL, NULL, 'Hà Nội');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1021, 'Đoài Phương', 12, NULL, NULL, NULL, 'Hà Nội');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1022, 'Đông Anh', 12, NULL, NULL, NULL, 'Hà Nội');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1023, 'Đống Đa', 12, NULL, NULL, NULL, 'Hà Nội');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1024, 'Đông Ngạc', 12, NULL, NULL, NULL, 'Hà Nội');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1025, 'Gia Lâm', 12, NULL, NULL, NULL, 'Hà Nội');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1026, 'Giảng Võ', 12, NULL, NULL, NULL, 'Hà Nội');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1027, 'Hạ Bằng', 12, NULL, NULL, NULL, 'Hà Nội');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1028, 'Hà Đông', 12, NULL, NULL, NULL, 'Hà Nội');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1029, 'Hai Bà Trưng', 12, NULL, NULL, NULL, 'Hà Nội');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1030, 'Hát Môn', 12, NULL, NULL, NULL, 'Hà Nội');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1031, 'Hòa Lạc', 12, NULL, NULL, NULL, 'Hà Nội');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1032, 'Hòa Phú', 12, NULL, NULL, NULL, 'Hà Nội');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1033, 'Hòa Xá', 12, NULL, NULL, NULL, 'Hà Nội');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1034, 'Hoài Đức', 12, NULL, NULL, NULL, 'Hà Nội');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1035, 'Hoàn Kiếm', 12, NULL, NULL, NULL, 'Hà Nội');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1036, 'Hoàng Liệt', 12, NULL, NULL, NULL, 'Hà Nội');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1037, 'Hoàng Mai', 12, NULL, NULL, NULL, 'Hà Nội');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1038, 'Hồng Hà', 12, NULL, NULL, NULL, 'Hà Nội');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1039, 'Hồng Sơn', 12, NULL, NULL, NULL, 'Hà Nội');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1040, 'Hồng Vân', 12, NULL, NULL, NULL, 'Hà Nội');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1041, 'Hưng Đạo', 12, NULL, NULL, NULL, 'Hà Nội');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1042, 'Hương Sơn', 12, NULL, NULL, NULL, 'Hà Nội');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1043, 'Khương Đình', 12, NULL, NULL, NULL, 'Hà Nội');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1044, 'Kiến Hưng', 12, NULL, NULL, NULL, 'Hà Nội');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1045, 'Kiều Phú', 12, NULL, NULL, NULL, 'Hà Nội');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1046, 'Kim Anh', 12, NULL, NULL, NULL, 'Hà Nội');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1047, 'Kim Liên', 12, NULL, NULL, NULL, 'Hà Nội');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1048, 'Láng', 12, NULL, NULL, NULL, 'Hà Nội');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1049, 'Liên Minh', 12, NULL, NULL, NULL, 'Hà Nội');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1050, 'Lĩnh Nam', 12, NULL, NULL, NULL, 'Hà Nội');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1051, 'Long Biên', 12, NULL, NULL, NULL, 'Hà Nội');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1052, 'Mê Linh', 12, NULL, NULL, NULL, 'Hà Nội');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1053, 'Minh Châu', 12, NULL, NULL, NULL, 'Hà Nội');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1054, 'Mỹ Đức', 12, NULL, NULL, NULL, 'Hà Nội');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1055, 'Nam Phù', 12, NULL, NULL, NULL, 'Hà Nội');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1056, 'Nghĩa Đô', 12, NULL, NULL, NULL, 'Hà Nội');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1057, 'Ngọc Hà', 12, NULL, NULL, NULL, 'Hà Nội');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1058, 'Ngọc Hồi', 12, NULL, NULL, NULL, 'Hà Nội');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1059, 'Nội Bài', 12, NULL, NULL, NULL, 'Hà Nội');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1060, 'Ô Chợ Dừa', 12, NULL, NULL, NULL, 'Hà Nội');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1061, 'Ô Diên', 12, NULL, NULL, NULL, 'Hà Nội');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1062, 'Phú Cát', 12, NULL, NULL, NULL, 'Hà Nội');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1063, 'Phú Diễn', 12, NULL, NULL, NULL, 'Hà Nội');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1064, 'Phù Đổng', 12, NULL, NULL, NULL, 'Hà Nội');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1065, 'Phú Lương', 12, NULL, NULL, NULL, 'Hà Nội');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1066, 'Phú Nghĩa', 12, NULL, NULL, NULL, 'Hà Nội');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1067, 'Phú Thượng', 12, NULL, NULL, NULL, 'Hà Nội');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1068, 'Phú Xuyên', 12, NULL, NULL, NULL, 'Hà Nội');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1069, 'Phúc Lộc', 12, NULL, NULL, NULL, 'Hà Nội');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1070, 'Phúc Lợi', 12, NULL, NULL, NULL, 'Hà Nội');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1071, 'Phúc Sơn', 12, NULL, NULL, NULL, 'Hà Nội');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1072, 'Phúc Thịnh', 12, NULL, NULL, NULL, 'Hà Nội');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1073, 'Phúc Thọ', 12, NULL, NULL, NULL, 'Hà Nội');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1074, 'Phượng Dực', 12, NULL, NULL, NULL, 'Hà Nội');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1075, 'Phương Liệt', 12, NULL, NULL, NULL, 'Hà Nội');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1076, 'Quảng Bị', 12, NULL, NULL, NULL, 'Hà Nội');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1077, 'Quang Minh', 12, NULL, NULL, NULL, 'Hà Nội');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1078, 'Quảng Oai', 12, NULL, NULL, NULL, 'Hà Nội');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1079, 'Quốc Oai', 12, NULL, NULL, NULL, 'Hà Nội');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1080, 'Sóc Sơn', 12, NULL, NULL, NULL, 'Hà Nội');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1081, 'Sơn Đồng', 12, NULL, NULL, NULL, 'Hà Nội');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1082, 'Sơn Tây', 12, NULL, NULL, NULL, 'Hà Nội');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1083, 'Suối Hai', 12, NULL, NULL, NULL, 'Hà Nội');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1084, 'Tam Hưng', 12, NULL, NULL, NULL, 'Hà Nội');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1085, 'Tây Hồ', 12, NULL, NULL, NULL, 'Hà Nội');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1086, 'Tây Mỗ', 12, NULL, NULL, NULL, 'Hà Nội');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1087, 'Tây Phương', 12, NULL, NULL, NULL, 'Hà Nội');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1088, 'Tây Tựu', 12, NULL, NULL, NULL, 'Hà Nội');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1089, 'Thạch Thất', 12, NULL, NULL, NULL, 'Hà Nội');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1090, 'Thanh Liệt', 12, NULL, NULL, NULL, 'Hà Nội');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1091, 'Thanh Oai', 12, NULL, NULL, NULL, 'Hà Nội');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1092, 'Thanh Trì', 12, NULL, NULL, NULL, 'Hà Nội');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1093, 'Thanh Xuân', 12, NULL, NULL, NULL, 'Hà Nội');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1094, 'Thiên Lộc', 12, NULL, NULL, NULL, 'Hà Nội');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1095, 'Thuận An', 12, NULL, NULL, NULL, 'Hà Nội');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1096, 'Thư Lâm', 12, NULL, NULL, NULL, 'Hà Nội');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1097, 'Thượng Cát', 12, NULL, NULL, NULL, 'Hà Nội');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1098, 'Thượng Phúc', 12, NULL, NULL, NULL, 'Hà Nội');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1099, 'Thường Tín', 12, NULL, NULL, NULL, 'Hà Nội');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1100, 'Tiến Thắng', 12, NULL, NULL, NULL, 'Hà Nội');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1101, 'Trần Phú', 12, NULL, NULL, NULL, 'Hà Nội');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1102, 'Trung Giã', 12, NULL, NULL, NULL, 'Hà Nội');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1103, 'Tùng Thiện', 12, NULL, NULL, NULL, 'Hà Nội');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1104, 'Từ Liêm', 12, NULL, NULL, NULL, 'Hà Nội');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1105, 'Tương Mai', 12, NULL, NULL, NULL, 'Hà Nội');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1106, 'Ứng Hòa', 12, NULL, NULL, NULL, 'Hà Nội');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1107, 'Ứng Thiên', 12, NULL, NULL, NULL, 'Hà Nội');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1108, 'Văn Miếu - Quốc Tử Giám', 12, NULL, NULL, NULL, 'Hà Nội');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1109, 'Vân Đình', 12, NULL, NULL, NULL, 'Hà Nội');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1110, 'Vật Lại', 12, NULL, NULL, NULL, 'Hà Nội');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1111, 'Việt Hưng', 12, NULL, NULL, NULL, 'Hà Nội');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1112, 'Vĩnh Hưng', 12, NULL, NULL, NULL, 'Hà Nội');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1113, 'Vĩnh Thanh', 12, NULL, NULL, NULL, 'Hà Nội');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1114, 'Vĩnh Tuy', 12, NULL, NULL, NULL, 'Hà Nội');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1115, 'Xuân Đỉnh', 12, NULL, NULL, NULL, 'Hà Nội');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1116, 'Xuân Mai', 12, NULL, NULL, NULL, 'Hà Nội');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1117, 'Xuân Phương', 12, NULL, NULL, NULL, 'Hà Nội');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1118, 'Yên Bài', 12, NULL, NULL, NULL, 'Hà Nội');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1119, 'Yên Hòa', 12, NULL, NULL, NULL, 'Hà Nội');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1120, 'Yên Lãng', 12, NULL, NULL, NULL, 'Hà Nội');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1121, 'Yên Nghĩa', 12, NULL, NULL, NULL, 'Hà Nội');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1122, 'Yên Sở', 12, NULL, NULL, NULL, 'Hà Nội');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1123, 'Yên Xuân', 12, NULL, NULL, NULL, 'Hà Nội');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1124, 'Bắc Hồng Lĩnh', 13, NULL, NULL, NULL, 'Hà Tĩnh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1125, 'Can Lộc', 13, NULL, NULL, NULL, 'Hà Tĩnh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1126, 'Cẩm Bình', 13, NULL, NULL, NULL, 'Hà Tĩnh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1127, 'Cẩm Duệ', 13, NULL, NULL, NULL, 'Hà Tĩnh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1128, 'Cẩm Hưng', 13, NULL, NULL, NULL, 'Hà Tĩnh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1129, 'Cẩm Lạc', 13, NULL, NULL, NULL, 'Hà Tĩnh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1130, 'Cẩm Trung', 13, NULL, NULL, NULL, 'Hà Tĩnh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1131, 'Cẩm Xuyên', 13, NULL, NULL, NULL, 'Hà Tĩnh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1132, 'Cổ Đạm', 13, NULL, NULL, NULL, 'Hà Tĩnh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1133, 'Đan Hải', 13, NULL, NULL, NULL, 'Hà Tĩnh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1134, 'Đông Kinh', 13, NULL, NULL, NULL, 'Hà Tĩnh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1135, 'Đồng Lộc', 13, NULL, NULL, NULL, 'Hà Tĩnh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1136, 'Đồng Tiến', 13, NULL, NULL, NULL, 'Hà Tĩnh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1137, 'Đức Đồng', 13, NULL, NULL, NULL, 'Hà Tĩnh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1138, 'Đức Minh', 13, NULL, NULL, NULL, 'Hà Tĩnh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1139, 'Đức Quang', 13, NULL, NULL, NULL, 'Hà Tĩnh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1140, 'Đức Thịnh', 13, NULL, NULL, NULL, 'Hà Tĩnh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1141, 'Đức Thọ', 13, NULL, NULL, NULL, 'Hà Tĩnh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1142, 'Gia Hanh', 13, NULL, NULL, NULL, 'Hà Tĩnh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1143, 'Hà Huy Tập', 13, NULL, NULL, NULL, 'Hà Tĩnh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1144, 'Hà Linh', 13, NULL, NULL, NULL, 'Hà Tĩnh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1145, 'Hải Ninh', 13, NULL, NULL, NULL, 'Hà Tĩnh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1146, 'Hoành Sơn', 13, NULL, NULL, NULL, 'Hà Tĩnh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1147, 'Hồng Lộc', 13, NULL, NULL, NULL, 'Hà Tĩnh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1148, 'Hương Bình', 13, NULL, NULL, NULL, 'Hà Tĩnh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1149, 'Hương Đô', 13, NULL, NULL, NULL, 'Hà Tĩnh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1150, 'Hương Khê', 13, NULL, NULL, NULL, 'Hà Tĩnh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1151, 'Hương Phố', 13, NULL, NULL, NULL, 'Hà Tĩnh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1152, 'Hương Sơn', 13, NULL, NULL, NULL, 'Hà Tĩnh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1153, 'Hương Xuân', 13, NULL, NULL, NULL, 'Hà Tĩnh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1154, 'Kim Hoa', 13, NULL, NULL, NULL, 'Hà Tĩnh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1155, 'Kỳ Anh', 13, NULL, NULL, NULL, 'Hà Tĩnh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1156, 'Kỳ Hoa', 13, NULL, NULL, NULL, 'Hà Tĩnh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1157, 'Kỳ Khang', 13, NULL, NULL, NULL, 'Hà Tĩnh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1158, 'Kỳ Lạc', 13, NULL, NULL, NULL, 'Hà Tĩnh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1159, 'Kỳ Thượng', 13, NULL, NULL, NULL, 'Hà Tĩnh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1160, 'Kỳ Văn', 13, NULL, NULL, NULL, 'Hà Tĩnh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1161, 'Kỳ Xuân', 13, NULL, NULL, NULL, 'Hà Tĩnh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1162, 'Lộc Hà', 13, NULL, NULL, NULL, 'Hà Tĩnh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1163, 'Mai Hoa', 13, NULL, NULL, NULL, 'Hà Tĩnh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1164, 'Mai Phụ', 13, NULL, NULL, NULL, 'Hà Tĩnh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1165, 'Nam Hồng Lĩnh', 13, NULL, NULL, NULL, 'Hà Tĩnh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1166, 'Nghi Xuân', 13, NULL, NULL, NULL, 'Hà Tĩnh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1167, 'Phúc Trạch', 13, NULL, NULL, NULL, 'Hà Tĩnh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1168, 'Sông Trí', 13, NULL, NULL, NULL, 'Hà Tĩnh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1169, 'Sơn Giang', 13, NULL, NULL, NULL, 'Hà Tĩnh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1170, 'Sơn Hồng', 13, NULL, NULL, NULL, 'Hà Tĩnh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1171, 'Sơn Kim 1', 13, NULL, NULL, NULL, 'Hà Tĩnh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1172, 'Sơn Kim 2', 13, NULL, NULL, NULL, 'Hà Tĩnh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1173, 'Sơn Tây', 13, NULL, NULL, NULL, 'Hà Tĩnh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1174, 'Sơn Tiến', 13, NULL, NULL, NULL, 'Hà Tĩnh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1175, 'Thạch Hà', 13, NULL, NULL, NULL, 'Hà Tĩnh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1176, 'Thạch Khê', 13, NULL, NULL, NULL, 'Hà Tĩnh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1177, 'Thạch Lạc', 13, NULL, NULL, NULL, 'Hà Tĩnh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1178, 'Thạch Xuân', 13, NULL, NULL, NULL, 'Hà Tĩnh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1179, 'Thành Sen', 13, NULL, NULL, NULL, 'Hà Tĩnh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1180, 'Thiên Cầm', 13, NULL, NULL, NULL, 'Hà Tĩnh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1181, 'Thượng Đức', 13, NULL, NULL, NULL, 'Hà Tĩnh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1182, 'Tiên Điền', 13, NULL, NULL, NULL, 'Hà Tĩnh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1183, 'Toàn Lưu', 13, NULL, NULL, NULL, 'Hà Tĩnh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1184, 'Trần Phú', 13, NULL, NULL, NULL, 'Hà Tĩnh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1185, 'Trường Lưu', 13, NULL, NULL, NULL, 'Hà Tĩnh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1186, 'Tùng Lộc', 13, NULL, NULL, NULL, 'Hà Tĩnh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1187, 'Tứ Mỹ', 13, NULL, NULL, NULL, 'Hà Tĩnh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1188, 'Việt Xuyên', 13, NULL, NULL, NULL, 'Hà Tĩnh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1189, 'Vũ Quang', 13, NULL, NULL, NULL, 'Hà Tĩnh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1190, 'Vũng Áng', 13, NULL, NULL, NULL, 'Hà Tĩnh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1191, 'Xuân Lộc', 13, NULL, NULL, NULL, 'Hà Tĩnh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1192, 'Yên Hòa', 13, NULL, NULL, NULL, 'Hà Tĩnh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1193, 'Ái Quốc', 14, NULL, NULL, NULL, 'Hải Phòng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1194, 'An Biên', 14, NULL, NULL, NULL, 'Hải Phòng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1195, 'An Dương', 14, NULL, NULL, NULL, 'Hải Phòng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1196, 'An Hải', 14, NULL, NULL, NULL, 'Hải Phòng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1197, 'An Hưng', 14, NULL, NULL, NULL, 'Hải Phòng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1198, 'An Khánh', 14, NULL, NULL, NULL, 'Hải Phòng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1199, 'An Lão', 14, NULL, NULL, NULL, 'Hải Phòng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1200, 'An Phong', 14, NULL, NULL, NULL, 'Hải Phòng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1201, 'An Phú', 14, NULL, NULL, NULL, 'Hải Phòng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1202, 'An Quang', 14, NULL, NULL, NULL, 'Hải Phòng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1203, 'An Thành', 14, NULL, NULL, NULL, 'Hải Phòng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1204, 'An Trường', 14, NULL, NULL, NULL, 'Hải Phòng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1205, 'Bạch Đằng', 14, NULL, NULL, NULL, 'Hải Phòng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1206, 'Bạch Long Vĩ', 14, NULL, NULL, NULL, 'Hải Phòng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1207, 'Bắc An Phụ', 14, NULL, NULL, NULL, 'Hải Phòng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1208, 'Bắc Thanh Miện', 14, NULL, NULL, NULL, 'Hải Phòng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1209, 'Bình Giang', 14, NULL, NULL, NULL, 'Hải Phòng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1210, 'Cát Hải', 14, NULL, NULL, NULL, 'Hải Phòng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1211, 'Cẩm Giang', 14, NULL, NULL, NULL, 'Hải Phòng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1212, 'Cẩm Giàng', 14, NULL, NULL, NULL, 'Hải Phòng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1213, 'Chấn Hưng', 14, NULL, NULL, NULL, 'Hải Phòng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1214, 'Chí Linh', 14, NULL, NULL, NULL, 'Hải Phòng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1215, 'Chí Minh', 14, NULL, NULL, NULL, 'Hải Phòng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1216, 'Chu Văn An', 14, NULL, NULL, NULL, 'Hải Phòng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1217, 'Dương Kinh', 14, NULL, NULL, NULL, 'Hải Phòng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1218, 'Đại Sơn', 14, NULL, NULL, NULL, 'Hải Phòng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1219, 'Đồ Sơn', 14, NULL, NULL, NULL, 'Hải Phòng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1220, 'Đông Hải', 14, NULL, NULL, NULL, 'Hải Phòng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1221, 'Đường An', 14, NULL, NULL, NULL, 'Hải Phòng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1222, 'Gia Lộc', 14, NULL, NULL, NULL, 'Hải Phòng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1223, 'Gia Phúc', 14, NULL, NULL, NULL, 'Hải Phòng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1224, 'Gia Viên', 14, NULL, NULL, NULL, 'Hải Phòng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1225, 'Hà Bắc', 14, NULL, NULL, NULL, 'Hải Phòng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1226, 'Hà Đông', 14, NULL, NULL, NULL, 'Hải Phòng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1227, 'Hà Nam', 14, NULL, NULL, NULL, 'Hải Phòng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1228, 'Hà Tây', 14, NULL, NULL, NULL, 'Hải Phòng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1229, 'Hải An', 14, NULL, NULL, NULL, 'Hải Phòng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1230, 'Hải Dương', 14, NULL, NULL, NULL, 'Hải Phòng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1231, 'Hải Hưng', 14, NULL, NULL, NULL, 'Hải Phòng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1232, 'Hòa Bình', 14, NULL, NULL, NULL, 'Hải Phòng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1233, 'Hồng An', 14, NULL, NULL, NULL, 'Hải Phòng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1234, 'Hồng Bàng', 14, NULL, NULL, NULL, 'Hải Phòng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1235, 'Hồng Châu', 14, NULL, NULL, NULL, 'Hải Phòng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1236, 'Hợp Tiến', 14, NULL, NULL, NULL, 'Hải Phòng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1237, 'Hùng Thắng', 14, NULL, NULL, NULL, 'Hải Phòng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1238, 'Hưng Đạo', 14, NULL, NULL, NULL, 'Hải Phòng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1239, 'Kẻ Sặt', 14, NULL, NULL, NULL, 'Hải Phòng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1240, 'Khúc Thừa Dụ', 14, NULL, NULL, NULL, 'Hải Phòng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1241, 'Kiến An', 14, NULL, NULL, NULL, 'Hải Phòng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1242, 'Kiến Hải', 14, NULL, NULL, NULL, 'Hải Phòng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1243, 'Kiến Hưng', 14, NULL, NULL, NULL, 'Hải Phòng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1244, 'Kiến Minh', 14, NULL, NULL, NULL, 'Hải Phòng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1245, 'Kiến Thụy', 14, NULL, NULL, NULL, 'Hải Phòng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1246, 'Kim Thành', 14, NULL, NULL, NULL, 'Hải Phòng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1247, 'Kinh Môn', 14, NULL, NULL, NULL, 'Hải Phòng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1248, 'Lạc Phượng', 14, NULL, NULL, NULL, 'Hải Phòng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1249, 'Lai Khê', 14, NULL, NULL, NULL, 'Hải Phòng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1250, 'Lê Chân', 14, NULL, NULL, NULL, 'Hải Phòng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1251, 'Lê Đại Hành', 14, NULL, NULL, NULL, 'Hải Phòng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1252, 'Lê Ích Mộc', 14, NULL, NULL, NULL, 'Hải Phòng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1253, 'Lê Thanh Nghị', 14, NULL, NULL, NULL, 'Hải Phòng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1254, 'Lưu Kiếm', 14, NULL, NULL, NULL, 'Hải Phòng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1255, 'Mao Điền', 14, NULL, NULL, NULL, 'Hải Phòng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1256, 'Nam An Phụ', 14, NULL, NULL, NULL, 'Hải Phòng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1257, 'Nam Đồ Sơn', 14, NULL, NULL, NULL, 'Hải Phòng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1258, 'Nam Đồng', 14, NULL, NULL, NULL, 'Hải Phòng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1259, 'Nam Sách', 14, NULL, NULL, NULL, 'Hải Phòng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1260, 'Nam Thanh Miện', 14, NULL, NULL, NULL, 'Hải Phòng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1261, 'Nam Triệu', 14, NULL, NULL, NULL, 'Hải Phòng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1262, 'Nghi Dương', 14, NULL, NULL, NULL, 'Hải Phòng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1263, 'Ngô Quyền', 14, NULL, NULL, NULL, 'Hải Phòng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1264, 'Nguyễn Bỉnh Khiêm', 14, NULL, NULL, NULL, 'Hải Phòng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1265, 'Nguyễn Đại Năng', 14, NULL, NULL, NULL, 'Hải Phòng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1266, 'Nguyên Giáp', 14, NULL, NULL, NULL, 'Hải Phòng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1267, 'Nguyễn Lương Bằng', 14, NULL, NULL, NULL, 'Hải Phòng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1268, 'Nguyễn Trãi', 14, NULL, NULL, NULL, 'Hải Phòng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1269, 'Nhị Chiểu', 14, NULL, NULL, NULL, 'Hải Phòng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1270, 'Ninh Giang', 14, NULL, NULL, NULL, 'Hải Phòng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1271, 'Phạm Sư Mạnh', 14, NULL, NULL, NULL, 'Hải Phòng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1272, 'Phù Liễn', 14, NULL, NULL, NULL, 'Hải Phòng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1273, 'Phú Thái', 14, NULL, NULL, NULL, 'Hải Phòng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1274, 'Quyết Thắng', 14, NULL, NULL, NULL, 'Hải Phòng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1275, 'Tân An', 14, NULL, NULL, NULL, 'Hải Phòng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1276, 'Tân Hưng', 14, NULL, NULL, NULL, 'Hải Phòng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1277, 'Tân Kỳ', 14, NULL, NULL, NULL, 'Hải Phòng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1278, 'Tân Minh', 14, NULL, NULL, NULL, 'Hải Phòng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1279, 'Thạch Khôi', 14, NULL, NULL, NULL, 'Hải Phòng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1280, 'Thái Tân', 14, NULL, NULL, NULL, 'Hải Phòng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1281, 'Thành Đông', 14, NULL, NULL, NULL, 'Hải Phòng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1282, 'Thanh Hà', 14, NULL, NULL, NULL, 'Hải Phòng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1283, 'Thanh Miện', 14, NULL, NULL, NULL, 'Hải Phòng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1284, 'Thiên Hương', 14, NULL, NULL, NULL, 'Hải Phòng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1285, 'Thủy Nguyên', 14, NULL, NULL, NULL, 'Hải Phòng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1286, 'Thượng Hồng', 14, NULL, NULL, NULL, 'Hải Phòng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1287, 'Tiên Lãng', 14, NULL, NULL, NULL, 'Hải Phòng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1288, 'Tiên Minh', 14, NULL, NULL, NULL, 'Hải Phòng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1289, 'Trần Hưng Đạo', 14, NULL, NULL, NULL, 'Hải Phòng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1290, 'Trần Liễu', 14, NULL, NULL, NULL, 'Hải Phòng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1291, 'Trần Nhân Tông', 14, NULL, NULL, NULL, 'Hải Phòng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1292, 'Trần Phú', 14, NULL, NULL, NULL, 'Hải Phòng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1293, 'Trường Tân', 14, NULL, NULL, NULL, 'Hải Phòng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1294, 'Tuệ Tĩnh', 14, NULL, NULL, NULL, 'Hải Phòng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1295, 'Tứ Kỳ', 14, NULL, NULL, NULL, 'Hải Phòng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1296, 'Tứ Minh', 14, NULL, NULL, NULL, 'Hải Phòng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1297, 'Việt Hòa', 14, NULL, NULL, NULL, 'Hải Phòng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1298, 'Việt Khê', 14, NULL, NULL, NULL, 'Hải Phòng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1299, 'Vĩnh Am', 14, NULL, NULL, NULL, 'Hải Phòng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1300, 'Vĩnh Bảo', 14, NULL, NULL, NULL, 'Hải Phòng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1301, 'Vĩnh Hải', 14, NULL, NULL, NULL, 'Hải Phòng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1302, 'Vĩnh Hòa', 14, NULL, NULL, NULL, 'Hải Phòng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1303, 'Vĩnh Lại', 14, NULL, NULL, NULL, 'Hải Phòng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1304, 'Vĩnh Thịnh', 14, NULL, NULL, NULL, 'Hải Phòng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1305, 'Vĩnh Thuận', 14, NULL, NULL, NULL, 'Hải Phòng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1306, 'Yết Kiêu', 14, NULL, NULL, NULL, 'Hải Phòng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1307, 'A Lưới 1', 15, NULL, NULL, NULL, 'Huế');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1308, 'A Lưới 2', 15, NULL, NULL, NULL, 'Huế');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1309, 'A Lưới 3', 15, NULL, NULL, NULL, 'Huế');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1310, 'A Lưới 4', 15, NULL, NULL, NULL, 'Huế');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1311, 'A Lưới 5', 15, NULL, NULL, NULL, 'Huế');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1312, 'An Cựu', 15, NULL, NULL, NULL, 'Huế');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1313, 'Bình Điền', 15, NULL, NULL, NULL, 'Huế');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1314, 'Chân Mây - Lăng Cô', 15, NULL, NULL, NULL, 'Huế');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1315, 'Dương Nỗ', 15, NULL, NULL, NULL, 'Huế');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1316, 'Đan Điền', 15, NULL, NULL, NULL, 'Huế');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1317, 'Hóa Châu', 15, NULL, NULL, NULL, 'Huế');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1318, 'Hưng Lộc', 15, NULL, NULL, NULL, 'Huế');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1319, 'Hương An', 15, NULL, NULL, NULL, 'Huế');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1320, 'Hương Thủy', 15, NULL, NULL, NULL, 'Huế');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1321, 'Hương Trà', 15, NULL, NULL, NULL, 'Huế');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1322, 'Khe Tre', 15, NULL, NULL, NULL, 'Huế');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1323, 'Kim Long', 15, NULL, NULL, NULL, 'Huế');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1324, 'Kim Trà', 15, NULL, NULL, NULL, 'Huế');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1325, 'Long Quảng', 15, NULL, NULL, NULL, 'Huế');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1326, 'Lộc An', 15, NULL, NULL, NULL, 'Huế');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1327, 'Mỹ Thượng', 15, NULL, NULL, NULL, 'Huế');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1328, 'Nam Đông', 15, NULL, NULL, NULL, 'Huế');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1329, 'Phong Dinh', 15, NULL, NULL, NULL, 'Huế');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1330, 'Phong Điền', 15, NULL, NULL, NULL, 'Huế');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1331, 'Phong Phú', 15, NULL, NULL, NULL, 'Huế');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1332, 'Phong Quảng', 15, NULL, NULL, NULL, 'Huế');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1333, 'Phong Thái', 15, NULL, NULL, NULL, 'Huế');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1334, 'Phú Bài', 15, NULL, NULL, NULL, 'Huế');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1335, 'Phú Hồ', 15, NULL, NULL, NULL, 'Huế');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1336, 'Phú Lộc', 15, NULL, NULL, NULL, 'Huế');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1337, 'Phú Vang', 15, NULL, NULL, NULL, 'Huế');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1338, 'Phú Vinh', 15, NULL, NULL, NULL, 'Huế');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1339, 'Phú Xuân', 15, NULL, NULL, NULL, 'Huế');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1340, 'Quảng Điền', 15, NULL, NULL, NULL, 'Huế');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1341, 'Thanh Thủy', 15, NULL, NULL, NULL, 'Huế');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1342, 'Thuận An', 15, NULL, NULL, NULL, 'Huế');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1343, 'Thuận Hóa', 15, NULL, NULL, NULL, 'Huế');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1344, 'Thủy Xuân', 15, NULL, NULL, NULL, 'Huế');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1345, 'Vinh Lộc', 15, NULL, NULL, NULL, 'Huế');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1346, 'Vỹ Dạ', 15, NULL, NULL, NULL, 'Huế');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1347, 'A Sào', 16, NULL, NULL, NULL, 'Hưng Yên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1348, 'Ái Quốc', 16, NULL, NULL, NULL, 'Hưng Yên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1349, 'Ân Thi', 16, NULL, NULL, NULL, 'Hưng Yên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1350, 'Bắc Đông Hưng', 16, NULL, NULL, NULL, 'Hưng Yên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1351, 'Bắc Đông Quan', 16, NULL, NULL, NULL, 'Hưng Yên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1352, 'Bắc Thái Ninh', 16, NULL, NULL, NULL, 'Hưng Yên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1353, 'Bắc Thụy Anh', 16, NULL, NULL, NULL, 'Hưng Yên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1354, 'Bắc Tiên Hưng', 16, NULL, NULL, NULL, 'Hưng Yên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1355, 'Bình Định', 16, NULL, NULL, NULL, 'Hưng Yên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1356, 'Bình Nguyên', 16, NULL, NULL, NULL, 'Hưng Yên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1357, 'Bình Thanh', 16, NULL, NULL, NULL, 'Hưng Yên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1358, 'Châu Ninh', 16, NULL, NULL, NULL, 'Hưng Yên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1359, 'Chí Minh', 16, NULL, NULL, NULL, 'Hưng Yên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1360, 'Diên Hà', 16, NULL, NULL, NULL, 'Hưng Yên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1361, 'Đại Đồng', 16, NULL, NULL, NULL, 'Hưng Yên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1362, 'Đoàn Đào', 16, NULL, NULL, NULL, 'Hưng Yên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1363, 'Đồng Bằng', 16, NULL, NULL, NULL, 'Hưng Yên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1364, 'Đồng Châu', 16, NULL, NULL, NULL, 'Hưng Yên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1365, 'Đông Hưng', 16, NULL, NULL, NULL, 'Hưng Yên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1366, 'Đông Quan', 16, NULL, NULL, NULL, 'Hưng Yên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1367, 'Đông Thái Ninh', 16, NULL, NULL, NULL, 'Hưng Yên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1368, 'Đông Thụy Anh', 16, NULL, NULL, NULL, 'Hưng Yên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1369, 'Đông Tiền Hải', 16, NULL, NULL, NULL, 'Hưng Yên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1370, 'Đông Tiên Hưng', 16, NULL, NULL, NULL, 'Hưng Yên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1371, 'Đức Hợp', 16, NULL, NULL, NULL, 'Hưng Yên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1372, 'Đường Hào', 16, NULL, NULL, NULL, 'Hưng Yên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1373, 'Hiệp Cường', 16, NULL, NULL, NULL, 'Hưng Yên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1374, 'Hoàn Long', 16, NULL, NULL, NULL, 'Hưng Yên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1375, 'Hoàng Hoa Thám', 16, NULL, NULL, NULL, 'Hưng Yên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1376, 'Hồng Châu', 16, NULL, NULL, NULL, 'Hưng Yên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1377, 'Hồng Minh', 16, NULL, NULL, NULL, 'Hưng Yên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1378, 'Hồng Quang', 16, NULL, NULL, NULL, 'Hưng Yên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1379, 'Hồng Vũ', 16, NULL, NULL, NULL, 'Hưng Yên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1380, 'Hưng Hà', 16, NULL, NULL, NULL, 'Hưng Yên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1381, 'Hưng Phú', 16, NULL, NULL, NULL, 'Hưng Yên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1382, 'Khoái Châu', 16, NULL, NULL, NULL, 'Hưng Yên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1383, 'Kiến Xương', 16, NULL, NULL, NULL, 'Hưng Yên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1384, 'Lạc Đạo', 16, NULL, NULL, NULL, 'Hưng Yên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1385, 'Lê Lợi', 16, NULL, NULL, NULL, 'Hưng Yên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1386, 'Lê Quý Đôn', 16, NULL, NULL, NULL, 'Hưng Yên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1387, 'Long Hưng', 16, NULL, NULL, NULL, 'Hưng Yên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1388, 'Lương Bằng', 16, NULL, NULL, NULL, 'Hưng Yên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1389, 'Mễ Sở', 16, NULL, NULL, NULL, 'Hưng Yên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1390, 'Minh Thọ', 16, NULL, NULL, NULL, 'Hưng Yên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1391, 'Mỹ Hào', 16, NULL, NULL, NULL, 'Hưng Yên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1392, 'Nam Cường', 16, NULL, NULL, NULL, 'Hưng Yên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1393, 'Nam Đông Hưng', 16, NULL, NULL, NULL, 'Hưng Yên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1394, 'Nam Thái Ninh', 16, NULL, NULL, NULL, 'Hưng Yên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1395, 'Nam Thụy Anh', 16, NULL, NULL, NULL, 'Hưng Yên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1396, 'Nam Tiền Hải', 16, NULL, NULL, NULL, 'Hưng Yên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1397, 'Nam Tiên Hưng', 16, NULL, NULL, NULL, 'Hưng Yên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1398, 'Nghĩa Dân', 16, NULL, NULL, NULL, 'Hưng Yên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1399, 'Nghĩa Trụ', 16, NULL, NULL, NULL, 'Hưng Yên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1400, 'Ngọc Lâm', 16, NULL, NULL, NULL, 'Hưng Yên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1401, 'Nguyễn Du', 16, NULL, NULL, NULL, 'Hưng Yên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1402, 'Nguyễn Trãi', 16, NULL, NULL, NULL, 'Hưng Yên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1403, 'Nguyễn Văn Linh', 16, NULL, NULL, NULL, 'Hưng Yên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1404, 'Ngự Thiên', 16, NULL, NULL, NULL, 'Hưng Yên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1405, 'Như Quỳnh', 16, NULL, NULL, NULL, 'Hưng Yên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1406, 'Phạm Ngũ Lão', 16, NULL, NULL, NULL, 'Hưng Yên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1407, 'Phố Hiến', 16, NULL, NULL, NULL, 'Hưng Yên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1408, 'Phụ Dực', 16, NULL, NULL, NULL, 'Hưng Yên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1409, 'Phụng Công', 16, NULL, NULL, NULL, 'Hưng Yên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1410, 'Quang Hưng', 16, NULL, NULL, NULL, 'Hưng Yên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1411, 'Quang Lịch', 16, NULL, NULL, NULL, 'Hưng Yên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1412, 'Quỳnh An', 16, NULL, NULL, NULL, 'Hưng Yên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1413, 'Quỳnh Phụ', 16, NULL, NULL, NULL, 'Hưng Yên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1414, 'Sơn Nam', 16, NULL, NULL, NULL, 'Hưng Yên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1415, 'Tân Hưng', 16, NULL, NULL, NULL, 'Hưng Yên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1416, 'Tân Thuận', 16, NULL, NULL, NULL, 'Hưng Yên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1417, 'Tân Tiến', 16, NULL, NULL, NULL, 'Hưng Yên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1418, 'Tây Thái Ninh', 16, NULL, NULL, NULL, 'Hưng Yên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1419, 'Tây Thụy Anh', 16, NULL, NULL, NULL, 'Hưng Yên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1420, 'Tây Tiền Hải', 16, NULL, NULL, NULL, 'Hưng Yên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1421, 'Thái Bình', 16, NULL, NULL, NULL, 'Hưng Yên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1422, 'Thái Ninh', 16, NULL, NULL, NULL, 'Hưng Yên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1423, 'Thái Thụy', 16, NULL, NULL, NULL, 'Hưng Yên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1424, 'Thần Khê', 16, NULL, NULL, NULL, 'Hưng Yên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1425, 'Thụy Anh', 16, NULL, NULL, NULL, 'Hưng Yên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1426, 'Thư Trì', 16, NULL, NULL, NULL, 'Hưng Yên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1427, 'Thư Vũ', 16, NULL, NULL, NULL, 'Hưng Yên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1428, 'Thượng Hồng', 16, NULL, NULL, NULL, 'Hưng Yên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1429, 'Tiền Hải', 16, NULL, NULL, NULL, 'Hưng Yên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1430, 'Tiên Hoa', 16, NULL, NULL, NULL, 'Hưng Yên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1431, 'Tiên Hưng', 16, NULL, NULL, NULL, 'Hưng Yên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1432, 'Tiên La', 16, NULL, NULL, NULL, 'Hưng Yên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1433, 'Tiên Lữ', 16, NULL, NULL, NULL, 'Hưng Yên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1434, 'Tiên Tiến', 16, NULL, NULL, NULL, 'Hưng Yên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1435, 'Tống Trân', 16, NULL, NULL, NULL, 'Hưng Yên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1436, 'Trà Giang', 16, NULL, NULL, NULL, 'Hưng Yên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1437, 'Trà Lý', 16, NULL, NULL, NULL, 'Hưng Yên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1438, 'Trần Hưng Đạo', 16, NULL, NULL, NULL, 'Hưng Yên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1439, 'Trần Lãm', 16, NULL, NULL, NULL, 'Hưng Yên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1440, 'Triệu Việt Vương', 16, NULL, NULL, NULL, 'Hưng Yên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1441, 'Vạn Xuân', 16, NULL, NULL, NULL, 'Hưng Yên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1442, 'Văn Giang', 16, NULL, NULL, NULL, 'Hưng Yên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1443, 'Việt Tiến', 16, NULL, NULL, NULL, 'Hưng Yên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1444, 'Việt Yên', 16, NULL, NULL, NULL, 'Hưng Yên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1445, 'Vũ Phúc', 16, NULL, NULL, NULL, 'Hưng Yên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1446, 'Vũ Quý', 16, NULL, NULL, NULL, 'Hưng Yên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1447, 'Vũ Thư', 16, NULL, NULL, NULL, 'Hưng Yên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1448, 'Vũ Tiên', 16, NULL, NULL, NULL, 'Hưng Yên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1449, 'Xuân Trúc', 16, NULL, NULL, NULL, 'Hưng Yên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1450, 'Yên Mỹ', 16, NULL, NULL, NULL, 'Hưng Yên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1451, 'Anh Dũng', 17, NULL, NULL, NULL, 'Khánh Hòa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1452, 'Ba Ngòi', 17, NULL, NULL, NULL, 'Khánh Hòa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1453, 'Bác Ái', 17, NULL, NULL, NULL, 'Khánh Hòa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1454, 'Bác Ái Đông', 17, NULL, NULL, NULL, 'Khánh Hòa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1455, 'Bác Ái Tây', 17, NULL, NULL, NULL, 'Khánh Hòa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1456, 'Bảo An', 17, NULL, NULL, NULL, 'Khánh Hòa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1457, 'Bắc Cam Ranh', 17, NULL, NULL, NULL, 'Khánh Hòa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1458, 'Bắc Khánh Vĩnh', 17, NULL, NULL, NULL, 'Khánh Hòa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1459, 'Bắc Nha Trang', 17, NULL, NULL, NULL, 'Khánh Hòa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1460, 'Bắc Ninh Hòa', 17, NULL, NULL, NULL, 'Khánh Hòa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1461, 'Cà Ná', 17, NULL, NULL, NULL, 'Khánh Hòa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1462, 'Cam An', 17, NULL, NULL, NULL, 'Khánh Hòa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1463, 'Cam Hiệp', 17, NULL, NULL, NULL, 'Khánh Hòa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1464, 'Cam Lâm', 17, NULL, NULL, NULL, 'Khánh Hòa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1465, 'Cam Linh', 17, NULL, NULL, NULL, 'Khánh Hòa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1466, 'Cam Ranh', 17, NULL, NULL, NULL, 'Khánh Hòa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1467, 'Công Hải', 17, NULL, NULL, NULL, 'Khánh Hòa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1468, 'Diên Điền', 17, NULL, NULL, NULL, 'Khánh Hòa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1469, 'Diên Khánh', 17, NULL, NULL, NULL, 'Khánh Hòa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1470, 'Diên Lạc', 17, NULL, NULL, NULL, 'Khánh Hòa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1471, 'Diên Lâm', 17, NULL, NULL, NULL, 'Khánh Hòa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1472, 'Diên Thọ', 17, NULL, NULL, NULL, 'Khánh Hòa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1473, 'Đại Lãnh', 17, NULL, NULL, NULL, 'Khánh Hòa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1474, 'Đô Vinh', 17, NULL, NULL, NULL, 'Khánh Hòa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1475, 'Đông Hải', 17, NULL, NULL, NULL, 'Khánh Hòa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1476, 'Đông Khánh Sơn', 17, NULL, NULL, NULL, 'Khánh Hòa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1477, 'Đông Ninh Hòa', 17, NULL, NULL, NULL, 'Khánh Hòa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1478, 'Hòa Thắng', 17, NULL, NULL, NULL, 'Khánh Hòa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1479, 'Hòa Trí', 17, NULL, NULL, NULL, 'Khánh Hòa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1480, 'Khánh Sơn', 17, NULL, NULL, NULL, 'Khánh Hòa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1481, 'Khánh Vĩnh', 17, NULL, NULL, NULL, 'Khánh Hòa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1482, 'Lâm Sơn', 17, NULL, NULL, NULL, 'Khánh Hòa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1483, 'Mỹ Sơn', 17, NULL, NULL, NULL, 'Khánh Hòa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1484, 'Nam Cam Ranh', 17, NULL, NULL, NULL, 'Khánh Hòa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1485, 'Nam Khánh Vĩnh', 17, NULL, NULL, NULL, 'Khánh Hòa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1486, 'Nam Nha Trang', 17, NULL, NULL, NULL, 'Khánh Hòa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1487, 'Nam Ninh Hòa', 17, NULL, NULL, NULL, 'Khánh Hòa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1488, 'Nha Trang', 17, NULL, NULL, NULL, 'Khánh Hòa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1489, 'Ninh Chử', 17, NULL, NULL, NULL, 'Khánh Hòa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1490, 'Ninh Hải', 17, NULL, NULL, NULL, 'Khánh Hòa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1491, 'Ninh Hòa', 17, NULL, NULL, NULL, 'Khánh Hòa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1492, 'Ninh Phước', 17, NULL, NULL, NULL, 'Khánh Hòa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1493, 'Ninh Sơn', 17, NULL, NULL, NULL, 'Khánh Hòa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1494, 'Phan Rang', 17, NULL, NULL, NULL, 'Khánh Hòa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1495, 'Phước Dinh', 17, NULL, NULL, NULL, 'Khánh Hòa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1496, 'Phước Hà', 17, NULL, NULL, NULL, 'Khánh Hòa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1497, 'Phước Hậu', 17, NULL, NULL, NULL, 'Khánh Hòa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1498, 'Phước Hữu', 17, NULL, NULL, NULL, 'Khánh Hòa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1499, 'Suối Dầu', 17, NULL, NULL, NULL, 'Khánh Hòa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1500, 'Suối Hiệp', 17, NULL, NULL, NULL, 'Khánh Hòa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1501, 'Tân Định', 17, NULL, NULL, NULL, 'Khánh Hòa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1502, 'Tây Khánh Sơn', 17, NULL, NULL, NULL, 'Khánh Hòa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1503, 'Tây Khánh Vĩnh', 17, NULL, NULL, NULL, 'Khánh Hòa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1504, 'Tây Nha Trang', 17, NULL, NULL, NULL, 'Khánh Hòa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1505, 'Tây Ninh Hòa', 17, NULL, NULL, NULL, 'Khánh Hòa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1506, 'Thuận Bắc', 17, NULL, NULL, NULL, 'Khánh Hòa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1507, 'Thuận Nam', 17, NULL, NULL, NULL, 'Khánh Hòa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1508, 'Trung Khánh Vĩnh', 17, NULL, NULL, NULL, 'Khánh Hòa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1509, 'Trường Sa', 17, NULL, NULL, NULL, 'Khánh Hòa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1510, 'Tu Bông', 17, NULL, NULL, NULL, 'Khánh Hòa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1511, 'Vạn Hưng', 17, NULL, NULL, NULL, 'Khánh Hòa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1512, 'Vạn Ninh', 17, NULL, NULL, NULL, 'Khánh Hòa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1513, 'Vạn Thắng', 17, NULL, NULL, NULL, 'Khánh Hòa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1514, 'Vĩnh Hải', 17, NULL, NULL, NULL, 'Khánh Hòa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1515, 'Xuân Hải', 17, NULL, NULL, NULL, 'Khánh Hòa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1516, 'Bản Bo', 18, NULL, NULL, NULL, 'Lai Châu');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1517, 'Bình Lư', 18, NULL, NULL, NULL, 'Lai Châu');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1518, 'Bum Nưa', 18, NULL, NULL, NULL, 'Lai Châu');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1519, 'Bum Tở', 18, NULL, NULL, NULL, 'Lai Châu');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1520, 'Dào San', 18, NULL, NULL, NULL, 'Lai Châu');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1521, 'Đoàn Kết', 18, NULL, NULL, NULL, 'Lai Châu');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1522, 'Hồng Thu', 18, NULL, NULL, NULL, 'Lai Châu');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1523, 'Hua Bum', 18, NULL, NULL, NULL, 'Lai Châu');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1524, 'Khoen On', 18, NULL, NULL, NULL, 'Lai Châu');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1525, 'Khổng Lào', 18, NULL, NULL, NULL, 'Lai Châu');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1526, 'Khun Há', 18, NULL, NULL, NULL, 'Lai Châu');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1527, 'Lê Lợi', 18, NULL, NULL, NULL, 'Lai Châu');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1528, 'Mù Cả', 18, NULL, NULL, NULL, 'Lai Châu');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1529, 'Mường Khoa', 18, NULL, NULL, NULL, 'Lai Châu');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1530, 'Mường Kim', 18, NULL, NULL, NULL, 'Lai Châu');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1531, 'Mường Mô', 18, NULL, NULL, NULL, 'Lai Châu');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1532, 'Mường Tè', 18, NULL, NULL, NULL, 'Lai Châu');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1533, 'Mường Than', 18, NULL, NULL, NULL, 'Lai Châu');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1534, 'Nậm Cuổi', 18, NULL, NULL, NULL, 'Lai Châu');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1535, 'Nậm Hàng', 18, NULL, NULL, NULL, 'Lai Châu');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1536, 'Nậm Mạ', 18, NULL, NULL, NULL, 'Lai Châu');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1537, 'Nậm Sỏ', 18, NULL, NULL, NULL, 'Lai Châu');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1538, 'Nậm Tăm', 18, NULL, NULL, NULL, 'Lai Châu');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1539, 'Pa Tần', 18, NULL, NULL, NULL, 'Lai Châu');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1540, 'Pa Ủ', 18, NULL, NULL, NULL, 'Lai Châu');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1541, 'Pắc Ta', 18, NULL, NULL, NULL, 'Lai Châu');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1542, 'Phong Thổ', 18, NULL, NULL, NULL, 'Lai Châu');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1543, 'Pu Sam Cáp', 18, NULL, NULL, NULL, 'Lai Châu');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1544, 'Sì Lở Lầu', 18, NULL, NULL, NULL, 'Lai Châu');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1545, 'Sìn Hồ', 18, NULL, NULL, NULL, 'Lai Châu');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1546, 'Sin Suối Hồ', 18, NULL, NULL, NULL, 'Lai Châu');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1547, 'Tả Lèng', 18, NULL, NULL, NULL, 'Lai Châu');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1548, 'Tà Tổng', 18, NULL, NULL, NULL, 'Lai Châu');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1549, 'Tân Phong', 18, NULL, NULL, NULL, 'Lai Châu');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1550, 'Tân Uyên', 18, NULL, NULL, NULL, 'Lai Châu');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1551, 'Than Uyên', 18, NULL, NULL, NULL, 'Lai Châu');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1552, 'Thu Lũm', 18, NULL, NULL, NULL, 'Lai Châu');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1553, 'Tủa Sín Chải', 18, NULL, NULL, NULL, 'Lai Châu');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1554, 'Ba Sơn', 19, NULL, NULL, NULL, 'Lạng Sơn');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1555, 'Bắc Sơn', 19, NULL, NULL, NULL, 'Lạng Sơn');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1556, 'Bằng Mạc', 19, NULL, NULL, NULL, 'Lạng Sơn');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1557, 'Bình Gia', 19, NULL, NULL, NULL, 'Lạng Sơn');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1558, 'Cai Kinh', 19, NULL, NULL, NULL, 'Lạng Sơn');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1559, 'Cao Lộc', 19, NULL, NULL, NULL, 'Lạng Sơn');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1560, 'Châu Sơn', 19, NULL, NULL, NULL, 'Lạng Sơn');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1561, 'Chi Lăng', 19, NULL, NULL, NULL, 'Lạng Sơn');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1562, 'Chiến Thắng', 19, NULL, NULL, NULL, 'Lạng Sơn');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1563, 'Công Sơn', 19, NULL, NULL, NULL, 'Lạng Sơn');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1564, 'Điềm He', 19, NULL, NULL, NULL, 'Lạng Sơn');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1565, 'Đình Lập', 19, NULL, NULL, NULL, 'Lạng Sơn');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1566, 'Đoàn Kết', 19, NULL, NULL, NULL, 'Lạng Sơn');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1567, 'Đồng Đăng', 19, NULL, NULL, NULL, 'Lạng Sơn');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1568, 'Đông Kinh', 19, NULL, NULL, NULL, 'Lạng Sơn');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1569, 'Hoa Thám', 19, NULL, NULL, NULL, 'Lạng Sơn');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1570, 'Hoàng Văn Thụ', 19, NULL, NULL, NULL, 'Lạng Sơn');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1571, 'Hội Hoan', 19, NULL, NULL, NULL, 'Lạng Sơn');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1572, 'Hồng Phong', 19, NULL, NULL, NULL, 'Lạng Sơn');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1573, 'Hưng Vũ', 19, NULL, NULL, NULL, 'Lạng Sơn');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1574, 'Hữu Liên', 19, NULL, NULL, NULL, 'Lạng Sơn');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1575, 'Hữu Lũng', 19, NULL, NULL, NULL, 'Lạng Sơn');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1576, 'Kháng Chiến', 19, NULL, NULL, NULL, 'Lạng Sơn');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1577, 'Khánh Khê', 19, NULL, NULL, NULL, 'Lạng Sơn');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1578, 'Khuất Xá', 19, NULL, NULL, NULL, 'Lạng Sơn');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1579, 'Kiên Mộc', 19, NULL, NULL, NULL, 'Lạng Sơn');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1580, 'Kỳ Lừa', 19, NULL, NULL, NULL, 'Lạng Sơn');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1581, 'Lộc Bình', 19, NULL, NULL, NULL, 'Lạng Sơn');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1582, 'Lợi Bác', 19, NULL, NULL, NULL, 'Lạng Sơn');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1583, 'Lương Văn Tri', 19, NULL, NULL, NULL, 'Lạng Sơn');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1584, 'Mẫu Sơn', 19, NULL, NULL, NULL, 'Lạng Sơn');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1585, 'Na Dương', 19, NULL, NULL, NULL, 'Lạng Sơn');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1586, 'Na Sầm', 19, NULL, NULL, NULL, 'Lạng Sơn');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1587, 'Nhân Lý', 19, NULL, NULL, NULL, 'Lạng Sơn');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1588, 'Nhất Hòa', 19, NULL, NULL, NULL, 'Lạng Sơn');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1589, 'Quan Sơn', 19, NULL, NULL, NULL, 'Lạng Sơn');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1590, 'Quốc Khánh', 19, NULL, NULL, NULL, 'Lạng Sơn');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1591, 'Quốc Việt', 19, NULL, NULL, NULL, 'Lạng Sơn');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1592, 'Quý Hòa', 19, NULL, NULL, NULL, 'Lạng Sơn');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1593, 'Tam Thanh', 19, NULL, NULL, NULL, 'Lạng Sơn');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1594, 'Tân Đoàn', 19, NULL, NULL, NULL, 'Lạng Sơn');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1595, 'Tân Thành', 19, NULL, NULL, NULL, 'Lạng Sơn');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1596, 'Tân Tiến', 19, NULL, NULL, NULL, 'Lạng Sơn');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1597, 'Tân Tri', 19, NULL, NULL, NULL, 'Lạng Sơn');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1598, 'Tân Văn', 19, NULL, NULL, NULL, 'Lạng Sơn');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1599, 'Thái Bình', 19, NULL, NULL, NULL, 'Lạng Sơn');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1600, 'Thất Khê', 19, NULL, NULL, NULL, 'Lạng Sơn');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1601, 'Thiện Hòa', 19, NULL, NULL, NULL, 'Lạng Sơn');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1602, 'Thiện Long', 19, NULL, NULL, NULL, 'Lạng Sơn');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1603, 'Thiện Tân', 19, NULL, NULL, NULL, 'Lạng Sơn');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1604, 'Thiện Thuật', 19, NULL, NULL, NULL, 'Lạng Sơn');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1605, 'Thống Nhất', 19, NULL, NULL, NULL, 'Lạng Sơn');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1606, 'Thụy Hùng', 19, NULL, NULL, NULL, 'Lạng Sơn');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1607, 'Tràng Định', 19, NULL, NULL, NULL, 'Lạng Sơn');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1608, 'Tri Lễ', 19, NULL, NULL, NULL, 'Lạng Sơn');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1609, 'Tuấn Sơn', 19, NULL, NULL, NULL, 'Lạng Sơn');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1610, 'Vạn Linh', 19, NULL, NULL, NULL, 'Lạng Sơn');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1611, 'Văn Lãng', 19, NULL, NULL, NULL, 'Lạng Sơn');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1612, 'Văn Quan', 19, NULL, NULL, NULL, 'Lạng Sơn');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1613, 'Vân Nham', 19, NULL, NULL, NULL, 'Lạng Sơn');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1614, 'Vũ Lăng', 19, NULL, NULL, NULL, 'Lạng Sơn');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1615, 'Vũ Lễ', 19, NULL, NULL, NULL, 'Lạng Sơn');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1616, 'Xuân Dương', 19, NULL, NULL, NULL, 'Lạng Sơn');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1617, 'Yên Bình', 19, NULL, NULL, NULL, 'Lạng Sơn');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1618, 'Yên Phúc', 19, NULL, NULL, NULL, 'Lạng Sơn');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1619, 'A Mú Sung', 20, NULL, NULL, NULL, 'Lào Cai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1620, 'Âu Lâu', 20, NULL, NULL, NULL, 'Lào Cai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1621, 'Bản Hồ', 20, NULL, NULL, NULL, 'Lào Cai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1622, 'Bản Lầu', 20, NULL, NULL, NULL, 'Lào Cai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1623, 'Bản Liền', 20, NULL, NULL, NULL, 'Lào Cai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1624, 'Bản Xèo', 20, NULL, NULL, NULL, 'Lào Cai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1625, 'Bảo Ái', 20, NULL, NULL, NULL, 'Lào Cai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1626, 'Bảo Hà', 20, NULL, NULL, NULL, 'Lào Cai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1627, 'Bảo Nhai', 20, NULL, NULL, NULL, 'Lào Cai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1628, 'Bảo Thắng', 20, NULL, NULL, NULL, 'Lào Cai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1629, 'Bảo Yên', 20, NULL, NULL, NULL, 'Lào Cai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1630, 'Bát Xát', 20, NULL, NULL, NULL, 'Lào Cai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1631, 'Bắc Hà', 20, NULL, NULL, NULL, 'Lào Cai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1632, 'Cam Đường', 20, NULL, NULL, NULL, 'Lào Cai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1633, 'Cảm Nhân', 20, NULL, NULL, NULL, 'Lào Cai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1634, 'Cao Sơn', 20, NULL, NULL, NULL, 'Lào Cai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1635, 'Cát Thịnh', 20, NULL, NULL, NULL, 'Lào Cai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1636, 'Cầu Thia', 20, NULL, NULL, NULL, 'Lào Cai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1637, 'Chấn Thịnh', 20, NULL, NULL, NULL, 'Lào Cai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1638, 'Châu Quế', 20, NULL, NULL, NULL, 'Lào Cai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1639, 'Chế Tạo', 20, NULL, NULL, NULL, 'Lào Cai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1640, 'Chiềng Ken', 20, NULL, NULL, NULL, 'Lào Cai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1641, 'Cốc Lầu', 20, NULL, NULL, NULL, 'Lào Cai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1642, 'Cốc San', 20, NULL, NULL, NULL, 'Lào Cai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1643, 'Dền Sáng', 20, NULL, NULL, NULL, 'Lào Cai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1644, 'Dương Quỳ', 20, NULL, NULL, NULL, 'Lào Cai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1645, 'Đông Cuông', 20, NULL, NULL, NULL, 'Lào Cai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1646, 'Gia Hội', 20, NULL, NULL, NULL, 'Lào Cai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1647, 'Gia Phú', 20, NULL, NULL, NULL, 'Lào Cai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1648, 'Hạnh Phúc', 20, NULL, NULL, NULL, 'Lào Cai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1649, 'Hợp Thành', 20, NULL, NULL, NULL, 'Lào Cai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1650, 'Hưng Khánh', 20, NULL, NULL, NULL, 'Lào Cai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1651, 'Khánh Hòa', 20, NULL, NULL, NULL, 'Lào Cai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1652, 'Khánh Yên', 20, NULL, NULL, NULL, 'Lào Cai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1653, 'Khao Mang', 20, NULL, NULL, NULL, 'Lào Cai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1654, 'Lào Cai', 20, NULL, NULL, NULL, 'Lào Cai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1655, 'Lao Chải', 20, NULL, NULL, NULL, 'Lào Cai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1656, 'Lâm Giang', 20, NULL, NULL, NULL, 'Lào Cai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1657, 'Lâm Thượng', 20, NULL, NULL, NULL, 'Lào Cai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1658, 'Liên Sơn', 20, NULL, NULL, NULL, 'Lào Cai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1659, 'Lục Yên', 20, NULL, NULL, NULL, 'Lào Cai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1660, 'Lùng Phình', 20, NULL, NULL, NULL, 'Lào Cai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1661, 'Lương Thịnh', 20, NULL, NULL, NULL, 'Lào Cai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1662, 'Mậu A', 20, NULL, NULL, NULL, 'Lào Cai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1663, 'Minh Lương', 20, NULL, NULL, NULL, 'Lào Cai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1664, 'Mỏ Vàng', 20, NULL, NULL, NULL, 'Lào Cai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1665, 'Mù Cang Chải', 20, NULL, NULL, NULL, 'Lào Cai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1666, 'Mường Bo', 20, NULL, NULL, NULL, 'Lào Cai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1667, 'Mường Hum', 20, NULL, NULL, NULL, 'Lào Cai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1668, 'Mường Khương', 20, NULL, NULL, NULL, 'Lào Cai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1669, 'Mường Lai', 20, NULL, NULL, NULL, 'Lào Cai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1670, 'Nam Cường', 20, NULL, NULL, NULL, 'Lào Cai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1671, 'Nậm Chày', 20, NULL, NULL, NULL, 'Lào Cai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1672, 'Nậm Có', 20, NULL, NULL, NULL, 'Lào Cai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1673, 'Nậm Xé', 20, NULL, NULL, NULL, 'Lào Cai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1674, 'Nghĩa Đô', 20, NULL, NULL, NULL, 'Lào Cai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1675, 'Nghĩa Lộ', 20, NULL, NULL, NULL, 'Lào Cai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1676, 'Nghĩa Tâm', 20, NULL, NULL, NULL, 'Lào Cai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1677, 'Ngũ Chỉ Sơn', 20, NULL, NULL, NULL, 'Lào Cai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1678, 'Pha Long', 20, NULL, NULL, NULL, 'Lào Cai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1679, 'Phình Hồ', 20, NULL, NULL, NULL, 'Lào Cai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1680, 'Phong Dụ Hạ', 20, NULL, NULL, NULL, 'Lào Cai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1681, 'Phong Dụ Thượng', 20, NULL, NULL, NULL, 'Lào Cai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1682, 'Phong Hải', 20, NULL, NULL, NULL, 'Lào Cai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1683, 'Phúc Khánh', 20, NULL, NULL, NULL, 'Lào Cai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1684, 'Phúc Lợi', 20, NULL, NULL, NULL, 'Lào Cai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1685, 'Púng Luông', 20, NULL, NULL, NULL, 'Lào Cai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1686, 'Quy Mông', 20, NULL, NULL, NULL, 'Lào Cai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1687, 'Sa Pa', 20, NULL, NULL, NULL, 'Lào Cai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1688, 'Si Ma Cai', 20, NULL, NULL, NULL, 'Lào Cai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1689, 'Sín Chéng', 20, NULL, NULL, NULL, 'Lào Cai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1690, 'Sơn Lương', 20, NULL, NULL, NULL, 'Lào Cai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1691, 'Tả Củ Tỷ', 20, NULL, NULL, NULL, 'Lào Cai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1692, 'Tả Phìn', 20, NULL, NULL, NULL, 'Lào Cai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1693, 'Tả Van', 20, NULL, NULL, NULL, 'Lào Cai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1694, 'Tà Xi Láng', 20, NULL, NULL, NULL, 'Lào Cai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1695, 'Tằng Loỏng', 20, NULL, NULL, NULL, 'Lào Cai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1696, 'Tân Hợp', 20, NULL, NULL, NULL, 'Lào Cai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1697, 'Tân Lĩnh', 20, NULL, NULL, NULL, 'Lào Cai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1698, 'Thác Bà', 20, NULL, NULL, NULL, 'Lào Cai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1699, 'Thượng Bằng La', 20, NULL, NULL, NULL, 'Lào Cai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1700, 'Thượng Hà', 20, NULL, NULL, NULL, 'Lào Cai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1701, 'Trạm Tấu', 20, NULL, NULL, NULL, 'Lào Cai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1702, 'Trấn Yên', 20, NULL, NULL, NULL, 'Lào Cai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1703, 'Trịnh Tường', 20, NULL, NULL, NULL, 'Lào Cai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1704, 'Trung Tâm', 20, NULL, NULL, NULL, 'Lào Cai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1705, 'Tú Lệ', 20, NULL, NULL, NULL, 'Lào Cai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1706, 'Văn Bàn', 20, NULL, NULL, NULL, 'Lào Cai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1707, 'Văn Chấn', 20, NULL, NULL, NULL, 'Lào Cai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1708, 'Văn Phú', 20, NULL, NULL, NULL, 'Lào Cai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1709, 'Việt Hồng', 20, NULL, NULL, NULL, 'Lào Cai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1710, 'Võ Lao', 20, NULL, NULL, NULL, 'Lào Cai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1711, 'Xuân Ái', 20, NULL, NULL, NULL, 'Lào Cai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1712, 'Xuân Hòa', 20, NULL, NULL, NULL, 'Lào Cai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1713, 'Xuân Quang', 20, NULL, NULL, NULL, 'Lào Cai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1714, 'Y Tý', 20, NULL, NULL, NULL, 'Lào Cai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1715, 'Yên Bái', 20, NULL, NULL, NULL, 'Lào Cai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1716, 'Yên Bình', 20, NULL, NULL, NULL, 'Lào Cai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1717, 'Yên Thành', 20, NULL, NULL, NULL, 'Lào Cai');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1718, '1 Bảo Lộc', 21, NULL, NULL, NULL, 'Lâm Đồng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1719, '2 Bảo Lộc', 21, NULL, NULL, NULL, 'Lâm Đồng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1720, '3 Bảo Lộc', 21, NULL, NULL, NULL, 'Lâm Đồng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1721, 'B’Lao', 21, NULL, NULL, NULL, 'Lâm Đồng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1722, 'Bảo Lâm 1', 21, NULL, NULL, NULL, 'Lâm Đồng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1723, 'Bảo Lâm 2', 21, NULL, NULL, NULL, 'Lâm Đồng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1724, 'Bảo Lâm 3', 21, NULL, NULL, NULL, 'Lâm Đồng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1725, 'Bảo Lâm 4', 21, NULL, NULL, NULL, 'Lâm Đồng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1726, 'Bảo Lâm 5', 21, NULL, NULL, NULL, 'Lâm Đồng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1727, 'Bảo Thuận', 21, NULL, NULL, NULL, 'Lâm Đồng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1728, 'Bắc Bình', 21, NULL, NULL, NULL, 'Lâm Đồng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1729, 'Bắc Gia Nghĩa', 21, NULL, NULL, NULL, 'Lâm Đồng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1730, 'Bắc Ruộng', 21, NULL, NULL, NULL, 'Lâm Đồng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1731, 'Bình Thuận', 21, NULL, NULL, NULL, 'Lâm Đồng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1732, 'Cam Ly - Đà Lạt', 21, NULL, NULL, NULL, 'Lâm Đồng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1733, 'Cát Tiên', 21, NULL, NULL, NULL, 'Lâm Đồng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1734, 'Cát Tiên 2', 21, NULL, NULL, NULL, 'Lâm Đồng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1735, 'Cát Tiên 3', 21, NULL, NULL, NULL, 'Lâm Đồng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1736, 'Cư Jút', 21, NULL, NULL, NULL, 'Lâm Đồng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1737, 'D’Ran', 21, NULL, NULL, NULL, 'Lâm Đồng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1738, 'Di Linh', 21, NULL, NULL, NULL, 'Lâm Đồng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1739, 'Đạ Huoai', 21, NULL, NULL, NULL, 'Lâm Đồng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1740, 'Đạ Huoai 2', 21, NULL, NULL, NULL, 'Lâm Đồng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1741, 'Đạ Huoai 3', 21, NULL, NULL, NULL, 'Lâm Đồng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1742, 'Đạ Tẻh', 21, NULL, NULL, NULL, 'Lâm Đồng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1743, 'Đạ Tẻh 2', 21, NULL, NULL, NULL, 'Lâm Đồng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1744, 'Đạ Tẻh 3', 21, NULL, NULL, NULL, 'Lâm Đồng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1745, 'Đam Rông 1', 21, NULL, NULL, NULL, 'Lâm Đồng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1746, 'Đam Rông 2', 21, NULL, NULL, NULL, 'Lâm Đồng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1747, 'Đam Rông 3', 21, NULL, NULL, NULL, 'Lâm Đồng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1748, 'Đam Rông 4', 21, NULL, NULL, NULL, 'Lâm Đồng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1749, 'Đắk Mil', 21, NULL, NULL, NULL, 'Lâm Đồng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1750, 'Đắk Sắk', 21, NULL, NULL, NULL, 'Lâm Đồng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1751, 'Đắk Song', 21, NULL, NULL, NULL, 'Lâm Đồng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1752, 'Đắk Wil', 21, NULL, NULL, NULL, 'Lâm Đồng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1753, 'Đinh Trang Thượng', 21, NULL, NULL, NULL, 'Lâm Đồng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1754, 'Đinh Văn Lâm Hà', 21, NULL, NULL, NULL, 'Lâm Đồng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1755, 'Đông Gia Nghĩa', 21, NULL, NULL, NULL, 'Lâm Đồng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1756, 'Đông Giang', 21, NULL, NULL, NULL, 'Lâm Đồng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1757, 'Đồng Kho', 21, NULL, NULL, NULL, 'Lâm Đồng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1758, 'Đơn Dương', 21, NULL, NULL, NULL, 'Lâm Đồng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1759, 'Đức An', 21, NULL, NULL, NULL, 'Lâm Đồng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1760, 'Đức Lập', 21, NULL, NULL, NULL, 'Lâm Đồng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1761, 'Đức Linh', 21, NULL, NULL, NULL, 'Lâm Đồng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1762, 'Đức Trọng', 21, NULL, NULL, NULL, 'Lâm Đồng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1763, 'Gia Hiệp', 21, NULL, NULL, NULL, 'Lâm Đồng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1764, 'Hải Ninh', 21, NULL, NULL, NULL, 'Lâm Đồng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1765, 'Hàm Kiệm', 21, NULL, NULL, NULL, 'Lâm Đồng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1766, 'Hàm Liêm', 21, NULL, NULL, NULL, 'Lâm Đồng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1767, 'Hàm Tân', 21, NULL, NULL, NULL, 'Lâm Đồng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1768, 'Hàm Thạnh', 21, NULL, NULL, NULL, 'Lâm Đồng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1769, 'Hàm Thắng', 21, NULL, NULL, NULL, 'Lâm Đồng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1770, 'Hàm Thuận', 21, NULL, NULL, NULL, 'Lâm Đồng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1771, 'Hàm Thuận Bắc', 21, NULL, NULL, NULL, 'Lâm Đồng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1772, 'Hàm Thuận Nam', 21, NULL, NULL, NULL, 'Lâm Đồng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1773, 'Hiệp Thạnh', 21, NULL, NULL, NULL, 'Lâm Đồng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1774, 'Hòa Bắc', 21, NULL, NULL, NULL, 'Lâm Đồng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1775, 'Hòa Ninh', 21, NULL, NULL, NULL, 'Lâm Đồng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1776, 'Hòa Thắng', 21, NULL, NULL, NULL, 'Lâm Đồng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1777, 'Hoài Đức', 21, NULL, NULL, NULL, 'Lâm Đồng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1778, 'Hồng Sơn', 21, NULL, NULL, NULL, 'Lâm Đồng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1779, 'Hồng Thái', 21, NULL, NULL, NULL, 'Lâm Đồng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1780, 'Ka Đô', 21, NULL, NULL, NULL, 'Lâm Đồng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1781, 'Kiến Đức', 21, NULL, NULL, NULL, 'Lâm Đồng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1782, 'Krông Nô', 21, NULL, NULL, NULL, 'Lâm Đồng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1783, 'La Dạ', 21, NULL, NULL, NULL, 'Lâm Đồng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1784, 'La Gi', 21, NULL, NULL, NULL, 'Lâm Đồng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1785, 'Lạc Dương', 21, NULL, NULL, NULL, 'Lâm Đồng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1786, 'Lang Biang - Đà Lạt', 21, NULL, NULL, NULL, 'Lâm Đồng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1787, 'Lâm Viên - Đà Lạt', 21, NULL, NULL, NULL, 'Lâm Đồng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1788, 'Liên Hương', 21, NULL, NULL, NULL, 'Lâm Đồng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1789, 'Lương Sơn', 21, NULL, NULL, NULL, 'Lâm Đồng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1790, 'Mũi Né', 21, NULL, NULL, NULL, 'Lâm Đồng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1791, 'Nam Ban Lâm Hà', 21, NULL, NULL, NULL, 'Lâm Đồng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1792, 'Nam Dong', 21, NULL, NULL, NULL, 'Lâm Đồng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1793, 'Nam Đà', 21, NULL, NULL, NULL, 'Lâm Đồng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1794, 'Nam Gia Nghĩa', 21, NULL, NULL, NULL, 'Lâm Đồng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1795, 'Nam Hà Lâm Hà', 21, NULL, NULL, NULL, 'Lâm Đồng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1796, 'Nam Thành', 21, NULL, NULL, NULL, 'Lâm Đồng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1797, 'Nâm Nung', 21, NULL, NULL, NULL, 'Lâm Đồng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1798, 'Nghị Đức', 21, NULL, NULL, NULL, 'Lâm Đồng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1799, 'Nhân Cơ', 21, NULL, NULL, NULL, 'Lâm Đồng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1800, 'Ninh Gia', 21, NULL, NULL, NULL, 'Lâm Đồng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1801, 'Phan Rí Cửa', 21, NULL, NULL, NULL, 'Lâm Đồng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1802, 'Phan Sơn', 21, NULL, NULL, NULL, 'Lâm Đồng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1803, 'Phan Thiết', 21, NULL, NULL, NULL, 'Lâm Đồng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1804, 'Phú Quý', 21, NULL, NULL, NULL, 'Lâm Đồng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1805, 'Phú Sơn Lâm Hà', 21, NULL, NULL, NULL, 'Lâm Đồng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1806, 'Phú Thủy', 21, NULL, NULL, NULL, 'Lâm Đồng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1807, 'Phúc Thọ Lâm Hà', 21, NULL, NULL, NULL, 'Lâm Đồng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1808, 'Phước Hội', 21, NULL, NULL, NULL, 'Lâm Đồng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1809, 'Quảng Hòa', 21, NULL, NULL, NULL, 'Lâm Đồng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1810, 'Quảng Khê', 21, NULL, NULL, NULL, 'Lâm Đồng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1811, 'Quảng Lập', 21, NULL, NULL, NULL, 'Lâm Đồng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1812, 'Quảng Phú', 21, NULL, NULL, NULL, 'Lâm Đồng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1813, 'Quảng Sơn', 21, NULL, NULL, NULL, 'Lâm Đồng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1814, 'Quảng Tân', 21, NULL, NULL, NULL, 'Lâm Đồng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1815, 'Quảng Tín', 21, NULL, NULL, NULL, 'Lâm Đồng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1816, 'Quảng Trực', 21, NULL, NULL, NULL, 'Lâm Đồng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1817, 'Sông Lũy', 21, NULL, NULL, NULL, 'Lâm Đồng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1818, 'Sơn Điền', 21, NULL, NULL, NULL, 'Lâm Đồng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1819, 'Sơn Mỹ', 21, NULL, NULL, NULL, 'Lâm Đồng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1820, 'Suối Kiết', 21, NULL, NULL, NULL, 'Lâm Đồng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1821, 'Tà Đùng', 21, NULL, NULL, NULL, 'Lâm Đồng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1822, 'Tà Hine', 21, NULL, NULL, NULL, 'Lâm Đồng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1823, 'Tà Năng', 21, NULL, NULL, NULL, 'Lâm Đồng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1824, 'Tánh Linh', 21, NULL, NULL, NULL, 'Lâm Đồng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1825, 'Tân Hà Lâm Hà', 21, NULL, NULL, NULL, 'Lâm Đồng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1826, 'Tân Hải', 21, NULL, NULL, NULL, 'Lâm Đồng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1827, 'Tân Hội', 21, NULL, NULL, NULL, 'Lâm Đồng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1828, 'Tân Lập', 21, NULL, NULL, NULL, 'Lâm Đồng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1829, 'Tân Minh', 21, NULL, NULL, NULL, 'Lâm Đồng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1830, 'Tân Thành', 21, NULL, NULL, NULL, 'Lâm Đồng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1831, 'Thuận An', 21, NULL, NULL, NULL, 'Lâm Đồng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1832, 'Thuận Hạnh', 21, NULL, NULL, NULL, 'Lâm Đồng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1833, 'Tiến Thành', 21, NULL, NULL, NULL, 'Lâm Đồng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1834, 'Trà Tân', 21, NULL, NULL, NULL, 'Lâm Đồng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1835, 'Trường Xuân', 21, NULL, NULL, NULL, 'Lâm Đồng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1836, 'Tuy Đức', 21, NULL, NULL, NULL, 'Lâm Đồng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1837, 'Tuy Phong', 21, NULL, NULL, NULL, 'Lâm Đồng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1838, 'Tuyên Quang', 21, NULL, NULL, NULL, 'Lâm Đồng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1839, 'Vĩnh Hảo', 21, NULL, NULL, NULL, 'Lâm Đồng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1840, 'Xuân Hương - Đà Lạt', 21, NULL, NULL, NULL, 'Lâm Đồng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1841, 'Xuân Trường - Đà Lạt', 21, NULL, NULL, NULL, 'Lâm Đồng');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1842, 'An Châu', 22, NULL, NULL, NULL, 'Nghệ An');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1843, 'Anh Sơn', 22, NULL, NULL, NULL, 'Nghệ An');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1844, 'Anh Sơn Đông', 22, NULL, NULL, NULL, 'Nghệ An');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1845, 'Bạch Hà', 22, NULL, NULL, NULL, 'Nghệ An');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1846, 'Bạch Ngọc', 22, NULL, NULL, NULL, 'Nghệ An');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1847, 'Bắc Lý', 22, NULL, NULL, NULL, 'Nghệ An');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1848, 'Bích Hào', 22, NULL, NULL, NULL, 'Nghệ An');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1849, 'Bình Chuẩn', 22, NULL, NULL, NULL, 'Nghệ An');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1850, 'Bình Minh', 22, NULL, NULL, NULL, 'Nghệ An');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1851, 'Cam Phục', 22, NULL, NULL, NULL, 'Nghệ An');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1852, 'Cát Ngạn', 22, NULL, NULL, NULL, 'Nghệ An');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1853, 'Châu Bình', 22, NULL, NULL, NULL, 'Nghệ An');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1854, 'Châu Hồng', 22, NULL, NULL, NULL, 'Nghệ An');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1855, 'Châu Khê', 22, NULL, NULL, NULL, 'Nghệ An');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1856, 'Châu Lộc', 22, NULL, NULL, NULL, 'Nghệ An');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1857, 'Châu Tiến', 22, NULL, NULL, NULL, 'Nghệ An');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1858, 'Chiêu Lưu', 22, NULL, NULL, NULL, 'Nghệ An');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1859, 'Con Cuông', 22, NULL, NULL, NULL, 'Nghệ An');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1860, 'Cửa Lò', 22, NULL, NULL, NULL, 'Nghệ An');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1861, 'Diễn Châu', 22, NULL, NULL, NULL, 'Nghệ An');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1862, 'Đại Đồng', 22, NULL, NULL, NULL, 'Nghệ An');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1863, 'Đại Huệ', 22, NULL, NULL, NULL, 'Nghệ An');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1864, 'Đô Lương', 22, NULL, NULL, NULL, 'Nghệ An');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1865, 'Đông Hiếu', 22, NULL, NULL, NULL, 'Nghệ An');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1866, 'Đông Lộc', 22, NULL, NULL, NULL, 'Nghệ An');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1867, 'Đông Thành', 22, NULL, NULL, NULL, 'Nghệ An');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1868, 'Đức Châu', 22, NULL, NULL, NULL, 'Nghệ An');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1869, 'Giai Lạc', 22, NULL, NULL, NULL, 'Nghệ An');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1870, 'Giai Xuân', 22, NULL, NULL, NULL, 'Nghệ An');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1871, 'Hải Châu', 22, NULL, NULL, NULL, 'Nghệ An');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1872, 'Hải Lộc', 22, NULL, NULL, NULL, 'Nghệ An');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1873, 'Hạnh Lâm', 22, NULL, NULL, NULL, 'Nghệ An');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1874, 'Hoa Quân', 22, NULL, NULL, NULL, 'Nghệ An');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1875, 'Hoàng Mai', 22, NULL, NULL, NULL, 'Nghệ An');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1876, 'Hợp Minh', 22, NULL, NULL, NULL, 'Nghệ An');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1877, 'Hùng Chân', 22, NULL, NULL, NULL, 'Nghệ An');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1878, 'Hùng Châu', 22, NULL, NULL, NULL, 'Nghệ An');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1879, 'Huồi Tụ', 22, NULL, NULL, NULL, 'Nghệ An');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1880, 'Hưng Nguyên', 22, NULL, NULL, NULL, 'Nghệ An');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1881, 'Hưng Nguyên Nam', 22, NULL, NULL, NULL, 'Nghệ An');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1882, 'Hữu Khuông', 22, NULL, NULL, NULL, 'Nghệ An');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1883, 'Hữu Kiệm', 22, NULL, NULL, NULL, 'Nghệ An');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1884, 'Keng Đu', 22, NULL, NULL, NULL, 'Nghệ An');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1885, 'Kim Bảng', 22, NULL, NULL, NULL, 'Nghệ An');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1886, 'Kim Liên', 22, NULL, NULL, NULL, 'Nghệ An');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1887, 'Lam Thành', 22, NULL, NULL, NULL, 'Nghệ An');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1888, 'Lượng Minh', 22, NULL, NULL, NULL, 'Nghệ An');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1889, 'Lương Sơn', 22, NULL, NULL, NULL, 'Nghệ An');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1890, 'Mậu Thạch', 22, NULL, NULL, NULL, 'Nghệ An');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1891, 'Minh Châu', 22, NULL, NULL, NULL, 'Nghệ An');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1892, 'Minh Hợp', 22, NULL, NULL, NULL, 'Nghệ An');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1893, 'Môn Sơn', 22, NULL, NULL, NULL, 'Nghệ An');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1894, 'Mường Chọng', 22, NULL, NULL, NULL, 'Nghệ An');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1895, 'Mường Ham', 22, NULL, NULL, NULL, 'Nghệ An');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1896, 'Mường Lống', 22, NULL, NULL, NULL, 'Nghệ An');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1897, 'Mường Quàng', 22, NULL, NULL, NULL, 'Nghệ An');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1898, 'Mường Típ', 22, NULL, NULL, NULL, 'Nghệ An');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1899, 'Mường Xén', 22, NULL, NULL, NULL, 'Nghệ An');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1900, 'Mỹ Lý', 22, NULL, NULL, NULL, 'Nghệ An');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1901, 'Na Loi', 22, NULL, NULL, NULL, 'Nghệ An');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1902, 'Na Ngoi', 22, NULL, NULL, NULL, 'Nghệ An');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1903, 'Nam Đàn', 22, NULL, NULL, NULL, 'Nghệ An');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1904, 'Nậm Cắn', 22, NULL, NULL, NULL, 'Nghệ An');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1905, 'Nga My', 22, NULL, NULL, NULL, 'Nghệ An');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1906, 'Nghi Lộc', 22, NULL, NULL, NULL, 'Nghệ An');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1907, 'Nghĩa Đàn', 22, NULL, NULL, NULL, 'Nghệ An');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1908, 'Nghĩa Đồng', 22, NULL, NULL, NULL, 'Nghệ An');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1909, 'Nghĩa Hành', 22, NULL, NULL, NULL, 'Nghệ An');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1910, 'Nghĩa Hưng', 22, NULL, NULL, NULL, 'Nghệ An');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1911, 'Nghĩa Khánh', 22, NULL, NULL, NULL, 'Nghệ An');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1912, 'Nghĩa Lâm', 22, NULL, NULL, NULL, 'Nghệ An');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1913, 'Nghĩa Lộc', 22, NULL, NULL, NULL, 'Nghệ An');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1914, 'Nghĩa Mai', 22, NULL, NULL, NULL, 'Nghệ An');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1915, 'Nghĩa Thọ', 22, NULL, NULL, NULL, 'Nghệ An');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1916, 'Nhân Hòa', 22, NULL, NULL, NULL, 'Nghệ An');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1917, 'Nhôn Mai', 22, NULL, NULL, NULL, 'Nghệ An');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1918, 'Phúc Lộc', 22, NULL, NULL, NULL, 'Nghệ An');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1919, 'Quan Thành', 22, NULL, NULL, NULL, 'Nghệ An');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1920, 'Quảng Châu', 22, NULL, NULL, NULL, 'Nghệ An');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1921, 'Quang Đồng', 22, NULL, NULL, NULL, 'Nghệ An');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1922, 'Quế Phong', 22, NULL, NULL, NULL, 'Nghệ An');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1923, 'Quỳ Châu', 22, NULL, NULL, NULL, 'Nghệ An');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1924, 'Quỳ Hợp', 22, NULL, NULL, NULL, 'Nghệ An');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1925, 'Quỳnh Anh', 22, NULL, NULL, NULL, 'Nghệ An');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1926, 'Quỳnh Lưu', 22, NULL, NULL, NULL, 'Nghệ An');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1927, 'Quỳnh Mai', 22, NULL, NULL, NULL, 'Nghệ An');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1928, 'Quỳnh Phú', 22, NULL, NULL, NULL, 'Nghệ An');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1929, 'Quỳnh Sơn', 22, NULL, NULL, NULL, 'Nghệ An');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1930, 'Quỳnh Tam', 22, NULL, NULL, NULL, 'Nghệ An');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1931, 'Quỳnh Thắng', 22, NULL, NULL, NULL, 'Nghệ An');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1932, 'Quỳnh Văn', 22, NULL, NULL, NULL, 'Nghệ An');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1933, 'Sơn Lâm', 22, NULL, NULL, NULL, 'Nghệ An');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1934, 'Tam Đồng', 22, NULL, NULL, NULL, 'Nghệ An');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1935, 'Tam Hợp', 22, NULL, NULL, NULL, 'Nghệ An');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1936, 'Tam Quang', 22, NULL, NULL, NULL, 'Nghệ An');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1937, 'Tam Thái', 22, NULL, NULL, NULL, 'Nghệ An');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1938, 'Tân An', 22, NULL, NULL, NULL, 'Nghệ An');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1939, 'Tân Châu', 22, NULL, NULL, NULL, 'Nghệ An');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1940, 'Tân Kỳ', 22, NULL, NULL, NULL, 'Nghệ An');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1941, 'Tân Mai', 22, NULL, NULL, NULL, 'Nghệ An');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1942, 'Tân Phú', 22, NULL, NULL, NULL, 'Nghệ An');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1943, 'Tây Hiếu', 22, NULL, NULL, NULL, 'Nghệ An');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1944, 'Thái Hòa', 22, NULL, NULL, NULL, 'Nghệ An');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1945, 'Thành Bình Thọ', 22, NULL, NULL, NULL, 'Nghệ An');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1946, 'Thành Vinh', 22, NULL, NULL, NULL, 'Nghệ An');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1947, 'Thần Lĩnh', 22, NULL, NULL, NULL, 'Nghệ An');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1948, 'Thiên Nhẫn', 22, NULL, NULL, NULL, 'Nghệ An');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1949, 'Thông Thụ', 22, NULL, NULL, NULL, 'Nghệ An');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1950, 'Thuần Trung', 22, NULL, NULL, NULL, 'Nghệ An');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1951, 'Tiên Đồng', 22, NULL, NULL, NULL, 'Nghệ An');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1952, 'Tiền Phong', 22, NULL, NULL, NULL, 'Nghệ An');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1953, 'Tri Lễ', 22, NULL, NULL, NULL, 'Nghệ An');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1954, 'Trung Lộc', 22, NULL, NULL, NULL, 'Nghệ An');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1955, 'Trường Vinh', 22, NULL, NULL, NULL, 'Nghệ An');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1956, 'Tương Dương', 22, NULL, NULL, NULL, 'Nghệ An');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1957, 'Vạn An', 22, NULL, NULL, NULL, 'Nghệ An');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1958, 'Văn Hiến', 22, NULL, NULL, NULL, 'Nghệ An');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1959, 'Văn Kiều', 22, NULL, NULL, NULL, 'Nghệ An');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1960, 'Vân Du', 22, NULL, NULL, NULL, 'Nghệ An');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1961, 'Vân Tụ', 22, NULL, NULL, NULL, 'Nghệ An');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1962, 'Vinh Hưng', 22, NULL, NULL, NULL, 'Nghệ An');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1963, 'Vinh Lộc', 22, NULL, NULL, NULL, 'Nghệ An');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1964, 'Vinh Phú', 22, NULL, NULL, NULL, 'Nghệ An');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1965, 'Vĩnh Tường', 22, NULL, NULL, NULL, 'Nghệ An');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1966, 'Xuân Lâm', 22, NULL, NULL, NULL, 'Nghệ An');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1967, 'Yên Hòa', 22, NULL, NULL, NULL, 'Nghệ An');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1968, 'Yên Na', 22, NULL, NULL, NULL, 'Nghệ An');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1969, 'Yên Thành', 22, NULL, NULL, NULL, 'Nghệ An');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1970, 'Yên Trung', 22, NULL, NULL, NULL, 'Nghệ An');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1971, 'Yên Xuân', 22, NULL, NULL, NULL, 'Nghệ An');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1972, 'Bắc Lý', 23, NULL, NULL, NULL, 'Ninh Bình');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1973, 'Bình An', 23, NULL, NULL, NULL, 'Ninh Bình');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1974, 'Bình Giang', 23, NULL, NULL, NULL, 'Ninh Bình');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1975, 'Bình Lục', 23, NULL, NULL, NULL, 'Ninh Bình');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1976, 'Bình Minh', 23, NULL, NULL, NULL, 'Ninh Bình');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1977, 'Bình Mỹ', 23, NULL, NULL, NULL, 'Ninh Bình');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1978, 'Bình Sơn', 23, NULL, NULL, NULL, 'Ninh Bình');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1979, 'Cát Thành', 23, NULL, NULL, NULL, 'Ninh Bình');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1980, 'Chất Bình', 23, NULL, NULL, NULL, 'Ninh Bình');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1981, 'Châu Sơn', 23, NULL, NULL, NULL, 'Ninh Bình');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1982, 'Cổ Lễ', 23, NULL, NULL, NULL, 'Ninh Bình');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1983, 'Cúc Phương', 23, NULL, NULL, NULL, 'Ninh Bình');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1984, 'Duy Hà', 23, NULL, NULL, NULL, 'Ninh Bình');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1985, 'Duy Tân', 23, NULL, NULL, NULL, 'Ninh Bình');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1986, 'Duy Tiên', 23, NULL, NULL, NULL, 'Ninh Bình');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1987, 'Đại Hoàng', 23, NULL, NULL, NULL, 'Ninh Bình');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1988, 'Định Hóa', 23, NULL, NULL, NULL, 'Ninh Bình');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1989, 'Đông A', 23, NULL, NULL, NULL, 'Ninh Bình');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1990, 'Đông Hoa Lư', 23, NULL, NULL, NULL, 'Ninh Bình');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1991, 'Đồng Thái', 23, NULL, NULL, NULL, 'Ninh Bình');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1992, 'Đồng Thịnh', 23, NULL, NULL, NULL, 'Ninh Bình');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1993, 'Đồng Văn', 23, NULL, NULL, NULL, 'Ninh Bình');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1994, 'Gia Hưng', 23, NULL, NULL, NULL, 'Ninh Bình');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1995, 'Gia Lâm', 23, NULL, NULL, NULL, 'Ninh Bình');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1996, 'Gia Phong', 23, NULL, NULL, NULL, 'Ninh Bình');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1997, 'Gia Trấn', 23, NULL, NULL, NULL, 'Ninh Bình');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1998, 'Gia Tường', 23, NULL, NULL, NULL, 'Ninh Bình');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (1999, 'Gia Vân', 23, NULL, NULL, NULL, 'Ninh Bình');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2000, 'Gia Viễn', 23, NULL, NULL, NULL, 'Ninh Bình');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2001, 'Giao Bình', 23, NULL, NULL, NULL, 'Ninh Bình');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2002, 'Giao Hòa', 23, NULL, NULL, NULL, 'Ninh Bình');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2003, 'Giao Hưng', 23, NULL, NULL, NULL, 'Ninh Bình');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2004, 'Giao Minh', 23, NULL, NULL, NULL, 'Ninh Bình');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2005, 'Giao Ninh', 23, NULL, NULL, NULL, 'Ninh Bình');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2006, 'Giao Phúc', 23, NULL, NULL, NULL, 'Ninh Bình');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2007, 'Giao Thủy', 23, NULL, NULL, NULL, 'Ninh Bình');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2008, 'Hà Nam', 23, NULL, NULL, NULL, 'Ninh Bình');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2009, 'Hải An', 23, NULL, NULL, NULL, 'Ninh Bình');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2010, 'Hải Anh', 23, NULL, NULL, NULL, 'Ninh Bình');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2011, 'Hải Hậu', 23, NULL, NULL, NULL, 'Ninh Bình');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2012, 'Hải Hưng', 23, NULL, NULL, NULL, 'Ninh Bình');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2013, 'Hải Quang', 23, NULL, NULL, NULL, 'Ninh Bình');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2014, 'Hải Thịnh', 23, NULL, NULL, NULL, 'Ninh Bình');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2015, 'Hải Tiến', 23, NULL, NULL, NULL, 'Ninh Bình');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2016, 'Hải Xuân', 23, NULL, NULL, NULL, 'Ninh Bình');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2017, 'Hiển Khánh', 23, NULL, NULL, NULL, 'Ninh Bình');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2018, 'Hoa Lư', 23, NULL, NULL, NULL, 'Ninh Bình');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2019, 'Hồng Phong', 23, NULL, NULL, NULL, 'Ninh Bình');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2020, 'Hồng Quang', 23, NULL, NULL, NULL, 'Ninh Bình');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2021, 'Khánh Hội', 23, NULL, NULL, NULL, 'Ninh Bình');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2022, 'Khánh Nhạc', 23, NULL, NULL, NULL, 'Ninh Bình');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2023, 'Khánh Thiện', 23, NULL, NULL, NULL, 'Ninh Bình');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2024, 'Khánh Trung', 23, NULL, NULL, NULL, 'Ninh Bình');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2025, 'Kim Bảng', 23, NULL, NULL, NULL, 'Ninh Bình');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2026, 'Kim Đông', 23, NULL, NULL, NULL, 'Ninh Bình');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2027, 'Kim Sơn', 23, NULL, NULL, NULL, 'Ninh Bình');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2028, 'Kim Thanh', 23, NULL, NULL, NULL, 'Ninh Bình');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2029, 'Lai Thành', 23, NULL, NULL, NULL, 'Ninh Bình');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2030, 'Lê Hồ', 23, NULL, NULL, NULL, 'Ninh Bình');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2031, 'Liêm Hà', 23, NULL, NULL, NULL, 'Ninh Bình');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2032, 'Liêm Tuyền', 23, NULL, NULL, NULL, 'Ninh Bình');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2033, 'Liên Minh', 23, NULL, NULL, NULL, 'Ninh Bình');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2034, 'Lý Nhân', 23, NULL, NULL, NULL, 'Ninh Bình');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2035, 'Lý Thường Kiệt', 23, NULL, NULL, NULL, 'Ninh Bình');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2036, 'Minh Tân', 23, NULL, NULL, NULL, 'Ninh Bình');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2037, 'Minh Thái', 23, NULL, NULL, NULL, 'Ninh Bình');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2038, 'Mỹ Lộc', 23, NULL, NULL, NULL, 'Ninh Bình');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2039, 'Nam Định', 23, NULL, NULL, NULL, 'Ninh Bình');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2040, 'Nam Đồng', 23, NULL, NULL, NULL, 'Ninh Bình');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2041, 'Nam Hoa Lư', 23, NULL, NULL, NULL, 'Ninh Bình');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2042, 'Nam Hồng', 23, NULL, NULL, NULL, 'Ninh Bình');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2043, 'Nam Lý', 23, NULL, NULL, NULL, 'Ninh Bình');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2044, 'Nam Minh', 23, NULL, NULL, NULL, 'Ninh Bình');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2045, 'Nam Ninh', 23, NULL, NULL, NULL, 'Ninh Bình');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2046, 'Nam Trực', 23, NULL, NULL, NULL, 'Ninh Bình');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2047, 'Nam Xang', 23, NULL, NULL, NULL, 'Ninh Bình');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2048, 'Nghĩa Hưng', 23, NULL, NULL, NULL, 'Ninh Bình');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2049, 'Nghĩa Lâm', 23, NULL, NULL, NULL, 'Ninh Bình');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2050, 'Nghĩa Sơn', 23, NULL, NULL, NULL, 'Ninh Bình');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2051, 'Nguyễn Úy', 23, NULL, NULL, NULL, 'Ninh Bình');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2052, 'Nhân Hà', 23, NULL, NULL, NULL, 'Ninh Bình');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2053, 'Nho Quan', 23, NULL, NULL, NULL, 'Ninh Bình');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2054, 'Ninh Cường', 23, NULL, NULL, NULL, 'Ninh Bình');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2055, 'Ninh Giang', 23, NULL, NULL, NULL, 'Ninh Bình');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2056, 'Phát Diệm', 23, NULL, NULL, NULL, 'Ninh Bình');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2057, 'Phong Doanh', 23, NULL, NULL, NULL, 'Ninh Bình');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2058, 'Phú Long', 23, NULL, NULL, NULL, 'Ninh Bình');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2059, 'Phủ Lý', 23, NULL, NULL, NULL, 'Ninh Bình');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2060, 'Phú Sơn', 23, NULL, NULL, NULL, 'Ninh Bình');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2061, 'Phù Vân', 23, NULL, NULL, NULL, 'Ninh Bình');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2062, 'Quang Hưng', 23, NULL, NULL, NULL, 'Ninh Bình');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2063, 'Quang Thiện', 23, NULL, NULL, NULL, 'Ninh Bình');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2064, 'Quỹ Nhất', 23, NULL, NULL, NULL, 'Ninh Bình');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2065, 'Quỳnh Lưu', 23, NULL, NULL, NULL, 'Ninh Bình');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2066, 'Rạng Đông', 23, NULL, NULL, NULL, 'Ninh Bình');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2067, 'Tam Chúc', 23, NULL, NULL, NULL, 'Ninh Bình');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2068, 'Tam Điệp', 23, NULL, NULL, NULL, 'Ninh Bình');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2069, 'Tân Minh', 23, NULL, NULL, NULL, 'Ninh Bình');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2070, 'Tân Thanh', 23, NULL, NULL, NULL, 'Ninh Bình');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2071, 'Tây Hoa Lư', 23, NULL, NULL, NULL, 'Ninh Bình');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2072, 'Thanh Bình', 23, NULL, NULL, NULL, 'Ninh Bình');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2073, 'Thanh Lâm', 23, NULL, NULL, NULL, 'Ninh Bình');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2074, 'Thanh Liêm', 23, NULL, NULL, NULL, 'Ninh Bình');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2075, 'Thành Nam', 23, NULL, NULL, NULL, 'Ninh Bình');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2076, 'Thanh Sơn', 23, NULL, NULL, NULL, 'Ninh Bình');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2077, 'Thiên Trường', 23, NULL, NULL, NULL, 'Ninh Bình');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2078, 'Tiên Sơn', 23, NULL, NULL, NULL, 'Ninh Bình');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2079, 'Trần Thương', 23, NULL, NULL, NULL, 'Ninh Bình');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2080, 'Trung Sơn', 23, NULL, NULL, NULL, 'Ninh Bình');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2081, 'Trực Ninh', 23, NULL, NULL, NULL, 'Ninh Bình');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2082, 'Trường Thi', 23, NULL, NULL, NULL, 'Ninh Bình');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2083, 'Vạn Thắng', 23, NULL, NULL, NULL, 'Ninh Bình');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2084, 'Vị Khê', 23, NULL, NULL, NULL, 'Ninh Bình');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2085, 'Vĩnh Trụ', 23, NULL, NULL, NULL, 'Ninh Bình');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2086, 'Vụ Bản', 23, NULL, NULL, NULL, 'Ninh Bình');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2087, 'Vũ Dương', 23, NULL, NULL, NULL, 'Ninh Bình');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2088, 'Xuân Giang', 23, NULL, NULL, NULL, 'Ninh Bình');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2089, 'Xuân Hồng', 23, NULL, NULL, NULL, 'Ninh Bình');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2090, 'Xuân Hưng', 23, NULL, NULL, NULL, 'Ninh Bình');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2091, 'Xuân Trường', 23, NULL, NULL, NULL, 'Ninh Bình');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2092, 'Ý Yên', 23, NULL, NULL, NULL, 'Ninh Bình');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2093, 'Yên Cường', 23, NULL, NULL, NULL, 'Ninh Bình');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2094, 'Yên Đồng', 23, NULL, NULL, NULL, 'Ninh Bình');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2095, 'Yên Khánh', 23, NULL, NULL, NULL, 'Ninh Bình');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2096, 'Yên Mạc', 23, NULL, NULL, NULL, 'Ninh Bình');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2097, 'Yên Mô', 23, NULL, NULL, NULL, 'Ninh Bình');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2098, 'Yên Sơn', 23, NULL, NULL, NULL, 'Ninh Bình');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2099, 'Yên Thắng', 23, NULL, NULL, NULL, 'Ninh Bình');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2100, 'Yên Từ', 23, NULL, NULL, NULL, 'Ninh Bình');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2101, 'An Bình', 24, NULL, NULL, NULL, 'Phú Thọ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2102, 'An Nghĩa', 24, NULL, NULL, NULL, 'Phú Thọ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2103, 'Âu Cơ', 24, NULL, NULL, NULL, 'Phú Thọ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2104, 'Bản Nguyên', 24, NULL, NULL, NULL, 'Phú Thọ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2105, 'Bao La', 24, NULL, NULL, NULL, 'Phú Thọ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2106, 'Bằng Luân', 24, NULL, NULL, NULL, 'Phú Thọ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2107, 'Bình Nguyên', 24, NULL, NULL, NULL, 'Phú Thọ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2108, 'Bình Phú', 24, NULL, NULL, NULL, 'Phú Thọ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2109, 'Bình Tuyền', 24, NULL, NULL, NULL, 'Phú Thọ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2110, 'Bình Xuyên', 24, NULL, NULL, NULL, 'Phú Thọ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2111, 'Cao Dương', 24, NULL, NULL, NULL, 'Phú Thọ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2112, 'Cao Phong', 24, NULL, NULL, NULL, 'Phú Thọ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2113, 'Cao Sơn', 24, NULL, NULL, NULL, 'Phú Thọ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2114, 'Cẩm Khê', 24, NULL, NULL, NULL, 'Phú Thọ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2115, 'Chân Mộng', 24, NULL, NULL, NULL, 'Phú Thọ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2116, 'Chí Đám', 24, NULL, NULL, NULL, 'Phú Thọ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2117, 'Chí Tiên', 24, NULL, NULL, NULL, 'Phú Thọ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2118, 'Cự Đồng', 24, NULL, NULL, NULL, 'Phú Thọ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2119, 'Dân Chủ', 24, NULL, NULL, NULL, 'Phú Thọ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2120, 'Dũng Tiến', 24, NULL, NULL, NULL, 'Phú Thọ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2121, 'Đà Bắc', 24, NULL, NULL, NULL, 'Phú Thọ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2122, 'Đại Đình', 24, NULL, NULL, NULL, 'Phú Thọ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2123, 'Đại Đồng', 24, NULL, NULL, NULL, 'Phú Thọ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2124, 'Đan Thượng', 24, NULL, NULL, NULL, 'Phú Thọ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2125, 'Đạo Trù', 24, NULL, NULL, NULL, 'Phú Thọ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2126, 'Đào Xá', 24, NULL, NULL, NULL, 'Phú Thọ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2127, 'Đoan Hùng', 24, NULL, NULL, NULL, 'Phú Thọ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2128, 'Đồng Lương', 24, NULL, NULL, NULL, 'Phú Thọ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2129, 'Đông Thành', 24, NULL, NULL, NULL, 'Phú Thọ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2130, 'Đức Nhàn', 24, NULL, NULL, NULL, 'Phú Thọ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2131, 'Hạ Hòa', 24, NULL, NULL, NULL, 'Phú Thọ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2132, 'Hải Lựu', 24, NULL, NULL, NULL, 'Phú Thọ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2133, 'Hiền Lương', 24, NULL, NULL, NULL, 'Phú Thọ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2134, 'Hiền Quan', 24, NULL, NULL, NULL, 'Phú Thọ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2135, 'Hòa Bình', 24, NULL, NULL, NULL, 'Phú Thọ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2136, 'Hoàng An', 24, NULL, NULL, NULL, 'Phú Thọ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2137, 'Hoàng Cương', 24, NULL, NULL, NULL, 'Phú Thọ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2138, 'Hội Thịnh', 24, NULL, NULL, NULL, 'Phú Thọ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2139, 'Hợp Kim', 24, NULL, NULL, NULL, 'Phú Thọ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2140, 'Hợp Lý', 24, NULL, NULL, NULL, 'Phú Thọ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2141, 'Hùng Việt', 24, NULL, NULL, NULL, 'Phú Thọ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2142, 'Hương Cần', 24, NULL, NULL, NULL, 'Phú Thọ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2143, 'Hy Cương', 24, NULL, NULL, NULL, 'Phú Thọ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2144, 'Khả Cửu', 24, NULL, NULL, NULL, 'Phú Thọ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2145, 'Kim Bôi', 24, NULL, NULL, NULL, 'Phú Thọ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2146, 'Kỳ Sơn', 24, NULL, NULL, NULL, 'Phú Thọ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2147, 'Lạc Lương', 24, NULL, NULL, NULL, 'Phú Thọ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2148, 'Lạc Sơn', 24, NULL, NULL, NULL, 'Phú Thọ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2149, 'Lạc Thủy', 24, NULL, NULL, NULL, 'Phú Thọ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2150, 'Lai Đồng', 24, NULL, NULL, NULL, 'Phú Thọ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2151, 'Lâm Thao', 24, NULL, NULL, NULL, 'Phú Thọ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2152, 'Lập Thạch', 24, NULL, NULL, NULL, 'Phú Thọ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2153, 'Liên Châu', 24, NULL, NULL, NULL, 'Phú Thọ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2154, 'Liên Hòa', 24, NULL, NULL, NULL, 'Phú Thọ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2155, 'Liên Minh', 24, NULL, NULL, NULL, 'Phú Thọ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2156, 'Liên Sơn', 24, NULL, NULL, NULL, 'Phú Thọ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2157, 'Long Cốc', 24, NULL, NULL, NULL, 'Phú Thọ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2158, 'Lương Sơn', 24, NULL, NULL, NULL, 'Phú Thọ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2159, 'Mai Châu', 24, NULL, NULL, NULL, 'Phú Thọ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2160, 'Mai Hạ', 24, NULL, NULL, NULL, 'Phú Thọ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2161, 'Minh Đài', 24, NULL, NULL, NULL, 'Phú Thọ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2162, 'Minh Hòa', 24, NULL, NULL, NULL, 'Phú Thọ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2163, 'Mường Bi', 24, NULL, NULL, NULL, 'Phú Thọ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2164, 'Mường Động', 24, NULL, NULL, NULL, 'Phú Thọ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2165, 'Mường Hoa', 24, NULL, NULL, NULL, 'Phú Thọ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2166, 'Mường Thàng', 24, NULL, NULL, NULL, 'Phú Thọ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2167, 'Mường Vang', 24, NULL, NULL, NULL, 'Phú Thọ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2168, 'Nật Sơn', 24, NULL, NULL, NULL, 'Phú Thọ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2169, 'Ngọc Sơn', 24, NULL, NULL, NULL, 'Phú Thọ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2170, 'Nguyệt Đức', 24, NULL, NULL, NULL, 'Phú Thọ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2171, 'Nhân Nghĩa', 24, NULL, NULL, NULL, 'Phú Thọ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2172, 'Nông Trang', 24, NULL, NULL, NULL, 'Phú Thọ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2173, 'Pà Cò', 24, NULL, NULL, NULL, 'Phú Thọ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2174, 'Phong Châu', 24, NULL, NULL, NULL, 'Phú Thọ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2175, 'Phú Khê', 24, NULL, NULL, NULL, 'Phú Thọ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2176, 'Phú Mỹ', 24, NULL, NULL, NULL, 'Phú Thọ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2177, 'Phù Ninh', 24, NULL, NULL, NULL, 'Phú Thọ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2178, 'Phú Thọ', 24, NULL, NULL, NULL, 'Phú Thọ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2179, 'Phúc Yên', 24, NULL, NULL, NULL, 'Phú Thọ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2180, 'Phùng Nguyên', 24, NULL, NULL, NULL, 'Phú Thọ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2181, 'Quảng Yên', 24, NULL, NULL, NULL, 'Phú Thọ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2182, 'Quy Đức', 24, NULL, NULL, NULL, 'Phú Thọ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2183, 'Quyết Thắng', 24, NULL, NULL, NULL, 'Phú Thọ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2184, 'Sông Lô', 24, NULL, NULL, NULL, 'Phú Thọ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2185, 'Sơn Đông', 24, NULL, NULL, NULL, 'Phú Thọ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2186, 'Sơn Lương', 24, NULL, NULL, NULL, 'Phú Thọ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2187, 'Tam Dương', 24, NULL, NULL, NULL, 'Phú Thọ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2188, 'Tam Dương Bắc', 24, NULL, NULL, NULL, 'Phú Thọ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2189, 'Tam Đảo', 24, NULL, NULL, NULL, 'Phú Thọ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2190, 'Tam Hồng', 24, NULL, NULL, NULL, 'Phú Thọ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2191, 'Tam Nông', 24, NULL, NULL, NULL, 'Phú Thọ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2192, 'Tam Sơn', 24, NULL, NULL, NULL, 'Phú Thọ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2193, 'Tân Hòa', 24, NULL, NULL, NULL, 'Phú Thọ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2194, 'Tân Lạc', 24, NULL, NULL, NULL, 'Phú Thọ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2195, 'Tân Mai', 24, NULL, NULL, NULL, 'Phú Thọ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2196, 'Tân Pheo', 24, NULL, NULL, NULL, 'Phú Thọ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2197, 'Tân Sơn', 24, NULL, NULL, NULL, 'Phú Thọ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2198, 'Tây Cốc', 24, NULL, NULL, NULL, 'Phú Thọ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2199, 'Tề Lỗ', 24, NULL, NULL, NULL, 'Phú Thọ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2200, 'Thái Hòa', 24, NULL, NULL, NULL, 'Phú Thọ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2201, 'Thanh Ba', 24, NULL, NULL, NULL, 'Phú Thọ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2202, 'Thanh Miếu', 24, NULL, NULL, NULL, 'Phú Thọ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2203, 'Thanh Sơn', 24, NULL, NULL, NULL, 'Phú Thọ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2204, 'Thanh Thủy', 24, NULL, NULL, NULL, 'Phú Thọ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2205, 'Thịnh Minh', 24, NULL, NULL, NULL, 'Phú Thọ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2206, 'Thọ Văn', 24, NULL, NULL, NULL, 'Phú Thọ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2207, 'Thổ Tang', 24, NULL, NULL, NULL, 'Phú Thọ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2208, 'Thống Nhất', 24, NULL, NULL, NULL, 'Phú Thọ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2209, 'Thu Cúc', 24, NULL, NULL, NULL, 'Phú Thọ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2210, 'Thung Nai', 24, NULL, NULL, NULL, 'Phú Thọ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2211, 'Thượng Cốc', 24, NULL, NULL, NULL, 'Phú Thọ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2212, 'Thượng Long', 24, NULL, NULL, NULL, 'Phú Thọ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2213, 'Tiên Lữ', 24, NULL, NULL, NULL, 'Phú Thọ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2214, 'Tiên Lương', 24, NULL, NULL, NULL, 'Phú Thọ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2215, 'Tiền Phong', 24, NULL, NULL, NULL, 'Phú Thọ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2216, 'Toàn Thắng', 24, NULL, NULL, NULL, 'Phú Thọ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2217, 'Trạm Thản', 24, NULL, NULL, NULL, 'Phú Thọ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2218, 'Trung Sơn', 24, NULL, NULL, NULL, 'Phú Thọ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2219, 'Tu Vũ', 24, NULL, NULL, NULL, 'Phú Thọ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2220, 'Vạn Xuân', 24, NULL, NULL, NULL, 'Phú Thọ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2221, 'Văn Lang', 24, NULL, NULL, NULL, 'Phú Thọ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2222, 'Văn Miếu', 24, NULL, NULL, NULL, 'Phú Thọ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2223, 'Vân Bán', 24, NULL, NULL, NULL, 'Phú Thọ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2224, 'Vân Phú', 24, NULL, NULL, NULL, 'Phú Thọ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2225, 'Vân Sơn', 24, NULL, NULL, NULL, 'Phú Thọ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2226, 'Việt Trì', 24, NULL, NULL, NULL, 'Phú Thọ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2227, 'Vĩnh An', 24, NULL, NULL, NULL, 'Phú Thọ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2228, 'Vĩnh Chân', 24, NULL, NULL, NULL, 'Phú Thọ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2229, 'Vĩnh Hưng', 24, NULL, NULL, NULL, 'Phú Thọ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2230, 'Vĩnh Phú', 24, NULL, NULL, NULL, 'Phú Thọ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2231, 'Vĩnh Phúc', 24, NULL, NULL, NULL, 'Phú Thọ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2232, 'Vĩnh Thành', 24, NULL, NULL, NULL, 'Phú Thọ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2233, 'Vĩnh Tường', 24, NULL, NULL, NULL, 'Phú Thọ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2234, 'Vĩnh Yên', 24, NULL, NULL, NULL, 'Phú Thọ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2235, 'Võ Miếu', 24, NULL, NULL, NULL, 'Phú Thọ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2236, 'Xuân Đài', 24, NULL, NULL, NULL, 'Phú Thọ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2237, 'Xuân Hòa', 24, NULL, NULL, NULL, 'Phú Thọ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2238, 'Xuân Lãng', 24, NULL, NULL, NULL, 'Phú Thọ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2239, 'Xuân Lũng', 24, NULL, NULL, NULL, 'Phú Thọ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2240, 'Xuân Viên', 24, NULL, NULL, NULL, 'Phú Thọ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2241, 'Yên Kỳ', 24, NULL, NULL, NULL, 'Phú Thọ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2242, 'Yên Lạc', 24, NULL, NULL, NULL, 'Phú Thọ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2243, 'Yên Lãng', 24, NULL, NULL, NULL, 'Phú Thọ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2244, 'Yên Lập', 24, NULL, NULL, NULL, 'Phú Thọ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2245, 'Yên Phú', 24, NULL, NULL, NULL, 'Phú Thọ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2246, 'Yên Sơn', 24, NULL, NULL, NULL, 'Phú Thọ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2247, 'Yên Thủy', 24, NULL, NULL, NULL, 'Phú Thọ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2248, 'Yên Trị', 24, NULL, NULL, NULL, 'Phú Thọ');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2249, 'An Phú', 25, NULL, NULL, NULL, 'Quảng Ngãi');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2250, 'Ba Dinh', 25, NULL, NULL, NULL, 'Quảng Ngãi');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2251, 'Ba Động', 25, NULL, NULL, NULL, 'Quảng Ngãi');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2252, 'Ba Gia', 25, NULL, NULL, NULL, 'Quảng Ngãi');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2253, 'Ba Tô', 25, NULL, NULL, NULL, 'Quảng Ngãi');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2254, 'Ba Tơ', 25, NULL, NULL, NULL, 'Quảng Ngãi');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2255, 'Ba Vì', 25, NULL, NULL, NULL, 'Quảng Ngãi');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2256, 'Ba Vinh', 25, NULL, NULL, NULL, 'Quảng Ngãi');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2257, 'Ba Xa', 25, NULL, NULL, NULL, 'Quảng Ngãi');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2258, 'Bình Chương', 25, NULL, NULL, NULL, 'Quảng Ngãi');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2259, 'Bình Minh', 25, NULL, NULL, NULL, 'Quảng Ngãi');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2260, 'Bình Sơn', 25, NULL, NULL, NULL, 'Quảng Ngãi');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2261, 'Bờ Y', 25, NULL, NULL, NULL, 'Quảng Ngãi');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2262, 'Cà Đam', 25, NULL, NULL, NULL, 'Quảng Ngãi');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2263, 'Cẩm Thành', 25, NULL, NULL, NULL, 'Quảng Ngãi');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2264, 'Dục Nông', 25, NULL, NULL, NULL, 'Quảng Ngãi');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2265, 'Đăk Bla', 25, NULL, NULL, NULL, 'Quảng Ngãi');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2266, 'Đăk Cấm', 25, NULL, NULL, NULL, 'Quảng Ngãi');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2267, 'Đăk Hà', 25, NULL, NULL, NULL, 'Quảng Ngãi');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2268, 'Đăk Kôi', 25, NULL, NULL, NULL, 'Quảng Ngãi');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2269, 'Đăk Long', 25, NULL, NULL, NULL, 'Quảng Ngãi');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2270, 'Đăk Mar', 25, NULL, NULL, NULL, 'Quảng Ngãi');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2271, 'Đăk Môn', 25, NULL, NULL, NULL, 'Quảng Ngãi');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2272, 'Đăk Pék', 25, NULL, NULL, NULL, 'Quảng Ngãi');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2273, 'Đăk Plô', 25, NULL, NULL, NULL, 'Quảng Ngãi');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2274, 'Đăk Pxi', 25, NULL, NULL, NULL, 'Quảng Ngãi');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2275, 'Đăk Rơ Wa', 25, NULL, NULL, NULL, 'Quảng Ngãi');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2276, 'Đăk Rve', 25, NULL, NULL, NULL, 'Quảng Ngãi');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2277, 'Đăk Sao', 25, NULL, NULL, NULL, 'Quảng Ngãi');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2278, 'Đăk Tô', 25, NULL, NULL, NULL, 'Quảng Ngãi');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2279, 'Đăk Tờ Kan', 25, NULL, NULL, NULL, 'Quảng Ngãi');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2280, 'Đăk Ui', 25, NULL, NULL, NULL, 'Quảng Ngãi');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2281, 'Đặng Thùy Trâm', 25, NULL, NULL, NULL, 'Quảng Ngãi');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2282, 'Đình Cương', 25, NULL, NULL, NULL, 'Quảng Ngãi');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2283, 'Đông Sơn', 25, NULL, NULL, NULL, 'Quảng Ngãi');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2284, 'Đông Trà Bồng', 25, NULL, NULL, NULL, 'Quảng Ngãi');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2285, 'Đức Phổ', 25, NULL, NULL, NULL, 'Quảng Ngãi');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2286, 'Ia Chim', 25, NULL, NULL, NULL, 'Quảng Ngãi');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2287, 'Ia Đal', 25, NULL, NULL, NULL, 'Quảng Ngãi');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2288, 'Ia Tơi', 25, NULL, NULL, NULL, 'Quảng Ngãi');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2289, 'Khánh Cường', 25, NULL, NULL, NULL, 'Quảng Ngãi');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2290, 'Kon Braih', 25, NULL, NULL, NULL, 'Quảng Ngãi');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2291, 'Kon Đào', 25, NULL, NULL, NULL, 'Quảng Ngãi');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2292, 'Kon Plông', 25, NULL, NULL, NULL, 'Quảng Ngãi');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2293, 'Kon Tum', 25, NULL, NULL, NULL, 'Quảng Ngãi');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2294, 'Lân Phong', 25, NULL, NULL, NULL, 'Quảng Ngãi');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2295, 'Long Phụng', 25, NULL, NULL, NULL, 'Quảng Ngãi');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2296, 'Lý Sơn', 25, NULL, NULL, NULL, 'Quảng Ngãi');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2297, 'Măng Bút', 25, NULL, NULL, NULL, 'Quảng Ngãi');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2298, 'Măng Đen', 25, NULL, NULL, NULL, 'Quảng Ngãi');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2299, 'Măng Ri', 25, NULL, NULL, NULL, 'Quảng Ngãi');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2300, 'Minh Long', 25, NULL, NULL, NULL, 'Quảng Ngãi');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2301, 'Mỏ Cày', 25, NULL, NULL, NULL, 'Quảng Ngãi');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2302, 'Mộ Đức', 25, NULL, NULL, NULL, 'Quảng Ngãi');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2303, 'Mô Rai', 25, NULL, NULL, NULL, 'Quảng Ngãi');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2304, 'Nghĩa Giang', 25, NULL, NULL, NULL, 'Quảng Ngãi');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2305, 'Nghĩa Hành', 25, NULL, NULL, NULL, 'Quảng Ngãi');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2306, 'Nghĩa Lộ', 25, NULL, NULL, NULL, 'Quảng Ngãi');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2307, 'Ngọc Linh', 25, NULL, NULL, NULL, 'Quảng Ngãi');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2308, 'Ngọk Bay', 25, NULL, NULL, NULL, 'Quảng Ngãi');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2309, 'Ngọk Réo', 25, NULL, NULL, NULL, 'Quảng Ngãi');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2310, 'Ngọk Tụ', 25, NULL, NULL, NULL, 'Quảng Ngãi');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2311, 'Nguyễn Nghiêm', 25, NULL, NULL, NULL, 'Quảng Ngãi');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2312, 'Phước Giang', 25, NULL, NULL, NULL, 'Quảng Ngãi');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2313, 'Rờ Kơi', 25, NULL, NULL, NULL, 'Quảng Ngãi');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2314, 'Sa Bình', 25, NULL, NULL, NULL, 'Quảng Ngãi');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2315, 'Sa Huỳnh', 25, NULL, NULL, NULL, 'Quảng Ngãi');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2316, 'Sa Loong', 25, NULL, NULL, NULL, 'Quảng Ngãi');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2317, 'Sa Thầy', 25, NULL, NULL, NULL, 'Quảng Ngãi');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2318, 'Sơn Hà', 25, NULL, NULL, NULL, 'Quảng Ngãi');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2319, 'Sơn Hạ', 25, NULL, NULL, NULL, 'Quảng Ngãi');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2320, 'Sơn Kỳ', 25, NULL, NULL, NULL, 'Quảng Ngãi');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2321, 'Sơn Linh', 25, NULL, NULL, NULL, 'Quảng Ngãi');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2322, 'Sơn Mai', 25, NULL, NULL, NULL, 'Quảng Ngãi');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2323, 'Sơn Tây', 25, NULL, NULL, NULL, 'Quảng Ngãi');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2324, 'Sơn Tây Hạ', 25, NULL, NULL, NULL, 'Quảng Ngãi');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2325, 'Sơn Tây Thượng', 25, NULL, NULL, NULL, 'Quảng Ngãi');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2326, 'Sơn Thủy', 25, NULL, NULL, NULL, 'Quảng Ngãi');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2327, 'Sơn Tịnh', 25, NULL, NULL, NULL, 'Quảng Ngãi');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2328, 'Tây Trà', 25, NULL, NULL, NULL, 'Quảng Ngãi');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2329, 'Tây Trà Bồng', 25, NULL, NULL, NULL, 'Quảng Ngãi');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2330, 'Thanh Bồng', 25, NULL, NULL, NULL, 'Quảng Ngãi');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2331, 'Thiện Tín', 25, NULL, NULL, NULL, 'Quảng Ngãi');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2332, 'Thọ Phong', 25, NULL, NULL, NULL, 'Quảng Ngãi');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2333, 'Tịnh Khê', 25, NULL, NULL, NULL, 'Quảng Ngãi');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2334, 'Trà Bồng', 25, NULL, NULL, NULL, 'Quảng Ngãi');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2335, 'Trà Câu', 25, NULL, NULL, NULL, 'Quảng Ngãi');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2336, 'Trà Giang', 25, NULL, NULL, NULL, 'Quảng Ngãi');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2337, 'Trường Giang', 25, NULL, NULL, NULL, 'Quảng Ngãi');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2338, 'Trương Quang Trọng', 25, NULL, NULL, NULL, 'Quảng Ngãi');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2339, 'Tu Mơ Rông', 25, NULL, NULL, NULL, 'Quảng Ngãi');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2340, 'Tư Nghĩa', 25, NULL, NULL, NULL, 'Quảng Ngãi');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2341, 'Vạn Tường', 25, NULL, NULL, NULL, 'Quảng Ngãi');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2342, 'Vệ Giang', 25, NULL, NULL, NULL, 'Quảng Ngãi');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2343, 'Xốp', 25, NULL, NULL, NULL, 'Quảng Ngãi');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2344, 'Ya Ly', 25, NULL, NULL, NULL, 'Quảng Ngãi');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2345, 'An Sinh', 26, NULL, NULL, NULL, 'Quảng Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2346, 'Ba Chẽ', 26, NULL, NULL, NULL, 'Quảng Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2347, 'Bãi Cháy', 26, NULL, NULL, NULL, 'Quảng Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2348, 'Bình Khê', 26, NULL, NULL, NULL, 'Quảng Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2349, 'Bình Liêu', 26, NULL, NULL, NULL, 'Quảng Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2350, 'Cái Chiên', 26, NULL, NULL, NULL, 'Quảng Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2351, 'Cao Xanh', 26, NULL, NULL, NULL, 'Quảng Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2352, 'Cẩm Phả', 26, NULL, NULL, NULL, 'Quảng Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2353, 'Cô Tô', 26, NULL, NULL, NULL, 'Quảng Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2354, 'Cửa Ông', 26, NULL, NULL, NULL, 'Quảng Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2355, 'Đầm Hà', 26, NULL, NULL, NULL, 'Quảng Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2356, 'Điền Xá', 26, NULL, NULL, NULL, 'Quảng Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2357, 'Đông Mai', 26, NULL, NULL, NULL, 'Quảng Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2358, 'Đông Ngũ', 26, NULL, NULL, NULL, 'Quảng Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2359, 'Đông Triều', 26, NULL, NULL, NULL, 'Quảng Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2360, 'Đường Hoa', 26, NULL, NULL, NULL, 'Quảng Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2361, 'Hà An', 26, NULL, NULL, NULL, 'Quảng Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2362, 'Hà Lầm', 26, NULL, NULL, NULL, 'Quảng Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2363, 'Hạ Long', 26, NULL, NULL, NULL, 'Quảng Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2364, 'Hà Tu', 26, NULL, NULL, NULL, 'Quảng Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2365, 'Hải Hòa', 26, NULL, NULL, NULL, 'Quảng Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2366, 'Hải Lạng', 26, NULL, NULL, NULL, 'Quảng Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2367, 'Hải Ninh', 26, NULL, NULL, NULL, 'Quảng Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2368, 'Hải Sơn', 26, NULL, NULL, NULL, 'Quảng Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2369, 'Hiệp Hòa', 26, NULL, NULL, NULL, 'Quảng Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2370, 'Hoàng Quế', 26, NULL, NULL, NULL, 'Quảng Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2371, 'Hoành Bồ', 26, NULL, NULL, NULL, 'Quảng Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2372, 'Hoành Mô', 26, NULL, NULL, NULL, 'Quảng Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2373, 'Hồng Gai', 26, NULL, NULL, NULL, 'Quảng Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2374, 'Kỳ Thượng', 26, NULL, NULL, NULL, 'Quảng Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2375, 'Liên Hòa', 26, NULL, NULL, NULL, 'Quảng Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2376, 'Lục Hồn', 26, NULL, NULL, NULL, 'Quảng Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2377, 'Lương Minh', 26, NULL, NULL, NULL, 'Quảng Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2378, 'Mạo Khê', 26, NULL, NULL, NULL, 'Quảng Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2379, 'Móng Cái 1', 26, NULL, NULL, NULL, 'Quảng Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2380, 'Móng Cái 2', 26, NULL, NULL, NULL, 'Quảng Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2381, 'Móng Cái 3', 26, NULL, NULL, NULL, 'Quảng Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2382, 'Mông Dương', 26, NULL, NULL, NULL, 'Quảng Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2383, 'Phong Cốc', 26, NULL, NULL, NULL, 'Quảng Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2384, 'Quảng Đức', 26, NULL, NULL, NULL, 'Quảng Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2385, 'Quảng Hà', 26, NULL, NULL, NULL, 'Quảng Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2386, 'Quang Hanh', 26, NULL, NULL, NULL, 'Quảng Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2387, 'Quảng La', 26, NULL, NULL, NULL, 'Quảng Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2388, 'Quảng Tân', 26, NULL, NULL, NULL, 'Quảng Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2389, 'Quảng Yên', 26, NULL, NULL, NULL, 'Quảng Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2390, 'Thống Nhất', 26, NULL, NULL, NULL, 'Quảng Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2391, 'Tiên Yên', 26, NULL, NULL, NULL, 'Quảng Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2392, 'Tuần Châu', 26, NULL, NULL, NULL, 'Quảng Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2393, 'Uông Bí', 26, NULL, NULL, NULL, 'Quảng Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2394, 'Vàng Danh', 26, NULL, NULL, NULL, 'Quảng Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2395, 'Vân Đồn', 26, NULL, NULL, NULL, 'Quảng Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2396, 'Việt Hưng', 26, NULL, NULL, NULL, 'Quảng Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2397, 'Vĩnh Thực', 26, NULL, NULL, NULL, 'Quảng Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2398, 'Yên Tử', 26, NULL, NULL, NULL, 'Quảng Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2399, 'A Dơi', 27, NULL, NULL, NULL, 'Quảng Trị');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2400, 'Ái Tử', 27, NULL, NULL, NULL, 'Quảng Trị');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2401, 'Ba Đồn', 27, NULL, NULL, NULL, 'Quảng Trị');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2402, 'Ba Lòng', 27, NULL, NULL, NULL, 'Quảng Trị');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2403, 'Bắc Gianh', 27, NULL, NULL, NULL, 'Quảng Trị');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2404, 'Bắc Trạch', 27, NULL, NULL, NULL, 'Quảng Trị');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2405, 'Bến Hải', 27, NULL, NULL, NULL, 'Quảng Trị');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2406, 'Bến Quan', 27, NULL, NULL, NULL, 'Quảng Trị');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2407, 'Bố Trạch', 27, NULL, NULL, NULL, 'Quảng Trị');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2408, 'Cam Hồng', 27, NULL, NULL, NULL, 'Quảng Trị');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2409, 'Cam Lộ', 27, NULL, NULL, NULL, 'Quảng Trị');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2410, 'Cồn Cỏ', 27, NULL, NULL, NULL, 'Quảng Trị');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2411, 'Cồn Tiên', 27, NULL, NULL, NULL, 'Quảng Trị');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2412, 'Cửa Tùng', 27, NULL, NULL, NULL, 'Quảng Trị');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2413, 'Cửa Việt', 27, NULL, NULL, NULL, 'Quảng Trị');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2414, 'Dân Hóa', 27, NULL, NULL, NULL, 'Quảng Trị');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2415, 'Diên Sanh', 27, NULL, NULL, NULL, 'Quảng Trị');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2416, 'Đakrông', 27, NULL, NULL, NULL, 'Quảng Trị');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2417, 'Đông Hà', 27, NULL, NULL, NULL, 'Quảng Trị');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2418, 'Đồng Hới', 27, NULL, NULL, NULL, 'Quảng Trị');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2419, 'Đồng Lê', 27, NULL, NULL, NULL, 'Quảng Trị');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2420, 'Đồng Sơn', 27, NULL, NULL, NULL, 'Quảng Trị');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2421, 'Đồng Thuận', 27, NULL, NULL, NULL, 'Quảng Trị');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2422, 'Đông Trạch', 27, NULL, NULL, NULL, 'Quảng Trị');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2423, 'Gio Linh', 27, NULL, NULL, NULL, 'Quảng Trị');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2424, 'Hải Lăng', 27, NULL, NULL, NULL, 'Quảng Trị');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2425, 'Hiếu Giang', 27, NULL, NULL, NULL, 'Quảng Trị');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2426, 'Hòa Trạch', 27, NULL, NULL, NULL, 'Quảng Trị');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2427, 'Hoàn Lão', 27, NULL, NULL, NULL, 'Quảng Trị');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2428, 'Hướng Hiệp', 27, NULL, NULL, NULL, 'Quảng Trị');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2429, 'Hướng Lập', 27, NULL, NULL, NULL, 'Quảng Trị');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2430, 'Hướng Phùng', 27, NULL, NULL, NULL, 'Quảng Trị');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2431, 'Khe Sanh', 27, NULL, NULL, NULL, 'Quảng Trị');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2432, 'Kim Điền', 27, NULL, NULL, NULL, 'Quảng Trị');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2433, 'Kim Ngân', 27, NULL, NULL, NULL, 'Quảng Trị');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2434, 'Kim Phú', 27, NULL, NULL, NULL, 'Quảng Trị');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2435, 'La Lay', 27, NULL, NULL, NULL, 'Quảng Trị');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2436, 'Lao Bảo', 27, NULL, NULL, NULL, 'Quảng Trị');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2437, 'Lệ Ninh', 27, NULL, NULL, NULL, 'Quảng Trị');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2438, 'Lệ Thủy', 27, NULL, NULL, NULL, 'Quảng Trị');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2439, 'Lìa', 27, NULL, NULL, NULL, 'Quảng Trị');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2440, 'Minh Hóa', 27, NULL, NULL, NULL, 'Quảng Trị');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2441, 'Mỹ Thủy', 27, NULL, NULL, NULL, 'Quảng Trị');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2442, 'Nam Ba Đồn', 27, NULL, NULL, NULL, 'Quảng Trị');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2443, 'Nam Cửa Việt', 27, NULL, NULL, NULL, 'Quảng Trị');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2444, 'Nam Đông Hà', 27, NULL, NULL, NULL, 'Quảng Trị');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2445, 'Nam Gianh', 27, NULL, NULL, NULL, 'Quảng Trị');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2446, 'Nam Hải Lăng', 27, NULL, NULL, NULL, 'Quảng Trị');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2447, 'Nam Trạch', 27, NULL, NULL, NULL, 'Quảng Trị');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2448, 'Ninh Châu', 27, NULL, NULL, NULL, 'Quảng Trị');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2449, 'Phong Nha', 27, NULL, NULL, NULL, 'Quảng Trị');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2450, 'Phú Trạch', 27, NULL, NULL, NULL, 'Quảng Trị');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2451, 'Quảng Ninh', 27, NULL, NULL, NULL, 'Quảng Trị');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2452, 'Quảng Trạch', 27, NULL, NULL, NULL, 'Quảng Trị');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2453, 'Quảng Trị', 27, NULL, NULL, NULL, 'Quảng Trị');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2454, 'Sen Ngư', 27, NULL, NULL, NULL, 'Quảng Trị');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2455, 'Tà Rụt', 27, NULL, NULL, NULL, 'Quảng Trị');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2456, 'Tân Gianh', 27, NULL, NULL, NULL, 'Quảng Trị');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2457, 'Tân Lập', 27, NULL, NULL, NULL, 'Quảng Trị');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2458, 'Tân Mỹ', 27, NULL, NULL, NULL, 'Quảng Trị');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2459, 'Tân Thành', 27, NULL, NULL, NULL, 'Quảng Trị');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2460, 'Thượng Trạch', 27, NULL, NULL, NULL, 'Quảng Trị');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2461, 'Triệu Bình', 27, NULL, NULL, NULL, 'Quảng Trị');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2462, 'Triệu Cơ', 27, NULL, NULL, NULL, 'Quảng Trị');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2463, 'Triệu Phong', 27, NULL, NULL, NULL, 'Quảng Trị');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2464, 'Trung Thuần', 27, NULL, NULL, NULL, 'Quảng Trị');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2465, 'Trường Ninh', 27, NULL, NULL, NULL, 'Quảng Trị');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2466, 'Trường Phú', 27, NULL, NULL, NULL, 'Quảng Trị');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2467, 'Trường Sơn', 27, NULL, NULL, NULL, 'Quảng Trị');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2468, 'Tuyên Bình', 27, NULL, NULL, NULL, 'Quảng Trị');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2469, 'Tuyên Hóa', 27, NULL, NULL, NULL, 'Quảng Trị');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2470, 'Tuyên Lâm', 27, NULL, NULL, NULL, 'Quảng Trị');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2471, 'Tuyên Phú', 27, NULL, NULL, NULL, 'Quảng Trị');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2472, 'Tuyên Sơn', 27, NULL, NULL, NULL, 'Quảng Trị');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2473, 'Vĩnh Định', 27, NULL, NULL, NULL, 'Quảng Trị');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2474, 'Vĩnh Hoàng', 27, NULL, NULL, NULL, 'Quảng Trị');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2475, 'Vĩnh Linh', 27, NULL, NULL, NULL, 'Quảng Trị');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2476, 'Vĩnh Thủy', 27, NULL, NULL, NULL, 'Quảng Trị');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2477, 'Bắc Yên', 28, NULL, NULL, NULL, 'Sơn La');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2478, 'Bình Thuận', 28, NULL, NULL, NULL, 'Sơn La');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2479, 'Bó Sinh', 28, NULL, NULL, NULL, 'Sơn La');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2480, 'Chiềng An', 28, NULL, NULL, NULL, 'Sơn La');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2481, 'Chiềng Cơi', 28, NULL, NULL, NULL, 'Sơn La');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2482, 'Chiềng Hặc', 28, NULL, NULL, NULL, 'Sơn La');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2483, 'Chiềng Hoa', 28, NULL, NULL, NULL, 'Sơn La');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2484, 'Chiềng Khoong', 28, NULL, NULL, NULL, 'Sơn La');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2485, 'Chiềng Khương', 28, NULL, NULL, NULL, 'Sơn La');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2486, 'Chiềng La', 28, NULL, NULL, NULL, 'Sơn La');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2487, 'Chiềng Lao', 28, NULL, NULL, NULL, 'Sơn La');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2488, 'Chiềng Mai', 28, NULL, NULL, NULL, 'Sơn La');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2489, 'Chiềng Mung', 28, NULL, NULL, NULL, 'Sơn La');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2490, 'Chiềng Sại', 28, NULL, NULL, NULL, 'Sơn La');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2491, 'Chiềng Sinh', 28, NULL, NULL, NULL, 'Sơn La');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2492, 'Chiềng Sơ', 28, NULL, NULL, NULL, 'Sơn La');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2493, 'Chiềng Sơn', 28, NULL, NULL, NULL, 'Sơn La');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2494, 'Chiềng Sung', 28, NULL, NULL, NULL, 'Sơn La');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2495, 'Co Mạ', 28, NULL, NULL, NULL, 'Sơn La');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2496, 'Đoàn Kết', 28, NULL, NULL, NULL, 'Sơn La');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2497, 'Gia Phù', 28, NULL, NULL, NULL, 'Sơn La');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2498, 'Huổi Một', 28, NULL, NULL, NULL, 'Sơn La');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2499, 'Kim Bon', 28, NULL, NULL, NULL, 'Sơn La');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2500, 'Long Hẹ', 28, NULL, NULL, NULL, 'Sơn La');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2501, 'Lóng Phiêng', 28, NULL, NULL, NULL, 'Sơn La');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2502, 'Lóng Sập', 28, NULL, NULL, NULL, 'Sơn La');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2503, 'Mai Sơn', 28, NULL, NULL, NULL, 'Sơn La');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2504, 'Mộc Châu', 28, NULL, NULL, NULL, 'Sơn La');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2505, 'Mộc Sơn', 28, NULL, NULL, NULL, 'Sơn La');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2506, 'Muổi Nọi', 28, NULL, NULL, NULL, 'Sơn La');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2507, 'Mường Bám', 28, NULL, NULL, NULL, 'Sơn La');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2508, 'Mường Bang', 28, NULL, NULL, NULL, 'Sơn La');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2509, 'Mường Bú', 28, NULL, NULL, NULL, 'Sơn La');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2510, 'Mường Chanh', 28, NULL, NULL, NULL, 'Sơn La');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2511, 'Mường Chiên', 28, NULL, NULL, NULL, 'Sơn La');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2512, 'Mường Cơi', 28, NULL, NULL, NULL, 'Sơn La');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2513, 'Mường É', 28, NULL, NULL, NULL, 'Sơn La');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2514, 'Mường Giôn', 28, NULL, NULL, NULL, 'Sơn La');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2515, 'Mường Hung', 28, NULL, NULL, NULL, 'Sơn La');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2516, 'Mường Khiêng', 28, NULL, NULL, NULL, 'Sơn La');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2517, 'Mường La', 28, NULL, NULL, NULL, 'Sơn La');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2518, 'Mường Lạn', 28, NULL, NULL, NULL, 'Sơn La');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2519, 'Mường Lầm', 28, NULL, NULL, NULL, 'Sơn La');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2520, 'Mường Lèo', 28, NULL, NULL, NULL, 'Sơn La');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2521, 'Mường Sại', 28, NULL, NULL, NULL, 'Sơn La');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2522, 'Nậm Lầu', 28, NULL, NULL, NULL, 'Sơn La');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2523, 'Nậm Ty', 28, NULL, NULL, NULL, 'Sơn La');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2524, 'Ngọc Chiến', 28, NULL, NULL, NULL, 'Sơn La');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2525, 'Pắc Ngà', 28, NULL, NULL, NULL, 'Sơn La');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2526, 'Phiêng Cằm', 28, NULL, NULL, NULL, 'Sơn La');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2527, 'Phiêng Khoài', 28, NULL, NULL, NULL, 'Sơn La');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2528, 'Phiêng Pằn', 28, NULL, NULL, NULL, 'Sơn La');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2529, 'Phù Yên', 28, NULL, NULL, NULL, 'Sơn La');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2530, 'Púng Bánh', 28, NULL, NULL, NULL, 'Sơn La');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2531, 'Quỳnh Nhai', 28, NULL, NULL, NULL, 'Sơn La');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2532, 'Song Khủa', 28, NULL, NULL, NULL, 'Sơn La');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2533, 'Sông Mã', 28, NULL, NULL, NULL, 'Sơn La');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2534, 'Sốp Cộp', 28, NULL, NULL, NULL, 'Sơn La');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2535, 'Suối Tọ', 28, NULL, NULL, NULL, 'Sơn La');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2536, 'Tà Hộc', 28, NULL, NULL, NULL, 'Sơn La');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2537, 'Tạ Khoa', 28, NULL, NULL, NULL, 'Sơn La');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2538, 'Tà Xùa', 28, NULL, NULL, NULL, 'Sơn La');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2539, 'Tân Phong', 28, NULL, NULL, NULL, 'Sơn La');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2540, 'Tân Yên', 28, NULL, NULL, NULL, 'Sơn La');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2541, 'Thảo Nguyên', 28, NULL, NULL, NULL, 'Sơn La');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2542, 'Thuận Châu', 28, NULL, NULL, NULL, 'Sơn La');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2543, 'Tô Hiệu', 28, NULL, NULL, NULL, 'Sơn La');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2544, 'Tô Múa', 28, NULL, NULL, NULL, 'Sơn La');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2545, 'Tường Hạ', 28, NULL, NULL, NULL, 'Sơn La');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2546, 'Vân Hồ', 28, NULL, NULL, NULL, 'Sơn La');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2547, 'Vân Sơn', 28, NULL, NULL, NULL, 'Sơn La');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2548, 'Xím Vàng', 28, NULL, NULL, NULL, 'Sơn La');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2549, 'Xuân Nha', 28, NULL, NULL, NULL, 'Sơn La');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2550, 'Yên Châu', 28, NULL, NULL, NULL, 'Sơn La');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2551, 'Yên Sơn', 28, NULL, NULL, NULL, 'Sơn La');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2552, 'An Lục Long', 29, NULL, NULL, NULL, 'Tây Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2553, 'An Ninh', 29, NULL, NULL, NULL, 'Tây Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2554, 'An Tịnh', 29, NULL, NULL, NULL, 'Tây Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2555, 'Bến Cầu', 29, NULL, NULL, NULL, 'Tây Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2556, 'Bến Lức', 29, NULL, NULL, NULL, 'Tây Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2557, 'Bình Đức', 29, NULL, NULL, NULL, 'Tây Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2558, 'Bình Hiệp', 29, NULL, NULL, NULL, 'Tây Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2559, 'Bình Hòa', 29, NULL, NULL, NULL, 'Tây Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2560, 'Bình Minh', 29, NULL, NULL, NULL, 'Tây Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2561, 'Bình Thành', 29, NULL, NULL, NULL, 'Tây Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2562, 'Cần Đước', 29, NULL, NULL, NULL, 'Tây Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2563, 'Cần Giuộc', 29, NULL, NULL, NULL, 'Tây Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2564, 'Cầu Khởi', 29, NULL, NULL, NULL, 'Tây Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2565, 'Châu Thành', 29, NULL, NULL, NULL, 'Tây Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2566, 'Dương Minh Châu', 29, NULL, NULL, NULL, 'Tây Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2567, 'Đông Thành', 29, NULL, NULL, NULL, 'Tây Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2568, 'Đức Hòa', 29, NULL, NULL, NULL, 'Tây Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2569, 'Đức Huệ', 29, NULL, NULL, NULL, 'Tây Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2570, 'Đức Lập', 29, NULL, NULL, NULL, 'Tây Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2571, 'Gia Lộc', 29, NULL, NULL, NULL, 'Tây Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2572, 'Gò Dầu', 29, NULL, NULL, NULL, 'Tây Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2573, 'Hảo Đước', 29, NULL, NULL, NULL, 'Tây Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2574, 'Hậu Nghĩa', 29, NULL, NULL, NULL, 'Tây Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2575, 'Hậu Thạnh', 29, NULL, NULL, NULL, 'Tây Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2576, 'Hiệp Hòa', 29, NULL, NULL, NULL, 'Tây Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2577, 'Hòa Hội', 29, NULL, NULL, NULL, 'Tây Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2578, 'Hòa Khánh', 29, NULL, NULL, NULL, 'Tây Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2579, 'Hòa Thành', 29, NULL, NULL, NULL, 'Tây Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2580, 'Hưng Điền', 29, NULL, NULL, NULL, 'Tây Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2581, 'Hưng Thuận', 29, NULL, NULL, NULL, 'Tây Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2582, 'Khánh Hậu', 29, NULL, NULL, NULL, 'Tây Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2583, 'Khánh Hưng', 29, NULL, NULL, NULL, 'Tây Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2584, 'Kiến Tường', 29, NULL, NULL, NULL, 'Tây Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2585, 'Long An', 29, NULL, NULL, NULL, 'Tây Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2586, 'Long Cang', 29, NULL, NULL, NULL, 'Tây Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2587, 'Long Chữ', 29, NULL, NULL, NULL, 'Tây Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2588, 'Long Hoa', 29, NULL, NULL, NULL, 'Tây Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2589, 'Long Hựu', 29, NULL, NULL, NULL, 'Tây Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2590, 'Long Thuận', 29, NULL, NULL, NULL, 'Tây Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2591, 'Lộc Ninh', 29, NULL, NULL, NULL, 'Tây Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2592, 'Lương Hòa', 29, NULL, NULL, NULL, 'Tây Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2593, 'Mộc Hóa', 29, NULL, NULL, NULL, 'Tây Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2594, 'Mỹ An', 29, NULL, NULL, NULL, 'Tây Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2595, 'Mỹ Hạnh', 29, NULL, NULL, NULL, 'Tây Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2596, 'Mỹ Lệ', 29, NULL, NULL, NULL, 'Tây Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2597, 'Mỹ Lộc', 29, NULL, NULL, NULL, 'Tây Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2598, 'Mỹ Quý', 29, NULL, NULL, NULL, 'Tây Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2599, 'Mỹ Thạnh', 29, NULL, NULL, NULL, 'Tây Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2600, 'Mỹ Yên', 29, NULL, NULL, NULL, 'Tây Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2601, 'Nhơn Hòa Lập', 29, NULL, NULL, NULL, 'Tây Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2602, 'Nhơn Ninh', 29, NULL, NULL, NULL, 'Tây Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2603, 'Nhựt Tảo', 29, NULL, NULL, NULL, 'Tây Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2604, 'Ninh Điền', 29, NULL, NULL, NULL, 'Tây Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2605, 'Ninh Thạnh', 29, NULL, NULL, NULL, 'Tây Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2606, 'Phước Chỉ', 29, NULL, NULL, NULL, 'Tây Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2607, 'Phước Lý', 29, NULL, NULL, NULL, 'Tây Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2608, 'Phước Thạnh', 29, NULL, NULL, NULL, 'Tây Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2609, 'Phước Vinh', 29, NULL, NULL, NULL, 'Tây Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2610, 'Phước Vĩnh Tây', 29, NULL, NULL, NULL, 'Tây Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2611, 'Rạch Kiến', 29, NULL, NULL, NULL, 'Tây Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2612, 'Tầm Vu', 29, NULL, NULL, NULL, 'Tây Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2613, 'Tân An', 29, NULL, NULL, NULL, 'Tây Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2614, 'Tân Biên', 29, NULL, NULL, NULL, 'Tây Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2615, 'Tân Châu', 29, NULL, NULL, NULL, 'Tây Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2616, 'Tân Đông', 29, NULL, NULL, NULL, 'Tây Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2617, 'Tân Hòa', 29, NULL, NULL, NULL, 'Tây Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2618, 'Tân Hội', 29, NULL, NULL, NULL, 'Tây Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2619, 'Tân Hưng', 29, NULL, NULL, NULL, 'Tây Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2620, 'Tân Lân', 29, NULL, NULL, NULL, 'Tây Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2621, 'Tân Lập', 29, NULL, NULL, NULL, 'Tây Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2622, 'Tân Long', 29, NULL, NULL, NULL, 'Tây Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2623, 'Tân Ninh', 29, NULL, NULL, NULL, 'Tây Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2624, 'Tân Phú', 29, NULL, NULL, NULL, 'Tây Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2625, 'Tân Tập', 29, NULL, NULL, NULL, 'Tây Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2626, 'Tân Tây', 29, NULL, NULL, NULL, 'Tây Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2627, 'Tân Thành', 29, NULL, NULL, NULL, 'Tây Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2628, 'Tân Thạnh', 29, NULL, NULL, NULL, 'Tây Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2629, 'Tân Trụ', 29, NULL, NULL, NULL, 'Tây Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2630, 'Thạnh Bình', 29, NULL, NULL, NULL, 'Tây Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2631, 'Thanh Điền', 29, NULL, NULL, NULL, 'Tây Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2632, 'Thạnh Đức', 29, NULL, NULL, NULL, 'Tây Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2633, 'Thạnh Hóa', 29, NULL, NULL, NULL, 'Tây Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2634, 'Thạnh Lợi', 29, NULL, NULL, NULL, 'Tây Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2635, 'Thạnh Phước', 29, NULL, NULL, NULL, 'Tây Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2636, 'Thủ Thừa', 29, NULL, NULL, NULL, 'Tây Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2637, 'Thuận Mỹ', 29, NULL, NULL, NULL, 'Tây Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2638, 'Trà Vong', 29, NULL, NULL, NULL, 'Tây Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2639, 'Trảng Bàng', 29, NULL, NULL, NULL, 'Tây Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2640, 'Truông Mít', 29, NULL, NULL, NULL, 'Tây Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2641, 'Tuyên Bình', 29, NULL, NULL, NULL, 'Tây Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2642, 'Tuyên Thạnh', 29, NULL, NULL, NULL, 'Tây Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2643, 'Vàm Cỏ', 29, NULL, NULL, NULL, 'Tây Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2644, 'Vĩnh Châu', 29, NULL, NULL, NULL, 'Tây Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2645, 'Vĩnh Công', 29, NULL, NULL, NULL, 'Tây Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2646, 'Vĩnh Hưng', 29, NULL, NULL, NULL, 'Tây Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2647, 'Vĩnh Thạnh', 29, NULL, NULL, NULL, 'Tây Ninh');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2648, 'An Khánh', 30, NULL, NULL, NULL, 'Thái Nguyên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2649, 'Ba Bể', 30, NULL, NULL, NULL, 'Thái Nguyên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2650, 'Bá Xuyên', 30, NULL, NULL, NULL, 'Thái Nguyên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2651, 'Bách Quang', 30, NULL, NULL, NULL, 'Thái Nguyên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2652, 'Bạch Thông', 30, NULL, NULL, NULL, 'Thái Nguyên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2653, 'Bắc Kạn', 30, NULL, NULL, NULL, 'Thái Nguyên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2654, 'Bằng Thành', 30, NULL, NULL, NULL, 'Thái Nguyên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2655, 'Bằng Vân', 30, NULL, NULL, NULL, 'Thái Nguyên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2656, 'Bình Thành', 30, NULL, NULL, NULL, 'Thái Nguyên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2657, 'Bình Yên', 30, NULL, NULL, NULL, 'Thái Nguyên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2658, 'Cao Minh', 30, NULL, NULL, NULL, 'Thái Nguyên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2659, 'Cẩm Giàng', 30, NULL, NULL, NULL, 'Thái Nguyên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2660, 'Chợ Đồn', 30, NULL, NULL, NULL, 'Thái Nguyên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2661, 'Chợ Mới', 30, NULL, NULL, NULL, 'Thái Nguyên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2662, 'Chợ Rã', 30, NULL, NULL, NULL, 'Thái Nguyên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2663, 'Côn Minh', 30, NULL, NULL, NULL, 'Thái Nguyên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2664, 'Cường Lợi', 30, NULL, NULL, NULL, 'Thái Nguyên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2665, 'Dân Tiến', 30, NULL, NULL, NULL, 'Thái Nguyên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2666, 'Đại Phúc', 30, NULL, NULL, NULL, 'Thái Nguyên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2667, 'Đại Từ', 30, NULL, NULL, NULL, 'Thái Nguyên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2668, 'Điềm Thụy', 30, NULL, NULL, NULL, 'Thái Nguyên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2669, 'Định Hóa', 30, NULL, NULL, NULL, 'Thái Nguyên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2670, 'Đồng Hỷ', 30, NULL, NULL, NULL, 'Thái Nguyên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2671, 'Đồng Phúc', 30, NULL, NULL, NULL, 'Thái Nguyên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2672, 'Đức Lương', 30, NULL, NULL, NULL, 'Thái Nguyên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2673, 'Đức Xuân', 30, NULL, NULL, NULL, 'Thái Nguyên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2674, 'Gia Sàng', 30, NULL, NULL, NULL, 'Thái Nguyên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2675, 'Hiệp Lực', 30, NULL, NULL, NULL, 'Thái Nguyên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2676, 'Hợp Thành', 30, NULL, NULL, NULL, 'Thái Nguyên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2677, 'Kha Sơn', 30, NULL, NULL, NULL, 'Thái Nguyên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2678, 'Kim Phượng', 30, NULL, NULL, NULL, 'Thái Nguyên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2679, 'La Bằng', 30, NULL, NULL, NULL, 'Thái Nguyên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2680, 'La Hiên', 30, NULL, NULL, NULL, 'Thái Nguyên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2681, 'Lam Vỹ', 30, NULL, NULL, NULL, 'Thái Nguyên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2682, 'Linh Sơn', 30, NULL, NULL, NULL, 'Thái Nguyên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2683, 'Nà Phặc', 30, NULL, NULL, NULL, 'Thái Nguyên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2684, 'Na Rì', 30, NULL, NULL, NULL, 'Thái Nguyên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2685, 'Nam Cường', 30, NULL, NULL, NULL, 'Thái Nguyên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2686, 'Nam Hòa', 30, NULL, NULL, NULL, 'Thái Nguyên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2687, 'Ngân Sơn', 30, NULL, NULL, NULL, 'Thái Nguyên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2688, 'Nghĩa Tá', 30, NULL, NULL, NULL, 'Thái Nguyên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2689, 'Nghiên Loan', 30, NULL, NULL, NULL, 'Thái Nguyên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2690, 'Nghinh Tường', 30, NULL, NULL, NULL, 'Thái Nguyên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2691, 'Phan Đình Phùng', 30, NULL, NULL, NULL, 'Thái Nguyên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2692, 'Phong Quang', 30, NULL, NULL, NULL, 'Thái Nguyên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2693, 'Phổ Yên', 30, NULL, NULL, NULL, 'Thái Nguyên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2694, 'Phú Bình', 30, NULL, NULL, NULL, 'Thái Nguyên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2695, 'Phú Đình', 30, NULL, NULL, NULL, 'Thái Nguyên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2696, 'Phú Lạc', 30, NULL, NULL, NULL, 'Thái Nguyên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2697, 'Phú Lương', 30, NULL, NULL, NULL, 'Thái Nguyên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2698, 'Phú Thịnh', 30, NULL, NULL, NULL, 'Thái Nguyên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2699, 'Phủ Thông', 30, NULL, NULL, NULL, 'Thái Nguyên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2700, 'Phú Xuyên', 30, NULL, NULL, NULL, 'Thái Nguyên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2701, 'Phúc Lộc', 30, NULL, NULL, NULL, 'Thái Nguyên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2702, 'Phúc Thuận', 30, NULL, NULL, NULL, 'Thái Nguyên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2703, 'Phượng Tiến', 30, NULL, NULL, NULL, 'Thái Nguyên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2704, 'Quan Triều', 30, NULL, NULL, NULL, 'Thái Nguyên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2705, 'Quảng Bạch', 30, NULL, NULL, NULL, 'Thái Nguyên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2706, 'Quang Sơn', 30, NULL, NULL, NULL, 'Thái Nguyên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2707, 'Quân Chu', 30, NULL, NULL, NULL, 'Thái Nguyên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2708, 'Quyết Thắng', 30, NULL, NULL, NULL, 'Thái Nguyên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2709, 'Sảng Mộc', 30, NULL, NULL, NULL, 'Thái Nguyên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2710, 'Sông Công', 30, NULL, NULL, NULL, 'Thái Nguyên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2711, 'Tân Cương', 30, NULL, NULL, NULL, 'Thái Nguyên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2712, 'Tân Khánh', 30, NULL, NULL, NULL, 'Thái Nguyên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2713, 'Tân Kỳ', 30, NULL, NULL, NULL, 'Thái Nguyên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2714, 'Tân Thành', 30, NULL, NULL, NULL, 'Thái Nguyên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2715, 'Thành Công', 30, NULL, NULL, NULL, 'Thái Nguyên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2716, 'Thanh Mai', 30, NULL, NULL, NULL, 'Thái Nguyên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2717, 'Thanh Thịnh', 30, NULL, NULL, NULL, 'Thái Nguyên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2718, 'Thần Sa', 30, NULL, NULL, NULL, 'Thái Nguyên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2719, 'Thượng Minh', 30, NULL, NULL, NULL, 'Thái Nguyên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2720, 'Thượng Quan', 30, NULL, NULL, NULL, 'Thái Nguyên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2721, 'Tích Lương', 30, NULL, NULL, NULL, 'Thái Nguyên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2722, 'Trại Cau', 30, NULL, NULL, NULL, 'Thái Nguyên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2723, 'Tràng Xá', 30, NULL, NULL, NULL, 'Thái Nguyên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2724, 'Trần Phú', 30, NULL, NULL, NULL, 'Thái Nguyên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2725, 'Trung Hội', 30, NULL, NULL, NULL, 'Thái Nguyên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2726, 'Trung Thành', 30, NULL, NULL, NULL, 'Thái Nguyên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2727, 'Vạn Phú', 30, NULL, NULL, NULL, 'Thái Nguyên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2728, 'Vạn Xuân', 30, NULL, NULL, NULL, 'Thái Nguyên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2729, 'Văn Hán', 30, NULL, NULL, NULL, 'Thái Nguyên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2730, 'Văn Lang', 30, NULL, NULL, NULL, 'Thái Nguyên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2731, 'Văn Lăng', 30, NULL, NULL, NULL, 'Thái Nguyên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2732, 'Vĩnh Thông', 30, NULL, NULL, NULL, 'Thái Nguyên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2733, 'Võ Nhai', 30, NULL, NULL, NULL, 'Thái Nguyên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2734, 'Vô Tranh', 30, NULL, NULL, NULL, 'Thái Nguyên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2735, 'Xuân Dương', 30, NULL, NULL, NULL, 'Thái Nguyên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2736, 'Yên Bình', 30, NULL, NULL, NULL, 'Thái Nguyên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2737, 'Yên Phong', 30, NULL, NULL, NULL, 'Thái Nguyên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2738, 'Yên Thịnh', 30, NULL, NULL, NULL, 'Thái Nguyên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2739, 'Yên Trạch', 30, NULL, NULL, NULL, 'Thái Nguyên');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2740, 'An Nông', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2741, 'Ba Đình', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2742, 'Bá Thước', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2743, 'Bát Mọt', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2744, 'Biện Thượng', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2745, 'Bỉm Sơn', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2746, 'Các Sơn', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2747, 'Cẩm Tân', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2748, 'Cẩm Thạch', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2749, 'Cẩm Thủy', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2750, 'Cẩm Tú', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2751, 'Cẩm Vân', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2752, 'Cổ Lũng', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2753, 'Công Chính', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2754, 'Đào Duy Từ', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2755, 'Điền Lư', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2756, 'Điền Quang', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2757, 'Định Hòa', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2758, 'Định Tân', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2759, 'Đồng Lương', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2760, 'Đông Quang', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2761, 'Đông Sơn', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2762, 'Đông Thành', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2763, 'Đông Tiến', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2764, 'Đồng Tiến', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2765, 'Giao An', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2766, 'Hà Long', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2767, 'Hà Trung', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2768, 'Hạc Thành', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2769, 'Hải Bình', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2770, 'Hải Lĩnh', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2771, 'Hàm Rồng', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2772, 'Hậu Lộc', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2773, 'Hiền Kiệt', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2774, 'Hoa Lộc', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2775, 'Hóa Quỳ', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2776, 'Hoạt Giang', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2777, 'Hoằng Châu', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2778, 'Hoằng Giang', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2779, 'Hoằng Hóa', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2780, 'Hoằng Lộc', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2781, 'Hoằng Phú', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2782, 'Hoằng Sơn', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2783, 'Hoằng Thanh', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2784, 'Hoằng Tiến', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2785, 'Hồ Vương', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2786, 'Hồi Xuân', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2787, 'Hợp Tiến', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2788, 'Kiên Thọ', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2789, 'Kim Tân', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2790, 'Lam Sơn', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2791, 'Linh Sơn', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2792, 'Lĩnh Toại', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2793, 'Luận Thành', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2794, 'Lương Sơn', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2795, 'Lưu Vệ', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2796, 'Mậu Lâm', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2797, 'Minh Sơn', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2798, 'Mường Chanh', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2799, 'Mường Lát', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2800, 'Mường Lý', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2801, 'Mường Mìn', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2802, 'Na Mèo', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2803, 'Nam Sầm Sơn', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2804, 'Nam Xuân', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2805, 'Nga An', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2806, 'Nga Sơn', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2807, 'Nga Thắng', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2808, 'Nghi Sơn', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2809, 'Ngọc Lặc', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2810, 'Ngọc Liên', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2811, 'Ngọc Sơn', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2812, 'Ngọc Trạo', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2813, 'Nguyệt Ấn', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2814, 'Nguyệt Viên', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2815, 'Nhi Sơn', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2816, 'Như Thanh', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2817, 'Như Xuân', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2818, 'Nông Cống', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2819, 'Phú Lệ', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2820, 'Phú Xuân', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2821, 'Pù Luông', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2822, 'Pù Nhi', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2823, 'Quan Sơn', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2824, 'Quảng Bình', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2825, 'Quang Chiểu', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2826, 'Quảng Chính', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2827, 'Quảng Ngọc', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2828, 'Quảng Ninh', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2829, 'Quảng Phú', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2830, 'Quang Trung', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2831, 'Quảng Yên', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2832, 'Quý Lộc', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2833, 'Quý Lương', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2834, 'Sao Vàng', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2835, 'Sầm Sơn', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2836, 'Sơn Điện', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2837, 'Sơn Thủy', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2838, 'Tam Chung', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2839, 'Tam Lư', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2840, 'Tam Thanh', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2841, 'Tân Dân', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2842, 'Tân Ninh', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2843, 'Tân Thành', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2844, 'Tân Tiến', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2845, 'Tây Đô', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2846, 'Thạch Bình', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2847, 'Thạch Lập', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2848, 'Thạch Quảng', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2849, 'Thanh Kỳ', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2850, 'Thanh Phong', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2851, 'Thanh Quân', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2852, 'Thành Vinh', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2853, 'Thăng Bình', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2854, 'Thắng Lộc', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2855, 'Thắng Lợi', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2856, 'Thiên Phủ', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2857, 'Thiết Ống', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2858, 'Thiệu Hóa', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2859, 'Thiệu Quang', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2860, 'Thiệu Tiến', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2861, 'Thiệu Toán', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2862, 'Thiệu Trung', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2863, 'Thọ Bình', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2864, 'Thọ Lập', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2865, 'Thọ Long', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2866, 'Thọ Ngọc', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2867, 'Thọ Phú', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2868, 'Thọ Xuân', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2869, 'Thượng Ninh', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2870, 'Thường Xuân', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2871, 'Tiên Trang', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2872, 'Tĩnh Gia', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2873, 'Tống Sơn', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2874, 'Triệu Lộc', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2875, 'Triệu Sơn', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2876, 'Trúc Lâm', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2877, 'Trung Chính', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2878, 'Trung Hạ', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2879, 'Trung Lý', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2880, 'Trung Sơn', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2881, 'Trung Thành', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2882, 'Trường Lâm', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2883, 'Trường Văn', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2884, 'Tượng Lĩnh', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2885, 'Vạn Lộc', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2886, 'Vạn Xuân', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2887, 'Văn Nho', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2888, 'Văn Phú', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2889, 'Vân Du', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2890, 'Vĩnh Lộc', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2891, 'Xuân Bình', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2892, 'Xuân Chinh', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2893, 'Xuân Du', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2894, 'Xuân Hòa', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2895, 'Xuân Lập', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2896, 'Xuân Thái', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2897, 'Xuân Tín', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2898, 'Yên Định', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2899, 'Yên Khương', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2900, 'Yên Nhân', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2901, 'Yên Ninh', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2902, 'Yên Phú', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2903, 'Yên Thắng', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2904, 'Yên Thọ', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2905, 'Yên Trường', 31, NULL, NULL, NULL, 'Thanh Hóa');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2906, 'An Đông', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2907, 'An Hội Đông', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2908, 'An Hội Tây', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2909, 'An Khánh', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2910, 'An Lạc', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2911, 'An Long', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2912, 'An Nhơn', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2913, 'An Nhơn Tây', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2914, 'An Phú', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2915, 'An Phú Đông', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2916, 'An Thới Đông', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2917, 'Bà Điểm', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2918, 'Bà Rịa', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2919, 'Bàn Cờ', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2920, 'Bàu Bàng', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2921, 'Bàu Lâm', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2922, 'Bảy Hiền', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2923, 'Bắc Tân Uyên', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2924, 'Bến Cát', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2925, 'Bến Thành', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2926, 'Bình Chánh', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2927, 'Bình Châu', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2928, 'Bình Cơ', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2929, 'Bình Dương', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2930, 'Bình Đông', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2931, 'Bình Giã', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2932, 'Bình Hòa', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2933, 'Bình Hưng', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2934, 'Bình Hưng Hòa', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2935, 'Bình Khánh', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2936, 'Bình Lợi', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2937, 'Bình Lợi Trung', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2938, 'Bình Mỹ', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2939, 'Bình Phú', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2940, 'Bình Quới', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2941, 'Bình Tân', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2942, 'Bình Tây', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2943, 'Bình Thạnh', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2944, 'Bình Thới', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2945, 'Bình Tiên', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2946, 'Bình Trị Đông', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2947, 'Bình Trưng', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2948, 'Cát Lái', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2949, 'Cần Giờ', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2950, 'Cầu Kiệu', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2951, 'Cầu Ông Lãnh', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2952, 'Chánh Hiệp', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2953, 'Chánh Hưng', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2954, 'Chánh Phú Hòa', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2955, 'Châu Đức', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2956, 'Châu Pha', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2957, 'Chợ Lớn', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2958, 'Chợ Quán', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2959, 'Côn Đảo', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2960, 'Củ Chi', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2961, 'Dầu Tiếng', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2962, 'Dĩ An', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2963, 'Diên Hồng', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2964, 'Đất Đỏ', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2965, 'Đông Hòa', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2966, 'Đông Hưng Thuận', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2967, 'Đông Thạnh', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2968, 'Đức Nhuận', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2969, 'Gia Định', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2970, 'Gò Vấp', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2971, 'Hạnh Thông', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2972, 'Hiệp Bình', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2973, 'Hiệp Phước', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2974, 'Hòa Bình', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2975, 'Hòa Hiệp', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2976, 'Hòa Hội', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2977, 'Hòa Hưng', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2978, 'Hòa Lợi', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2979, 'Hóc Môn', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2980, 'Hồ Tràm', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2981, 'Hưng Long', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2982, 'Khánh Hội', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2983, 'Kim Long', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2984, 'Lái Thiêu', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2985, 'Linh Xuân', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2986, 'Long Bình', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2987, 'Long Điền', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2988, 'Long Hải', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2989, 'Long Hòa', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2990, 'Long Hương', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2991, 'Long Nguyên', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2992, 'Long Phước', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2993, 'Long Sơn', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2994, 'Long Trường', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2995, 'Minh Phụng', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2996, 'Minh Thạnh', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2997, 'Ngãi Giao', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2998, 'Nghĩa Thành', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (2999, 'Nhà Bè', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3000, 'Nhiêu Lộc', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3001, 'Nhuận Đức', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3002, 'Phú An', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3003, 'Phú Định', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3004, 'Phú Giáo', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3005, 'Phú Hòa Đông', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3006, 'Phú Lâm', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3007, 'Phú Lợi', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3008, 'Phú Mỹ', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3009, 'Phú Nhuận', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3010, 'Phú Thạnh', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3011, 'Phú Thọ', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3012, 'Phú Thọ Hòa', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3013, 'Phú Thuận', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3014, 'Phước Hải', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3015, 'Phước Hòa', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3016, 'Phước Long', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3017, 'Phước Thành', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3018, 'Phước Thắng', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3019, 'Rạch Dừa', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3020, 'Sài Gòn', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3021, 'Tam Bình', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3022, 'Tam Long', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3023, 'Tam Thắng', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3024, 'Tăng Nhơn Phú', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3025, 'Tân An Hội', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3026, 'Tân Bình', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3027, 'Tân Định', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3028, 'Tân Đông Hiệp', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3029, 'Tân Hải', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3030, 'Tân Hiệp', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3031, 'Tân Hòa', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3032, 'Tân Hưng', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3033, 'Tân Khánh', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3034, 'Tân Mỹ', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3035, 'Tân Nhựt', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3036, 'Tân Phú', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3037, 'Tân Phước', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3038, 'Tân Sơn', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3039, 'Tân Sơn Hòa', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3040, 'Tân Sơn Nhất', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3041, 'Tân Sơn Nhì', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3042, 'Tân Tạo', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3043, 'Tân Thành', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3044, 'Tân Thới Hiệp', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3045, 'Tân Thuận', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3046, 'Tân Uyên', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3047, 'Tân Vĩnh Lộc', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3048, 'Tây Nam', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3049, 'Tây Thạnh', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3050, 'Thái Mỹ', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3051, 'Thanh An', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3052, 'Thạnh An', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3053, 'Thạnh Mỹ Tây', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3054, 'Thông Tây Hội', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3055, 'Thới An', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3056, 'Thới Hòa', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3057, 'Thủ Dầu Một', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3058, 'Thủ Đức', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3059, 'Thuận An', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3060, 'Thuận Giao', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3061, 'Thường Tân', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3062, 'Trung Mỹ Tây', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3063, 'Trừ Văn Thố', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3064, 'Vĩnh Hội', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3065, 'Vĩnh Lộc', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3066, 'Vĩnh Tân', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3067, 'Vũng Tàu', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3068, 'Vườn Lài', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3069, 'Xóm Chiếu', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3070, 'Xuân Hòa', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3071, 'Xuân Sơn', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3072, 'Xuân Thới Sơn', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3073, 'Xuyên Mộc', 32, NULL, NULL, NULL, 'TP HCM');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3074, 'An Tường', 33, NULL, NULL, NULL, 'Tuyên Quang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3075, 'Bạch Đích', 33, NULL, NULL, NULL, 'Tuyên Quang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3076, 'Bạch Ngọc', 33, NULL, NULL, NULL, 'Tuyên Quang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3077, 'Bạch Xa', 33, NULL, NULL, NULL, 'Tuyên Quang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3078, 'Bản Máy', 33, NULL, NULL, NULL, 'Tuyên Quang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3079, 'Bắc Mê', 33, NULL, NULL, NULL, 'Tuyên Quang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3080, 'Bắc Quang', 33, NULL, NULL, NULL, 'Tuyên Quang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3081, 'Bằng Hành', 33, NULL, NULL, NULL, 'Tuyên Quang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3082, 'Bằng Lang', 33, NULL, NULL, NULL, 'Tuyên Quang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3083, 'Bình An', 33, NULL, NULL, NULL, 'Tuyên Quang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3084, 'Bình Ca', 33, NULL, NULL, NULL, 'Tuyên Quang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3085, 'Bình Thuận', 33, NULL, NULL, NULL, 'Tuyên Quang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3086, 'Bình Xa', 33, NULL, NULL, NULL, 'Tuyên Quang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3087, 'Cán Tỷ', 33, NULL, NULL, NULL, 'Tuyên Quang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3088, 'Cao Bồ', 33, NULL, NULL, NULL, 'Tuyên Quang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3089, 'Chiêm Hóa', 33, NULL, NULL, NULL, 'Tuyên Quang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3090, 'Côn Lôn', 33, NULL, NULL, NULL, 'Tuyên Quang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3091, 'Du Già', 33, NULL, NULL, NULL, 'Tuyên Quang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3092, 'Đồng Tâm', 33, NULL, NULL, NULL, 'Tuyên Quang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3093, 'Đông Thọ', 33, NULL, NULL, NULL, 'Tuyên Quang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3094, 'Đồng Văn', 33, NULL, NULL, NULL, 'Tuyên Quang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3095, 'Đồng Yên', 33, NULL, NULL, NULL, 'Tuyên Quang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3096, 'Đường Hồng', 33, NULL, NULL, NULL, 'Tuyên Quang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3097, 'Đường Thượng', 33, NULL, NULL, NULL, 'Tuyên Quang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3098, 'Giáp Trung', 33, NULL, NULL, NULL, 'Tuyên Quang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3099, 'Hà Giang 1', 33, NULL, NULL, NULL, 'Tuyên Quang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3100, 'Hà Giang 2', 33, NULL, NULL, NULL, 'Tuyên Quang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3101, 'Hàm Yên', 33, NULL, NULL, NULL, 'Tuyên Quang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3102, 'Hòa An', 33, NULL, NULL, NULL, 'Tuyên Quang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3103, 'Hoàng Su Phì', 33, NULL, NULL, NULL, 'Tuyên Quang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3104, 'Hồ Thầu', 33, NULL, NULL, NULL, 'Tuyên Quang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3105, 'Hồng Sơn', 33, NULL, NULL, NULL, 'Tuyên Quang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3106, 'Hồng Thái', 33, NULL, NULL, NULL, 'Tuyên Quang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3107, 'Hùng An', 33, NULL, NULL, NULL, 'Tuyên Quang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3108, 'Hùng Đức', 33, NULL, NULL, NULL, 'Tuyên Quang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3109, 'Hùng Lợi', 33, NULL, NULL, NULL, 'Tuyên Quang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3110, 'Khâu Vai', 33, NULL, NULL, NULL, 'Tuyên Quang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3111, 'Khuôn Lùng', 33, NULL, NULL, NULL, 'Tuyên Quang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3112, 'Kiên Đài', 33, NULL, NULL, NULL, 'Tuyên Quang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3113, 'Kiến Thiết', 33, NULL, NULL, NULL, 'Tuyên Quang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3114, 'Kim Bình', 33, NULL, NULL, NULL, 'Tuyên Quang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3115, 'Lao Chải', 33, NULL, NULL, NULL, 'Tuyên Quang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3116, 'Lâm Bình', 33, NULL, NULL, NULL, 'Tuyên Quang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3117, 'Liên Hiệp', 33, NULL, NULL, NULL, 'Tuyên Quang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3118, 'Linh Hồ', 33, NULL, NULL, NULL, 'Tuyên Quang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3119, 'Lũng Cú', 33, NULL, NULL, NULL, 'Tuyên Quang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3120, 'Lũng Phìn', 33, NULL, NULL, NULL, 'Tuyên Quang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3121, 'Lùng Tám', 33, NULL, NULL, NULL, 'Tuyên Quang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3122, 'Lực Hành', 33, NULL, NULL, NULL, 'Tuyên Quang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3123, 'Mậu Duệ', 33, NULL, NULL, NULL, 'Tuyên Quang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3124, 'Mèo Vạc', 33, NULL, NULL, NULL, 'Tuyên Quang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3125, 'Minh Ngọc', 33, NULL, NULL, NULL, 'Tuyên Quang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3126, 'Minh Quang', 33, NULL, NULL, NULL, 'Tuyên Quang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3127, 'Minh Sơn', 33, NULL, NULL, NULL, 'Tuyên Quang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3128, 'Minh Tân', 33, NULL, NULL, NULL, 'Tuyên Quang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3129, 'Minh Thanh', 33, NULL, NULL, NULL, 'Tuyên Quang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3130, 'Minh Xuân', 33, NULL, NULL, NULL, 'Tuyên Quang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3131, 'Mỹ Lâm', 33, NULL, NULL, NULL, 'Tuyên Quang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3132, 'Nà Hang', 33, NULL, NULL, NULL, 'Tuyên Quang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3133, 'Nấm Dẩn', 33, NULL, NULL, NULL, 'Tuyên Quang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3134, 'Nậm Dịch', 33, NULL, NULL, NULL, 'Tuyên Quang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3135, 'Nghĩa Thuận', 33, NULL, NULL, NULL, 'Tuyên Quang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3136, 'Ngọc Đường', 33, NULL, NULL, NULL, 'Tuyên Quang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3137, 'Ngọc Long', 33, NULL, NULL, NULL, 'Tuyên Quang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3138, 'Nhữ Khê', 33, NULL, NULL, NULL, 'Tuyên Quang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3139, 'Niêm Sơn', 33, NULL, NULL, NULL, 'Tuyên Quang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3140, 'Nông Tiến', 33, NULL, NULL, NULL, 'Tuyên Quang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3141, 'Pà Vầy Sủ', 33, NULL, NULL, NULL, 'Tuyên Quang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3142, 'Phố Bảng', 33, NULL, NULL, NULL, 'Tuyên Quang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3143, 'Phú Linh', 33, NULL, NULL, NULL, 'Tuyên Quang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3144, 'Phú Lương', 33, NULL, NULL, NULL, 'Tuyên Quang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3145, 'Phù Lưu', 33, NULL, NULL, NULL, 'Tuyên Quang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3146, 'Pờ Ly Ngài', 33, NULL, NULL, NULL, 'Tuyên Quang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3147, 'Quản Bạ', 33, NULL, NULL, NULL, 'Tuyên Quang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3148, 'Quang Bình', 33, NULL, NULL, NULL, 'Tuyên Quang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3149, 'Quảng Nguyên', 33, NULL, NULL, NULL, 'Tuyên Quang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3150, 'Sà Phìn', 33, NULL, NULL, NULL, 'Tuyên Quang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3151, 'Sơn Dương', 33, NULL, NULL, NULL, 'Tuyên Quang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3152, 'Sơn Thủy', 33, NULL, NULL, NULL, 'Tuyên Quang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3153, 'Sơn Vĩ', 33, NULL, NULL, NULL, 'Tuyên Quang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3154, 'Sủng Máng', 33, NULL, NULL, NULL, 'Tuyên Quang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3155, 'Tát Ngà', 33, NULL, NULL, NULL, 'Tuyên Quang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3156, 'Tân An', 33, NULL, NULL, NULL, 'Tuyên Quang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3157, 'Tân Long', 33, NULL, NULL, NULL, 'Tuyên Quang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3158, 'Tân Mỹ', 33, NULL, NULL, NULL, 'Tuyên Quang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3159, 'Tân Quang', 33, NULL, NULL, NULL, 'Tuyên Quang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3160, 'Tân Thanh', 33, NULL, NULL, NULL, 'Tuyên Quang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3161, 'Tân Tiến', 33, NULL, NULL, NULL, 'Tuyên Quang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3162, 'Tân Trào', 33, NULL, NULL, NULL, 'Tuyên Quang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3163, 'Tân Trịnh', 33, NULL, NULL, NULL, 'Tuyên Quang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3164, 'Thái Bình', 33, NULL, NULL, NULL, 'Tuyên Quang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3165, 'Thái Hòa', 33, NULL, NULL, NULL, 'Tuyên Quang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3166, 'Thái Sơn', 33, NULL, NULL, NULL, 'Tuyên Quang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3167, 'Thàng Tín', 33, NULL, NULL, NULL, 'Tuyên Quang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3168, 'Thanh Thủy', 33, NULL, NULL, NULL, 'Tuyên Quang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3169, 'Thắng Mố', 33, NULL, NULL, NULL, 'Tuyên Quang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3170, 'Thông Nguyên', 33, NULL, NULL, NULL, 'Tuyên Quang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3171, 'Thuận Hòa', 33, NULL, NULL, NULL, 'Tuyên Quang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3172, 'Thượng Lâm', 33, NULL, NULL, NULL, 'Tuyên Quang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3173, 'Thượng Nông', 33, NULL, NULL, NULL, 'Tuyên Quang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3174, 'Thượng Sơn', 33, NULL, NULL, NULL, 'Tuyên Quang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3175, 'Tiên Nguyên', 33, NULL, NULL, NULL, 'Tuyên Quang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3176, 'Tiên Yên', 33, NULL, NULL, NULL, 'Tuyên Quang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3177, 'Tri Phú', 33, NULL, NULL, NULL, 'Tuyên Quang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3178, 'Trung Hà', 33, NULL, NULL, NULL, 'Tuyên Quang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3179, 'Trung Sơn', 33, NULL, NULL, NULL, 'Tuyên Quang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3180, 'Trung Thịnh', 33, NULL, NULL, NULL, 'Tuyên Quang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3181, 'Trường Sinh', 33, NULL, NULL, NULL, 'Tuyên Quang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3182, 'Tùng Bá', 33, NULL, NULL, NULL, 'Tuyên Quang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3183, 'Tùng Vài', 33, NULL, NULL, NULL, 'Tuyên Quang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3184, 'Vị Xuyên', 33, NULL, NULL, NULL, 'Tuyên Quang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3185, 'Việt Lâm', 33, NULL, NULL, NULL, 'Tuyên Quang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3186, 'Vĩnh Tuy', 33, NULL, NULL, NULL, 'Tuyên Quang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3187, 'Xín Mần', 33, NULL, NULL, NULL, 'Tuyên Quang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3188, 'Xuân Giang', 33, NULL, NULL, NULL, 'Tuyên Quang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3189, 'Xuân Vân', 33, NULL, NULL, NULL, 'Tuyên Quang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3190, 'Yên Cường', 33, NULL, NULL, NULL, 'Tuyên Quang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3191, 'Yên Hoa', 33, NULL, NULL, NULL, 'Tuyên Quang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3192, 'Yên Lập', 33, NULL, NULL, NULL, 'Tuyên Quang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3193, 'Yên Minh', 33, NULL, NULL, NULL, 'Tuyên Quang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3194, 'Yên Nguyên', 33, NULL, NULL, NULL, 'Tuyên Quang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3195, 'Yên Phú', 33, NULL, NULL, NULL, 'Tuyên Quang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3196, 'Yên Sơn', 33, NULL, NULL, NULL, 'Tuyên Quang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3197, 'Yên Thành', 33, NULL, NULL, NULL, 'Tuyên Quang');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3198, 'An Bình', 34, NULL, NULL, NULL, 'Vĩnh Long');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3199, 'An Định', 34, NULL, NULL, NULL, 'Vĩnh Long');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3200, 'An Hiệp', 34, NULL, NULL, NULL, 'Vĩnh Long');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3201, 'An Hội', 34, NULL, NULL, NULL, 'Vĩnh Long');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3202, 'An Ngãi Trung', 34, NULL, NULL, NULL, 'Vĩnh Long');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3203, 'An Phú Tân', 34, NULL, NULL, NULL, 'Vĩnh Long');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3204, 'An Qui', 34, NULL, NULL, NULL, 'Vĩnh Long');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3205, 'An Trường', 34, NULL, NULL, NULL, 'Vĩnh Long');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3206, 'Ba Tri', 34, NULL, NULL, NULL, 'Vĩnh Long');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3207, 'Bảo Thạnh', 34, NULL, NULL, NULL, 'Vĩnh Long');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3208, 'Bến Tre', 34, NULL, NULL, NULL, 'Vĩnh Long');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3209, 'Bình Đại', 34, NULL, NULL, NULL, 'Vĩnh Long');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3210, 'Bình Minh', 34, NULL, NULL, NULL, 'Vĩnh Long');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3211, 'Bình Phú', 34, NULL, NULL, NULL, 'Vĩnh Long');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3212, 'Bình Phước', 34, NULL, NULL, NULL, 'Vĩnh Long');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3213, 'Cái Ngang', 34, NULL, NULL, NULL, 'Vĩnh Long');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3214, 'Cái Nhum', 34, NULL, NULL, NULL, 'Vĩnh Long');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3215, 'Cái Vồn', 34, NULL, NULL, NULL, 'Vĩnh Long');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3216, 'Càng Long', 34, NULL, NULL, NULL, 'Vĩnh Long');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3217, 'Cầu Kè', 34, NULL, NULL, NULL, 'Vĩnh Long');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3218, 'Cầu Ngang', 34, NULL, NULL, NULL, 'Vĩnh Long');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3219, 'Châu Hòa', 34, NULL, NULL, NULL, 'Vĩnh Long');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3220, 'Châu Hưng', 34, NULL, NULL, NULL, 'Vĩnh Long');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3221, 'Châu Thành', 34, NULL, NULL, NULL, 'Vĩnh Long');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3222, 'Chợ Lách', 34, NULL, NULL, NULL, 'Vĩnh Long');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3223, 'Duyên Hải', 34, NULL, NULL, NULL, 'Vĩnh Long');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3224, 'Đại An', 34, NULL, NULL, NULL, 'Vĩnh Long');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3225, 'Đại Điền', 34, NULL, NULL, NULL, 'Vĩnh Long');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3226, 'Đôn Châu', 34, NULL, NULL, NULL, 'Vĩnh Long');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3227, 'Đông Hải', 34, NULL, NULL, NULL, 'Vĩnh Long');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3228, 'Đồng Khởi', 34, NULL, NULL, NULL, 'Vĩnh Long');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3229, 'Đông Thành', 34, NULL, NULL, NULL, 'Vĩnh Long');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3230, 'Giao Long', 34, NULL, NULL, NULL, 'Vĩnh Long');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3231, 'Giồng Trôm', 34, NULL, NULL, NULL, 'Vĩnh Long');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3232, 'Hàm Giang', 34, NULL, NULL, NULL, 'Vĩnh Long');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3233, 'Hiệp Mỹ', 34, NULL, NULL, NULL, 'Vĩnh Long');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3234, 'Hiếu Phụng', 34, NULL, NULL, NULL, 'Vĩnh Long');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3235, 'Hiếu Thành', 34, NULL, NULL, NULL, 'Vĩnh Long');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3236, 'Hòa Bình', 34, NULL, NULL, NULL, 'Vĩnh Long');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3237, 'Hòa Hiệp', 34, NULL, NULL, NULL, 'Vĩnh Long');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3238, 'Hòa Minh', 34, NULL, NULL, NULL, 'Vĩnh Long');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3239, 'Hoà Thuận', 34, NULL, NULL, NULL, 'Vĩnh Long');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3240, 'Hùng Hoà', 34, NULL, NULL, NULL, 'Vĩnh Long');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3241, 'Hưng Khánh Trung', 34, NULL, NULL, NULL, 'Vĩnh Long');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3242, 'Hưng Mỹ', 34, NULL, NULL, NULL, 'Vĩnh Long');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3243, 'Hưng Nhượng', 34, NULL, NULL, NULL, 'Vĩnh Long');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3244, 'Hương Mỹ', 34, NULL, NULL, NULL, 'Vĩnh Long');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3245, 'Long Châu', 34, NULL, NULL, NULL, 'Vĩnh Long');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3246, 'Long Đức', 34, NULL, NULL, NULL, 'Vĩnh Long');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3247, 'Long Hiệp', 34, NULL, NULL, NULL, 'Vĩnh Long');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3248, 'Long Hòa', 34, NULL, NULL, NULL, 'Vĩnh Long');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3249, 'Long Hồ', 34, NULL, NULL, NULL, 'Vĩnh Long');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3250, 'Long Hữu', 34, NULL, NULL, NULL, 'Vĩnh Long');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3251, 'Long Thành', 34, NULL, NULL, NULL, 'Vĩnh Long');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3252, 'Long Vĩnh', 34, NULL, NULL, NULL, 'Vĩnh Long');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3253, 'Lộc Thuận', 34, NULL, NULL, NULL, 'Vĩnh Long');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3254, 'Lục Sĩ Thành', 34, NULL, NULL, NULL, 'Vĩnh Long');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3255, 'Lương Hòa', 34, NULL, NULL, NULL, 'Vĩnh Long');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3256, 'Lương Phú', 34, NULL, NULL, NULL, 'Vĩnh Long');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3257, 'Lưu Nghiệp Anh', 34, NULL, NULL, NULL, 'Vĩnh Long');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3258, 'Mỏ Cày', 34, NULL, NULL, NULL, 'Vĩnh Long');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3259, 'Mỹ Chánh Hòa', 34, NULL, NULL, NULL, 'Vĩnh Long');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3260, 'Mỹ Long', 34, NULL, NULL, NULL, 'Vĩnh Long');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3261, 'Mỹ Thuận', 34, NULL, NULL, NULL, 'Vĩnh Long');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3262, 'Ngãi Tứ', 34, NULL, NULL, NULL, 'Vĩnh Long');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3263, 'Ngũ Lạc', 34, NULL, NULL, NULL, 'Vĩnh Long');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3264, 'Nguyệt Hoá', 34, NULL, NULL, NULL, 'Vĩnh Long');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3265, 'Nhị Long', 34, NULL, NULL, NULL, 'Vĩnh Long');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3266, 'Nhị Trường', 34, NULL, NULL, NULL, 'Vĩnh Long');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3267, 'Nhơn Phú', 34, NULL, NULL, NULL, 'Vĩnh Long');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3268, 'Nhuận Phú Tân', 34, NULL, NULL, NULL, 'Vĩnh Long');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3269, 'Phong Thạnh', 34, NULL, NULL, NULL, 'Vĩnh Long');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3270, 'Phú Khương', 34, NULL, NULL, NULL, 'Vĩnh Long');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3271, 'Phú Phụng', 34, NULL, NULL, NULL, 'Vĩnh Long');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3272, 'Phú Quới', 34, NULL, NULL, NULL, 'Vĩnh Long');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3273, 'Phú Tân', 34, NULL, NULL, NULL, 'Vĩnh Long');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3274, 'Phú Thuận', 34, NULL, NULL, NULL, 'Vĩnh Long');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3275, 'Phú Túc', 34, NULL, NULL, NULL, 'Vĩnh Long');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3276, 'Phước Hậu', 34, NULL, NULL, NULL, 'Vĩnh Long');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3277, 'Phước Long', 34, NULL, NULL, NULL, 'Vĩnh Long');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3278, 'Phước Mỹ Trung', 34, NULL, NULL, NULL, 'Vĩnh Long');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3279, 'Quới An', 34, NULL, NULL, NULL, 'Vĩnh Long');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3280, 'Quới Điền', 34, NULL, NULL, NULL, 'Vĩnh Long');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3281, 'Quới Thiện', 34, NULL, NULL, NULL, 'Vĩnh Long');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3282, 'Song Lộc', 34, NULL, NULL, NULL, 'Vĩnh Long');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3283, 'Song Phú', 34, NULL, NULL, NULL, 'Vĩnh Long');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3284, 'Sơn Đông', 34, NULL, NULL, NULL, 'Vĩnh Long');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3285, 'Tam Bình', 34, NULL, NULL, NULL, 'Vĩnh Long');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3286, 'Tam Ngãi', 34, NULL, NULL, NULL, 'Vĩnh Long');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3287, 'Tân An', 34, NULL, NULL, NULL, 'Vĩnh Long');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3288, 'Tân Hạnh', 34, NULL, NULL, NULL, 'Vĩnh Long');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3289, 'Tân Hào', 34, NULL, NULL, NULL, 'Vĩnh Long');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3290, 'Tân Hoà', 34, NULL, NULL, NULL, 'Vĩnh Long');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3291, 'Tân Long Hội', 34, NULL, NULL, NULL, 'Vĩnh Long');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3292, 'Tân Lược', 34, NULL, NULL, NULL, 'Vĩnh Long');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3293, 'Tân Ngãi', 34, NULL, NULL, NULL, 'Vĩnh Long');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3294, 'Tân Phú', 34, NULL, NULL, NULL, 'Vĩnh Long');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3295, 'Tân Quới', 34, NULL, NULL, NULL, 'Vĩnh Long');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3296, 'Tân Thành Bình', 34, NULL, NULL, NULL, 'Vĩnh Long');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3297, 'Tân Thủy', 34, NULL, NULL, NULL, 'Vĩnh Long');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3298, 'Tân Xuân', 34, NULL, NULL, NULL, 'Vĩnh Long');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3299, 'Tập Ngãi', 34, NULL, NULL, NULL, 'Vĩnh Long');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3300, 'Tập Sơn', 34, NULL, NULL, NULL, 'Vĩnh Long');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3301, 'Thanh Đức', 34, NULL, NULL, NULL, 'Vĩnh Long');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3302, 'Thạnh Hải', 34, NULL, NULL, NULL, 'Vĩnh Long');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3303, 'Thạnh Phong', 34, NULL, NULL, NULL, 'Vĩnh Long');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3304, 'Thạnh Phú', 34, NULL, NULL, NULL, 'Vĩnh Long');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3305, 'Thạnh Phước', 34, NULL, NULL, NULL, 'Vĩnh Long');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3306, 'Thành Thới', 34, NULL, NULL, NULL, 'Vĩnh Long');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3307, 'Thạnh Trị', 34, NULL, NULL, NULL, 'Vĩnh Long');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3308, 'Thới Thuận', 34, NULL, NULL, NULL, 'Vĩnh Long');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3309, 'Tiên Thủy', 34, NULL, NULL, NULL, 'Vĩnh Long');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3310, 'Tiểu Cần', 34, NULL, NULL, NULL, 'Vĩnh Long');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3311, 'Trà Côn', 34, NULL, NULL, NULL, 'Vĩnh Long');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3312, 'Trà Cú', 34, NULL, NULL, NULL, 'Vĩnh Long');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3313, 'Trà Ôn', 34, NULL, NULL, NULL, 'Vĩnh Long');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3314, 'Trà Vinh', 34, NULL, NULL, NULL, 'Vĩnh Long');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3315, 'Trung Hiệp', 34, NULL, NULL, NULL, 'Vĩnh Long');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3316, 'Trung Ngãi', 34, NULL, NULL, NULL, 'Vĩnh Long');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3317, 'Trung Thành', 34, NULL, NULL, NULL, 'Vĩnh Long');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3318, 'Trường Long Hoà', 34, NULL, NULL, NULL, 'Vĩnh Long');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3319, 'Vinh Kim', 34, NULL, NULL, NULL, 'Vĩnh Long');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3320, 'Vĩnh Thành', 34, NULL, NULL, NULL, 'Vĩnh Long');
INSERT INTO `districts` (`id`, `name`, `province_id`, `created_at`, `updated_at`, `accounting_id`, `province_name`) VALUES (3321, 'Vĩnh Xuân', 34, NULL, NULL, NULL, 'Vĩnh Long');
COMMIT;

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
-- Records of email_queues
-- ----------------------------
BEGIN;
COMMIT;

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
-- Records of exchanges
-- ----------------------------
BEGIN;
COMMIT;

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
-- Records of jobs
-- ----------------------------
BEGIN;
INSERT INTO `jobs` (`id`, `title`, `status`) VALUES (1, 'Tài chính', 1);
INSERT INTO `jobs` (`id`, `title`, `status`) VALUES (2, 'Khách sạn ', 1);
INSERT INTO `jobs` (`id`, `title`, `status`) VALUES (3, 'Kinh doanh', 1);
INSERT INTO `jobs` (`id`, `title`, `status`) VALUES (4, 'Nhân sự', 1);
INSERT INTO `jobs` (`id`, `title`, `status`) VALUES (5, 'Chăm sóc khách hàng', 1);
INSERT INTO `jobs` (`id`, `title`, `status`) VALUES (6, 'Bán hàng', 1);
INSERT INTO `jobs` (`id`, `title`, `status`) VALUES (7, 'Giáo dục/Đào tạo', 1);
INSERT INTO `jobs` (`id`, `title`, `status`) VALUES (8, 'Báo chí - Truyền hình', 1);
INSERT INTO `jobs` (`id`, `title`, `status`) VALUES (9, 'Bất động sản', 1);
INSERT INTO `jobs` (`id`, `title`, `status`) VALUES (10, 'An ninh/Vệ sĩ/Bảo vệ', 1);
INSERT INTO `jobs` (`id`, `title`, `status`) VALUES (11, 'Kế toán/Kiểm toán', 1);
INSERT INTO `jobs` (`id`, `title`, `status`) VALUES (12, 'Biên dịch/Phiên dịch', 1);
INSERT INTO `jobs` (`id`, `title`, `status`) VALUES (13, 'Bưu chính viễn thông', 1);
INSERT INTO `jobs` (`id`, `title`, `status`) VALUES (14, 'Cơ khí', 1);
INSERT INTO `jobs` (`id`, `title`, `status`) VALUES (15, 'Dầu khí/Địa chất', 1);
INSERT INTO `jobs` (`id`, `title`, `status`) VALUES (16, 'Dệt may', 1);
INSERT INTO `jobs` (`id`, `title`, `status`) VALUES (17, 'Du lịch', 1);
INSERT INTO `jobs` (`id`, `title`, `status`) VALUES (18, 'Nhà hàng', 1);
INSERT INTO `jobs` (`id`, `title`, `status`) VALUES (19, 'Dược/Y tế', 1);
INSERT INTO `jobs` (`id`, `title`, `status`) VALUES (20, 'Hóa chất/Sinh hóa', 1);
INSERT INTO `jobs` (`id`, `title`, `status`) VALUES (21, 'Điện tử - Điện lạnh', 1);
INSERT INTO `jobs` (`id`, `title`, `status`) VALUES (22, 'Giải trí', 1);
INSERT INTO `jobs` (`id`, `title`, `status`) VALUES (23, 'Giao thông vận tải/Cầu đường', 1);
INSERT INTO `jobs` (`id`, `title`, `status`) VALUES (24, 'Thu mua', 1);
INSERT INTO `jobs` (`id`, `title`, `status`) VALUES (25, 'Khu công nghiệp', 1);
INSERT INTO `jobs` (`id`, `title`, `status`) VALUES (26, 'Spa - Mỹ phẩm - Trang sức', 1);
INSERT INTO `jobs` (`id`, `title`, `status`) VALUES (27, 'Thể dục thể thao', 1);
INSERT INTO `jobs` (`id`, `title`, `status`) VALUES (28, 'Lao động phổ thông', 1);
INSERT INTO `jobs` (`id`, `title`, `status`) VALUES (29, 'Luật pháp', 1);
INSERT INTO `jobs` (`id`, `title`, `status`) VALUES (30, 'Điện tử viễn thông', 1);
INSERT INTO `jobs` (`id`, `title`, `status`) VALUES (31, 'Hàng hải', 1);
INSERT INTO `jobs` (`id`, `title`, `status`) VALUES (32, 'Dệt may - Da giày', 1);
INSERT INTO `jobs` (`id`, `title`, `status`) VALUES (33, 'Công nghệ cao', 1);
INSERT INTO `jobs` (`id`, `title`, `status`) VALUES (34, 'Kỹ thuật ứng dụng', 1);
INSERT INTO `jobs` (`id`, `title`, `status`) VALUES (35, 'Tổ chức sự kiện', 1);
INSERT INTO `jobs` (`id`, `title`, `status`) VALUES (36, 'Thực phẩm', 1);
INSERT INTO `jobs` (`id`, `title`, `status`) VALUES (37, 'Vận tải', 1);
INSERT INTO `jobs` (`id`, `title`, `status`) VALUES (38, 'Môi trường/Xử lý chất thải', 1);
INSERT INTO `jobs` (`id`, `title`, `status`) VALUES (39, 'Ngân hàng', 1);
INSERT INTO `jobs` (`id`, `title`, `status`) VALUES (40, 'Chứng khoán/Đầu tư', 1);
INSERT INTO `jobs` (`id`, `title`, `status`) VALUES (41, 'Nghệ thuật', 1);
INSERT INTO `jobs` (`id`, `title`, `status`) VALUES (42, 'Điện ảnh', 1);
INSERT INTO `jobs` (`id`, `title`, `status`) VALUES (43, 'Ngoại ngữ', 1);
INSERT INTO `jobs` (`id`, `title`, `status`) VALUES (44, 'Nông/Lâm/Ngư nghiệp', 1);
INSERT INTO `jobs` (`id`, `title`, `status`) VALUES (45, 'Phát triển thị trường', 1);
INSERT INTO `jobs` (`id`, `title`, `status`) VALUES (46, 'Giúp việc', 1);
INSERT INTO `jobs` (`id`, `title`, `status`) VALUES (47, 'Quan hệ đối ngoại', 1);
INSERT INTO `jobs` (`id`, `title`, `status`) VALUES (48, 'Quảng cáo/Marketing/PR', 1);
INSERT INTO `jobs` (`id`, `title`, `status`) VALUES (49, 'Sản xuất', 1);
INSERT INTO `jobs` (`id`, `title`, `status`) VALUES (50, 'Sinh viên/Thực tập sinh', 1);
INSERT INTO `jobs` (`id`, `title`, `status`) VALUES (51, 'Thẩm định/Giám định', 1);
INSERT INTO `jobs` (`id`, `title`, `status`) VALUES (52, 'Thiết kế/Mỹ thuật', 1);
INSERT INTO `jobs` (`id`, `title`, `status`) VALUES (53, 'Thời vụ/Bán thời gian', 1);
INSERT INTO `jobs` (`id`, `title`, `status`) VALUES (54, 'Đồ uống', 1);
INSERT INTO `jobs` (`id`, `title`, `status`) VALUES (55, 'Trang thiết bị công nghiệp', 1);
INSERT INTO `jobs` (`id`, `title`, `status`) VALUES (56, 'Tư vấn bảo hiểm', 1);
INSERT INTO `jobs` (`id`, `title`, `status`) VALUES (57, 'Xây dựng', 1);
INSERT INTO `jobs` (`id`, `title`, `status`) VALUES (58, 'Quản trị kinh doanh', 1);
INSERT INTO `jobs` (`id`, `title`, `status`) VALUES (59, 'Xuất - Nhập khẩu/Ngoại thương', 1);
INSERT INTO `jobs` (`id`, `title`, `status`) VALUES (60, 'Kiến trúc/Nội thất', 1);
INSERT INTO `jobs` (`id`, `title`, `status`) VALUES (61, 'Bảo hiểm', 1);
INSERT INTO `jobs` (`id`, `title`, `status`) VALUES (62, 'Hành chính/Thư ký/Trợ lý', 1);
INSERT INTO `jobs` (`id`, `title`, `status`) VALUES (63, 'Tài xế/Giao nhận', 1);
INSERT INTO `jobs` (`id`, `title`, `status`) VALUES (64, 'Đồ gia dụng', 1);
INSERT INTO `jobs` (`id`, `title`, `status`) VALUES (65, 'Tiếp thị quảng cáo', 1);
INSERT INTO `jobs` (`id`, `title`, `status`) VALUES (66, 'Thương mại điện tử', 1);
INSERT INTO `jobs` (`id`, `title`, `status`) VALUES (67, 'Hàng không', 1);
INSERT INTO `jobs` (`id`, `title`, `status`) VALUES (68, 'Thủ công mỹ nghệ', 1);
INSERT INTO `jobs` (`id`, `title`, `status`) VALUES (69, 'Hoạch định - Dự án', 1);
INSERT INTO `jobs` (`id`, `title`, `status`) VALUES (70, 'Kho vận - Vật tư', 1);
INSERT INTO `jobs` (`id`, `title`, `status`) VALUES (71, 'Khác', 1);
COMMIT;

-- ----------------------------
-- Table structure for log_agreements
-- ----------------------------
DROP TABLE IF EXISTS `log_agreements`;
CREATE TABLE `log_agreements` (
  `id` int NOT NULL AUTO_INCREMENT,
  `agreement_id` int DEFAULT NULL,
  `student_id` int DEFAULT NULL,
  `branch_id` int DEFAULT NULL,
  `ec_id` int DEFAULT NULL,
  `ec_leader_id` int DEFAULT NULL,
  `code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `product_id` int DEFAULT NULL,
  `type_fee` tinyint DEFAULT NULL,
  `tuition_fee_id` int DEFAULT NULL,
  `must_charge` int DEFAULT NULL,
  `total_charged` int DEFAULT NULL,
  `debt_amount` int DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `note` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `status` tinyint DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `creator_id` int DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updator_id` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of log_agreements
-- ----------------------------
BEGIN;
INSERT INTO `log_agreements` (`id`, `agreement_id`, `student_id`, `branch_id`, `ec_id`, `ec_leader_id`, `code`, `product_id`, `type_fee`, `tuition_fee_id`, `must_charge`, `total_charged`, `debt_amount`, `start_date`, `note`, `status`, `created_at`, `creator_id`, `updated_at`, `updator_id`) VALUES (16, 7, 541, 9, 1, NULL, '000007', NULL, 2, 43, 5936000, 0, 5936000, '2026-01-01', NULL, 1, '2026-01-14 22:15:22', 1, NULL, NULL);
INSERT INTO `log_agreements` (`id`, `agreement_id`, `student_id`, `branch_id`, `ec_id`, `ec_leader_id`, `code`, `product_id`, `type_fee`, `tuition_fee_id`, `must_charge`, `total_charged`, `debt_amount`, `start_date`, `note`, `status`, `created_at`, `creator_id`, `updated_at`, `updator_id`) VALUES (17, 7, 541, 9, 1, NULL, '000007', NULL, 2, 43, 5936000, 5936000, 0, '2026-01-01', NULL, 3, '2026-01-14 22:15:22', 1, '2026-01-14 22:17:18', 1);
COMMIT;

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
  `init_total_charged` int DEFAULT NULL,
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
  `b2b_campaign_id` int DEFAULT NULL,
  `b2b_amount` int DEFAULT NULL,
  `b2b_bonus_session` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `agreement_id` int DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `ec_id` (`ec_id`) USING BTREE,
  KEY `tuition_fee_id` (`tuition_fee_id`) USING BTREE,
  KEY `creator_id` (`creator_id`) USING BTREE,
  KEY `cm_id` (`cm_id`) USING BTREE,
  KEY `student_id` (`student_id`) USING BTREE,
  KEY `branch_id` (`branch_id`) USING BTREE,
  KEY `ceo_branch_id` (`ceo_branch_id`) USING BTREE,
  KEY `contract_id` (`contract_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=44644 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of log_contracts
-- ----------------------------
BEGIN;
INSERT INTO `log_contracts` (`id`, `contract_id`, `type`, `code`, `student_id`, `reference_contract_id`, `transfer_to_contract_id`, `branch_id`, `ceo_branch_id`, `ec_id`, `ec_leader_id`, `cm_id`, `cm_leader_id`, `product_id`, `program_id`, `tuition_fee_id`, `init_tuition_fee_id`, `init_tuition_fee_amount`, `init_tuition_fee_receivable`, `init_tuition_fee_session`, `init_total_charged`, `must_charge`, `total_charged`, `debt_amount`, `total_discount`, `discount_code`, `discount_code_id`, `discount_code_amount`, `discount_code_percent`, `coupon_code`, `coupon_amount`, `coupon_session`, `total_sessions`, `real_sessions`, `bonus_sessions`, `summary_sessions`, `reserved_sessions`, `reservable_sessions`, `done_sessions`, `left_sessions`, `last_done_sessions`, `description`, `bill_info`, `start_date`, `end_date`, `class_id`, `enrolment_start_date`, `enrolment_last_date`, `status`, `created_at`, `creator_id`, `updated_at`, `updator_id`, `note`, `action`, `count_recharge`, `type_withdraw`, `log_time`, `completed_date`, `renewed_date`, `success_renewed_date`, `renew_runtime`, `b2b_campaign_id`, `b2b_amount`, `b2b_bonus_session`, `agreement_id`) VALUES (44640, 563, 1, 'C000563', 541, NULL, NULL, 9, NULL, 1, NULL, NULL, NULL, 25, NULL, 38, 38, 2218000, NULL, 14, 0, 2218000, 0, 2218000, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, 14, 14, 0, 0, 0, 0, 0, 0, 0, NULL, NULL, '2026-01-01', NULL, NULL, NULL, NULL, 1, '2026-01-14 22:15:22', 1, NULL, NULL, NULL, NULL, 1, NULL, '2026-01-14 22:15:22', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 7);
INSERT INTO `log_contracts` (`id`, `contract_id`, `type`, `code`, `student_id`, `reference_contract_id`, `transfer_to_contract_id`, `branch_id`, `ceo_branch_id`, `ec_id`, `ec_leader_id`, `cm_id`, `cm_leader_id`, `product_id`, `program_id`, `tuition_fee_id`, `init_tuition_fee_id`, `init_tuition_fee_amount`, `init_tuition_fee_receivable`, `init_tuition_fee_session`, `init_total_charged`, `must_charge`, `total_charged`, `debt_amount`, `total_discount`, `discount_code`, `discount_code_id`, `discount_code_amount`, `discount_code_percent`, `coupon_code`, `coupon_amount`, `coupon_session`, `total_sessions`, `real_sessions`, `bonus_sessions`, `summary_sessions`, `reserved_sessions`, `reservable_sessions`, `done_sessions`, `left_sessions`, `last_done_sessions`, `description`, `bill_info`, `start_date`, `end_date`, `class_id`, `enrolment_start_date`, `enrolment_last_date`, `status`, `created_at`, `creator_id`, `updated_at`, `updator_id`, `note`, `action`, `count_recharge`, `type_withdraw`, `log_time`, `completed_date`, `renewed_date`, `success_renewed_date`, `renew_runtime`, `b2b_campaign_id`, `b2b_amount`, `b2b_bonus_session`, `agreement_id`) VALUES (44641, 564, 1, 'C000564', 541, NULL, NULL, 9, NULL, 1, NULL, NULL, NULL, 26, NULL, 39, 39, 3718000, NULL, 25, 0, 3718000, 0, 3718000, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, 25, 25, 0, 0, 0, 0, 0, 0, 0, NULL, NULL, '2026-01-01', NULL, NULL, NULL, NULL, 1, '2026-01-14 22:15:22', 1, NULL, NULL, NULL, NULL, 2, NULL, '2026-01-14 22:15:22', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 7);
INSERT INTO `log_contracts` (`id`, `contract_id`, `type`, `code`, `student_id`, `reference_contract_id`, `transfer_to_contract_id`, `branch_id`, `ceo_branch_id`, `ec_id`, `ec_leader_id`, `cm_id`, `cm_leader_id`, `product_id`, `program_id`, `tuition_fee_id`, `init_tuition_fee_id`, `init_tuition_fee_amount`, `init_tuition_fee_receivable`, `init_tuition_fee_session`, `init_total_charged`, `must_charge`, `total_charged`, `debt_amount`, `total_discount`, `discount_code`, `discount_code_id`, `discount_code_amount`, `discount_code_percent`, `coupon_code`, `coupon_amount`, `coupon_session`, `total_sessions`, `real_sessions`, `bonus_sessions`, `summary_sessions`, `reserved_sessions`, `reservable_sessions`, `done_sessions`, `left_sessions`, `last_done_sessions`, `description`, `bill_info`, `start_date`, `end_date`, `class_id`, `enrolment_start_date`, `enrolment_last_date`, `status`, `created_at`, `creator_id`, `updated_at`, `updator_id`, `note`, `action`, `count_recharge`, `type_withdraw`, `log_time`, `completed_date`, `renewed_date`, `success_renewed_date`, `renew_runtime`, `b2b_campaign_id`, `b2b_amount`, `b2b_bonus_session`, `agreement_id`) VALUES (44642, 563, 1, 'C000563', 541, NULL, NULL, 9, NULL, 1, NULL, NULL, NULL, 25, NULL, 38, 38, 2218000, NULL, 14, 2218000, 2218000, 2218000, 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, 14, 14, 0, 14, 0, 0, 0, 14, 0, NULL, NULL, '2026-01-01', NULL, NULL, NULL, NULL, 3, '2026-01-14 22:15:22', 1, '2026-01-14 22:28:45', 0, NULL, NULL, 1, NULL, '2026-01-14 22:28:46', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 7);
INSERT INTO `log_contracts` (`id`, `contract_id`, `type`, `code`, `student_id`, `reference_contract_id`, `transfer_to_contract_id`, `branch_id`, `ceo_branch_id`, `ec_id`, `ec_leader_id`, `cm_id`, `cm_leader_id`, `product_id`, `program_id`, `tuition_fee_id`, `init_tuition_fee_id`, `init_tuition_fee_amount`, `init_tuition_fee_receivable`, `init_tuition_fee_session`, `init_total_charged`, `must_charge`, `total_charged`, `debt_amount`, `total_discount`, `discount_code`, `discount_code_id`, `discount_code_amount`, `discount_code_percent`, `coupon_code`, `coupon_amount`, `coupon_session`, `total_sessions`, `real_sessions`, `bonus_sessions`, `summary_sessions`, `reserved_sessions`, `reservable_sessions`, `done_sessions`, `left_sessions`, `last_done_sessions`, `description`, `bill_info`, `start_date`, `end_date`, `class_id`, `enrolment_start_date`, `enrolment_last_date`, `status`, `created_at`, `creator_id`, `updated_at`, `updator_id`, `note`, `action`, `count_recharge`, `type_withdraw`, `log_time`, `completed_date`, `renewed_date`, `success_renewed_date`, `renew_runtime`, `b2b_campaign_id`, `b2b_amount`, `b2b_bonus_session`, `agreement_id`) VALUES (44643, 564, 1, 'C000564', 541, NULL, NULL, 9, NULL, 1, NULL, NULL, NULL, 26, NULL, 39, 39, 3718000, NULL, 25, 3718000, 3718000, 3718000, 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, 25, 25, 0, 25, 0, 0, 0, 25, 0, NULL, NULL, '2026-01-01', NULL, NULL, NULL, NULL, 3, '2026-01-14 22:15:22', 1, '2026-01-14 22:28:46', 0, NULL, NULL, 2, NULL, '2026-01-14 22:28:46', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 7);
COMMIT;

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
) ENGINE=InnoDB AUTO_INCREMENT=2277 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of log_jobs
-- ----------------------------
BEGIN;
INSERT INTO `log_jobs` (`id`, `action`, `data`, `created_at`) VALUES (2276, 'ProcessDataScheduleHasStudent', '2026-01-14', '2026-01-15 00:05:02');
COMMIT;

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
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- ----------------------------
-- Records of make_up_sessions
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for payments
-- ----------------------------
DROP TABLE IF EXISTS `payments`;
CREATE TABLE `payments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `agreement_id` int DEFAULT NULL,
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
  `type` tinyint(1) DEFAULT NULL COMMENT '1- nhập học, 2-bảo lưu',
  `student_id` int DEFAULT NULL,
  `note` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `branch_id` int DEFAULT NULL,
  `ec_id` int DEFAULT NULL,
  `cm_id` int DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `contract_id` (`contract_id`) USING BTREE,
  KEY `creator_id` (`creator_id`) USING BTREE,
  KEY `student_id` (`student_id`) USING BTREE,
  KEY `branch_id` (`branch_id`) USING BTREE,
  KEY `cm_id` (`cm_id`) USING BTREE,
  KEY `ec_id` (`ec_id`) USING BTREE,
  KEY `agreement_id` (`agreement_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=37149 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of payments
-- ----------------------------
BEGIN;
INSERT INTO `payments` (`id`, `agreement_id`, `contract_id`, `accounting_id`, `method`, `payload`, `must_charge`, `amount`, `total`, `debt`, `charge_date`, `created_at`, `creator_id`, `hash_key`, `count`, `type`, `student_id`, `note`, `branch_id`, `ec_id`, `cm_id`) VALUES (37148, 7, NULL, NULL, 1, 0, 5936000, 5936000, 5936000, 0, '2026-01-01', '2026-01-14 22:17:18', 1, NULL, 0, 1, 541, NULL, 9, 1, NULL);
COMMIT;

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
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- ----------------------------
-- Records of permission_groups
-- ----------------------------
BEGIN;
INSERT INTO `permission_groups` (`id`, `name`, `description`, `status`, `display_order`) VALUES (1, 'CRM', 'CRM', 1, 3);
INSERT INTO `permission_groups` (`id`, `name`, `description`, `status`, `display_order`) VALUES (2, 'LMS', 'LMS', 1, 2);
INSERT INTO `permission_groups` (`id`, `name`, `description`, `status`, `display_order`) VALUES (3, 'Config', 'Cấu hình hệ thống', 1, 1);
INSERT INTO `permission_groups` (`id`, `name`, `description`, `status`, `display_order`) VALUES (4, 'Orther', 'Khác', 1, 7);
INSERT INTO `permission_groups` (`id`, `name`, `description`, `status`, `display_order`) VALUES (5, 'Marketing', 'Marketing', 1, 4);
INSERT INTO `permission_groups` (`id`, `name`, `description`, `status`, `display_order`) VALUES (6, 'Dashboard', 'Dashboard', 1, 6);
INSERT INTO `permission_groups` (`id`, `name`, `description`, `status`, `display_order`) VALUES (7, 'Reports', 'Báo cáo', 1, 5);
INSERT INTO `permission_groups` (`id`, `name`, `description`, `status`, `display_order`) VALUES (8, 'Approve', 'Phê duyệt', 1, 8);
COMMIT;

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
-- Records of permission_has_role
-- ----------------------------
BEGIN;
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (36, 3);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (36, 6);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (36, 11);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (36, 12);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (36, 24);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (36, 28);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (55, 3);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (55, 4);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (55, 5);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (55, 6);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (55, 7);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (55, 8);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (55, 9);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (55, 10);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (55, 11);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (55, 12);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (55, 17);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (55, 21);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (55, 24);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (55, 26);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (55, 27);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (55, 28);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (55, 29);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (55, 30);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (55, 80);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (56, 3);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (56, 4);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (56, 5);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (56, 6);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (56, 7);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (56, 8);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (56, 9);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (56, 10);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (56, 11);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (56, 12);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (56, 15);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (56, 17);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (56, 18);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (56, 19);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (56, 24);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (56, 26);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (56, 27);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (56, 28);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (56, 35);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (56, 43);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (56, 49);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (56, 50);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (56, 51);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (56, 53);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (56, 54);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (56, 55);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (56, 60);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (56, 67);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (56, 68);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (56, 69);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (56, 70);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (56, 71);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (56, 72);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (56, 77);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (56, 78);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (56, 79);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (56, 80);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (68, 1);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (68, 2);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (68, 3);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (68, 4);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (68, 5);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (68, 24);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (68, 28);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (69, 1);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (69, 2);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (69, 3);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (69, 4);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (69, 24);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (69, 28);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (69, 41);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (69, 42);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (69, 43);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (69, 44);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (69, 45);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (69, 67);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (69, 68);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (69, 69);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (69, 70);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (69, 71);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (69, 72);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (69, 73);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (69, 74);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (69, 75);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (69, 76);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (69, 77);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (69, 78);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (69, 79);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (686868, 1);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (686868, 2);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (686868, 3);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (686868, 4);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (686868, 5);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (686868, 6);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (686868, 7);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (686868, 8);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (686868, 9);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (686868, 10);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (686868, 11);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (686868, 12);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (686868, 24);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (686868, 26);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (686868, 27);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (686868, 28);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (686868, 41);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (686868, 42);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (686868, 43);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (686868, 44);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (686868, 45);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (686868, 46);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (686868, 47);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (686868, 48);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (686868, 49);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (686868, 50);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (686868, 51);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (686868, 52);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (686868, 53);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (686868, 54);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (686868, 55);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (686868, 56);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (686868, 57);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (686868, 58);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (686868, 59);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (686868, 60);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (686868, 61);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (686868, 62);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (686868, 63);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (686868, 64);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (686868, 65);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (686868, 66);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (686868, 67);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (686868, 68);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (686868, 69);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (686868, 70);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (686868, 71);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (686868, 72);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (686868, 73);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (686868, 74);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (686868, 75);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (686868, 76);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (686868, 77);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (686868, 78);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (686868, 79);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (999999, 1);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (999999, 2);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (999999, 3);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (999999, 4);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (999999, 5);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (999999, 6);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (999999, 7);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (999999, 8);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (999999, 9);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (999999, 10);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (999999, 11);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (999999, 12);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (999999, 13);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (999999, 14);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (999999, 15);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (999999, 16);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (999999, 17);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (999999, 18);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (999999, 19);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (999999, 20);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (999999, 21);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (999999, 22);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (999999, 23);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (999999, 24);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (999999, 26);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (999999, 27);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (999999, 28);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (999999, 29);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (999999, 30);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (999999, 31);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (999999, 32);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (999999, 33);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (999999, 34);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (999999, 35);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (999999, 36);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (999999, 37);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (999999, 38);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (999999, 39);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (999999, 40);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (999999, 41);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (999999, 42);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (999999, 43);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (999999, 44);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (999999, 45);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (999999, 46);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (999999, 47);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (999999, 48);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (999999, 49);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (999999, 50);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (999999, 51);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (999999, 52);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (999999, 53);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (999999, 54);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (999999, 55);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (999999, 56);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (999999, 57);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (999999, 58);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (999999, 59);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (999999, 60);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (999999, 61);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (999999, 62);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (999999, 63);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (999999, 64);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (999999, 65);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (999999, 66);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (999999, 67);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (999999, 68);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (999999, 69);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (999999, 70);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (999999, 71);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (999999, 72);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (999999, 73);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (999999, 74);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (999999, 75);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (999999, 76);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (999999, 77);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (999999, 78);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (999999, 79);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (999999, 80);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (999999, 81);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (1000000, 1);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (1000000, 2);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (1000000, 3);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (1000000, 4);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (1000000, 5);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (1000000, 6);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (1000000, 7);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (1000000, 8);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (1000000, 9);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (1000000, 10);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (1000000, 11);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (1000000, 12);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (1000000, 24);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (1000000, 26);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (1000000, 27);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (1000000, 28);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (1000000, 31);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (1000000, 32);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (1000000, 33);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (1000000, 34);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (1000000, 35);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (1000000, 36);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (1000000, 37);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (1000000, 38);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (1000000, 39);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (1000000, 40);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (1000000, 41);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (1000000, 42);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (1000000, 43);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (1000000, 44);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (1000000, 45);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (1000000, 46);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (1000000, 47);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (1000000, 48);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (1000000, 49);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (1000000, 50);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (1000000, 51);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (1000000, 52);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (1000000, 53);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (1000000, 54);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (1000000, 55);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (1000000, 56);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (1000000, 57);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (1000000, 58);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (1000000, 59);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (1000000, 60);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (1000000, 61);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (1000000, 62);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (1000000, 63);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (1000000, 64);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (1000000, 65);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (1000000, 66);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (1000000, 67);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (1000000, 68);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (1000000, 69);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (1000000, 70);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (1000000, 71);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (1000000, 72);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (1000000, 73);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (1000000, 74);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (1000000, 75);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (1000000, 76);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (1000000, 77);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (1000000, 78);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (1000000, 79);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (1000000, 80);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (1000000, 81);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (1000001, 1);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (1000001, 2);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (1000001, 3);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (1000001, 4);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (1000001, 5);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (1000001, 6);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (1000001, 7);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (1000001, 8);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (1000001, 9);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (1000001, 10);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (1000001, 11);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (1000001, 12);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (1000001, 24);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (1000001, 26);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (1000001, 27);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (1000001, 28);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (1000001, 31);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (1000001, 32);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (1000001, 33);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (1000001, 34);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (1000001, 35);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (1000001, 36);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (1000001, 37);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (1000001, 38);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (1000001, 39);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (1000001, 40);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (1000001, 41);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (1000001, 42);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (1000001, 43);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (1000001, 44);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (1000001, 45);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (1000001, 46);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (1000001, 47);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (1000001, 48);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (1000001, 49);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (1000001, 50);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (1000001, 51);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (1000001, 52);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (1000001, 53);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (1000001, 54);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (1000001, 55);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (1000001, 56);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (1000001, 57);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (1000001, 58);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (1000001, 59);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (1000001, 60);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (1000001, 61);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (1000001, 62);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (1000001, 63);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (1000001, 64);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (1000001, 65);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (1000001, 66);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (1000001, 67);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (1000001, 68);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (1000001, 69);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (1000001, 70);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (1000001, 71);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (1000001, 72);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (1000001, 73);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (1000001, 74);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (1000001, 75);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (1000001, 76);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (1000001, 77);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (1000001, 78);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (1000001, 79);
INSERT INTO `permission_has_role` (`role_id`, `permission_id`) VALUES (1000001, 80);
COMMIT;

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
  `parent_id` int DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=82 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- ----------------------------
-- Records of permissions
-- ----------------------------
BEGIN;
INSERT INTO `permissions` (`id`, `name`, `description`, `group_id`, `display_order`, `status`, `parent_id`) VALUES (1, 'crm_parent', 'Khách hàng', 1, 1, 1, NULL);
INSERT INTO `permissions` (`id`, `name`, `description`, `group_id`, `display_order`, `status`, `parent_id`) VALUES (2, 'crm_imports', 'Import', 1, 2, 1, NULL);
INSERT INTO `permissions` (`id`, `name`, `description`, `group_id`, `display_order`, `status`, `parent_id`) VALUES (3, 'lms_students', 'DS học sinh', 2, 1, 1, NULL);
INSERT INTO `permissions` (`id`, `name`, `description`, `group_id`, `display_order`, `status`, `parent_id`) VALUES (4, 'lms_checkin', 'DS checkin', 2, 2, 1, NULL);
INSERT INTO `permissions` (`id`, `name`, `description`, `group_id`, `display_order`, `status`, `parent_id`) VALUES (5, 'lms_agreements', 'Nhập học', 2, 3, 1, NULL);
INSERT INTO `permissions` (`id`, `name`, `description`, `group_id`, `display_order`, `status`, `parent_id`) VALUES (6, 'lms_enrolments', 'Xếp lớp', 2, 4, 1, NULL);
INSERT INTO `permissions` (`id`, `name`, `description`, `group_id`, `display_order`, `status`, `parent_id`) VALUES (7, 'lms_reserves', 'Bảo lưu', 2, 5, 1, NULL);
INSERT INTO `permissions` (`id`, `name`, `description`, `group_id`, `display_order`, `status`, `parent_id`) VALUES (8, 'lms_class_transfers', 'Chuyển lớp', 2, 6, 1, NULL);
INSERT INTO `permissions` (`id`, `name`, `description`, `group_id`, `display_order`, `status`, `parent_id`) VALUES (9, 'lms_branch_transfers', 'Chuyển trung tâm', 2, 7, 1, NULL);
INSERT INTO `permissions` (`id`, `name`, `description`, `group_id`, `display_order`, `status`, `parent_id`) VALUES (10, 'lms_tuition_transfers', 'Chuyển phí', 2, 8, 1, NULL);
INSERT INTO `permissions` (`id`, `name`, `description`, `group_id`, `display_order`, `status`, `parent_id`) VALUES (11, 'lms_attendances', 'Điểm danh', 2, 9, 1, NULL);
INSERT INTO `permissions` (`id`, `name`, `description`, `group_id`, `display_order`, `status`, `parent_id`) VALUES (12, 'lms_assessments', 'Nhận xét & đánh giá', 2, 10, 1, NULL);
INSERT INTO `permissions` (`id`, `name`, `description`, `group_id`, `display_order`, `status`, `parent_id`) VALUES (13, 'settings_branches', 'Trung tâm', 3, 1, 1, NULL);
INSERT INTO `permissions` (`id`, `name`, `description`, `group_id`, `display_order`, `status`, `parent_id`) VALUES (14, 'settings_products', 'Khóa học', 3, 2, 1, NULL);
INSERT INTO `permissions` (`id`, `name`, `description`, `group_id`, `display_order`, `status`, `parent_id`) VALUES (15, 'settings_programs', 'Chương trình học', 3, 3, 1, NULL);
INSERT INTO `permissions` (`id`, `name`, `description`, `group_id`, `display_order`, `status`, `parent_id`) VALUES (16, 'settings_subjects', 'Môn học', 3, 4, 1, NULL);
INSERT INTO `permissions` (`id`, `name`, `description`, `group_id`, `display_order`, `status`, `parent_id`) VALUES (17, 'settings_classes', 'Lớp học', 3, 5, 1, NULL);
INSERT INTO `permissions` (`id`, `name`, `description`, `group_id`, `display_order`, `status`, `parent_id`) VALUES (18, 'settings_rooms', 'Phòng học', 3, 6, 1, NULL);
INSERT INTO `permissions` (`id`, `name`, `description`, `group_id`, `display_order`, `status`, `parent_id`) VALUES (19, 'settings_shifts', 'Ca học', 3, 7, 1, NULL);
INSERT INTO `permissions` (`id`, `name`, `description`, `group_id`, `display_order`, `status`, `parent_id`) VALUES (20, 'settings_holidays', 'Ngày nghỉ lễ', 3, 8, 1, NULL);
INSERT INTO `permissions` (`id`, `name`, `description`, `group_id`, `display_order`, `status`, `parent_id`) VALUES (21, 'settings_tuition_fees', 'Gói phí', 3, 9, 1, NULL);
INSERT INTO `permissions` (`id`, `name`, `description`, `group_id`, `display_order`, `status`, `parent_id`) VALUES (22, 'settings_users', 'Người dùng', 3, 10, 1, NULL);
INSERT INTO `permissions` (`id`, `name`, `description`, `group_id`, `display_order`, `status`, `parent_id`) VALUES (23, 'settings_roles', 'Chức vụ, phân quyền', 3, 11, 1, NULL);
INSERT INTO `permissions` (`id`, `name`, `description`, `group_id`, `display_order`, `status`, `parent_id`) VALUES (24, 'dashboard', 'Dashboard', 4, 1, 1, NULL);
INSERT INTO `permissions` (`id`, `name`, `description`, `group_id`, `display_order`, `status`, `parent_id`) VALUES (26, 'lms_charges', 'Đã thu phí', 2, 11, 1, NULL);
INSERT INTO `permissions` (`id`, `name`, `description`, `group_id`, `display_order`, `status`, `parent_id`) VALUES (27, 'lms_waitcharges', 'Chờ thu phí', 2, 12, 1, NULL);
INSERT INTO `permissions` (`id`, `name`, `description`, `group_id`, `display_order`, `status`, `parent_id`) VALUES (28, 'lms_reports', 'Báo cáo', 2, 13, 1, NULL);
INSERT INTO `permissions` (`id`, `name`, `description`, `group_id`, `display_order`, `status`, `parent_id`) VALUES (29, 'settings_discount_codes', 'Mã chiết khấu', 3, 12, 1, NULL);
INSERT INTO `permissions` (`id`, `name`, `description`, `group_id`, `display_order`, `status`, `parent_id`) VALUES (30, 'settings_coupons', 'Voucher', 3, 13, 1, NULL);
INSERT INTO `permissions` (`id`, `name`, `description`, `group_id`, `display_order`, `status`, `parent_id`) VALUES (31, 'canViewAllSale', 'Xem tất cả CSKH', 4, 2, 1, NULL);
INSERT INTO `permissions` (`id`, `name`, `description`, `group_id`, `display_order`, `status`, `parent_id`) VALUES (32, 'canViewAllParents', 'Xem tất cả khách hàng', 4, 3, 1, NULL);
INSERT INTO `permissions` (`id`, `name`, `description`, `group_id`, `display_order`, `status`, `parent_id`) VALUES (33, 'canViewAllImport', 'Xem tất cả import', 4, 4, 1, NULL);
INSERT INTO `permissions` (`id`, `name`, `description`, `group_id`, `display_order`, `status`, `parent_id`) VALUES (34, 'lms_teachers', 'Giáo viên & trợ giảng', 2, 14, 1, NULL);
INSERT INTO `permissions` (`id`, `name`, `description`, `group_id`, `display_order`, `status`, `parent_id`) VALUES (35, 'lms_exchange', 'Quy đổi', 2, 8, 1, NULL);
INSERT INTO `permissions` (`id`, `name`, `description`, `group_id`, `display_order`, `status`, `parent_id`) VALUES (36, 'marketing_coupons', 'Voucher', 5, 2, 1, NULL);
INSERT INTO `permissions` (`id`, `name`, `description`, `group_id`, `display_order`, `status`, `parent_id`) VALUES (37, 'marketing_campaigns', 'Chiến dịch', 5, 1, 1, NULL);
INSERT INTO `permissions` (`id`, `name`, `description`, `group_id`, `display_order`, `status`, `parent_id`) VALUES (38, 'marketing_b2b_sources', 'Nguồn B2B', 5, 3, 1, NULL);
INSERT INTO `permissions` (`id`, `name`, `description`, `group_id`, `display_order`, `status`, `parent_id`) VALUES (39, 'marketing_b2b_campaigns', 'Chính sách B2B', 5, 3, 1, NULL);
INSERT INTO `permissions` (`id`, `name`, `description`, `group_id`, `display_order`, `status`, `parent_id`) VALUES (40, 'marketing_c2c_campaigns', 'Chính sách C2C', 5, 3, 1, NULL);
INSERT INTO `permissions` (`id`, `name`, `description`, `group_id`, `display_order`, `status`, `parent_id`) VALUES (41, 'lms_reports_business', 'Kinh doanh', 7, 1, 1, NULL);
INSERT INTO `permissions` (`id`, `name`, `description`, `group_id`, `display_order`, `status`, `parent_id`) VALUES (42, 'lms_reports_finance', 'Tài chính kế toán', 7, 2, 1, NULL);
INSERT INTO `permissions` (`id`, `name`, `description`, `group_id`, `display_order`, `status`, `parent_id`) VALUES (43, 'lms_reports_operate', 'Vận hành', 7, 3, 1, NULL);
INSERT INTO `permissions` (`id`, `name`, `description`, `group_id`, `display_order`, `status`, `parent_id`) VALUES (44, 'lms_reports_academic', 'Học thuật', 7, 4, 1, NULL);
INSERT INTO `permissions` (`id`, `name`, `description`, `group_id`, `display_order`, `status`, `parent_id`) VALUES (45, 'lms_reports_teacher', 'Giáo viên', 7, 5, 1, NULL);
INSERT INTO `permissions` (`id`, `name`, `description`, `group_id`, `display_order`, `status`, `parent_id`) VALUES (46, 'lms_reports_academic_01', 'BÁO CÁO CHI TIẾT GIỜ DẠY CỦA TRỢ GIẢNG', NULL, 1, 1, 44);
INSERT INTO `permissions` (`id`, `name`, `description`, `group_id`, `display_order`, `status`, `parent_id`) VALUES (47, 'lms_reports_academic_02', 'DANH SÁCH LỚP TEST', NULL, 1, 1, 44);
INSERT INTO `permissions` (`id`, `name`, `description`, `group_id`, `display_order`, `status`, `parent_id`) VALUES (48, 'lms_reports_business_01', 'KẾT QUẢ KINH DOANH NGÀY', NULL, 1, 1, 41);
INSERT INTO `permissions` (`id`, `name`, `description`, `group_id`, `display_order`, `status`, `parent_id`) VALUES (49, 'lms_reports_operate_01', 'BÁO CÁO CHI TIẾT HỌC SINH FULL FEE ACTIVE', NULL, 1, 1, 43);
INSERT INTO `permissions` (`id`, `name`, `description`, `group_id`, `display_order`, `status`, `parent_id`) VALUES (50, 'lms_reports_operate_02', 'BÁO CÁO CHI TIẾT HỌC SINH TÁI PHÍ', NULL, 1, 1, 43);
INSERT INTO `permissions` (`id`, `name`, `description`, `group_id`, `display_order`, `status`, `parent_id`) VALUES (51, 'lms_reports_operate_03', 'BÁO CÁO TỔNG HỢP HỌC SINH TÁI PHÍ', NULL, 1, 1, 43);
INSERT INTO `permissions` (`id`, `name`, `description`, `group_id`, `display_order`, `status`, `parent_id`) VALUES (52, 'lms_reports_operate_04', 'BÁO CÁO THEO AF HỌC SINH TÁI PHÍ', NULL, 1, 1, 43);
INSERT INTO `permissions` (`id`, `name`, `description`, `group_id`, `display_order`, `status`, `parent_id`) VALUES (53, 'lms_reports_operate_05', 'BÁO CÁO HỌC SINH PENDING', NULL, 1, 1, 43);
INSERT INTO `permissions` (`id`, `name`, `description`, `group_id`, `display_order`, `status`, `parent_id`) VALUES (54, 'lms_reports_operate_06', 'BÁO CÁO HỌC SINH BẢO LƯU', NULL, 1, 1, 43);
INSERT INTO `permissions` (`id`, `name`, `description`, `group_id`, `display_order`, `status`, `parent_id`) VALUES (55, 'lms_reports_operate_07', 'BÁO CÁO LỚP HỌC', NULL, 1, 1, 43);
INSERT INTO `permissions` (`id`, `name`, `description`, `group_id`, `display_order`, `status`, `parent_id`) VALUES (56, 'lms_reports_operate_08', 'CHỈ SỐ VẬN HÀNH ACS', NULL, 1, 1, 43);
INSERT INTO `permissions` (`id`, `name`, `description`, `group_id`, `display_order`, `status`, `parent_id`) VALUES (57, 'lms_reports_operate_09', 'BÁO CÁO TỶ LỆ ACS CỦA TỪNG AF', NULL, 1, 1, 43);
INSERT INTO `permissions` (`id`, `name`, `description`, `group_id`, `display_order`, `status`, `parent_id`) VALUES (58, 'lms_reports_operate_10', 'BÁO CÁO TỶ LỆ ACS CỦA TỪNG TRUNG TÂM', NULL, 1, 1, 43);
INSERT INTO `permissions` (`id`, `name`, `description`, `group_id`, `display_order`, `status`, `parent_id`) VALUES (59, 'lms_reports_operate_11', 'BÁO CÁO HỌC VIÊN TRIAL', NULL, 1, 1, 43);
INSERT INTO `permissions` (`id`, `name`, `description`, `group_id`, `display_order`, `status`, `parent_id`) VALUES (60, 'lms_reports_operate_12', 'BÁO CÁO HỌC VIÊN ĐẶT CỌC', NULL, 1, 1, 43);
INSERT INTO `permissions` (`id`, `name`, `description`, `group_id`, `display_order`, `status`, `parent_id`) VALUES (61, 'lms_reports_finance_01', 'BÁO CÁO CHI TIẾT DOANH SỐ', NULL, 1, 1, 42);
INSERT INTO `permissions` (`id`, `name`, `description`, `group_id`, `display_order`, `status`, `parent_id`) VALUES (62, 'lms_reports_finance_02', 'BÁO CÁO DOANH SỐ THEO TRUNG TÂM', NULL, 1, 1, 42);
INSERT INTO `permissions` (`id`, `name`, `description`, `group_id`, `display_order`, `status`, `parent_id`) VALUES (63, 'lms_reports_finance_03', 'BÁO CÁO DOANH THU THỰC HIỆN', NULL, 1, 1, 42);
INSERT INTO `permissions` (`id`, `name`, `description`, `group_id`, `display_order`, `status`, `parent_id`) VALUES (64, 'lms_reports_finance_04', 'BÁO CÁO DOANH SỐ THEO NHÂN VIÊN', NULL, 1, 1, 42);
INSERT INTO `permissions` (`id`, `name`, `description`, `group_id`, `display_order`, `status`, `parent_id`) VALUES (65, 'lms_reports_finance_05', 'BÁO CÁO CHI TIẾT ĐƠN HÀNG', NULL, 1, 1, 42);
INSERT INTO `permissions` (`id`, `name`, `description`, `group_id`, `display_order`, `status`, `parent_id`) VALUES (66, 'lms_reports_teacher_01', 'BÁO CÁO CHI TIẾT GIỜ DẠY CỦA GIÁO VIÊN', NULL, 1, 1, 45);
INSERT INTO `permissions` (`id`, `name`, `description`, `group_id`, `display_order`, `status`, `parent_id`) VALUES (67, 'dashboard_01', 'Học viên đang học', 6, 1, 1, NULL);
INSERT INTO `permissions` (`id`, `name`, `description`, `group_id`, `display_order`, `status`, `parent_id`) VALUES (68, 'dashboard_02', 'Tổng số lớp', 6, 1, 1, NULL);
INSERT INTO `permissions` (`id`, `name`, `description`, `group_id`, `display_order`, `status`, `parent_id`) VALUES (69, 'dashboard_03', 'Tổng số giáo viên', 6, 1, 1, NULL);
INSERT INTO `permissions` (`id`, `name`, `description`, `group_id`, `display_order`, `status`, `parent_id`) VALUES (70, 'dashboard_04', 'Tổng số phòng học', 6, 1, 1, NULL);
INSERT INTO `permissions` (`id`, `name`, `description`, `group_id`, `display_order`, `status`, `parent_id`) VALUES (71, 'dashboard_05', 'Tình hình học viên', 6, 1, 1, NULL);
INSERT INTO `permissions` (`id`, `name`, `description`, `group_id`, `display_order`, `status`, `parent_id`) VALUES (72, 'dashboard_06', 'Đăng ký khóa học', 6, 1, 1, NULL);
INSERT INTO `permissions` (`id`, `name`, `description`, `group_id`, `display_order`, `status`, `parent_id`) VALUES (73, 'dashboard_07', 'Doanh thu theo trung tâm', 6, 1, 1, NULL);
INSERT INTO `permissions` (`id`, `name`, `description`, `group_id`, `display_order`, `status`, `parent_id`) VALUES (74, 'dashboard_08', 'Doanh thu theo EC', 6, 1, 1, NULL);
INSERT INTO `permissions` (`id`, `name`, `description`, `group_id`, `display_order`, `status`, `parent_id`) VALUES (75, 'dashboard_09', 'Doanh số ngày', 6, 1, 1, NULL);
INSERT INTO `permissions` (`id`, `name`, `description`, `group_id`, `display_order`, `status`, `parent_id`) VALUES (76, 'dashboard_10', 'Doanh số tháng', 6, 1, 1, NULL);
INSERT INTO `permissions` (`id`, `name`, `description`, `group_id`, `display_order`, `status`, `parent_id`) VALUES (77, 'dashboard_11', 'Học sinh Renew theo trung tâm', 6, 1, 1, NULL);
INSERT INTO `permissions` (`id`, `name`, `description`, `group_id`, `display_order`, `status`, `parent_id`) VALUES (78, 'dashboard_12', 'Học sinh Renew theo AF', 6, 1, 1, NULL);
INSERT INTO `permissions` (`id`, `name`, `description`, `group_id`, `display_order`, `status`, `parent_id`) VALUES (79, 'dashboard_13', 'Học sinh Renew theo tháng', 6, 1, 1, NULL);
INSERT INTO `permissions` (`id`, `name`, `description`, `group_id`, `display_order`, `status`, `parent_id`) VALUES (80, 'lms_waitcharge_approve', 'Duyệt đóng phí', 2, 12, 1, NULL);
INSERT INTO `permissions` (`id`, `name`, `description`, `group_id`, `display_order`, `status`, `parent_id`) VALUES (81, 'approve_add_fee', 'Phê duyệt đóng phí', 8, 1, 1, NULL);
COMMIT;

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
  `num_sessions` int DEFAULT '0',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `proc_id` (`code`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of products
-- ----------------------------
BEGIN;
INSERT INTO `products` (`id`, `accounting_id`, `code`, `name`, `description`, `created_at`, `updated_at`, `creator_id`, `updator_id`, `status`, `max_student`, `min_number_of_days_in_week`, `max_number_of_days_in_week`, `num_sessions`) VALUES (25, NULL, 'PRE', 'Pre-Toeic', NULL, '2026-01-14 14:59:31', '2026-01-14 15:41:39', 1, 1, 1, NULL, NULL, NULL, NULL);
INSERT INTO `products` (`id`, `accounting_id`, `code`, `name`, `description`, `created_at`, `updated_at`, `creator_id`, `updator_id`, `status`, `max_student`, `min_number_of_days_in_week`, `max_number_of_days_in_week`, `num_sessions`) VALUES (26, NULL, 'LV1', 'Toeic level 1', NULL, '2026-01-14 15:41:56', NULL, 1, NULL, 1, NULL, NULL, NULL, NULL);
INSERT INTO `products` (`id`, `accounting_id`, `code`, `name`, `description`, `created_at`, `updated_at`, `creator_id`, `updator_id`, `status`, `max_student`, `min_number_of_days_in_week`, `max_number_of_days_in_week`, `num_sessions`) VALUES (27, NULL, 'LV2', 'Toeic level 2', NULL, '2026-01-14 15:42:10', NULL, 1, NULL, 1, NULL, NULL, NULL, NULL);
INSERT INTO `products` (`id`, `accounting_id`, `code`, `name`, `description`, `created_at`, `updated_at`, `creator_id`, `updator_id`, `status`, `max_student`, `min_number_of_days_in_week`, `max_number_of_days_in_week`, `num_sessions`) VALUES (28, NULL, 'Spe', 'Toeic Speaking', NULL, '2026-01-14 15:42:42', NULL, 1, NULL, 1, NULL, NULL, NULL, NULL);
INSERT INTO `products` (`id`, `accounting_id`, `code`, `name`, `description`, `created_at`, `updated_at`, `creator_id`, `updator_id`, `status`, `max_student`, `min_number_of_days_in_week`, `max_number_of_days_in_week`, `num_sessions`) VALUES (29, NULL, 'Wri', 'Toeic Writing', NULL, '2026-01-14 15:43:03', NULL, 1, NULL, 1, NULL, NULL, NULL, NULL);
COMMIT;

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
  `lo_trinh_id` int DEFAULT NULL,
  `option_id` int DEFAULT NULL,
  `type` int DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `product_id` (`product_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of programs
-- ----------------------------
BEGIN;
COMMIT;

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
-- Records of provinces
-- ----------------------------
BEGIN;
INSERT INTO `provinces` (`id`, `name`, `created_at`, `updated_at`, `accounting_id`, `caresoft_id`) VALUES (1, 'An Giang', NULL, NULL, NULL, NULL);
INSERT INTO `provinces` (`id`, `name`, `created_at`, `updated_at`, `accounting_id`, `caresoft_id`) VALUES (2, 'Bắc Ninh', NULL, NULL, NULL, NULL);
INSERT INTO `provinces` (`id`, `name`, `created_at`, `updated_at`, `accounting_id`, `caresoft_id`) VALUES (3, 'Cà Mau', NULL, NULL, NULL, NULL);
INSERT INTO `provinces` (`id`, `name`, `created_at`, `updated_at`, `accounting_id`, `caresoft_id`) VALUES (4, 'Cao Bằng', NULL, NULL, NULL, NULL);
INSERT INTO `provinces` (`id`, `name`, `created_at`, `updated_at`, `accounting_id`, `caresoft_id`) VALUES (5, 'Cần Thơ', NULL, NULL, NULL, NULL);
INSERT INTO `provinces` (`id`, `name`, `created_at`, `updated_at`, `accounting_id`, `caresoft_id`) VALUES (6, 'Đà Nẵng', NULL, NULL, NULL, NULL);
INSERT INTO `provinces` (`id`, `name`, `created_at`, `updated_at`, `accounting_id`, `caresoft_id`) VALUES (7, 'Đắk Lắk', NULL, NULL, NULL, NULL);
INSERT INTO `provinces` (`id`, `name`, `created_at`, `updated_at`, `accounting_id`, `caresoft_id`) VALUES (8, 'Điện Biên', NULL, NULL, NULL, NULL);
INSERT INTO `provinces` (`id`, `name`, `created_at`, `updated_at`, `accounting_id`, `caresoft_id`) VALUES (9, 'Đồng Nai', NULL, NULL, NULL, NULL);
INSERT INTO `provinces` (`id`, `name`, `created_at`, `updated_at`, `accounting_id`, `caresoft_id`) VALUES (10, 'Đồng Tháp', NULL, NULL, NULL, NULL);
INSERT INTO `provinces` (`id`, `name`, `created_at`, `updated_at`, `accounting_id`, `caresoft_id`) VALUES (11, 'Gia Lai', NULL, NULL, NULL, NULL);
INSERT INTO `provinces` (`id`, `name`, `created_at`, `updated_at`, `accounting_id`, `caresoft_id`) VALUES (12, 'Hà Nội', NULL, NULL, NULL, NULL);
INSERT INTO `provinces` (`id`, `name`, `created_at`, `updated_at`, `accounting_id`, `caresoft_id`) VALUES (13, 'Hà Tĩnh', NULL, NULL, NULL, NULL);
INSERT INTO `provinces` (`id`, `name`, `created_at`, `updated_at`, `accounting_id`, `caresoft_id`) VALUES (14, 'Hải Phòng', NULL, NULL, NULL, NULL);
INSERT INTO `provinces` (`id`, `name`, `created_at`, `updated_at`, `accounting_id`, `caresoft_id`) VALUES (15, 'Huế', NULL, NULL, NULL, NULL);
INSERT INTO `provinces` (`id`, `name`, `created_at`, `updated_at`, `accounting_id`, `caresoft_id`) VALUES (16, 'Hưng Yên', NULL, NULL, NULL, NULL);
INSERT INTO `provinces` (`id`, `name`, `created_at`, `updated_at`, `accounting_id`, `caresoft_id`) VALUES (17, 'Khánh Hòa', NULL, NULL, NULL, NULL);
INSERT INTO `provinces` (`id`, `name`, `created_at`, `updated_at`, `accounting_id`, `caresoft_id`) VALUES (18, 'Lai Châu', NULL, NULL, NULL, NULL);
INSERT INTO `provinces` (`id`, `name`, `created_at`, `updated_at`, `accounting_id`, `caresoft_id`) VALUES (19, 'Lạng Sơn', NULL, NULL, NULL, NULL);
INSERT INTO `provinces` (`id`, `name`, `created_at`, `updated_at`, `accounting_id`, `caresoft_id`) VALUES (20, 'Lào Cai', NULL, NULL, NULL, NULL);
INSERT INTO `provinces` (`id`, `name`, `created_at`, `updated_at`, `accounting_id`, `caresoft_id`) VALUES (21, 'Lâm Đồng', NULL, NULL, NULL, NULL);
INSERT INTO `provinces` (`id`, `name`, `created_at`, `updated_at`, `accounting_id`, `caresoft_id`) VALUES (22, 'Nghệ An', NULL, NULL, NULL, NULL);
INSERT INTO `provinces` (`id`, `name`, `created_at`, `updated_at`, `accounting_id`, `caresoft_id`) VALUES (23, 'Ninh Bình', NULL, NULL, NULL, NULL);
INSERT INTO `provinces` (`id`, `name`, `created_at`, `updated_at`, `accounting_id`, `caresoft_id`) VALUES (24, 'Phú Thọ', NULL, NULL, NULL, NULL);
INSERT INTO `provinces` (`id`, `name`, `created_at`, `updated_at`, `accounting_id`, `caresoft_id`) VALUES (25, 'Quảng Ngãi', NULL, NULL, NULL, NULL);
INSERT INTO `provinces` (`id`, `name`, `created_at`, `updated_at`, `accounting_id`, `caresoft_id`) VALUES (26, 'Quảng Ninh', NULL, NULL, NULL, NULL);
INSERT INTO `provinces` (`id`, `name`, `created_at`, `updated_at`, `accounting_id`, `caresoft_id`) VALUES (27, 'Quảng Trị', NULL, NULL, NULL, NULL);
INSERT INTO `provinces` (`id`, `name`, `created_at`, `updated_at`, `accounting_id`, `caresoft_id`) VALUES (28, 'Sơn La', NULL, NULL, NULL, NULL);
INSERT INTO `provinces` (`id`, `name`, `created_at`, `updated_at`, `accounting_id`, `caresoft_id`) VALUES (29, 'Tây Ninh', NULL, NULL, NULL, NULL);
INSERT INTO `provinces` (`id`, `name`, `created_at`, `updated_at`, `accounting_id`, `caresoft_id`) VALUES (30, 'Thái Nguyên', NULL, NULL, NULL, NULL);
INSERT INTO `provinces` (`id`, `name`, `created_at`, `updated_at`, `accounting_id`, `caresoft_id`) VALUES (31, 'Thanh Hóa', NULL, NULL, NULL, NULL);
INSERT INTO `provinces` (`id`, `name`, `created_at`, `updated_at`, `accounting_id`, `caresoft_id`) VALUES (32, 'Hồ Chí Minh', NULL, NULL, NULL, NULL);
INSERT INTO `provinces` (`id`, `name`, `created_at`, `updated_at`, `accounting_id`, `caresoft_id`) VALUES (33, 'Tuyên Quang', NULL, NULL, NULL, NULL);
INSERT INTO `provinces` (`id`, `name`, `created_at`, `updated_at`, `accounting_id`, `caresoft_id`) VALUES (34, 'Vĩnh Long', NULL, NULL, NULL, NULL);
COMMIT;

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
) ENGINE=InnoDB AUTO_INCREMENT=135 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of public_holiday
-- ----------------------------
BEGIN;
COMMIT;

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
  `report_month` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL COMMENT 'Tháng báo cáo',
  `type` tinyint(1) DEFAULT '0' COMMENT 'Phân loại bản ghi',
  `branch_id` int DEFAULT '0' COMMENT 'ID trung tâm',
  `note` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci COMMENT 'Ghi chú',
  `meta` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci COMMENT 'Dữ liệu mở rộng',
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
) ENGINE=InnoDB AUTO_INCREMENT=7079 DEFAULT CHARSET=utf8mb3;

-- ----------------------------
-- Records of report_full_fee_active
-- ----------------------------
BEGIN;
COMMIT;

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
  `renewed_month` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL COMMENT 'Thời điểm thực hiện renew tính theo Năm-Tháng với dữ liệu được định dạng là YYYY-MM ví dụ: 2019-05',
  `last_date` date DEFAULT NULL COMMENT 'Ngày đến hạn tái phí của contract',
  `note` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci COMMENT 'Thông tin ghi chú về bản ghi',
  `meta_data` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci COMMENT 'Dữ liệu tham chiếu mở rộng của bản ghi',
  `created_at` datetime DEFAULT NULL COMMENT 'Thời điểm khởi tạo bản ghi',
  `created_by` int DEFAULT '0' COMMENT 'Mã ID người tạo bản ghi',
  `disabled` tinyint(1) DEFAULT '0' COMMENT 'Có tính vào báo cáo hay không với 0 là có và 1 là không tính vào',
  `fixed` tinyint(1) DEFAULT '0' COMMENT 'Đánh dấu bản ghi đã được fix giá trị, không được phép cập nhật lại',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `cm_id` (`cm_id`) USING BTREE,
  KEY `branch_id` (`branch_id`) USING BTREE,
  KEY `renewed_month` (`renewed_month`) USING BTREE,
  KEY `contract_id` (`contract_id`) USING BTREE,
  KEY `student_id` (`student_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=7485 DEFAULT CHARSET=utf8mb3;

-- ----------------------------
-- Records of report_renews
-- ----------------------------
BEGIN;
COMMIT;

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
  `status` tinyint DEFAULT '0' COMMENT '0 - đã xóa, 1 - chờ duyệt, 2 - đã duyệt, 3 - từ chối, 4 - đã xử lý, 5 - chờ đóng phí, 6 - đã đóng phí',
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
  `must_charge` int DEFAULT NULL,
  `total_charged` int DEFAULT NULL,
  `debt_amount` int DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `student_id` (`student_id`) USING BTREE,
  KEY `creator_id` (`creator_id`) USING BTREE,
  KEY `approver_id` (`approver_id`) USING BTREE,
  KEY `contract_id` (`contract_id`) USING BTREE,
  KEY `branch_id` (`branch_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of reserves
-- ----------------------------
BEGIN;
COMMIT;

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
-- Records of role_has_user
-- ----------------------------
BEGIN;
INSERT INTO `role_has_user` (`user_id`, `role_id`) VALUES (1, 55);
INSERT INTO `role_has_user` (`user_id`, `role_id`) VALUES (1, 68);
INSERT INTO `role_has_user` (`user_id`, `role_id`) VALUES (1, 999999);
COMMIT;

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
) ENGINE=InnoDB AUTO_INCREMENT=1000002 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of roles
-- ----------------------------
BEGIN;
INSERT INTO `roles` (`id`, `code`, `name`, `description`, `guard_name`, `created_at`, `updated_at`, `creator_id`, `updator_id`) VALUES (36, '36', 'teacher', 'Giáo viên', 'api', NULL, NULL, NULL, NULL);
INSERT INTO `roles` (`id`, `code`, `name`, `description`, `guard_name`, `created_at`, `updated_at`, `creator_id`, `updator_id`) VALUES (37, '37', 'teacher_leader', 'Trưởng nhóm giáo viên', 'api', NULL, NULL, NULL, NULL);
INSERT INTO `roles` (`id`, `code`, `name`, `description`, `guard_name`, `created_at`, `updated_at`, `creator_id`, `updator_id`) VALUES (54, '54', 'ta', 'Trợ giảng', 'api', NULL, NULL, NULL, NULL);
INSERT INTO `roles` (`id`, `code`, `name`, `description`, `guard_name`, `created_at`, `updated_at`, `creator_id`, `updator_id`) VALUES (55, '55', 'cm', 'Nhân viên CSKH', 'api', '2020-08-27 09:04:43', '2025-02-17 22:07:57', NULL, 1);
INSERT INTO `roles` (`id`, `code`, `name`, `description`, `guard_name`, `created_at`, `updated_at`, `creator_id`, `updator_id`) VALUES (56, '56', 'cm_leader', 'Trưởng nhóm CSKH', 'api', '2024-04-06 22:17:25', '2025-02-17 22:07:45', 1, 1);
INSERT INTO `roles` (`id`, `code`, `name`, `description`, `guard_name`, `created_at`, `updated_at`, `creator_id`, `updator_id`) VALUES (68, '68', 'ec', 'Nhân viên kinh doanh', 'api', '2020-08-27 09:04:43', '2025-02-17 22:07:37', NULL, 1);
INSERT INTO `roles` (`id`, `code`, `name`, `description`, `guard_name`, `created_at`, `updated_at`, `creator_id`, `updator_id`) VALUES (69, '69', 'ec_leader', 'Trưởng nhóm Kinh doanh', 'api', '2023-07-27 13:44:05', '2025-02-17 22:07:27', NULL, 1);
INSERT INTO `roles` (`id`, `code`, `name`, `description`, `guard_name`, `created_at`, `updated_at`, `creator_id`, `updator_id`) VALUES (686868, '686868', 'ceo', 'Giám đốc trung tâm', 'api', '2023-05-26 10:44:07', '2023-05-26 10:44:07', NULL, NULL);
INSERT INTO `roles` (`id`, `code`, `name`, `description`, `guard_name`, `created_at`, `updated_at`, `creator_id`, `updator_id`) VALUES (999999, '999999', 'admin', 'Quản trị hệ thống', 'api', '2020-08-27 09:04:43', '2020-08-27 09:04:43', NULL, NULL);
COMMIT;

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
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of rooms
-- ----------------------------
BEGIN;
COMMIT;

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
  `subject_id` int DEFAULT '0',
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
) ENGINE=InnoDB AUTO_INCREMENT=2249 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- ----------------------------
-- Records of schedule_has_student
-- ----------------------------
BEGIN;
COMMIT;

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
) ENGINE=InnoDB AUTO_INCREMENT=5054 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of schedules
-- ----------------------------
BEGIN;
COMMIT;

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
) ENGINE=InnoDB AUTO_INCREMENT=278 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of sessions
-- ----------------------------
BEGIN;
COMMIT;

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
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of shifts
-- ----------------------------
BEGIN;
COMMIT;

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
  `source_id` int DEFAULT NULL,
  `campaign_id` int DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `branch_id` (`branch_id`) USING BTREE,
  KEY `source_id` (`source_id`) USING BTREE,
  KEY `campaign_id` (`campaign_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=471 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of source_detail
-- ----------------------------
BEGIN;
COMMIT;

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
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of sources
-- ----------------------------
BEGIN;
INSERT INTO `sources` (`id`, `name`, `description`, `status`, `created_at`, `updated_at`) VALUES (1, 'Marketing', NULL, 1, NULL, NULL);
INSERT INTO `sources` (`id`, `name`, `description`, `status`, `created_at`, `updated_at`) VALUES (2, 'B2B', NULL, 1, NULL, NULL);
INSERT INTO `sources` (`id`, `name`, `description`, `status`, `created_at`, `updated_at`) VALUES (3, 'C2C', NULL, 1, NULL, NULL);
INSERT INTO `sources` (`id`, `name`, `description`, `status`, `created_at`, `updated_at`) VALUES (4, 'Trung tâm', NULL, 1, NULL, NULL);
INSERT INTO `sources` (`id`, `name`, `description`, `status`, `created_at`, `updated_at`) VALUES (5, 'Khác', NULL, 1, NULL, NULL);
COMMIT;

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
) ENGINE=InnoDB AUTO_INCREMENT=568 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- ----------------------------
-- Records of student_logs
-- ----------------------------
BEGIN;
INSERT INTO `student_logs` (`id`, `student_id`, `content`, `created_at`, `creator_id`, `status`) VALUES (566, 541, 'Thêm mới hợp đồng nhập học - C000564', '2026-01-14 22:15:22', 1, 1);
INSERT INTO `student_logs` (`id`, `student_id`, `content`, `created_at`, `creator_id`, `status`) VALUES (567, 541, 'Thu đủ phí cho hợp đồng - 000007', '2026-01-14 22:17:18', 1, 1);
COMMIT;

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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- ----------------------------
-- Records of student_waitting_process
-- ----------------------------
BEGIN;
COMMIT;

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
  `aspiration` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'Nguyện vọng của phụ huynh',
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
  `avatar_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `c2c_mobile` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `index_student` (`lms_code`) USING BTREE,
  KEY `province_id` (`province_id`) USING BTREE,
  KEY `district_id` (`district_id`) USING BTREE,
  KEY `gud_mobile1` (`gud_mobile1`) USING BTREE,
  KEY `gud_mobile2` (`gud_mobile2`) USING BTREE,
  KEY `branch_id` (`branch_id`) USING BTREE,
  KEY `status` (`status`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=542 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of students
-- ----------------------------
BEGIN;
INSERT INTO `students` (`id`, `lms_code`, `name`, `firstname`, `midname`, `lastname`, `nick`, `gender`, `type`, `date_of_birth`, `gud_mobile1`, `gud_name1`, `gud_email1`, `gud_mobile2`, `gud_name2`, `gud_email2`, `address`, `province_id`, `district_id`, `school`, `school_level`, `school_grade`, `created_at`, `creator_id`, `updated_at`, `updator_id`, `note`, `aspiration`, `avatar`, `branch_id`, `attached_file`, `gud_card1`, `gud_card2`, `gud_birth_day1`, `gud_birth_day2`, `gud_gender1`, `gud_gender2`, `gud_job1`, `gud_job2`, `status`, `source_detail_id`, `source_id`, `avatar_url`, `c2c_mobile`) VALUES (541, 'LAPO000541', 'Nguyễn Văn A', 'Nguyễn', 'Văn', 'A', NULL, 'M', 0, NULL, '0389941902', NULL, NULL, NULL, NULL, NULL, NULL, 12, 999, NULL, NULL, NULL, '2026-01-14 22:15:22', 1, '2026-01-14 22:15:22', NULL, NULL, NULL, NULL, 9, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, 4, '/images/common/avatar-boy.svg', NULL);
COMMIT;

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
) ENGINE=InnoDB AUTO_INCREMENT=289 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- ----------------------------
-- Records of subject_has_class
-- ----------------------------
BEGIN;
COMMIT;

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
-- Records of subjects
-- ----------------------------
BEGIN;
COMMIT;

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
) ENGINE=InnoDB AUTO_INCREMENT=539 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of term_student_user
-- ----------------------------
BEGIN;
INSERT INTO `term_student_user` (`id`, `student_id`, `ec_id`, `ec_leader_id`, `cm_id`, `cm_leader_id`, `ceo_branch_id`, `teacher_id`, `created_at`, `updated_at`, `status`, `branch_id`, `creator_id`, `updator_id`) VALUES (538, 541, 1, NULL, NULL, NULL, NULL, NULL, '2026-01-14 22:15:22', NULL, 1, 9, 1, NULL);
COMMIT;

-- ----------------------------
-- Table structure for tmp_hanam
-- ----------------------------
DROP TABLE IF EXISTS `tmp_hanam`;
CREATE TABLE `tmp_hanam` (
  `id` int NOT NULL AUTO_INCREMENT,
  `student_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `so_buoi_tra_phi` int DEFAULT NULL,
  `so_buoi_hoc_bong` int DEFAULT NULL,
  `so_tien_con_lai` int DEFAULT NULL,
  `parent_mobile` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parent_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint DEFAULT '0',
  `start_date` date DEFAULT NULL,
  `tuition_fee_id` int DEFAULT NULL,
  `branch_id` int DEFAULT NULL,
  `crm_parent_id` int DEFAULT NULL,
  `crm_student_id` int DEFAULT NULL,
  `student_id` int DEFAULT NULL,
  `contract_id` int DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=600 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of tmp_hanam
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for tmp_payments
-- ----------------------------
DROP TABLE IF EXISTS `tmp_payments`;
CREATE TABLE `tmp_payments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `agreement_id` int DEFAULT NULL,
  `contract_id` int DEFAULT NULL,
  `charge_amount` int DEFAULT NULL,
  `debt_amount` int DEFAULT NULL,
  `total_charged` int DEFAULT NULL,
  `payload` int DEFAULT NULL,
  `charge_date` date DEFAULT NULL,
  `method` int DEFAULT NULL,
  `note` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `meta_data` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` datetime DEFAULT NULL,
  `creator_id` int DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updator_id` int DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `approver_id` int DEFAULT NULL,
  `status` tinyint DEFAULT '0',
  `charge_time` int DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `contract_id` (`contract_id`) USING BTREE,
  KEY `agreement_id` (`agreement_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=53 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of tmp_payments
-- ----------------------------
BEGIN;
INSERT INTO `tmp_payments` (`id`, `agreement_id`, `contract_id`, `charge_amount`, `debt_amount`, `total_charged`, `payload`, `charge_date`, `method`, `note`, `meta_data`, `created_at`, `creator_id`, `updated_at`, `updator_id`, `approved_at`, `approver_id`, `status`, `charge_time`) VALUES (52, 7, NULL, 5936000, 0, 5936000, NULL, '2026-01-01', 1, NULL, '{\"agreement_id\":7,\"note\":null,\"charge_date\":\"2026-01-01\",\"amount\":5936000,\"method\":1}', '2026-01-14 22:17:07', 1, NULL, NULL, '2026-01-14 22:17:18', 1, 1, NULL);
COMMIT;

-- ----------------------------
-- Table structure for tuition_fee
-- ----------------------------
DROP TABLE IF EXISTS `tuition_fee`;
CREATE TABLE `tuition_fee` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `accounting_id` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `product_id` int DEFAULT NULL,
  `program_id` int DEFAULT NULL,
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
  `number_of_months` float DEFAULT NULL COMMENT 'số tháng đào tạo của gói phí',
  `type_fee` tinyint DEFAULT NULL,
  `type_contract` tinyint DEFAULT '1' COMMENT '1- chính thức, 0 - học thử',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `product_id` (`product_id`) USING BTREE,
  KEY `zone_id` (`zone_id`) USING BTREE,
  KEY `creator_id` (`creator_id`) USING BTREE,
  KEY `editor_id` (`updator_id`) USING BTREE,
  KEY `program_id` (`program_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=44 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of tuition_fee
-- ----------------------------
BEGIN;
INSERT INTO `tuition_fee` (`id`, `name`, `accounting_id`, `product_id`, `program_id`, `zone_id`, `session`, `price`, `discount`, `tax`, `receivable`, `created_at`, `creator_id`, `updated_at`, `updator_id`, `available_date`, `expired_date`, `hash_key`, `changed_fields`, `status`, `type`, `branch_id`, `meta_data`, `number_of_months`, `type_fee`, `type_contract`) VALUES (38, 'Pre_Toeic', NULL, 25, NULL, NULL, 14, 2468000, NULL, NULL, 2468000, '2026-01-14 16:09:48', 1, '2026-01-14 16:11:09', 1, '2026-01-14', '2026-12-31', NULL, NULL, 1, 0, '9,10,11,12', NULL, 0.5, 1, 1);
INSERT INTO `tuition_fee` (`id`, `name`, `accounting_id`, `product_id`, `program_id`, `zone_id`, `session`, `price`, `discount`, `tax`, `receivable`, `created_at`, `creator_id`, `updated_at`, `updator_id`, `available_date`, `expired_date`, `hash_key`, `changed_fields`, `status`, `type`, `branch_id`, `meta_data`, `number_of_months`, `type_fee`, `type_contract`) VALUES (39, 'Toeic Lv1', NULL, 26, NULL, NULL, 25, 3968000, NULL, NULL, 3968000, '2026-01-14 16:13:29', 1, NULL, NULL, '2026-01-14', '2026-12-31', NULL, NULL, 1, 0, '9,10,11,12', NULL, 1, 1, 1);
INSERT INTO `tuition_fee` (`id`, `name`, `accounting_id`, `product_id`, `program_id`, `zone_id`, `session`, `price`, `discount`, `tax`, `receivable`, `created_at`, `creator_id`, `updated_at`, `updator_id`, `available_date`, `expired_date`, `hash_key`, `changed_fields`, `status`, `type`, `branch_id`, `meta_data`, `number_of_months`, `type_fee`, `type_contract`) VALUES (40, 'Toeic Lv2', NULL, 27, NULL, NULL, 25, 4268000, NULL, NULL, 4268000, '2026-01-14 16:14:10', 1, NULL, NULL, '2026-01-14', '2026-01-31', NULL, NULL, 1, 0, '9,10,11,12', NULL, 1, 1, 1);
INSERT INTO `tuition_fee` (`id`, `name`, `accounting_id`, `product_id`, `program_id`, `zone_id`, `session`, `price`, `discount`, `tax`, `receivable`, `created_at`, `creator_id`, `updated_at`, `updator_id`, `available_date`, `expired_date`, `hash_key`, `changed_fields`, `status`, `type`, `branch_id`, `meta_data`, `number_of_months`, `type_fee`, `type_contract`) VALUES (41, 'Toeic Speaking', NULL, 28, NULL, NULL, 20, 3968000, NULL, NULL, 3968000, '2026-01-14 16:14:42', 1, NULL, NULL, '2026-01-14', '2026-01-31', NULL, NULL, 1, 0, '', NULL, 1, 1, 1);
INSERT INTO `tuition_fee` (`id`, `name`, `accounting_id`, `product_id`, `program_id`, `zone_id`, `session`, `price`, `discount`, `tax`, `receivable`, `created_at`, `creator_id`, `updated_at`, `updator_id`, `available_date`, `expired_date`, `hash_key`, `changed_fields`, `status`, `type`, `branch_id`, `meta_data`, `number_of_months`, `type_fee`, `type_contract`) VALUES (42, 'Toeic Writing', NULL, 29, NULL, NULL, 20, 4268000, NULL, NULL, 4268000, '2026-01-14 16:15:16', 1, NULL, NULL, '2026-01-14', '2026-01-31', NULL, NULL, 1, 0, '9,10,11,12', NULL, 1, 1, 1);
INSERT INTO `tuition_fee` (`id`, `name`, `accounting_id`, `product_id`, `program_id`, `zone_id`, `session`, `price`, `discount`, `tax`, `receivable`, `created_at`, `creator_id`, `updated_at`, `updator_id`, `available_date`, `expired_date`, `hash_key`, `changed_fields`, `status`, `type`, `branch_id`, `meta_data`, `number_of_months`, `type_fee`, `type_contract`) VALUES (43, 'Combo Pre_Toeic + Toeic Lv1', NULL, NULL, NULL, NULL, NULL, 5936000, NULL, NULL, 5936000, '2026-01-14 16:16:38', 1, '2026-01-14 16:36:06', 1, '2026-01-14', '2026-01-31', NULL, NULL, 1, 0, '9,10,11,12', NULL, 1.5, 2, 1);
COMMIT;

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
  `price_combo` int DEFAULT '0',
  `stt` tinyint DEFAULT '0',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `tuition_id` (`tuition_fee_id`) USING BTREE,
  KEY `exchange_tuition_id` (`exchange_tuition_fee_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb3;

-- ----------------------------
-- Records of tuition_fee_relation
-- ----------------------------
BEGIN;
INSERT INTO `tuition_fee_relation` (`id`, `tuition_fee_id`, `exchange_tuition_fee_id`, `status`, `tuition_fee_name`, `exchange_tuition_fee_name`, `price_combo`, `stt`) VALUES (16, 43, 38, 1, 'Combo Pre_Toeic + Toeic Lv1', 'Pre_Toeic', 2218000, 1);
INSERT INTO `tuition_fee_relation` (`id`, `tuition_fee_id`, `exchange_tuition_fee_id`, `status`, `tuition_fee_name`, `exchange_tuition_fee_name`, `price_combo`, `stt`) VALUES (17, 43, 39, 1, 'Combo Pre_Toeic + Toeic Lv1', 'Toeic Lv1', 3718000, 2);
COMMIT;

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
-- Records of tuition_transfer
-- ----------------------------
BEGIN;
COMMIT;

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
  `avatar_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `manager_id` (`manager_id`) USING BTREE,
  KEY `hrm_id` (`hrm_id`) USING BTREE,
  KEY `manager_hrm_id` (`manager_hrm_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of users
-- ----------------------------
BEGIN;
INSERT INTO `users` (`id`, `name`, `phone`, `email`, `email_verified_at`, `password`, `api_token`, `menuroles`, `status`, `remember_token`, `created_at`, `updated_at`, `creator_id`, `updator_id`, `deleted_at`, `branch_id`, `branch_name`, `hrm_id`, `manager_hrm_id`, `manager_id`, `change_password`, `role_name`, `sip_id`, `avatar_url`) VALUES (1, 'Lương Thành Công', '0389941902', 'admin@admin.com', '2020-08-27 09:04:43', '$2y$10$upeEWEPRUHvriPrThSy8hOMzfqxFGRFPrBP0UgkBwC8C54RBz/6C6', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwczovL2Rldi1lcnAubGFwby5lZHUudm4vYXBpL2F1dGgvbG9naW4iLCJpYXQiOjE3Njg0MDMwODIsImV4cCI6MTc2OTAwNzg4MiwibmJmIjoxNzY4NDAzMDgyLCJqdGkiOiJpODUxVVJnZVNtd3M0NE5tIiwic3ViIjoiMSIsInBydiI6Ijg3ZTBhZjFlZjlmZDE1ODEyZmRlYzk3MTUzYTE0ZTBiMDQ3NTQ2YWEifQ.DqowbIaFzuSLcFa_xNeGk3gGu6Ii7GOxR894R5TXxz4', 'admin,user', 1, 'EQt3UDeo4F', '2020-08-27 09:04:43', '2026-01-14 22:05:23', NULL, 1, NULL, 12, 'Cơ sở Hà Đông, Cơ sở Thương Mại, Cơ sở Đống Đa 1, Cơ sở Trần Quốc Hoàn', 'admin', NULL, NULL, 0, 'Nhân viên CSKH, Nhân viên kinh doanh, Quản trị hệ thống', NULL, '/static/upload/avatars/2025_03/default-avatar-icon-of-social-media-user-vector.jpg');
COMMIT;

SET FOREIGN_KEY_CHECKS = 1;
