/*
 Navicat Premium Data Transfer

 Source Server         : DB - XGenius - 34.126.131.120
 Source Server Type    : MySQL
 Source Server Version : 80040
 Source Host           : 34.126.131.120:3306
 Source Schema         : xgenius_erp

 Target Server Type    : MySQL
 Target Server Version : 80040
 File Encoding         : 65001

 Date: 13/12/2024 15:50:49
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
-- Records of assessments
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
INSERT INTO `branch_has_user` VALUES (4, 1);
INSERT INTO `branch_has_user` VALUES (4, 6);
INSERT INTO `branch_has_user` VALUES (4, 9);
INSERT INTO `branch_has_user` VALUES (4, 10);
INSERT INTO `branch_has_user` VALUES (4, 11);
INSERT INTO `branch_has_user` VALUES (5, 1);
INSERT INTO `branch_has_user` VALUES (5, 4);
INSERT INTO `branch_has_user` VALUES (5, 9);
INSERT INTO `branch_has_user` VALUES (5, 12);
INSERT INTO `branch_has_user` VALUES (6, 1);
INSERT INTO `branch_has_user` VALUES (6, 4);
INSERT INTO `branch_has_user` VALUES (6, 9);
INSERT INTO `branch_has_user` VALUES (6, 13);
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
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of branches
-- ----------------------------
BEGIN;
INSERT INTO `branches` VALUES (4, 'Trung tâm Xgenius Nguyễn Văn Cừ', 'X01', NULL, NULL, NULL, NULL, NULL, NULL, '2024-11-19', '2024-11-30 18:49:59', NULL, 1, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `branches` VALUES (5, 'Trung tâm Xgenius Vĩnh Phúc', 'X02', NULL, NULL, NULL, NULL, NULL, NULL, '2024-12-01', '2024-12-07 19:57:53', NULL, 1, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `branches` VALUES (6, 'Trung tâm Xgenius Cẩm Phả', 'X03', NULL, NULL, NULL, NULL, NULL, NULL, '2024-12-01', '2024-12-07 19:58:19', NULL, 1, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL);
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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of classes
-- ----------------------------
BEGIN;
INSERT INTO `classes` VALUES (14, 10, '***CK2/TS 5/ Room 2/ Thomas', 8, 10, 6, '2024-12-01', '2025-03-24', NULL, '5,8', 16, '2024-12-08 20:26:21', '2024-12-08 20:27:07', 1, 1, 4, 1, 32, 1);
INSERT INTO `classes` VALUES (15, 9, '***PP/SS 6/ Room 2/ Marry', 8, 8, 4, '2024-12-01', '2025-03-24', NULL, '7,8', 16, '2024-12-08 20:32:28', NULL, 1, NULL, 5, 1, 32, 1);
INSERT INTO `classes` VALUES (16, 9, '***PP/TF 5/ Room 2/ Thomas', 8, 8, 4, '2024-12-01', '2025-03-22', NULL, '3,6', 16, '2024-12-08 20:33:15', NULL, 1, NULL, 6, 1, 32, 1);
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
-- Records of contracts
-- ----------------------------
BEGIN;
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
  PRIMARY KEY (`id`) USING BTREE,
  KEY `code` (`code`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=287 DEFAULT CHARSET=latin1;

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
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

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
  PRIMARY KEY (`id`) USING BTREE,
  KEY `import_id` (`import_id`) USING BTREE,
  KEY `gud_mobile1` (`gud_mobile1`) USING BTREE,
  KEY `gud_mobile2` (`gud_mobile2`) USING BTREE,
  KEY `parent_id` (`parent_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- ----------------------------
-- Records of crm_import_parents
-- ----------------------------
BEGIN;
INSERT INTO `crm_import_parents` VALUES (25, 10126, 'Nguyễn Thị Huế', 'demo@gmail.com', '0954112541', '0954112542', 'hà nội', 'ghi chú', 1, '2024-12-11 23:12:42', 1, '', 'Nguyễn Văn A', 'Nguyễn Văn B', '2015-07-08', '2016-08-16', 'C00001', NULL, '2022-07-24 00:00:00', 'C01', 1, NULL, NULL, NULL, NULL, NULL);
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
) ENGINE=InnoDB AUTO_INCREMENT=10127 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- ----------------------------
-- Records of crm_imports
-- ----------------------------
BEGIN;
INSERT INTO `crm_imports` VALUES (10126, 'ba164e3b42428da6dd97b6b970449c22.xlsx', 'static/upload/ba164e3b42428da6dd97b6b970449c22.xlsx', 0, '2024-12-11 23:12:42', 1, NULL, NULL);
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
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- ----------------------------
-- Records of crm_parent_logs
-- ----------------------------
BEGIN;
INSERT INTO `crm_parent_logs` VALUES (22, 13, 'Khởi tạo khách hàng thủ công', '2024-11-30 20:47:48', 1, 1);
INSERT INTO `crm_parent_logs` VALUES (23, 13, 'Thêm mới học sinh: Lương Bảo Hân (ID: 11)', '2024-11-30 21:02:22', 1, 1);
INSERT INTO `crm_parent_logs` VALUES (24, 13, 'Thêm mới học sinh: Lương Bảo Long (ID: 12)', '2024-11-30 21:19:27', 1, 1);
INSERT INTO `crm_parent_logs` VALUES (25, 14, 'Khởi tạo khách hàng thủ công', '2024-12-09 08:59:16', 13, 1);
INSERT INTO `crm_parent_logs` VALUES (26, 14, 'Thêm mới học sinh: trần bảo tâm (ID: 13)', '2024-12-09 09:30:11', 13, 1);
INSERT INTO `crm_parent_logs` VALUES (27, 14, 'Thêm mới học sinh: trần bảo nam (ID: 14)', '2024-12-09 09:30:36', 13, 1);
INSERT INTO `crm_parent_logs` VALUES (28, 14, 'Cập nhật thông tin học sinh (ID - 14): ', '2024-12-09 12:23:23', 13, 1);
INSERT INTO `crm_parent_logs` VALUES (29, 15, 'Khởi tạo khách hàng thủ công', '2024-12-11 09:25:40', 13, 1);
INSERT INTO `crm_parent_logs` VALUES (30, 15, 'Thêm mới học sinh: Hồ Bảo Phong (ID: 15)', '2024-12-11 09:29:29', 13, 1);
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
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- ----------------------------
-- Records of crm_parents
-- ----------------------------
BEGIN;
INSERT INTO `crm_parents` VALUES (13, 'Lương Thành Công', NULL, '0389941902', NULL, NULL, 1, 5, '2024-11-30 20:47:48', 1, 'M', NULL, NULL, 461, 31, NULL, 82, '2024-11-30 20:47:48', 1, 4, NULL, NULL, 1, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, '');
INSERT INTO `crm_parents` VALUES (14, 'Đinh Thị Ngọc Hân', NULL, '0987683713', NULL, NULL, 22, 195, '2024-12-09 08:59:16', 13, 'F', NULL, 3, 461, 31, NULL, 0, '2024-12-09 08:59:16', 13, 6, NULL, NULL, 1, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL);
INSERT INTO `crm_parents` VALUES (15, 'Hồ Thị Ngân', 'a@gmail.com', '0868036633', NULL, NULL, 22, 195, '2024-12-11 09:25:40', 13, 'F', '2024-12-11', 3, 461, 31, NULL, 71, '2024-12-11 09:25:40', 13, 6, NULL, NULL, 1, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL);
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
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- ----------------------------
-- Records of crm_students
-- ----------------------------
BEGIN;
INSERT INTO `crm_students` VALUES (11, 13, 'Lương Bảo Hân', 'F', NULL, NULL, '2018-09-08', '2024-11-30 21:02:21', 1, 3, NULL, NULL, '2024-11-30 21:02:38', 1, 1, 4, '2024-11-01 00:00:00', '2024-11-02 00:00:00', 1, 'đã check in', 6, NULL, 6);
INSERT INTO `crm_students` VALUES (12, 13, 'Lương Bảo Long', 'M', NULL, NULL, '2021-05-27', '2024-11-30 21:19:27', 1, 3, NULL, NULL, '2024-11-30 21:19:39', 1, 1, 4, '2024-11-01 00:00:00', '2024-11-01 00:00:03', 1, 'đã checkin', 7, NULL, 6);
INSERT INTO `crm_students` VALUES (13, 14, 'trần bảo tâm', 'M', NULL, NULL, '2014-09-16', '2024-12-09 09:30:11', 13, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0);
INSERT INTO `crm_students` VALUES (14, 14, 'trần bảo nam', 'M', NULL, NULL, '2013-03-18', '2024-12-09 09:30:36', 13, 3, NULL, NULL, '2024-12-09 12:23:57', 13, 13, 6, '2024-12-10 09:00:00', '2024-12-10 10:00:00', 13, 'Đến check in muộn', 8, NULL, 8);
INSERT INTO `crm_students` VALUES (15, 15, 'Hồ Bảo Phong', 'M', NULL, NULL, '2014-04-14', '2024-12-11 09:29:29', 13, 3, NULL, NULL, '2024-12-11 09:33:45', 13, 13, 6, '2024-12-03 00:00:00', '2024-11-26 00:00:00', 13, 'FFFH', 9, NULL, 8);
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
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

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
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `province_id` int DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `accounting_id` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=976 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of districts
-- ----------------------------
BEGIN;
INSERT INTO `districts` VALUES (1, 'Ba Đình', 1, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '24.01');
INSERT INTO `districts` VALUES (2, 'Hoàn Kiếm', 1, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '24.07');
INSERT INTO `districts` VALUES (3, 'Tây Hồ', 1, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '24.11');
INSERT INTO `districts` VALUES (4, 'Long Biên', 1, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '24.09');
INSERT INTO `districts` VALUES (5, 'Cầu Giấy', 1, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '24.03');
INSERT INTO `districts` VALUES (6, 'Đống Đa', 1, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '24.04');
INSERT INTO `districts` VALUES (7, 'Hai Bà Trưng', 1, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '24.06');
INSERT INTO `districts` VALUES (8, 'Hoàng Mai', 1, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '24.08');
INSERT INTO `districts` VALUES (9, 'Thanh Xuân', 1, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '24.12');
INSERT INTO `districts` VALUES (16, 'Sóc Sơn', 1, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '24.25');
INSERT INTO `districts` VALUES (17, 'Đông Anh', 1, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '24.17');
INSERT INTO `districts` VALUES (18, 'Gia Lâm', 1, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '24.18');
INSERT INTO `districts` VALUES (19, 'Từ Liêm', 1, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '24.10');
INSERT INTO `districts` VALUES (20, 'Thanh Trì', 1, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '24.28');
INSERT INTO `districts` VALUES (24, 'Hà Giang', 2, '2018-03-03 00:00:00', '2018-03-03 00:00:00', NULL);
INSERT INTO `districts` VALUES (26, 'Đồng Văn', 2, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '06.03');
INSERT INTO `districts` VALUES (27, 'Mèo Vạc', 2, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '06.05');
INSERT INTO `districts` VALUES (28, 'Yên Minh', 2, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '06.10');
INSERT INTO `districts` VALUES (29, 'Quản Bạ', 2, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '06.06');
INSERT INTO `districts` VALUES (30, 'Vị Xuyên', 2, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '06.08');
INSERT INTO `districts` VALUES (31, 'Bắc Mê', 2, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '06.01');
INSERT INTO `districts` VALUES (32, 'Hoàng Su Phì', 2, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '06.04');
INSERT INTO `districts` VALUES (33, 'Xín Mần', 2, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '06.09');
INSERT INTO `districts` VALUES (34, 'Bắc Quang', 2, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '06.02');
INSERT INTO `districts` VALUES (35, 'Quang Bình', 2, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '06.07');
INSERT INTO `districts` VALUES (40, 'Cao Bằng', 4, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '04.01');
INSERT INTO `districts` VALUES (42, 'Bảo Lâm', 4, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '04.03');
INSERT INTO `districts` VALUES (43, 'Bảo Lạc', 4, '2018-03-03 00:00:00', '2018-03-03 00:00:00', NULL);
INSERT INTO `districts` VALUES (44, 'Thông Nông', 4, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '04.11');
INSERT INTO `districts` VALUES (45, 'Hà Quảng', 4, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '04.05');
INSERT INTO `districts` VALUES (46, 'Trà Lĩnh', 4, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '04.12');
INSERT INTO `districts` VALUES (47, 'Trùng Khánh', 4, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '04.13');
INSERT INTO `districts` VALUES (48, 'Hạ Lang', 4, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '04.04');
INSERT INTO `districts` VALUES (49, 'Quảng Uyên', 4, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '04.09');
INSERT INTO `districts` VALUES (50, 'Phục Hoà', 4, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '04.08');
INSERT INTO `districts` VALUES (51, 'Hoà An', 4, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '04.06');
INSERT INTO `districts` VALUES (52, 'Nguyên Bình', 4, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '04.07');
INSERT INTO `districts` VALUES (53, 'Thạch An', 4, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '04.10');
INSERT INTO `districts` VALUES (58, 'Bắc Kạn', 6, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '02.01');
INSERT INTO `districts` VALUES (60, 'Pác Nặm', 6, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '02.08');
INSERT INTO `districts` VALUES (61, 'Ba Bể', 6, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '02.02');
INSERT INTO `districts` VALUES (62, 'Ngân Sơn', 6, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '02.07');
INSERT INTO `districts` VALUES (63, 'Bạch Thông', 6, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '02.03');
INSERT INTO `districts` VALUES (64, 'Chợ Đồn', 6, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '02.04');
INSERT INTO `districts` VALUES (65, 'Chợ Mới', 6, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '02.05');
INSERT INTO `districts` VALUES (66, 'Na Rì', 6, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '02.06');
INSERT INTO `districts` VALUES (70, 'Tuyên Quang', 8, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '23.01');
INSERT INTO `districts` VALUES (72, 'Nà Hang', 8, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '23.05');
INSERT INTO `districts` VALUES (73, 'Chiêm Hóa', 8, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '23.02');
INSERT INTO `districts` VALUES (74, 'Hàm Yên', 8, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '23.03');
INSERT INTO `districts` VALUES (75, 'Yên Sơn', 8, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '23.07');
INSERT INTO `districts` VALUES (76, 'Sơn Dương', 8, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '23.06');
INSERT INTO `districts` VALUES (80, 'Lào Cai', 10, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '15.01');
INSERT INTO `districts` VALUES (82, 'Bát Xát', 10, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '15.04');
INSERT INTO `districts` VALUES (83, 'Mường Khương', 10, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '15.06');
INSERT INTO `districts` VALUES (84, 'Si Ma Cai', 10, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '15.08');
INSERT INTO `districts` VALUES (85, 'Bắc Hà', 10, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '15.05');
INSERT INTO `districts` VALUES (86, 'Bảo Thắng', 10, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '15.02');
INSERT INTO `districts` VALUES (87, 'Bảo Yên', 10, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '15.03');
INSERT INTO `districts` VALUES (88, 'Sa Pa', 10, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '15.07');
INSERT INTO `districts` VALUES (89, 'Văn Bàn', 10, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '15.09');
INSERT INTO `districts` VALUES (94, 'Điện Biên Phủ', 11, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '05.01');
INSERT INTO `districts` VALUES (95, 'Mường Lay', 11, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '05.02');
INSERT INTO `districts` VALUES (96, 'Mường Nhé', 11, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '05.07');
INSERT INTO `districts` VALUES (97, 'Mường Chà', 11, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '05.06');
INSERT INTO `districts` VALUES (98, 'Tủa Chùa', 11, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '05.08');
INSERT INTO `districts` VALUES (99, 'Tuần Giáo', 11, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '05.09');
INSERT INTO `districts` VALUES (100, 'Điện Biên', 11, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '05.03');
INSERT INTO `districts` VALUES (101, 'Điện Biên Đông', 11, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '05.04');
INSERT INTO `districts` VALUES (102, 'Mường Ảng', 11, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '05.05');
INSERT INTO `districts` VALUES (104, 'Lai Châu', 12, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '13.01');
INSERT INTO `districts` VALUES (106, 'Tam Đường', 12, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '13.05');
INSERT INTO `districts` VALUES (107, 'Mường Tè', 12, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '13.02');
INSERT INTO `districts` VALUES (108, 'Sìn Hồ', 12, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '13.04');
INSERT INTO `districts` VALUES (109, 'Phong Thổ', 12, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '13.03');
INSERT INTO `districts` VALUES (110, 'Than Uyên', 12, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '13.06');
INSERT INTO `districts` VALUES (111, 'Tân Uyên', 12, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '13.07');
INSERT INTO `districts` VALUES (116, 'Sơn La', 14, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '20.01');
INSERT INTO `districts` VALUES (118, 'Quỳnh Nhai', 14, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '20.02');
INSERT INTO `districts` VALUES (119, 'Thuận Châu', 14, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '20.04');
INSERT INTO `districts` VALUES (120, 'Mường La', 14, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '20.03');
INSERT INTO `districts` VALUES (121, 'Bắc Yên', 14, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '20.06');
INSERT INTO `districts` VALUES (122, 'Phù Yên', 14, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '20.05');
INSERT INTO `districts` VALUES (123, 'Mộc Châu', 14, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '20.10');
INSERT INTO `districts` VALUES (124, 'Yên Châu', 14, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '20.09');
INSERT INTO `districts` VALUES (125, 'Mai Sơn', 14, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '20.07');
INSERT INTO `districts` VALUES (126, 'Sông Mã', 14, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '20.08');
INSERT INTO `districts` VALUES (127, 'Sốp Cộp', 14, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '20.11');
INSERT INTO `districts` VALUES (132, 'Yên Bái', 15, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '25.01');
INSERT INTO `districts` VALUES (133, 'Nghĩa Lộ', 15, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '25.02');
INSERT INTO `districts` VALUES (135, 'Lục Yên', 15, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '25.03');
INSERT INTO `districts` VALUES (136, 'Văn Yên', 15, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '25.08');
INSERT INTO `districts` VALUES (137, 'Mù Cang Chải', 15, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '25.04');
INSERT INTO `districts` VALUES (138, 'Trấn Yên', 15, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '25.06');
INSERT INTO `districts` VALUES (139, 'Trạm Tấu', 15, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '25.05');
INSERT INTO `districts` VALUES (140, 'Văn Chấn', 15, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '25.07');
INSERT INTO `districts` VALUES (141, 'Yên Bình', 15, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '25.09');
INSERT INTO `districts` VALUES (148, 'Hòa Bình', 17, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '11.01');
INSERT INTO `districts` VALUES (150, 'Đà Bắc', 17, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '11.04');
INSERT INTO `districts` VALUES (151, 'Kỳ Sơn', 17, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '11.06');
INSERT INTO `districts` VALUES (152, 'Lương Sơn', 17, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '11.02');
INSERT INTO `districts` VALUES (153, 'Kim Bôi', 17, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '11.05');
INSERT INTO `districts` VALUES (154, 'Cao Phong', 17, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '11.03');
INSERT INTO `districts` VALUES (155, 'Tân Lạc', 17, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '11.10');
INSERT INTO `districts` VALUES (156, 'Mai Châu', 17, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '11.09');
INSERT INTO `districts` VALUES (157, 'Lạc Sơn', 17, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '11.07');
INSERT INTO `districts` VALUES (158, 'Yên Thủy', 17, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '11.11');
INSERT INTO `districts` VALUES (159, 'Lạc Thủy', 17, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '11.08');
INSERT INTO `districts` VALUES (164, 'Thái Nguyên', 19, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '22.01');
INSERT INTO `districts` VALUES (165, 'Sông Công', 19, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '22.02');
INSERT INTO `districts` VALUES (167, 'Định Hóa', 19, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '22.05');
INSERT INTO `districts` VALUES (168, 'Phú Lương', 19, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '22.08');
INSERT INTO `districts` VALUES (169, 'Đồng Hỷ', 19, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '22.06');
INSERT INTO `districts` VALUES (170, 'Võ Nhai', 19, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '22.09');
INSERT INTO `districts` VALUES (171, 'Đại Từ', 19, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '22.04');
INSERT INTO `districts` VALUES (172, 'Phổ Yên', 19, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '22.03');
INSERT INTO `districts` VALUES (173, 'Phú Bình', 19, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '22.07');
INSERT INTO `districts` VALUES (178, 'Lạng Sơn', 20, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '14.01');
INSERT INTO `districts` VALUES (180, 'Tràng Định', 20, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '14.09');
INSERT INTO `districts` VALUES (181, 'Bình Gia', 20, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '14.03');
INSERT INTO `districts` VALUES (182, 'Văn Lãng', 20, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '14.10');
INSERT INTO `districts` VALUES (183, 'Cao Lộc', 20, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '14.04');
INSERT INTO `districts` VALUES (184, 'Văn Quan', 20, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '14.11');
INSERT INTO `districts` VALUES (185, 'Bắc Sơn', 20, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '14.02');
INSERT INTO `districts` VALUES (186, 'Hữu Lũng', 20, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '14.07');
INSERT INTO `districts` VALUES (187, 'Chi Lăng', 20, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '14.05');
INSERT INTO `districts` VALUES (188, 'Lộc Bình', 20, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '14.08');
INSERT INTO `districts` VALUES (189, 'Đình Lập', 20, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '14.06');
INSERT INTO `districts` VALUES (193, 'Hạ Long', 22, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '19.01');
INSERT INTO `districts` VALUES (194, 'Móng Cái', 22, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '19.02');
INSERT INTO `districts` VALUES (195, 'Cẩm Phả', 22, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '19.04');
INSERT INTO `districts` VALUES (196, 'Uông Bí', 22, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '19.03');
INSERT INTO `districts` VALUES (198, 'Bình Liêu', 22, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '19.13');
INSERT INTO `districts` VALUES (199, 'Tiên Yên', 22, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '19.11');
INSERT INTO `districts` VALUES (200, 'Đầm Hà', 22, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '19.09');
INSERT INTO `districts` VALUES (201, 'Hải Hà', 22, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '19.12');
INSERT INTO `districts` VALUES (202, 'Ba Chẽ', 22, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '19.14');
INSERT INTO `districts` VALUES (203, 'Vân Đồn', 22, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '19.07');
INSERT INTO `districts` VALUES (204, 'Hoành Bồ', 22, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '19.08');
INSERT INTO `districts` VALUES (205, 'Đông Triều', 22, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '19.06');
INSERT INTO `districts` VALUES (206, 'Yên Hưng', 22, '2018-03-03 00:00:00', '2018-03-03 00:00:00', NULL);
INSERT INTO `districts` VALUES (207, 'Cô Tô', 22, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '19.10');
INSERT INTO `districts` VALUES (213, 'Bắc Giang', 24, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '01.01');
INSERT INTO `districts` VALUES (215, 'Yên Thế', 24, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '01.02');
INSERT INTO `districts` VALUES (216, 'Tân Yên', 24, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '01.03');
INSERT INTO `districts` VALUES (217, 'Lạng Giang', 24, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '01.06');
INSERT INTO `districts` VALUES (218, 'Lục Nam', 24, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '01.08');
INSERT INTO `districts` VALUES (219, 'Lục Ngạn', 24, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '01.04');
INSERT INTO `districts` VALUES (220, 'Sơn Động', 24, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '01.07');
INSERT INTO `districts` VALUES (221, 'Yên Dũng', 24, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '01.10');
INSERT INTO `districts` VALUES (222, 'Việt Yên', 24, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '01.09');
INSERT INTO `districts` VALUES (223, 'Hiệp Hòa', 24, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '01.05');
INSERT INTO `districts` VALUES (227, 'Việt Trì', 25, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '18.01');
INSERT INTO `districts` VALUES (228, 'Phú Thọ', 25, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '18.02');
INSERT INTO `districts` VALUES (230, 'Đoan Hùng', 25, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '18.04');
INSERT INTO `districts` VALUES (231, 'Hạ Hoà', 25, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '18.13');
INSERT INTO `districts` VALUES (232, 'Thanh Ba', 25, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '18.09');
INSERT INTO `districts` VALUES (233, 'Phù Ninh', 25, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '18.06');
INSERT INTO `districts` VALUES (234, 'Yên Lập', 25, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '18.12');
INSERT INTO `districts` VALUES (235, 'Cẩm Khê', 25, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '18.03');
INSERT INTO `districts` VALUES (236, 'Tam Nông', 25, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '18.07');
INSERT INTO `districts` VALUES (237, 'Lâm Thao', 25, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '18.05');
INSERT INTO `districts` VALUES (238, 'Thanh Sơn', 25, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '18.10');
INSERT INTO `districts` VALUES (239, 'Thanh Thuỷ', 25, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '18.11');
INSERT INTO `districts` VALUES (240, 'Tân Sơn', 25, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '18.08');
INSERT INTO `districts` VALUES (243, 'Vĩnh Yên', 26, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '08.01');
INSERT INTO `districts` VALUES (244, 'Phúc Yên', 26, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '08.02');
INSERT INTO `districts` VALUES (246, 'Lập Thạch', 26, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '08.03');
INSERT INTO `districts` VALUES (247, 'Tam Dương', 26, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '08.04');
INSERT INTO `districts` VALUES (248, 'Tam Đảo', 26, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '08.05');
INSERT INTO `districts` VALUES (249, 'Bình Xuyên', 26, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '08.06');
INSERT INTO `districts` VALUES (250, 'Mê Linh', 1, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '24.20');
INSERT INTO `districts` VALUES (251, 'Yên Lạc', 26, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '08.07');
INSERT INTO `districts` VALUES (252, 'Vĩnh Tường', 26, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '08.08');
INSERT INTO `districts` VALUES (253, 'Sông Lô', 26, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '08.09');
INSERT INTO `districts` VALUES (256, 'Bắc Ninh', 27, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '03.01');
INSERT INTO `districts` VALUES (258, 'Yên Phong', 27, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '03.08');
INSERT INTO `districts` VALUES (259, 'Quế Võ', 27, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '03.05');
INSERT INTO `districts` VALUES (260, 'Tiên Du', 27, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '03.07');
INSERT INTO `districts` VALUES (261, 'Từ Sơn', 27, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '03.02');
INSERT INTO `districts` VALUES (262, 'Thuận Thành', 27, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '03.06');
INSERT INTO `districts` VALUES (263, 'Gia Bình', 27, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '03.03');
INSERT INTO `districts` VALUES (264, 'Lương Tài', 27, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '03.04');
INSERT INTO `districts` VALUES (268, 'Hà Đông', 1, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '24.05');
INSERT INTO `districts` VALUES (269, 'Sơn Tây', 1, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '24.13');
INSERT INTO `districts` VALUES (271, 'Ba Vì', 1, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '24.14');
INSERT INTO `districts` VALUES (272, 'Phúc Thọ', 1, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '24.23');
INSERT INTO `districts` VALUES (273, 'Đan Phượng', 1, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '24.16');
INSERT INTO `districts` VALUES (274, 'Hoài Đức', 1, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '24.19');
INSERT INTO `districts` VALUES (275, 'Quốc Oai', 1, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '24.24');
INSERT INTO `districts` VALUES (276, 'Thạch Thất', 1, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '24.26');
INSERT INTO `districts` VALUES (277, 'Chương Mỹ', 1, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '24.15');
INSERT INTO `districts` VALUES (278, 'Thanh Oai', 1, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '24.27');
INSERT INTO `districts` VALUES (279, 'Thường Tín', 1, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '24.29');
INSERT INTO `districts` VALUES (280, 'Phú Xuyên', 1, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '24.22');
INSERT INTO `districts` VALUES (281, 'Ứng Hòa', 1, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '24.30');
INSERT INTO `districts` VALUES (282, 'Mỹ Đức', 1, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '24.21');
INSERT INTO `districts` VALUES (288, 'Hải Dương', 30, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '09.01');
INSERT INTO `districts` VALUES (290, 'Chí Linh', 30, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '09.02');
INSERT INTO `districts` VALUES (291, 'Nam Sách', 30, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '09.03');
INSERT INTO `districts` VALUES (292, 'Kinh Môn', 30, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '09.04');
INSERT INTO `districts` VALUES (293, 'Kim Thành', 30, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '09.05');
INSERT INTO `districts` VALUES (294, 'Thanh Hà', 30, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '09.06');
INSERT INTO `districts` VALUES (295, 'Cẩm Giàng', 30, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '09.07');
INSERT INTO `districts` VALUES (296, 'Bình Giang', 30, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '09.08');
INSERT INTO `districts` VALUES (297, 'Gia Lộc', 30, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '09.09');
INSERT INTO `districts` VALUES (298, 'Tứ Kỳ', 30, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '09.10');
INSERT INTO `districts` VALUES (299, 'Ninh Giang', 30, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '09.11');
INSERT INTO `districts` VALUES (300, 'Thanh Miện', 30, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '09.12');
INSERT INTO `districts` VALUES (303, 'Hồng Bàng', 31, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '10.04');
INSERT INTO `districts` VALUES (304, 'Ngô Quyền', 31, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '10.07');
INSERT INTO `districts` VALUES (305, 'Lê Chân', 31, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '10.06');
INSERT INTO `districts` VALUES (306, 'Hải An', 31, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '10.03');
INSERT INTO `districts` VALUES (307, 'Kiến An', 31, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '10.05');
INSERT INTO `districts` VALUES (308, 'Đồ Sơn', 31, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '10.02');
INSERT INTO `districts` VALUES (309, 'Kinh Dương', 31, '2018-03-03 00:00:00', '2018-03-03 00:00:00', NULL);
INSERT INTO `districts` VALUES (311, 'Thuỷ Nguyên', 31, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '10.12');
INSERT INTO `districts` VALUES (312, 'An Dương', 31, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '10.08');
INSERT INTO `districts` VALUES (313, 'An Lão', 31, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '10.09');
INSERT INTO `districts` VALUES (314, 'Kiến Thụy', 31, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '10.11');
INSERT INTO `districts` VALUES (315, 'Tiên Lãng', 31, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '10.13');
INSERT INTO `districts` VALUES (316, 'Vĩnh Bảo', 31, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '10.14');
INSERT INTO `districts` VALUES (317, 'Cát Hải', 31, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '10.10');
INSERT INTO `districts` VALUES (318, 'Bạch Long Vĩ', 31, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '10.15');
INSERT INTO `districts` VALUES (323, 'Hưng Yên', 33, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '12.01');
INSERT INTO `districts` VALUES (325, 'Văn Lâm', 33, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '12.02');
INSERT INTO `districts` VALUES (326, 'Văn Giang', 33, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '12.03');
INSERT INTO `districts` VALUES (327, 'Yên Mỹ', 33, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '12.04');
INSERT INTO `districts` VALUES (328, 'Mỹ Hào', 33, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '12.05');
INSERT INTO `districts` VALUES (329, 'Ân Thi', 33, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '12.06');
INSERT INTO `districts` VALUES (330, 'Khoái Châu', 33, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '12.07');
INSERT INTO `districts` VALUES (331, 'Kim Động', 33, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '12.08');
INSERT INTO `districts` VALUES (332, 'Tiên Lữ', 33, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '12.09');
INSERT INTO `districts` VALUES (333, 'Phù Cừ', 33, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '12.10');
INSERT INTO `districts` VALUES (336, 'Thái Bình', 34, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '21.01');
INSERT INTO `districts` VALUES (338, 'Quỳnh Phụ', 34, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '21.05');
INSERT INTO `districts` VALUES (339, 'Hưng Hà', 34, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '21.03');
INSERT INTO `districts` VALUES (340, 'Đông Hưng', 34, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '21.02');
INSERT INTO `districts` VALUES (341, 'Thái Thụy', 34, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '21.06');
INSERT INTO `districts` VALUES (342, 'Tiền Hải', 34, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '21.07');
INSERT INTO `districts` VALUES (343, 'Kiến Xương', 34, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '21.04');
INSERT INTO `districts` VALUES (344, 'Vũ Thư', 34, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '21.08');
INSERT INTO `districts` VALUES (347, 'Phủ Lý', 35, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '07.01');
INSERT INTO `districts` VALUES (349, 'Duy Tiên', 35, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '07.02');
INSERT INTO `districts` VALUES (350, 'Kim Bảng', 35, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '07.03');
INSERT INTO `districts` VALUES (351, 'Thanh Liêm', 35, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '07.05');
INSERT INTO `districts` VALUES (352, 'Bình Lục', 35, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '07.06');
INSERT INTO `districts` VALUES (353, 'Lý Nhân', 35, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '07.04');
INSERT INTO `districts` VALUES (356, 'Nam Định', 36, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '16.01');
INSERT INTO `districts` VALUES (358, 'Mỹ Lộc', 36, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '16.04');
INSERT INTO `districts` VALUES (359, 'Vụ Bản', 36, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '16.08');
INSERT INTO `districts` VALUES (360, 'Ý Yên', 36, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '16.10');
INSERT INTO `districts` VALUES (361, 'Nghĩa Hưng', 36, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '16.06');
INSERT INTO `districts` VALUES (362, 'Nam Trực', 36, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '16.05');
INSERT INTO `districts` VALUES (363, 'Trực Ninh', 36, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '16.07');
INSERT INTO `districts` VALUES (364, 'Xuân Trường', 36, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '16.09');
INSERT INTO `districts` VALUES (365, 'Giao Thủy', 36, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '16.02');
INSERT INTO `districts` VALUES (366, 'Hải Hậu', 36, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '16.03');
INSERT INTO `districts` VALUES (369, 'Ninh Bình', 37, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '17.08');
INSERT INTO `districts` VALUES (370, 'Tam Điệp', 37, '2018-03-03 00:00:00', '2018-03-03 00:00:00', NULL);
INSERT INTO `districts` VALUES (372, 'Nho Quan', 37, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '17.04');
INSERT INTO `districts` VALUES (373, 'Gia Viễn', 37, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '17.01');
INSERT INTO `districts` VALUES (374, 'Hoa Lư', 37, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '17.02');
INSERT INTO `districts` VALUES (375, 'Yên Khánh', 37, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '17.05');
INSERT INTO `districts` VALUES (376, 'Kim Sơn', 37, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '17.03');
INSERT INTO `districts` VALUES (377, 'Yên Mô', 37, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '17.06');
INSERT INTO `districts` VALUES (380, 'Thanh Hóa', 38, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '39.01');
INSERT INTO `districts` VALUES (381, 'Bỉm Sơn', 38, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '39.02');
INSERT INTO `districts` VALUES (382, 'Sầm Sơn', 38, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '39.03');
INSERT INTO `districts` VALUES (384, 'Mường Lát', 38, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '39.11');
INSERT INTO `districts` VALUES (385, 'Quan Hóa', 38, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '39.17');
INSERT INTO `districts` VALUES (386, 'Bá Thước', 38, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '39.04');
INSERT INTO `districts` VALUES (387, 'Quan Sơn', 38, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '39.18');
INSERT INTO `districts` VALUES (388, 'Lang Chánh', 38, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '39.10');
INSERT INTO `districts` VALUES (389, 'Ngọc Lặc', 38, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '39.13');
INSERT INTO `districts` VALUES (390, 'Cẩm Thủy', 38, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '39.05');
INSERT INTO `districts` VALUES (391, 'Thạch Thành', 38, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '39.20');
INSERT INTO `districts` VALUES (392, 'Hà Trung', 38, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '39.07');
INSERT INTO `districts` VALUES (393, 'Vĩnh Lộc', 38, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '39.26');
INSERT INTO `districts` VALUES (394, 'Yên Định', 38, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '39.27');
INSERT INTO `districts` VALUES (395, 'Thọ Xuân', 38, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '39.22');
INSERT INTO `districts` VALUES (396, 'Thường Xuân', 38, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '39.23');
INSERT INTO `districts` VALUES (397, 'Triệu Sơn', 38, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '39.25');
INSERT INTO `districts` VALUES (398, 'Thiệu Hoá', 38, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '39.21');
INSERT INTO `districts` VALUES (399, 'Hoằng Hóa', 38, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '39.09');
INSERT INTO `districts` VALUES (400, 'Hậu Lộc', 38, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '39.08');
INSERT INTO `districts` VALUES (401, 'Nga Sơn', 38, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '39.12');
INSERT INTO `districts` VALUES (402, 'Như Xuân', 38, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '39.15');
INSERT INTO `districts` VALUES (403, 'Như Thanh', 38, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '39.14');
INSERT INTO `districts` VALUES (404, 'Nông Cống', 38, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '39.16');
INSERT INTO `districts` VALUES (405, 'Đông Sơn', 38, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '39.06');
INSERT INTO `districts` VALUES (406, 'Quảng Xương', 38, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '39.19');
INSERT INTO `districts` VALUES (407, 'Tĩnh Gia', 38, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '39.24');
INSERT INTO `districts` VALUES (412, 'Vinh', 40, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '33.01');
INSERT INTO `districts` VALUES (413, 'Cửa Lò', 40, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '33.02');
INSERT INTO `districts` VALUES (414, 'Thái Hoà', 40, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '33.04');
INSERT INTO `districts` VALUES (415, 'Quế Phong', 40, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '33.15');
INSERT INTO `districts` VALUES (416, 'Quỳ Châu', 40, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '33.10');
INSERT INTO `districts` VALUES (417, 'Kỳ Sơn', 40, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '33.11');
INSERT INTO `districts` VALUES (418, 'Tương Dương', 40, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '33.19');
INSERT INTO `districts` VALUES (419, 'Nghĩa Đàn', 40, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '33.14');
INSERT INTO `districts` VALUES (420, 'Quỳ Hợp', 40, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '33.16');
INSERT INTO `districts` VALUES (421, 'Quỳnh Lưu', 40, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '33.17');
INSERT INTO `districts` VALUES (422, 'Con Cuông', 40, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '33.06');
INSERT INTO `districts` VALUES (423, 'Tân Kỳ', 40, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '33.18');
INSERT INTO `districts` VALUES (424, 'Anh Sơn', 40, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '33.05');
INSERT INTO `districts` VALUES (425, 'Diễn Châu', 40, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '33.07');
INSERT INTO `districts` VALUES (426, 'Yên Thành', 40, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '33.20');
INSERT INTO `districts` VALUES (427, 'Đô Lương', 40, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '33.08');
INSERT INTO `districts` VALUES (428, 'Thanh Chương', 40, '2018-03-03 00:00:00', '2018-03-03 00:00:00', NULL);
INSERT INTO `districts` VALUES (429, 'Nghi Lộc', 40, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '33.13');
INSERT INTO `districts` VALUES (430, 'Nam Đàn', 40, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '33.12');
INSERT INTO `districts` VALUES (431, 'Hưng Nguyên', 40, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '33.09');
INSERT INTO `districts` VALUES (436, 'Hà Tĩnh', 42, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '30.01');
INSERT INTO `districts` VALUES (437, 'Hồng Lĩnh', 42, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '30.02');
INSERT INTO `districts` VALUES (439, 'Hương Sơn', 42, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '30.08');
INSERT INTO `districts` VALUES (440, 'Đức Thọ', 42, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '30.06');
INSERT INTO `districts` VALUES (441, 'Vũ Quang', 42, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '30.12');
INSERT INTO `districts` VALUES (442, 'Nghi Xuân', 42, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '30.10');
INSERT INTO `districts` VALUES (443, 'Can Lộc', 42, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '30.05');
INSERT INTO `districts` VALUES (444, 'Hương Khê', 42, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '30.07');
INSERT INTO `districts` VALUES (445, 'Thạch Hà', 42, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '30.11');
INSERT INTO `districts` VALUES (446, 'Cẩm Xuyên', 42, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '30.04');
INSERT INTO `districts` VALUES (447, 'Kỳ Anh', 42, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '30.09');
INSERT INTO `districts` VALUES (448, 'Lộc Hà', 42, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '30.13');
INSERT INTO `districts` VALUES (450, 'Đồng Hới', 44, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '35.01');
INSERT INTO `districts` VALUES (452, 'Minh Hóa', 44, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '35.03');
INSERT INTO `districts` VALUES (453, 'Tuyên Hóa', 44, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '35.04');
INSERT INTO `districts` VALUES (454, 'Quảng Trạch', 44, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '35.05');
INSERT INTO `districts` VALUES (455, 'Bố Trạch', 44, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '35.06');
INSERT INTO `districts` VALUES (456, 'Quảng Ninh', 44, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '35.07');
INSERT INTO `districts` VALUES (457, 'Lệ Thủy', 44, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '35.08');
INSERT INTO `districts` VALUES (461, 'Đông Hà', 45, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '38.01');
INSERT INTO `districts` VALUES (462, 'Quảng Trị', 45, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '38.02');
INSERT INTO `districts` VALUES (464, 'Vĩnh Linh', 45, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '38.09');
INSERT INTO `districts` VALUES (465, 'Hướng Hóa', 45, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '38.07');
INSERT INTO `districts` VALUES (466, 'Gio Linh', 45, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '38.05');
INSERT INTO `districts` VALUES (467, 'Đa Krông', 45, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '38.04');
INSERT INTO `districts` VALUES (468, 'Cam Lộ', 45, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '38.03');
INSERT INTO `districts` VALUES (469, 'Triệu Phong', 45, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '38.08');
INSERT INTO `districts` VALUES (470, 'Hải Lăng', 45, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '38.06');
INSERT INTO `districts` VALUES (471, 'Cồn Cỏ', 45, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '38.10');
INSERT INTO `districts` VALUES (474, 'Huế', 46, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '40.01');
INSERT INTO `districts` VALUES (476, 'Phong Điền', 46, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '40.06');
INSERT INTO `districts` VALUES (477, 'Quảng Điền', 46, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '40.03');
INSERT INTO `districts` VALUES (478, 'Phú Vang', 46, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '40.02');
INSERT INTO `districts` VALUES (479, 'Hương Thủy', 46, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '40.08');
INSERT INTO `districts` VALUES (480, 'Hương Trà', 46, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '40.09');
INSERT INTO `districts` VALUES (481, 'A Lưới', 46, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '40.04');
INSERT INTO `districts` VALUES (482, 'Phú Lộc', 46, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '40.07');
INSERT INTO `districts` VALUES (483, 'Nam Đông', 46, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '40.05');
INSERT INTO `districts` VALUES (490, 'Liên Chiểu', 48, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '28.05');
INSERT INTO `districts` VALUES (491, 'Thanh Khê', 48, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '28.02');
INSERT INTO `districts` VALUES (492, 'Hải Châu', 48, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '28.01');
INSERT INTO `districts` VALUES (493, 'Sơn Trà', 48, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '28.03');
INSERT INTO `districts` VALUES (494, 'Ngũ Hành Sơn', 48, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '28.04');
INSERT INTO `districts` VALUES (495, 'Cẩm Lệ', 48, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '28.06');
INSERT INTO `districts` VALUES (497, 'Hoà Vang', 48, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '28.07');
INSERT INTO `districts` VALUES (498, 'Hoàng Sa', 48, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '28.08');
INSERT INTO `districts` VALUES (502, 'Tam Kỳ', 49, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '36.01');
INSERT INTO `districts` VALUES (503, 'Hội An', 49, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '36.02');
INSERT INTO `districts` VALUES (504, 'Tây Giang', 49, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '36.15');
INSERT INTO `districts` VALUES (505, 'Đông Giang', 49, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '36.11');
INSERT INTO `districts` VALUES (506, 'Đại Lộc', 49, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '36.13');
INSERT INTO `districts` VALUES (507, 'Điện Bàn', 49, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '36.03');
INSERT INTO `districts` VALUES (508, 'Duy Xuyên', 49, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '36.16');
INSERT INTO `districts` VALUES (509, 'Quế Sơn', 49, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '36.17');
INSERT INTO `districts` VALUES (510, 'Nam Giang', 49, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '36.12');
INSERT INTO `districts` VALUES (511, 'Phước Sơn', 49, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '36.07');
INSERT INTO `districts` VALUES (512, 'Hiệp Đức', 49, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '36.09');
INSERT INTO `districts` VALUES (513, 'Thăng Bình', 49, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '36.04');
INSERT INTO `districts` VALUES (514, 'Tiên Phước', 49, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '36.08');
INSERT INTO `districts` VALUES (515, 'Bắc Trà My', 49, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '36.18');
INSERT INTO `districts` VALUES (516, 'Nam Trà My', 49, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '36.05');
INSERT INTO `districts` VALUES (517, 'Núi Thành', 49, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '36.06');
INSERT INTO `districts` VALUES (518, 'Phú Ninh', 49, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '36.14');
INSERT INTO `districts` VALUES (519, 'Nông Sơn', 49, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '36.10');
INSERT INTO `districts` VALUES (522, 'Quảng Ngãi', 51, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '37.01');
INSERT INTO `districts` VALUES (524, 'Bình Sơn', 51, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '37.03');
INSERT INTO `districts` VALUES (525, 'Trà Bồng', 51, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '37.08');
INSERT INTO `districts` VALUES (526, 'Tây Trà', 51, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '37.09');
INSERT INTO `districts` VALUES (527, 'Sơn Tịnh', 51, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '37.10');
INSERT INTO `districts` VALUES (528, 'Tư Nghĩa', 51, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '37.07');
INSERT INTO `districts` VALUES (529, 'Sơn Hà', 51, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '37.12');
INSERT INTO `districts` VALUES (530, 'Sơn Tây', 51, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '37.11');
INSERT INTO `districts` VALUES (531, 'Minh Long', 51, '2018-03-03 00:00:00', '2018-03-03 00:00:00', NULL);
INSERT INTO `districts` VALUES (532, 'Nghĩa Hành', 51, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '37.13');
INSERT INTO `districts` VALUES (533, 'Mộ Đức', 51, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '37.05');
INSERT INTO `districts` VALUES (534, 'Đức Phổ', 51, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '37.04');
INSERT INTO `districts` VALUES (535, 'Ba Tơ', 51, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '37.02');
INSERT INTO `districts` VALUES (536, 'Lý Sơn', 51, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '37.06');
INSERT INTO `districts` VALUES (540, 'Qui Nhơn', 52, '2018-03-03 00:00:00', '2018-03-03 00:00:00', NULL);
INSERT INTO `districts` VALUES (542, 'An Lão', 52, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '26.04');
INSERT INTO `districts` VALUES (543, 'Hoài Nhơn', 52, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '26.03');
INSERT INTO `districts` VALUES (544, 'Hoài Ân', 52, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '26.05');
INSERT INTO `districts` VALUES (545, 'Phù Mỹ', 52, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '26.06');
INSERT INTO `districts` VALUES (546, 'Vĩnh Thạnh', 52, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '26.07');
INSERT INTO `districts` VALUES (547, 'Tây Sơn', 52, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '26.08');
INSERT INTO `districts` VALUES (548, 'Phù Cát', 52, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '26.09');
INSERT INTO `districts` VALUES (549, 'An Nhơn', 52, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '26.02');
INSERT INTO `districts` VALUES (550, 'Tuy Phước', 52, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '26.10');
INSERT INTO `districts` VALUES (551, 'Vân Canh', 52, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '26.11');
INSERT INTO `districts` VALUES (555, 'Tuy Hòa', 54, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '34.01');
INSERT INTO `districts` VALUES (557, 'Sông Cầu', 54, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '34.02');
INSERT INTO `districts` VALUES (558, 'Đồng Xuân', 54, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '34.04');
INSERT INTO `districts` VALUES (559, 'Tuy An', 54, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '34.08');
INSERT INTO `districts` VALUES (560, 'Sơn Hòa', 54, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '34.06');
INSERT INTO `districts` VALUES (561, 'Sông Hinh', 54, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '34.07');
INSERT INTO `districts` VALUES (562, 'Tây Hoà', 54, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '34.09');
INSERT INTO `districts` VALUES (563, 'Phú Hoà', 54, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '34.05');
INSERT INTO `districts` VALUES (564, 'Đông Hoà', 54, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '34.03');
INSERT INTO `districts` VALUES (568, 'Nha Trang', 56, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '31.01');
INSERT INTO `districts` VALUES (569, 'Cam Ranh', 56, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '31.02');
INSERT INTO `districts` VALUES (570, 'Cam Lâm', 56, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '31.08');
INSERT INTO `districts` VALUES (571, 'Vạn Ninh', 56, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '31.04');
INSERT INTO `districts` VALUES (572, 'Ninh Hòa', 56, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '31.03');
INSERT INTO `districts` VALUES (573, 'Khánh Vĩnh', 56, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '31.06');
INSERT INTO `districts` VALUES (574, 'Diên Khánh', 56, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '31.05');
INSERT INTO `districts` VALUES (575, 'Khánh Sơn', 56, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '31.07');
INSERT INTO `districts` VALUES (576, 'Trường Sa', 56, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '31.09');
INSERT INTO `districts` VALUES (582, 'Phan Rang-Tháp Chàm', 58, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '58.01');
INSERT INTO `districts` VALUES (584, 'Bác Ái', 58, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '58.02');
INSERT INTO `districts` VALUES (585, 'Ninh Sơn', 58, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '58.05');
INSERT INTO `districts` VALUES (586, 'Ninh Hải', 58, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '58.03');
INSERT INTO `districts` VALUES (587, 'Ninh Phước', 58, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '58.04');
INSERT INTO `districts` VALUES (588, 'Thuận Bắc', 58, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '58.06');
INSERT INTO `districts` VALUES (589, 'Thuận Nam', 58, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '58.07');
INSERT INTO `districts` VALUES (593, 'Phan Thiết', 60, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '47.01');
INSERT INTO `districts` VALUES (594, 'La Gi', 60, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '47.02');
INSERT INTO `districts` VALUES (595, 'Tuy Phong', 60, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '47.03');
INSERT INTO `districts` VALUES (596, 'Bắc Bình', 60, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '47.04');
INSERT INTO `districts` VALUES (597, 'Hàm Thuận Bắc', 60, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '47.05');
INSERT INTO `districts` VALUES (598, 'Hàm Thuận Nam', 60, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '47.06');
INSERT INTO `districts` VALUES (599, 'Tánh Linh', 60, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '47.07');
INSERT INTO `districts` VALUES (600, 'Đức Linh', 60, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '47.09');
INSERT INTO `districts` VALUES (601, 'Hàm Tân', 60, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '47.08');
INSERT INTO `districts` VALUES (602, 'Phú Quí', 60, '2018-03-03 00:00:00', '2018-03-03 00:00:00', NULL);
INSERT INTO `districts` VALUES (608, 'Kon Tum', 62, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '32.01');
INSERT INTO `districts` VALUES (610, 'Đắk Glei', 62, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '32.02');
INSERT INTO `districts` VALUES (611, 'Ngọc Hồi', 62, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '32.08');
INSERT INTO `districts` VALUES (612, 'Đắk Tô', 62, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '32.04');
INSERT INTO `districts` VALUES (613, 'Kon Plông', 62, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '32.06');
INSERT INTO `districts` VALUES (614, 'Kon Rẫy', 62, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '32.07');
INSERT INTO `districts` VALUES (615, 'Đắk Hà', 62, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '32.03');
INSERT INTO `districts` VALUES (616, 'Sa Thầy', 62, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '32.09');
INSERT INTO `districts` VALUES (617, 'Tu Mơ Rông', 62, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '32.10');
INSERT INTO `districts` VALUES (622, 'Pleiku', 64, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '52.01');
INSERT INTO `districts` VALUES (623, 'An Khê', 64, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '52.02');
INSERT INTO `districts` VALUES (624, 'Ayun Pa', 64, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '52.03');
INSERT INTO `districts` VALUES (625, 'K\'bang', 64, '2018-03-03 00:00:00', '2018-03-03 00:00:00', NULL);
INSERT INTO `districts` VALUES (626, 'Đăk Đoa', 64, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '52.07');
INSERT INTO `districts` VALUES (627, 'Chư Păh', 64, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '52.04');
INSERT INTO `districts` VALUES (628, 'Ia Grai', 64, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '52.15');
INSERT INTO `districts` VALUES (629, 'Mang Yang', 64, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '52.10');
INSERT INTO `districts` VALUES (630, 'Kông Chro', 64, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '52.12');
INSERT INTO `districts` VALUES (631, 'Đức Cơ', 64, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '52.16');
INSERT INTO `districts` VALUES (632, 'Chư Prông', 64, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '52.05');
INSERT INTO `districts` VALUES (633, 'Chư Sê', 64, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '52.06');
INSERT INTO `districts` VALUES (634, 'Đăk Pơ', 64, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '52.17');
INSERT INTO `districts` VALUES (635, 'Ia Pa', 64, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '52.14');
INSERT INTO `districts` VALUES (637, 'Krông Pa', 64, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '52.11');
INSERT INTO `districts` VALUES (638, 'Phú Thiện', 64, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '52.09');
INSERT INTO `districts` VALUES (639, 'Chư Pưh', 64, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '52.08');
INSERT INTO `districts` VALUES (643, 'Buôn Ma Thuột', 66, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '29.01');
INSERT INTO `districts` VALUES (644, 'Buôn Hồ', 66, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '29.02');
INSERT INTO `districts` VALUES (645, 'Ea H\'leo', 66, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '29.12');
INSERT INTO `districts` VALUES (646, 'Ea Súp', 66, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '29.03');
INSERT INTO `districts` VALUES (647, 'Buôn Đôn', 66, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '29.15');
INSERT INTO `districts` VALUES (648, 'Cư M\'gar', 66, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '29.13');
INSERT INTO `districts` VALUES (649, 'Krông Búk', 66, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '29.05');
INSERT INTO `districts` VALUES (650, 'Krông Năng', 66, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '29.07');
INSERT INTO `districts` VALUES (651, 'Ea Kar', 66, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '29.11');
INSERT INTO `districts` VALUES (652, 'M\'đrắk', 66, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '29.09');
INSERT INTO `districts` VALUES (653, 'Krông Bông', 66, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '29.04');
INSERT INTO `districts` VALUES (654, 'Krông Pắk', 66, '2018-03-03 00:00:00', '2018-03-03 00:00:00', NULL);
INSERT INTO `districts` VALUES (655, 'Krông A Na', 66, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '29.08');
INSERT INTO `districts` VALUES (656, 'Lắk', 66, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '29.10');
INSERT INTO `districts` VALUES (657, 'Cư Kuin', 66, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '29.14');
INSERT INTO `districts` VALUES (660, 'Gia Nghĩa', 67, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '49.01');
INSERT INTO `districts` VALUES (661, 'Đắk Glong', 67, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '49.03');
INSERT INTO `districts` VALUES (662, 'Cư Jút', 67, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '49.02');
INSERT INTO `districts` VALUES (663, 'Đắk Mil', 67, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '49.04');
INSERT INTO `districts` VALUES (664, 'Krông Nô', 67, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '49.07');
INSERT INTO `districts` VALUES (665, 'Đắk Song', 67, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '49.06');
INSERT INTO `districts` VALUES (666, 'Đắk R\'lấp', 67, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '49.05');
INSERT INTO `districts` VALUES (667, 'Tuy Đức', 67, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '49.08');
INSERT INTO `districts` VALUES (672, 'Đà Lạt', 68, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '56.01');
INSERT INTO `districts` VALUES (673, 'Bảo Lộc', 68, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '56.02');
INSERT INTO `districts` VALUES (674, 'Đam Rông', 68, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '56.06');
INSERT INTO `districts` VALUES (675, 'Lạc Dương', 68, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '56.10');
INSERT INTO `districts` VALUES (676, 'Lâm Hà', 68, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '56.11');
INSERT INTO `districts` VALUES (677, 'Đơn Dương', 68, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '56.09');
INSERT INTO `districts` VALUES (678, 'Đức Trọng', 68, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '56.12');
INSERT INTO `districts` VALUES (679, 'Di Linh', 68, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '56.05');
INSERT INTO `districts` VALUES (680, 'Bảo Lâm', 68, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '56.03');
INSERT INTO `districts` VALUES (681, 'Đạ Huoai', 68, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '56.07');
INSERT INTO `districts` VALUES (682, 'Đạ Tẻh', 68, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '56.08');
INSERT INTO `districts` VALUES (683, 'Cát Tiên', 68, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '56.04');
INSERT INTO `districts` VALUES (688, 'Phước Long', 70, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '46.03');
INSERT INTO `districts` VALUES (689, 'Đồng Xoài', 70, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '46.01');
INSERT INTO `districts` VALUES (690, 'Bình Long', 70, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '46.02');
INSERT INTO `districts` VALUES (691, 'Bù Gia Mập', 70, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '46.06');
INSERT INTO `districts` VALUES (692, 'Lộc Ninh', 70, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '46.10');
INSERT INTO `districts` VALUES (693, 'Bù Đốp', 70, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '46.05');
INSERT INTO `districts` VALUES (694, 'Hớn Quản', 70, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '46.09');
INSERT INTO `districts` VALUES (695, 'Đồng Phù', 70, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '46.08');
INSERT INTO `districts` VALUES (696, 'Bù Đăng', 70, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '46.04');
INSERT INTO `districts` VALUES (697, 'Chơn Thành', 70, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '46.07');
INSERT INTO `districts` VALUES (703, 'Tây Ninh', 72, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '60.01');
INSERT INTO `districts` VALUES (705, 'Tân Biên', 72, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '60.02');
INSERT INTO `districts` VALUES (706, 'Tân Châu', 72, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '60.03');
INSERT INTO `districts` VALUES (707, 'Dương Minh Châu', 72, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '60.04');
INSERT INTO `districts` VALUES (708, 'Châu Thành', 72, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '60.05');
INSERT INTO `districts` VALUES (709, 'Hòa Thành', 72, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '60.06');
INSERT INTO `districts` VALUES (710, 'Gò Dầu', 72, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '60.08');
INSERT INTO `districts` VALUES (711, 'Bến Cầu', 72, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '60.07');
INSERT INTO `districts` VALUES (712, 'Trảng Bàng', 72, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '60.09');
INSERT INTO `districts` VALUES (718, 'Thủ Dầu Một', 74, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '45.01');
INSERT INTO `districts` VALUES (720, 'Dầu Tiếng', 74, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '45.06');
INSERT INTO `districts` VALUES (721, 'Bến Cát', 74, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '45.05');
INSERT INTO `districts` VALUES (722, 'Phú Giáo', 74, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '45.07');
INSERT INTO `districts` VALUES (723, 'Tân Uyên', 74, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '45.04');
INSERT INTO `districts` VALUES (724, 'Dĩ An', 74, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '45.03');
INSERT INTO `districts` VALUES (725, 'Thuận An', 74, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '45.02');
INSERT INTO `districts` VALUES (731, 'Biên Hòa', 75, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '50.01');
INSERT INTO `districts` VALUES (732, 'Long Khánh', 75, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '50.02');
INSERT INTO `districts` VALUES (734, 'Tân Phú', 75, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '50.09');
INSERT INTO `districts` VALUES (735, 'Vĩnh Cửu', 75, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '50.06');
INSERT INTO `districts` VALUES (736, 'Định Quán', 75, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '50.10');
INSERT INTO `districts` VALUES (737, 'Trảng Bom', 75, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '50.04');
INSERT INTO `districts` VALUES (738, 'Thống Nhất', 75, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '50.05');
INSERT INTO `districts` VALUES (739, 'Cẩm Mỹ', 75, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '50.07');
INSERT INTO `districts` VALUES (740, 'Long Thành', 75, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '50.02');
INSERT INTO `districts` VALUES (741, 'Xuân Lộc', 75, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '50.08');
INSERT INTO `districts` VALUES (742, 'Nhơn Trạch', 75, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '50.03');
INSERT INTO `districts` VALUES (747, 'Vũng Tầu', 77, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '42.01');
INSERT INTO `districts` VALUES (748, 'Bà Rịa', 77, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '42.02');
INSERT INTO `districts` VALUES (750, 'Châu Đức', 77, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '42.05');
INSERT INTO `districts` VALUES (751, 'Xuyên Mộc', 77, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '42.08');
INSERT INTO `districts` VALUES (752, 'Long Điền', 77, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '42.03');
INSERT INTO `districts` VALUES (753, 'Đất Đỏ', 77, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '42.04');
INSERT INTO `districts` VALUES (754, 'Tân Thành', 77, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '42.06');
INSERT INTO `districts` VALUES (755, 'Côn Đảo', 77, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '42.07');
INSERT INTO `districts` VALUES (760, 'Quận 1', 79, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '54.01');
INSERT INTO `districts` VALUES (761, 'Quận 12', 79, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '54.02');
INSERT INTO `districts` VALUES (762, 'Thủ Đức', 79, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '54.03');
INSERT INTO `districts` VALUES (763, 'Quận 9', 79, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '54.04');
INSERT INTO `districts` VALUES (764, 'Gò Vấp', 79, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '54.05');
INSERT INTO `districts` VALUES (765, 'Bình Thạnh', 79, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '54.06');
INSERT INTO `districts` VALUES (766, 'Tân Bình', 79, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '54.07');
INSERT INTO `districts` VALUES (767, 'Tân Phú', 79, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '54.08');
INSERT INTO `districts` VALUES (768, 'Phú Nhuận', 79, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '54.09');
INSERT INTO `districts` VALUES (769, 'Quận 2', 79, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '54.10');
INSERT INTO `districts` VALUES (770, 'Quận 3', 79, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '54.11');
INSERT INTO `districts` VALUES (771, 'Quận 10', 79, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '54.12');
INSERT INTO `districts` VALUES (772, 'Quận 11', 79, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '54.13');
INSERT INTO `districts` VALUES (773, 'Quận 4', 79, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '54.14');
INSERT INTO `districts` VALUES (774, 'Quận 5', 79, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '54.15');
INSERT INTO `districts` VALUES (775, 'Quận 6', 79, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '54.16');
INSERT INTO `districts` VALUES (776, 'Quận 8', 79, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '54.17');
INSERT INTO `districts` VALUES (777, 'Bình Tân', 79, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '54.18');
INSERT INTO `districts` VALUES (778, 'Quận 7', 79, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '54.19');
INSERT INTO `districts` VALUES (783, 'Củ Chi', 79, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '54.20');
INSERT INTO `districts` VALUES (784, 'Hóc Môn', 79, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '54.21');
INSERT INTO `districts` VALUES (785, 'Bình Chánh', 79, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '54.22');
INSERT INTO `districts` VALUES (786, 'Nhà Bè', 79, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '54.23');
INSERT INTO `districts` VALUES (787, 'Cần Giờ', 79, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '54.24');
INSERT INTO `districts` VALUES (794, 'Tân An', 80, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '57.01');
INSERT INTO `districts` VALUES (796, 'Tân Hưng', 80, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '57.09');
INSERT INTO `districts` VALUES (797, 'Vĩnh Hưng', 80, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '57.14');
INSERT INTO `districts` VALUES (798, 'Mộc Hóa', 80, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '57.08');
INSERT INTO `districts` VALUES (799, 'Tân Thạnh', 80, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '57.10');
INSERT INTO `districts` VALUES (800, 'Thạnh Hóa', 80, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '57.12');
INSERT INTO `districts` VALUES (801, 'Đức Huệ', 80, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '57.07');
INSERT INTO `districts` VALUES (802, 'Đức Hòa', 80, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '57.15');
INSERT INTO `districts` VALUES (803, 'Bến Lức', 80, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '57.03');
INSERT INTO `districts` VALUES (804, 'Thủ Thừa', 80, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '57.13');
INSERT INTO `districts` VALUES (805, 'Tân Trụ', 80, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '57.11');
INSERT INTO `districts` VALUES (806, 'Cần Đước', 80, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '57.04');
INSERT INTO `districts` VALUES (807, 'Cần Giuộc', 80, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '57.05');
INSERT INTO `districts` VALUES (808, 'Châu Thành', 80, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '57.06');
INSERT INTO `districts` VALUES (815, 'Mỹ Tho', 82, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '61.01');
INSERT INTO `districts` VALUES (816, 'Gò Công', 82, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '61.02');
INSERT INTO `districts` VALUES (818, 'Tân Phước', 82, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '61.09');
INSERT INTO `districts` VALUES (819, 'Cái Bè', 82, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '61.04');
INSERT INTO `districts` VALUES (820, 'Cai Lậy', 82, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '61.10');
INSERT INTO `districts` VALUES (821, 'Châu Thành', 82, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '61.08');
INSERT INTO `districts` VALUES (822, 'Chợ Gạo', 82, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '61.07');
INSERT INTO `districts` VALUES (823, 'Gò Công Tây', 82, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '61.06');
INSERT INTO `districts` VALUES (824, 'Gò Công Đông', 82, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '61.05');
INSERT INTO `districts` VALUES (825, 'Tân Phú Đông', 82, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '61.11');
INSERT INTO `districts` VALUES (829, 'Bến Tre', 83, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '44.01');
INSERT INTO `districts` VALUES (831, 'Châu Thành', 83, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '44.04');
INSERT INTO `districts` VALUES (832, 'Chợ Lách', 83, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '44.09');
INSERT INTO `districts` VALUES (833, 'Mỏ Cày Nam', 83, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '44.07');
INSERT INTO `districts` VALUES (834, 'Giồng Trôm', 83, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '44.05');
INSERT INTO `districts` VALUES (835, 'Bình Đại', 83, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '44.03');
INSERT INTO `districts` VALUES (836, 'Ba Tri', 83, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '44.02');
INSERT INTO `districts` VALUES (837, 'Thạnh Phú', 83, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '44.08');
INSERT INTO `districts` VALUES (838, 'Mỏ Cày Bắc', 83, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '44.06');
INSERT INTO `districts` VALUES (842, 'Trà Vinh', 84, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '62.01');
INSERT INTO `districts` VALUES (844, 'Càng Long', 84, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '62.03');
INSERT INTO `districts` VALUES (845, 'Cầu Kè', 84, '2018-03-03 00:00:00', '2018-03-03 00:00:00', NULL);
INSERT INTO `districts` VALUES (846, 'Tiểu Cần', 84, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '62.04');
INSERT INTO `districts` VALUES (847, 'Châu Thành', 84, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '62.05');
INSERT INTO `districts` VALUES (848, 'Cầu Ngang', 84, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '62.06');
INSERT INTO `districts` VALUES (849, 'Trà Cú', 84, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '62.07');
INSERT INTO `districts` VALUES (850, 'Duyên Hải', 84, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '62.08');
INSERT INTO `districts` VALUES (855, 'Vĩnh Long', 86, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '63.01');
INSERT INTO `districts` VALUES (857, 'Long Hồ', 86, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '63.04');
INSERT INTO `districts` VALUES (858, 'Mang Thít', 86, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '63.05');
INSERT INTO `districts` VALUES (859, 'Vũng Liêm', 86, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '63.08');
INSERT INTO `districts` VALUES (860, 'Tam Bình', 86, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '63.06');
INSERT INTO `districts` VALUES (861, 'Bình Minh', 86, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '63.02');
INSERT INTO `districts` VALUES (862, 'Trà Ôn', 86, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '63.07');
INSERT INTO `districts` VALUES (863, 'Bình Tân', 86, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '63.03');
INSERT INTO `districts` VALUES (866, 'Cao Lãnh', 87, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '51.03');
INSERT INTO `districts` VALUES (867, 'Sa Đéc', 87, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '51.02');
INSERT INTO `districts` VALUES (868, 'Hồng Ngự', 87, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '51.05');
INSERT INTO `districts` VALUES (869, 'Tân Hồng', 87, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '51.09');
INSERT INTO `districts` VALUES (870, 'Hồng Ngự', 87, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '51.05');
INSERT INTO `districts` VALUES (871, 'Tam Nông', 87, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '51.08');
INSERT INTO `districts` VALUES (872, 'Tháp Mười', 87, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '51.11');
INSERT INTO `districts` VALUES (873, 'Cao Lãnh', 87, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '51.03');
INSERT INTO `districts` VALUES (874, 'Thanh Bình', 87, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '51.10');
INSERT INTO `districts` VALUES (875, 'Lấp Vò', 87, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '51.07');
INSERT INTO `districts` VALUES (876, 'Lai Vung', 87, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '51.06');
INSERT INTO `districts` VALUES (877, 'Châu Thành', 87, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '51.04');
INSERT INTO `districts` VALUES (883, 'Long Xuyên', 89, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '41.01');
INSERT INTO `districts` VALUES (884, 'Châu Đốc', 89, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '41.02');
INSERT INTO `districts` VALUES (886, 'An Phú', 89, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '41.04');
INSERT INTO `districts` VALUES (887, 'Tân Châu', 89, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '41.03');
INSERT INTO `districts` VALUES (888, 'Phú Tân', 89, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '41.08');
INSERT INTO `districts` VALUES (889, 'Châu Phú', 89, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '41.05');
INSERT INTO `districts` VALUES (890, 'Tịnh Biên', 89, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '41.10');
INSERT INTO `districts` VALUES (891, 'Tri Tôn', 89, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '41.11');
INSERT INTO `districts` VALUES (892, 'Châu Thành', 89, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '41.06');
INSERT INTO `districts` VALUES (893, 'Chợ Mới', 89, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '41.07');
INSERT INTO `districts` VALUES (894, 'Thoại Sơn', 89, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '41.09');
INSERT INTO `districts` VALUES (899, 'Rạch Giá', 91, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '55.01');
INSERT INTO `districts` VALUES (900, 'Hà Tiên', 91, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '55.02');
INSERT INTO `districts` VALUES (902, 'Kiên Lương', 91, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '55.11');
INSERT INTO `districts` VALUES (903, 'Hòn Đất', 91, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '55.09');
INSERT INTO `districts` VALUES (904, 'Tân Hiệp', 91, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '55.12');
INSERT INTO `districts` VALUES (905, 'Châu Thành', 91, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '55.05');
INSERT INTO `districts` VALUES (906, 'Giồng Riềng', 91, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '55.06');
INSERT INTO `districts` VALUES (907, 'Gò Quao', 91, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '55.08');
INSERT INTO `districts` VALUES (908, 'An Biên', 91, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '55.03');
INSERT INTO `districts` VALUES (909, 'An Minh', 91, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '55.04');
INSERT INTO `districts` VALUES (910, 'Vĩnh Thuận', 91, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '55.13');
INSERT INTO `districts` VALUES (911, 'Phú Quốc', 91, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '55.15');
INSERT INTO `districts` VALUES (912, 'Kiên Hải', 91, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '55.14');
INSERT INTO `districts` VALUES (913, 'U Minh Thượng', 91, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '55.10');
INSERT INTO `districts` VALUES (914, 'Giang Thành', 91, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '55.07');
INSERT INTO `districts` VALUES (916, 'Ninh Kiều', 92, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '27.01');
INSERT INTO `districts` VALUES (917, 'Ô Môn', 92, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '27.04');
INSERT INTO `districts` VALUES (918, 'Bình Thuỷ', 92, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '27.02');
INSERT INTO `districts` VALUES (919, 'Cái Răng', 92, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '27.03');
INSERT INTO `districts` VALUES (923, 'Thốt Nốt', 92, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '27.05');
INSERT INTO `districts` VALUES (924, 'Vĩnh Thạnh', 92, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '27.09');
INSERT INTO `districts` VALUES (925, 'Cờ Đỏ', 92, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '27.07');
INSERT INTO `districts` VALUES (926, 'Phong Điền', 92, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '27.06');
INSERT INTO `districts` VALUES (927, 'Thới Lai', 92, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '27.08');
INSERT INTO `districts` VALUES (930, 'Vị Thanh', 93, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '53.01');
INSERT INTO `districts` VALUES (931, 'Ngã Bảy', 93, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '53.03');
INSERT INTO `districts` VALUES (932, 'Châu Thành A', 93, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '53.05');
INSERT INTO `districts` VALUES (933, 'Châu Thành', 93, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '53.04');
INSERT INTO `districts` VALUES (934, 'Phụng Hiệp', 93, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '53.07');
INSERT INTO `districts` VALUES (935, 'Vị Thuỷ', 93, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '53.08');
INSERT INTO `districts` VALUES (936, 'Long Mỹ', 93, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '53.06');
INSERT INTO `districts` VALUES (941, 'Sóc Trăng', 94, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '59.01');
INSERT INTO `districts` VALUES (942, 'Châu Thành', 94, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '59.10');
INSERT INTO `districts` VALUES (943, 'Kế Sách', 94, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '59.05');
INSERT INTO `districts` VALUES (944, 'Mỹ Tú', 94, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '59.06');
INSERT INTO `districts` VALUES (945, 'Cù Lao Dung', 94, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '59.11');
INSERT INTO `districts` VALUES (946, 'Long Phú', 94, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '59.04');
INSERT INTO `districts` VALUES (947, 'Mỹ Xuyên', 94, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '59.07');
INSERT INTO `districts` VALUES (948, 'Ngã Năm', 94, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '59.03');
INSERT INTO `districts` VALUES (949, 'Thạnh Trị', 94, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '59.09');
INSERT INTO `districts` VALUES (950, 'Vĩnh Châu', 94, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '59.02');
INSERT INTO `districts` VALUES (951, 'Trần Đề', 94, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '59.08');
INSERT INTO `districts` VALUES (954, 'Bạc Liêu', 95, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '43.01');
INSERT INTO `districts` VALUES (956, 'Hồng Dân', 95, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '43.03');
INSERT INTO `districts` VALUES (957, 'Phước Long', 95, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '43.05');
INSERT INTO `districts` VALUES (958, 'Vĩnh Lợi', 95, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '43.06');
INSERT INTO `districts` VALUES (959, 'Giá Rai', 95, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '43.02');
INSERT INTO `districts` VALUES (960, 'Đông Hải', 95, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '43.07');
INSERT INTO `districts` VALUES (961, 'Hoà Bình', 95, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '43.04');
INSERT INTO `districts` VALUES (964, 'Cà Mau', 96, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '48.01');
INSERT INTO `districts` VALUES (966, 'U Minh', 96, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '48.06');
INSERT INTO `districts` VALUES (967, 'Thới Bình', 96, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '48.07');
INSERT INTO `districts` VALUES (968, 'Trần Văn Thời', 96, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '48.05');
INSERT INTO `districts` VALUES (969, 'Cái Nước', 96, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '48.04');
INSERT INTO `districts` VALUES (970, 'Đầm Dơi', 96, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '48.02');
INSERT INTO `districts` VALUES (971, 'Năm Căn', 96, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '48.08');
INSERT INTO `districts` VALUES (972, 'Phú Tân', 96, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '48.09');
INSERT INTO `districts` VALUES (973, 'Ngọc Hiển', 96, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '48.03');
INSERT INTO `districts` VALUES (974, 'Nam Từ Liêm', 1, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '24.10');
INSERT INTO `districts` VALUES (975, 'Bắc Từ Liêm', 1, '2018-03-03 00:00:00', '2018-03-03 00:00:00', '24.02');
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
INSERT INTO `jobs` VALUES (1, 'Tài chính', 1);
INSERT INTO `jobs` VALUES (2, 'Khách sạn ', 1);
INSERT INTO `jobs` VALUES (3, 'Kinh doanh', 1);
INSERT INTO `jobs` VALUES (4, 'Nhân sự', 1);
INSERT INTO `jobs` VALUES (5, 'Chăm sóc khách hàng', 1);
INSERT INTO `jobs` VALUES (6, 'Bán hàng', 1);
INSERT INTO `jobs` VALUES (7, 'Giáo dục/Đào tạo', 1);
INSERT INTO `jobs` VALUES (8, 'Báo chí - Truyền hình', 1);
INSERT INTO `jobs` VALUES (9, 'Bất động sản', 1);
INSERT INTO `jobs` VALUES (10, 'An ninh/Vệ sĩ/Bảo vệ', 1);
INSERT INTO `jobs` VALUES (11, 'Kế toán/Kiểm toán', 1);
INSERT INTO `jobs` VALUES (12, 'Biên dịch/Phiên dịch', 1);
INSERT INTO `jobs` VALUES (13, 'Bưu chính viễn thông', 1);
INSERT INTO `jobs` VALUES (14, 'Cơ khí', 1);
INSERT INTO `jobs` VALUES (15, 'Dầu khí/Địa chất', 1);
INSERT INTO `jobs` VALUES (16, 'Dệt may', 1);
INSERT INTO `jobs` VALUES (17, 'Du lịch', 1);
INSERT INTO `jobs` VALUES (18, 'Nhà hàng', 1);
INSERT INTO `jobs` VALUES (19, 'Dược/Y tế', 1);
INSERT INTO `jobs` VALUES (20, 'Hóa chất/Sinh hóa', 1);
INSERT INTO `jobs` VALUES (21, 'Điện tử - Điện lạnh', 1);
INSERT INTO `jobs` VALUES (22, 'Giải trí', 1);
INSERT INTO `jobs` VALUES (23, 'Giao thông vận tải/Cầu đường', 1);
INSERT INTO `jobs` VALUES (24, 'Thu mua', 1);
INSERT INTO `jobs` VALUES (25, 'Khu công nghiệp', 1);
INSERT INTO `jobs` VALUES (26, 'Spa - Mỹ phẩm - Trang sức', 1);
INSERT INTO `jobs` VALUES (27, 'Thể dục thể thao', 1);
INSERT INTO `jobs` VALUES (28, 'Lao động phổ thông', 1);
INSERT INTO `jobs` VALUES (29, 'Luật pháp', 1);
INSERT INTO `jobs` VALUES (30, 'Điện tử viễn thông', 1);
INSERT INTO `jobs` VALUES (31, 'Hàng hải', 1);
INSERT INTO `jobs` VALUES (32, 'Dệt may - Da giày', 1);
INSERT INTO `jobs` VALUES (33, 'Công nghệ cao', 1);
INSERT INTO `jobs` VALUES (34, 'Kỹ thuật ứng dụng', 1);
INSERT INTO `jobs` VALUES (35, 'Tổ chức sự kiện', 1);
INSERT INTO `jobs` VALUES (36, 'Thực phẩm', 1);
INSERT INTO `jobs` VALUES (37, 'Vận tải', 1);
INSERT INTO `jobs` VALUES (38, 'Môi trường/Xử lý chất thải', 1);
INSERT INTO `jobs` VALUES (39, 'Ngân hàng', 1);
INSERT INTO `jobs` VALUES (40, 'Chứng khoán/Đầu tư', 1);
INSERT INTO `jobs` VALUES (41, 'Nghệ thuật', 1);
INSERT INTO `jobs` VALUES (42, 'Điện ảnh', 1);
INSERT INTO `jobs` VALUES (43, 'Ngoại ngữ', 1);
INSERT INTO `jobs` VALUES (44, 'Nông/Lâm/Ngư nghiệp', 1);
INSERT INTO `jobs` VALUES (45, 'Phát triển thị trường', 1);
INSERT INTO `jobs` VALUES (46, 'Giúp việc', 1);
INSERT INTO `jobs` VALUES (47, 'Quan hệ đối ngoại', 1);
INSERT INTO `jobs` VALUES (48, 'Quảng cáo/Marketing/PR', 1);
INSERT INTO `jobs` VALUES (49, 'Sản xuất', 1);
INSERT INTO `jobs` VALUES (50, 'Sinh viên/Thực tập sinh', 1);
INSERT INTO `jobs` VALUES (51, 'Thẩm định/Giám định', 1);
INSERT INTO `jobs` VALUES (52, 'Thiết kế/Mỹ thuật', 1);
INSERT INTO `jobs` VALUES (53, 'Thời vụ/Bán thời gian', 1);
INSERT INTO `jobs` VALUES (54, 'Đồ uống', 1);
INSERT INTO `jobs` VALUES (55, 'Trang thiết bị công nghiệp', 1);
INSERT INTO `jobs` VALUES (56, 'Tư vấn bảo hiểm', 1);
INSERT INTO `jobs` VALUES (57, 'Xây dựng', 1);
INSERT INTO `jobs` VALUES (58, 'Quản trị kinh doanh', 1);
INSERT INTO `jobs` VALUES (59, 'Xuất - Nhập khẩu/Ngoại thương', 1);
INSERT INTO `jobs` VALUES (60, 'Kiến trúc/Nội thất', 1);
INSERT INTO `jobs` VALUES (61, 'Bảo hiểm', 1);
INSERT INTO `jobs` VALUES (62, 'Hành chính/Thư ký/Trợ lý', 1);
INSERT INTO `jobs` VALUES (63, 'Tài xế/Giao nhận', 1);
INSERT INTO `jobs` VALUES (64, 'Đồ gia dụng', 1);
INSERT INTO `jobs` VALUES (65, 'Tiếp thị quảng cáo', 1);
INSERT INTO `jobs` VALUES (66, 'Thương mại điện tử', 1);
INSERT INTO `jobs` VALUES (67, 'Hàng không', 1);
INSERT INTO `jobs` VALUES (68, 'Thủ công mỹ nghệ', 1);
INSERT INTO `jobs` VALUES (69, 'Hoạch định - Dự án', 1);
INSERT INTO `jobs` VALUES (70, 'Kho vận - Vật tư', 1);
INSERT INTO `jobs` VALUES (71, 'Khác', 1);
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
-- Records of log_contracts
-- ----------------------------
BEGIN;
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
) ENGINE=InnoDB AUTO_INCREMENT=1409 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of log_jobs
-- ----------------------------
BEGIN;
INSERT INTO `log_jobs` VALUES (1370, 'ProcessDataScheduleHasStudent', '2024-11-30', '2024-12-01 00:05:01');
INSERT INTO `log_jobs` VALUES (1371, 'AddScheduleHasStudent', '2024-12-01', '2024-12-01 01:00:02');
INSERT INTO `log_jobs` VALUES (1372, 'ProcessData', NULL, '2024-12-01 02:00:02');
INSERT INTO `log_jobs` VALUES (1373, 'ProcessDataScheduleHasStudent', '2024-12-01', '2024-12-02 00:05:02');
INSERT INTO `log_jobs` VALUES (1374, 'AddScheduleHasStudent', '2024-12-02', '2024-12-02 01:00:02');
INSERT INTO `log_jobs` VALUES (1375, 'ProcessData', NULL, '2024-12-02 02:00:02');
INSERT INTO `log_jobs` VALUES (1376, 'ProcessDataScheduleHasStudent', '2024-12-02', '2024-12-03 00:05:01');
INSERT INTO `log_jobs` VALUES (1377, 'AddScheduleHasStudent', '2024-12-03', '2024-12-03 01:00:02');
INSERT INTO `log_jobs` VALUES (1378, 'ProcessData', NULL, '2024-12-03 02:00:01');
INSERT INTO `log_jobs` VALUES (1379, 'ProcessDataScheduleHasStudent', '2024-12-03', '2024-12-04 00:05:01');
INSERT INTO `log_jobs` VALUES (1380, 'AddScheduleHasStudent', '2024-12-04', '2024-12-04 01:00:01');
INSERT INTO `log_jobs` VALUES (1381, 'ProcessData', NULL, '2024-12-04 02:00:01');
INSERT INTO `log_jobs` VALUES (1382, 'ProcessDataScheduleHasStudent', '2024-12-04', '2024-12-05 00:05:01');
INSERT INTO `log_jobs` VALUES (1383, 'AddScheduleHasStudent', '2024-12-05', '2024-12-05 01:00:02');
INSERT INTO `log_jobs` VALUES (1384, 'ProcessData', NULL, '2024-12-05 02:00:01');
INSERT INTO `log_jobs` VALUES (1385, 'ProcessDataScheduleHasStudent', '2024-12-05', '2024-12-06 00:05:02');
INSERT INTO `log_jobs` VALUES (1386, 'AddScheduleHasStudent', '2024-12-06', '2024-12-06 01:00:02');
INSERT INTO `log_jobs` VALUES (1387, 'ProcessData', NULL, '2024-12-06 02:00:01');
INSERT INTO `log_jobs` VALUES (1388, 'ProcessDataScheduleHasStudent', '2024-12-06', '2024-12-07 00:05:02');
INSERT INTO `log_jobs` VALUES (1389, 'AddScheduleHasStudent', '2024-12-07', '2024-12-07 01:00:02');
INSERT INTO `log_jobs` VALUES (1390, 'ProcessData', NULL, '2024-12-07 02:00:02');
INSERT INTO `log_jobs` VALUES (1391, 'ProcessDataScheduleHasStudent', '2024-12-07', '2024-12-08 00:05:01');
INSERT INTO `log_jobs` VALUES (1392, 'AddScheduleHasStudent', '2024-12-08', '2024-12-08 01:00:01');
INSERT INTO `log_jobs` VALUES (1393, 'ProcessData', NULL, '2024-12-08 02:00:02');
INSERT INTO `log_jobs` VALUES (1394, 'ProcessDataScheduleHasStudent', '2024-12-08', '2024-12-09 00:05:01');
INSERT INTO `log_jobs` VALUES (1395, 'AddScheduleHasStudent', '2024-12-09', '2024-12-09 01:00:01');
INSERT INTO `log_jobs` VALUES (1396, 'ProcessData', NULL, '2024-12-09 02:00:01');
INSERT INTO `log_jobs` VALUES (1397, 'ProcessDataScheduleHasStudent', '2024-12-09', '2024-12-10 00:05:01');
INSERT INTO `log_jobs` VALUES (1398, 'AddScheduleHasStudent', '2024-12-10', '2024-12-10 01:00:01');
INSERT INTO `log_jobs` VALUES (1399, 'ProcessData', NULL, '2024-12-10 02:00:01');
INSERT INTO `log_jobs` VALUES (1400, 'ProcessDataScheduleHasStudent', '2024-12-10', '2024-12-11 00:05:02');
INSERT INTO `log_jobs` VALUES (1401, 'AddScheduleHasStudent', '2024-12-11', '2024-12-11 01:00:02');
INSERT INTO `log_jobs` VALUES (1402, 'ProcessData', NULL, '2024-12-11 02:00:01');
INSERT INTO `log_jobs` VALUES (1403, 'ProcessDataScheduleHasStudent', '2024-12-11', '2024-12-12 00:05:01');
INSERT INTO `log_jobs` VALUES (1404, 'AddScheduleHasStudent', '2024-12-12', '2024-12-12 01:00:02');
INSERT INTO `log_jobs` VALUES (1405, 'ProcessData', NULL, '2024-12-12 02:00:02');
INSERT INTO `log_jobs` VALUES (1406, 'ProcessDataScheduleHasStudent', '2024-12-12', '2024-12-13 00:05:01');
INSERT INTO `log_jobs` VALUES (1407, 'AddScheduleHasStudent', '2024-12-13', '2024-12-13 01:00:02');
INSERT INTO `log_jobs` VALUES (1408, 'ProcessData', NULL, '2024-12-13 02:00:02');
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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

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
-- Records of payments
-- ----------------------------
BEGIN;
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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- ----------------------------
-- Records of permission_groups
-- ----------------------------
BEGIN;
INSERT INTO `permission_groups` VALUES (1, 'CRM', 'CRM', 1, 3);
INSERT INTO `permission_groups` VALUES (2, 'LMS', 'LMS', 1, 2);
INSERT INTO `permission_groups` VALUES (3, 'Config', 'Cấu hình hệ thống', 1, 1);
INSERT INTO `permission_groups` VALUES (4, 'Orther', 'Khác', 1, 4);
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
INSERT INTO `permission_has_role` VALUES (36, 3);
INSERT INTO `permission_has_role` VALUES (36, 6);
INSERT INTO `permission_has_role` VALUES (36, 11);
INSERT INTO `permission_has_role` VALUES (36, 12);
INSERT INTO `permission_has_role` VALUES (36, 24);
INSERT INTO `permission_has_role` VALUES (36, 28);
INSERT INTO `permission_has_role` VALUES (55, 3);
INSERT INTO `permission_has_role` VALUES (55, 4);
INSERT INTO `permission_has_role` VALUES (55, 5);
INSERT INTO `permission_has_role` VALUES (55, 6);
INSERT INTO `permission_has_role` VALUES (55, 7);
INSERT INTO `permission_has_role` VALUES (55, 8);
INSERT INTO `permission_has_role` VALUES (55, 9);
INSERT INTO `permission_has_role` VALUES (55, 10);
INSERT INTO `permission_has_role` VALUES (55, 11);
INSERT INTO `permission_has_role` VALUES (55, 12);
INSERT INTO `permission_has_role` VALUES (55, 17);
INSERT INTO `permission_has_role` VALUES (55, 21);
INSERT INTO `permission_has_role` VALUES (55, 24);
INSERT INTO `permission_has_role` VALUES (55, 26);
INSERT INTO `permission_has_role` VALUES (55, 27);
INSERT INTO `permission_has_role` VALUES (55, 28);
INSERT INTO `permission_has_role` VALUES (55, 29);
INSERT INTO `permission_has_role` VALUES (55, 30);
INSERT INTO `permission_has_role` VALUES (68, 1);
INSERT INTO `permission_has_role` VALUES (68, 2);
INSERT INTO `permission_has_role` VALUES (68, 3);
INSERT INTO `permission_has_role` VALUES (68, 4);
INSERT INTO `permission_has_role` VALUES (68, 5);
INSERT INTO `permission_has_role` VALUES (68, 24);
INSERT INTO `permission_has_role` VALUES (68, 28);
INSERT INTO `permission_has_role` VALUES (686868, 1);
INSERT INTO `permission_has_role` VALUES (686868, 2);
INSERT INTO `permission_has_role` VALUES (686868, 3);
INSERT INTO `permission_has_role` VALUES (686868, 4);
INSERT INTO `permission_has_role` VALUES (686868, 5);
INSERT INTO `permission_has_role` VALUES (686868, 6);
INSERT INTO `permission_has_role` VALUES (686868, 7);
INSERT INTO `permission_has_role` VALUES (686868, 8);
INSERT INTO `permission_has_role` VALUES (686868, 9);
INSERT INTO `permission_has_role` VALUES (686868, 10);
INSERT INTO `permission_has_role` VALUES (686868, 11);
INSERT INTO `permission_has_role` VALUES (686868, 12);
INSERT INTO `permission_has_role` VALUES (686868, 24);
INSERT INTO `permission_has_role` VALUES (686868, 26);
INSERT INTO `permission_has_role` VALUES (686868, 27);
INSERT INTO `permission_has_role` VALUES (686868, 28);
INSERT INTO `permission_has_role` VALUES (999999, 1);
INSERT INTO `permission_has_role` VALUES (999999, 2);
INSERT INTO `permission_has_role` VALUES (999999, 3);
INSERT INTO `permission_has_role` VALUES (999999, 4);
INSERT INTO `permission_has_role` VALUES (999999, 5);
INSERT INTO `permission_has_role` VALUES (999999, 6);
INSERT INTO `permission_has_role` VALUES (999999, 7);
INSERT INTO `permission_has_role` VALUES (999999, 8);
INSERT INTO `permission_has_role` VALUES (999999, 9);
INSERT INTO `permission_has_role` VALUES (999999, 10);
INSERT INTO `permission_has_role` VALUES (999999, 11);
INSERT INTO `permission_has_role` VALUES (999999, 12);
INSERT INTO `permission_has_role` VALUES (999999, 13);
INSERT INTO `permission_has_role` VALUES (999999, 14);
INSERT INTO `permission_has_role` VALUES (999999, 15);
INSERT INTO `permission_has_role` VALUES (999999, 16);
INSERT INTO `permission_has_role` VALUES (999999, 17);
INSERT INTO `permission_has_role` VALUES (999999, 18);
INSERT INTO `permission_has_role` VALUES (999999, 19);
INSERT INTO `permission_has_role` VALUES (999999, 20);
INSERT INTO `permission_has_role` VALUES (999999, 21);
INSERT INTO `permission_has_role` VALUES (999999, 22);
INSERT INTO `permission_has_role` VALUES (999999, 23);
INSERT INTO `permission_has_role` VALUES (999999, 24);
INSERT INTO `permission_has_role` VALUES (999999, 26);
INSERT INTO `permission_has_role` VALUES (999999, 27);
INSERT INTO `permission_has_role` VALUES (999999, 28);
INSERT INTO `permission_has_role` VALUES (999999, 29);
INSERT INTO `permission_has_role` VALUES (999999, 30);
INSERT INTO `permission_has_role` VALUES (999999, 31);
INSERT INTO `permission_has_role` VALUES (999999, 32);
INSERT INTO `permission_has_role` VALUES (999999, 34);
INSERT INTO `permission_has_role` VALUES (999999, 35);
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
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- ----------------------------
-- Records of permissions
-- ----------------------------
BEGIN;
INSERT INTO `permissions` VALUES (1, 'crm_parent', 'Khách hàng', 1, 1, 1);
INSERT INTO `permissions` VALUES (2, 'crm_imports', 'Import', 1, 2, 1);
INSERT INTO `permissions` VALUES (3, 'lms_students', 'DS học sinh', 2, 1, 1);
INSERT INTO `permissions` VALUES (4, 'lms_checkin', 'DS checkin', 2, 2, 1);
INSERT INTO `permissions` VALUES (5, 'lms_contracts', 'Nhập học', 2, 3, 1);
INSERT INTO `permissions` VALUES (6, 'lms_enrolments', 'Xếp lớp', 2, 4, 1);
INSERT INTO `permissions` VALUES (7, 'lms_reserves', 'Bảo lưu', 2, 5, 1);
INSERT INTO `permissions` VALUES (8, 'lms_class_transfers', 'Chuyển lớp', 2, 6, 1);
INSERT INTO `permissions` VALUES (9, 'lms_branch_transfers', 'Chuyển trung tâm', 2, 7, 1);
INSERT INTO `permissions` VALUES (10, 'lms_tuition_transfers', 'Chuyển phí', 2, 8, 1);
INSERT INTO `permissions` VALUES (11, 'lms_attendances', 'Điểm danh', 2, 9, 1);
INSERT INTO `permissions` VALUES (12, 'lms_assessments', 'Nhận xét & đánh giá', 2, 10, 1);
INSERT INTO `permissions` VALUES (13, 'settings_branches', 'Trung tâm', 3, 1, 1);
INSERT INTO `permissions` VALUES (14, 'settings_products', 'Khóa học', 3, 2, 1);
INSERT INTO `permissions` VALUES (15, 'settings_programs', 'Chương trình học', 3, 3, 1);
INSERT INTO `permissions` VALUES (16, 'settings_subjects', 'Môn học', 3, 4, 1);
INSERT INTO `permissions` VALUES (17, 'settings_classes', 'Lớp học', 3, 5, 1);
INSERT INTO `permissions` VALUES (18, 'settings_rooms', 'Phòng học', 3, 6, 1);
INSERT INTO `permissions` VALUES (19, 'settings_shifts', 'Ca học', 3, 7, 1);
INSERT INTO `permissions` VALUES (20, 'settings_holidays', 'Ngày nghỉ lễ', 3, 8, 1);
INSERT INTO `permissions` VALUES (21, 'settings_tuition_fees', 'Gói phí', 3, 9, 1);
INSERT INTO `permissions` VALUES (22, 'settings_users', 'Người dùng', 3, 10, 1);
INSERT INTO `permissions` VALUES (23, 'settings_roles', 'Chức vụ, phân quyền', 3, 11, 1);
INSERT INTO `permissions` VALUES (24, 'dashboard', 'Dashboard', 4, 1, 1);
INSERT INTO `permissions` VALUES (26, 'lms_charges', 'Đã thu phí', 2, 11, 1);
INSERT INTO `permissions` VALUES (27, 'lms_waitcharges', 'Chờ thu phí', 2, 12, 1);
INSERT INTO `permissions` VALUES (28, 'lms_reports', 'Báo cáo', 2, 13, 1);
INSERT INTO `permissions` VALUES (29, 'settings_discount_codes', 'Mã chiết khấu', 3, 12, 1);
INSERT INTO `permissions` VALUES (30, 'settings_coupons', 'Voucher', 3, 13, 1);
INSERT INTO `permissions` VALUES (31, 'canViewAllSale', 'Xem tất cả CSKH', 4, 2, 1);
INSERT INTO `permissions` VALUES (32, 'canViewAllParents', 'Xem tất cả khách hàng', 4, 3, 1);
INSERT INTO `permissions` VALUES (33, 'canViewAllImport', 'Xem tất cả import', 4, 4, 1);
INSERT INTO `permissions` VALUES (34, 'lms_teachers', 'Giáo viên & trợ giảng', 2, 14, 1);
INSERT INTO `permissions` VALUES (35, 'lms_exchange', 'Quy đổi', 2, 8, 1);
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
  PRIMARY KEY (`id`) USING BTREE,
  KEY `proc_id` (`code`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of products
-- ----------------------------
BEGIN;
INSERT INTO `products` VALUES (8, NULL, 'CK ESL', 'CK ESL', NULL, '2024-11-30 19:29:30', '2024-12-08 20:33:59', 1, 1, 1, NULL, NULL, NULL);
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
  PRIMARY KEY (`id`) USING BTREE,
  KEY `product_id` (`product_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of programs
-- ----------------------------
BEGIN;
INSERT INTO `programs` VALUES (7, NULL, 'CK Phonics', 'CKPhonic', NULL, '2024-11-30 19:28:57', 1, '2024-11-30 19:43:41', 1, 1, 0, 8);
INSERT INTO `programs` VALUES (8, NULL, '2410 - 2504', '2410 - 2504', NULL, '2024-12-08 17:45:31', 1, '2024-12-08 19:23:08', 1, 1, 7, 8);
INSERT INTO `programs` VALUES (9, NULL, 'CK English 2', 'CKEnglish2', NULL, '2024-12-08 19:23:53', 1, NULL, NULL, 1, 0, 8);
INSERT INTO `programs` VALUES (10, NULL, '2410 - 2504', '2410 - 2504', NULL, '2024-12-08 20:24:40', 1, NULL, NULL, 1, 9, 8);
INSERT INTO `programs` VALUES (11, NULL, 'CK English 3', 'CKEnglish3', NULL, '2024-12-08 20:25:10', 1, NULL, NULL, 1, 0, 8);
INSERT INTO `programs` VALUES (12, NULL, '2410 - 2504', '2410 - 2504', NULL, '2024-12-08 20:25:23', 1, NULL, NULL, 1, 11, 8);
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
INSERT INTO `provinces` VALUES (1, 'Hà Nội', '2018-03-03 00:00:00', '2018-03-03 00:00:00', '24', 1);
INSERT INTO `provinces` VALUES (2, 'Hà Giang', '2018-03-03 00:00:00', '2018-03-03 00:00:00', '06', 2);
INSERT INTO `provinces` VALUES (4, 'Cao Bằng', '2018-03-03 00:00:00', '2018-03-03 00:00:00', '04', 4);
INSERT INTO `provinces` VALUES (6, 'Bắc Kạn', '2018-03-03 00:00:00', '2018-03-03 00:00:00', '02', 6);
INSERT INTO `provinces` VALUES (8, 'Tuyên Quang', '2018-03-03 00:00:00', '2018-03-03 00:00:00', '23', 8);
INSERT INTO `provinces` VALUES (10, 'Lào Cai', '2018-03-03 00:00:00', '2018-03-03 00:00:00', '15', 10);
INSERT INTO `provinces` VALUES (11, 'Điện Biên', '2018-03-03 00:00:00', '2018-03-03 00:00:00', '05', 11);
INSERT INTO `provinces` VALUES (12, 'Lai Châu', '2018-03-03 00:00:00', '2018-03-03 00:00:00', '13', 12);
INSERT INTO `provinces` VALUES (14, 'Sơn La', '2018-03-03 00:00:00', '2018-03-03 00:00:00', '20', 14);
INSERT INTO `provinces` VALUES (15, 'Yên Bái', '2018-03-03 00:00:00', '2018-03-03 00:00:00', '25', 15);
INSERT INTO `provinces` VALUES (17, 'Hòa Bình', '2018-03-03 00:00:00', '2018-03-03 00:00:00', '11', 17);
INSERT INTO `provinces` VALUES (19, 'Thái Nguyên', '2018-03-03 00:00:00', '2018-03-03 00:00:00', '22', 19);
INSERT INTO `provinces` VALUES (20, 'Lạng Sơn', '2018-03-03 00:00:00', '2018-03-03 00:00:00', '14', 20);
INSERT INTO `provinces` VALUES (22, 'Quảng Ninh', '2018-03-03 00:00:00', '2018-03-03 00:00:00', '19', 22);
INSERT INTO `provinces` VALUES (24, 'Bắc Giang', '2018-03-03 00:00:00', '2018-03-03 00:00:00', '01', 24);
INSERT INTO `provinces` VALUES (25, 'Phú Thọ', '2018-03-03 00:00:00', '2018-03-03 00:00:00', '18', 25);
INSERT INTO `provinces` VALUES (26, 'Vĩnh Phúc', '2018-03-03 00:00:00', '2018-03-03 00:00:00', '08', 26);
INSERT INTO `provinces` VALUES (27, 'Bắc Ninh', '2018-03-03 00:00:00', '2018-03-03 00:00:00', '03', 27);
INSERT INTO `provinces` VALUES (30, 'Hải Dương', '2018-03-03 00:00:00', '2018-03-03 00:00:00', '09', 30);
INSERT INTO `provinces` VALUES (31, 'Hải Phòng', '2018-03-03 00:00:00', '2018-03-03 00:00:00', '10', 31);
INSERT INTO `provinces` VALUES (33, 'Hưng Yên', '2018-03-03 00:00:00', '2018-03-03 00:00:00', '12', 33);
INSERT INTO `provinces` VALUES (34, 'Thái Bình', '2018-03-03 00:00:00', '2018-03-03 00:00:00', '21', 34);
INSERT INTO `provinces` VALUES (35, 'Hà Nam', '2018-03-03 00:00:00', '2018-03-03 00:00:00', '07', 35);
INSERT INTO `provinces` VALUES (36, 'Nam Định', '2018-03-03 00:00:00', '2018-03-03 00:00:00', '16', 36);
INSERT INTO `provinces` VALUES (37, 'Ninh Bình', '2018-03-03 00:00:00', '2018-03-03 00:00:00', '17', 37);
INSERT INTO `provinces` VALUES (38, 'Thanh Hóa', '2018-03-03 00:00:00', '2018-03-03 00:00:00', '39', 38);
INSERT INTO `provinces` VALUES (40, 'Nghệ An', '2018-03-03 00:00:00', '2018-03-03 00:00:00', '33', 40);
INSERT INTO `provinces` VALUES (42, 'Hà Tĩnh', '2018-03-03 00:00:00', '2018-03-03 00:00:00', '30', 42);
INSERT INTO `provinces` VALUES (44, 'Quảng Bình', '2018-03-03 00:00:00', '2018-03-03 00:00:00', '35', 44);
INSERT INTO `provinces` VALUES (45, 'Quảng Trị', '2018-03-03 00:00:00', '2018-03-03 00:00:00', '38', 45);
INSERT INTO `provinces` VALUES (46, 'Thừa Thiên Huế', '2018-03-03 00:00:00', '2018-03-03 00:00:00', '40', 46);
INSERT INTO `provinces` VALUES (48, 'Đà Nẵng', '2018-03-03 00:00:00', '2018-03-03 00:00:00', '28', 48);
INSERT INTO `provinces` VALUES (49, 'Quảng Nam', '2018-03-03 00:00:00', '2018-03-03 00:00:00', '36', 49);
INSERT INTO `provinces` VALUES (51, 'Quảng Ngãi', '2018-03-03 00:00:00', '2018-03-03 00:00:00', '37', 51);
INSERT INTO `provinces` VALUES (52, 'Bình Định', '2018-03-03 00:00:00', '2018-03-03 00:00:00', '26', 52);
INSERT INTO `provinces` VALUES (54, 'Phú Yên', '2018-03-03 00:00:00', '2018-03-03 00:00:00', '34', 54);
INSERT INTO `provinces` VALUES (56, 'Khánh Hòa', '2018-03-03 00:00:00', '2018-03-03 00:00:00', '31', 56);
INSERT INTO `provinces` VALUES (58, 'Ninh Thuận', '2018-03-03 00:00:00', '2018-03-03 00:00:00', '58', 58);
INSERT INTO `provinces` VALUES (60, 'Bình Thuận', '2018-03-03 00:00:00', '2018-03-03 00:00:00', '47', 60);
INSERT INTO `provinces` VALUES (62, 'Kon Tum', '2018-03-03 00:00:00', '2018-03-03 00:00:00', '32', 62);
INSERT INTO `provinces` VALUES (64, 'Gia Lai', '2018-03-03 00:00:00', '2018-03-03 00:00:00', '52', 64);
INSERT INTO `provinces` VALUES (66, 'Đắk Lắk', '2018-03-03 00:00:00', '2018-03-03 00:00:00', '29', 66);
INSERT INTO `provinces` VALUES (67, 'Đắk Nông', '2018-03-03 00:00:00', '2018-03-03 00:00:00', '49', 67);
INSERT INTO `provinces` VALUES (68, 'Lâm Đồng', '2018-03-03 00:00:00', '2018-03-03 00:00:00', '56', 68);
INSERT INTO `provinces` VALUES (70, 'Bình Phước', '2018-03-03 00:00:00', '2018-03-03 00:00:00', '46', 70);
INSERT INTO `provinces` VALUES (72, 'Tây Ninh', '2018-03-03 00:00:00', '2018-03-03 00:00:00', '60', 72);
INSERT INTO `provinces` VALUES (74, 'Bình Dương', '2018-03-03 00:00:00', '2018-03-03 00:00:00', '45', 74);
INSERT INTO `provinces` VALUES (75, 'Đồng Nai', '2018-03-03 00:00:00', '2018-03-03 00:00:00', '50', 75);
INSERT INTO `provinces` VALUES (77, 'Bà Rịa - Vũng Tàu', '2018-03-03 00:00:00', '2018-03-03 00:00:00', '42', 77);
INSERT INTO `provinces` VALUES (79, 'Hồ Chí Minh', '2018-03-03 00:00:00', '2018-03-03 00:00:00', '54', 79);
INSERT INTO `provinces` VALUES (80, 'Long An', '2018-03-03 00:00:00', '2018-03-03 00:00:00', '57', 80);
INSERT INTO `provinces` VALUES (82, 'Tiền Giang', '2018-03-03 00:00:00', '2018-03-03 00:00:00', '61', 82);
INSERT INTO `provinces` VALUES (83, 'Bến Tre', '2018-03-03 00:00:00', '2018-03-03 00:00:00', '44', 83);
INSERT INTO `provinces` VALUES (84, 'Trà Vinh', '2018-03-03 00:00:00', '2018-03-03 00:00:00', '62', 84);
INSERT INTO `provinces` VALUES (86, 'Vĩnh Long', '2018-03-03 00:00:00', '2018-03-03 00:00:00', '63', 86);
INSERT INTO `provinces` VALUES (87, 'Đồng Tháp', '2018-03-03 00:00:00', '2018-03-03 00:00:00', '51', 87);
INSERT INTO `provinces` VALUES (89, 'An Giang', '2018-03-03 00:00:00', '2018-03-03 00:00:00', '41', 89);
INSERT INTO `provinces` VALUES (91, 'Kiên Giang', '2018-03-03 00:00:00', '2018-03-03 00:00:00', '55', 91);
INSERT INTO `provinces` VALUES (92, 'Cần Thơ', '2018-03-03 00:00:00', '2018-03-03 00:00:00', '27', 92);
INSERT INTO `provinces` VALUES (93, 'Hậu Giang', '2018-03-03 00:00:00', '2018-03-03 00:00:00', '53', 93);
INSERT INTO `provinces` VALUES (94, 'Sóc Trăng', '2018-03-03 00:00:00', '2018-03-03 00:00:00', '59', 94);
INSERT INTO `provinces` VALUES (95, 'Bạc Liêu', '2018-03-03 00:00:00', '2018-03-03 00:00:00', '43', 95);
INSERT INTO `provinces` VALUES (96, 'Cà Mau', '2018-03-03 00:00:00', '2018-03-03 00:00:00', '48', 96);
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
) ENGINE=InnoDB AUTO_INCREMENT=131 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb3;

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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb3;

-- ----------------------------
-- Records of report_renews
-- ----------------------------
BEGIN;
INSERT INTO `report_renews` VALUES (1, 1, 3, 1, 3, 0, 3, 5, 5, 5, 4, 4, 0, 4, 2, 23400000, 2, '2025-05', '2025-05-02', NULL, NULL, '2024-11-28 15:20:31', 0, 0, 0);
INSERT INTO `report_renews` VALUES (2, 5, 5, 2, 2, 0, 6, 4, 4, 5, 4, 4, 0, 4, 2, 15000000, 2, '2025-05', '2025-05-19', NULL, NULL, '2024-11-28 15:20:31', 0, 0, 0);
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
-- Records of reserves
-- ----------------------------
BEGIN;
INSERT INTO `reserves` VALUES (1, 1, 1, '2024-05-14', 3, '2024-05-22', 2, 1, '2024-05-14 22:45:02', 1, '2024-05-14 22:45:21', NULL, '{\"reserve\":{\"branch_id\":1,\"student_id\":1,\"contract_id\":3,\"note\":null,\"start_date\":\"2024-05-14\",\"end_date\":\"2024-05-22\",\"session\":\"3\",\"is_reserved\":\"0\"},\"student_info\":{\"name\":\"L\\u01b0\\u01a1ng B\\u1ea3o Long\",\"lms_code\":\"LMS000001\",\"gud_name1\":\"L\\u01b0\\u01a1ng Th\\u00e0nh C\\u00f4ng\",\"gud_mobile1\":\"0389941902\",\"gud_email1\":\"thanhcong1710@gmail.com\",\"address\":null,\"student_id\":1,\"label\":\"L\\u01b0\\u01a1ng B\\u1ea3o Long - LMS000001\",\"product_name\":\"FasTracKids\",\"program_name\":\"FasTracKids - Th\\u1ee7 l\\u0129nh nh\\u00ed\",\"class_name\":\"L\\u1edbp ch\\u00ednh th\\u1ee9c 1\",\"tuition_fee_name\":\"FasTracKids - 12 th\\u00e1ng\",\"total_charged\":23400000,\"summary_sessions\":100,\"bonus_sessions\":4,\"real_sessions\":96,\"reservable_sessions\":4,\"reserved_sessions\":0,\"product_id\":3,\"class_id\":2,\"contract_id\":3,\"enrolment_start_date\":\"2024-05-13\",\"enrolment_last_date\":\"2025-04-30\"}}', 0, NULL, 3, 1, 3, 4, 2, NULL, '2024-05-14 22:45:21', 1);
INSERT INTO `reserves` VALUES (2, 4, 0, '2024-12-01', 1, '2024-12-08', 2, 1, '2024-11-24 20:28:00', NULL, NULL, NULL, '{\"reserve\":{\"branch_id\":2,\"student_id\":4,\"contract_id\":4,\"note\":null,\"start_date\":\"2024-12-01\",\"end_date\":\"2024-12-08\",\"session\":\"1\",\"is_reserved\":1},\"student_info\":{\"name\":\"Nguy\\u1ec5n V\\u0103n Hoang\",\"lms_code\":\"LMS000004\",\"gud_name1\":\"Nguy\\u1ec5n Th\\u1ecb Hoa\",\"gud_mobile1\":\"0954112553\",\"gud_email1\":\"demo@gmail.com\",\"address\":\"h\\u00e0 n\\u1ed9i\",\"student_id\":4,\"label\":\"Nguy\\u1ec5n V\\u0103n Hoang - LMS000004\",\"product_name\":\"FasTracKids\",\"program_name\":\"FasTracKids - Th\\u1ee7 l\\u0129nh nh\\u00ed\",\"class_name\":\"FT - Fundamentals Th\\u1ee7 l\\u0129nh\",\"tuition_fee_name\":\"FasTracKids - 12 th\\u00e1ng\",\"total_charged\":22100000,\"summary_sessions\":96,\"bonus_sessions\":0,\"real_sessions\":96,\"reservable_sessions\":4,\"reserved_sessions\":0,\"product_id\":3,\"class_id\":9,\"contract_id\":4,\"enrolment_start_date\":\"2024-12-01\",\"enrolment_last_date\":\"2025-11-03\"}}', 1, NULL, 4, 2, 3, 4, 9, NULL, NULL, NULL);
INSERT INTO `reserves` VALUES (3, 4, 1, '2024-12-13', 2, '2024-12-16', 2, 1, '2024-11-24 20:28:33', 1, '2024-11-24 20:28:40', NULL, '{\"reserve\":{\"branch_id\":2,\"student_id\":4,\"contract_id\":4,\"note\":null,\"start_date\":\"2024-12-13\",\"end_date\":\"2024-12-16\",\"session\":\"2\",\"is_reserved\":\"0\"},\"student_info\":{\"name\":\"Nguy\\u1ec5n V\\u0103n Hoang\",\"lms_code\":\"LMS000004\",\"gud_name1\":\"Nguy\\u1ec5n Th\\u1ecb Hoa\",\"gud_mobile1\":\"0954112553\",\"gud_email1\":\"demo@gmail.com\",\"address\":\"h\\u00e0 n\\u1ed9i\",\"student_id\":4,\"label\":\"Nguy\\u1ec5n V\\u0103n Hoang - LMS000004\",\"product_name\":\"FasTracKids\",\"program_name\":\"FasTracKids - Th\\u1ee7 l\\u0129nh nh\\u00ed\",\"class_name\":\"FT - Fundamentals Th\\u1ee7 l\\u0129nh\",\"tuition_fee_name\":\"FasTracKids - 12 th\\u00e1ng\",\"total_charged\":22100000,\"summary_sessions\":96,\"bonus_sessions\":0,\"real_sessions\":96,\"reservable_sessions\":4,\"reserved_sessions\":1,\"product_id\":3,\"class_id\":9,\"contract_id\":4,\"enrolment_start_date\":\"2024-12-01\",\"enrolment_last_date\":\"2025-11-03\"}}', 0, NULL, 4, 2, 3, 4, 9, NULL, '2024-11-24 20:28:40', 1);
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
INSERT INTO `role_has_user` VALUES (1, 999999);
INSERT INTO `role_has_user` VALUES (2, 686868);
INSERT INTO `role_has_user` VALUES (3, 36);
INSERT INTO `role_has_user` VALUES (4, 55);
INSERT INTO `role_has_user` VALUES (5, 68);
INSERT INTO `role_has_user` VALUES (6, 55);
INSERT INTO `role_has_user` VALUES (7, 55);
INSERT INTO `role_has_user` VALUES (8, 68);
INSERT INTO `role_has_user` VALUES (9, 36);
INSERT INTO `role_has_user` VALUES (10, 36);
INSERT INTO `role_has_user` VALUES (11, 686868);
INSERT INTO `role_has_user` VALUES (12, 686868);
INSERT INTO `role_has_user` VALUES (13, 686868);
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
) ENGINE=InnoDB AUTO_INCREMENT=1000000 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of roles
-- ----------------------------
BEGIN;
INSERT INTO `roles` VALUES (36, '36', 'teacher', 'Giáo viên', 'api', NULL, NULL, NULL, NULL);
INSERT INTO `roles` VALUES (37, '37', 'teacher_leader', 'Trưởng nhóm giáo viên', 'api', NULL, NULL, NULL, NULL);
INSERT INTO `roles` VALUES (55, '55', 'cm', 'Vận hành', 'api', '2020-08-27 02:04:43', '2020-08-27 02:04:43', NULL, NULL);
INSERT INTO `roles` VALUES (56, '56', 'cm_leader', 'Trưởng nhóm vận hành', 'api', '2024-04-06 15:17:25', '2024-04-06 15:17:33', 1, 1);
INSERT INTO `roles` VALUES (68, '68', 'ec', 'CSKH', 'api', '2020-08-27 02:04:43', '2020-08-27 02:04:43', NULL, NULL);
INSERT INTO `roles` VALUES (69, '69', 'ec_leader', 'Trưởng nhóm CSKH', 'api', '2023-07-27 06:44:05', '2023-07-27 06:44:05', NULL, NULL);
INSERT INTO `roles` VALUES (686868, '686868', 'ceo', 'Giám đốc trung tâm', 'api', '2023-05-26 03:44:07', '2023-05-26 03:44:07', NULL, NULL);
INSERT INTO `roles` VALUES (999999, '999999', 'admin', 'Quản trị hệ thống', 'api', '2020-08-27 02:04:43', '2020-08-27 02:04:43', NULL, NULL);
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
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of rooms
-- ----------------------------
BEGIN;
INSERT INTO `rooms` VALUES (5, 'Phonix1', 'Phonix1', 4, '2024-11-30 19:25:35', 1, NULL, NULL, 1, NULL, NULL);
INSERT INTO `rooms` VALUES (6, 'CK English1', 'CK English1', 4, '2024-11-30 19:26:07', 1, NULL, NULL, 1, NULL, NULL);
INSERT INTO `rooms` VALUES (7, 'i-Master1', 'i-Master1', 4, '2024-11-30 19:26:21', 1, NULL, NULL, 1, NULL, NULL);
INSERT INTO `rooms` VALUES (8, 'Phonix1', 'Phonix1', 5, '2024-12-08 20:31:32', 1, NULL, NULL, 1, NULL, NULL);
INSERT INTO `rooms` VALUES (9, 'Phonix1', 'Phonix1', 6, '2024-12-08 20:31:38', 1, NULL, NULL, 1, NULL, NULL);
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
) ENGINE=InnoDB AUTO_INCREMENT=1399 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of schedules
-- ----------------------------
BEGIN;
INSERT INTO `schedules` VALUES (1303, '2024-12-06', 1, 14, 1, '2024-12-08 20:27:06', NULL, NULL, NULL, 1, 1, 10, 6, 4);
INSERT INTO `schedules` VALUES (1304, '2024-12-09', 1, 14, 1, '2024-12-08 20:27:06', NULL, NULL, NULL, 2, 2, 10, 6, 4);
INSERT INTO `schedules` VALUES (1305, '2024-12-13', 1, 14, 1, '2024-12-08 20:27:06', NULL, NULL, NULL, 3, 3, 10, 6, 4);
INSERT INTO `schedules` VALUES (1306, '2024-12-16', 1, 14, 1, '2024-12-08 20:27:06', NULL, NULL, NULL, 4, 4, 10, 6, 4);
INSERT INTO `schedules` VALUES (1307, '2024-12-20', 1, 14, 1, '2024-12-08 20:27:06', NULL, NULL, NULL, 5, 5, 10, 6, 4);
INSERT INTO `schedules` VALUES (1308, '2024-12-23', 1, 14, 1, '2024-12-08 20:27:06', NULL, NULL, NULL, 6, 6, 10, 6, 4);
INSERT INTO `schedules` VALUES (1309, '2024-12-27', 1, 14, 1, '2024-12-08 20:27:06', NULL, NULL, NULL, 7, 7, 10, 6, 4);
INSERT INTO `schedules` VALUES (1310, '2024-12-30', 1, 14, 1, '2024-12-08 20:27:06', NULL, NULL, NULL, 8, 8, 10, 6, 4);
INSERT INTO `schedules` VALUES (1311, '2025-01-03', 1, 14, 1, '2024-12-08 20:27:06', NULL, NULL, NULL, 9, 9, 10, 6, 4);
INSERT INTO `schedules` VALUES (1312, '2025-01-06', 1, 14, 1, '2024-12-08 20:27:06', NULL, NULL, NULL, 10, 10, 10, 6, 4);
INSERT INTO `schedules` VALUES (1313, '2025-01-10', 1, 14, 1, '2024-12-08 20:27:06', NULL, NULL, NULL, 11, 11, 10, 6, 4);
INSERT INTO `schedules` VALUES (1314, '2025-01-13', 1, 14, 1, '2024-12-08 20:27:06', NULL, NULL, NULL, 12, 12, 10, 6, 4);
INSERT INTO `schedules` VALUES (1315, '2025-01-17', 1, 14, 1, '2024-12-08 20:27:06', NULL, NULL, NULL, 13, 13, 10, 6, 4);
INSERT INTO `schedules` VALUES (1316, '2025-01-20', 1, 14, 1, '2024-12-08 20:27:06', NULL, NULL, NULL, 14, 14, 10, 6, 4);
INSERT INTO `schedules` VALUES (1317, '2025-01-24', 1, 14, 1, '2024-12-08 20:27:06', NULL, NULL, NULL, 15, 15, 10, 6, 4);
INSERT INTO `schedules` VALUES (1318, '2025-01-27', 1, 14, 1, '2024-12-08 20:27:06', NULL, NULL, NULL, 16, 16, 10, 6, 4);
INSERT INTO `schedules` VALUES (1319, '2025-01-31', 1, 14, 1, '2024-12-08 20:27:06', NULL, NULL, NULL, 17, 17, 10, 6, 4);
INSERT INTO `schedules` VALUES (1320, '2025-02-03', 1, 14, 1, '2024-12-08 20:27:06', NULL, NULL, NULL, 18, 18, 10, 6, 4);
INSERT INTO `schedules` VALUES (1321, '2025-02-07', 1, 14, 1, '2024-12-08 20:27:06', NULL, NULL, NULL, 19, 19, 10, 6, 4);
INSERT INTO `schedules` VALUES (1322, '2025-02-10', 1, 14, 1, '2024-12-08 20:27:06', NULL, NULL, NULL, 20, 20, 10, 6, 4);
INSERT INTO `schedules` VALUES (1323, '2025-02-14', 1, 14, 1, '2024-12-08 20:27:06', NULL, NULL, NULL, 21, 21, 10, 6, 4);
INSERT INTO `schedules` VALUES (1324, '2025-02-17', 1, 14, 1, '2024-12-08 20:27:06', NULL, NULL, NULL, 22, 22, 10, 6, 4);
INSERT INTO `schedules` VALUES (1325, '2025-02-21', 1, 14, 1, '2024-12-08 20:27:06', NULL, NULL, NULL, 23, 23, 10, 6, 4);
INSERT INTO `schedules` VALUES (1326, '2025-02-24', 1, 14, 1, '2024-12-08 20:27:06', NULL, NULL, NULL, 24, 24, 10, 6, 4);
INSERT INTO `schedules` VALUES (1327, '2025-02-28', 1, 14, 1, '2024-12-08 20:27:06', NULL, NULL, NULL, 25, 25, 10, 6, 4);
INSERT INTO `schedules` VALUES (1328, '2025-03-03', 1, 14, 1, '2024-12-08 20:27:06', NULL, NULL, NULL, 26, 26, 10, 6, 4);
INSERT INTO `schedules` VALUES (1329, '2025-03-07', 1, 14, 1, '2024-12-08 20:27:06', NULL, NULL, NULL, 27, 27, 10, 6, 4);
INSERT INTO `schedules` VALUES (1330, '2025-03-10', 1, 14, 1, '2024-12-08 20:27:06', NULL, NULL, NULL, 28, 28, 10, 6, 4);
INSERT INTO `schedules` VALUES (1331, '2025-03-14', 1, 14, 1, '2024-12-08 20:27:06', NULL, NULL, NULL, 29, 29, 10, 6, 4);
INSERT INTO `schedules` VALUES (1332, '2025-03-17', 1, 14, 1, '2024-12-08 20:27:06', NULL, NULL, NULL, 30, 30, 10, 6, 4);
INSERT INTO `schedules` VALUES (1333, '2025-03-21', 1, 14, 1, '2024-12-08 20:27:06', NULL, NULL, NULL, 31, 31, 10, 6, 4);
INSERT INTO `schedules` VALUES (1334, '2025-03-24', 1, 14, 1, '2024-12-08 20:27:06', NULL, NULL, NULL, 32, 32, 10, 6, 4);
INSERT INTO `schedules` VALUES (1335, '2024-12-08', 1, 15, 1, '2024-12-08 20:32:28', NULL, NULL, NULL, 1, 1, 9, 4, 5);
INSERT INTO `schedules` VALUES (1336, '2024-12-09', 1, 15, 1, '2024-12-08 20:32:28', NULL, NULL, NULL, 2, 2, 9, 4, 5);
INSERT INTO `schedules` VALUES (1337, '2024-12-15', 1, 15, 1, '2024-12-08 20:32:28', NULL, NULL, NULL, 3, 3, 9, 4, 5);
INSERT INTO `schedules` VALUES (1338, '2024-12-16', 1, 15, 1, '2024-12-08 20:32:28', NULL, NULL, NULL, 4, 4, 9, 4, 5);
INSERT INTO `schedules` VALUES (1339, '2024-12-22', 1, 15, 1, '2024-12-08 20:32:28', NULL, NULL, NULL, 5, 5, 9, 4, 5);
INSERT INTO `schedules` VALUES (1340, '2024-12-23', 1, 15, 1, '2024-12-08 20:32:28', NULL, NULL, NULL, 6, 6, 9, 4, 5);
INSERT INTO `schedules` VALUES (1341, '2024-12-29', 1, 15, 1, '2024-12-08 20:32:28', NULL, NULL, NULL, 7, 7, 9, 4, 5);
INSERT INTO `schedules` VALUES (1342, '2024-12-30', 1, 15, 1, '2024-12-08 20:32:28', NULL, NULL, NULL, 8, 8, 9, 4, 5);
INSERT INTO `schedules` VALUES (1343, '2025-01-05', 1, 15, 1, '2024-12-08 20:32:28', NULL, NULL, NULL, 9, 9, 9, 4, 5);
INSERT INTO `schedules` VALUES (1344, '2025-01-06', 1, 15, 1, '2024-12-08 20:32:28', NULL, NULL, NULL, 10, 10, 9, 4, 5);
INSERT INTO `schedules` VALUES (1345, '2025-01-12', 1, 15, 1, '2024-12-08 20:32:28', NULL, NULL, NULL, 11, 11, 9, 4, 5);
INSERT INTO `schedules` VALUES (1346, '2025-01-13', 1, 15, 1, '2024-12-08 20:32:28', NULL, NULL, NULL, 12, 12, 9, 4, 5);
INSERT INTO `schedules` VALUES (1347, '2025-01-19', 1, 15, 1, '2024-12-08 20:32:28', NULL, NULL, NULL, 13, 13, 9, 4, 5);
INSERT INTO `schedules` VALUES (1348, '2025-01-20', 1, 15, 1, '2024-12-08 20:32:28', NULL, NULL, NULL, 14, 14, 9, 4, 5);
INSERT INTO `schedules` VALUES (1349, '2025-01-26', 1, 15, 1, '2024-12-08 20:32:28', NULL, NULL, NULL, 15, 15, 9, 4, 5);
INSERT INTO `schedules` VALUES (1350, '2025-01-27', 1, 15, 1, '2024-12-08 20:32:28', NULL, NULL, NULL, 16, 16, 9, 4, 5);
INSERT INTO `schedules` VALUES (1351, '2025-02-02', 1, 15, 1, '2024-12-08 20:32:28', NULL, NULL, NULL, 17, 17, 9, 4, 5);
INSERT INTO `schedules` VALUES (1352, '2025-02-03', 1, 15, 1, '2024-12-08 20:32:28', NULL, NULL, NULL, 18, 18, 9, 4, 5);
INSERT INTO `schedules` VALUES (1353, '2025-02-09', 1, 15, 1, '2024-12-08 20:32:28', NULL, NULL, NULL, 19, 19, 9, 4, 5);
INSERT INTO `schedules` VALUES (1354, '2025-02-10', 1, 15, 1, '2024-12-08 20:32:28', NULL, NULL, NULL, 20, 20, 9, 4, 5);
INSERT INTO `schedules` VALUES (1355, '2025-02-16', 1, 15, 1, '2024-12-08 20:32:28', NULL, NULL, NULL, 21, 21, 9, 4, 5);
INSERT INTO `schedules` VALUES (1356, '2025-02-17', 1, 15, 1, '2024-12-08 20:32:28', NULL, NULL, NULL, 22, 22, 9, 4, 5);
INSERT INTO `schedules` VALUES (1357, '2025-02-23', 1, 15, 1, '2024-12-08 20:32:28', NULL, NULL, NULL, 23, 23, 9, 4, 5);
INSERT INTO `schedules` VALUES (1358, '2025-02-24', 1, 15, 1, '2024-12-08 20:32:28', NULL, NULL, NULL, 24, 24, 9, 4, 5);
INSERT INTO `schedules` VALUES (1359, '2025-03-02', 1, 15, 1, '2024-12-08 20:32:28', NULL, NULL, NULL, 25, 25, 9, 4, 5);
INSERT INTO `schedules` VALUES (1360, '2025-03-03', 1, 15, 1, '2024-12-08 20:32:28', NULL, NULL, NULL, 26, 26, 9, 4, 5);
INSERT INTO `schedules` VALUES (1361, '2025-03-09', 1, 15, 1, '2024-12-08 20:32:28', NULL, NULL, NULL, 27, 27, 9, 4, 5);
INSERT INTO `schedules` VALUES (1362, '2025-03-10', 1, 15, 1, '2024-12-08 20:32:28', NULL, NULL, NULL, 28, 28, 9, 4, 5);
INSERT INTO `schedules` VALUES (1363, '2025-03-16', 1, 15, 1, '2024-12-08 20:32:28', NULL, NULL, NULL, 29, 29, 9, 4, 5);
INSERT INTO `schedules` VALUES (1364, '2025-03-17', 1, 15, 1, '2024-12-08 20:32:28', NULL, NULL, NULL, 30, 30, 9, 4, 5);
INSERT INTO `schedules` VALUES (1365, '2025-03-23', 1, 15, 1, '2024-12-08 20:32:28', NULL, NULL, NULL, 31, 31, 9, 4, 5);
INSERT INTO `schedules` VALUES (1366, '2025-03-24', 1, 15, 1, '2024-12-08 20:32:28', NULL, NULL, NULL, 32, 32, 9, 4, 5);
INSERT INTO `schedules` VALUES (1367, '2024-12-04', 1, 16, 1, '2024-12-08 20:33:15', NULL, NULL, NULL, 1, 1, 9, 4, 6);
INSERT INTO `schedules` VALUES (1368, '2024-12-07', 1, 16, 1, '2024-12-08 20:33:15', NULL, NULL, NULL, 2, 2, 9, 4, 6);
INSERT INTO `schedules` VALUES (1369, '2024-12-11', 1, 16, 1, '2024-12-08 20:33:15', NULL, NULL, NULL, 3, 3, 9, 4, 6);
INSERT INTO `schedules` VALUES (1370, '2024-12-14', 1, 16, 1, '2024-12-08 20:33:15', NULL, NULL, NULL, 4, 4, 9, 4, 6);
INSERT INTO `schedules` VALUES (1371, '2024-12-18', 1, 16, 1, '2024-12-08 20:33:15', NULL, NULL, NULL, 5, 5, 9, 4, 6);
INSERT INTO `schedules` VALUES (1372, '2024-12-21', 1, 16, 1, '2024-12-08 20:33:15', NULL, NULL, NULL, 6, 6, 9, 4, 6);
INSERT INTO `schedules` VALUES (1373, '2024-12-25', 1, 16, 1, '2024-12-08 20:33:15', NULL, NULL, NULL, 7, 7, 9, 4, 6);
INSERT INTO `schedules` VALUES (1374, '2024-12-28', 1, 16, 1, '2024-12-08 20:33:15', NULL, NULL, NULL, 8, 8, 9, 4, 6);
INSERT INTO `schedules` VALUES (1375, '2025-01-01', 1, 16, 1, '2024-12-08 20:33:15', NULL, NULL, NULL, 9, 9, 9, 4, 6);
INSERT INTO `schedules` VALUES (1376, '2025-01-04', 1, 16, 1, '2024-12-08 20:33:15', NULL, NULL, NULL, 10, 10, 9, 4, 6);
INSERT INTO `schedules` VALUES (1377, '2025-01-08', 1, 16, 1, '2024-12-08 20:33:15', NULL, NULL, NULL, 11, 11, 9, 4, 6);
INSERT INTO `schedules` VALUES (1378, '2025-01-11', 1, 16, 1, '2024-12-08 20:33:15', NULL, NULL, NULL, 12, 12, 9, 4, 6);
INSERT INTO `schedules` VALUES (1379, '2025-01-15', 1, 16, 1, '2024-12-08 20:33:15', NULL, NULL, NULL, 13, 13, 9, 4, 6);
INSERT INTO `schedules` VALUES (1380, '2025-01-18', 1, 16, 1, '2024-12-08 20:33:15', NULL, NULL, NULL, 14, 14, 9, 4, 6);
INSERT INTO `schedules` VALUES (1381, '2025-01-22', 1, 16, 1, '2024-12-08 20:33:15', NULL, NULL, NULL, 15, 15, 9, 4, 6);
INSERT INTO `schedules` VALUES (1382, '2025-01-25', 1, 16, 1, '2024-12-08 20:33:15', NULL, NULL, NULL, 16, 16, 9, 4, 6);
INSERT INTO `schedules` VALUES (1383, '2025-01-29', 1, 16, 1, '2024-12-08 20:33:15', NULL, NULL, NULL, 17, 17, 9, 4, 6);
INSERT INTO `schedules` VALUES (1384, '2025-02-01', 1, 16, 1, '2024-12-08 20:33:15', NULL, NULL, NULL, 18, 18, 9, 4, 6);
INSERT INTO `schedules` VALUES (1385, '2025-02-05', 1, 16, 1, '2024-12-08 20:33:15', NULL, NULL, NULL, 19, 19, 9, 4, 6);
INSERT INTO `schedules` VALUES (1386, '2025-02-08', 1, 16, 1, '2024-12-08 20:33:15', NULL, NULL, NULL, 20, 20, 9, 4, 6);
INSERT INTO `schedules` VALUES (1387, '2025-02-12', 1, 16, 1, '2024-12-08 20:33:15', NULL, NULL, NULL, 21, 21, 9, 4, 6);
INSERT INTO `schedules` VALUES (1388, '2025-02-15', 1, 16, 1, '2024-12-08 20:33:15', NULL, NULL, NULL, 22, 22, 9, 4, 6);
INSERT INTO `schedules` VALUES (1389, '2025-02-19', 1, 16, 1, '2024-12-08 20:33:15', NULL, NULL, NULL, 23, 23, 9, 4, 6);
INSERT INTO `schedules` VALUES (1390, '2025-02-22', 1, 16, 1, '2024-12-08 20:33:15', NULL, NULL, NULL, 24, 24, 9, 4, 6);
INSERT INTO `schedules` VALUES (1391, '2025-02-26', 1, 16, 1, '2024-12-08 20:33:15', NULL, NULL, NULL, 25, 25, 9, 4, 6);
INSERT INTO `schedules` VALUES (1392, '2025-03-01', 1, 16, 1, '2024-12-08 20:33:15', NULL, NULL, NULL, 26, 26, 9, 4, 6);
INSERT INTO `schedules` VALUES (1393, '2025-03-05', 1, 16, 1, '2024-12-08 20:33:15', NULL, NULL, NULL, 27, 27, 9, 4, 6);
INSERT INTO `schedules` VALUES (1394, '2025-03-08', 1, 16, 1, '2024-12-08 20:33:15', NULL, NULL, NULL, 28, 28, 9, 4, 6);
INSERT INTO `schedules` VALUES (1395, '2025-03-12', 1, 16, 1, '2024-12-08 20:33:15', NULL, NULL, NULL, 29, 29, 9, 4, 6);
INSERT INTO `schedules` VALUES (1396, '2025-03-15', 1, 16, 1, '2024-12-08 20:33:15', NULL, NULL, NULL, 30, 30, 9, 4, 6);
INSERT INTO `schedules` VALUES (1397, '2025-03-19', 1, 16, 1, '2024-12-08 20:33:15', NULL, NULL, NULL, 31, 31, 9, 4, 6);
INSERT INTO `schedules` VALUES (1398, '2025-03-22', 1, 16, 1, '2024-12-08 20:33:15', NULL, NULL, NULL, 32, 32, 9, 4, 6);
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
) ENGINE=InnoDB AUTO_INCREMENT=123 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of sessions
-- ----------------------------
BEGIN;
INSERT INTO `sessions` VALUES (117, 14, 7, 5, 10, 5, 1, '2024-12-08 20:27:06', 1, NULL, NULL, 4);
INSERT INTO `sessions` VALUES (118, 14, 7, 5, 10, 8, 1, '2024-12-08 20:27:06', 1, NULL, NULL, 4);
INSERT INTO `sessions` VALUES (119, 15, 7, 8, 9, 7, 1, '2024-12-08 20:32:28', 1, NULL, NULL, 5);
INSERT INTO `sessions` VALUES (120, 15, 7, 8, 9, 8, 1, '2024-12-08 20:32:28', 1, NULL, NULL, 5);
INSERT INTO `sessions` VALUES (121, 16, 7, 9, 9, 3, 1, '2024-12-08 20:33:15', 1, NULL, NULL, 6);
INSERT INTO `sessions` VALUES (122, 16, 7, 9, 9, 6, 1, '2024-12-08 20:33:15', 1, NULL, NULL, 6);
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
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of shifts
-- ----------------------------
BEGIN;
INSERT INTO `shifts` VALUES (7, '17h30 - 19h', '17:31:00', '19:00:00', 1, NULL, 1, NULL, '2024-11-30 19:24:11', NULL);
INSERT INTO `shifts` VALUES (8, '19h15 - 20h45', '19:15:00', '20:45:00', 1, NULL, 1, NULL, '2024-11-30 19:25:03', NULL);
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
  PRIMARY KEY (`id`) USING BTREE,
  KEY `branch_id` (`branch_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=462 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of source_detail
-- ----------------------------
BEGIN;
INSERT INTO `source_detail` VALUES (461, 'Chiến dịch 1', 1, NULL, NULL, NULL, NULL, NULL);
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
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of sources
-- ----------------------------
BEGIN;
INSERT INTO `sources` VALUES (31, 'Marketing', NULL, 1, NULL, NULL);
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
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- ----------------------------
-- Records of student_logs
-- ----------------------------
BEGIN;
INSERT INTO `student_logs` VALUES (28, 6, 'Chuyển lên danh sách học sinh chính thức', '2024-11-30 21:03:15', 1, 1);
INSERT INTO `student_logs` VALUES (29, 7, 'Chuyển lên danh sách học sinh chính thức', '2024-11-30 21:20:21', 1, 1);
INSERT INTO `student_logs` VALUES (30, 8, 'Chuyển lên danh sách học sinh chính thức', '2024-12-09 12:27:46', 13, 1);
INSERT INTO `student_logs` VALUES (31, 9, 'Chuyển lên danh sách học sinh chính thức', '2024-12-11 09:35:15', 13, 1);
INSERT INTO `student_logs` VALUES (32, 9, 'Cập nhật thông tin học sinh: ', '2024-12-11 09:35:22', 13, 1);
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

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
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `index_student` (`lms_code`) USING BTREE,
  KEY `province_id` (`province_id`) USING BTREE,
  KEY `district_id` (`district_id`) USING BTREE,
  KEY `gud_mobile1` (`gud_mobile1`) USING BTREE,
  KEY `gud_mobile2` (`gud_mobile2`) USING BTREE,
  KEY `branch_id` (`branch_id`) USING BTREE,
  KEY `status` (`status`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of students
-- ----------------------------
BEGIN;
INSERT INTO `students` VALUES (6, 'LMS000006', 'Lương Bảo Hân', 'Lương', 'Bảo', 'Hân', NULL, 'F', 0, '2018-09-08', '0389941902', 'Lương Thành Công', NULL, NULL, NULL, NULL, NULL, 1, 5, NULL, NULL, NULL, '2024-11-30 21:03:14', 1, '2024-12-11 16:21:29', NULL, NULL, NULL, NULL, 4, NULL, NULL, NULL, NULL, NULL, 'M', NULL, NULL, NULL, 1, 461, 31, '/static/upload/avatar_students/2024_12/avatar.png');
INSERT INTO `students` VALUES (7, 'LMS000007', 'Lương Bảo Long', 'Lương', 'Bảo', 'Long', NULL, 'M', 0, '2021-05-27', '0389941902', 'Lương Thành Công', NULL, NULL, NULL, NULL, NULL, 1, 5, NULL, NULL, NULL, '2024-11-30 21:20:20', 1, '2024-11-30 16:06:18', NULL, NULL, NULL, NULL, 4, NULL, NULL, NULL, NULL, NULL, 'M', NULL, NULL, NULL, 1, 461, 31, '/images/common/avatar-boy.svg');
INSERT INTO `students` VALUES (8, 'LMS000008', 'trần bảo nam', 'trần', 'bảo', 'nam', NULL, 'M', 0, '2013-03-18', '0987683713', 'Đinh Thị Ngọc Hân', NULL, NULL, NULL, NULL, NULL, 22, 195, NULL, NULL, NULL, '2024-12-09 12:27:46', 13, '2024-12-09 05:27:46', NULL, NULL, NULL, NULL, 6, NULL, NULL, NULL, NULL, NULL, 'F', NULL, 3, NULL, 1, 461, 31, '/images/common/avatar-boy.svg');
INSERT INTO `students` VALUES (9, 'LMS000009', 'Hồ Bảo Phong', 'Hồ', 'Bảo', 'Phong', NULL, 'M', 0, '2014-04-14', '0868036633', 'Hồ Thị Ngân', 'a@gmail.com', NULL, NULL, NULL, NULL, 22, 195, NULL, NULL, NULL, '2024-12-11 09:35:15', 13, '2024-12-11 09:35:22', 13, NULL, NULL, NULL, 6, NULL, NULL, NULL, '2024-12-11', NULL, 'F', NULL, 3, NULL, 1, 461, 31, '/images/common/avatar-boy.svg');
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
) ENGINE=InnoDB AUTO_INCREMENT=231 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- ----------------------------
-- Records of subject_has_class
-- ----------------------------
BEGIN;
INSERT INTO `subject_has_class` VALUES (228, 1, 14, 32, 1, '2024-12-08 20:27:06');
INSERT INTO `subject_has_class` VALUES (229, 1, 15, 32, 1, '2024-12-08 20:32:28');
INSERT INTO `subject_has_class` VALUES (230, 1, 16, 32, 1, '2024-12-08 20:33:15');
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
INSERT INTO `subjects` VALUES (1, 'M01', 'Tiếng anh', NULL, 1, NULL, NULL, NULL, NULL);
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
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of term_student_user
-- ----------------------------
BEGIN;
INSERT INTO `term_student_user` VALUES (6, 6, 1, NULL, NULL, NULL, 2, NULL, '2024-11-30 21:03:15', NULL, 1, 4, 1, NULL);
INSERT INTO `term_student_user` VALUES (7, 7, 1, NULL, NULL, NULL, 2, NULL, '2024-11-30 21:20:21', NULL, 1, 4, 1, NULL);
INSERT INTO `term_student_user` VALUES (8, 8, 13, NULL, NULL, NULL, 2, NULL, '2024-12-09 12:27:46', NULL, 1, 6, 13, NULL);
INSERT INTO `term_student_user` VALUES (9, 9, 13, NULL, NULL, NULL, 2, NULL, '2024-12-11 09:35:15', NULL, 1, 6, 13, NULL);
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
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of tuition_fee
-- ----------------------------
BEGIN;
INSERT INTO `tuition_fee` VALUES (7, 'Gói CK Phonics 1 tháng', NULL, 8, NULL, 8, 4000000, NULL, NULL, 4000000, '2024-11-30 19:38:11', 1, '2024-12-08 20:29:08', 1, '2024-11-30', '2024-12-31', NULL, NULL, 1, 0, '4,5,6', NULL, 1, 1);
INSERT INTO `tuition_fee` VALUES (8, 'Gói CK Phonics 6 tháng', NULL, 8, NULL, 48, 4000000, NULL, NULL, 4000000, '2024-11-30 19:41:48', 1, '2024-12-08 20:29:03', 1, '2024-11-01', '2024-12-31', NULL, NULL, 1, 0, '4,5,6', NULL, 6, 1);
INSERT INTO `tuition_fee` VALUES (9, 'Gói CK Phonics 3 tháng', NULL, 8, NULL, 24, 12000000, NULL, NULL, 12000000, '2024-11-30 19:42:23', 1, '2024-12-08 20:34:14', 1, '2024-11-01', '2024-12-31', NULL, NULL, 1, 0, '4,5,6', NULL, 3, 1);
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
  PRIMARY KEY (`id`) USING BTREE,
  KEY `tuition_id` (`tuition_fee_id`) USING BTREE,
  KEY `exchange_tuition_id` (`exchange_tuition_fee_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb3;

-- ----------------------------
-- Records of tuition_fee_relation
-- ----------------------------
BEGIN;
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
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of users
-- ----------------------------
BEGIN;
INSERT INTO `users` VALUES (1, 'Lương Thành Công', '0389941902', 'admin@admin.com', '2020-08-27 02:04:43', '$2y$10$upeEWEPRUHvriPrThSy8hOMzfqxFGRFPrBP0UgkBwC8C54RBz/6C6', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vbG9jYWwtZXJwLmNvbmduZ2hlZ2lhb2R1Yy5jb20vYXBpL2F1dGgvbG9naW4iLCJpYXQiOjE3MzQwNzk0MjAsImV4cCI6MTczNDY4NDIyMCwibmJmIjoxNzM0MDc5NDIwLCJqdGkiOiJZZUl4SURFTGFXSXlZTGQ0Iiwic3ViIjoiMSIsInBydiI6Ijg3ZTBhZjFlZjlmZDE1ODEyZmRlYzk3MTUzYTE0ZTBiMDQ3NTQ2YWEifQ.RxgrMAs_XTvg8vvNjilodLDd9IEudI7FF15CC9Rj6ng', 'admin,user', 1, 'EQt3UDeo4F', '2020-08-27 02:04:43', '2024-12-07 19:58:30', NULL, 1, NULL, 6, 'Trung tâm Xgenius Nguyễn Văn Cừ, Trung tâm Xgenius Vĩnh Phúc, Trung tâm Xgenius Cẩm Phả', 'admin', NULL, NULL, 0, 'admin', NULL, '');
INSERT INTO `users` VALUES (2, 'Giám đốc trung tâm', '0388888888', 'gdtt@gmail.com', NULL, '$2y$10$Oj6RZMdDQ43LadutsxpXZet.dVDDYm7rd7q1Aj8541XMwvgJdSDPS', NULL, '', 1, NULL, '2024-05-12 14:43:31', NULL, 1, NULL, NULL, 3, 'Trung tâm Giảng Võ, Trung tâm Mỹ Đình, Trung tâm Cầu Giấy', 'C00002', NULL, NULL, 0, 'Giám đốc trung tâm', NULL, NULL);
INSERT INTO `users` VALUES (3, 'Giáo viên', '0388888888', 'giaovien@gmail.com', NULL, '$2y$10$Q33qboMMmLejTZRJ28GsMOv5f.ZGOnfd6BJhDnQztAlTDNHMVVYPa', NULL, '', 1, NULL, '2024-05-12 14:44:40', NULL, 1, NULL, NULL, 3, 'Trung tâm Giảng Võ, Trung tâm Mỹ Đình, Trung tâm Cầu Giấy', 'C00003', NULL, NULL, 0, 'Giáo viên', NULL, NULL);
INSERT INTO `users` VALUES (4, 'Vận hành', '0388888888', 'vanhanh@gmail.com', NULL, '$2y$10$AuQRTmWhgKyfvUau7zOsb.nv7u6/jls8o2uJNt0RpoQhXwhElqENK', NULL, '', 1, NULL, '2024-05-12 14:45:39', '2024-12-08 20:30:31', 1, 1, NULL, 6, 'Trung tâm Xgenius Vĩnh Phúc, Trung tâm Xgenius Cẩm Phả', 'C00004', NULL, NULL, 0, 'Vận hành', NULL, NULL);
INSERT INTO `users` VALUES (5, 'CSKH', '0388888888', 'cskh@gmail.com', NULL, '$2y$10$fniRs3v1mKsaJFNgkffhb.FDotIpYgnBjqd/XIXxEbD.ss/4yRmo2', NULL, '', 1, NULL, '2024-05-12 14:46:47', '2024-05-24 10:37:12', 1, 1, NULL, 2, 'Trung tâm Mỹ Đình', 'C00005', NULL, NULL, 0, 'CSKH', NULL, NULL);
INSERT INTO `users` VALUES (6, 'Giáo viên 1', '0388888888', 'giaovien1@gmail.com', NULL, '$2y$10$PZ21PfVuQ6FGt/D93XyHhetjnmLMdBJ5LA9703PpOk.fIC1kwJeya', NULL, '', 1, NULL, '2024-05-12 19:46:09', '2024-12-08 19:30:01', 1, 1, NULL, 4, 'Trung tâm Xgenius Nguyễn Văn Cừ', 'C00006', NULL, NULL, 0, 'Vận hành', NULL, NULL);
INSERT INTO `users` VALUES (7, 'Vận hành 1', '0388888888', 'vanhanh1@gmail.com', NULL, '$2y$10$2y/yvZK36FbSozhMe8gkUOKSx8e84bJZsRwxAKzjTf.z3ol4urnHO', NULL, '', 1, NULL, '2024-05-12 19:46:53', NULL, 1, NULL, NULL, 3, 'Trung tâm Giảng Võ, Trung tâm Mỹ Đình, Trung tâm Cầu Giấy', 'C00007', NULL, NULL, 0, 'Vận hành', NULL, NULL);
INSERT INTO `users` VALUES (8, 'CSKH 1', '0388888888', 'cskh1@gmail.com', NULL, '$2y$10$iUiGUMExBuJnMS4CglN5tub4My34DS7EATVXYJ.zFPS7FlzETHO.G', NULL, '', 1, NULL, '2024-05-12 19:47:32', NULL, 1, NULL, NULL, 3, 'Trung tâm Giảng Võ, Trung tâm Mỹ Đình, Trung tâm Cầu Giấy', 'C00008', NULL, NULL, 0, 'CSKH', NULL, NULL);
INSERT INTO `users` VALUES (9, 'Mary', '0972416618', 'test@gmail.com', NULL, '$2y$10$32SdEgmIXE3X0BpbLT/yPOdQmkhG9DBX/jRPu.4eYaeVu2Y1majVG', NULL, '', 1, NULL, '2024-11-30 19:35:51', '2024-12-08 20:30:50', 1, 1, NULL, 6, 'Trung tâm Xgenius Nguyễn Văn Cừ, Trung tâm Xgenius Vĩnh Phúc, Trung tâm Xgenius Cẩm Phả', 'X001', NULL, NULL, 0, 'Giáo viên', NULL, NULL);
INSERT INTO `users` VALUES (10, 'Thomas', '0972416619', 'test@gmail.com', NULL, '$2y$10$M49NJpKS7YOvziY8ZD147uS6WmsX/RN3CAKyzbzE1xKMyKGVGx/Xi', NULL, '', 1, NULL, '2024-11-30 19:36:27', NULL, 1, NULL, NULL, 4, 'Trung tâm Xgenius Long Biên', 'X002', NULL, NULL, 0, 'Giáo viên', NULL, NULL);
INSERT INTO `users` VALUES (11, 'GĐTT Nguyễn Văn Cừ', '0111111111', 'nguyenvancu@xgenius.edu.vn', NULL, '$2y$10$qH7gaimwpmJkHOgmyEoP2OAL4g6gS4kHFPovT9j5sQ4Y.vPau53GG', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwczovL2VycC54Z2VuaXVzLmVkdS52bi9hcGkvYXV0aC9sb2dpbiIsImlhdCI6MTczMzU3ODgyNywiZXhwIjoxNzM0MTgzNjI3LCJuYmYiOjE3MzM1Nzg4MjcsImp0aSI6IklYMDFpYXNkOXozaHgyODAiLCJzdWIiOiIxMSIsInBydiI6Ijg3ZTBhZjFlZjlmZDE1ODEyZmRlYzk3MTUzYTE0ZTBiMDQ3NTQ2YWEifQ.JMWkY9afPDsMifTuqtM2H_Q6Ioezt1BLo6tb36K8oBQ', '', 1, NULL, '2024-12-07 20:00:05', NULL, 1, NULL, NULL, 4, 'Trung tâm Xgenius Nguyễn Văn Cừ', 'nguyenvancu', NULL, NULL, 0, 'ceo', NULL, NULL);
INSERT INTO `users` VALUES (12, 'GĐTT Vĩnh Phúc', '0111111112', 'vinhphuc@xgenius.edu.vn', NULL, '$2y$10$5LlOFYwdZRH5TK7MC1ifN.t0JJJmXoG5BLy9Dp4VkjF8NPcazQwu6', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwczovL2VycC54Z2VuaXVzLmVkdS52bi9hcGkvYXV0aC9sb2dpbiIsImlhdCI6MTczMzcxMzg0OCwiZXhwIjoxNzM0MzE4NjQ4LCJuYmYiOjE3MzM3MTM4NDgsImp0aSI6InYwR1p3MXR6UVNTRmh4UzkiLCJzdWIiOiIxMiIsInBydiI6Ijg3ZTBhZjFlZjlmZDE1ODEyZmRlYzk3MTUzYTE0ZTBiMDQ3NTQ2YWEifQ.2kvVIujaSSPKPP7FRJwuIrAv8SB3fGMDh0KKaeCujaw', '', 1, NULL, '2024-12-07 20:01:33', NULL, 1, NULL, NULL, 5, 'Trung tâm Xgenius Vĩnh Phúc', 'vinhphuc', NULL, NULL, 0, 'ceo', NULL, NULL);
INSERT INTO `users` VALUES (13, 'GĐTT Cẩm Phả', '0111111113', 'campha@xgenius.edu.vn', NULL, '$2y$10$vlGjf4kGGQzyFzqTJ5THVeRySbza3wPEdV.R4gAwOLz57pLB2z/Ey', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwczovL2VycC54Z2VuaXVzLmVkdS52bi9hcGkvYXV0aC9sb2dpbiIsImlhdCI6MTczMzg4MzkwMSwiZXhwIjoxNzM0NDg4NzAxLCJuYmYiOjE3MzM4ODM5MDEsImp0aSI6IkFJdmR1VHFubURTRDhJUWMiLCJzdWIiOiIxMyIsInBydiI6Ijg3ZTBhZjFlZjlmZDE1ODEyZmRlYzk3MTUzYTE0ZTBiMDQ3NTQ2YWEifQ.HSoXwUGCnWX0a7FdKv6-sQmldiffhHBiJShYcc3IVLU', '', 1, NULL, '2024-12-07 20:02:21', NULL, 1, NULL, NULL, 6, 'Trung tâm Xgenius Cẩm Phả', 'campha', NULL, NULL, 0, 'ceo', NULL, NULL);
COMMIT;

SET FOREIGN_KEY_CHECKS = 1;
