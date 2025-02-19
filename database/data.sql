/*
 Navicat Premium Data Transfer

 Source Server         : GCP - ERP
 Source Server Type    : MySQL
 Source Server Version : 80041
 Source Host           : 34.143.164.62:3306
 Source Schema         : logiclab_erp

 Target Server Type    : MySQL
 Target Server Version : 80041
 File Encoding         : 65001

 Date: 19/02/2025 14:29:48
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
  PRIMARY KEY (`id`),
  KEY `b2b_source_id` (`b2b_source_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of b2b_campaigns
-- ----------------------------
BEGIN;
INSERT INTO `b2b_campaigns` VALUES (5, 3, 'chính sách b2b cho VCB', '2024-12-01', '2024-12-31', 'nội dung chính sách', '2024-12-18 11:31:46', NULL, 1, NULL, 1, '', '[{\"tuition_fee\":{\"name\":\"G\\u00f3i KINDY 3 th\\u00e1ng\",\"id\":9,\"available_date\":\"2024-11-01\",\"expired_date\":\"2024-12-31\",\"product_name\":\"KINDY\"},\"amount\":\"1000000\",\"bonus_session\":\"4\"},{\"tuition_fee\":{\"name\":\"G\\u00f3i KINDY 6 th\\u00e1ng\",\"id\":8,\"available_date\":\"2024-11-01\",\"expired_date\":\"2024-12-31\",\"product_name\":\"KINDY\"},\"amount\":\"2000000\",\"bonus_session\":\"8\"}]');
INSERT INTO `b2b_campaigns` VALUES (6, 3, 'chính sách b2b cho VCB tháng 12/2024', '2024-12-01', '2024-12-31', 'nội dung chính sách', '2024-12-18 11:41:42', '2024-12-18 11:54:59', 1, 1, 1, '8,7', '[{\"tuition_fee\":{\"name\":\"G\\u00f3i KINDY 6 th\\u00e1ng\",\"id\":8,\"available_date\":\"2024-11-01\",\"expired_date\":\"2024-12-31\",\"product_name\":\"KINDY\"},\"amount\":\"2000000\",\"bonus_session\":\"8\"},{\"tuition_fee\":{\"name\":\"G\\u00f3i KINDY 12 th\\u00e1ng\",\"id\":7,\"available_date\":\"2024-11-30\",\"expired_date\":\"2024-12-31\",\"product_name\":\"KINDY\"},\"amount\":\"5000000\",\"bonus_session\":\"16\"}]');
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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of b2b_sources
-- ----------------------------
BEGIN;
INSERT INTO `b2b_sources` VALUES (3, 'VCBank', 'VCBank', 1, '2024-12-18 00:56:06', NULL, 1, NULL);
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
INSERT INTO `branch_has_user` VALUES (9, 1);
INSERT INTO `branch_has_user` VALUES (9, 15);
INSERT INTO `branch_has_user` VALUES (9, 16);
INSERT INTO `branch_has_user` VALUES (9, 17);
INSERT INTO `branch_has_user` VALUES (9, 18);
INSERT INTO `branch_has_user` VALUES (9, 19);
INSERT INTO `branch_has_user` VALUES (9, 20);
INSERT INTO `branch_has_user` VALUES (9, 21);
INSERT INTO `branch_has_user` VALUES (9, 22);
INSERT INTO `branch_has_user` VALUES (9, 23);
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
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of branches
-- ----------------------------
BEGIN;
INSERT INTO `branches` VALUES (9, 'Trung Tâm Hà Nam', 'LOG01', NULL, NULL, NULL, NULL, NULL, NULL, '2025-02-17', '2025-02-17 15:01:04', NULL, 1, NULL, 1, NULL, NULL, NULL, NULL, 'loghanam@logiclab.vn', NULL);
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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of c2c_campaigns
-- ----------------------------
BEGIN;
INSERT INTO `c2c_campaigns` VALUES (8, 'Chính sánh giới thiệu khách hàng T12/2024', '2024-12-01', '2024-12-31', NULL, '2024-12-21 19:43:14', '2024-12-21 19:47:23', 1, 1, 1, '8', '[{\"tuition_fee\":{\"name\":\"G\\u00f3i KINDY 6 th\\u00e1ng\",\"id\":8,\"available_date\":\"2024-11-01\",\"expired_date\":\"2024-12-31\",\"status\":1,\"product_name\":\"KINDY\"},\"amount\":\"1000000\",\"bonus_session\":\"4\",\"ref_amount\":\"1000000\",\"ref_bonus_session\":\"4\"}]');
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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of campaigns
-- ----------------------------
BEGIN;
INSERT INTO `campaigns` VALUES (3, 'chiến dịch 1', 4, '2024-12-01', '2024-12-31', 20000000, 1000000, 3, 8, 'ghi chú', '2024-12-17 22:48:56', '2024-12-17 23:39:35', 1, 1, 1);
INSERT INTO `campaigns` VALUES (4, 'chiến dịch 2', 0, '2024-12-01', '2024-12-31', 3000000, NULL, NULL, NULL, NULL, '2024-12-17 23:40:17', NULL, 1, NULL, 1);
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
  PRIMARY KEY (`id`) USING BTREE,
  KEY `program_id` (`program_id`) USING BTREE,
  KEY `product_id` (`product_id`) USING BTREE,
  KEY `branch_id` (`branch_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of classes
-- ----------------------------
BEGIN;
INSERT INTO `classes` VALUES (18, 21, 'Seedbed 1 TF5 R2', 14, 24, 15, NULL, '2025-02-11', '2026-01-09', NULL, '3,6', 16, '2025-02-17 16:10:55', '2025-02-17 16:15:10', 15, 15, 9, 1, 96, 1);
INSERT INTO `classes` VALUES (19, 21, 'Sprout 2 TF5 R1', 14, 24, 15, NULL, '2025-02-11', '2026-01-09', NULL, '3,6', 16, '2025-02-17 16:13:37', '2025-02-17 16:14:50', 15, 15, 9, 1, 96, 1);
INSERT INTO `classes` VALUES (20, 22, 'Cocoon 1A TF5 IG', 14, 24, 15, NULL, '2025-02-11', '2026-01-09', NULL, '3,6', 16, '2025-02-17 16:15:43', NULL, 15, NULL, 9, 1, 96, 1);
INSERT INTO `classes` VALUES (21, 22, 'Cocoon 1B TF6 IG', 14, 24, 15, NULL, '2025-02-11', '2026-01-09', NULL, '3,6', 16, '2025-02-17 16:16:28', NULL, 15, NULL, 9, 1, 96, 1);
INSERT INTO `classes` VALUES (22, 21, 'Seed 1 TF6 R1', 14, 24, 15, NULL, '2025-02-11', '2026-01-09', NULL, '3,6', 16, '2025-02-17 16:17:55', NULL, 15, NULL, 9, 1, 96, 1);
INSERT INTO `classes` VALUES (23, 21, 'Seed 2 WS5 R1', 14, 24, 15, NULL, '2025-02-12', '2026-01-10', NULL, '4,7', 16, '2025-02-17 16:18:45', NULL, 15, NULL, 9, 1, 96, 1);
INSERT INTO `classes` VALUES (24, 22, 'Caterpillar 1 WS5 IG', 14, 24, 15, NULL, '2025-02-12', '2026-01-10', NULL, '4,7', 16, '2025-02-17 16:19:24', NULL, 15, NULL, 9, 1, 96, 1);
INSERT INTO `classes` VALUES (25, 21, 'Seed 1 WS6 R1', 14, 24, 15, NULL, '2025-02-12', '2026-01-10', NULL, '4,7', 16, '2025-02-17 16:19:59', NULL, 15, NULL, 9, 1, 96, 1);
INSERT INTO `classes` VALUES (26, 22, 'Caterpillar 2 WS6 IG', 14, 24, 15, NULL, '2025-02-12', '2026-01-10', NULL, '4,7', 16, '2025-02-17 16:20:31', NULL, 15, NULL, 9, 1, 96, 1);
INSERT INTO `classes` VALUES (27, 21, 'Sprout 1 TS5 R1', 14, 24, 15, NULL, '2025-02-13', '2026-01-11', NULL, '5,8', 16, '2025-02-17 16:25:40', NULL, 15, NULL, 9, 1, 96, 1);
INSERT INTO `classes` VALUES (28, 22, 'Seedbed 2 TS5 R6', 14, 24, 15, NULL, '2025-02-13', '2026-01-11', NULL, '5,8', 16, '2025-02-17 16:26:33', NULL, 15, NULL, 9, 1, 96, 1);
INSERT INTO `classes` VALUES (29, 21, 'Sprout 3 TS6 R1', 14, 24, 15, NULL, '2025-02-13', '2026-01-11', NULL, '5,8', 16, '2025-02-17 16:34:27', NULL, 15, NULL, 9, 1, 96, 1);
INSERT INTO `classes` VALUES (30, 22, 'Cocoon 1C SS2 IG', 14, 24, 15, NULL, '2025-02-15', '2026-01-11', NULL, '7,8', 16, '2025-02-17 16:37:25', NULL, 15, NULL, 9, 1, 96, 1);
INSERT INTO `classes` VALUES (31, 21, 'Seedbed 2 SS2 R1', 14, 24, 15, NULL, '2025-02-15', '2026-01-11', NULL, '7,8', 16, '2025-02-17 16:38:02', NULL, 15, NULL, 9, 1, 96, 1);
INSERT INTO `classes` VALUES (32, 22, 'Sprout 2 SS3 R6', 14, 24, 15, NULL, '2025-02-15', '2026-01-11', NULL, '7,8', 16, '2025-02-17 16:38:29', NULL, 15, NULL, 9, 1, 96, 1);
INSERT INTO `classes` VALUES (33, 22, 'Seed 1 SS4 R6', 14, 24, 15, NULL, '2025-02-15', '2026-01-11', NULL, '7,8', 16, '2025-02-17 16:38:58', NULL, 15, NULL, 9, 1, 96, 1);
INSERT INTO `classes` VALUES (34, 23, 'Seedbed 1 SS4 R2', 14, 24, 15, NULL, '2025-02-15', '2026-01-11', NULL, '7,8', 16, '2025-02-17 16:39:25', NULL, 15, NULL, 9, 1, 96, 1);
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
  PRIMARY KEY (`id`) USING BTREE,
  KEY `ec_id` (`ec_id`) USING BTREE,
  KEY `tuition_fee_id` (`tuition_fee_id`) USING BTREE,
  KEY `creator_id` (`creator_id`) USING BTREE,
  KEY `cm_id` (`cm_id`) USING BTREE,
  KEY `student_id` (`student_id`) USING BTREE,
  KEY `branch_id` (`branch_id`) USING BTREE,
  KEY `ceo_branch_id` (`ceo_branch_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of contracts
-- ----------------------------
BEGIN;
INSERT INTO `contracts` VALUES (7, 1, 'C000007', 7, NULL, NULL, 4, 2, 1, NULL, 6, 6, 8, 10, 8, 8, 24000000, 24000000, 48, 19600000, 19600000, 19600000, 0, 4400000, NULL, 3, 2400000, 10, '', 0, 0, 56, 48, 0, 56, 0, 2, 0, 56, 0, NULL, NULL, '2024-12-01', NULL, 14, '2025-01-09', '2025-07-20', 6, '2024-12-20 12:15:41', 1, '2025-01-07 23:04:09', 1, NULL, NULL, 0, NULL, '2025-01-07', '2025-07-20', NULL, '2025-02-19', 6, 2000000, 8);
INSERT INTO `contracts` VALUES (8, 1, 'C000008', 10, NULL, NULL, 4, 2, 5, NULL, 6, 6, 8, 10, 8, 8, 24000000, 24000000, 48, 24000000, 24000000, 24000000, 0, NULL, NULL, NULL, NULL, NULL, '', 0, 0, 48, 48, 0, 48, 0, 2, 0, 48, 0, NULL, NULL, '2024-12-01', NULL, 14, '2025-01-09', '2025-06-22', 6, '2024-12-21 20:45:35', 1, '2025-01-07 23:04:09', 1, NULL, NULL, 0, NULL, '2024-12-21', '2025-06-22', NULL, '2025-02-19', NULL, NULL, NULL);
INSERT INTO `contracts` VALUES (9, 1, 'C000009', 6, NULL, NULL, 4, 2, 1, NULL, 6, 6, 8, 10, 8, 8, 24000000, 24000000, 48, 24000000, 24000000, 24000000, 0, NULL, NULL, NULL, NULL, NULL, '', 0, 0, 48, 48, 0, 48, 0, 2, 0, 48, 0, NULL, NULL, '2025-01-01', NULL, 14, '2025-01-09', '2025-06-22', 6, '2025-01-07 22:56:43', 1, '2025-01-07 23:04:09', 1, NULL, NULL, 0, NULL, '2025-01-01', '2025-06-22', NULL, '2025-02-19', NULL, NULL, NULL);
INSERT INTO `contracts` VALUES (10, 1, 'C000010', 11, NULL, NULL, 9, 16, 17, NULL, 15, 15, 14, 24, 10, 10, 1900000, 1900000, 8, 1900000, 1900000, 1900000, 0, NULL, NULL, NULL, NULL, NULL, '', 0, 0, 8, 8, 0, 8, 0, 0, 1, 7, 0, NULL, NULL, '2025-02-17', NULL, 18, '2025-02-18', '2025-03-14', 6, '2025-02-17 15:25:10', 15, '2025-02-18 10:31:51', 15, NULL, NULL, 0, NULL, '2025-02-17', '2025-03-14', NULL, '2025-02-19', NULL, NULL, NULL);
INSERT INTO `contracts` VALUES (11, 1, 'C000011', 12, NULL, NULL, 9, 16, 17, NULL, 15, 15, 14, 24, 10, 10, 1900000, 1900000, 8, 1900000, 1900000, 1900000, 0, NULL, NULL, NULL, NULL, NULL, '', 0, 0, 8, 8, 0, 8, 0, 0, 1, 7, 0, NULL, NULL, '2025-02-18', NULL, 18, '2025-02-18', '2025-03-14', 6, '2025-02-18 10:37:57', 15, '2025-02-18 10:40:08', 15, NULL, NULL, 0, NULL, '2025-02-18', '2025-03-14', NULL, '2025-02-19', NULL, NULL, NULL);
INSERT INTO `contracts` VALUES (12, 1, 'C000012', 15, NULL, NULL, 9, 16, 17, NULL, 15, 15, 14, 24, 10, 10, 1900000, 1900000, 8, 1900000, 1900000, 1900000, 0, NULL, NULL, NULL, NULL, NULL, '', 0, 0, 8, 8, 0, 8, 0, 0, 1, 7, 0, NULL, NULL, '2025-02-18', NULL, 18, '2025-02-18', '2025-03-14', 6, '2025-02-18 10:38:26', 15, '2025-02-18 10:40:08', 15, NULL, NULL, 0, NULL, '2025-02-18', '2025-03-14', '2025-02-19', '2025-02-19', NULL, NULL, NULL);
INSERT INTO `contracts` VALUES (13, 1, 'C000013', 14, NULL, NULL, 9, 16, 17, NULL, 15, 15, 14, 24, 10, 10, 1900000, 1900000, 8, 1900000, 1900000, 1900000, 0, NULL, NULL, NULL, NULL, NULL, '', 0, 0, 8, 8, 0, 8, 0, 0, 1, 7, 0, NULL, NULL, '2025-02-18', NULL, 18, '2025-02-18', '2025-03-14', 6, '2025-02-18 10:38:43', 15, '2025-02-18 10:40:08', 15, NULL, NULL, 0, NULL, '2025-02-18', '2025-03-14', NULL, '2025-02-19', NULL, NULL, NULL);
INSERT INTO `contracts` VALUES (14, 1, 'C000014', 13, NULL, NULL, 9, 16, 17, NULL, 15, 15, 14, 24, 10, 10, 1900000, 1900000, 8, 1900000, 1900000, 1900000, 0, NULL, NULL, NULL, NULL, NULL, '', 0, 0, 8, 8, 0, 8, 0, 0, 1, 7, 0, NULL, NULL, '2025-02-18', NULL, 18, '2025-02-18', '2025-03-14', 6, '2025-02-18 10:39:00', 15, '2025-02-18 10:40:08', 15, NULL, NULL, 0, NULL, '2025-02-18', '2025-03-14', NULL, '2025-02-19', NULL, NULL, NULL);
INSERT INTO `contracts` VALUES (15, 1, 'C000015', 15, NULL, NULL, 9, 16, 17, NULL, 15, 15, 14, NULL, 10, 10, 1900000, 1900000, 8, 1900000, 1900000, 1900000, 0, NULL, NULL, NULL, NULL, NULL, '', 0, 0, 8, 8, 0, 8, 0, 0, 0, 8, 0, NULL, NULL, '2025-02-18', NULL, NULL, NULL, NULL, 3, '2025-02-18 16:57:23', 15, '2025-02-19 00:09:58', 1, NULL, NULL, 1, NULL, '2025-02-19', NULL, NULL, '2025-02-19', NULL, NULL, NULL);
INSERT INTO `contracts` VALUES (16, 1, 'C000016', 17, NULL, NULL, 9, 16, 17, NULL, NULL, NULL, 14, NULL, 12, 12, 10830000, 10830000, 48, 1000000, 10830000, 1000000, 9830000, NULL, NULL, NULL, NULL, NULL, '', 0, 0, 48, 48, 0, 0, 0, 0, 0, 0, 0, NULL, NULL, '2025-06-01', NULL, NULL, NULL, NULL, 2, '2025-02-18 17:20:15', 15, '2025-02-18 17:21:14', 15, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2025-02-19', NULL, NULL, NULL);
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
  `c2c_mobile` varchar(50) DEFAULT NULL,
  `c2c_contract_id` int DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `code` (`code`) USING BTREE,
  KEY `campaign_id` (`campaign_id`) USING BTREE,
  KEY `c2c_mobile` (`c2c_mobile`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=304 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of coupons
-- ----------------------------
BEGIN;
INSERT INTO `coupons` VALUES (297, 'G8W1L6', NULL, 0, 1, 0, NULL, NULL, 0, 1000000, 8, NULL, '2024-12-01', '2024-12-31', NULL, NULL, 2, '2024-12-17 23:34:54', 1, '2024-12-17 23:39:36', 1, 3, NULL, NULL, NULL);
INSERT INTO `coupons` VALUES (298, '9EC0F4', NULL, 0, 1, 0, NULL, NULL, 0, 1000000, 8, NULL, '2024-12-01', '2024-12-31', NULL, NULL, 1, '2024-12-17 23:34:55', 1, '2024-12-17 23:39:36', 1, 3, NULL, NULL, NULL);
INSERT INTO `coupons` VALUES (299, 'KVNKWT', NULL, 0, 1, 0, NULL, NULL, 0, 1000000, 8, NULL, '2024-12-01', '2024-12-31', NULL, NULL, 1, '2024-12-17 23:34:55', 1, '2024-12-18 00:00:15', 1, 3, NULL, NULL, NULL);
INSERT INTO `coupons` VALUES (302, '2OXO6A', NULL, 1, 1, 0, NULL, NULL, 0, 1000000, 4, NULL, '2024-12-21', '2025-12-21', NULL, NULL, 1, '2024-12-21 20:55:29', 1, NULL, NULL, 8, 3, '0389941902', 8);
INSERT INTO `coupons` VALUES (303, 'TDF27F', NULL, 1, 1, 0, NULL, NULL, 0, 1000000, 4, NULL, '2024-12-21', '2025-12-21', NULL, NULL, 1, '2024-12-21 20:55:29', 1, NULL, NULL, 8, 3, '0389941952', 8);
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
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- ----------------------------
-- Records of crm_import_parents
-- ----------------------------
BEGIN;
INSERT INTO `crm_import_parents` VALUES (25, 10126, 'Nguyễn Thị Huế', 'demo@gmail.com', '0954112541', '0954112542', 'hà nội', 'ghi chú', 1, '2024-12-11 23:15:27', 1, '', 'Nguyễn Văn A', 'Nguyễn Văn B', '2015-07-08', '2016-08-16', 'C00001', NULL, '2022-07-24 00:00:00', 'C01', 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `crm_import_parents` VALUES (26, 10127, 'Đào Thị Phương Dung', '', '0367341544', '', '', '', 1, '2025-01-07 22:26:22', 1, '', 'Nguyễn Gia Huy', '', NULL, NULL, '', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `crm_import_parents` VALUES (27, 10127, 'Nguyễn Thị Hải Vân', '', '0988837511', '', '', '', 1, '2025-01-07 22:26:22', 1, '', 'Ngô Minh Anh', '', NULL, NULL, '', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `crm_import_parents` VALUES (28, 10127, 'Nguyễn Phương Dung', '', '0943691486', '', '', '', 1, '2025-01-07 22:26:22', 1, '', 'Nguyễn Minh Khánh', '', NULL, NULL, '', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `crm_import_parents` VALUES (29, 10127, 'Nguyễn Phương Dung', '', '0934213816', '', '', '', 1, '2025-01-07 22:26:22', 1, '', 'Nguyễn Khánh An', '', NULL, NULL, '', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `crm_import_parents` VALUES (30, 10127, 'Trịnh Thị Vân Anh', '', '0966189669', '', '', '', 1, '2025-01-07 22:26:22', 1, '', 'Cao Cát Linh', '', NULL, NULL, '', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `crm_import_parents` VALUES (31, 10127, 'Tăng Thị Phương Thảo', '', '0912312384', '', '', '', 3, '2025-01-07 22:26:22', 1, 'Trùng lặp dữ liệu trong file import', 'Nguyễn Minh Anh', '', NULL, NULL, '', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `crm_import_parents` VALUES (32, 10127, 'Tăng Thị Phương Thảo', '', '0923693333', '', '', '', 1, '2025-01-07 22:26:22', 1, '', 'Nguyễn Huyền Anh', '', NULL, NULL, '', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `crm_import_parents` VALUES (33, 10127, 'Tăng Thị Phương Thảo', '', '0912312384', '', '', '', 1, '2025-01-07 22:26:22', 1, '', 'Nguyễn Hoàng Anh', '', NULL, NULL, '', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `crm_import_parents` VALUES (34, 10127, 'Vũ Thu Thảo', '', '0987575449', '', '', '', 1, '2025-01-07 22:26:22', 1, '', 'Nguyễn Bảo Phương', '', NULL, NULL, '', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `crm_import_parents` VALUES (35, 10127, 'Vũ Thu Thảo', '', '0987585449', '', '', '', 1, '2025-01-07 22:26:22', 1, '', 'Nguyễn Phương Anh', '', NULL, NULL, '', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `crm_import_parents` VALUES (36, 10127, 'Vũ Thu Thảo', '', '0987595449', '', '', '', 1, '2025-01-07 22:26:22', 1, '', 'Nguyễn Phương Anh', '', NULL, NULL, '', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL);
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
) ENGINE=InnoDB AUTO_INCREMENT=10128 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- ----------------------------
-- Records of crm_imports
-- ----------------------------
BEGIN;
INSERT INTO `crm_imports` VALUES (10126, 'e35bbaef5d241a6dff8fe5da340bc76a.xlsx', 'static/upload/e35bbaef5d241a6dff8fe5da340bc76a.xlsx', 0, '2024-12-11 23:15:27', 1, NULL, NULL);
INSERT INTO `crm_imports` VALUES (10127, '635ea400958fdd8ab0b3a34e9e37282d.xlsx', 'static/upload/635ea400958fdd8ab0b3a34e9e37282d.xlsx', 0, '2025-01-07 22:26:22', 1, NULL, NULL);
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
) ENGINE=InnoDB AUTO_INCREMENT=52 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- ----------------------------
-- Records of crm_parent_logs
-- ----------------------------
BEGIN;
INSERT INTO `crm_parent_logs` VALUES (38, 19, 'Khởi tạo khách hàng thủ công', '2025-02-17 15:17:02', 1, 1);
INSERT INTO `crm_parent_logs` VALUES (39, 19, 'Thêm mới học sinh: Nguyễn Minh Khôi (ID: 18)', '2025-02-17 15:17:18', 1, 1);
INSERT INTO `crm_parent_logs` VALUES (40, 20, 'Khởi tạo khách hàng thủ công', '2025-02-17 15:53:05', 1, 1);
INSERT INTO `crm_parent_logs` VALUES (41, 20, 'Thêm mới học sinh: HS test 1 (ID: 19)', '2025-02-18 10:33:08', 1, 1);
INSERT INTO `crm_parent_logs` VALUES (42, 21, 'Khởi tạo khách hàng thủ công', '2025-02-18 10:33:47', 1, 1);
INSERT INTO `crm_parent_logs` VALUES (43, 21, 'Thêm mới học sinh: HS test 2 (ID: 20)', '2025-02-18 10:34:12', 1, 1);
INSERT INTO `crm_parent_logs` VALUES (44, 22, 'Khởi tạo khách hàng thủ công', '2025-02-18 10:34:48', 1, 1);
INSERT INTO `crm_parent_logs` VALUES (45, 22, 'Thêm mới học sinh: HS test 3 (ID: 21)', '2025-02-18 10:35:01', 1, 1);
INSERT INTO `crm_parent_logs` VALUES (46, 23, 'Khởi tạo khách hàng thủ công', '2025-02-18 10:35:33', 1, 1);
INSERT INTO `crm_parent_logs` VALUES (47, 23, 'Thêm mới học sinh: HS test 4 (ID: 22)', '2025-02-18 10:35:46', 1, 1);
INSERT INTO `crm_parent_logs` VALUES (48, 22, 'Cập nhật thông tin khách hàng: Tên từ `PH test 2` thành `PH test 3`, ', '2025-02-18 10:36:04', 1, 1);
INSERT INTO `crm_parent_logs` VALUES (49, 23, 'Thêm mới học sinh: Nguyễn Văn B (ID: 23)', '2025-02-18 16:32:19', 17, 1);
INSERT INTO `crm_parent_logs` VALUES (50, 24, 'Khởi tạo khách hàng thủ công', '2025-02-18 17:09:50', 17, 1);
INSERT INTO `crm_parent_logs` VALUES (51, 24, 'Thêm mới học sinh: Phùng Ngọc Hoa (ID: 24)', '2025-02-18 17:13:53', 17, 1);
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
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- ----------------------------
-- Records of crm_parents
-- ----------------------------
BEGIN;
INSERT INTO `crm_parents` VALUES (19, 'Nguyễn Văn Đạt', NULL, '0966948868', NULL, NULL, NULL, NULL, '2025-02-17 15:17:02', 1, 'M', NULL, NULL, NULL, 4, NULL, 0, '2025-02-17 15:17:02', 17, 9, NULL, NULL, 1, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL);
INSERT INTO `crm_parents` VALUES (20, 'PH 1', NULL, '0965487421', NULL, NULL, NULL, NULL, '2025-02-17 15:53:05', 1, 'M', NULL, NULL, NULL, 4, NULL, 0, '2025-02-17 15:53:05', 17, 9, NULL, NULL, 1, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL);
INSERT INTO `crm_parents` VALUES (21, 'PH test 2', NULL, '0965412236', NULL, NULL, NULL, NULL, '2025-02-18 10:33:47', 1, 'M', NULL, NULL, NULL, 4, NULL, 0, '2025-02-18 10:33:47', 17, 9, NULL, NULL, 1, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL);
INSERT INTO `crm_parents` VALUES (22, 'PH test 3', NULL, '0965412364', NULL, NULL, NULL, NULL, '2025-02-18 10:34:48', 1, 'M', NULL, NULL, NULL, 4, NULL, 0, '2025-02-18 10:34:48', 17, 9, '2025-02-18 10:36:04', 1, 1, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL);
INSERT INTO `crm_parents` VALUES (23, 'PH test 4', NULL, '0965412548', NULL, NULL, NULL, NULL, '2025-02-18 10:35:33', 1, 'M', NULL, NULL, NULL, 4, NULL, 0, '2025-02-18 10:35:33', 17, 9, NULL, NULL, 1, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL);
INSERT INTO `crm_parents` VALUES (24, 'Chị Bổ', NULL, '0862882290', NULL, NULL, 35, 347, '2025-02-18 17:09:50', 17, 'F', NULL, NULL, NULL, 4, NULL, 83, '2025-02-18 17:09:50', 17, 9, NULL, NULL, 1, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL);
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
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- ----------------------------
-- Records of crm_students
-- ----------------------------
BEGIN;
INSERT INTO `crm_students` VALUES (11, 13, 'Lương Bảo Hân', 'F', NULL, NULL, '2018-09-08', '2024-11-30 21:02:21', 1, 3, NULL, NULL, '2024-11-30 21:02:38', 1, 1, 4, '2024-11-01 00:00:00', '2024-11-02 00:00:00', 1, 'đã check in', 6, NULL, 6);
INSERT INTO `crm_students` VALUES (12, 13, 'Lương Bảo Long', 'M', NULL, NULL, '2021-05-27', '2024-11-30 21:19:27', 1, 3, NULL, NULL, '2024-11-30 21:19:39', 1, 1, 4, '2024-11-01 00:00:00', '2024-11-01 00:00:03', 1, 'đã checkin', 7, NULL, 6);
INSERT INTO `crm_students` VALUES (13, 14, 'trần bảo tâm', 'M', NULL, NULL, '2014-09-16', '2024-12-09 09:30:11', 13, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0);
INSERT INTO `crm_students` VALUES (14, 14, 'trần bảo nam', 'M', NULL, NULL, '2013-03-18', '2024-12-09 09:30:36', 13, 3, NULL, NULL, '2024-12-09 12:23:57', 13, 13, 6, '2024-12-10 09:00:00', '2024-12-10 10:00:00', 13, 'Đến check in muộn', 8, NULL, 8);
INSERT INTO `crm_students` VALUES (15, 15, 'Hồ Bảo Phong', 'M', NULL, NULL, '2014-04-14', '2024-12-11 09:29:29', 13, 3, NULL, NULL, '2024-12-11 09:33:45', 13, 13, 6, '2024-12-03 00:00:00', '2024-11-26 00:00:00', 13, 'FFFH', 9, NULL, 8);
INSERT INTO `crm_students` VALUES (16, 17, 'Nguyễn Văn Bách', 'M', NULL, NULL, '2024-12-01', '2024-12-21 20:43:40', 1, 3, NULL, NULL, '2024-12-21 20:43:53', 1, 5, 4, '2024-12-01 00:00:00', '2024-12-01 00:00:00', 1, 'ok', 10, NULL, 8);
INSERT INTO `crm_students` VALUES (17, 18, 'Nguyễn Minh Khôi', 'M', NULL, NULL, '2024-03-27', '2025-02-17 14:17:38', 1, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0);
INSERT INTO `crm_students` VALUES (18, 19, 'Nguyễn Minh Khôi', 'M', NULL, NULL, '2024-03-27', '2025-02-17 15:17:18', 1, 3, NULL, NULL, '2025-02-17 15:24:03', 1, 17, 9, '2025-02-01 01:00:00', '2025-02-01 00:00:00', 15, 'Đã checkin', 11, NULL, 14);
INSERT INTO `crm_students` VALUES (19, 20, 'HS test 1', 'M', NULL, NULL, '2024-12-31', '2025-02-18 10:33:08', 1, 3, NULL, NULL, '2025-02-18 10:33:20', 1, 17, 9, '2025-02-18 00:00:00', '2025-02-18 00:00:00', 15, 'ce', 15, NULL, 14);
INSERT INTO `crm_students` VALUES (20, 21, 'HS test 2', 'M', NULL, NULL, '2025-02-18', '2025-02-18 10:34:12', 1, 3, NULL, NULL, '2025-02-18 10:34:18', 1, 17, 9, '2025-01-31 00:00:00', '2025-02-18 00:00:00', 15, 'che', 14, NULL, 14);
INSERT INTO `crm_students` VALUES (21, 22, 'HS test 3', 'F', NULL, NULL, '2025-02-05', '2025-02-18 10:35:01', 1, 3, NULL, NULL, '2025-02-18 10:35:07', 1, 17, 9, '2025-02-18 00:00:00', '2025-02-18 00:00:00', 15, 'ch', 13, NULL, 14);
INSERT INTO `crm_students` VALUES (22, 23, 'HS test 4', 'F', NULL, NULL, '2025-02-18', '2025-02-18 10:35:46', 1, 3, NULL, NULL, '2025-02-18 10:35:51', 1, 17, 9, '2025-02-18 00:00:00', '2025-02-06 00:00:01', 15, 'ce', 12, NULL, 14);
INSERT INTO `crm_students` VALUES (23, 23, 'Nguyễn Văn B', 'M', NULL, NULL, '2025-02-18', '2025-02-18 16:32:19', 17, 3, NULL, NULL, '2025-02-18 16:36:12', 17, 17, 9, '2025-02-18 03:00:00', '2025-02-18 00:00:00', 15, 'Checkin', 16, NULL, 14);
INSERT INTO `crm_students` VALUES (24, 24, 'Phùng Ngọc Hoa', 'F', NULL, NULL, '2025-02-18', '2025-02-18 17:13:53', 17, 3, 'Lớp Cocoon 1', NULL, '2025-02-18 17:14:31', 17, 17, 9, '2025-02-18 15:04:00', '2025-02-18 16:00:00', 15, 'HS mua phí', 17, NULL, 14);
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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

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
  `b2b_campaign_id` int DEFAULT NULL,
  `b2b_amount` int DEFAULT NULL,
  `b2b_bonus_session` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `ec_id` (`ec_id`) USING BTREE,
  KEY `tuition_fee_id` (`tuition_fee_id`) USING BTREE,
  KEY `creator_id` (`creator_id`) USING BTREE,
  KEY `cm_id` (`cm_id`) USING BTREE,
  KEY `student_id` (`student_id`) USING BTREE,
  KEY `branch_id` (`branch_id`) USING BTREE,
  KEY `ceo_branch_id` (`ceo_branch_id`) USING BTREE,
  KEY `contract_id` (`contract_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=44145 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of log_contracts
-- ----------------------------
BEGIN;
INSERT INTO `log_contracts` VALUES (44115, 7, 1, 'C000007', 7, NULL, NULL, 4, 2, 1, NULL, NULL, NULL, 8, NULL, 8, 8, NULL, 24000000, 48, 0, 19600000, 0, 19600000, 19600000, NULL, 3, 2400000, 10, '', 0, 0, 488, 48, 0, 0, 0, 0, 0, 0, 0, NULL, NULL, '2024-12-01', NULL, NULL, NULL, NULL, 1, '2024-12-20 12:15:41', 1, NULL, NULL, NULL, NULL, 0, NULL, '2024-12-20 12:15:41', NULL, NULL, NULL, '2024-12-21', 6, 2000000, '8');
INSERT INTO `log_contracts` VALUES (44116, 7, 1, 'C000007', 7, NULL, NULL, 4, 2, 1, NULL, NULL, NULL, 8, NULL, 8, 8, 24000000, 24000000, 48, 0, 19600000, 0, 19600000, 19600000, NULL, 3, 2400000, 10, '', 0, 0, 56, 48, 0, 0, 0, 0, 0, 0, 0, NULL, NULL, '2024-12-01', NULL, NULL, NULL, NULL, 1, '2024-12-20 12:15:41', 1, '2024-12-21 19:25:01', 1, NULL, NULL, 0, NULL, '2024-12-21 19:25:01', NULL, NULL, NULL, '2024-12-21', 6, 2000000, '8');
INSERT INTO `log_contracts` VALUES (44117, 8, 1, 'C000008', 10, NULL, NULL, 4, 2, 5, NULL, NULL, NULL, 8, NULL, 8, 8, 24000000, 24000000, 48, 0, 24000000, 0, 24000000, 24000000, NULL, NULL, NULL, NULL, '', 0, 0, 48, 48, 0, 0, 0, 0, 0, 0, 0, NULL, NULL, '2024-12-01', NULL, NULL, NULL, NULL, 1, '2024-12-21 20:45:35', 1, NULL, NULL, NULL, NULL, 0, NULL, '2024-12-21 20:45:36', NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `log_contracts` VALUES (44118, 8, 1, 'C000008', 10, NULL, NULL, 4, 2, 5, NULL, NULL, NULL, 8, NULL, 8, 8, 24000000, 24000000, 48, 24000000, 24000000, 24000000, 0, 24000000, NULL, NULL, NULL, NULL, '', 0, 0, 48, 48, 0, 48, 0, 2, 0, 48, 0, NULL, NULL, '2024-12-01', NULL, NULL, NULL, NULL, 3, '2024-12-21 20:45:35', 1, '2024-12-21 20:46:44', 1, NULL, NULL, 0, NULL, '2024-12-21 20:46:45', NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `log_contracts` VALUES (44119, 7, 1, 'C000007', 7, NULL, NULL, 4, 2, 1, NULL, NULL, NULL, 8, NULL, 8, 8, 24000000, 24000000, 48, 19600000, 19600000, 19600000, 0, 19600000, NULL, 3, 2400000, 10, '', 0, 0, 56, 48, 0, 56, 0, 2, 0, 56, 0, NULL, NULL, '2024-12-01', NULL, NULL, NULL, NULL, 3, '2024-12-20 12:15:41', 1, '2025-01-07 22:53:57', 1, NULL, NULL, 0, NULL, '2025-01-07 22:53:57', NULL, NULL, NULL, '2024-12-21', 6, 2000000, '8');
INSERT INTO `log_contracts` VALUES (44120, 9, 1, 'C000009', 6, NULL, NULL, 4, 2, 1, NULL, NULL, NULL, 8, NULL, 8, 8, 24000000, 24000000, 48, 0, 24000000, 0, 24000000, 24000000, NULL, NULL, NULL, NULL, '', 0, 0, 48, 48, 0, 0, 0, 0, 0, 0, 0, NULL, NULL, '2025-01-01', NULL, NULL, NULL, NULL, 1, '2025-01-07 22:56:43', 1, NULL, NULL, NULL, NULL, 0, NULL, '2025-01-07 22:56:43', NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `log_contracts` VALUES (44121, 9, 1, 'C000009', 6, NULL, NULL, 4, 2, 1, NULL, NULL, NULL, 8, NULL, 8, 8, 24000000, 24000000, 48, 24000000, 24000000, 24000000, 0, 24000000, NULL, NULL, NULL, NULL, '', 0, 0, 48, 48, 0, 48, 0, 2, 0, 48, 0, NULL, NULL, '2025-01-01', NULL, NULL, NULL, NULL, 3, '2025-01-07 22:56:43', 1, '2025-01-07 22:57:10', 1, NULL, NULL, 0, NULL, '2025-01-07 22:57:11', NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `log_contracts` VALUES (44122, 8, 1, 'C000008', 10, NULL, NULL, 4, 2, 5, NULL, 6, 6, 8, 10, 8, 8, 24000000, 24000000, 48, 24000000, 24000000, 24000000, 0, 24000000, NULL, NULL, NULL, NULL, '', 0, 0, 48, 48, 0, 48, 0, 2, 0, 48, 0, NULL, NULL, '2024-12-01', NULL, 14, '2025-01-09', NULL, 6, '2024-12-21 20:45:35', 1, '2025-01-07 23:04:09', 1, NULL, NULL, 0, NULL, '2025-01-07 23:04:09', NULL, '2025-06-22', NULL, '2025-02-19', NULL, NULL, NULL);
INSERT INTO `log_contracts` VALUES (44123, 7, 1, 'C000007', 7, NULL, NULL, 4, 2, 1, NULL, 6, 6, 8, 10, 8, 8, 24000000, 24000000, 48, 19600000, 19600000, 19600000, 0, 19600000, NULL, 3, 2400000, 10, '', 0, 0, 56, 48, 0, 56, 0, 2, 0, 56, 0, NULL, NULL, '2024-12-01', NULL, 14, '2025-01-09', NULL, 6, '2024-12-20 12:15:41', 1, '2025-01-07 23:04:09', 1, NULL, NULL, 0, NULL, '2025-01-07 23:04:09', NULL, '2025-07-20', NULL, '2025-02-19', 6, 2000000, '8');
INSERT INTO `log_contracts` VALUES (44124, 9, 1, 'C000009', 6, NULL, NULL, 4, 2, 1, NULL, 6, 6, 8, 10, 8, 8, 24000000, 24000000, 48, 24000000, 24000000, 24000000, 0, 24000000, NULL, NULL, NULL, NULL, '', 0, 0, 48, 48, 0, 48, 0, 2, 0, 48, 0, NULL, NULL, '2025-01-01', NULL, 14, '2025-01-09', NULL, 6, '2025-01-07 22:56:43', 1, '2025-01-07 23:04:09', 1, NULL, NULL, 0, NULL, '2025-01-07 23:04:09', NULL, '2025-06-22', NULL, '2025-02-19', NULL, NULL, NULL);
INSERT INTO `log_contracts` VALUES (44125, 10, 1, 'C000010', 11, NULL, NULL, 9, 16, 17, NULL, NULL, NULL, 14, NULL, 10, 10, 1900000, 1900000, 8, 0, 1900000, 0, 1900000, 1900000, NULL, NULL, NULL, NULL, '', 0, 0, 8, 8, 0, 0, 0, 0, 0, 0, 0, NULL, NULL, '2025-02-17', NULL, NULL, NULL, NULL, 1, '2025-02-17 15:25:10', 15, NULL, NULL, NULL, NULL, 0, NULL, '2025-02-17 15:25:10', NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `log_contracts` VALUES (44126, 10, 1, 'C000010', 11, NULL, NULL, 9, 16, 17, NULL, NULL, NULL, 14, NULL, 10, 10, 1900000, 1900000, 8, 1900000, 1900000, 1900000, 0, 1900000, NULL, NULL, NULL, NULL, '', 0, 0, 8, 8, 0, 8, 0, 0, 0, 8, 0, NULL, NULL, '2025-02-17', NULL, NULL, NULL, NULL, 3, '2025-02-17 15:25:10', 15, '2025-02-17 15:25:36', 15, NULL, NULL, 0, NULL, '2025-02-17 15:25:36', NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `log_contracts` VALUES (44127, 10, 1, 'C000010', 11, NULL, NULL, 9, 16, 17, NULL, 15, 15, 14, 24, 10, 10, 1900000, 1900000, 8, 1900000, 1900000, 1900000, 0, 1900000, NULL, NULL, NULL, NULL, '', 0, 0, 8, 8, 0, 8, 0, 0, 0, 8, 0, NULL, NULL, '2025-02-17', NULL, 18, '2025-02-18', NULL, 6, '2025-02-17 15:25:10', 15, '2025-02-18 10:31:51', 15, NULL, NULL, 0, NULL, '2025-02-18 10:31:52', NULL, '2025-03-14', NULL, '2025-02-19', NULL, NULL, NULL);
INSERT INTO `log_contracts` VALUES (44128, 11, 1, 'C000011', 12, NULL, NULL, 9, 16, 17, NULL, NULL, NULL, 14, NULL, 10, 10, 1900000, 1900000, 8, 0, 1900000, 0, 1900000, 1900000, NULL, NULL, NULL, NULL, '', 0, 0, 8, 8, 0, 0, 0, 0, 0, 0, 0, NULL, NULL, '2025-02-18', NULL, NULL, NULL, NULL, 1, '2025-02-18 10:37:57', 15, NULL, NULL, NULL, NULL, 0, NULL, '2025-02-18 10:37:57', NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `log_contracts` VALUES (44129, 12, 1, 'C000012', 15, NULL, NULL, 9, 16, 17, NULL, NULL, NULL, 14, NULL, 10, 10, 1900000, 1900000, 8, 0, 1900000, 0, 1900000, 1900000, NULL, NULL, NULL, NULL, '', 0, 0, 8, 8, 0, 0, 0, 0, 0, 0, 0, NULL, NULL, '2025-02-18', NULL, NULL, NULL, NULL, 1, '2025-02-18 10:38:26', 15, NULL, NULL, NULL, NULL, 0, NULL, '2025-02-18 10:38:26', NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `log_contracts` VALUES (44130, 13, 1, 'C000013', 14, NULL, NULL, 9, 16, 17, NULL, NULL, NULL, 14, NULL, 10, 10, 1900000, 1900000, 8, 0, 1900000, 0, 1900000, 1900000, NULL, NULL, NULL, NULL, '', 0, 0, 8, 8, 0, 0, 0, 0, 0, 0, 0, NULL, NULL, '2025-02-18', NULL, NULL, NULL, NULL, 1, '2025-02-18 10:38:43', 15, NULL, NULL, NULL, NULL, 0, NULL, '2025-02-18 10:38:43', NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `log_contracts` VALUES (44131, 14, 1, 'C000014', 13, NULL, NULL, 9, 16, 17, NULL, NULL, NULL, 14, NULL, 10, 10, 1900000, 1900000, 8, 0, 1900000, 0, 1900000, 1900000, NULL, NULL, NULL, NULL, '', 0, 0, 8, 8, 0, 0, 0, 0, 0, 0, 0, NULL, NULL, '2025-02-18', NULL, NULL, NULL, NULL, 1, '2025-02-18 10:39:00', 15, NULL, NULL, NULL, NULL, 0, NULL, '2025-02-18 10:39:00', NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `log_contracts` VALUES (44132, 14, 1, 'C000014', 13, NULL, NULL, 9, 16, 17, NULL, NULL, NULL, 14, NULL, 10, 10, 1900000, 1900000, 8, 1900000, 1900000, 1900000, 0, 1900000, NULL, NULL, NULL, NULL, '', 0, 0, 8, 8, 0, 8, 0, 0, 0, 8, 0, NULL, NULL, '2025-02-18', NULL, NULL, NULL, NULL, 3, '2025-02-18 10:39:00', 15, '2025-02-18 10:39:18', 1, NULL, NULL, 0, NULL, '2025-02-18 10:39:18', NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `log_contracts` VALUES (44133, 13, 1, 'C000013', 14, NULL, NULL, 9, 16, 17, NULL, NULL, NULL, 14, NULL, 10, 10, 1900000, 1900000, 8, 1900000, 1900000, 1900000, 0, 1900000, NULL, NULL, NULL, NULL, '', 0, 0, 8, 8, 0, 8, 0, 0, 0, 8, 0, NULL, NULL, '2025-02-18', NULL, NULL, NULL, NULL, 3, '2025-02-18 10:38:43', 15, '2025-02-18 10:39:25', 1, NULL, NULL, 0, NULL, '2025-02-18 10:39:25', NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `log_contracts` VALUES (44134, 12, 1, 'C000012', 15, NULL, NULL, 9, 16, 17, NULL, NULL, NULL, 14, NULL, 10, 10, 1900000, 1900000, 8, 1900000, 1900000, 1900000, 0, 1900000, NULL, NULL, NULL, NULL, '', 0, 0, 8, 8, 0, 8, 0, 0, 0, 8, 0, NULL, NULL, '2025-02-18', NULL, NULL, NULL, NULL, 3, '2025-02-18 10:38:26', 15, '2025-02-18 10:39:35', 1, NULL, NULL, 0, NULL, '2025-02-18 10:39:35', NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `log_contracts` VALUES (44135, 11, 1, 'C000011', 12, NULL, NULL, 9, 16, 17, NULL, NULL, NULL, 14, NULL, 10, 10, 1900000, 1900000, 8, 1900000, 1900000, 1900000, 0, 1900000, NULL, NULL, NULL, NULL, '', 0, 0, 8, 8, 0, 8, 0, 0, 0, 8, 0, NULL, NULL, '2025-02-18', NULL, NULL, NULL, NULL, 3, '2025-02-18 10:37:57', 15, '2025-02-18 10:39:43', 1, NULL, NULL, 0, NULL, '2025-02-18 10:39:43', NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `log_contracts` VALUES (44136, 12, 1, 'C000012', 15, NULL, NULL, 9, 16, 17, NULL, 15, 15, 14, 24, 10, 10, 1900000, 1900000, 8, 1900000, 1900000, 1900000, 0, 1900000, NULL, NULL, NULL, NULL, '', 0, 0, 8, 8, 0, 8, 0, 0, 0, 8, 0, NULL, NULL, '2025-02-18', NULL, 18, '2025-02-18', NULL, 6, '2025-02-18 10:38:26', 15, '2025-02-18 10:40:08', 15, NULL, NULL, 0, NULL, '2025-02-18 10:40:08', NULL, '2025-03-14', '2025-02-19', '2025-02-19', NULL, NULL, NULL);
INSERT INTO `log_contracts` VALUES (44137, 13, 1, 'C000013', 14, NULL, NULL, 9, 16, 17, NULL, 15, 15, 14, 24, 10, 10, 1900000, 1900000, 8, 1900000, 1900000, 1900000, 0, 1900000, NULL, NULL, NULL, NULL, '', 0, 0, 8, 8, 0, 8, 0, 0, 0, 8, 0, NULL, NULL, '2025-02-18', NULL, 18, '2025-02-18', NULL, 6, '2025-02-18 10:38:43', 15, '2025-02-18 10:40:08', 15, NULL, NULL, 0, NULL, '2025-02-18 10:40:08', NULL, '2025-03-14', NULL, '2025-02-19', NULL, NULL, NULL);
INSERT INTO `log_contracts` VALUES (44138, 14, 1, 'C000014', 13, NULL, NULL, 9, 16, 17, NULL, 15, 15, 14, 24, 10, 10, 1900000, 1900000, 8, 1900000, 1900000, 1900000, 0, 1900000, NULL, NULL, NULL, NULL, '', 0, 0, 8, 8, 0, 8, 0, 0, 0, 8, 0, NULL, NULL, '2025-02-18', NULL, 18, '2025-02-18', NULL, 6, '2025-02-18 10:39:00', 15, '2025-02-18 10:40:08', 15, NULL, NULL, 0, NULL, '2025-02-18 10:40:08', NULL, '2025-03-14', NULL, '2025-02-19', NULL, NULL, NULL);
INSERT INTO `log_contracts` VALUES (44139, 11, 1, 'C000011', 12, NULL, NULL, 9, 16, 17, NULL, 15, 15, 14, 24, 10, 10, 1900000, 1900000, 8, 1900000, 1900000, 1900000, 0, 1900000, NULL, NULL, NULL, NULL, '', 0, 0, 8, 8, 0, 8, 0, 0, 0, 8, 0, NULL, NULL, '2025-02-18', NULL, 18, '2025-02-18', NULL, 6, '2025-02-18 10:37:57', 15, '2025-02-18 10:40:08', 15, NULL, NULL, 0, NULL, '2025-02-18 10:40:08', NULL, '2025-03-14', NULL, '2025-02-19', NULL, NULL, NULL);
INSERT INTO `log_contracts` VALUES (44140, 15, 1, 'C000015', 15, NULL, NULL, 9, 16, 17, NULL, 15, 15, 14, NULL, 10, 10, 1900000, 1900000, 8, 0, 1900000, 0, 1900000, 1900000, NULL, NULL, NULL, NULL, '', 0, 0, 8, 8, 0, 0, 0, 0, 0, 0, 0, NULL, NULL, '2025-02-18', NULL, NULL, NULL, NULL, 1, '2025-02-18 16:57:23', 15, NULL, NULL, NULL, NULL, 1, NULL, '2025-02-18 16:57:23', NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `log_contracts` VALUES (44141, 16, 1, 'C000016', 17, NULL, NULL, 9, 16, 17, NULL, NULL, NULL, 14, NULL, 12, 12, 10830000, 10830000, 48, 0, 10830000, 0, 10830000, 10830000, NULL, NULL, NULL, NULL, '', 0, 0, 48, 48, 0, 0, 0, 0, 0, 0, 0, NULL, NULL, '2025-06-01', NULL, NULL, NULL, NULL, 1, '2025-02-18 17:20:15', 15, NULL, NULL, NULL, NULL, 0, NULL, '2025-02-18 17:20:15', NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `log_contracts` VALUES (44142, 16, 1, 'C000016', 17, NULL, NULL, 9, 16, 17, NULL, NULL, NULL, 14, NULL, 12, 12, 10830000, 10830000, 48, 1000000, 10830000, 1000000, 9830000, 10830000, NULL, NULL, NULL, NULL, '', 0, 0, 48, 48, 0, 0, 0, 0, 0, 0, 0, NULL, NULL, '2025-06-01', NULL, NULL, NULL, NULL, 2, '2025-02-18 17:20:15', 15, '2025-02-18 17:21:14', 15, NULL, NULL, 0, NULL, '2025-02-18 17:21:14', NULL, NULL, NULL, '2025-02-19', NULL, NULL, NULL);
INSERT INTO `log_contracts` VALUES (44143, 15, 1, 'C000015', 15, NULL, NULL, 9, 16, 17, NULL, 15, 15, 14, NULL, 10, 10, 1900000, 1900000, 8, 1000000, 1900000, 1000000, 900000, NULL, NULL, NULL, NULL, NULL, '', 0, 0, 8, 8, 0, 0, 0, 0, 0, 0, 0, NULL, NULL, '2025-02-18', NULL, NULL, NULL, NULL, 2, '2025-02-18 16:57:23', 15, '2025-02-19 00:09:29', 1, NULL, NULL, 1, NULL, '2025-02-19 00:09:30', NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `log_contracts` VALUES (44144, 15, 1, 'C000015', 15, NULL, NULL, 9, 16, 17, NULL, 15, 15, 14, NULL, 10, 10, 1900000, 1900000, 8, 1900000, 1900000, 1900000, 0, NULL, NULL, NULL, NULL, NULL, '', 0, 0, 8, 8, 0, 8, 0, 0, 0, 8, 0, NULL, NULL, '2025-02-18', NULL, NULL, NULL, NULL, 3, '2025-02-18 16:57:23', 15, '2025-02-19 00:09:58', 1, NULL, NULL, 1, NULL, '2025-02-19 00:09:59', NULL, NULL, NULL, '2025-02-19', NULL, NULL, NULL);
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
) ENGINE=InnoDB AUTO_INCREMENT=1436 DEFAULT CHARSET=latin1;

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
INSERT INTO `log_jobs` VALUES (1403, 'ProcessDataScheduleHasStudent', '2024-12-11', '2024-12-12 00:05:02');
INSERT INTO `log_jobs` VALUES (1404, 'AddScheduleHasStudent', '2024-12-12', '2024-12-12 01:00:02');
INSERT INTO `log_jobs` VALUES (1405, 'ProcessData', NULL, '2024-12-12 02:00:02');
INSERT INTO `log_jobs` VALUES (1406, 'ProcessDataScheduleHasStudent', '2024-12-12', '2024-12-13 00:05:01');
INSERT INTO `log_jobs` VALUES (1407, 'AddScheduleHasStudent', '2024-12-13', '2024-12-13 01:00:02');
INSERT INTO `log_jobs` VALUES (1408, 'ProcessData', NULL, '2024-12-13 02:00:02');
INSERT INTO `log_jobs` VALUES (1409, 'ProcessDataScheduleHasStudent', '2024-12-13', '2024-12-14 00:05:02');
INSERT INTO `log_jobs` VALUES (1410, 'AddScheduleHasStudent', '2024-12-14', '2024-12-14 01:00:02');
INSERT INTO `log_jobs` VALUES (1411, 'ProcessData', NULL, '2024-12-14 02:00:02');
INSERT INTO `log_jobs` VALUES (1412, 'ProcessDataScheduleHasStudent', '2024-12-14', '2024-12-15 00:05:02');
INSERT INTO `log_jobs` VALUES (1413, 'AddScheduleHasStudent', '2024-12-15', '2024-12-15 01:00:02');
INSERT INTO `log_jobs` VALUES (1414, 'ProcessData', NULL, '2024-12-15 02:00:02');
INSERT INTO `log_jobs` VALUES (1415, 'ProcessDataScheduleHasStudent', '2024-12-15', '2024-12-16 00:05:02');
INSERT INTO `log_jobs` VALUES (1416, 'AddScheduleHasStudent', '2024-12-16', '2024-12-16 01:00:02');
INSERT INTO `log_jobs` VALUES (1417, 'ProcessData', NULL, '2024-12-16 02:00:02');
INSERT INTO `log_jobs` VALUES (1418, 'ProcessDataScheduleHasStudent', '2024-12-16', '2024-12-17 00:05:02');
INSERT INTO `log_jobs` VALUES (1419, 'AddScheduleHasStudent', '2024-12-17', '2024-12-17 01:00:02');
INSERT INTO `log_jobs` VALUES (1420, 'ProcessData', NULL, '2024-12-17 02:00:02');
INSERT INTO `log_jobs` VALUES (1421, 'ProcessDataScheduleHasStudent', '2024-12-17', '2024-12-18 00:05:02');
INSERT INTO `log_jobs` VALUES (1422, 'AddScheduleHasStudent', '2024-12-18', '2024-12-18 01:00:01');
INSERT INTO `log_jobs` VALUES (1423, 'ProcessData', NULL, '2024-12-18 02:00:02');
INSERT INTO `log_jobs` VALUES (1424, 'ProcessDataScheduleHasStudent', '2024-12-18', '2024-12-19 00:05:02');
INSERT INTO `log_jobs` VALUES (1425, 'AddScheduleHasStudent', '2024-12-19', '2024-12-19 01:00:01');
INSERT INTO `log_jobs` VALUES (1426, 'ProcessData', NULL, '2024-12-19 02:00:02');
INSERT INTO `log_jobs` VALUES (1427, 'ProcessDataScheduleHasStudent', '2024-12-19', '2024-12-20 00:05:02');
INSERT INTO `log_jobs` VALUES (1428, 'AddScheduleHasStudent', '2024-12-20', '2024-12-20 01:00:02');
INSERT INTO `log_jobs` VALUES (1429, 'ProcessData', NULL, '2024-12-20 02:00:02');
INSERT INTO `log_jobs` VALUES (1430, 'ProcessDataScheduleHasStudent', '2024-12-20', '2024-12-21 00:05:02');
INSERT INTO `log_jobs` VALUES (1431, 'AddScheduleHasStudent', '2024-12-21', '2024-12-21 01:00:02');
INSERT INTO `log_jobs` VALUES (1432, 'ProcessData', NULL, '2024-12-21 02:00:02');
INSERT INTO `log_jobs` VALUES (1433, 'ProcessDataScheduleHasStudent', '2025-02-18', '2025-02-19 00:05:02');
INSERT INTO `log_jobs` VALUES (1434, 'AddScheduleHasStudent', '2025-02-19', '2025-02-19 01:00:03');
INSERT INTO `log_jobs` VALUES (1435, 'ProcessData', NULL, '2025-02-19 02:00:03');
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
  `branch_id` int DEFAULT NULL,
  `ec_id` int DEFAULT NULL,
  `cm_id` int DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `contract_id` (`contract_id`) USING BTREE,
  KEY `creator_id` (`creator_id`) USING BTREE,
  KEY `student_id` (`student_id`) USING BTREE,
  KEY `branch_id` (`branch_id`) USING BTREE,
  KEY `cm_id` (`cm_id`) USING BTREE,
  KEY `ec_id` (`ec_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=37107 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of payments
-- ----------------------------
BEGIN;
INSERT INTO `payments` VALUES (37096, 8, NULL, 1, 0, 24000000, 24000000, 24000000, 0, '2024-12-21', '2024-12-21 20:46:44', 1, NULL, 0, NULL, 10, NULL, 4, 5, NULL);
INSERT INTO `payments` VALUES (37097, 7, NULL, 1, 0, 19600000, 19600000, 19600000, 0, '2025-01-07', '2025-01-07 22:53:57', 1, NULL, 0, NULL, 7, NULL, 4, 8, NULL);
INSERT INTO `payments` VALUES (37098, 9, NULL, 1, 0, 24000000, 24000000, 24000000, 0, '2025-01-01', '2025-01-07 22:57:10', 1, NULL, 0, NULL, 6, NULL, 4, 5, NULL);
INSERT INTO `payments` VALUES (37099, 10, NULL, 1, 0, 1900000, 1900000, 1900000, 0, '2025-02-17', '2025-02-17 15:25:36', 15, NULL, 0, NULL, 11, NULL, 9, 17, NULL);
INSERT INTO `payments` VALUES (37100, 14, NULL, 1, 0, 1900000, 1900000, 1900000, 0, '2025-02-18', '2025-02-18 10:39:18', 1, NULL, 0, NULL, 13, NULL, 9, 17, NULL);
INSERT INTO `payments` VALUES (37101, 13, NULL, 1, 0, 1900000, 1900000, 1900000, 0, '2025-02-18', '2025-02-18 10:39:25', 1, NULL, 0, NULL, 14, NULL, 9, 17, NULL);
INSERT INTO `payments` VALUES (37102, 12, NULL, 1, 0, 1900000, 1900000, 1900000, 0, '2025-02-18', '2025-02-18 10:39:35', 1, NULL, 0, NULL, 15, NULL, 9, 17, NULL);
INSERT INTO `payments` VALUES (37103, 11, NULL, 1, 0, 1900000, 1900000, 1900000, 0, '2025-02-18', '2025-02-18 10:39:43', 1, NULL, 0, NULL, 12, NULL, 9, 17, NULL);
INSERT INTO `payments` VALUES (37104, 16, NULL, 1, 0, 10830000, 1000000, 1000000, 9830000, '2025-02-18', '2025-02-18 17:21:14', 15, NULL, 0, NULL, 17, 'Đặt cọc lần 1', 9, 17, NULL);
INSERT INTO `payments` VALUES (37105, 15, NULL, 1, 0, 1900000, NULL, 1000000, 900000, '2025-02-19', '2025-02-19 00:09:29', 1, NULL, 0, NULL, 15, 'ghi chú', 9, 17, 15);
INSERT INTO `payments` VALUES (37106, 15, NULL, 1, 0, 1900000, NULL, 1900000, 0, '2025-02-19', '2025-02-19 00:09:58', 1, NULL, 0, NULL, 15, 'ghi chú', 9, 17, 15);
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
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- ----------------------------
-- Records of permission_groups
-- ----------------------------
BEGIN;
INSERT INTO `permission_groups` VALUES (1, 'CRM', 'CRM', 1, 3);
INSERT INTO `permission_groups` VALUES (2, 'LMS', 'LMS', 1, 2);
INSERT INTO `permission_groups` VALUES (3, 'Config', 'Cấu hình hệ thống', 1, 1);
INSERT INTO `permission_groups` VALUES (4, 'Orther', 'Khác', 1, 7);
INSERT INTO `permission_groups` VALUES (5, 'Marketing', 'Marketing', 1, 4);
INSERT INTO `permission_groups` VALUES (6, 'Dashboard', 'Dashboard', 1, 6);
INSERT INTO `permission_groups` VALUES (7, 'Reports', 'Báo cáo', 1, 5);
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
INSERT INTO `permission_has_role` VALUES (56, 3);
INSERT INTO `permission_has_role` VALUES (56, 4);
INSERT INTO `permission_has_role` VALUES (56, 5);
INSERT INTO `permission_has_role` VALUES (56, 6);
INSERT INTO `permission_has_role` VALUES (56, 7);
INSERT INTO `permission_has_role` VALUES (56, 8);
INSERT INTO `permission_has_role` VALUES (56, 9);
INSERT INTO `permission_has_role` VALUES (56, 10);
INSERT INTO `permission_has_role` VALUES (56, 11);
INSERT INTO `permission_has_role` VALUES (56, 12);
INSERT INTO `permission_has_role` VALUES (56, 15);
INSERT INTO `permission_has_role` VALUES (56, 17);
INSERT INTO `permission_has_role` VALUES (56, 18);
INSERT INTO `permission_has_role` VALUES (56, 19);
INSERT INTO `permission_has_role` VALUES (56, 26);
INSERT INTO `permission_has_role` VALUES (56, 27);
INSERT INTO `permission_has_role` VALUES (56, 35);
INSERT INTO `permission_has_role` VALUES (56, 67);
INSERT INTO `permission_has_role` VALUES (56, 68);
INSERT INTO `permission_has_role` VALUES (56, 69);
INSERT INTO `permission_has_role` VALUES (56, 70);
INSERT INTO `permission_has_role` VALUES (56, 71);
INSERT INTO `permission_has_role` VALUES (56, 72);
INSERT INTO `permission_has_role` VALUES (56, 77);
INSERT INTO `permission_has_role` VALUES (56, 78);
INSERT INTO `permission_has_role` VALUES (56, 79);
INSERT INTO `permission_has_role` VALUES (68, 1);
INSERT INTO `permission_has_role` VALUES (68, 2);
INSERT INTO `permission_has_role` VALUES (68, 3);
INSERT INTO `permission_has_role` VALUES (68, 4);
INSERT INTO `permission_has_role` VALUES (68, 5);
INSERT INTO `permission_has_role` VALUES (68, 24);
INSERT INTO `permission_has_role` VALUES (68, 28);
INSERT INTO `permission_has_role` VALUES (69, 1);
INSERT INTO `permission_has_role` VALUES (69, 2);
INSERT INTO `permission_has_role` VALUES (69, 3);
INSERT INTO `permission_has_role` VALUES (69, 4);
INSERT INTO `permission_has_role` VALUES (69, 24);
INSERT INTO `permission_has_role` VALUES (69, 28);
INSERT INTO `permission_has_role` VALUES (69, 41);
INSERT INTO `permission_has_role` VALUES (69, 42);
INSERT INTO `permission_has_role` VALUES (69, 43);
INSERT INTO `permission_has_role` VALUES (69, 44);
INSERT INTO `permission_has_role` VALUES (69, 45);
INSERT INTO `permission_has_role` VALUES (69, 67);
INSERT INTO `permission_has_role` VALUES (69, 68);
INSERT INTO `permission_has_role` VALUES (69, 69);
INSERT INTO `permission_has_role` VALUES (69, 70);
INSERT INTO `permission_has_role` VALUES (69, 71);
INSERT INTO `permission_has_role` VALUES (69, 72);
INSERT INTO `permission_has_role` VALUES (69, 73);
INSERT INTO `permission_has_role` VALUES (69, 74);
INSERT INTO `permission_has_role` VALUES (69, 75);
INSERT INTO `permission_has_role` VALUES (69, 76);
INSERT INTO `permission_has_role` VALUES (69, 77);
INSERT INTO `permission_has_role` VALUES (69, 78);
INSERT INTO `permission_has_role` VALUES (69, 79);
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
INSERT INTO `permission_has_role` VALUES (686868, 41);
INSERT INTO `permission_has_role` VALUES (686868, 42);
INSERT INTO `permission_has_role` VALUES (686868, 43);
INSERT INTO `permission_has_role` VALUES (686868, 44);
INSERT INTO `permission_has_role` VALUES (686868, 45);
INSERT INTO `permission_has_role` VALUES (686868, 67);
INSERT INTO `permission_has_role` VALUES (686868, 68);
INSERT INTO `permission_has_role` VALUES (686868, 69);
INSERT INTO `permission_has_role` VALUES (686868, 70);
INSERT INTO `permission_has_role` VALUES (686868, 71);
INSERT INTO `permission_has_role` VALUES (686868, 72);
INSERT INTO `permission_has_role` VALUES (686868, 73);
INSERT INTO `permission_has_role` VALUES (686868, 74);
INSERT INTO `permission_has_role` VALUES (686868, 75);
INSERT INTO `permission_has_role` VALUES (686868, 76);
INSERT INTO `permission_has_role` VALUES (686868, 77);
INSERT INTO `permission_has_role` VALUES (686868, 78);
INSERT INTO `permission_has_role` VALUES (686868, 79);
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
INSERT INTO `permission_has_role` VALUES (999999, 33);
INSERT INTO `permission_has_role` VALUES (999999, 34);
INSERT INTO `permission_has_role` VALUES (999999, 35);
INSERT INTO `permission_has_role` VALUES (999999, 36);
INSERT INTO `permission_has_role` VALUES (999999, 37);
INSERT INTO `permission_has_role` VALUES (999999, 38);
INSERT INTO `permission_has_role` VALUES (999999, 39);
INSERT INTO `permission_has_role` VALUES (999999, 40);
INSERT INTO `permission_has_role` VALUES (999999, 41);
INSERT INTO `permission_has_role` VALUES (999999, 42);
INSERT INTO `permission_has_role` VALUES (999999, 43);
INSERT INTO `permission_has_role` VALUES (999999, 44);
INSERT INTO `permission_has_role` VALUES (999999, 45);
INSERT INTO `permission_has_role` VALUES (999999, 46);
INSERT INTO `permission_has_role` VALUES (999999, 47);
INSERT INTO `permission_has_role` VALUES (999999, 48);
INSERT INTO `permission_has_role` VALUES (999999, 49);
INSERT INTO `permission_has_role` VALUES (999999, 50);
INSERT INTO `permission_has_role` VALUES (999999, 51);
INSERT INTO `permission_has_role` VALUES (999999, 52);
INSERT INTO `permission_has_role` VALUES (999999, 53);
INSERT INTO `permission_has_role` VALUES (999999, 54);
INSERT INTO `permission_has_role` VALUES (999999, 55);
INSERT INTO `permission_has_role` VALUES (999999, 56);
INSERT INTO `permission_has_role` VALUES (999999, 57);
INSERT INTO `permission_has_role` VALUES (999999, 58);
INSERT INTO `permission_has_role` VALUES (999999, 59);
INSERT INTO `permission_has_role` VALUES (999999, 60);
INSERT INTO `permission_has_role` VALUES (999999, 61);
INSERT INTO `permission_has_role` VALUES (999999, 62);
INSERT INTO `permission_has_role` VALUES (999999, 63);
INSERT INTO `permission_has_role` VALUES (999999, 64);
INSERT INTO `permission_has_role` VALUES (999999, 65);
INSERT INTO `permission_has_role` VALUES (999999, 66);
INSERT INTO `permission_has_role` VALUES (999999, 67);
INSERT INTO `permission_has_role` VALUES (999999, 68);
INSERT INTO `permission_has_role` VALUES (999999, 69);
INSERT INTO `permission_has_role` VALUES (999999, 70);
INSERT INTO `permission_has_role` VALUES (999999, 71);
INSERT INTO `permission_has_role` VALUES (999999, 72);
INSERT INTO `permission_has_role` VALUES (999999, 73);
INSERT INTO `permission_has_role` VALUES (999999, 74);
INSERT INTO `permission_has_role` VALUES (999999, 75);
INSERT INTO `permission_has_role` VALUES (999999, 76);
INSERT INTO `permission_has_role` VALUES (999999, 77);
INSERT INTO `permission_has_role` VALUES (999999, 78);
INSERT INTO `permission_has_role` VALUES (999999, 79);
INSERT INTO `permission_has_role` VALUES (999999, 80);
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
) ENGINE=InnoDB AUTO_INCREMENT=81 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- ----------------------------
-- Records of permissions
-- ----------------------------
BEGIN;
INSERT INTO `permissions` VALUES (1, 'crm_parent', 'Khách hàng', 1, 1, 1, NULL);
INSERT INTO `permissions` VALUES (2, 'crm_imports', 'Import', 1, 2, 1, NULL);
INSERT INTO `permissions` VALUES (3, 'lms_students', 'DS học sinh', 2, 1, 1, NULL);
INSERT INTO `permissions` VALUES (4, 'lms_checkin', 'DS checkin', 2, 2, 1, NULL);
INSERT INTO `permissions` VALUES (5, 'lms_contracts', 'Nhập học', 2, 3, 1, NULL);
INSERT INTO `permissions` VALUES (6, 'lms_enrolments', 'Xếp lớp', 2, 4, 1, NULL);
INSERT INTO `permissions` VALUES (7, 'lms_reserves', 'Bảo lưu', 2, 5, 1, NULL);
INSERT INTO `permissions` VALUES (8, 'lms_class_transfers', 'Chuyển lớp', 2, 6, 1, NULL);
INSERT INTO `permissions` VALUES (9, 'lms_branch_transfers', 'Chuyển trung tâm', 2, 7, 1, NULL);
INSERT INTO `permissions` VALUES (10, 'lms_tuition_transfers', 'Chuyển phí', 2, 8, 1, NULL);
INSERT INTO `permissions` VALUES (11, 'lms_attendances', 'Điểm danh', 2, 9, 1, NULL);
INSERT INTO `permissions` VALUES (12, 'lms_assessments', 'Nhận xét & đánh giá', 2, 10, 1, NULL);
INSERT INTO `permissions` VALUES (13, 'settings_branches', 'Trung tâm', 3, 1, 1, NULL);
INSERT INTO `permissions` VALUES (14, 'settings_products', 'Khóa học', 3, 2, 1, NULL);
INSERT INTO `permissions` VALUES (15, 'settings_programs', 'Chương trình học', 3, 3, 1, NULL);
INSERT INTO `permissions` VALUES (16, 'settings_subjects', 'Môn học', 3, 4, 1, NULL);
INSERT INTO `permissions` VALUES (17, 'settings_classes', 'Lớp học', 3, 5, 1, NULL);
INSERT INTO `permissions` VALUES (18, 'settings_rooms', 'Phòng học', 3, 6, 1, NULL);
INSERT INTO `permissions` VALUES (19, 'settings_shifts', 'Ca học', 3, 7, 1, NULL);
INSERT INTO `permissions` VALUES (20, 'settings_holidays', 'Ngày nghỉ lễ', 3, 8, 1, NULL);
INSERT INTO `permissions` VALUES (21, 'settings_tuition_fees', 'Gói phí', 3, 9, 1, NULL);
INSERT INTO `permissions` VALUES (22, 'settings_users', 'Người dùng', 3, 10, 1, NULL);
INSERT INTO `permissions` VALUES (23, 'settings_roles', 'Chức vụ, phân quyền', 3, 11, 1, NULL);
INSERT INTO `permissions` VALUES (24, 'dashboard', 'Dashboard', 4, 1, 1, NULL);
INSERT INTO `permissions` VALUES (26, 'lms_charges', 'Đã thu phí', 2, 11, 1, NULL);
INSERT INTO `permissions` VALUES (27, 'lms_waitcharges', 'Chờ thu phí', 2, 12, 1, NULL);
INSERT INTO `permissions` VALUES (28, 'lms_reports', 'Báo cáo', 2, 13, 1, NULL);
INSERT INTO `permissions` VALUES (29, 'settings_discount_codes', 'Mã chiết khấu', 3, 12, 1, NULL);
INSERT INTO `permissions` VALUES (30, 'settings_coupons', 'Voucher', 3, 13, 1, NULL);
INSERT INTO `permissions` VALUES (31, 'canViewAllSale', 'Xem tất cả CSKH', 4, 2, 1, NULL);
INSERT INTO `permissions` VALUES (32, 'canViewAllParents', 'Xem tất cả khách hàng', 4, 3, 1, NULL);
INSERT INTO `permissions` VALUES (33, 'canViewAllImport', 'Xem tất cả import', 4, 4, 1, NULL);
INSERT INTO `permissions` VALUES (34, 'lms_teachers', 'Giáo viên & trợ giảng', 2, 14, 1, NULL);
INSERT INTO `permissions` VALUES (35, 'lms_exchange', 'Quy đổi', 2, 8, 1, NULL);
INSERT INTO `permissions` VALUES (36, 'marketing_coupons', 'Voucher', 5, 2, 1, NULL);
INSERT INTO `permissions` VALUES (37, 'marketing_campaigns', 'Chiến dịch', 5, 1, 1, NULL);
INSERT INTO `permissions` VALUES (38, 'marketing_b2b_sources', 'Nguồn B2B', 5, 3, 1, NULL);
INSERT INTO `permissions` VALUES (39, 'marketing_b2b_campaigns', 'Chính sách B2B', 5, 3, 1, NULL);
INSERT INTO `permissions` VALUES (40, 'marketing_c2c_campaigns', 'Chính sách C2C', 5, 3, 1, NULL);
INSERT INTO `permissions` VALUES (41, 'lms_reports_business', 'Kinh doanh', 7, 1, 1, NULL);
INSERT INTO `permissions` VALUES (42, 'lms_reports_finance', 'Tài chính kế toán', 7, 2, 1, NULL);
INSERT INTO `permissions` VALUES (43, 'lms_reports_operate', 'Vận hành', 7, 3, 1, NULL);
INSERT INTO `permissions` VALUES (44, 'lms_reports_academic', 'Học thuật', 7, 4, 1, NULL);
INSERT INTO `permissions` VALUES (45, 'lms_reports_teacher', 'Giáo viên', 7, 5, 1, NULL);
INSERT INTO `permissions` VALUES (46, 'lms_reports_academic_01', 'BÁO CÁO CHI TIẾT GIỜ DẠY CỦA TRỢ GIẢNG', NULL, 1, 1, 44);
INSERT INTO `permissions` VALUES (47, 'lms_reports_academic_02', 'DANH SÁCH LỚP TEST', NULL, 1, 1, 44);
INSERT INTO `permissions` VALUES (48, 'lms_reports_business_01', 'KẾT QUẢ KINH DOANH NGÀY', NULL, 1, 1, 41);
INSERT INTO `permissions` VALUES (49, 'lms_reports_operate_01', 'BÁO CÁO CHI TIẾT HỌC SINH FULL FEE ACTIVE', NULL, 1, 1, 43);
INSERT INTO `permissions` VALUES (50, 'lms_reports_operate_02', 'BÁO CÁO CHI TIẾT HỌC SINH TÁI PHÍ', NULL, 1, 1, 43);
INSERT INTO `permissions` VALUES (51, 'lms_reports_operate_03', 'BÁO CÁO TỔNG HỢP HỌC SINH TÁI PHÍ', NULL, 1, 1, 43);
INSERT INTO `permissions` VALUES (52, 'lms_reports_operate_04', 'BÁO CÁO THEO AF HỌC SINH TÁI PHÍ', NULL, 1, 1, 43);
INSERT INTO `permissions` VALUES (53, 'lms_reports_operate_05', 'BÁO CÁO HỌC SINH PENDING', NULL, 1, 1, 43);
INSERT INTO `permissions` VALUES (54, 'lms_reports_operate_06', 'BÁO CÁO HỌC SINH BẢO LƯU', NULL, 1, 1, 43);
INSERT INTO `permissions` VALUES (55, 'lms_reports_operate_07', 'BÁO CÁO LỚP HỌC', NULL, 1, 1, 43);
INSERT INTO `permissions` VALUES (56, 'lms_reports_operate_08', 'CHỈ SỐ VẬN HÀNH ACS', NULL, 1, 1, 43);
INSERT INTO `permissions` VALUES (57, 'lms_reports_operate_09', 'BÁO CÁO TỶ LỆ ACS CỦA TỪNG AF', NULL, 1, 1, 43);
INSERT INTO `permissions` VALUES (58, 'lms_reports_operate_10', 'BÁO CÁO TỶ LỆ ACS CỦA TỪNG TRUNG TÂM', NULL, 1, 1, 43);
INSERT INTO `permissions` VALUES (59, 'lms_reports_operate_11', 'BÁO CÁO HỌC VIÊN TRIAL', NULL, 1, 1, 43);
INSERT INTO `permissions` VALUES (60, 'lms_reports_operate_12', 'BÁO CÁO HỌC VIÊN ĐẶT CỌC', NULL, 1, 1, 43);
INSERT INTO `permissions` VALUES (61, 'lms_reports_finance_01', 'BÁO CÁO CHI TIẾT DOANH SỐ', NULL, 1, 1, 42);
INSERT INTO `permissions` VALUES (62, 'lms_reports_finance_02', 'BÁO CÁO DOANH SỐ THEO TRUNG TÂM', NULL, 1, 1, 42);
INSERT INTO `permissions` VALUES (63, 'lms_reports_finance_03', 'BÁO CÁO DOANH THU THỰC HIỆN', NULL, 1, 1, 42);
INSERT INTO `permissions` VALUES (64, 'lms_reports_finance_04', 'BÁO CÁO DOANH SỐ THEO NHÂN VIÊN', NULL, 1, 1, 42);
INSERT INTO `permissions` VALUES (65, 'lms_reports_finance_05', 'BÁO CÁO CHI TIẾT ĐƠN HÀNG', NULL, 1, 1, 42);
INSERT INTO `permissions` VALUES (66, 'lms_reports_teacher_01', 'BÁO CÁO CHI TIẾT GIỜ DẠY CỦA GIÁO VIÊN', NULL, 1, 1, 45);
INSERT INTO `permissions` VALUES (67, 'dashboard_01', 'Học viên đang học', 6, 1, 1, NULL);
INSERT INTO `permissions` VALUES (68, 'dashboard_02', 'Tổng số lớp', 6, 1, 1, NULL);
INSERT INTO `permissions` VALUES (69, 'dashboard_03', 'Tổng số giáo viên', 6, 1, 1, NULL);
INSERT INTO `permissions` VALUES (70, 'dashboard_04', 'Tổng số phòng học', 6, 1, 1, NULL);
INSERT INTO `permissions` VALUES (71, 'dashboard_05', 'Tình hình học viên', 6, 1, 1, NULL);
INSERT INTO `permissions` VALUES (72, 'dashboard_06', 'Đăng ký khóa học', 6, 1, 1, NULL);
INSERT INTO `permissions` VALUES (73, 'dashboard_07', 'Doanh thu theo trung tâm', 6, 1, 1, NULL);
INSERT INTO `permissions` VALUES (74, 'dashboard_08', 'Doanh thu theo EC', 6, 1, 1, NULL);
INSERT INTO `permissions` VALUES (75, 'dashboard_09', 'Doanh số ngày', 6, 1, 1, NULL);
INSERT INTO `permissions` VALUES (76, 'dashboard_10', 'Doanh số tháng', 6, 1, 1, NULL);
INSERT INTO `permissions` VALUES (77, 'dashboard_11', 'Học sinh Renew theo trung tâm', 6, 1, 1, NULL);
INSERT INTO `permissions` VALUES (78, 'dashboard_12', 'Học sinh Renew theo AF', 6, 1, 1, NULL);
INSERT INTO `permissions` VALUES (79, 'dashboard_13', 'Học sinh Renew theo tháng', 6, 1, 1, NULL);
INSERT INTO `permissions` VALUES (80, 'lms_waitcharge_approve', 'Duyệt đóng phí', 2, 12, 1, NULL);
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
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of products
-- ----------------------------
BEGIN;
INSERT INTO `products` VALUES (14, NULL, 'APAX', 'Khóa học APAX', NULL, '2025-02-17 15:10:56', NULL, 1, NULL, 1, NULL, NULL, NULL);
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
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of programs
-- ----------------------------
BEGIN;
INSERT INTO `programs` VALUES (24, NULL, 'APAX', 'APAX', NULL, '2025-02-17 15:11:25', 1, NULL, NULL, 1, 0, 14);
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
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb3;

-- ----------------------------
-- Records of report_full_fee_active
-- ----------------------------
BEGIN;
INSERT INTO `report_full_fee_active` VALUES (3, 11, 10, 18, 14, 10, 10, 15, '2025-02', 0, 9, NULL, NULL, 0, 0, '2025-02-19 02:00:03', 99999, '2025-03-14', '2025-02-18', 1, 0, 8);
INSERT INTO `report_full_fee_active` VALUES (4, 12, 11, 18, 14, 10, 10, 15, '2025-02', 0, 9, NULL, NULL, 0, 0, '2025-02-19 02:00:03', 99999, '2025-03-14', '2025-02-18', 1, 0, 8);
INSERT INTO `report_full_fee_active` VALUES (5, 15, 12, 18, 14, 10, 10, 15, '2025-02', 0, 9, NULL, NULL, 0, 0, '2025-02-19 02:00:03', 99999, '2025-03-14', '2025-02-18', 1, 0, 8);
INSERT INTO `report_full_fee_active` VALUES (6, 14, 13, 18, 14, 10, 10, 15, '2025-02', 0, 9, NULL, NULL, 0, 0, '2025-02-19 02:00:03', 99999, '2025-03-14', '2025-02-18', 1, 0, 8);
INSERT INTO `report_full_fee_active` VALUES (7, 13, 14, 18, 14, 10, 10, 15, '2025-02', 0, 9, NULL, NULL, 0, 0, '2025-02-19 02:00:03', 99999, '2025-03-14', '2025-02-18', 1, 0, 8);
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
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb3;

-- ----------------------------
-- Records of report_renews
-- ----------------------------
BEGIN;
INSERT INTO `report_renews` VALUES (3, 11, 10, 9, 14, 0, 18, 10, 0, 17, 15, 15, 0, 15, 16, 0, 2, '2025-03', '2025-03-14', NULL, NULL, '2025-02-19 02:00:03', 0, 0, 0);
INSERT INTO `report_renews` VALUES (4, 12, 11, 9, 14, 0, 18, 10, 0, 17, 15, 15, 0, 15, 16, 0, 2, '2025-03', '2025-03-14', NULL, NULL, '2025-02-19 02:00:03', 0, 0, 0);
INSERT INTO `report_renews` VALUES (5, 13, 14, 9, 14, 0, 18, 10, 0, 17, 15, 15, 0, 15, 16, 0, 2, '2025-03', '2025-03-14', NULL, NULL, '2025-02-19 02:00:03', 0, 0, 0);
INSERT INTO `report_renews` VALUES (6, 14, 13, 9, 14, 0, 18, 10, 0, 17, 15, 15, 0, 15, 16, 0, 2, '2025-03', '2025-03-14', NULL, NULL, '2025-02-19 02:00:03', 0, 0, 0);
INSERT INTO `report_renews` VALUES (7, 15, 12, 9, 14, 0, 18, 10, 10, 17, 15, 15, 0, 15, 16, 1900000, 1, '2025-03', '2025-03-14', NULL, NULL, '2025-02-19 02:00:03', 0, 0, 0);
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
INSERT INTO `role_has_user` VALUES (14, 54);
INSERT INTO `role_has_user` VALUES (15, 56);
INSERT INTO `role_has_user` VALUES (16, 686868);
INSERT INTO `role_has_user` VALUES (17, 69);
INSERT INTO `role_has_user` VALUES (18, 68);
INSERT INTO `role_has_user` VALUES (19, 37);
INSERT INTO `role_has_user` VALUES (20, 36);
INSERT INTO `role_has_user` VALUES (21, 36);
INSERT INTO `role_has_user` VALUES (22, 36);
INSERT INTO `role_has_user` VALUES (23, 36);
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
INSERT INTO `roles` VALUES (54, '54', 'ta', 'Trợ giảng', 'api', NULL, NULL, NULL, NULL);
INSERT INTO `roles` VALUES (55, '55', 'cm', 'Nhân viên CSKH', 'api', '2020-08-27 02:04:43', '2025-02-17 15:07:57', NULL, 1);
INSERT INTO `roles` VALUES (56, '56', 'cm_leader', 'Trưởng nhóm CSKH', 'api', '2024-04-06 15:17:25', '2025-02-17 15:07:45', 1, 1);
INSERT INTO `roles` VALUES (68, '68', 'ec', 'Nhân viên kinh doanh', 'api', '2020-08-27 02:04:43', '2025-02-17 15:07:37', NULL, 1);
INSERT INTO `roles` VALUES (69, '69', 'ec_leader', 'Trưởng nhóm Kinh doanh', 'api', '2023-07-27 06:44:05', '2025-02-17 15:07:27', NULL, 1);
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
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of rooms
-- ----------------------------
BEGIN;
INSERT INTO `rooms` VALUES (10, 'P1', 'Phòng Harvard', 9, '2025-02-17 15:45:05', 15, NULL, NULL, 1, NULL, NULL);
INSERT INTO `rooms` VALUES (11, 'P2', 'Phòng 1: Cornell', 9, '2025-02-17 15:45:50', 15, NULL, NULL, 1, NULL, NULL);
INSERT INTO `rooms` VALUES (12, 'P3', 'Phòng 2: Columbia', 9, '2025-02-17 15:46:02', 15, '2025-02-17 15:46:20', 15, 1, NULL, NULL);
INSERT INTO `rooms` VALUES (13, 'P4', 'Phòng 3: Dartmouth', 9, '2025-02-17 15:46:11', 15, '2025-02-17 15:46:25', 15, 1, NULL, NULL);
INSERT INTO `rooms` VALUES (14, 'P5', 'Phòng 4: Princeton', 9, '2025-02-17 15:46:40', 15, NULL, NULL, 1, NULL, NULL);
INSERT INTO `rooms` VALUES (15, 'P6', 'Phòng 5: Yale', 9, '2025-02-17 15:47:48', 15, NULL, NULL, 1, NULL, NULL);
INSERT INTO `rooms` VALUES (16, 'P7', 'Phòng 6: Brown', 9, '2025-02-17 15:47:59', 15, NULL, NULL, 1, NULL, NULL);
INSERT INTO `rooms` VALUES (17, 'P8', 'Phòng I-garten', 9, '2025-02-17 15:48:10', 15, NULL, NULL, 1, NULL, NULL);
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
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- ----------------------------
-- Records of schedule_has_student
-- ----------------------------
BEGIN;
INSERT INTO `schedule_has_student` VALUES (33, 11, 9, 18, 10, 14, 24, 1, 3, '2025-02-18', '2025-02-18 10:31:51', '2025-02-18 10:32:11', 15, NULL, 1, 1);
INSERT INTO `schedule_has_student` VALUES (34, 15, 9, 18, 12, 14, 24, 1, 3, '2025-02-18', '2025-02-18 10:40:08', NULL, NULL, NULL, 1, 0);
INSERT INTO `schedule_has_student` VALUES (35, 14, 9, 18, 13, 14, 24, 1, 3, '2025-02-18', '2025-02-18 10:40:08', NULL, NULL, NULL, 1, 0);
INSERT INTO `schedule_has_student` VALUES (36, 13, 9, 18, 14, 14, 24, 1, 3, '2025-02-18', '2025-02-18 10:40:08', NULL, NULL, NULL, 1, 0);
INSERT INTO `schedule_has_student` VALUES (37, 12, 9, 18, 11, 14, 24, 1, 3, '2025-02-18', '2025-02-18 10:40:08', NULL, NULL, NULL, 1, 0);
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
) ENGINE=InnoDB AUTO_INCREMENT=3383 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of schedules
-- ----------------------------
BEGIN;
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
INSERT INTO `schedules` VALUES (1431, '2024-12-05', 1, 14, 1, '2025-01-07 23:03:40', NULL, NULL, NULL, 1, 1, 10, 6, 4);
INSERT INTO `schedules` VALUES (1432, '2024-12-08', 1, 14, 1, '2025-01-07 23:03:40', NULL, NULL, NULL, 2, 2, 10, 6, 4);
INSERT INTO `schedules` VALUES (1433, '2024-12-12', 1, 14, 1, '2025-01-07 23:03:40', NULL, NULL, NULL, 3, 3, 10, 6, 4);
INSERT INTO `schedules` VALUES (1434, '2024-12-15', 1, 14, 1, '2025-01-07 23:03:40', NULL, NULL, NULL, 4, 4, 10, 6, 4);
INSERT INTO `schedules` VALUES (1435, '2024-12-19', 1, 14, 1, '2025-01-07 23:03:40', NULL, NULL, NULL, 5, 5, 10, 6, 4);
INSERT INTO `schedules` VALUES (1436, '2024-12-22', 1, 14, 1, '2025-01-07 23:03:40', NULL, NULL, NULL, 6, 6, 10, 6, 4);
INSERT INTO `schedules` VALUES (1437, '2024-12-26', 1, 14, 1, '2025-01-07 23:03:40', NULL, NULL, NULL, 7, 7, 10, 6, 4);
INSERT INTO `schedules` VALUES (1438, '2024-12-29', 1, 14, 1, '2025-01-07 23:03:40', NULL, NULL, NULL, 8, 8, 10, 6, 4);
INSERT INTO `schedules` VALUES (1439, '2025-01-02', 1, 14, 1, '2025-01-07 23:03:40', NULL, NULL, NULL, 9, 9, 10, 6, 4);
INSERT INTO `schedules` VALUES (1440, '2025-01-05', 1, 14, 1, '2025-01-07 23:03:40', NULL, NULL, NULL, 10, 10, 10, 6, 4);
INSERT INTO `schedules` VALUES (1441, '2025-01-09', 1, 14, 1, '2025-01-07 23:03:40', NULL, NULL, NULL, 11, 11, 10, 6, 4);
INSERT INTO `schedules` VALUES (1442, '2025-01-12', 1, 14, 1, '2025-01-07 23:03:40', NULL, NULL, NULL, 12, 12, 10, 6, 4);
INSERT INTO `schedules` VALUES (1443, '2025-01-16', 1, 14, 1, '2025-01-07 23:03:40', NULL, NULL, NULL, 13, 13, 10, 6, 4);
INSERT INTO `schedules` VALUES (1444, '2025-01-19', 1, 14, 1, '2025-01-07 23:03:40', NULL, NULL, NULL, 14, 14, 10, 6, 4);
INSERT INTO `schedules` VALUES (1445, '2025-01-23', 1, 14, 1, '2025-01-07 23:03:40', NULL, NULL, NULL, 15, 15, 10, 6, 4);
INSERT INTO `schedules` VALUES (1446, '2025-01-26', 1, 14, 1, '2025-01-07 23:03:40', NULL, NULL, NULL, 16, 16, 10, 6, 4);
INSERT INTO `schedules` VALUES (1447, '2025-01-30', 1, 14, 1, '2025-01-07 23:03:40', NULL, NULL, NULL, 17, 17, 10, 6, 4);
INSERT INTO `schedules` VALUES (1448, '2025-02-02', 1, 14, 1, '2025-01-07 23:03:40', NULL, NULL, NULL, 18, 18, 10, 6, 4);
INSERT INTO `schedules` VALUES (1449, '2025-02-06', 1, 14, 1, '2025-01-07 23:03:40', NULL, NULL, NULL, 19, 19, 10, 6, 4);
INSERT INTO `schedules` VALUES (1450, '2025-02-09', 1, 14, 1, '2025-01-07 23:03:40', NULL, NULL, NULL, 20, 20, 10, 6, 4);
INSERT INTO `schedules` VALUES (1451, '2025-02-13', 1, 14, 1, '2025-01-07 23:03:40', NULL, NULL, NULL, 21, 21, 10, 6, 4);
INSERT INTO `schedules` VALUES (1452, '2025-02-16', 1, 14, 1, '2025-01-07 23:03:40', NULL, NULL, NULL, 22, 22, 10, 6, 4);
INSERT INTO `schedules` VALUES (1453, '2025-02-20', 1, 14, 1, '2025-01-07 23:03:40', NULL, NULL, NULL, 23, 23, 10, 6, 4);
INSERT INTO `schedules` VALUES (1454, '2025-02-23', 1, 14, 1, '2025-01-07 23:03:40', NULL, NULL, NULL, 24, 24, 10, 6, 4);
INSERT INTO `schedules` VALUES (1455, '2025-02-27', 1, 14, 1, '2025-01-07 23:03:40', NULL, NULL, NULL, 25, 25, 10, 6, 4);
INSERT INTO `schedules` VALUES (1456, '2025-03-02', 1, 14, 1, '2025-01-07 23:03:40', NULL, NULL, NULL, 26, 26, 10, 6, 4);
INSERT INTO `schedules` VALUES (1457, '2025-03-06', 1, 14, 1, '2025-01-07 23:03:40', NULL, NULL, NULL, 27, 27, 10, 6, 4);
INSERT INTO `schedules` VALUES (1458, '2025-03-09', 1, 14, 1, '2025-01-07 23:03:40', NULL, NULL, NULL, 28, 28, 10, 6, 4);
INSERT INTO `schedules` VALUES (1459, '2025-03-13', 1, 14, 1, '2025-01-07 23:03:40', NULL, NULL, NULL, 29, 29, 10, 6, 4);
INSERT INTO `schedules` VALUES (1460, '2025-03-16', 1, 14, 1, '2025-01-07 23:03:40', NULL, NULL, NULL, 30, 30, 10, 6, 4);
INSERT INTO `schedules` VALUES (1461, '2025-03-20', 1, 14, 1, '2025-01-07 23:03:40', NULL, NULL, NULL, 31, 31, 10, 6, 4);
INSERT INTO `schedules` VALUES (1462, '2025-03-23', 1, 14, 1, '2025-01-07 23:03:40', NULL, NULL, NULL, 32, 32, 10, 6, 4);
INSERT INTO `schedules` VALUES (1463, '2025-02-11', 1, 17, 1, '2025-02-17 14:44:10', NULL, NULL, NULL, 1, 1, 3, 4, 4);
INSERT INTO `schedules` VALUES (1464, '2025-02-13', 1, 17, 1, '2025-02-17 14:44:10', NULL, NULL, NULL, 2, 2, 3, 4, 4);
INSERT INTO `schedules` VALUES (1465, '2025-02-18', 1, 17, 1, '2025-02-17 14:44:10', NULL, NULL, NULL, 3, 3, 3, 4, 4);
INSERT INTO `schedules` VALUES (1466, '2025-02-20', 1, 17, 1, '2025-02-17 14:44:10', NULL, NULL, NULL, 4, 4, 3, 4, 4);
INSERT INTO `schedules` VALUES (1467, '2025-02-25', 1, 17, 1, '2025-02-17 14:44:10', NULL, NULL, NULL, 5, 5, 3, 4, 4);
INSERT INTO `schedules` VALUES (1468, '2025-02-27', 1, 17, 1, '2025-02-17 14:44:10', NULL, NULL, NULL, 6, 6, 3, 4, 4);
INSERT INTO `schedules` VALUES (1469, '2025-03-04', 1, 17, 1, '2025-02-17 14:44:10', NULL, NULL, NULL, 7, 7, 3, 4, 4);
INSERT INTO `schedules` VALUES (1470, '2025-03-06', 1, 17, 1, '2025-02-17 14:44:10', NULL, NULL, NULL, 8, 8, 3, 4, 4);
INSERT INTO `schedules` VALUES (1471, '2025-03-11', 1, 17, 1, '2025-02-17 14:44:10', NULL, NULL, NULL, 9, 9, 3, 4, 4);
INSERT INTO `schedules` VALUES (1472, '2025-03-13', 1, 17, 1, '2025-02-17 14:44:10', NULL, NULL, NULL, 10, 10, 3, 4, 4);
INSERT INTO `schedules` VALUES (1473, '2025-03-18', 1, 17, 1, '2025-02-17 14:44:10', NULL, NULL, NULL, 11, 11, 3, 4, 4);
INSERT INTO `schedules` VALUES (1474, '2025-03-20', 1, 17, 1, '2025-02-17 14:44:10', NULL, NULL, NULL, 12, 12, 3, 4, 4);
INSERT INTO `schedules` VALUES (1475, '2025-03-25', 1, 17, 1, '2025-02-17 14:44:10', NULL, NULL, NULL, 13, 13, 3, 4, 4);
INSERT INTO `schedules` VALUES (1476, '2025-03-27', 1, 17, 1, '2025-02-17 14:44:10', NULL, NULL, NULL, 14, 14, 3, 4, 4);
INSERT INTO `schedules` VALUES (1477, '2025-04-01', 1, 17, 1, '2025-02-17 14:44:10', NULL, NULL, NULL, 15, 15, 3, 4, 4);
INSERT INTO `schedules` VALUES (1478, '2025-04-03', 1, 17, 1, '2025-02-17 14:44:10', NULL, NULL, NULL, 16, 16, 3, 4, 4);
INSERT INTO `schedules` VALUES (1479, '2025-04-08', 1, 17, 1, '2025-02-17 14:44:11', NULL, NULL, NULL, 17, 17, 3, 4, 4);
INSERT INTO `schedules` VALUES (1480, '2025-04-10', 1, 17, 1, '2025-02-17 14:44:11', NULL, NULL, NULL, 18, 18, 3, 4, 4);
INSERT INTO `schedules` VALUES (1481, '2025-04-15', 1, 17, 1, '2025-02-17 14:44:11', NULL, NULL, NULL, 19, 19, 3, 4, 4);
INSERT INTO `schedules` VALUES (1482, '2025-04-17', 1, 17, 1, '2025-02-17 14:44:11', NULL, NULL, NULL, 20, 20, 3, 4, 4);
INSERT INTO `schedules` VALUES (1483, '2025-04-22', 1, 17, 1, '2025-02-17 14:44:11', NULL, NULL, NULL, 21, 21, 3, 4, 4);
INSERT INTO `schedules` VALUES (1484, '2025-04-24', 1, 17, 1, '2025-02-17 14:44:11', NULL, NULL, NULL, 22, 22, 3, 4, 4);
INSERT INTO `schedules` VALUES (1485, '2025-04-29', 1, 17, 1, '2025-02-17 14:44:11', NULL, NULL, NULL, 23, 23, 3, 4, 4);
INSERT INTO `schedules` VALUES (1486, '2025-05-01', 1, 17, 1, '2025-02-17 14:44:11', NULL, NULL, NULL, 24, 24, 3, 4, 4);
INSERT INTO `schedules` VALUES (1487, '2025-05-06', 1, 17, 1, '2025-02-17 14:44:11', NULL, NULL, NULL, 25, 25, 3, 4, 4);
INSERT INTO `schedules` VALUES (1488, '2025-05-08', 1, 17, 1, '2025-02-17 14:44:11', NULL, NULL, NULL, 26, 26, 3, 4, 4);
INSERT INTO `schedules` VALUES (1489, '2025-05-13', 1, 17, 1, '2025-02-17 14:44:11', NULL, NULL, NULL, 27, 27, 3, 4, 4);
INSERT INTO `schedules` VALUES (1490, '2025-05-15', 1, 17, 1, '2025-02-17 14:44:11', NULL, NULL, NULL, 28, 28, 3, 4, 4);
INSERT INTO `schedules` VALUES (1491, '2025-05-20', 1, 17, 1, '2025-02-17 14:44:11', NULL, NULL, NULL, 29, 29, 3, 4, 4);
INSERT INTO `schedules` VALUES (1492, '2025-05-22', 1, 17, 1, '2025-02-17 14:44:11', NULL, NULL, NULL, 30, 30, 3, 4, 4);
INSERT INTO `schedules` VALUES (1493, '2025-05-27', 1, 17, 1, '2025-02-17 14:44:11', NULL, NULL, NULL, 31, 31, 3, 4, 4);
INSERT INTO `schedules` VALUES (1494, '2025-05-29', 1, 17, 1, '2025-02-17 14:44:11', NULL, NULL, NULL, 32, 32, 3, 4, 4);
INSERT INTO `schedules` VALUES (1495, '2025-06-03', 1, 17, 1, '2025-02-17 14:44:11', NULL, NULL, NULL, 33, 33, 3, 4, 4);
INSERT INTO `schedules` VALUES (1496, '2025-06-05', 1, 17, 1, '2025-02-17 14:44:11', NULL, NULL, NULL, 34, 34, 3, 4, 4);
INSERT INTO `schedules` VALUES (1497, '2025-06-10', 1, 17, 1, '2025-02-17 14:44:11', NULL, NULL, NULL, 35, 35, 3, 4, 4);
INSERT INTO `schedules` VALUES (1498, '2025-06-12', 1, 17, 1, '2025-02-17 14:44:11', NULL, NULL, NULL, 36, 36, 3, 4, 4);
INSERT INTO `schedules` VALUES (1499, '2025-06-17', 1, 17, 1, '2025-02-17 14:44:11', NULL, NULL, NULL, 37, 37, 3, 4, 4);
INSERT INTO `schedules` VALUES (1500, '2025-06-19', 1, 17, 1, '2025-02-17 14:44:11', NULL, NULL, NULL, 38, 38, 3, 4, 4);
INSERT INTO `schedules` VALUES (1501, '2025-06-24', 1, 17, 1, '2025-02-17 14:44:11', NULL, NULL, NULL, 39, 39, 3, 4, 4);
INSERT INTO `schedules` VALUES (1502, '2025-06-26', 1, 17, 1, '2025-02-17 14:44:11', NULL, NULL, NULL, 40, 40, 3, 4, 4);
INSERT INTO `schedules` VALUES (1503, '2025-07-01', 1, 17, 1, '2025-02-17 14:44:11', NULL, NULL, NULL, 41, 41, 3, 4, 4);
INSERT INTO `schedules` VALUES (1504, '2025-07-03', 1, 17, 1, '2025-02-17 14:44:11', NULL, NULL, NULL, 42, 42, 3, 4, 4);
INSERT INTO `schedules` VALUES (1505, '2025-07-08', 1, 17, 1, '2025-02-17 14:44:11', NULL, NULL, NULL, 43, 43, 3, 4, 4);
INSERT INTO `schedules` VALUES (1506, '2025-07-10', 1, 17, 1, '2025-02-17 14:44:11', NULL, NULL, NULL, 44, 44, 3, 4, 4);
INSERT INTO `schedules` VALUES (1507, '2025-07-15', 1, 17, 1, '2025-02-17 14:44:11', NULL, NULL, NULL, 45, 45, 3, 4, 4);
INSERT INTO `schedules` VALUES (1508, '2025-07-17', 1, 17, 1, '2025-02-17 14:44:11', NULL, NULL, NULL, 46, 46, 3, 4, 4);
INSERT INTO `schedules` VALUES (1509, '2025-07-22', 1, 17, 1, '2025-02-17 14:44:11', NULL, NULL, NULL, 47, 47, 3, 4, 4);
INSERT INTO `schedules` VALUES (1510, '2025-07-24', 1, 17, 1, '2025-02-17 14:44:11', NULL, NULL, NULL, 48, 48, 3, 4, 4);
INSERT INTO `schedules` VALUES (1511, '2025-07-29', 1, 17, 1, '2025-02-17 14:44:11', NULL, NULL, NULL, 49, 49, 3, 4, 4);
INSERT INTO `schedules` VALUES (1512, '2025-07-31', 1, 17, 1, '2025-02-17 14:44:11', NULL, NULL, NULL, 50, 50, 3, 4, 4);
INSERT INTO `schedules` VALUES (1513, '2025-08-05', 1, 17, 1, '2025-02-17 14:44:11', NULL, NULL, NULL, 51, 51, 3, 4, 4);
INSERT INTO `schedules` VALUES (1514, '2025-08-07', 1, 17, 1, '2025-02-17 14:44:11', NULL, NULL, NULL, 52, 52, 3, 4, 4);
INSERT INTO `schedules` VALUES (1515, '2025-08-12', 1, 17, 1, '2025-02-17 14:44:11', NULL, NULL, NULL, 53, 53, 3, 4, 4);
INSERT INTO `schedules` VALUES (1516, '2025-08-14', 1, 17, 1, '2025-02-17 14:44:11', NULL, NULL, NULL, 54, 54, 3, 4, 4);
INSERT INTO `schedules` VALUES (1517, '2025-08-19', 1, 17, 1, '2025-02-17 14:44:11', NULL, NULL, NULL, 55, 55, 3, 4, 4);
INSERT INTO `schedules` VALUES (1518, '2025-08-21', 1, 17, 1, '2025-02-17 14:44:11', NULL, NULL, NULL, 56, 56, 3, 4, 4);
INSERT INTO `schedules` VALUES (1519, '2025-08-26', 1, 17, 1, '2025-02-17 14:44:11', NULL, NULL, NULL, 57, 57, 3, 4, 4);
INSERT INTO `schedules` VALUES (1520, '2025-08-28', 1, 17, 1, '2025-02-17 14:44:11', NULL, NULL, NULL, 58, 58, 3, 4, 4);
INSERT INTO `schedules` VALUES (1521, '2025-09-02', 1, 17, 1, '2025-02-17 14:44:11', NULL, NULL, NULL, 59, 59, 3, 4, 4);
INSERT INTO `schedules` VALUES (1522, '2025-09-04', 1, 17, 1, '2025-02-17 14:44:11', NULL, NULL, NULL, 60, 60, 3, 4, 4);
INSERT INTO `schedules` VALUES (1523, '2025-09-09', 1, 17, 1, '2025-02-17 14:44:11', NULL, NULL, NULL, 61, 61, 3, 4, 4);
INSERT INTO `schedules` VALUES (1524, '2025-09-11', 1, 17, 1, '2025-02-17 14:44:11', NULL, NULL, NULL, 62, 62, 3, 4, 4);
INSERT INTO `schedules` VALUES (1525, '2025-09-16', 1, 17, 1, '2025-02-17 14:44:11', NULL, NULL, NULL, 63, 63, 3, 4, 4);
INSERT INTO `schedules` VALUES (1526, '2025-09-18', 1, 17, 1, '2025-02-17 14:44:11', NULL, NULL, NULL, 64, 64, 3, 4, 4);
INSERT INTO `schedules` VALUES (1527, '2025-09-23', 1, 17, 1, '2025-02-17 14:44:11', NULL, NULL, NULL, 65, 65, 3, 4, 4);
INSERT INTO `schedules` VALUES (1528, '2025-09-25', 1, 17, 1, '2025-02-17 14:44:11', NULL, NULL, NULL, 66, 66, 3, 4, 4);
INSERT INTO `schedules` VALUES (1529, '2025-09-30', 1, 17, 1, '2025-02-17 14:44:11', NULL, NULL, NULL, 67, 67, 3, 4, 4);
INSERT INTO `schedules` VALUES (1530, '2025-10-02', 1, 17, 1, '2025-02-17 14:44:11', NULL, NULL, NULL, 68, 68, 3, 4, 4);
INSERT INTO `schedules` VALUES (1531, '2025-10-07', 1, 17, 1, '2025-02-17 14:44:11', NULL, NULL, NULL, 69, 69, 3, 4, 4);
INSERT INTO `schedules` VALUES (1532, '2025-10-09', 1, 17, 1, '2025-02-17 14:44:11', NULL, NULL, NULL, 70, 70, 3, 4, 4);
INSERT INTO `schedules` VALUES (1533, '2025-10-14', 1, 17, 1, '2025-02-17 14:44:11', NULL, NULL, NULL, 71, 71, 3, 4, 4);
INSERT INTO `schedules` VALUES (1534, '2025-10-16', 1, 17, 1, '2025-02-17 14:44:11', NULL, NULL, NULL, 72, 72, 3, 4, 4);
INSERT INTO `schedules` VALUES (1535, '2025-10-21', 1, 17, 1, '2025-02-17 14:44:11', NULL, NULL, NULL, 73, 73, 3, 4, 4);
INSERT INTO `schedules` VALUES (1536, '2025-10-23', 1, 17, 1, '2025-02-17 14:44:11', NULL, NULL, NULL, 74, 74, 3, 4, 4);
INSERT INTO `schedules` VALUES (1537, '2025-10-28', 1, 17, 1, '2025-02-17 14:44:11', NULL, NULL, NULL, 75, 75, 3, 4, 4);
INSERT INTO `schedules` VALUES (1538, '2025-10-30', 1, 17, 1, '2025-02-17 14:44:11', NULL, NULL, NULL, 76, 76, 3, 4, 4);
INSERT INTO `schedules` VALUES (1539, '2025-11-04', 1, 17, 1, '2025-02-17 14:44:11', NULL, NULL, NULL, 77, 77, 3, 4, 4);
INSERT INTO `schedules` VALUES (1540, '2025-11-06', 1, 17, 1, '2025-02-17 14:44:11', NULL, NULL, NULL, 78, 78, 3, 4, 4);
INSERT INTO `schedules` VALUES (1541, '2025-11-11', 1, 17, 1, '2025-02-17 14:44:11', NULL, NULL, NULL, 79, 79, 3, 4, 4);
INSERT INTO `schedules` VALUES (1542, '2025-11-13', 1, 17, 1, '2025-02-17 14:44:11', NULL, NULL, NULL, 80, 80, 3, 4, 4);
INSERT INTO `schedules` VALUES (1543, '2025-11-18', 1, 17, 1, '2025-02-17 14:44:11', NULL, NULL, NULL, 81, 81, 3, 4, 4);
INSERT INTO `schedules` VALUES (1544, '2025-11-20', 1, 17, 1, '2025-02-17 14:44:11', NULL, NULL, NULL, 82, 82, 3, 4, 4);
INSERT INTO `schedules` VALUES (1545, '2025-11-25', 1, 17, 1, '2025-02-17 14:44:11', NULL, NULL, NULL, 83, 83, 3, 4, 4);
INSERT INTO `schedules` VALUES (1546, '2025-11-27', 1, 17, 1, '2025-02-17 14:44:11', NULL, NULL, NULL, 84, 84, 3, 4, 4);
INSERT INTO `schedules` VALUES (1547, '2025-12-02', 1, 17, 1, '2025-02-17 14:44:11', NULL, NULL, NULL, 85, 85, 3, 4, 4);
INSERT INTO `schedules` VALUES (1548, '2025-12-04', 1, 17, 1, '2025-02-17 14:44:11', NULL, NULL, NULL, 86, 86, 3, 4, 4);
INSERT INTO `schedules` VALUES (1549, '2025-12-09', 1, 17, 1, '2025-02-17 14:44:11', NULL, NULL, NULL, 87, 87, 3, 4, 4);
INSERT INTO `schedules` VALUES (1550, '2025-12-11', 1, 17, 1, '2025-02-17 14:44:11', NULL, NULL, NULL, 88, 88, 3, 4, 4);
INSERT INTO `schedules` VALUES (1551, '2025-12-16', 1, 17, 1, '2025-02-17 14:44:11', NULL, NULL, NULL, 89, 89, 3, 4, 4);
INSERT INTO `schedules` VALUES (1552, '2025-12-18', 1, 17, 1, '2025-02-17 14:44:11', NULL, NULL, NULL, 90, 90, 3, 4, 4);
INSERT INTO `schedules` VALUES (1553, '2025-12-23', 1, 17, 1, '2025-02-17 14:44:11', NULL, NULL, NULL, 91, 91, 3, 4, 4);
INSERT INTO `schedules` VALUES (1554, '2025-12-25', 1, 17, 1, '2025-02-17 14:44:11', NULL, NULL, NULL, 92, 92, 3, 4, 4);
INSERT INTO `schedules` VALUES (1555, '2025-12-30', 1, 17, 1, '2025-02-17 14:44:11', NULL, NULL, NULL, 93, 93, 3, 4, 4);
INSERT INTO `schedules` VALUES (1556, '2026-01-01', 1, 17, 1, '2025-02-17 14:44:11', NULL, NULL, NULL, 94, 94, 3, 4, 4);
INSERT INTO `schedules` VALUES (1557, '2026-01-06', 1, 17, 1, '2025-02-17 14:44:11', NULL, NULL, NULL, 95, 95, 3, 4, 4);
INSERT INTO `schedules` VALUES (1558, '2026-01-08', 1, 17, 1, '2025-02-17 14:44:11', NULL, NULL, NULL, 96, 96, 3, 4, 4);
INSERT INTO `schedules` VALUES (1751, '2025-02-11', 1, 19, 1, '2025-02-17 16:14:44', NULL, NULL, NULL, 1, 1, 21, 15, 9);
INSERT INTO `schedules` VALUES (1752, '2025-02-14', 1, 19, 1, '2025-02-17 16:14:44', NULL, NULL, NULL, 2, 2, 21, 15, 9);
INSERT INTO `schedules` VALUES (1753, '2025-02-18', 1, 19, 1, '2025-02-17 16:14:44', NULL, NULL, NULL, 3, 3, 21, 15, 9);
INSERT INTO `schedules` VALUES (1754, '2025-02-21', 1, 19, 1, '2025-02-17 16:14:44', NULL, NULL, NULL, 4, 4, 21, 15, 9);
INSERT INTO `schedules` VALUES (1755, '2025-02-25', 1, 19, 1, '2025-02-17 16:14:44', NULL, NULL, NULL, 5, 5, 21, 15, 9);
INSERT INTO `schedules` VALUES (1756, '2025-02-28', 1, 19, 1, '2025-02-17 16:14:44', NULL, NULL, NULL, 6, 6, 21, 15, 9);
INSERT INTO `schedules` VALUES (1757, '2025-03-04', 1, 19, 1, '2025-02-17 16:14:44', NULL, NULL, NULL, 7, 7, 21, 15, 9);
INSERT INTO `schedules` VALUES (1758, '2025-03-07', 1, 19, 1, '2025-02-17 16:14:44', NULL, NULL, NULL, 8, 8, 21, 15, 9);
INSERT INTO `schedules` VALUES (1759, '2025-03-11', 1, 19, 1, '2025-02-17 16:14:44', NULL, NULL, NULL, 9, 9, 21, 15, 9);
INSERT INTO `schedules` VALUES (1760, '2025-03-14', 1, 19, 1, '2025-02-17 16:14:44', NULL, NULL, NULL, 10, 10, 21, 15, 9);
INSERT INTO `schedules` VALUES (1761, '2025-03-18', 1, 19, 1, '2025-02-17 16:14:44', NULL, NULL, NULL, 11, 11, 21, 15, 9);
INSERT INTO `schedules` VALUES (1762, '2025-03-21', 1, 19, 1, '2025-02-17 16:14:44', NULL, NULL, NULL, 12, 12, 21, 15, 9);
INSERT INTO `schedules` VALUES (1763, '2025-03-25', 1, 19, 1, '2025-02-17 16:14:44', NULL, NULL, NULL, 13, 13, 21, 15, 9);
INSERT INTO `schedules` VALUES (1764, '2025-03-28', 1, 19, 1, '2025-02-17 16:14:44', NULL, NULL, NULL, 14, 14, 21, 15, 9);
INSERT INTO `schedules` VALUES (1765, '2025-04-01', 1, 19, 1, '2025-02-17 16:14:44', NULL, NULL, NULL, 15, 15, 21, 15, 9);
INSERT INTO `schedules` VALUES (1766, '2025-04-04', 1, 19, 1, '2025-02-17 16:14:44', NULL, NULL, NULL, 16, 16, 21, 15, 9);
INSERT INTO `schedules` VALUES (1767, '2025-04-08', 1, 19, 1, '2025-02-17 16:14:44', NULL, NULL, NULL, 17, 17, 21, 15, 9);
INSERT INTO `schedules` VALUES (1768, '2025-04-11', 1, 19, 1, '2025-02-17 16:14:44', NULL, NULL, NULL, 18, 18, 21, 15, 9);
INSERT INTO `schedules` VALUES (1769, '2025-04-15', 1, 19, 1, '2025-02-17 16:14:44', NULL, NULL, NULL, 19, 19, 21, 15, 9);
INSERT INTO `schedules` VALUES (1770, '2025-04-18', 1, 19, 1, '2025-02-17 16:14:44', NULL, NULL, NULL, 20, 20, 21, 15, 9);
INSERT INTO `schedules` VALUES (1771, '2025-04-22', 1, 19, 1, '2025-02-17 16:14:44', NULL, NULL, NULL, 21, 21, 21, 15, 9);
INSERT INTO `schedules` VALUES (1772, '2025-04-25', 1, 19, 1, '2025-02-17 16:14:44', NULL, NULL, NULL, 22, 22, 21, 15, 9);
INSERT INTO `schedules` VALUES (1773, '2025-04-29', 1, 19, 1, '2025-02-17 16:14:44', NULL, NULL, NULL, 23, 23, 21, 15, 9);
INSERT INTO `schedules` VALUES (1774, '2025-05-02', 1, 19, 1, '2025-02-17 16:14:44', NULL, NULL, NULL, 24, 24, 21, 15, 9);
INSERT INTO `schedules` VALUES (1775, '2025-05-06', 1, 19, 1, '2025-02-17 16:14:44', NULL, NULL, NULL, 25, 25, 21, 15, 9);
INSERT INTO `schedules` VALUES (1776, '2025-05-09', 1, 19, 1, '2025-02-17 16:14:44', NULL, NULL, NULL, 26, 26, 21, 15, 9);
INSERT INTO `schedules` VALUES (1777, '2025-05-13', 1, 19, 1, '2025-02-17 16:14:44', NULL, NULL, NULL, 27, 27, 21, 15, 9);
INSERT INTO `schedules` VALUES (1778, '2025-05-16', 1, 19, 1, '2025-02-17 16:14:44', NULL, NULL, NULL, 28, 28, 21, 15, 9);
INSERT INTO `schedules` VALUES (1779, '2025-05-20', 1, 19, 1, '2025-02-17 16:14:44', NULL, NULL, NULL, 29, 29, 21, 15, 9);
INSERT INTO `schedules` VALUES (1780, '2025-05-23', 1, 19, 1, '2025-02-17 16:14:44', NULL, NULL, NULL, 30, 30, 21, 15, 9);
INSERT INTO `schedules` VALUES (1781, '2025-05-27', 1, 19, 1, '2025-02-17 16:14:44', NULL, NULL, NULL, 31, 31, 21, 15, 9);
INSERT INTO `schedules` VALUES (1782, '2025-05-30', 1, 19, 1, '2025-02-17 16:14:44', NULL, NULL, NULL, 32, 32, 21, 15, 9);
INSERT INTO `schedules` VALUES (1783, '2025-06-03', 1, 19, 1, '2025-02-17 16:14:44', NULL, NULL, NULL, 33, 33, 21, 15, 9);
INSERT INTO `schedules` VALUES (1784, '2025-06-06', 1, 19, 1, '2025-02-17 16:14:44', NULL, NULL, NULL, 34, 34, 21, 15, 9);
INSERT INTO `schedules` VALUES (1785, '2025-06-10', 1, 19, 1, '2025-02-17 16:14:44', NULL, NULL, NULL, 35, 35, 21, 15, 9);
INSERT INTO `schedules` VALUES (1786, '2025-06-13', 1, 19, 1, '2025-02-17 16:14:44', NULL, NULL, NULL, 36, 36, 21, 15, 9);
INSERT INTO `schedules` VALUES (1787, '2025-06-17', 1, 19, 1, '2025-02-17 16:14:44', NULL, NULL, NULL, 37, 37, 21, 15, 9);
INSERT INTO `schedules` VALUES (1788, '2025-06-20', 1, 19, 1, '2025-02-17 16:14:44', NULL, NULL, NULL, 38, 38, 21, 15, 9);
INSERT INTO `schedules` VALUES (1789, '2025-06-24', 1, 19, 1, '2025-02-17 16:14:44', NULL, NULL, NULL, 39, 39, 21, 15, 9);
INSERT INTO `schedules` VALUES (1790, '2025-06-27', 1, 19, 1, '2025-02-17 16:14:44', NULL, NULL, NULL, 40, 40, 21, 15, 9);
INSERT INTO `schedules` VALUES (1791, '2025-07-01', 1, 19, 1, '2025-02-17 16:14:44', NULL, NULL, NULL, 41, 41, 21, 15, 9);
INSERT INTO `schedules` VALUES (1792, '2025-07-04', 1, 19, 1, '2025-02-17 16:14:44', NULL, NULL, NULL, 42, 42, 21, 15, 9);
INSERT INTO `schedules` VALUES (1793, '2025-07-08', 1, 19, 1, '2025-02-17 16:14:44', NULL, NULL, NULL, 43, 43, 21, 15, 9);
INSERT INTO `schedules` VALUES (1794, '2025-07-11', 1, 19, 1, '2025-02-17 16:14:44', NULL, NULL, NULL, 44, 44, 21, 15, 9);
INSERT INTO `schedules` VALUES (1795, '2025-07-15', 1, 19, 1, '2025-02-17 16:14:44', NULL, NULL, NULL, 45, 45, 21, 15, 9);
INSERT INTO `schedules` VALUES (1796, '2025-07-18', 1, 19, 1, '2025-02-17 16:14:44', NULL, NULL, NULL, 46, 46, 21, 15, 9);
INSERT INTO `schedules` VALUES (1797, '2025-07-22', 1, 19, 1, '2025-02-17 16:14:44', NULL, NULL, NULL, 47, 47, 21, 15, 9);
INSERT INTO `schedules` VALUES (1798, '2025-07-25', 1, 19, 1, '2025-02-17 16:14:44', NULL, NULL, NULL, 48, 48, 21, 15, 9);
INSERT INTO `schedules` VALUES (1799, '2025-07-29', 1, 19, 1, '2025-02-17 16:14:44', NULL, NULL, NULL, 49, 49, 21, 15, 9);
INSERT INTO `schedules` VALUES (1800, '2025-08-01', 1, 19, 1, '2025-02-17 16:14:44', NULL, NULL, NULL, 50, 50, 21, 15, 9);
INSERT INTO `schedules` VALUES (1801, '2025-08-05', 1, 19, 1, '2025-02-17 16:14:44', NULL, NULL, NULL, 51, 51, 21, 15, 9);
INSERT INTO `schedules` VALUES (1802, '2025-08-08', 1, 19, 1, '2025-02-17 16:14:44', NULL, NULL, NULL, 52, 52, 21, 15, 9);
INSERT INTO `schedules` VALUES (1803, '2025-08-12', 1, 19, 1, '2025-02-17 16:14:44', NULL, NULL, NULL, 53, 53, 21, 15, 9);
INSERT INTO `schedules` VALUES (1804, '2025-08-15', 1, 19, 1, '2025-02-17 16:14:44', NULL, NULL, NULL, 54, 54, 21, 15, 9);
INSERT INTO `schedules` VALUES (1805, '2025-08-19', 1, 19, 1, '2025-02-17 16:14:44', NULL, NULL, NULL, 55, 55, 21, 15, 9);
INSERT INTO `schedules` VALUES (1806, '2025-08-22', 1, 19, 1, '2025-02-17 16:14:44', NULL, NULL, NULL, 56, 56, 21, 15, 9);
INSERT INTO `schedules` VALUES (1807, '2025-08-26', 1, 19, 1, '2025-02-17 16:14:44', NULL, NULL, NULL, 57, 57, 21, 15, 9);
INSERT INTO `schedules` VALUES (1808, '2025-08-29', 1, 19, 1, '2025-02-17 16:14:44', NULL, NULL, NULL, 58, 58, 21, 15, 9);
INSERT INTO `schedules` VALUES (1809, '2025-09-02', 1, 19, 1, '2025-02-17 16:14:44', NULL, NULL, NULL, 59, 59, 21, 15, 9);
INSERT INTO `schedules` VALUES (1810, '2025-09-05', 1, 19, 1, '2025-02-17 16:14:44', NULL, NULL, NULL, 60, 60, 21, 15, 9);
INSERT INTO `schedules` VALUES (1811, '2025-09-09', 1, 19, 1, '2025-02-17 16:14:44', NULL, NULL, NULL, 61, 61, 21, 15, 9);
INSERT INTO `schedules` VALUES (1812, '2025-09-12', 1, 19, 1, '2025-02-17 16:14:44', NULL, NULL, NULL, 62, 62, 21, 15, 9);
INSERT INTO `schedules` VALUES (1813, '2025-09-16', 1, 19, 1, '2025-02-17 16:14:44', NULL, NULL, NULL, 63, 63, 21, 15, 9);
INSERT INTO `schedules` VALUES (1814, '2025-09-19', 1, 19, 1, '2025-02-17 16:14:45', NULL, NULL, NULL, 64, 64, 21, 15, 9);
INSERT INTO `schedules` VALUES (1815, '2025-09-23', 1, 19, 1, '2025-02-17 16:14:45', NULL, NULL, NULL, 65, 65, 21, 15, 9);
INSERT INTO `schedules` VALUES (1816, '2025-09-26', 1, 19, 1, '2025-02-17 16:14:45', NULL, NULL, NULL, 66, 66, 21, 15, 9);
INSERT INTO `schedules` VALUES (1817, '2025-09-30', 1, 19, 1, '2025-02-17 16:14:45', NULL, NULL, NULL, 67, 67, 21, 15, 9);
INSERT INTO `schedules` VALUES (1818, '2025-10-03', 1, 19, 1, '2025-02-17 16:14:45', NULL, NULL, NULL, 68, 68, 21, 15, 9);
INSERT INTO `schedules` VALUES (1819, '2025-10-07', 1, 19, 1, '2025-02-17 16:14:45', NULL, NULL, NULL, 69, 69, 21, 15, 9);
INSERT INTO `schedules` VALUES (1820, '2025-10-10', 1, 19, 1, '2025-02-17 16:14:45', NULL, NULL, NULL, 70, 70, 21, 15, 9);
INSERT INTO `schedules` VALUES (1821, '2025-10-14', 1, 19, 1, '2025-02-17 16:14:45', NULL, NULL, NULL, 71, 71, 21, 15, 9);
INSERT INTO `schedules` VALUES (1822, '2025-10-17', 1, 19, 1, '2025-02-17 16:14:45', NULL, NULL, NULL, 72, 72, 21, 15, 9);
INSERT INTO `schedules` VALUES (1823, '2025-10-21', 1, 19, 1, '2025-02-17 16:14:45', NULL, NULL, NULL, 73, 73, 21, 15, 9);
INSERT INTO `schedules` VALUES (1824, '2025-10-24', 1, 19, 1, '2025-02-17 16:14:45', NULL, NULL, NULL, 74, 74, 21, 15, 9);
INSERT INTO `schedules` VALUES (1825, '2025-10-28', 1, 19, 1, '2025-02-17 16:14:45', NULL, NULL, NULL, 75, 75, 21, 15, 9);
INSERT INTO `schedules` VALUES (1826, '2025-10-31', 1, 19, 1, '2025-02-17 16:14:45', NULL, NULL, NULL, 76, 76, 21, 15, 9);
INSERT INTO `schedules` VALUES (1827, '2025-11-04', 1, 19, 1, '2025-02-17 16:14:45', NULL, NULL, NULL, 77, 77, 21, 15, 9);
INSERT INTO `schedules` VALUES (1828, '2025-11-07', 1, 19, 1, '2025-02-17 16:14:45', NULL, NULL, NULL, 78, 78, 21, 15, 9);
INSERT INTO `schedules` VALUES (1829, '2025-11-11', 1, 19, 1, '2025-02-17 16:14:45', NULL, NULL, NULL, 79, 79, 21, 15, 9);
INSERT INTO `schedules` VALUES (1830, '2025-11-14', 1, 19, 1, '2025-02-17 16:14:45', NULL, NULL, NULL, 80, 80, 21, 15, 9);
INSERT INTO `schedules` VALUES (1831, '2025-11-18', 1, 19, 1, '2025-02-17 16:14:45', NULL, NULL, NULL, 81, 81, 21, 15, 9);
INSERT INTO `schedules` VALUES (1832, '2025-11-21', 1, 19, 1, '2025-02-17 16:14:45', NULL, NULL, NULL, 82, 82, 21, 15, 9);
INSERT INTO `schedules` VALUES (1833, '2025-11-25', 1, 19, 1, '2025-02-17 16:14:45', NULL, NULL, NULL, 83, 83, 21, 15, 9);
INSERT INTO `schedules` VALUES (1834, '2025-11-28', 1, 19, 1, '2025-02-17 16:14:45', NULL, NULL, NULL, 84, 84, 21, 15, 9);
INSERT INTO `schedules` VALUES (1835, '2025-12-02', 1, 19, 1, '2025-02-17 16:14:45', NULL, NULL, NULL, 85, 85, 21, 15, 9);
INSERT INTO `schedules` VALUES (1836, '2025-12-05', 1, 19, 1, '2025-02-17 16:14:45', NULL, NULL, NULL, 86, 86, 21, 15, 9);
INSERT INTO `schedules` VALUES (1837, '2025-12-09', 1, 19, 1, '2025-02-17 16:14:45', NULL, NULL, NULL, 87, 87, 21, 15, 9);
INSERT INTO `schedules` VALUES (1838, '2025-12-12', 1, 19, 1, '2025-02-17 16:14:45', NULL, NULL, NULL, 88, 88, 21, 15, 9);
INSERT INTO `schedules` VALUES (1839, '2025-12-16', 1, 19, 1, '2025-02-17 16:14:45', NULL, NULL, NULL, 89, 89, 21, 15, 9);
INSERT INTO `schedules` VALUES (1840, '2025-12-19', 1, 19, 1, '2025-02-17 16:14:45', NULL, NULL, NULL, 90, 90, 21, 15, 9);
INSERT INTO `schedules` VALUES (1841, '2025-12-23', 1, 19, 1, '2025-02-17 16:14:45', NULL, NULL, NULL, 91, 91, 21, 15, 9);
INSERT INTO `schedules` VALUES (1842, '2025-12-26', 1, 19, 1, '2025-02-17 16:14:45', NULL, NULL, NULL, 92, 92, 21, 15, 9);
INSERT INTO `schedules` VALUES (1843, '2025-12-30', 1, 19, 1, '2025-02-17 16:14:45', NULL, NULL, NULL, 93, 93, 21, 15, 9);
INSERT INTO `schedules` VALUES (1844, '2026-01-02', 1, 19, 1, '2025-02-17 16:14:45', NULL, NULL, NULL, 94, 94, 21, 15, 9);
INSERT INTO `schedules` VALUES (1845, '2026-01-06', 1, 19, 1, '2025-02-17 16:14:45', NULL, NULL, NULL, 95, 95, 21, 15, 9);
INSERT INTO `schedules` VALUES (1846, '2026-01-09', 1, 19, 1, '2025-02-17 16:14:45', NULL, NULL, NULL, 96, 96, 21, 15, 9);
INSERT INTO `schedules` VALUES (1847, '2025-02-11', 1, 18, 1, '2025-02-17 16:15:02', NULL, NULL, NULL, 1, 1, 21, 15, 9);
INSERT INTO `schedules` VALUES (1848, '2025-02-14', 1, 18, 1, '2025-02-17 16:15:02', NULL, NULL, NULL, 2, 2, 21, 15, 9);
INSERT INTO `schedules` VALUES (1849, '2025-02-18', 1, 18, 1, '2025-02-17 16:15:02', NULL, NULL, NULL, 3, 3, 21, 15, 9);
INSERT INTO `schedules` VALUES (1850, '2025-02-21', 1, 18, 1, '2025-02-17 16:15:02', NULL, NULL, NULL, 4, 4, 21, 15, 9);
INSERT INTO `schedules` VALUES (1851, '2025-02-25', 1, 18, 1, '2025-02-17 16:15:02', NULL, NULL, NULL, 5, 5, 21, 15, 9);
INSERT INTO `schedules` VALUES (1852, '2025-02-28', 1, 18, 1, '2025-02-17 16:15:02', NULL, NULL, NULL, 6, 6, 21, 15, 9);
INSERT INTO `schedules` VALUES (1853, '2025-03-04', 1, 18, 1, '2025-02-17 16:15:02', NULL, NULL, NULL, 7, 7, 21, 15, 9);
INSERT INTO `schedules` VALUES (1854, '2025-03-07', 1, 18, 1, '2025-02-17 16:15:02', NULL, NULL, NULL, 8, 8, 21, 15, 9);
INSERT INTO `schedules` VALUES (1855, '2025-03-11', 1, 18, 1, '2025-02-17 16:15:02', NULL, NULL, NULL, 9, 9, 21, 15, 9);
INSERT INTO `schedules` VALUES (1856, '2025-03-14', 1, 18, 1, '2025-02-17 16:15:02', NULL, NULL, NULL, 10, 10, 21, 15, 9);
INSERT INTO `schedules` VALUES (1857, '2025-03-18', 1, 18, 1, '2025-02-17 16:15:02', NULL, NULL, NULL, 11, 11, 21, 15, 9);
INSERT INTO `schedules` VALUES (1858, '2025-03-21', 1, 18, 1, '2025-02-17 16:15:03', NULL, NULL, NULL, 12, 12, 21, 15, 9);
INSERT INTO `schedules` VALUES (1859, '2025-03-25', 1, 18, 1, '2025-02-17 16:15:03', NULL, NULL, NULL, 13, 13, 21, 15, 9);
INSERT INTO `schedules` VALUES (1860, '2025-03-28', 1, 18, 1, '2025-02-17 16:15:03', NULL, NULL, NULL, 14, 14, 21, 15, 9);
INSERT INTO `schedules` VALUES (1861, '2025-04-01', 1, 18, 1, '2025-02-17 16:15:03', NULL, NULL, NULL, 15, 15, 21, 15, 9);
INSERT INTO `schedules` VALUES (1862, '2025-04-04', 1, 18, 1, '2025-02-17 16:15:03', NULL, NULL, NULL, 16, 16, 21, 15, 9);
INSERT INTO `schedules` VALUES (1863, '2025-04-08', 1, 18, 1, '2025-02-17 16:15:03', NULL, NULL, NULL, 17, 17, 21, 15, 9);
INSERT INTO `schedules` VALUES (1864, '2025-04-11', 1, 18, 1, '2025-02-17 16:15:03', NULL, NULL, NULL, 18, 18, 21, 15, 9);
INSERT INTO `schedules` VALUES (1865, '2025-04-15', 1, 18, 1, '2025-02-17 16:15:03', NULL, NULL, NULL, 19, 19, 21, 15, 9);
INSERT INTO `schedules` VALUES (1866, '2025-04-18', 1, 18, 1, '2025-02-17 16:15:03', NULL, NULL, NULL, 20, 20, 21, 15, 9);
INSERT INTO `schedules` VALUES (1867, '2025-04-22', 1, 18, 1, '2025-02-17 16:15:03', NULL, NULL, NULL, 21, 21, 21, 15, 9);
INSERT INTO `schedules` VALUES (1868, '2025-04-25', 1, 18, 1, '2025-02-17 16:15:03', NULL, NULL, NULL, 22, 22, 21, 15, 9);
INSERT INTO `schedules` VALUES (1869, '2025-04-29', 1, 18, 1, '2025-02-17 16:15:03', NULL, NULL, NULL, 23, 23, 21, 15, 9);
INSERT INTO `schedules` VALUES (1870, '2025-05-02', 1, 18, 1, '2025-02-17 16:15:03', NULL, NULL, NULL, 24, 24, 21, 15, 9);
INSERT INTO `schedules` VALUES (1871, '2025-05-06', 1, 18, 1, '2025-02-17 16:15:03', NULL, NULL, NULL, 25, 25, 21, 15, 9);
INSERT INTO `schedules` VALUES (1872, '2025-05-09', 1, 18, 1, '2025-02-17 16:15:03', NULL, NULL, NULL, 26, 26, 21, 15, 9);
INSERT INTO `schedules` VALUES (1873, '2025-05-13', 1, 18, 1, '2025-02-17 16:15:03', NULL, NULL, NULL, 27, 27, 21, 15, 9);
INSERT INTO `schedules` VALUES (1874, '2025-05-16', 1, 18, 1, '2025-02-17 16:15:03', NULL, NULL, NULL, 28, 28, 21, 15, 9);
INSERT INTO `schedules` VALUES (1875, '2025-05-20', 1, 18, 1, '2025-02-17 16:15:03', NULL, NULL, NULL, 29, 29, 21, 15, 9);
INSERT INTO `schedules` VALUES (1876, '2025-05-23', 1, 18, 1, '2025-02-17 16:15:03', NULL, NULL, NULL, 30, 30, 21, 15, 9);
INSERT INTO `schedules` VALUES (1877, '2025-05-27', 1, 18, 1, '2025-02-17 16:15:03', NULL, NULL, NULL, 31, 31, 21, 15, 9);
INSERT INTO `schedules` VALUES (1878, '2025-05-30', 1, 18, 1, '2025-02-17 16:15:03', NULL, NULL, NULL, 32, 32, 21, 15, 9);
INSERT INTO `schedules` VALUES (1879, '2025-06-03', 1, 18, 1, '2025-02-17 16:15:03', NULL, NULL, NULL, 33, 33, 21, 15, 9);
INSERT INTO `schedules` VALUES (1880, '2025-06-06', 1, 18, 1, '2025-02-17 16:15:03', NULL, NULL, NULL, 34, 34, 21, 15, 9);
INSERT INTO `schedules` VALUES (1881, '2025-06-10', 1, 18, 1, '2025-02-17 16:15:03', NULL, NULL, NULL, 35, 35, 21, 15, 9);
INSERT INTO `schedules` VALUES (1882, '2025-06-13', 1, 18, 1, '2025-02-17 16:15:03', NULL, NULL, NULL, 36, 36, 21, 15, 9);
INSERT INTO `schedules` VALUES (1883, '2025-06-17', 1, 18, 1, '2025-02-17 16:15:03', NULL, NULL, NULL, 37, 37, 21, 15, 9);
INSERT INTO `schedules` VALUES (1884, '2025-06-20', 1, 18, 1, '2025-02-17 16:15:03', NULL, NULL, NULL, 38, 38, 21, 15, 9);
INSERT INTO `schedules` VALUES (1885, '2025-06-24', 1, 18, 1, '2025-02-17 16:15:03', NULL, NULL, NULL, 39, 39, 21, 15, 9);
INSERT INTO `schedules` VALUES (1886, '2025-06-27', 1, 18, 1, '2025-02-17 16:15:03', NULL, NULL, NULL, 40, 40, 21, 15, 9);
INSERT INTO `schedules` VALUES (1887, '2025-07-01', 1, 18, 1, '2025-02-17 16:15:03', NULL, NULL, NULL, 41, 41, 21, 15, 9);
INSERT INTO `schedules` VALUES (1888, '2025-07-04', 1, 18, 1, '2025-02-17 16:15:03', NULL, NULL, NULL, 42, 42, 21, 15, 9);
INSERT INTO `schedules` VALUES (1889, '2025-07-08', 1, 18, 1, '2025-02-17 16:15:03', NULL, NULL, NULL, 43, 43, 21, 15, 9);
INSERT INTO `schedules` VALUES (1890, '2025-07-11', 1, 18, 1, '2025-02-17 16:15:03', NULL, NULL, NULL, 44, 44, 21, 15, 9);
INSERT INTO `schedules` VALUES (1891, '2025-07-15', 1, 18, 1, '2025-02-17 16:15:03', NULL, NULL, NULL, 45, 45, 21, 15, 9);
INSERT INTO `schedules` VALUES (1892, '2025-07-18', 1, 18, 1, '2025-02-17 16:15:03', NULL, NULL, NULL, 46, 46, 21, 15, 9);
INSERT INTO `schedules` VALUES (1893, '2025-07-22', 1, 18, 1, '2025-02-17 16:15:03', NULL, NULL, NULL, 47, 47, 21, 15, 9);
INSERT INTO `schedules` VALUES (1894, '2025-07-25', 1, 18, 1, '2025-02-17 16:15:03', NULL, NULL, NULL, 48, 48, 21, 15, 9);
INSERT INTO `schedules` VALUES (1895, '2025-07-29', 1, 18, 1, '2025-02-17 16:15:03', NULL, NULL, NULL, 49, 49, 21, 15, 9);
INSERT INTO `schedules` VALUES (1896, '2025-08-01', 1, 18, 1, '2025-02-17 16:15:03', NULL, NULL, NULL, 50, 50, 21, 15, 9);
INSERT INTO `schedules` VALUES (1897, '2025-08-05', 1, 18, 1, '2025-02-17 16:15:03', NULL, NULL, NULL, 51, 51, 21, 15, 9);
INSERT INTO `schedules` VALUES (1898, '2025-08-08', 1, 18, 1, '2025-02-17 16:15:03', NULL, NULL, NULL, 52, 52, 21, 15, 9);
INSERT INTO `schedules` VALUES (1899, '2025-08-12', 1, 18, 1, '2025-02-17 16:15:03', NULL, NULL, NULL, 53, 53, 21, 15, 9);
INSERT INTO `schedules` VALUES (1900, '2025-08-15', 1, 18, 1, '2025-02-17 16:15:03', NULL, NULL, NULL, 54, 54, 21, 15, 9);
INSERT INTO `schedules` VALUES (1901, '2025-08-19', 1, 18, 1, '2025-02-17 16:15:03', NULL, NULL, NULL, 55, 55, 21, 15, 9);
INSERT INTO `schedules` VALUES (1902, '2025-08-22', 1, 18, 1, '2025-02-17 16:15:03', NULL, NULL, NULL, 56, 56, 21, 15, 9);
INSERT INTO `schedules` VALUES (1903, '2025-08-26', 1, 18, 1, '2025-02-17 16:15:03', NULL, NULL, NULL, 57, 57, 21, 15, 9);
INSERT INTO `schedules` VALUES (1904, '2025-08-29', 1, 18, 1, '2025-02-17 16:15:03', NULL, NULL, NULL, 58, 58, 21, 15, 9);
INSERT INTO `schedules` VALUES (1905, '2025-09-02', 1, 18, 1, '2025-02-17 16:15:03', NULL, NULL, NULL, 59, 59, 21, 15, 9);
INSERT INTO `schedules` VALUES (1906, '2025-09-05', 1, 18, 1, '2025-02-17 16:15:03', NULL, NULL, NULL, 60, 60, 21, 15, 9);
INSERT INTO `schedules` VALUES (1907, '2025-09-09', 1, 18, 1, '2025-02-17 16:15:03', NULL, NULL, NULL, 61, 61, 21, 15, 9);
INSERT INTO `schedules` VALUES (1908, '2025-09-12', 1, 18, 1, '2025-02-17 16:15:03', NULL, NULL, NULL, 62, 62, 21, 15, 9);
INSERT INTO `schedules` VALUES (1909, '2025-09-16', 1, 18, 1, '2025-02-17 16:15:03', NULL, NULL, NULL, 63, 63, 21, 15, 9);
INSERT INTO `schedules` VALUES (1910, '2025-09-19', 1, 18, 1, '2025-02-17 16:15:03', NULL, NULL, NULL, 64, 64, 21, 15, 9);
INSERT INTO `schedules` VALUES (1911, '2025-09-23', 1, 18, 1, '2025-02-17 16:15:03', NULL, NULL, NULL, 65, 65, 21, 15, 9);
INSERT INTO `schedules` VALUES (1912, '2025-09-26', 1, 18, 1, '2025-02-17 16:15:03', NULL, NULL, NULL, 66, 66, 21, 15, 9);
INSERT INTO `schedules` VALUES (1913, '2025-09-30', 1, 18, 1, '2025-02-17 16:15:03', NULL, NULL, NULL, 67, 67, 21, 15, 9);
INSERT INTO `schedules` VALUES (1914, '2025-10-03', 1, 18, 1, '2025-02-17 16:15:03', NULL, NULL, NULL, 68, 68, 21, 15, 9);
INSERT INTO `schedules` VALUES (1915, '2025-10-07', 1, 18, 1, '2025-02-17 16:15:03', NULL, NULL, NULL, 69, 69, 21, 15, 9);
INSERT INTO `schedules` VALUES (1916, '2025-10-10', 1, 18, 1, '2025-02-17 16:15:03', NULL, NULL, NULL, 70, 70, 21, 15, 9);
INSERT INTO `schedules` VALUES (1917, '2025-10-14', 1, 18, 1, '2025-02-17 16:15:03', NULL, NULL, NULL, 71, 71, 21, 15, 9);
INSERT INTO `schedules` VALUES (1918, '2025-10-17', 1, 18, 1, '2025-02-17 16:15:03', NULL, NULL, NULL, 72, 72, 21, 15, 9);
INSERT INTO `schedules` VALUES (1919, '2025-10-21', 1, 18, 1, '2025-02-17 16:15:03', NULL, NULL, NULL, 73, 73, 21, 15, 9);
INSERT INTO `schedules` VALUES (1920, '2025-10-24', 1, 18, 1, '2025-02-17 16:15:03', NULL, NULL, NULL, 74, 74, 21, 15, 9);
INSERT INTO `schedules` VALUES (1921, '2025-10-28', 1, 18, 1, '2025-02-17 16:15:03', NULL, NULL, NULL, 75, 75, 21, 15, 9);
INSERT INTO `schedules` VALUES (1922, '2025-10-31', 1, 18, 1, '2025-02-17 16:15:03', NULL, NULL, NULL, 76, 76, 21, 15, 9);
INSERT INTO `schedules` VALUES (1923, '2025-11-04', 1, 18, 1, '2025-02-17 16:15:03', NULL, NULL, NULL, 77, 77, 21, 15, 9);
INSERT INTO `schedules` VALUES (1924, '2025-11-07', 1, 18, 1, '2025-02-17 16:15:03', NULL, NULL, NULL, 78, 78, 21, 15, 9);
INSERT INTO `schedules` VALUES (1925, '2025-11-11', 1, 18, 1, '2025-02-17 16:15:03', NULL, NULL, NULL, 79, 79, 21, 15, 9);
INSERT INTO `schedules` VALUES (1926, '2025-11-14', 1, 18, 1, '2025-02-17 16:15:03', NULL, NULL, NULL, 80, 80, 21, 15, 9);
INSERT INTO `schedules` VALUES (1927, '2025-11-18', 1, 18, 1, '2025-02-17 16:15:03', NULL, NULL, NULL, 81, 81, 21, 15, 9);
INSERT INTO `schedules` VALUES (1928, '2025-11-21', 1, 18, 1, '2025-02-17 16:15:03', NULL, NULL, NULL, 82, 82, 21, 15, 9);
INSERT INTO `schedules` VALUES (1929, '2025-11-25', 1, 18, 1, '2025-02-17 16:15:03', NULL, NULL, NULL, 83, 83, 21, 15, 9);
INSERT INTO `schedules` VALUES (1930, '2025-11-28', 1, 18, 1, '2025-02-17 16:15:03', NULL, NULL, NULL, 84, 84, 21, 15, 9);
INSERT INTO `schedules` VALUES (1931, '2025-12-02', 1, 18, 1, '2025-02-17 16:15:03', NULL, NULL, NULL, 85, 85, 21, 15, 9);
INSERT INTO `schedules` VALUES (1932, '2025-12-05', 1, 18, 1, '2025-02-17 16:15:03', NULL, NULL, NULL, 86, 86, 21, 15, 9);
INSERT INTO `schedules` VALUES (1933, '2025-12-09', 1, 18, 1, '2025-02-17 16:15:03', NULL, NULL, NULL, 87, 87, 21, 15, 9);
INSERT INTO `schedules` VALUES (1934, '2025-12-12', 1, 18, 1, '2025-02-17 16:15:03', NULL, NULL, NULL, 88, 88, 21, 15, 9);
INSERT INTO `schedules` VALUES (1935, '2025-12-16', 1, 18, 1, '2025-02-17 16:15:03', NULL, NULL, NULL, 89, 89, 21, 15, 9);
INSERT INTO `schedules` VALUES (1936, '2025-12-19', 1, 18, 1, '2025-02-17 16:15:03', NULL, NULL, NULL, 90, 90, 21, 15, 9);
INSERT INTO `schedules` VALUES (1937, '2025-12-23', 1, 18, 1, '2025-02-17 16:15:03', NULL, NULL, NULL, 91, 91, 21, 15, 9);
INSERT INTO `schedules` VALUES (1938, '2025-12-26', 1, 18, 1, '2025-02-17 16:15:03', NULL, NULL, NULL, 92, 92, 21, 15, 9);
INSERT INTO `schedules` VALUES (1939, '2025-12-30', 1, 18, 1, '2025-02-17 16:15:03', NULL, NULL, NULL, 93, 93, 21, 15, 9);
INSERT INTO `schedules` VALUES (1940, '2026-01-02', 1, 18, 1, '2025-02-17 16:15:03', NULL, NULL, NULL, 94, 94, 21, 15, 9);
INSERT INTO `schedules` VALUES (1941, '2026-01-06', 1, 18, 1, '2025-02-17 16:15:03', NULL, NULL, NULL, 95, 95, 21, 15, 9);
INSERT INTO `schedules` VALUES (1942, '2026-01-09', 1, 18, 1, '2025-02-17 16:15:03', NULL, NULL, NULL, 96, 96, 21, 15, 9);
INSERT INTO `schedules` VALUES (1943, '2025-02-11', 1, 20, 1, '2025-02-17 16:15:43', NULL, NULL, NULL, 1, 1, 22, 15, 9);
INSERT INTO `schedules` VALUES (1944, '2025-02-14', 1, 20, 1, '2025-02-17 16:15:43', NULL, NULL, NULL, 2, 2, 22, 15, 9);
INSERT INTO `schedules` VALUES (1945, '2025-02-18', 1, 20, 1, '2025-02-17 16:15:43', NULL, NULL, NULL, 3, 3, 22, 15, 9);
INSERT INTO `schedules` VALUES (1946, '2025-02-21', 1, 20, 1, '2025-02-17 16:15:43', NULL, NULL, NULL, 4, 4, 22, 15, 9);
INSERT INTO `schedules` VALUES (1947, '2025-02-25', 1, 20, 1, '2025-02-17 16:15:43', NULL, NULL, NULL, 5, 5, 22, 15, 9);
INSERT INTO `schedules` VALUES (1948, '2025-02-28', 1, 20, 1, '2025-02-17 16:15:43', NULL, NULL, NULL, 6, 6, 22, 15, 9);
INSERT INTO `schedules` VALUES (1949, '2025-03-04', 1, 20, 1, '2025-02-17 16:15:43', NULL, NULL, NULL, 7, 7, 22, 15, 9);
INSERT INTO `schedules` VALUES (1950, '2025-03-07', 1, 20, 1, '2025-02-17 16:15:43', NULL, NULL, NULL, 8, 8, 22, 15, 9);
INSERT INTO `schedules` VALUES (1951, '2025-03-11', 1, 20, 1, '2025-02-17 16:15:43', NULL, NULL, NULL, 9, 9, 22, 15, 9);
INSERT INTO `schedules` VALUES (1952, '2025-03-14', 1, 20, 1, '2025-02-17 16:15:43', NULL, NULL, NULL, 10, 10, 22, 15, 9);
INSERT INTO `schedules` VALUES (1953, '2025-03-18', 1, 20, 1, '2025-02-17 16:15:43', NULL, NULL, NULL, 11, 11, 22, 15, 9);
INSERT INTO `schedules` VALUES (1954, '2025-03-21', 1, 20, 1, '2025-02-17 16:15:43', NULL, NULL, NULL, 12, 12, 22, 15, 9);
INSERT INTO `schedules` VALUES (1955, '2025-03-25', 1, 20, 1, '2025-02-17 16:15:43', NULL, NULL, NULL, 13, 13, 22, 15, 9);
INSERT INTO `schedules` VALUES (1956, '2025-03-28', 1, 20, 1, '2025-02-17 16:15:43', NULL, NULL, NULL, 14, 14, 22, 15, 9);
INSERT INTO `schedules` VALUES (1957, '2025-04-01', 1, 20, 1, '2025-02-17 16:15:43', NULL, NULL, NULL, 15, 15, 22, 15, 9);
INSERT INTO `schedules` VALUES (1958, '2025-04-04', 1, 20, 1, '2025-02-17 16:15:43', NULL, NULL, NULL, 16, 16, 22, 15, 9);
INSERT INTO `schedules` VALUES (1959, '2025-04-08', 1, 20, 1, '2025-02-17 16:15:43', NULL, NULL, NULL, 17, 17, 22, 15, 9);
INSERT INTO `schedules` VALUES (1960, '2025-04-11', 1, 20, 1, '2025-02-17 16:15:43', NULL, NULL, NULL, 18, 18, 22, 15, 9);
INSERT INTO `schedules` VALUES (1961, '2025-04-15', 1, 20, 1, '2025-02-17 16:15:43', NULL, NULL, NULL, 19, 19, 22, 15, 9);
INSERT INTO `schedules` VALUES (1962, '2025-04-18', 1, 20, 1, '2025-02-17 16:15:43', NULL, NULL, NULL, 20, 20, 22, 15, 9);
INSERT INTO `schedules` VALUES (1963, '2025-04-22', 1, 20, 1, '2025-02-17 16:15:43', NULL, NULL, NULL, 21, 21, 22, 15, 9);
INSERT INTO `schedules` VALUES (1964, '2025-04-25', 1, 20, 1, '2025-02-17 16:15:43', NULL, NULL, NULL, 22, 22, 22, 15, 9);
INSERT INTO `schedules` VALUES (1965, '2025-04-29', 1, 20, 1, '2025-02-17 16:15:43', NULL, NULL, NULL, 23, 23, 22, 15, 9);
INSERT INTO `schedules` VALUES (1966, '2025-05-02', 1, 20, 1, '2025-02-17 16:15:43', NULL, NULL, NULL, 24, 24, 22, 15, 9);
INSERT INTO `schedules` VALUES (1967, '2025-05-06', 1, 20, 1, '2025-02-17 16:15:43', NULL, NULL, NULL, 25, 25, 22, 15, 9);
INSERT INTO `schedules` VALUES (1968, '2025-05-09', 1, 20, 1, '2025-02-17 16:15:43', NULL, NULL, NULL, 26, 26, 22, 15, 9);
INSERT INTO `schedules` VALUES (1969, '2025-05-13', 1, 20, 1, '2025-02-17 16:15:43', NULL, NULL, NULL, 27, 27, 22, 15, 9);
INSERT INTO `schedules` VALUES (1970, '2025-05-16', 1, 20, 1, '2025-02-17 16:15:43', NULL, NULL, NULL, 28, 28, 22, 15, 9);
INSERT INTO `schedules` VALUES (1971, '2025-05-20', 1, 20, 1, '2025-02-17 16:15:43', NULL, NULL, NULL, 29, 29, 22, 15, 9);
INSERT INTO `schedules` VALUES (1972, '2025-05-23', 1, 20, 1, '2025-02-17 16:15:43', NULL, NULL, NULL, 30, 30, 22, 15, 9);
INSERT INTO `schedules` VALUES (1973, '2025-05-27', 1, 20, 1, '2025-02-17 16:15:43', NULL, NULL, NULL, 31, 31, 22, 15, 9);
INSERT INTO `schedules` VALUES (1974, '2025-05-30', 1, 20, 1, '2025-02-17 16:15:43', NULL, NULL, NULL, 32, 32, 22, 15, 9);
INSERT INTO `schedules` VALUES (1975, '2025-06-03', 1, 20, 1, '2025-02-17 16:15:43', NULL, NULL, NULL, 33, 33, 22, 15, 9);
INSERT INTO `schedules` VALUES (1976, '2025-06-06', 1, 20, 1, '2025-02-17 16:15:43', NULL, NULL, NULL, 34, 34, 22, 15, 9);
INSERT INTO `schedules` VALUES (1977, '2025-06-10', 1, 20, 1, '2025-02-17 16:15:43', NULL, NULL, NULL, 35, 35, 22, 15, 9);
INSERT INTO `schedules` VALUES (1978, '2025-06-13', 1, 20, 1, '2025-02-17 16:15:43', NULL, NULL, NULL, 36, 36, 22, 15, 9);
INSERT INTO `schedules` VALUES (1979, '2025-06-17', 1, 20, 1, '2025-02-17 16:15:43', NULL, NULL, NULL, 37, 37, 22, 15, 9);
INSERT INTO `schedules` VALUES (1980, '2025-06-20', 1, 20, 1, '2025-02-17 16:15:43', NULL, NULL, NULL, 38, 38, 22, 15, 9);
INSERT INTO `schedules` VALUES (1981, '2025-06-24', 1, 20, 1, '2025-02-17 16:15:43', NULL, NULL, NULL, 39, 39, 22, 15, 9);
INSERT INTO `schedules` VALUES (1982, '2025-06-27', 1, 20, 1, '2025-02-17 16:15:43', NULL, NULL, NULL, 40, 40, 22, 15, 9);
INSERT INTO `schedules` VALUES (1983, '2025-07-01', 1, 20, 1, '2025-02-17 16:15:43', NULL, NULL, NULL, 41, 41, 22, 15, 9);
INSERT INTO `schedules` VALUES (1984, '2025-07-04', 1, 20, 1, '2025-02-17 16:15:43', NULL, NULL, NULL, 42, 42, 22, 15, 9);
INSERT INTO `schedules` VALUES (1985, '2025-07-08', 1, 20, 1, '2025-02-17 16:15:43', NULL, NULL, NULL, 43, 43, 22, 15, 9);
INSERT INTO `schedules` VALUES (1986, '2025-07-11', 1, 20, 1, '2025-02-17 16:15:43', NULL, NULL, NULL, 44, 44, 22, 15, 9);
INSERT INTO `schedules` VALUES (1987, '2025-07-15', 1, 20, 1, '2025-02-17 16:15:43', NULL, NULL, NULL, 45, 45, 22, 15, 9);
INSERT INTO `schedules` VALUES (1988, '2025-07-18', 1, 20, 1, '2025-02-17 16:15:43', NULL, NULL, NULL, 46, 46, 22, 15, 9);
INSERT INTO `schedules` VALUES (1989, '2025-07-22', 1, 20, 1, '2025-02-17 16:15:43', NULL, NULL, NULL, 47, 47, 22, 15, 9);
INSERT INTO `schedules` VALUES (1990, '2025-07-25', 1, 20, 1, '2025-02-17 16:15:43', NULL, NULL, NULL, 48, 48, 22, 15, 9);
INSERT INTO `schedules` VALUES (1991, '2025-07-29', 1, 20, 1, '2025-02-17 16:15:43', NULL, NULL, NULL, 49, 49, 22, 15, 9);
INSERT INTO `schedules` VALUES (1992, '2025-08-01', 1, 20, 1, '2025-02-17 16:15:43', NULL, NULL, NULL, 50, 50, 22, 15, 9);
INSERT INTO `schedules` VALUES (1993, '2025-08-05', 1, 20, 1, '2025-02-17 16:15:43', NULL, NULL, NULL, 51, 51, 22, 15, 9);
INSERT INTO `schedules` VALUES (1994, '2025-08-08', 1, 20, 1, '2025-02-17 16:15:43', NULL, NULL, NULL, 52, 52, 22, 15, 9);
INSERT INTO `schedules` VALUES (1995, '2025-08-12', 1, 20, 1, '2025-02-17 16:15:43', NULL, NULL, NULL, 53, 53, 22, 15, 9);
INSERT INTO `schedules` VALUES (1996, '2025-08-15', 1, 20, 1, '2025-02-17 16:15:43', NULL, NULL, NULL, 54, 54, 22, 15, 9);
INSERT INTO `schedules` VALUES (1997, '2025-08-19', 1, 20, 1, '2025-02-17 16:15:43', NULL, NULL, NULL, 55, 55, 22, 15, 9);
INSERT INTO `schedules` VALUES (1998, '2025-08-22', 1, 20, 1, '2025-02-17 16:15:43', NULL, NULL, NULL, 56, 56, 22, 15, 9);
INSERT INTO `schedules` VALUES (1999, '2025-08-26', 1, 20, 1, '2025-02-17 16:15:43', NULL, NULL, NULL, 57, 57, 22, 15, 9);
INSERT INTO `schedules` VALUES (2000, '2025-08-29', 1, 20, 1, '2025-02-17 16:15:43', NULL, NULL, NULL, 58, 58, 22, 15, 9);
INSERT INTO `schedules` VALUES (2001, '2025-09-02', 1, 20, 1, '2025-02-17 16:15:43', NULL, NULL, NULL, 59, 59, 22, 15, 9);
INSERT INTO `schedules` VALUES (2002, '2025-09-05', 1, 20, 1, '2025-02-17 16:15:43', NULL, NULL, NULL, 60, 60, 22, 15, 9);
INSERT INTO `schedules` VALUES (2003, '2025-09-09', 1, 20, 1, '2025-02-17 16:15:43', NULL, NULL, NULL, 61, 61, 22, 15, 9);
INSERT INTO `schedules` VALUES (2004, '2025-09-12', 1, 20, 1, '2025-02-17 16:15:43', NULL, NULL, NULL, 62, 62, 22, 15, 9);
INSERT INTO `schedules` VALUES (2005, '2025-09-16', 1, 20, 1, '2025-02-17 16:15:43', NULL, NULL, NULL, 63, 63, 22, 15, 9);
INSERT INTO `schedules` VALUES (2006, '2025-09-19', 1, 20, 1, '2025-02-17 16:15:43', NULL, NULL, NULL, 64, 64, 22, 15, 9);
INSERT INTO `schedules` VALUES (2007, '2025-09-23', 1, 20, 1, '2025-02-17 16:15:43', NULL, NULL, NULL, 65, 65, 22, 15, 9);
INSERT INTO `schedules` VALUES (2008, '2025-09-26', 1, 20, 1, '2025-02-17 16:15:43', NULL, NULL, NULL, 66, 66, 22, 15, 9);
INSERT INTO `schedules` VALUES (2009, '2025-09-30', 1, 20, 1, '2025-02-17 16:15:43', NULL, NULL, NULL, 67, 67, 22, 15, 9);
INSERT INTO `schedules` VALUES (2010, '2025-10-03', 1, 20, 1, '2025-02-17 16:15:43', NULL, NULL, NULL, 68, 68, 22, 15, 9);
INSERT INTO `schedules` VALUES (2011, '2025-10-07', 1, 20, 1, '2025-02-17 16:15:43', NULL, NULL, NULL, 69, 69, 22, 15, 9);
INSERT INTO `schedules` VALUES (2012, '2025-10-10', 1, 20, 1, '2025-02-17 16:15:43', NULL, NULL, NULL, 70, 70, 22, 15, 9);
INSERT INTO `schedules` VALUES (2013, '2025-10-14', 1, 20, 1, '2025-02-17 16:15:43', NULL, NULL, NULL, 71, 71, 22, 15, 9);
INSERT INTO `schedules` VALUES (2014, '2025-10-17', 1, 20, 1, '2025-02-17 16:15:43', NULL, NULL, NULL, 72, 72, 22, 15, 9);
INSERT INTO `schedules` VALUES (2015, '2025-10-21', 1, 20, 1, '2025-02-17 16:15:43', NULL, NULL, NULL, 73, 73, 22, 15, 9);
INSERT INTO `schedules` VALUES (2016, '2025-10-24', 1, 20, 1, '2025-02-17 16:15:43', NULL, NULL, NULL, 74, 74, 22, 15, 9);
INSERT INTO `schedules` VALUES (2017, '2025-10-28', 1, 20, 1, '2025-02-17 16:15:43', NULL, NULL, NULL, 75, 75, 22, 15, 9);
INSERT INTO `schedules` VALUES (2018, '2025-10-31', 1, 20, 1, '2025-02-17 16:15:43', NULL, NULL, NULL, 76, 76, 22, 15, 9);
INSERT INTO `schedules` VALUES (2019, '2025-11-04', 1, 20, 1, '2025-02-17 16:15:43', NULL, NULL, NULL, 77, 77, 22, 15, 9);
INSERT INTO `schedules` VALUES (2020, '2025-11-07', 1, 20, 1, '2025-02-17 16:15:43', NULL, NULL, NULL, 78, 78, 22, 15, 9);
INSERT INTO `schedules` VALUES (2021, '2025-11-11', 1, 20, 1, '2025-02-17 16:15:43', NULL, NULL, NULL, 79, 79, 22, 15, 9);
INSERT INTO `schedules` VALUES (2022, '2025-11-14', 1, 20, 1, '2025-02-17 16:15:43', NULL, NULL, NULL, 80, 80, 22, 15, 9);
INSERT INTO `schedules` VALUES (2023, '2025-11-18', 1, 20, 1, '2025-02-17 16:15:43', NULL, NULL, NULL, 81, 81, 22, 15, 9);
INSERT INTO `schedules` VALUES (2024, '2025-11-21', 1, 20, 1, '2025-02-17 16:15:43', NULL, NULL, NULL, 82, 82, 22, 15, 9);
INSERT INTO `schedules` VALUES (2025, '2025-11-25', 1, 20, 1, '2025-02-17 16:15:43', NULL, NULL, NULL, 83, 83, 22, 15, 9);
INSERT INTO `schedules` VALUES (2026, '2025-11-28', 1, 20, 1, '2025-02-17 16:15:43', NULL, NULL, NULL, 84, 84, 22, 15, 9);
INSERT INTO `schedules` VALUES (2027, '2025-12-02', 1, 20, 1, '2025-02-17 16:15:43', NULL, NULL, NULL, 85, 85, 22, 15, 9);
INSERT INTO `schedules` VALUES (2028, '2025-12-05', 1, 20, 1, '2025-02-17 16:15:43', NULL, NULL, NULL, 86, 86, 22, 15, 9);
INSERT INTO `schedules` VALUES (2029, '2025-12-09', 1, 20, 1, '2025-02-17 16:15:43', NULL, NULL, NULL, 87, 87, 22, 15, 9);
INSERT INTO `schedules` VALUES (2030, '2025-12-12', 1, 20, 1, '2025-02-17 16:15:43', NULL, NULL, NULL, 88, 88, 22, 15, 9);
INSERT INTO `schedules` VALUES (2031, '2025-12-16', 1, 20, 1, '2025-02-17 16:15:43', NULL, NULL, NULL, 89, 89, 22, 15, 9);
INSERT INTO `schedules` VALUES (2032, '2025-12-19', 1, 20, 1, '2025-02-17 16:15:43', NULL, NULL, NULL, 90, 90, 22, 15, 9);
INSERT INTO `schedules` VALUES (2033, '2025-12-23', 1, 20, 1, '2025-02-17 16:15:44', NULL, NULL, NULL, 91, 91, 22, 15, 9);
INSERT INTO `schedules` VALUES (2034, '2025-12-26', 1, 20, 1, '2025-02-17 16:15:44', NULL, NULL, NULL, 92, 92, 22, 15, 9);
INSERT INTO `schedules` VALUES (2035, '2025-12-30', 1, 20, 1, '2025-02-17 16:15:44', NULL, NULL, NULL, 93, 93, 22, 15, 9);
INSERT INTO `schedules` VALUES (2036, '2026-01-02', 1, 20, 1, '2025-02-17 16:15:44', NULL, NULL, NULL, 94, 94, 22, 15, 9);
INSERT INTO `schedules` VALUES (2037, '2026-01-06', 1, 20, 1, '2025-02-17 16:15:44', NULL, NULL, NULL, 95, 95, 22, 15, 9);
INSERT INTO `schedules` VALUES (2038, '2026-01-09', 1, 20, 1, '2025-02-17 16:15:44', NULL, NULL, NULL, 96, 96, 22, 15, 9);
INSERT INTO `schedules` VALUES (2039, '2025-02-11', 1, 21, 1, '2025-02-17 16:16:28', NULL, NULL, NULL, 1, 1, 22, 15, 9);
INSERT INTO `schedules` VALUES (2040, '2025-02-14', 1, 21, 1, '2025-02-17 16:16:28', NULL, NULL, NULL, 2, 2, 22, 15, 9);
INSERT INTO `schedules` VALUES (2041, '2025-02-18', 1, 21, 1, '2025-02-17 16:16:28', NULL, NULL, NULL, 3, 3, 22, 15, 9);
INSERT INTO `schedules` VALUES (2042, '2025-02-21', 1, 21, 1, '2025-02-17 16:16:28', NULL, NULL, NULL, 4, 4, 22, 15, 9);
INSERT INTO `schedules` VALUES (2043, '2025-02-25', 1, 21, 1, '2025-02-17 16:16:28', NULL, NULL, NULL, 5, 5, 22, 15, 9);
INSERT INTO `schedules` VALUES (2044, '2025-02-28', 1, 21, 1, '2025-02-17 16:16:28', NULL, NULL, NULL, 6, 6, 22, 15, 9);
INSERT INTO `schedules` VALUES (2045, '2025-03-04', 1, 21, 1, '2025-02-17 16:16:28', NULL, NULL, NULL, 7, 7, 22, 15, 9);
INSERT INTO `schedules` VALUES (2046, '2025-03-07', 1, 21, 1, '2025-02-17 16:16:28', NULL, NULL, NULL, 8, 8, 22, 15, 9);
INSERT INTO `schedules` VALUES (2047, '2025-03-11', 1, 21, 1, '2025-02-17 16:16:28', NULL, NULL, NULL, 9, 9, 22, 15, 9);
INSERT INTO `schedules` VALUES (2048, '2025-03-14', 1, 21, 1, '2025-02-17 16:16:28', NULL, NULL, NULL, 10, 10, 22, 15, 9);
INSERT INTO `schedules` VALUES (2049, '2025-03-18', 1, 21, 1, '2025-02-17 16:16:28', NULL, NULL, NULL, 11, 11, 22, 15, 9);
INSERT INTO `schedules` VALUES (2050, '2025-03-21', 1, 21, 1, '2025-02-17 16:16:28', NULL, NULL, NULL, 12, 12, 22, 15, 9);
INSERT INTO `schedules` VALUES (2051, '2025-03-25', 1, 21, 1, '2025-02-17 16:16:28', NULL, NULL, NULL, 13, 13, 22, 15, 9);
INSERT INTO `schedules` VALUES (2052, '2025-03-28', 1, 21, 1, '2025-02-17 16:16:28', NULL, NULL, NULL, 14, 14, 22, 15, 9);
INSERT INTO `schedules` VALUES (2053, '2025-04-01', 1, 21, 1, '2025-02-17 16:16:28', NULL, NULL, NULL, 15, 15, 22, 15, 9);
INSERT INTO `schedules` VALUES (2054, '2025-04-04', 1, 21, 1, '2025-02-17 16:16:28', NULL, NULL, NULL, 16, 16, 22, 15, 9);
INSERT INTO `schedules` VALUES (2055, '2025-04-08', 1, 21, 1, '2025-02-17 16:16:28', NULL, NULL, NULL, 17, 17, 22, 15, 9);
INSERT INTO `schedules` VALUES (2056, '2025-04-11', 1, 21, 1, '2025-02-17 16:16:28', NULL, NULL, NULL, 18, 18, 22, 15, 9);
INSERT INTO `schedules` VALUES (2057, '2025-04-15', 1, 21, 1, '2025-02-17 16:16:28', NULL, NULL, NULL, 19, 19, 22, 15, 9);
INSERT INTO `schedules` VALUES (2058, '2025-04-18', 1, 21, 1, '2025-02-17 16:16:28', NULL, NULL, NULL, 20, 20, 22, 15, 9);
INSERT INTO `schedules` VALUES (2059, '2025-04-22', 1, 21, 1, '2025-02-17 16:16:28', NULL, NULL, NULL, 21, 21, 22, 15, 9);
INSERT INTO `schedules` VALUES (2060, '2025-04-25', 1, 21, 1, '2025-02-17 16:16:29', NULL, NULL, NULL, 22, 22, 22, 15, 9);
INSERT INTO `schedules` VALUES (2061, '2025-04-29', 1, 21, 1, '2025-02-17 16:16:29', NULL, NULL, NULL, 23, 23, 22, 15, 9);
INSERT INTO `schedules` VALUES (2062, '2025-05-02', 1, 21, 1, '2025-02-17 16:16:29', NULL, NULL, NULL, 24, 24, 22, 15, 9);
INSERT INTO `schedules` VALUES (2063, '2025-05-06', 1, 21, 1, '2025-02-17 16:16:29', NULL, NULL, NULL, 25, 25, 22, 15, 9);
INSERT INTO `schedules` VALUES (2064, '2025-05-09', 1, 21, 1, '2025-02-17 16:16:29', NULL, NULL, NULL, 26, 26, 22, 15, 9);
INSERT INTO `schedules` VALUES (2065, '2025-05-13', 1, 21, 1, '2025-02-17 16:16:29', NULL, NULL, NULL, 27, 27, 22, 15, 9);
INSERT INTO `schedules` VALUES (2066, '2025-05-16', 1, 21, 1, '2025-02-17 16:16:29', NULL, NULL, NULL, 28, 28, 22, 15, 9);
INSERT INTO `schedules` VALUES (2067, '2025-05-20', 1, 21, 1, '2025-02-17 16:16:29', NULL, NULL, NULL, 29, 29, 22, 15, 9);
INSERT INTO `schedules` VALUES (2068, '2025-05-23', 1, 21, 1, '2025-02-17 16:16:29', NULL, NULL, NULL, 30, 30, 22, 15, 9);
INSERT INTO `schedules` VALUES (2069, '2025-05-27', 1, 21, 1, '2025-02-17 16:16:29', NULL, NULL, NULL, 31, 31, 22, 15, 9);
INSERT INTO `schedules` VALUES (2070, '2025-05-30', 1, 21, 1, '2025-02-17 16:16:29', NULL, NULL, NULL, 32, 32, 22, 15, 9);
INSERT INTO `schedules` VALUES (2071, '2025-06-03', 1, 21, 1, '2025-02-17 16:16:29', NULL, NULL, NULL, 33, 33, 22, 15, 9);
INSERT INTO `schedules` VALUES (2072, '2025-06-06', 1, 21, 1, '2025-02-17 16:16:29', NULL, NULL, NULL, 34, 34, 22, 15, 9);
INSERT INTO `schedules` VALUES (2073, '2025-06-10', 1, 21, 1, '2025-02-17 16:16:29', NULL, NULL, NULL, 35, 35, 22, 15, 9);
INSERT INTO `schedules` VALUES (2074, '2025-06-13', 1, 21, 1, '2025-02-17 16:16:29', NULL, NULL, NULL, 36, 36, 22, 15, 9);
INSERT INTO `schedules` VALUES (2075, '2025-06-17', 1, 21, 1, '2025-02-17 16:16:29', NULL, NULL, NULL, 37, 37, 22, 15, 9);
INSERT INTO `schedules` VALUES (2076, '2025-06-20', 1, 21, 1, '2025-02-17 16:16:29', NULL, NULL, NULL, 38, 38, 22, 15, 9);
INSERT INTO `schedules` VALUES (2077, '2025-06-24', 1, 21, 1, '2025-02-17 16:16:29', NULL, NULL, NULL, 39, 39, 22, 15, 9);
INSERT INTO `schedules` VALUES (2078, '2025-06-27', 1, 21, 1, '2025-02-17 16:16:29', NULL, NULL, NULL, 40, 40, 22, 15, 9);
INSERT INTO `schedules` VALUES (2079, '2025-07-01', 1, 21, 1, '2025-02-17 16:16:29', NULL, NULL, NULL, 41, 41, 22, 15, 9);
INSERT INTO `schedules` VALUES (2080, '2025-07-04', 1, 21, 1, '2025-02-17 16:16:29', NULL, NULL, NULL, 42, 42, 22, 15, 9);
INSERT INTO `schedules` VALUES (2081, '2025-07-08', 1, 21, 1, '2025-02-17 16:16:29', NULL, NULL, NULL, 43, 43, 22, 15, 9);
INSERT INTO `schedules` VALUES (2082, '2025-07-11', 1, 21, 1, '2025-02-17 16:16:29', NULL, NULL, NULL, 44, 44, 22, 15, 9);
INSERT INTO `schedules` VALUES (2083, '2025-07-15', 1, 21, 1, '2025-02-17 16:16:29', NULL, NULL, NULL, 45, 45, 22, 15, 9);
INSERT INTO `schedules` VALUES (2084, '2025-07-18', 1, 21, 1, '2025-02-17 16:16:29', NULL, NULL, NULL, 46, 46, 22, 15, 9);
INSERT INTO `schedules` VALUES (2085, '2025-07-22', 1, 21, 1, '2025-02-17 16:16:29', NULL, NULL, NULL, 47, 47, 22, 15, 9);
INSERT INTO `schedules` VALUES (2086, '2025-07-25', 1, 21, 1, '2025-02-17 16:16:29', NULL, NULL, NULL, 48, 48, 22, 15, 9);
INSERT INTO `schedules` VALUES (2087, '2025-07-29', 1, 21, 1, '2025-02-17 16:16:29', NULL, NULL, NULL, 49, 49, 22, 15, 9);
INSERT INTO `schedules` VALUES (2088, '2025-08-01', 1, 21, 1, '2025-02-17 16:16:29', NULL, NULL, NULL, 50, 50, 22, 15, 9);
INSERT INTO `schedules` VALUES (2089, '2025-08-05', 1, 21, 1, '2025-02-17 16:16:29', NULL, NULL, NULL, 51, 51, 22, 15, 9);
INSERT INTO `schedules` VALUES (2090, '2025-08-08', 1, 21, 1, '2025-02-17 16:16:29', NULL, NULL, NULL, 52, 52, 22, 15, 9);
INSERT INTO `schedules` VALUES (2091, '2025-08-12', 1, 21, 1, '2025-02-17 16:16:29', NULL, NULL, NULL, 53, 53, 22, 15, 9);
INSERT INTO `schedules` VALUES (2092, '2025-08-15', 1, 21, 1, '2025-02-17 16:16:29', NULL, NULL, NULL, 54, 54, 22, 15, 9);
INSERT INTO `schedules` VALUES (2093, '2025-08-19', 1, 21, 1, '2025-02-17 16:16:29', NULL, NULL, NULL, 55, 55, 22, 15, 9);
INSERT INTO `schedules` VALUES (2094, '2025-08-22', 1, 21, 1, '2025-02-17 16:16:29', NULL, NULL, NULL, 56, 56, 22, 15, 9);
INSERT INTO `schedules` VALUES (2095, '2025-08-26', 1, 21, 1, '2025-02-17 16:16:29', NULL, NULL, NULL, 57, 57, 22, 15, 9);
INSERT INTO `schedules` VALUES (2096, '2025-08-29', 1, 21, 1, '2025-02-17 16:16:29', NULL, NULL, NULL, 58, 58, 22, 15, 9);
INSERT INTO `schedules` VALUES (2097, '2025-09-02', 1, 21, 1, '2025-02-17 16:16:29', NULL, NULL, NULL, 59, 59, 22, 15, 9);
INSERT INTO `schedules` VALUES (2098, '2025-09-05', 1, 21, 1, '2025-02-17 16:16:29', NULL, NULL, NULL, 60, 60, 22, 15, 9);
INSERT INTO `schedules` VALUES (2099, '2025-09-09', 1, 21, 1, '2025-02-17 16:16:29', NULL, NULL, NULL, 61, 61, 22, 15, 9);
INSERT INTO `schedules` VALUES (2100, '2025-09-12', 1, 21, 1, '2025-02-17 16:16:29', NULL, NULL, NULL, 62, 62, 22, 15, 9);
INSERT INTO `schedules` VALUES (2101, '2025-09-16', 1, 21, 1, '2025-02-17 16:16:29', NULL, NULL, NULL, 63, 63, 22, 15, 9);
INSERT INTO `schedules` VALUES (2102, '2025-09-19', 1, 21, 1, '2025-02-17 16:16:29', NULL, NULL, NULL, 64, 64, 22, 15, 9);
INSERT INTO `schedules` VALUES (2103, '2025-09-23', 1, 21, 1, '2025-02-17 16:16:29', NULL, NULL, NULL, 65, 65, 22, 15, 9);
INSERT INTO `schedules` VALUES (2104, '2025-09-26', 1, 21, 1, '2025-02-17 16:16:29', NULL, NULL, NULL, 66, 66, 22, 15, 9);
INSERT INTO `schedules` VALUES (2105, '2025-09-30', 1, 21, 1, '2025-02-17 16:16:29', NULL, NULL, NULL, 67, 67, 22, 15, 9);
INSERT INTO `schedules` VALUES (2106, '2025-10-03', 1, 21, 1, '2025-02-17 16:16:29', NULL, NULL, NULL, 68, 68, 22, 15, 9);
INSERT INTO `schedules` VALUES (2107, '2025-10-07', 1, 21, 1, '2025-02-17 16:16:29', NULL, NULL, NULL, 69, 69, 22, 15, 9);
INSERT INTO `schedules` VALUES (2108, '2025-10-10', 1, 21, 1, '2025-02-17 16:16:29', NULL, NULL, NULL, 70, 70, 22, 15, 9);
INSERT INTO `schedules` VALUES (2109, '2025-10-14', 1, 21, 1, '2025-02-17 16:16:29', NULL, NULL, NULL, 71, 71, 22, 15, 9);
INSERT INTO `schedules` VALUES (2110, '2025-10-17', 1, 21, 1, '2025-02-17 16:16:29', NULL, NULL, NULL, 72, 72, 22, 15, 9);
INSERT INTO `schedules` VALUES (2111, '2025-10-21', 1, 21, 1, '2025-02-17 16:16:29', NULL, NULL, NULL, 73, 73, 22, 15, 9);
INSERT INTO `schedules` VALUES (2112, '2025-10-24', 1, 21, 1, '2025-02-17 16:16:29', NULL, NULL, NULL, 74, 74, 22, 15, 9);
INSERT INTO `schedules` VALUES (2113, '2025-10-28', 1, 21, 1, '2025-02-17 16:16:29', NULL, NULL, NULL, 75, 75, 22, 15, 9);
INSERT INTO `schedules` VALUES (2114, '2025-10-31', 1, 21, 1, '2025-02-17 16:16:29', NULL, NULL, NULL, 76, 76, 22, 15, 9);
INSERT INTO `schedules` VALUES (2115, '2025-11-04', 1, 21, 1, '2025-02-17 16:16:29', NULL, NULL, NULL, 77, 77, 22, 15, 9);
INSERT INTO `schedules` VALUES (2116, '2025-11-07', 1, 21, 1, '2025-02-17 16:16:29', NULL, NULL, NULL, 78, 78, 22, 15, 9);
INSERT INTO `schedules` VALUES (2117, '2025-11-11', 1, 21, 1, '2025-02-17 16:16:29', NULL, NULL, NULL, 79, 79, 22, 15, 9);
INSERT INTO `schedules` VALUES (2118, '2025-11-14', 1, 21, 1, '2025-02-17 16:16:29', NULL, NULL, NULL, 80, 80, 22, 15, 9);
INSERT INTO `schedules` VALUES (2119, '2025-11-18', 1, 21, 1, '2025-02-17 16:16:29', NULL, NULL, NULL, 81, 81, 22, 15, 9);
INSERT INTO `schedules` VALUES (2120, '2025-11-21', 1, 21, 1, '2025-02-17 16:16:29', NULL, NULL, NULL, 82, 82, 22, 15, 9);
INSERT INTO `schedules` VALUES (2121, '2025-11-25', 1, 21, 1, '2025-02-17 16:16:29', NULL, NULL, NULL, 83, 83, 22, 15, 9);
INSERT INTO `schedules` VALUES (2122, '2025-11-28', 1, 21, 1, '2025-02-17 16:16:29', NULL, NULL, NULL, 84, 84, 22, 15, 9);
INSERT INTO `schedules` VALUES (2123, '2025-12-02', 1, 21, 1, '2025-02-17 16:16:29', NULL, NULL, NULL, 85, 85, 22, 15, 9);
INSERT INTO `schedules` VALUES (2124, '2025-12-05', 1, 21, 1, '2025-02-17 16:16:29', NULL, NULL, NULL, 86, 86, 22, 15, 9);
INSERT INTO `schedules` VALUES (2125, '2025-12-09', 1, 21, 1, '2025-02-17 16:16:29', NULL, NULL, NULL, 87, 87, 22, 15, 9);
INSERT INTO `schedules` VALUES (2126, '2025-12-12', 1, 21, 1, '2025-02-17 16:16:29', NULL, NULL, NULL, 88, 88, 22, 15, 9);
INSERT INTO `schedules` VALUES (2127, '2025-12-16', 1, 21, 1, '2025-02-17 16:16:29', NULL, NULL, NULL, 89, 89, 22, 15, 9);
INSERT INTO `schedules` VALUES (2128, '2025-12-19', 1, 21, 1, '2025-02-17 16:16:29', NULL, NULL, NULL, 90, 90, 22, 15, 9);
INSERT INTO `schedules` VALUES (2129, '2025-12-23', 1, 21, 1, '2025-02-17 16:16:29', NULL, NULL, NULL, 91, 91, 22, 15, 9);
INSERT INTO `schedules` VALUES (2130, '2025-12-26', 1, 21, 1, '2025-02-17 16:16:29', NULL, NULL, NULL, 92, 92, 22, 15, 9);
INSERT INTO `schedules` VALUES (2131, '2025-12-30', 1, 21, 1, '2025-02-17 16:16:29', NULL, NULL, NULL, 93, 93, 22, 15, 9);
INSERT INTO `schedules` VALUES (2132, '2026-01-02', 1, 21, 1, '2025-02-17 16:16:29', NULL, NULL, NULL, 94, 94, 22, 15, 9);
INSERT INTO `schedules` VALUES (2133, '2026-01-06', 1, 21, 1, '2025-02-17 16:16:29', NULL, NULL, NULL, 95, 95, 22, 15, 9);
INSERT INTO `schedules` VALUES (2134, '2026-01-09', 1, 21, 1, '2025-02-17 16:16:29', NULL, NULL, NULL, 96, 96, 22, 15, 9);
INSERT INTO `schedules` VALUES (2135, '2025-02-11', 1, 22, 1, '2025-02-17 16:17:55', NULL, NULL, NULL, 1, 1, 21, 15, 9);
INSERT INTO `schedules` VALUES (2136, '2025-02-14', 1, 22, 1, '2025-02-17 16:17:55', NULL, NULL, NULL, 2, 2, 21, 15, 9);
INSERT INTO `schedules` VALUES (2137, '2025-02-18', 1, 22, 1, '2025-02-17 16:17:55', NULL, NULL, NULL, 3, 3, 21, 15, 9);
INSERT INTO `schedules` VALUES (2138, '2025-02-21', 1, 22, 1, '2025-02-17 16:17:55', NULL, NULL, NULL, 4, 4, 21, 15, 9);
INSERT INTO `schedules` VALUES (2139, '2025-02-25', 1, 22, 1, '2025-02-17 16:17:55', NULL, NULL, NULL, 5, 5, 21, 15, 9);
INSERT INTO `schedules` VALUES (2140, '2025-02-28', 1, 22, 1, '2025-02-17 16:17:55', NULL, NULL, NULL, 6, 6, 21, 15, 9);
INSERT INTO `schedules` VALUES (2141, '2025-03-04', 1, 22, 1, '2025-02-17 16:17:55', NULL, NULL, NULL, 7, 7, 21, 15, 9);
INSERT INTO `schedules` VALUES (2142, '2025-03-07', 1, 22, 1, '2025-02-17 16:17:55', NULL, NULL, NULL, 8, 8, 21, 15, 9);
INSERT INTO `schedules` VALUES (2143, '2025-03-11', 1, 22, 1, '2025-02-17 16:17:55', NULL, NULL, NULL, 9, 9, 21, 15, 9);
INSERT INTO `schedules` VALUES (2144, '2025-03-14', 1, 22, 1, '2025-02-17 16:17:55', NULL, NULL, NULL, 10, 10, 21, 15, 9);
INSERT INTO `schedules` VALUES (2145, '2025-03-18', 1, 22, 1, '2025-02-17 16:17:55', NULL, NULL, NULL, 11, 11, 21, 15, 9);
INSERT INTO `schedules` VALUES (2146, '2025-03-21', 1, 22, 1, '2025-02-17 16:17:55', NULL, NULL, NULL, 12, 12, 21, 15, 9);
INSERT INTO `schedules` VALUES (2147, '2025-03-25', 1, 22, 1, '2025-02-17 16:17:55', NULL, NULL, NULL, 13, 13, 21, 15, 9);
INSERT INTO `schedules` VALUES (2148, '2025-03-28', 1, 22, 1, '2025-02-17 16:17:55', NULL, NULL, NULL, 14, 14, 21, 15, 9);
INSERT INTO `schedules` VALUES (2149, '2025-04-01', 1, 22, 1, '2025-02-17 16:17:55', NULL, NULL, NULL, 15, 15, 21, 15, 9);
INSERT INTO `schedules` VALUES (2150, '2025-04-04', 1, 22, 1, '2025-02-17 16:17:55', NULL, NULL, NULL, 16, 16, 21, 15, 9);
INSERT INTO `schedules` VALUES (2151, '2025-04-08', 1, 22, 1, '2025-02-17 16:17:55', NULL, NULL, NULL, 17, 17, 21, 15, 9);
INSERT INTO `schedules` VALUES (2152, '2025-04-11', 1, 22, 1, '2025-02-17 16:17:55', NULL, NULL, NULL, 18, 18, 21, 15, 9);
INSERT INTO `schedules` VALUES (2153, '2025-04-15', 1, 22, 1, '2025-02-17 16:17:55', NULL, NULL, NULL, 19, 19, 21, 15, 9);
INSERT INTO `schedules` VALUES (2154, '2025-04-18', 1, 22, 1, '2025-02-17 16:17:55', NULL, NULL, NULL, 20, 20, 21, 15, 9);
INSERT INTO `schedules` VALUES (2155, '2025-04-22', 1, 22, 1, '2025-02-17 16:17:55', NULL, NULL, NULL, 21, 21, 21, 15, 9);
INSERT INTO `schedules` VALUES (2156, '2025-04-25', 1, 22, 1, '2025-02-17 16:17:55', NULL, NULL, NULL, 22, 22, 21, 15, 9);
INSERT INTO `schedules` VALUES (2157, '2025-04-29', 1, 22, 1, '2025-02-17 16:17:55', NULL, NULL, NULL, 23, 23, 21, 15, 9);
INSERT INTO `schedules` VALUES (2158, '2025-05-02', 1, 22, 1, '2025-02-17 16:17:55', NULL, NULL, NULL, 24, 24, 21, 15, 9);
INSERT INTO `schedules` VALUES (2159, '2025-05-06', 1, 22, 1, '2025-02-17 16:17:55', NULL, NULL, NULL, 25, 25, 21, 15, 9);
INSERT INTO `schedules` VALUES (2160, '2025-05-09', 1, 22, 1, '2025-02-17 16:17:55', NULL, NULL, NULL, 26, 26, 21, 15, 9);
INSERT INTO `schedules` VALUES (2161, '2025-05-13', 1, 22, 1, '2025-02-17 16:17:55', NULL, NULL, NULL, 27, 27, 21, 15, 9);
INSERT INTO `schedules` VALUES (2162, '2025-05-16', 1, 22, 1, '2025-02-17 16:17:55', NULL, NULL, NULL, 28, 28, 21, 15, 9);
INSERT INTO `schedules` VALUES (2163, '2025-05-20', 1, 22, 1, '2025-02-17 16:17:55', NULL, NULL, NULL, 29, 29, 21, 15, 9);
INSERT INTO `schedules` VALUES (2164, '2025-05-23', 1, 22, 1, '2025-02-17 16:17:55', NULL, NULL, NULL, 30, 30, 21, 15, 9);
INSERT INTO `schedules` VALUES (2165, '2025-05-27', 1, 22, 1, '2025-02-17 16:17:55', NULL, NULL, NULL, 31, 31, 21, 15, 9);
INSERT INTO `schedules` VALUES (2166, '2025-05-30', 1, 22, 1, '2025-02-17 16:17:55', NULL, NULL, NULL, 32, 32, 21, 15, 9);
INSERT INTO `schedules` VALUES (2167, '2025-06-03', 1, 22, 1, '2025-02-17 16:17:55', NULL, NULL, NULL, 33, 33, 21, 15, 9);
INSERT INTO `schedules` VALUES (2168, '2025-06-06', 1, 22, 1, '2025-02-17 16:17:55', NULL, NULL, NULL, 34, 34, 21, 15, 9);
INSERT INTO `schedules` VALUES (2169, '2025-06-10', 1, 22, 1, '2025-02-17 16:17:55', NULL, NULL, NULL, 35, 35, 21, 15, 9);
INSERT INTO `schedules` VALUES (2170, '2025-06-13', 1, 22, 1, '2025-02-17 16:17:55', NULL, NULL, NULL, 36, 36, 21, 15, 9);
INSERT INTO `schedules` VALUES (2171, '2025-06-17', 1, 22, 1, '2025-02-17 16:17:55', NULL, NULL, NULL, 37, 37, 21, 15, 9);
INSERT INTO `schedules` VALUES (2172, '2025-06-20', 1, 22, 1, '2025-02-17 16:17:55', NULL, NULL, NULL, 38, 38, 21, 15, 9);
INSERT INTO `schedules` VALUES (2173, '2025-06-24', 1, 22, 1, '2025-02-17 16:17:55', NULL, NULL, NULL, 39, 39, 21, 15, 9);
INSERT INTO `schedules` VALUES (2174, '2025-06-27', 1, 22, 1, '2025-02-17 16:17:55', NULL, NULL, NULL, 40, 40, 21, 15, 9);
INSERT INTO `schedules` VALUES (2175, '2025-07-01', 1, 22, 1, '2025-02-17 16:17:55', NULL, NULL, NULL, 41, 41, 21, 15, 9);
INSERT INTO `schedules` VALUES (2176, '2025-07-04', 1, 22, 1, '2025-02-17 16:17:55', NULL, NULL, NULL, 42, 42, 21, 15, 9);
INSERT INTO `schedules` VALUES (2177, '2025-07-08', 1, 22, 1, '2025-02-17 16:17:55', NULL, NULL, NULL, 43, 43, 21, 15, 9);
INSERT INTO `schedules` VALUES (2178, '2025-07-11', 1, 22, 1, '2025-02-17 16:17:55', NULL, NULL, NULL, 44, 44, 21, 15, 9);
INSERT INTO `schedules` VALUES (2179, '2025-07-15', 1, 22, 1, '2025-02-17 16:17:55', NULL, NULL, NULL, 45, 45, 21, 15, 9);
INSERT INTO `schedules` VALUES (2180, '2025-07-18', 1, 22, 1, '2025-02-17 16:17:55', NULL, NULL, NULL, 46, 46, 21, 15, 9);
INSERT INTO `schedules` VALUES (2181, '2025-07-22', 1, 22, 1, '2025-02-17 16:17:55', NULL, NULL, NULL, 47, 47, 21, 15, 9);
INSERT INTO `schedules` VALUES (2182, '2025-07-25', 1, 22, 1, '2025-02-17 16:17:55', NULL, NULL, NULL, 48, 48, 21, 15, 9);
INSERT INTO `schedules` VALUES (2183, '2025-07-29', 1, 22, 1, '2025-02-17 16:17:55', NULL, NULL, NULL, 49, 49, 21, 15, 9);
INSERT INTO `schedules` VALUES (2184, '2025-08-01', 1, 22, 1, '2025-02-17 16:17:55', NULL, NULL, NULL, 50, 50, 21, 15, 9);
INSERT INTO `schedules` VALUES (2185, '2025-08-05', 1, 22, 1, '2025-02-17 16:17:55', NULL, NULL, NULL, 51, 51, 21, 15, 9);
INSERT INTO `schedules` VALUES (2186, '2025-08-08', 1, 22, 1, '2025-02-17 16:17:55', NULL, NULL, NULL, 52, 52, 21, 15, 9);
INSERT INTO `schedules` VALUES (2187, '2025-08-12', 1, 22, 1, '2025-02-17 16:17:55', NULL, NULL, NULL, 53, 53, 21, 15, 9);
INSERT INTO `schedules` VALUES (2188, '2025-08-15', 1, 22, 1, '2025-02-17 16:17:55', NULL, NULL, NULL, 54, 54, 21, 15, 9);
INSERT INTO `schedules` VALUES (2189, '2025-08-19', 1, 22, 1, '2025-02-17 16:17:55', NULL, NULL, NULL, 55, 55, 21, 15, 9);
INSERT INTO `schedules` VALUES (2190, '2025-08-22', 1, 22, 1, '2025-02-17 16:17:55', NULL, NULL, NULL, 56, 56, 21, 15, 9);
INSERT INTO `schedules` VALUES (2191, '2025-08-26', 1, 22, 1, '2025-02-17 16:17:55', NULL, NULL, NULL, 57, 57, 21, 15, 9);
INSERT INTO `schedules` VALUES (2192, '2025-08-29', 1, 22, 1, '2025-02-17 16:17:55', NULL, NULL, NULL, 58, 58, 21, 15, 9);
INSERT INTO `schedules` VALUES (2193, '2025-09-02', 1, 22, 1, '2025-02-17 16:17:55', NULL, NULL, NULL, 59, 59, 21, 15, 9);
INSERT INTO `schedules` VALUES (2194, '2025-09-05', 1, 22, 1, '2025-02-17 16:17:55', NULL, NULL, NULL, 60, 60, 21, 15, 9);
INSERT INTO `schedules` VALUES (2195, '2025-09-09', 1, 22, 1, '2025-02-17 16:17:55', NULL, NULL, NULL, 61, 61, 21, 15, 9);
INSERT INTO `schedules` VALUES (2196, '2025-09-12', 1, 22, 1, '2025-02-17 16:17:55', NULL, NULL, NULL, 62, 62, 21, 15, 9);
INSERT INTO `schedules` VALUES (2197, '2025-09-16', 1, 22, 1, '2025-02-17 16:17:55', NULL, NULL, NULL, 63, 63, 21, 15, 9);
INSERT INTO `schedules` VALUES (2198, '2025-09-19', 1, 22, 1, '2025-02-17 16:17:55', NULL, NULL, NULL, 64, 64, 21, 15, 9);
INSERT INTO `schedules` VALUES (2199, '2025-09-23', 1, 22, 1, '2025-02-17 16:17:55', NULL, NULL, NULL, 65, 65, 21, 15, 9);
INSERT INTO `schedules` VALUES (2200, '2025-09-26', 1, 22, 1, '2025-02-17 16:17:55', NULL, NULL, NULL, 66, 66, 21, 15, 9);
INSERT INTO `schedules` VALUES (2201, '2025-09-30', 1, 22, 1, '2025-02-17 16:17:55', NULL, NULL, NULL, 67, 67, 21, 15, 9);
INSERT INTO `schedules` VALUES (2202, '2025-10-03', 1, 22, 1, '2025-02-17 16:17:55', NULL, NULL, NULL, 68, 68, 21, 15, 9);
INSERT INTO `schedules` VALUES (2203, '2025-10-07', 1, 22, 1, '2025-02-17 16:17:55', NULL, NULL, NULL, 69, 69, 21, 15, 9);
INSERT INTO `schedules` VALUES (2204, '2025-10-10', 1, 22, 1, '2025-02-17 16:17:55', NULL, NULL, NULL, 70, 70, 21, 15, 9);
INSERT INTO `schedules` VALUES (2205, '2025-10-14', 1, 22, 1, '2025-02-17 16:17:55', NULL, NULL, NULL, 71, 71, 21, 15, 9);
INSERT INTO `schedules` VALUES (2206, '2025-10-17', 1, 22, 1, '2025-02-17 16:17:55', NULL, NULL, NULL, 72, 72, 21, 15, 9);
INSERT INTO `schedules` VALUES (2207, '2025-10-21', 1, 22, 1, '2025-02-17 16:17:55', NULL, NULL, NULL, 73, 73, 21, 15, 9);
INSERT INTO `schedules` VALUES (2208, '2025-10-24', 1, 22, 1, '2025-02-17 16:17:55', NULL, NULL, NULL, 74, 74, 21, 15, 9);
INSERT INTO `schedules` VALUES (2209, '2025-10-28', 1, 22, 1, '2025-02-17 16:17:55', NULL, NULL, NULL, 75, 75, 21, 15, 9);
INSERT INTO `schedules` VALUES (2210, '2025-10-31', 1, 22, 1, '2025-02-17 16:17:55', NULL, NULL, NULL, 76, 76, 21, 15, 9);
INSERT INTO `schedules` VALUES (2211, '2025-11-04', 1, 22, 1, '2025-02-17 16:17:55', NULL, NULL, NULL, 77, 77, 21, 15, 9);
INSERT INTO `schedules` VALUES (2212, '2025-11-07', 1, 22, 1, '2025-02-17 16:17:56', NULL, NULL, NULL, 78, 78, 21, 15, 9);
INSERT INTO `schedules` VALUES (2213, '2025-11-11', 1, 22, 1, '2025-02-17 16:17:56', NULL, NULL, NULL, 79, 79, 21, 15, 9);
INSERT INTO `schedules` VALUES (2214, '2025-11-14', 1, 22, 1, '2025-02-17 16:17:56', NULL, NULL, NULL, 80, 80, 21, 15, 9);
INSERT INTO `schedules` VALUES (2215, '2025-11-18', 1, 22, 1, '2025-02-17 16:17:56', NULL, NULL, NULL, 81, 81, 21, 15, 9);
INSERT INTO `schedules` VALUES (2216, '2025-11-21', 1, 22, 1, '2025-02-17 16:17:56', NULL, NULL, NULL, 82, 82, 21, 15, 9);
INSERT INTO `schedules` VALUES (2217, '2025-11-25', 1, 22, 1, '2025-02-17 16:17:56', NULL, NULL, NULL, 83, 83, 21, 15, 9);
INSERT INTO `schedules` VALUES (2218, '2025-11-28', 1, 22, 1, '2025-02-17 16:17:56', NULL, NULL, NULL, 84, 84, 21, 15, 9);
INSERT INTO `schedules` VALUES (2219, '2025-12-02', 1, 22, 1, '2025-02-17 16:17:56', NULL, NULL, NULL, 85, 85, 21, 15, 9);
INSERT INTO `schedules` VALUES (2220, '2025-12-05', 1, 22, 1, '2025-02-17 16:17:56', NULL, NULL, NULL, 86, 86, 21, 15, 9);
INSERT INTO `schedules` VALUES (2221, '2025-12-09', 1, 22, 1, '2025-02-17 16:17:56', NULL, NULL, NULL, 87, 87, 21, 15, 9);
INSERT INTO `schedules` VALUES (2222, '2025-12-12', 1, 22, 1, '2025-02-17 16:17:56', NULL, NULL, NULL, 88, 88, 21, 15, 9);
INSERT INTO `schedules` VALUES (2223, '2025-12-16', 1, 22, 1, '2025-02-17 16:17:56', NULL, NULL, NULL, 89, 89, 21, 15, 9);
INSERT INTO `schedules` VALUES (2224, '2025-12-19', 1, 22, 1, '2025-02-17 16:17:56', NULL, NULL, NULL, 90, 90, 21, 15, 9);
INSERT INTO `schedules` VALUES (2225, '2025-12-23', 1, 22, 1, '2025-02-17 16:17:56', NULL, NULL, NULL, 91, 91, 21, 15, 9);
INSERT INTO `schedules` VALUES (2226, '2025-12-26', 1, 22, 1, '2025-02-17 16:17:56', NULL, NULL, NULL, 92, 92, 21, 15, 9);
INSERT INTO `schedules` VALUES (2227, '2025-12-30', 1, 22, 1, '2025-02-17 16:17:56', NULL, NULL, NULL, 93, 93, 21, 15, 9);
INSERT INTO `schedules` VALUES (2228, '2026-01-02', 1, 22, 1, '2025-02-17 16:17:56', NULL, NULL, NULL, 94, 94, 21, 15, 9);
INSERT INTO `schedules` VALUES (2229, '2026-01-06', 1, 22, 1, '2025-02-17 16:17:56', NULL, NULL, NULL, 95, 95, 21, 15, 9);
INSERT INTO `schedules` VALUES (2230, '2026-01-09', 1, 22, 1, '2025-02-17 16:17:56', NULL, NULL, NULL, 96, 96, 21, 15, 9);
INSERT INTO `schedules` VALUES (2231, '2025-02-12', 1, 23, 1, '2025-02-17 16:18:45', NULL, NULL, NULL, 1, 1, 21, 15, 9);
INSERT INTO `schedules` VALUES (2232, '2025-02-15', 1, 23, 1, '2025-02-17 16:18:45', NULL, NULL, NULL, 2, 2, 21, 15, 9);
INSERT INTO `schedules` VALUES (2233, '2025-02-19', 1, 23, 1, '2025-02-17 16:18:45', NULL, NULL, NULL, 3, 3, 21, 15, 9);
INSERT INTO `schedules` VALUES (2234, '2025-02-22', 1, 23, 1, '2025-02-17 16:18:45', NULL, NULL, NULL, 4, 4, 21, 15, 9);
INSERT INTO `schedules` VALUES (2235, '2025-02-26', 1, 23, 1, '2025-02-17 16:18:45', NULL, NULL, NULL, 5, 5, 21, 15, 9);
INSERT INTO `schedules` VALUES (2236, '2025-03-01', 1, 23, 1, '2025-02-17 16:18:45', NULL, NULL, NULL, 6, 6, 21, 15, 9);
INSERT INTO `schedules` VALUES (2237, '2025-03-05', 1, 23, 1, '2025-02-17 16:18:45', NULL, NULL, NULL, 7, 7, 21, 15, 9);
INSERT INTO `schedules` VALUES (2238, '2025-03-08', 1, 23, 1, '2025-02-17 16:18:45', NULL, NULL, NULL, 8, 8, 21, 15, 9);
INSERT INTO `schedules` VALUES (2239, '2025-03-12', 1, 23, 1, '2025-02-17 16:18:45', NULL, NULL, NULL, 9, 9, 21, 15, 9);
INSERT INTO `schedules` VALUES (2240, '2025-03-15', 1, 23, 1, '2025-02-17 16:18:45', NULL, NULL, NULL, 10, 10, 21, 15, 9);
INSERT INTO `schedules` VALUES (2241, '2025-03-19', 1, 23, 1, '2025-02-17 16:18:45', NULL, NULL, NULL, 11, 11, 21, 15, 9);
INSERT INTO `schedules` VALUES (2242, '2025-03-22', 1, 23, 1, '2025-02-17 16:18:45', NULL, NULL, NULL, 12, 12, 21, 15, 9);
INSERT INTO `schedules` VALUES (2243, '2025-03-26', 1, 23, 1, '2025-02-17 16:18:45', NULL, NULL, NULL, 13, 13, 21, 15, 9);
INSERT INTO `schedules` VALUES (2244, '2025-03-29', 1, 23, 1, '2025-02-17 16:18:45', NULL, NULL, NULL, 14, 14, 21, 15, 9);
INSERT INTO `schedules` VALUES (2245, '2025-04-02', 1, 23, 1, '2025-02-17 16:18:45', NULL, NULL, NULL, 15, 15, 21, 15, 9);
INSERT INTO `schedules` VALUES (2246, '2025-04-05', 1, 23, 1, '2025-02-17 16:18:45', NULL, NULL, NULL, 16, 16, 21, 15, 9);
INSERT INTO `schedules` VALUES (2247, '2025-04-09', 1, 23, 1, '2025-02-17 16:18:45', NULL, NULL, NULL, 17, 17, 21, 15, 9);
INSERT INTO `schedules` VALUES (2248, '2025-04-12', 1, 23, 1, '2025-02-17 16:18:45', NULL, NULL, NULL, 18, 18, 21, 15, 9);
INSERT INTO `schedules` VALUES (2249, '2025-04-16', 1, 23, 1, '2025-02-17 16:18:45', NULL, NULL, NULL, 19, 19, 21, 15, 9);
INSERT INTO `schedules` VALUES (2250, '2025-04-19', 1, 23, 1, '2025-02-17 16:18:45', NULL, NULL, NULL, 20, 20, 21, 15, 9);
INSERT INTO `schedules` VALUES (2251, '2025-04-23', 1, 23, 1, '2025-02-17 16:18:45', NULL, NULL, NULL, 21, 21, 21, 15, 9);
INSERT INTO `schedules` VALUES (2252, '2025-04-26', 1, 23, 1, '2025-02-17 16:18:45', NULL, NULL, NULL, 22, 22, 21, 15, 9);
INSERT INTO `schedules` VALUES (2253, '2025-04-30', 1, 23, 1, '2025-02-17 16:18:45', NULL, NULL, NULL, 23, 23, 21, 15, 9);
INSERT INTO `schedules` VALUES (2254, '2025-05-03', 1, 23, 1, '2025-02-17 16:18:45', NULL, NULL, NULL, 24, 24, 21, 15, 9);
INSERT INTO `schedules` VALUES (2255, '2025-05-07', 1, 23, 1, '2025-02-17 16:18:45', NULL, NULL, NULL, 25, 25, 21, 15, 9);
INSERT INTO `schedules` VALUES (2256, '2025-05-10', 1, 23, 1, '2025-02-17 16:18:45', NULL, NULL, NULL, 26, 26, 21, 15, 9);
INSERT INTO `schedules` VALUES (2257, '2025-05-14', 1, 23, 1, '2025-02-17 16:18:45', NULL, NULL, NULL, 27, 27, 21, 15, 9);
INSERT INTO `schedules` VALUES (2258, '2025-05-17', 1, 23, 1, '2025-02-17 16:18:45', NULL, NULL, NULL, 28, 28, 21, 15, 9);
INSERT INTO `schedules` VALUES (2259, '2025-05-21', 1, 23, 1, '2025-02-17 16:18:45', NULL, NULL, NULL, 29, 29, 21, 15, 9);
INSERT INTO `schedules` VALUES (2260, '2025-05-24', 1, 23, 1, '2025-02-17 16:18:45', NULL, NULL, NULL, 30, 30, 21, 15, 9);
INSERT INTO `schedules` VALUES (2261, '2025-05-28', 1, 23, 1, '2025-02-17 16:18:45', NULL, NULL, NULL, 31, 31, 21, 15, 9);
INSERT INTO `schedules` VALUES (2262, '2025-05-31', 1, 23, 1, '2025-02-17 16:18:45', NULL, NULL, NULL, 32, 32, 21, 15, 9);
INSERT INTO `schedules` VALUES (2263, '2025-06-04', 1, 23, 1, '2025-02-17 16:18:45', NULL, NULL, NULL, 33, 33, 21, 15, 9);
INSERT INTO `schedules` VALUES (2264, '2025-06-07', 1, 23, 1, '2025-02-17 16:18:45', NULL, NULL, NULL, 34, 34, 21, 15, 9);
INSERT INTO `schedules` VALUES (2265, '2025-06-11', 1, 23, 1, '2025-02-17 16:18:45', NULL, NULL, NULL, 35, 35, 21, 15, 9);
INSERT INTO `schedules` VALUES (2266, '2025-06-14', 1, 23, 1, '2025-02-17 16:18:45', NULL, NULL, NULL, 36, 36, 21, 15, 9);
INSERT INTO `schedules` VALUES (2267, '2025-06-18', 1, 23, 1, '2025-02-17 16:18:45', NULL, NULL, NULL, 37, 37, 21, 15, 9);
INSERT INTO `schedules` VALUES (2268, '2025-06-21', 1, 23, 1, '2025-02-17 16:18:45', NULL, NULL, NULL, 38, 38, 21, 15, 9);
INSERT INTO `schedules` VALUES (2269, '2025-06-25', 1, 23, 1, '2025-02-17 16:18:45', NULL, NULL, NULL, 39, 39, 21, 15, 9);
INSERT INTO `schedules` VALUES (2270, '2025-06-28', 1, 23, 1, '2025-02-17 16:18:45', NULL, NULL, NULL, 40, 40, 21, 15, 9);
INSERT INTO `schedules` VALUES (2271, '2025-07-02', 1, 23, 1, '2025-02-17 16:18:45', NULL, NULL, NULL, 41, 41, 21, 15, 9);
INSERT INTO `schedules` VALUES (2272, '2025-07-05', 1, 23, 1, '2025-02-17 16:18:45', NULL, NULL, NULL, 42, 42, 21, 15, 9);
INSERT INTO `schedules` VALUES (2273, '2025-07-09', 1, 23, 1, '2025-02-17 16:18:45', NULL, NULL, NULL, 43, 43, 21, 15, 9);
INSERT INTO `schedules` VALUES (2274, '2025-07-12', 1, 23, 1, '2025-02-17 16:18:45', NULL, NULL, NULL, 44, 44, 21, 15, 9);
INSERT INTO `schedules` VALUES (2275, '2025-07-16', 1, 23, 1, '2025-02-17 16:18:45', NULL, NULL, NULL, 45, 45, 21, 15, 9);
INSERT INTO `schedules` VALUES (2276, '2025-07-19', 1, 23, 1, '2025-02-17 16:18:45', NULL, NULL, NULL, 46, 46, 21, 15, 9);
INSERT INTO `schedules` VALUES (2277, '2025-07-23', 1, 23, 1, '2025-02-17 16:18:45', NULL, NULL, NULL, 47, 47, 21, 15, 9);
INSERT INTO `schedules` VALUES (2278, '2025-07-26', 1, 23, 1, '2025-02-17 16:18:45', NULL, NULL, NULL, 48, 48, 21, 15, 9);
INSERT INTO `schedules` VALUES (2279, '2025-07-30', 1, 23, 1, '2025-02-17 16:18:45', NULL, NULL, NULL, 49, 49, 21, 15, 9);
INSERT INTO `schedules` VALUES (2280, '2025-08-02', 1, 23, 1, '2025-02-17 16:18:45', NULL, NULL, NULL, 50, 50, 21, 15, 9);
INSERT INTO `schedules` VALUES (2281, '2025-08-06', 1, 23, 1, '2025-02-17 16:18:45', NULL, NULL, NULL, 51, 51, 21, 15, 9);
INSERT INTO `schedules` VALUES (2282, '2025-08-09', 1, 23, 1, '2025-02-17 16:18:45', NULL, NULL, NULL, 52, 52, 21, 15, 9);
INSERT INTO `schedules` VALUES (2283, '2025-08-13', 1, 23, 1, '2025-02-17 16:18:45', NULL, NULL, NULL, 53, 53, 21, 15, 9);
INSERT INTO `schedules` VALUES (2284, '2025-08-16', 1, 23, 1, '2025-02-17 16:18:45', NULL, NULL, NULL, 54, 54, 21, 15, 9);
INSERT INTO `schedules` VALUES (2285, '2025-08-20', 1, 23, 1, '2025-02-17 16:18:45', NULL, NULL, NULL, 55, 55, 21, 15, 9);
INSERT INTO `schedules` VALUES (2286, '2025-08-23', 1, 23, 1, '2025-02-17 16:18:45', NULL, NULL, NULL, 56, 56, 21, 15, 9);
INSERT INTO `schedules` VALUES (2287, '2025-08-27', 1, 23, 1, '2025-02-17 16:18:45', NULL, NULL, NULL, 57, 57, 21, 15, 9);
INSERT INTO `schedules` VALUES (2288, '2025-08-30', 1, 23, 1, '2025-02-17 16:18:45', NULL, NULL, NULL, 58, 58, 21, 15, 9);
INSERT INTO `schedules` VALUES (2289, '2025-09-03', 1, 23, 1, '2025-02-17 16:18:45', NULL, NULL, NULL, 59, 59, 21, 15, 9);
INSERT INTO `schedules` VALUES (2290, '2025-09-06', 1, 23, 1, '2025-02-17 16:18:45', NULL, NULL, NULL, 60, 60, 21, 15, 9);
INSERT INTO `schedules` VALUES (2291, '2025-09-10', 1, 23, 1, '2025-02-17 16:18:45', NULL, NULL, NULL, 61, 61, 21, 15, 9);
INSERT INTO `schedules` VALUES (2292, '2025-09-13', 1, 23, 1, '2025-02-17 16:18:45', NULL, NULL, NULL, 62, 62, 21, 15, 9);
INSERT INTO `schedules` VALUES (2293, '2025-09-17', 1, 23, 1, '2025-02-17 16:18:45', NULL, NULL, NULL, 63, 63, 21, 15, 9);
INSERT INTO `schedules` VALUES (2294, '2025-09-20', 1, 23, 1, '2025-02-17 16:18:45', NULL, NULL, NULL, 64, 64, 21, 15, 9);
INSERT INTO `schedules` VALUES (2295, '2025-09-24', 1, 23, 1, '2025-02-17 16:18:45', NULL, NULL, NULL, 65, 65, 21, 15, 9);
INSERT INTO `schedules` VALUES (2296, '2025-09-27', 1, 23, 1, '2025-02-17 16:18:45', NULL, NULL, NULL, 66, 66, 21, 15, 9);
INSERT INTO `schedules` VALUES (2297, '2025-10-01', 1, 23, 1, '2025-02-17 16:18:45', NULL, NULL, NULL, 67, 67, 21, 15, 9);
INSERT INTO `schedules` VALUES (2298, '2025-10-04', 1, 23, 1, '2025-02-17 16:18:45', NULL, NULL, NULL, 68, 68, 21, 15, 9);
INSERT INTO `schedules` VALUES (2299, '2025-10-08', 1, 23, 1, '2025-02-17 16:18:45', NULL, NULL, NULL, 69, 69, 21, 15, 9);
INSERT INTO `schedules` VALUES (2300, '2025-10-11', 1, 23, 1, '2025-02-17 16:18:45', NULL, NULL, NULL, 70, 70, 21, 15, 9);
INSERT INTO `schedules` VALUES (2301, '2025-10-15', 1, 23, 1, '2025-02-17 16:18:45', NULL, NULL, NULL, 71, 71, 21, 15, 9);
INSERT INTO `schedules` VALUES (2302, '2025-10-18', 1, 23, 1, '2025-02-17 16:18:45', NULL, NULL, NULL, 72, 72, 21, 15, 9);
INSERT INTO `schedules` VALUES (2303, '2025-10-22', 1, 23, 1, '2025-02-17 16:18:45', NULL, NULL, NULL, 73, 73, 21, 15, 9);
INSERT INTO `schedules` VALUES (2304, '2025-10-25', 1, 23, 1, '2025-02-17 16:18:45', NULL, NULL, NULL, 74, 74, 21, 15, 9);
INSERT INTO `schedules` VALUES (2305, '2025-10-29', 1, 23, 1, '2025-02-17 16:18:45', NULL, NULL, NULL, 75, 75, 21, 15, 9);
INSERT INTO `schedules` VALUES (2306, '2025-11-01', 1, 23, 1, '2025-02-17 16:18:45', NULL, NULL, NULL, 76, 76, 21, 15, 9);
INSERT INTO `schedules` VALUES (2307, '2025-11-05', 1, 23, 1, '2025-02-17 16:18:45', NULL, NULL, NULL, 77, 77, 21, 15, 9);
INSERT INTO `schedules` VALUES (2308, '2025-11-08', 1, 23, 1, '2025-02-17 16:18:45', NULL, NULL, NULL, 78, 78, 21, 15, 9);
INSERT INTO `schedules` VALUES (2309, '2025-11-12', 1, 23, 1, '2025-02-17 16:18:45', NULL, NULL, NULL, 79, 79, 21, 15, 9);
INSERT INTO `schedules` VALUES (2310, '2025-11-15', 1, 23, 1, '2025-02-17 16:18:45', NULL, NULL, NULL, 80, 80, 21, 15, 9);
INSERT INTO `schedules` VALUES (2311, '2025-11-19', 1, 23, 1, '2025-02-17 16:18:45', NULL, NULL, NULL, 81, 81, 21, 15, 9);
INSERT INTO `schedules` VALUES (2312, '2025-11-22', 1, 23, 1, '2025-02-17 16:18:45', NULL, NULL, NULL, 82, 82, 21, 15, 9);
INSERT INTO `schedules` VALUES (2313, '2025-11-26', 1, 23, 1, '2025-02-17 16:18:45', NULL, NULL, NULL, 83, 83, 21, 15, 9);
INSERT INTO `schedules` VALUES (2314, '2025-11-29', 1, 23, 1, '2025-02-17 16:18:45', NULL, NULL, NULL, 84, 84, 21, 15, 9);
INSERT INTO `schedules` VALUES (2315, '2025-12-03', 1, 23, 1, '2025-02-17 16:18:45', NULL, NULL, NULL, 85, 85, 21, 15, 9);
INSERT INTO `schedules` VALUES (2316, '2025-12-06', 1, 23, 1, '2025-02-17 16:18:45', NULL, NULL, NULL, 86, 86, 21, 15, 9);
INSERT INTO `schedules` VALUES (2317, '2025-12-10', 1, 23, 1, '2025-02-17 16:18:45', NULL, NULL, NULL, 87, 87, 21, 15, 9);
INSERT INTO `schedules` VALUES (2318, '2025-12-13', 1, 23, 1, '2025-02-17 16:18:45', NULL, NULL, NULL, 88, 88, 21, 15, 9);
INSERT INTO `schedules` VALUES (2319, '2025-12-17', 1, 23, 1, '2025-02-17 16:18:45', NULL, NULL, NULL, 89, 89, 21, 15, 9);
INSERT INTO `schedules` VALUES (2320, '2025-12-20', 1, 23, 1, '2025-02-17 16:18:45', NULL, NULL, NULL, 90, 90, 21, 15, 9);
INSERT INTO `schedules` VALUES (2321, '2025-12-24', 1, 23, 1, '2025-02-17 16:18:45', NULL, NULL, NULL, 91, 91, 21, 15, 9);
INSERT INTO `schedules` VALUES (2322, '2025-12-27', 1, 23, 1, '2025-02-17 16:18:45', NULL, NULL, NULL, 92, 92, 21, 15, 9);
INSERT INTO `schedules` VALUES (2323, '2025-12-31', 1, 23, 1, '2025-02-17 16:18:45', NULL, NULL, NULL, 93, 93, 21, 15, 9);
INSERT INTO `schedules` VALUES (2324, '2026-01-03', 1, 23, 1, '2025-02-17 16:18:45', NULL, NULL, NULL, 94, 94, 21, 15, 9);
INSERT INTO `schedules` VALUES (2325, '2026-01-07', 1, 23, 1, '2025-02-17 16:18:45', NULL, NULL, NULL, 95, 95, 21, 15, 9);
INSERT INTO `schedules` VALUES (2326, '2026-01-10', 1, 23, 1, '2025-02-17 16:18:45', NULL, NULL, NULL, 96, 96, 21, 15, 9);
INSERT INTO `schedules` VALUES (2327, '2025-02-12', 1, 24, 1, '2025-02-17 16:19:24', NULL, NULL, NULL, 1, 1, 22, 15, 9);
INSERT INTO `schedules` VALUES (2328, '2025-02-15', 1, 24, 1, '2025-02-17 16:19:24', NULL, NULL, NULL, 2, 2, 22, 15, 9);
INSERT INTO `schedules` VALUES (2329, '2025-02-19', 1, 24, 1, '2025-02-17 16:19:24', NULL, NULL, NULL, 3, 3, 22, 15, 9);
INSERT INTO `schedules` VALUES (2330, '2025-02-22', 1, 24, 1, '2025-02-17 16:19:24', NULL, NULL, NULL, 4, 4, 22, 15, 9);
INSERT INTO `schedules` VALUES (2331, '2025-02-26', 1, 24, 1, '2025-02-17 16:19:24', NULL, NULL, NULL, 5, 5, 22, 15, 9);
INSERT INTO `schedules` VALUES (2332, '2025-03-01', 1, 24, 1, '2025-02-17 16:19:24', NULL, NULL, NULL, 6, 6, 22, 15, 9);
INSERT INTO `schedules` VALUES (2333, '2025-03-05', 1, 24, 1, '2025-02-17 16:19:24', NULL, NULL, NULL, 7, 7, 22, 15, 9);
INSERT INTO `schedules` VALUES (2334, '2025-03-08', 1, 24, 1, '2025-02-17 16:19:24', NULL, NULL, NULL, 8, 8, 22, 15, 9);
INSERT INTO `schedules` VALUES (2335, '2025-03-12', 1, 24, 1, '2025-02-17 16:19:24', NULL, NULL, NULL, 9, 9, 22, 15, 9);
INSERT INTO `schedules` VALUES (2336, '2025-03-15', 1, 24, 1, '2025-02-17 16:19:24', NULL, NULL, NULL, 10, 10, 22, 15, 9);
INSERT INTO `schedules` VALUES (2337, '2025-03-19', 1, 24, 1, '2025-02-17 16:19:24', NULL, NULL, NULL, 11, 11, 22, 15, 9);
INSERT INTO `schedules` VALUES (2338, '2025-03-22', 1, 24, 1, '2025-02-17 16:19:24', NULL, NULL, NULL, 12, 12, 22, 15, 9);
INSERT INTO `schedules` VALUES (2339, '2025-03-26', 1, 24, 1, '2025-02-17 16:19:24', NULL, NULL, NULL, 13, 13, 22, 15, 9);
INSERT INTO `schedules` VALUES (2340, '2025-03-29', 1, 24, 1, '2025-02-17 16:19:24', NULL, NULL, NULL, 14, 14, 22, 15, 9);
INSERT INTO `schedules` VALUES (2341, '2025-04-02', 1, 24, 1, '2025-02-17 16:19:24', NULL, NULL, NULL, 15, 15, 22, 15, 9);
INSERT INTO `schedules` VALUES (2342, '2025-04-05', 1, 24, 1, '2025-02-17 16:19:24', NULL, NULL, NULL, 16, 16, 22, 15, 9);
INSERT INTO `schedules` VALUES (2343, '2025-04-09', 1, 24, 1, '2025-02-17 16:19:24', NULL, NULL, NULL, 17, 17, 22, 15, 9);
INSERT INTO `schedules` VALUES (2344, '2025-04-12', 1, 24, 1, '2025-02-17 16:19:24', NULL, NULL, NULL, 18, 18, 22, 15, 9);
INSERT INTO `schedules` VALUES (2345, '2025-04-16', 1, 24, 1, '2025-02-17 16:19:24', NULL, NULL, NULL, 19, 19, 22, 15, 9);
INSERT INTO `schedules` VALUES (2346, '2025-04-19', 1, 24, 1, '2025-02-17 16:19:24', NULL, NULL, NULL, 20, 20, 22, 15, 9);
INSERT INTO `schedules` VALUES (2347, '2025-04-23', 1, 24, 1, '2025-02-17 16:19:24', NULL, NULL, NULL, 21, 21, 22, 15, 9);
INSERT INTO `schedules` VALUES (2348, '2025-04-26', 1, 24, 1, '2025-02-17 16:19:24', NULL, NULL, NULL, 22, 22, 22, 15, 9);
INSERT INTO `schedules` VALUES (2349, '2025-04-30', 1, 24, 1, '2025-02-17 16:19:24', NULL, NULL, NULL, 23, 23, 22, 15, 9);
INSERT INTO `schedules` VALUES (2350, '2025-05-03', 1, 24, 1, '2025-02-17 16:19:24', NULL, NULL, NULL, 24, 24, 22, 15, 9);
INSERT INTO `schedules` VALUES (2351, '2025-05-07', 1, 24, 1, '2025-02-17 16:19:24', NULL, NULL, NULL, 25, 25, 22, 15, 9);
INSERT INTO `schedules` VALUES (2352, '2025-05-10', 1, 24, 1, '2025-02-17 16:19:24', NULL, NULL, NULL, 26, 26, 22, 15, 9);
INSERT INTO `schedules` VALUES (2353, '2025-05-14', 1, 24, 1, '2025-02-17 16:19:24', NULL, NULL, NULL, 27, 27, 22, 15, 9);
INSERT INTO `schedules` VALUES (2354, '2025-05-17', 1, 24, 1, '2025-02-17 16:19:24', NULL, NULL, NULL, 28, 28, 22, 15, 9);
INSERT INTO `schedules` VALUES (2355, '2025-05-21', 1, 24, 1, '2025-02-17 16:19:24', NULL, NULL, NULL, 29, 29, 22, 15, 9);
INSERT INTO `schedules` VALUES (2356, '2025-05-24', 1, 24, 1, '2025-02-17 16:19:24', NULL, NULL, NULL, 30, 30, 22, 15, 9);
INSERT INTO `schedules` VALUES (2357, '2025-05-28', 1, 24, 1, '2025-02-17 16:19:24', NULL, NULL, NULL, 31, 31, 22, 15, 9);
INSERT INTO `schedules` VALUES (2358, '2025-05-31', 1, 24, 1, '2025-02-17 16:19:24', NULL, NULL, NULL, 32, 32, 22, 15, 9);
INSERT INTO `schedules` VALUES (2359, '2025-06-04', 1, 24, 1, '2025-02-17 16:19:24', NULL, NULL, NULL, 33, 33, 22, 15, 9);
INSERT INTO `schedules` VALUES (2360, '2025-06-07', 1, 24, 1, '2025-02-17 16:19:24', NULL, NULL, NULL, 34, 34, 22, 15, 9);
INSERT INTO `schedules` VALUES (2361, '2025-06-11', 1, 24, 1, '2025-02-17 16:19:24', NULL, NULL, NULL, 35, 35, 22, 15, 9);
INSERT INTO `schedules` VALUES (2362, '2025-06-14', 1, 24, 1, '2025-02-17 16:19:24', NULL, NULL, NULL, 36, 36, 22, 15, 9);
INSERT INTO `schedules` VALUES (2363, '2025-06-18', 1, 24, 1, '2025-02-17 16:19:24', NULL, NULL, NULL, 37, 37, 22, 15, 9);
INSERT INTO `schedules` VALUES (2364, '2025-06-21', 1, 24, 1, '2025-02-17 16:19:24', NULL, NULL, NULL, 38, 38, 22, 15, 9);
INSERT INTO `schedules` VALUES (2365, '2025-06-25', 1, 24, 1, '2025-02-17 16:19:24', NULL, NULL, NULL, 39, 39, 22, 15, 9);
INSERT INTO `schedules` VALUES (2366, '2025-06-28', 1, 24, 1, '2025-02-17 16:19:24', NULL, NULL, NULL, 40, 40, 22, 15, 9);
INSERT INTO `schedules` VALUES (2367, '2025-07-02', 1, 24, 1, '2025-02-17 16:19:24', NULL, NULL, NULL, 41, 41, 22, 15, 9);
INSERT INTO `schedules` VALUES (2368, '2025-07-05', 1, 24, 1, '2025-02-17 16:19:24', NULL, NULL, NULL, 42, 42, 22, 15, 9);
INSERT INTO `schedules` VALUES (2369, '2025-07-09', 1, 24, 1, '2025-02-17 16:19:24', NULL, NULL, NULL, 43, 43, 22, 15, 9);
INSERT INTO `schedules` VALUES (2370, '2025-07-12', 1, 24, 1, '2025-02-17 16:19:24', NULL, NULL, NULL, 44, 44, 22, 15, 9);
INSERT INTO `schedules` VALUES (2371, '2025-07-16', 1, 24, 1, '2025-02-17 16:19:24', NULL, NULL, NULL, 45, 45, 22, 15, 9);
INSERT INTO `schedules` VALUES (2372, '2025-07-19', 1, 24, 1, '2025-02-17 16:19:24', NULL, NULL, NULL, 46, 46, 22, 15, 9);
INSERT INTO `schedules` VALUES (2373, '2025-07-23', 1, 24, 1, '2025-02-17 16:19:24', NULL, NULL, NULL, 47, 47, 22, 15, 9);
INSERT INTO `schedules` VALUES (2374, '2025-07-26', 1, 24, 1, '2025-02-17 16:19:24', NULL, NULL, NULL, 48, 48, 22, 15, 9);
INSERT INTO `schedules` VALUES (2375, '2025-07-30', 1, 24, 1, '2025-02-17 16:19:24', NULL, NULL, NULL, 49, 49, 22, 15, 9);
INSERT INTO `schedules` VALUES (2376, '2025-08-02', 1, 24, 1, '2025-02-17 16:19:24', NULL, NULL, NULL, 50, 50, 22, 15, 9);
INSERT INTO `schedules` VALUES (2377, '2025-08-06', 1, 24, 1, '2025-02-17 16:19:24', NULL, NULL, NULL, 51, 51, 22, 15, 9);
INSERT INTO `schedules` VALUES (2378, '2025-08-09', 1, 24, 1, '2025-02-17 16:19:24', NULL, NULL, NULL, 52, 52, 22, 15, 9);
INSERT INTO `schedules` VALUES (2379, '2025-08-13', 1, 24, 1, '2025-02-17 16:19:24', NULL, NULL, NULL, 53, 53, 22, 15, 9);
INSERT INTO `schedules` VALUES (2380, '2025-08-16', 1, 24, 1, '2025-02-17 16:19:24', NULL, NULL, NULL, 54, 54, 22, 15, 9);
INSERT INTO `schedules` VALUES (2381, '2025-08-20', 1, 24, 1, '2025-02-17 16:19:24', NULL, NULL, NULL, 55, 55, 22, 15, 9);
INSERT INTO `schedules` VALUES (2382, '2025-08-23', 1, 24, 1, '2025-02-17 16:19:24', NULL, NULL, NULL, 56, 56, 22, 15, 9);
INSERT INTO `schedules` VALUES (2383, '2025-08-27', 1, 24, 1, '2025-02-17 16:19:24', NULL, NULL, NULL, 57, 57, 22, 15, 9);
INSERT INTO `schedules` VALUES (2384, '2025-08-30', 1, 24, 1, '2025-02-17 16:19:24', NULL, NULL, NULL, 58, 58, 22, 15, 9);
INSERT INTO `schedules` VALUES (2385, '2025-09-03', 1, 24, 1, '2025-02-17 16:19:24', NULL, NULL, NULL, 59, 59, 22, 15, 9);
INSERT INTO `schedules` VALUES (2386, '2025-09-06', 1, 24, 1, '2025-02-17 16:19:24', NULL, NULL, NULL, 60, 60, 22, 15, 9);
INSERT INTO `schedules` VALUES (2387, '2025-09-10', 1, 24, 1, '2025-02-17 16:19:24', NULL, NULL, NULL, 61, 61, 22, 15, 9);
INSERT INTO `schedules` VALUES (2388, '2025-09-13', 1, 24, 1, '2025-02-17 16:19:24', NULL, NULL, NULL, 62, 62, 22, 15, 9);
INSERT INTO `schedules` VALUES (2389, '2025-09-17', 1, 24, 1, '2025-02-17 16:19:24', NULL, NULL, NULL, 63, 63, 22, 15, 9);
INSERT INTO `schedules` VALUES (2390, '2025-09-20', 1, 24, 1, '2025-02-17 16:19:24', NULL, NULL, NULL, 64, 64, 22, 15, 9);
INSERT INTO `schedules` VALUES (2391, '2025-09-24', 1, 24, 1, '2025-02-17 16:19:24', NULL, NULL, NULL, 65, 65, 22, 15, 9);
INSERT INTO `schedules` VALUES (2392, '2025-09-27', 1, 24, 1, '2025-02-17 16:19:24', NULL, NULL, NULL, 66, 66, 22, 15, 9);
INSERT INTO `schedules` VALUES (2393, '2025-10-01', 1, 24, 1, '2025-02-17 16:19:24', NULL, NULL, NULL, 67, 67, 22, 15, 9);
INSERT INTO `schedules` VALUES (2394, '2025-10-04', 1, 24, 1, '2025-02-17 16:19:24', NULL, NULL, NULL, 68, 68, 22, 15, 9);
INSERT INTO `schedules` VALUES (2395, '2025-10-08', 1, 24, 1, '2025-02-17 16:19:24', NULL, NULL, NULL, 69, 69, 22, 15, 9);
INSERT INTO `schedules` VALUES (2396, '2025-10-11', 1, 24, 1, '2025-02-17 16:19:24', NULL, NULL, NULL, 70, 70, 22, 15, 9);
INSERT INTO `schedules` VALUES (2397, '2025-10-15', 1, 24, 1, '2025-02-17 16:19:24', NULL, NULL, NULL, 71, 71, 22, 15, 9);
INSERT INTO `schedules` VALUES (2398, '2025-10-18', 1, 24, 1, '2025-02-17 16:19:24', NULL, NULL, NULL, 72, 72, 22, 15, 9);
INSERT INTO `schedules` VALUES (2399, '2025-10-22', 1, 24, 1, '2025-02-17 16:19:24', NULL, NULL, NULL, 73, 73, 22, 15, 9);
INSERT INTO `schedules` VALUES (2400, '2025-10-25', 1, 24, 1, '2025-02-17 16:19:24', NULL, NULL, NULL, 74, 74, 22, 15, 9);
INSERT INTO `schedules` VALUES (2401, '2025-10-29', 1, 24, 1, '2025-02-17 16:19:24', NULL, NULL, NULL, 75, 75, 22, 15, 9);
INSERT INTO `schedules` VALUES (2402, '2025-11-01', 1, 24, 1, '2025-02-17 16:19:24', NULL, NULL, NULL, 76, 76, 22, 15, 9);
INSERT INTO `schedules` VALUES (2403, '2025-11-05', 1, 24, 1, '2025-02-17 16:19:24', NULL, NULL, NULL, 77, 77, 22, 15, 9);
INSERT INTO `schedules` VALUES (2404, '2025-11-08', 1, 24, 1, '2025-02-17 16:19:24', NULL, NULL, NULL, 78, 78, 22, 15, 9);
INSERT INTO `schedules` VALUES (2405, '2025-11-12', 1, 24, 1, '2025-02-17 16:19:24', NULL, NULL, NULL, 79, 79, 22, 15, 9);
INSERT INTO `schedules` VALUES (2406, '2025-11-15', 1, 24, 1, '2025-02-17 16:19:24', NULL, NULL, NULL, 80, 80, 22, 15, 9);
INSERT INTO `schedules` VALUES (2407, '2025-11-19', 1, 24, 1, '2025-02-17 16:19:24', NULL, NULL, NULL, 81, 81, 22, 15, 9);
INSERT INTO `schedules` VALUES (2408, '2025-11-22', 1, 24, 1, '2025-02-17 16:19:24', NULL, NULL, NULL, 82, 82, 22, 15, 9);
INSERT INTO `schedules` VALUES (2409, '2025-11-26', 1, 24, 1, '2025-02-17 16:19:24', NULL, NULL, NULL, 83, 83, 22, 15, 9);
INSERT INTO `schedules` VALUES (2410, '2025-11-29', 1, 24, 1, '2025-02-17 16:19:24', NULL, NULL, NULL, 84, 84, 22, 15, 9);
INSERT INTO `schedules` VALUES (2411, '2025-12-03', 1, 24, 1, '2025-02-17 16:19:24', NULL, NULL, NULL, 85, 85, 22, 15, 9);
INSERT INTO `schedules` VALUES (2412, '2025-12-06', 1, 24, 1, '2025-02-17 16:19:24', NULL, NULL, NULL, 86, 86, 22, 15, 9);
INSERT INTO `schedules` VALUES (2413, '2025-12-10', 1, 24, 1, '2025-02-17 16:19:24', NULL, NULL, NULL, 87, 87, 22, 15, 9);
INSERT INTO `schedules` VALUES (2414, '2025-12-13', 1, 24, 1, '2025-02-17 16:19:24', NULL, NULL, NULL, 88, 88, 22, 15, 9);
INSERT INTO `schedules` VALUES (2415, '2025-12-17', 1, 24, 1, '2025-02-17 16:19:24', NULL, NULL, NULL, 89, 89, 22, 15, 9);
INSERT INTO `schedules` VALUES (2416, '2025-12-20', 1, 24, 1, '2025-02-17 16:19:24', NULL, NULL, NULL, 90, 90, 22, 15, 9);
INSERT INTO `schedules` VALUES (2417, '2025-12-24', 1, 24, 1, '2025-02-17 16:19:24', NULL, NULL, NULL, 91, 91, 22, 15, 9);
INSERT INTO `schedules` VALUES (2418, '2025-12-27', 1, 24, 1, '2025-02-17 16:19:24', NULL, NULL, NULL, 92, 92, 22, 15, 9);
INSERT INTO `schedules` VALUES (2419, '2025-12-31', 1, 24, 1, '2025-02-17 16:19:24', NULL, NULL, NULL, 93, 93, 22, 15, 9);
INSERT INTO `schedules` VALUES (2420, '2026-01-03', 1, 24, 1, '2025-02-17 16:19:24', NULL, NULL, NULL, 94, 94, 22, 15, 9);
INSERT INTO `schedules` VALUES (2421, '2026-01-07', 1, 24, 1, '2025-02-17 16:19:24', NULL, NULL, NULL, 95, 95, 22, 15, 9);
INSERT INTO `schedules` VALUES (2422, '2026-01-10', 1, 24, 1, '2025-02-17 16:19:24', NULL, NULL, NULL, 96, 96, 22, 15, 9);
INSERT INTO `schedules` VALUES (2423, '2025-02-12', 1, 25, 1, '2025-02-17 16:19:59', NULL, NULL, NULL, 1, 1, 21, 15, 9);
INSERT INTO `schedules` VALUES (2424, '2025-02-15', 1, 25, 1, '2025-02-17 16:19:59', NULL, NULL, NULL, 2, 2, 21, 15, 9);
INSERT INTO `schedules` VALUES (2425, '2025-02-19', 1, 25, 1, '2025-02-17 16:19:59', NULL, NULL, NULL, 3, 3, 21, 15, 9);
INSERT INTO `schedules` VALUES (2426, '2025-02-22', 1, 25, 1, '2025-02-17 16:19:59', NULL, NULL, NULL, 4, 4, 21, 15, 9);
INSERT INTO `schedules` VALUES (2427, '2025-02-26', 1, 25, 1, '2025-02-17 16:19:59', NULL, NULL, NULL, 5, 5, 21, 15, 9);
INSERT INTO `schedules` VALUES (2428, '2025-03-01', 1, 25, 1, '2025-02-17 16:19:59', NULL, NULL, NULL, 6, 6, 21, 15, 9);
INSERT INTO `schedules` VALUES (2429, '2025-03-05', 1, 25, 1, '2025-02-17 16:19:59', NULL, NULL, NULL, 7, 7, 21, 15, 9);
INSERT INTO `schedules` VALUES (2430, '2025-03-08', 1, 25, 1, '2025-02-17 16:19:59', NULL, NULL, NULL, 8, 8, 21, 15, 9);
INSERT INTO `schedules` VALUES (2431, '2025-03-12', 1, 25, 1, '2025-02-17 16:19:59', NULL, NULL, NULL, 9, 9, 21, 15, 9);
INSERT INTO `schedules` VALUES (2432, '2025-03-15', 1, 25, 1, '2025-02-17 16:19:59', NULL, NULL, NULL, 10, 10, 21, 15, 9);
INSERT INTO `schedules` VALUES (2433, '2025-03-19', 1, 25, 1, '2025-02-17 16:19:59', NULL, NULL, NULL, 11, 11, 21, 15, 9);
INSERT INTO `schedules` VALUES (2434, '2025-03-22', 1, 25, 1, '2025-02-17 16:19:59', NULL, NULL, NULL, 12, 12, 21, 15, 9);
INSERT INTO `schedules` VALUES (2435, '2025-03-26', 1, 25, 1, '2025-02-17 16:19:59', NULL, NULL, NULL, 13, 13, 21, 15, 9);
INSERT INTO `schedules` VALUES (2436, '2025-03-29', 1, 25, 1, '2025-02-17 16:19:59', NULL, NULL, NULL, 14, 14, 21, 15, 9);
INSERT INTO `schedules` VALUES (2437, '2025-04-02', 1, 25, 1, '2025-02-17 16:19:59', NULL, NULL, NULL, 15, 15, 21, 15, 9);
INSERT INTO `schedules` VALUES (2438, '2025-04-05', 1, 25, 1, '2025-02-17 16:19:59', NULL, NULL, NULL, 16, 16, 21, 15, 9);
INSERT INTO `schedules` VALUES (2439, '2025-04-09', 1, 25, 1, '2025-02-17 16:19:59', NULL, NULL, NULL, 17, 17, 21, 15, 9);
INSERT INTO `schedules` VALUES (2440, '2025-04-12', 1, 25, 1, '2025-02-17 16:19:59', NULL, NULL, NULL, 18, 18, 21, 15, 9);
INSERT INTO `schedules` VALUES (2441, '2025-04-16', 1, 25, 1, '2025-02-17 16:19:59', NULL, NULL, NULL, 19, 19, 21, 15, 9);
INSERT INTO `schedules` VALUES (2442, '2025-04-19', 1, 25, 1, '2025-02-17 16:19:59', NULL, NULL, NULL, 20, 20, 21, 15, 9);
INSERT INTO `schedules` VALUES (2443, '2025-04-23', 1, 25, 1, '2025-02-17 16:19:59', NULL, NULL, NULL, 21, 21, 21, 15, 9);
INSERT INTO `schedules` VALUES (2444, '2025-04-26', 1, 25, 1, '2025-02-17 16:19:59', NULL, NULL, NULL, 22, 22, 21, 15, 9);
INSERT INTO `schedules` VALUES (2445, '2025-04-30', 1, 25, 1, '2025-02-17 16:19:59', NULL, NULL, NULL, 23, 23, 21, 15, 9);
INSERT INTO `schedules` VALUES (2446, '2025-05-03', 1, 25, 1, '2025-02-17 16:19:59', NULL, NULL, NULL, 24, 24, 21, 15, 9);
INSERT INTO `schedules` VALUES (2447, '2025-05-07', 1, 25, 1, '2025-02-17 16:19:59', NULL, NULL, NULL, 25, 25, 21, 15, 9);
INSERT INTO `schedules` VALUES (2448, '2025-05-10', 1, 25, 1, '2025-02-17 16:19:59', NULL, NULL, NULL, 26, 26, 21, 15, 9);
INSERT INTO `schedules` VALUES (2449, '2025-05-14', 1, 25, 1, '2025-02-17 16:19:59', NULL, NULL, NULL, 27, 27, 21, 15, 9);
INSERT INTO `schedules` VALUES (2450, '2025-05-17', 1, 25, 1, '2025-02-17 16:19:59', NULL, NULL, NULL, 28, 28, 21, 15, 9);
INSERT INTO `schedules` VALUES (2451, '2025-05-21', 1, 25, 1, '2025-02-17 16:19:59', NULL, NULL, NULL, 29, 29, 21, 15, 9);
INSERT INTO `schedules` VALUES (2452, '2025-05-24', 1, 25, 1, '2025-02-17 16:19:59', NULL, NULL, NULL, 30, 30, 21, 15, 9);
INSERT INTO `schedules` VALUES (2453, '2025-05-28', 1, 25, 1, '2025-02-17 16:19:59', NULL, NULL, NULL, 31, 31, 21, 15, 9);
INSERT INTO `schedules` VALUES (2454, '2025-05-31', 1, 25, 1, '2025-02-17 16:19:59', NULL, NULL, NULL, 32, 32, 21, 15, 9);
INSERT INTO `schedules` VALUES (2455, '2025-06-04', 1, 25, 1, '2025-02-17 16:19:59', NULL, NULL, NULL, 33, 33, 21, 15, 9);
INSERT INTO `schedules` VALUES (2456, '2025-06-07', 1, 25, 1, '2025-02-17 16:19:59', NULL, NULL, NULL, 34, 34, 21, 15, 9);
INSERT INTO `schedules` VALUES (2457, '2025-06-11', 1, 25, 1, '2025-02-17 16:19:59', NULL, NULL, NULL, 35, 35, 21, 15, 9);
INSERT INTO `schedules` VALUES (2458, '2025-06-14', 1, 25, 1, '2025-02-17 16:19:59', NULL, NULL, NULL, 36, 36, 21, 15, 9);
INSERT INTO `schedules` VALUES (2459, '2025-06-18', 1, 25, 1, '2025-02-17 16:19:59', NULL, NULL, NULL, 37, 37, 21, 15, 9);
INSERT INTO `schedules` VALUES (2460, '2025-06-21', 1, 25, 1, '2025-02-17 16:19:59', NULL, NULL, NULL, 38, 38, 21, 15, 9);
INSERT INTO `schedules` VALUES (2461, '2025-06-25', 1, 25, 1, '2025-02-17 16:19:59', NULL, NULL, NULL, 39, 39, 21, 15, 9);
INSERT INTO `schedules` VALUES (2462, '2025-06-28', 1, 25, 1, '2025-02-17 16:19:59', NULL, NULL, NULL, 40, 40, 21, 15, 9);
INSERT INTO `schedules` VALUES (2463, '2025-07-02', 1, 25, 1, '2025-02-17 16:19:59', NULL, NULL, NULL, 41, 41, 21, 15, 9);
INSERT INTO `schedules` VALUES (2464, '2025-07-05', 1, 25, 1, '2025-02-17 16:19:59', NULL, NULL, NULL, 42, 42, 21, 15, 9);
INSERT INTO `schedules` VALUES (2465, '2025-07-09', 1, 25, 1, '2025-02-17 16:19:59', NULL, NULL, NULL, 43, 43, 21, 15, 9);
INSERT INTO `schedules` VALUES (2466, '2025-07-12', 1, 25, 1, '2025-02-17 16:19:59', NULL, NULL, NULL, 44, 44, 21, 15, 9);
INSERT INTO `schedules` VALUES (2467, '2025-07-16', 1, 25, 1, '2025-02-17 16:19:59', NULL, NULL, NULL, 45, 45, 21, 15, 9);
INSERT INTO `schedules` VALUES (2468, '2025-07-19', 1, 25, 1, '2025-02-17 16:19:59', NULL, NULL, NULL, 46, 46, 21, 15, 9);
INSERT INTO `schedules` VALUES (2469, '2025-07-23', 1, 25, 1, '2025-02-17 16:19:59', NULL, NULL, NULL, 47, 47, 21, 15, 9);
INSERT INTO `schedules` VALUES (2470, '2025-07-26', 1, 25, 1, '2025-02-17 16:19:59', NULL, NULL, NULL, 48, 48, 21, 15, 9);
INSERT INTO `schedules` VALUES (2471, '2025-07-30', 1, 25, 1, '2025-02-17 16:19:59', NULL, NULL, NULL, 49, 49, 21, 15, 9);
INSERT INTO `schedules` VALUES (2472, '2025-08-02', 1, 25, 1, '2025-02-17 16:19:59', NULL, NULL, NULL, 50, 50, 21, 15, 9);
INSERT INTO `schedules` VALUES (2473, '2025-08-06', 1, 25, 1, '2025-02-17 16:19:59', NULL, NULL, NULL, 51, 51, 21, 15, 9);
INSERT INTO `schedules` VALUES (2474, '2025-08-09', 1, 25, 1, '2025-02-17 16:19:59', NULL, NULL, NULL, 52, 52, 21, 15, 9);
INSERT INTO `schedules` VALUES (2475, '2025-08-13', 1, 25, 1, '2025-02-17 16:19:59', NULL, NULL, NULL, 53, 53, 21, 15, 9);
INSERT INTO `schedules` VALUES (2476, '2025-08-16', 1, 25, 1, '2025-02-17 16:19:59', NULL, NULL, NULL, 54, 54, 21, 15, 9);
INSERT INTO `schedules` VALUES (2477, '2025-08-20', 1, 25, 1, '2025-02-17 16:19:59', NULL, NULL, NULL, 55, 55, 21, 15, 9);
INSERT INTO `schedules` VALUES (2478, '2025-08-23', 1, 25, 1, '2025-02-17 16:19:59', NULL, NULL, NULL, 56, 56, 21, 15, 9);
INSERT INTO `schedules` VALUES (2479, '2025-08-27', 1, 25, 1, '2025-02-17 16:19:59', NULL, NULL, NULL, 57, 57, 21, 15, 9);
INSERT INTO `schedules` VALUES (2480, '2025-08-30', 1, 25, 1, '2025-02-17 16:19:59', NULL, NULL, NULL, 58, 58, 21, 15, 9);
INSERT INTO `schedules` VALUES (2481, '2025-09-03', 1, 25, 1, '2025-02-17 16:19:59', NULL, NULL, NULL, 59, 59, 21, 15, 9);
INSERT INTO `schedules` VALUES (2482, '2025-09-06', 1, 25, 1, '2025-02-17 16:19:59', NULL, NULL, NULL, 60, 60, 21, 15, 9);
INSERT INTO `schedules` VALUES (2483, '2025-09-10', 1, 25, 1, '2025-02-17 16:19:59', NULL, NULL, NULL, 61, 61, 21, 15, 9);
INSERT INTO `schedules` VALUES (2484, '2025-09-13', 1, 25, 1, '2025-02-17 16:19:59', NULL, NULL, NULL, 62, 62, 21, 15, 9);
INSERT INTO `schedules` VALUES (2485, '2025-09-17', 1, 25, 1, '2025-02-17 16:19:59', NULL, NULL, NULL, 63, 63, 21, 15, 9);
INSERT INTO `schedules` VALUES (2486, '2025-09-20', 1, 25, 1, '2025-02-17 16:19:59', NULL, NULL, NULL, 64, 64, 21, 15, 9);
INSERT INTO `schedules` VALUES (2487, '2025-09-24', 1, 25, 1, '2025-02-17 16:19:59', NULL, NULL, NULL, 65, 65, 21, 15, 9);
INSERT INTO `schedules` VALUES (2488, '2025-09-27', 1, 25, 1, '2025-02-17 16:19:59', NULL, NULL, NULL, 66, 66, 21, 15, 9);
INSERT INTO `schedules` VALUES (2489, '2025-10-01', 1, 25, 1, '2025-02-17 16:19:59', NULL, NULL, NULL, 67, 67, 21, 15, 9);
INSERT INTO `schedules` VALUES (2490, '2025-10-04', 1, 25, 1, '2025-02-17 16:19:59', NULL, NULL, NULL, 68, 68, 21, 15, 9);
INSERT INTO `schedules` VALUES (2491, '2025-10-08', 1, 25, 1, '2025-02-17 16:19:59', NULL, NULL, NULL, 69, 69, 21, 15, 9);
INSERT INTO `schedules` VALUES (2492, '2025-10-11', 1, 25, 1, '2025-02-17 16:19:59', NULL, NULL, NULL, 70, 70, 21, 15, 9);
INSERT INTO `schedules` VALUES (2493, '2025-10-15', 1, 25, 1, '2025-02-17 16:19:59', NULL, NULL, NULL, 71, 71, 21, 15, 9);
INSERT INTO `schedules` VALUES (2494, '2025-10-18', 1, 25, 1, '2025-02-17 16:19:59', NULL, NULL, NULL, 72, 72, 21, 15, 9);
INSERT INTO `schedules` VALUES (2495, '2025-10-22', 1, 25, 1, '2025-02-17 16:19:59', NULL, NULL, NULL, 73, 73, 21, 15, 9);
INSERT INTO `schedules` VALUES (2496, '2025-10-25', 1, 25, 1, '2025-02-17 16:19:59', NULL, NULL, NULL, 74, 74, 21, 15, 9);
INSERT INTO `schedules` VALUES (2497, '2025-10-29', 1, 25, 1, '2025-02-17 16:20:00', NULL, NULL, NULL, 75, 75, 21, 15, 9);
INSERT INTO `schedules` VALUES (2498, '2025-11-01', 1, 25, 1, '2025-02-17 16:20:00', NULL, NULL, NULL, 76, 76, 21, 15, 9);
INSERT INTO `schedules` VALUES (2499, '2025-11-05', 1, 25, 1, '2025-02-17 16:20:00', NULL, NULL, NULL, 77, 77, 21, 15, 9);
INSERT INTO `schedules` VALUES (2500, '2025-11-08', 1, 25, 1, '2025-02-17 16:20:00', NULL, NULL, NULL, 78, 78, 21, 15, 9);
INSERT INTO `schedules` VALUES (2501, '2025-11-12', 1, 25, 1, '2025-02-17 16:20:00', NULL, NULL, NULL, 79, 79, 21, 15, 9);
INSERT INTO `schedules` VALUES (2502, '2025-11-15', 1, 25, 1, '2025-02-17 16:20:00', NULL, NULL, NULL, 80, 80, 21, 15, 9);
INSERT INTO `schedules` VALUES (2503, '2025-11-19', 1, 25, 1, '2025-02-17 16:20:00', NULL, NULL, NULL, 81, 81, 21, 15, 9);
INSERT INTO `schedules` VALUES (2504, '2025-11-22', 1, 25, 1, '2025-02-17 16:20:00', NULL, NULL, NULL, 82, 82, 21, 15, 9);
INSERT INTO `schedules` VALUES (2505, '2025-11-26', 1, 25, 1, '2025-02-17 16:20:00', NULL, NULL, NULL, 83, 83, 21, 15, 9);
INSERT INTO `schedules` VALUES (2506, '2025-11-29', 1, 25, 1, '2025-02-17 16:20:00', NULL, NULL, NULL, 84, 84, 21, 15, 9);
INSERT INTO `schedules` VALUES (2507, '2025-12-03', 1, 25, 1, '2025-02-17 16:20:00', NULL, NULL, NULL, 85, 85, 21, 15, 9);
INSERT INTO `schedules` VALUES (2508, '2025-12-06', 1, 25, 1, '2025-02-17 16:20:00', NULL, NULL, NULL, 86, 86, 21, 15, 9);
INSERT INTO `schedules` VALUES (2509, '2025-12-10', 1, 25, 1, '2025-02-17 16:20:00', NULL, NULL, NULL, 87, 87, 21, 15, 9);
INSERT INTO `schedules` VALUES (2510, '2025-12-13', 1, 25, 1, '2025-02-17 16:20:00', NULL, NULL, NULL, 88, 88, 21, 15, 9);
INSERT INTO `schedules` VALUES (2511, '2025-12-17', 1, 25, 1, '2025-02-17 16:20:00', NULL, NULL, NULL, 89, 89, 21, 15, 9);
INSERT INTO `schedules` VALUES (2512, '2025-12-20', 1, 25, 1, '2025-02-17 16:20:00', NULL, NULL, NULL, 90, 90, 21, 15, 9);
INSERT INTO `schedules` VALUES (2513, '2025-12-24', 1, 25, 1, '2025-02-17 16:20:00', NULL, NULL, NULL, 91, 91, 21, 15, 9);
INSERT INTO `schedules` VALUES (2514, '2025-12-27', 1, 25, 1, '2025-02-17 16:20:00', NULL, NULL, NULL, 92, 92, 21, 15, 9);
INSERT INTO `schedules` VALUES (2515, '2025-12-31', 1, 25, 1, '2025-02-17 16:20:00', NULL, NULL, NULL, 93, 93, 21, 15, 9);
INSERT INTO `schedules` VALUES (2516, '2026-01-03', 1, 25, 1, '2025-02-17 16:20:00', NULL, NULL, NULL, 94, 94, 21, 15, 9);
INSERT INTO `schedules` VALUES (2517, '2026-01-07', 1, 25, 1, '2025-02-17 16:20:00', NULL, NULL, NULL, 95, 95, 21, 15, 9);
INSERT INTO `schedules` VALUES (2518, '2026-01-10', 1, 25, 1, '2025-02-17 16:20:00', NULL, NULL, NULL, 96, 96, 21, 15, 9);
INSERT INTO `schedules` VALUES (2519, '2025-02-12', 1, 26, 1, '2025-02-17 16:20:31', NULL, NULL, NULL, 1, 1, 22, 15, 9);
INSERT INTO `schedules` VALUES (2520, '2025-02-15', 1, 26, 1, '2025-02-17 16:20:31', NULL, NULL, NULL, 2, 2, 22, 15, 9);
INSERT INTO `schedules` VALUES (2521, '2025-02-19', 1, 26, 1, '2025-02-17 16:20:31', NULL, NULL, NULL, 3, 3, 22, 15, 9);
INSERT INTO `schedules` VALUES (2522, '2025-02-22', 1, 26, 1, '2025-02-17 16:20:31', NULL, NULL, NULL, 4, 4, 22, 15, 9);
INSERT INTO `schedules` VALUES (2523, '2025-02-26', 1, 26, 1, '2025-02-17 16:20:31', NULL, NULL, NULL, 5, 5, 22, 15, 9);
INSERT INTO `schedules` VALUES (2524, '2025-03-01', 1, 26, 1, '2025-02-17 16:20:31', NULL, NULL, NULL, 6, 6, 22, 15, 9);
INSERT INTO `schedules` VALUES (2525, '2025-03-05', 1, 26, 1, '2025-02-17 16:20:31', NULL, NULL, NULL, 7, 7, 22, 15, 9);
INSERT INTO `schedules` VALUES (2526, '2025-03-08', 1, 26, 1, '2025-02-17 16:20:31', NULL, NULL, NULL, 8, 8, 22, 15, 9);
INSERT INTO `schedules` VALUES (2527, '2025-03-12', 1, 26, 1, '2025-02-17 16:20:31', NULL, NULL, NULL, 9, 9, 22, 15, 9);
INSERT INTO `schedules` VALUES (2528, '2025-03-15', 1, 26, 1, '2025-02-17 16:20:31', NULL, NULL, NULL, 10, 10, 22, 15, 9);
INSERT INTO `schedules` VALUES (2529, '2025-03-19', 1, 26, 1, '2025-02-17 16:20:31', NULL, NULL, NULL, 11, 11, 22, 15, 9);
INSERT INTO `schedules` VALUES (2530, '2025-03-22', 1, 26, 1, '2025-02-17 16:20:31', NULL, NULL, NULL, 12, 12, 22, 15, 9);
INSERT INTO `schedules` VALUES (2531, '2025-03-26', 1, 26, 1, '2025-02-17 16:20:31', NULL, NULL, NULL, 13, 13, 22, 15, 9);
INSERT INTO `schedules` VALUES (2532, '2025-03-29', 1, 26, 1, '2025-02-17 16:20:31', NULL, NULL, NULL, 14, 14, 22, 15, 9);
INSERT INTO `schedules` VALUES (2533, '2025-04-02', 1, 26, 1, '2025-02-17 16:20:31', NULL, NULL, NULL, 15, 15, 22, 15, 9);
INSERT INTO `schedules` VALUES (2534, '2025-04-05', 1, 26, 1, '2025-02-17 16:20:31', NULL, NULL, NULL, 16, 16, 22, 15, 9);
INSERT INTO `schedules` VALUES (2535, '2025-04-09', 1, 26, 1, '2025-02-17 16:20:31', NULL, NULL, NULL, 17, 17, 22, 15, 9);
INSERT INTO `schedules` VALUES (2536, '2025-04-12', 1, 26, 1, '2025-02-17 16:20:31', NULL, NULL, NULL, 18, 18, 22, 15, 9);
INSERT INTO `schedules` VALUES (2537, '2025-04-16', 1, 26, 1, '2025-02-17 16:20:31', NULL, NULL, NULL, 19, 19, 22, 15, 9);
INSERT INTO `schedules` VALUES (2538, '2025-04-19', 1, 26, 1, '2025-02-17 16:20:31', NULL, NULL, NULL, 20, 20, 22, 15, 9);
INSERT INTO `schedules` VALUES (2539, '2025-04-23', 1, 26, 1, '2025-02-17 16:20:31', NULL, NULL, NULL, 21, 21, 22, 15, 9);
INSERT INTO `schedules` VALUES (2540, '2025-04-26', 1, 26, 1, '2025-02-17 16:20:31', NULL, NULL, NULL, 22, 22, 22, 15, 9);
INSERT INTO `schedules` VALUES (2541, '2025-04-30', 1, 26, 1, '2025-02-17 16:20:31', NULL, NULL, NULL, 23, 23, 22, 15, 9);
INSERT INTO `schedules` VALUES (2542, '2025-05-03', 1, 26, 1, '2025-02-17 16:20:31', NULL, NULL, NULL, 24, 24, 22, 15, 9);
INSERT INTO `schedules` VALUES (2543, '2025-05-07', 1, 26, 1, '2025-02-17 16:20:31', NULL, NULL, NULL, 25, 25, 22, 15, 9);
INSERT INTO `schedules` VALUES (2544, '2025-05-10', 1, 26, 1, '2025-02-17 16:20:31', NULL, NULL, NULL, 26, 26, 22, 15, 9);
INSERT INTO `schedules` VALUES (2545, '2025-05-14', 1, 26, 1, '2025-02-17 16:20:31', NULL, NULL, NULL, 27, 27, 22, 15, 9);
INSERT INTO `schedules` VALUES (2546, '2025-05-17', 1, 26, 1, '2025-02-17 16:20:31', NULL, NULL, NULL, 28, 28, 22, 15, 9);
INSERT INTO `schedules` VALUES (2547, '2025-05-21', 1, 26, 1, '2025-02-17 16:20:31', NULL, NULL, NULL, 29, 29, 22, 15, 9);
INSERT INTO `schedules` VALUES (2548, '2025-05-24', 1, 26, 1, '2025-02-17 16:20:31', NULL, NULL, NULL, 30, 30, 22, 15, 9);
INSERT INTO `schedules` VALUES (2549, '2025-05-28', 1, 26, 1, '2025-02-17 16:20:31', NULL, NULL, NULL, 31, 31, 22, 15, 9);
INSERT INTO `schedules` VALUES (2550, '2025-05-31', 1, 26, 1, '2025-02-17 16:20:31', NULL, NULL, NULL, 32, 32, 22, 15, 9);
INSERT INTO `schedules` VALUES (2551, '2025-06-04', 1, 26, 1, '2025-02-17 16:20:31', NULL, NULL, NULL, 33, 33, 22, 15, 9);
INSERT INTO `schedules` VALUES (2552, '2025-06-07', 1, 26, 1, '2025-02-17 16:20:31', NULL, NULL, NULL, 34, 34, 22, 15, 9);
INSERT INTO `schedules` VALUES (2553, '2025-06-11', 1, 26, 1, '2025-02-17 16:20:31', NULL, NULL, NULL, 35, 35, 22, 15, 9);
INSERT INTO `schedules` VALUES (2554, '2025-06-14', 1, 26, 1, '2025-02-17 16:20:31', NULL, NULL, NULL, 36, 36, 22, 15, 9);
INSERT INTO `schedules` VALUES (2555, '2025-06-18', 1, 26, 1, '2025-02-17 16:20:31', NULL, NULL, NULL, 37, 37, 22, 15, 9);
INSERT INTO `schedules` VALUES (2556, '2025-06-21', 1, 26, 1, '2025-02-17 16:20:31', NULL, NULL, NULL, 38, 38, 22, 15, 9);
INSERT INTO `schedules` VALUES (2557, '2025-06-25', 1, 26, 1, '2025-02-17 16:20:31', NULL, NULL, NULL, 39, 39, 22, 15, 9);
INSERT INTO `schedules` VALUES (2558, '2025-06-28', 1, 26, 1, '2025-02-17 16:20:31', NULL, NULL, NULL, 40, 40, 22, 15, 9);
INSERT INTO `schedules` VALUES (2559, '2025-07-02', 1, 26, 1, '2025-02-17 16:20:31', NULL, NULL, NULL, 41, 41, 22, 15, 9);
INSERT INTO `schedules` VALUES (2560, '2025-07-05', 1, 26, 1, '2025-02-17 16:20:31', NULL, NULL, NULL, 42, 42, 22, 15, 9);
INSERT INTO `schedules` VALUES (2561, '2025-07-09', 1, 26, 1, '2025-02-17 16:20:31', NULL, NULL, NULL, 43, 43, 22, 15, 9);
INSERT INTO `schedules` VALUES (2562, '2025-07-12', 1, 26, 1, '2025-02-17 16:20:31', NULL, NULL, NULL, 44, 44, 22, 15, 9);
INSERT INTO `schedules` VALUES (2563, '2025-07-16', 1, 26, 1, '2025-02-17 16:20:31', NULL, NULL, NULL, 45, 45, 22, 15, 9);
INSERT INTO `schedules` VALUES (2564, '2025-07-19', 1, 26, 1, '2025-02-17 16:20:31', NULL, NULL, NULL, 46, 46, 22, 15, 9);
INSERT INTO `schedules` VALUES (2565, '2025-07-23', 1, 26, 1, '2025-02-17 16:20:31', NULL, NULL, NULL, 47, 47, 22, 15, 9);
INSERT INTO `schedules` VALUES (2566, '2025-07-26', 1, 26, 1, '2025-02-17 16:20:31', NULL, NULL, NULL, 48, 48, 22, 15, 9);
INSERT INTO `schedules` VALUES (2567, '2025-07-30', 1, 26, 1, '2025-02-17 16:20:31', NULL, NULL, NULL, 49, 49, 22, 15, 9);
INSERT INTO `schedules` VALUES (2568, '2025-08-02', 1, 26, 1, '2025-02-17 16:20:31', NULL, NULL, NULL, 50, 50, 22, 15, 9);
INSERT INTO `schedules` VALUES (2569, '2025-08-06', 1, 26, 1, '2025-02-17 16:20:31', NULL, NULL, NULL, 51, 51, 22, 15, 9);
INSERT INTO `schedules` VALUES (2570, '2025-08-09', 1, 26, 1, '2025-02-17 16:20:31', NULL, NULL, NULL, 52, 52, 22, 15, 9);
INSERT INTO `schedules` VALUES (2571, '2025-08-13', 1, 26, 1, '2025-02-17 16:20:31', NULL, NULL, NULL, 53, 53, 22, 15, 9);
INSERT INTO `schedules` VALUES (2572, '2025-08-16', 1, 26, 1, '2025-02-17 16:20:31', NULL, NULL, NULL, 54, 54, 22, 15, 9);
INSERT INTO `schedules` VALUES (2573, '2025-08-20', 1, 26, 1, '2025-02-17 16:20:31', NULL, NULL, NULL, 55, 55, 22, 15, 9);
INSERT INTO `schedules` VALUES (2574, '2025-08-23', 1, 26, 1, '2025-02-17 16:20:31', NULL, NULL, NULL, 56, 56, 22, 15, 9);
INSERT INTO `schedules` VALUES (2575, '2025-08-27', 1, 26, 1, '2025-02-17 16:20:31', NULL, NULL, NULL, 57, 57, 22, 15, 9);
INSERT INTO `schedules` VALUES (2576, '2025-08-30', 1, 26, 1, '2025-02-17 16:20:31', NULL, NULL, NULL, 58, 58, 22, 15, 9);
INSERT INTO `schedules` VALUES (2577, '2025-09-03', 1, 26, 1, '2025-02-17 16:20:31', NULL, NULL, NULL, 59, 59, 22, 15, 9);
INSERT INTO `schedules` VALUES (2578, '2025-09-06', 1, 26, 1, '2025-02-17 16:20:31', NULL, NULL, NULL, 60, 60, 22, 15, 9);
INSERT INTO `schedules` VALUES (2579, '2025-09-10', 1, 26, 1, '2025-02-17 16:20:31', NULL, NULL, NULL, 61, 61, 22, 15, 9);
INSERT INTO `schedules` VALUES (2580, '2025-09-13', 1, 26, 1, '2025-02-17 16:20:31', NULL, NULL, NULL, 62, 62, 22, 15, 9);
INSERT INTO `schedules` VALUES (2581, '2025-09-17', 1, 26, 1, '2025-02-17 16:20:31', NULL, NULL, NULL, 63, 63, 22, 15, 9);
INSERT INTO `schedules` VALUES (2582, '2025-09-20', 1, 26, 1, '2025-02-17 16:20:31', NULL, NULL, NULL, 64, 64, 22, 15, 9);
INSERT INTO `schedules` VALUES (2583, '2025-09-24', 1, 26, 1, '2025-02-17 16:20:31', NULL, NULL, NULL, 65, 65, 22, 15, 9);
INSERT INTO `schedules` VALUES (2584, '2025-09-27', 1, 26, 1, '2025-02-17 16:20:31', NULL, NULL, NULL, 66, 66, 22, 15, 9);
INSERT INTO `schedules` VALUES (2585, '2025-10-01', 1, 26, 1, '2025-02-17 16:20:31', NULL, NULL, NULL, 67, 67, 22, 15, 9);
INSERT INTO `schedules` VALUES (2586, '2025-10-04', 1, 26, 1, '2025-02-17 16:20:31', NULL, NULL, NULL, 68, 68, 22, 15, 9);
INSERT INTO `schedules` VALUES (2587, '2025-10-08', 1, 26, 1, '2025-02-17 16:20:31', NULL, NULL, NULL, 69, 69, 22, 15, 9);
INSERT INTO `schedules` VALUES (2588, '2025-10-11', 1, 26, 1, '2025-02-17 16:20:32', NULL, NULL, NULL, 70, 70, 22, 15, 9);
INSERT INTO `schedules` VALUES (2589, '2025-10-15', 1, 26, 1, '2025-02-17 16:20:32', NULL, NULL, NULL, 71, 71, 22, 15, 9);
INSERT INTO `schedules` VALUES (2590, '2025-10-18', 1, 26, 1, '2025-02-17 16:20:32', NULL, NULL, NULL, 72, 72, 22, 15, 9);
INSERT INTO `schedules` VALUES (2591, '2025-10-22', 1, 26, 1, '2025-02-17 16:20:32', NULL, NULL, NULL, 73, 73, 22, 15, 9);
INSERT INTO `schedules` VALUES (2592, '2025-10-25', 1, 26, 1, '2025-02-17 16:20:32', NULL, NULL, NULL, 74, 74, 22, 15, 9);
INSERT INTO `schedules` VALUES (2593, '2025-10-29', 1, 26, 1, '2025-02-17 16:20:32', NULL, NULL, NULL, 75, 75, 22, 15, 9);
INSERT INTO `schedules` VALUES (2594, '2025-11-01', 1, 26, 1, '2025-02-17 16:20:32', NULL, NULL, NULL, 76, 76, 22, 15, 9);
INSERT INTO `schedules` VALUES (2595, '2025-11-05', 1, 26, 1, '2025-02-17 16:20:32', NULL, NULL, NULL, 77, 77, 22, 15, 9);
INSERT INTO `schedules` VALUES (2596, '2025-11-08', 1, 26, 1, '2025-02-17 16:20:32', NULL, NULL, NULL, 78, 78, 22, 15, 9);
INSERT INTO `schedules` VALUES (2597, '2025-11-12', 1, 26, 1, '2025-02-17 16:20:32', NULL, NULL, NULL, 79, 79, 22, 15, 9);
INSERT INTO `schedules` VALUES (2598, '2025-11-15', 1, 26, 1, '2025-02-17 16:20:32', NULL, NULL, NULL, 80, 80, 22, 15, 9);
INSERT INTO `schedules` VALUES (2599, '2025-11-19', 1, 26, 1, '2025-02-17 16:20:32', NULL, NULL, NULL, 81, 81, 22, 15, 9);
INSERT INTO `schedules` VALUES (2600, '2025-11-22', 1, 26, 1, '2025-02-17 16:20:32', NULL, NULL, NULL, 82, 82, 22, 15, 9);
INSERT INTO `schedules` VALUES (2601, '2025-11-26', 1, 26, 1, '2025-02-17 16:20:32', NULL, NULL, NULL, 83, 83, 22, 15, 9);
INSERT INTO `schedules` VALUES (2602, '2025-11-29', 1, 26, 1, '2025-02-17 16:20:32', NULL, NULL, NULL, 84, 84, 22, 15, 9);
INSERT INTO `schedules` VALUES (2603, '2025-12-03', 1, 26, 1, '2025-02-17 16:20:32', NULL, NULL, NULL, 85, 85, 22, 15, 9);
INSERT INTO `schedules` VALUES (2604, '2025-12-06', 1, 26, 1, '2025-02-17 16:20:32', NULL, NULL, NULL, 86, 86, 22, 15, 9);
INSERT INTO `schedules` VALUES (2605, '2025-12-10', 1, 26, 1, '2025-02-17 16:20:32', NULL, NULL, NULL, 87, 87, 22, 15, 9);
INSERT INTO `schedules` VALUES (2606, '2025-12-13', 1, 26, 1, '2025-02-17 16:20:32', NULL, NULL, NULL, 88, 88, 22, 15, 9);
INSERT INTO `schedules` VALUES (2607, '2025-12-17', 1, 26, 1, '2025-02-17 16:20:32', NULL, NULL, NULL, 89, 89, 22, 15, 9);
INSERT INTO `schedules` VALUES (2608, '2025-12-20', 1, 26, 1, '2025-02-17 16:20:32', NULL, NULL, NULL, 90, 90, 22, 15, 9);
INSERT INTO `schedules` VALUES (2609, '2025-12-24', 1, 26, 1, '2025-02-17 16:20:32', NULL, NULL, NULL, 91, 91, 22, 15, 9);
INSERT INTO `schedules` VALUES (2610, '2025-12-27', 1, 26, 1, '2025-02-17 16:20:32', NULL, NULL, NULL, 92, 92, 22, 15, 9);
INSERT INTO `schedules` VALUES (2611, '2025-12-31', 1, 26, 1, '2025-02-17 16:20:32', NULL, NULL, NULL, 93, 93, 22, 15, 9);
INSERT INTO `schedules` VALUES (2612, '2026-01-03', 1, 26, 1, '2025-02-17 16:20:32', NULL, NULL, NULL, 94, 94, 22, 15, 9);
INSERT INTO `schedules` VALUES (2613, '2026-01-07', 1, 26, 1, '2025-02-17 16:20:32', NULL, NULL, NULL, 95, 95, 22, 15, 9);
INSERT INTO `schedules` VALUES (2614, '2026-01-10', 1, 26, 1, '2025-02-17 16:20:32', NULL, NULL, NULL, 96, 96, 22, 15, 9);
INSERT INTO `schedules` VALUES (2615, '2025-02-13', 1, 27, 1, '2025-02-17 16:25:40', NULL, NULL, NULL, 1, 1, 21, 15, 9);
INSERT INTO `schedules` VALUES (2616, '2025-02-16', 1, 27, 1, '2025-02-17 16:25:40', NULL, NULL, NULL, 2, 2, 21, 15, 9);
INSERT INTO `schedules` VALUES (2617, '2025-02-20', 1, 27, 1, '2025-02-17 16:25:40', NULL, NULL, NULL, 3, 3, 21, 15, 9);
INSERT INTO `schedules` VALUES (2618, '2025-02-23', 1, 27, 1, '2025-02-17 16:25:40', NULL, NULL, NULL, 4, 4, 21, 15, 9);
INSERT INTO `schedules` VALUES (2619, '2025-02-27', 1, 27, 1, '2025-02-17 16:25:40', NULL, NULL, NULL, 5, 5, 21, 15, 9);
INSERT INTO `schedules` VALUES (2620, '2025-03-02', 1, 27, 1, '2025-02-17 16:25:40', NULL, NULL, NULL, 6, 6, 21, 15, 9);
INSERT INTO `schedules` VALUES (2621, '2025-03-06', 1, 27, 1, '2025-02-17 16:25:40', NULL, NULL, NULL, 7, 7, 21, 15, 9);
INSERT INTO `schedules` VALUES (2622, '2025-03-09', 1, 27, 1, '2025-02-17 16:25:40', NULL, NULL, NULL, 8, 8, 21, 15, 9);
INSERT INTO `schedules` VALUES (2623, '2025-03-13', 1, 27, 1, '2025-02-17 16:25:40', NULL, NULL, NULL, 9, 9, 21, 15, 9);
INSERT INTO `schedules` VALUES (2624, '2025-03-16', 1, 27, 1, '2025-02-17 16:25:40', NULL, NULL, NULL, 10, 10, 21, 15, 9);
INSERT INTO `schedules` VALUES (2625, '2025-03-20', 1, 27, 1, '2025-02-17 16:25:40', NULL, NULL, NULL, 11, 11, 21, 15, 9);
INSERT INTO `schedules` VALUES (2626, '2025-03-23', 1, 27, 1, '2025-02-17 16:25:40', NULL, NULL, NULL, 12, 12, 21, 15, 9);
INSERT INTO `schedules` VALUES (2627, '2025-03-27', 1, 27, 1, '2025-02-17 16:25:40', NULL, NULL, NULL, 13, 13, 21, 15, 9);
INSERT INTO `schedules` VALUES (2628, '2025-03-30', 1, 27, 1, '2025-02-17 16:25:40', NULL, NULL, NULL, 14, 14, 21, 15, 9);
INSERT INTO `schedules` VALUES (2629, '2025-04-03', 1, 27, 1, '2025-02-17 16:25:40', NULL, NULL, NULL, 15, 15, 21, 15, 9);
INSERT INTO `schedules` VALUES (2630, '2025-04-06', 1, 27, 1, '2025-02-17 16:25:40', NULL, NULL, NULL, 16, 16, 21, 15, 9);
INSERT INTO `schedules` VALUES (2631, '2025-04-10', 1, 27, 1, '2025-02-17 16:25:40', NULL, NULL, NULL, 17, 17, 21, 15, 9);
INSERT INTO `schedules` VALUES (2632, '2025-04-13', 1, 27, 1, '2025-02-17 16:25:40', NULL, NULL, NULL, 18, 18, 21, 15, 9);
INSERT INTO `schedules` VALUES (2633, '2025-04-17', 1, 27, 1, '2025-02-17 16:25:40', NULL, NULL, NULL, 19, 19, 21, 15, 9);
INSERT INTO `schedules` VALUES (2634, '2025-04-20', 1, 27, 1, '2025-02-17 16:25:40', NULL, NULL, NULL, 20, 20, 21, 15, 9);
INSERT INTO `schedules` VALUES (2635, '2025-04-24', 1, 27, 1, '2025-02-17 16:25:40', NULL, NULL, NULL, 21, 21, 21, 15, 9);
INSERT INTO `schedules` VALUES (2636, '2025-04-27', 1, 27, 1, '2025-02-17 16:25:40', NULL, NULL, NULL, 22, 22, 21, 15, 9);
INSERT INTO `schedules` VALUES (2637, '2025-05-01', 1, 27, 1, '2025-02-17 16:25:40', NULL, NULL, NULL, 23, 23, 21, 15, 9);
INSERT INTO `schedules` VALUES (2638, '2025-05-04', 1, 27, 1, '2025-02-17 16:25:40', NULL, NULL, NULL, 24, 24, 21, 15, 9);
INSERT INTO `schedules` VALUES (2639, '2025-05-08', 1, 27, 1, '2025-02-17 16:25:40', NULL, NULL, NULL, 25, 25, 21, 15, 9);
INSERT INTO `schedules` VALUES (2640, '2025-05-11', 1, 27, 1, '2025-02-17 16:25:40', NULL, NULL, NULL, 26, 26, 21, 15, 9);
INSERT INTO `schedules` VALUES (2641, '2025-05-15', 1, 27, 1, '2025-02-17 16:25:40', NULL, NULL, NULL, 27, 27, 21, 15, 9);
INSERT INTO `schedules` VALUES (2642, '2025-05-18', 1, 27, 1, '2025-02-17 16:25:40', NULL, NULL, NULL, 28, 28, 21, 15, 9);
INSERT INTO `schedules` VALUES (2643, '2025-05-22', 1, 27, 1, '2025-02-17 16:25:40', NULL, NULL, NULL, 29, 29, 21, 15, 9);
INSERT INTO `schedules` VALUES (2644, '2025-05-25', 1, 27, 1, '2025-02-17 16:25:40', NULL, NULL, NULL, 30, 30, 21, 15, 9);
INSERT INTO `schedules` VALUES (2645, '2025-05-29', 1, 27, 1, '2025-02-17 16:25:40', NULL, NULL, NULL, 31, 31, 21, 15, 9);
INSERT INTO `schedules` VALUES (2646, '2025-06-01', 1, 27, 1, '2025-02-17 16:25:40', NULL, NULL, NULL, 32, 32, 21, 15, 9);
INSERT INTO `schedules` VALUES (2647, '2025-06-05', 1, 27, 1, '2025-02-17 16:25:40', NULL, NULL, NULL, 33, 33, 21, 15, 9);
INSERT INTO `schedules` VALUES (2648, '2025-06-08', 1, 27, 1, '2025-02-17 16:25:40', NULL, NULL, NULL, 34, 34, 21, 15, 9);
INSERT INTO `schedules` VALUES (2649, '2025-06-12', 1, 27, 1, '2025-02-17 16:25:40', NULL, NULL, NULL, 35, 35, 21, 15, 9);
INSERT INTO `schedules` VALUES (2650, '2025-06-15', 1, 27, 1, '2025-02-17 16:25:40', NULL, NULL, NULL, 36, 36, 21, 15, 9);
INSERT INTO `schedules` VALUES (2651, '2025-06-19', 1, 27, 1, '2025-02-17 16:25:40', NULL, NULL, NULL, 37, 37, 21, 15, 9);
INSERT INTO `schedules` VALUES (2652, '2025-06-22', 1, 27, 1, '2025-02-17 16:25:40', NULL, NULL, NULL, 38, 38, 21, 15, 9);
INSERT INTO `schedules` VALUES (2653, '2025-06-26', 1, 27, 1, '2025-02-17 16:25:40', NULL, NULL, NULL, 39, 39, 21, 15, 9);
INSERT INTO `schedules` VALUES (2654, '2025-06-29', 1, 27, 1, '2025-02-17 16:25:40', NULL, NULL, NULL, 40, 40, 21, 15, 9);
INSERT INTO `schedules` VALUES (2655, '2025-07-03', 1, 27, 1, '2025-02-17 16:25:40', NULL, NULL, NULL, 41, 41, 21, 15, 9);
INSERT INTO `schedules` VALUES (2656, '2025-07-06', 1, 27, 1, '2025-02-17 16:25:40', NULL, NULL, NULL, 42, 42, 21, 15, 9);
INSERT INTO `schedules` VALUES (2657, '2025-07-10', 1, 27, 1, '2025-02-17 16:25:40', NULL, NULL, NULL, 43, 43, 21, 15, 9);
INSERT INTO `schedules` VALUES (2658, '2025-07-13', 1, 27, 1, '2025-02-17 16:25:40', NULL, NULL, NULL, 44, 44, 21, 15, 9);
INSERT INTO `schedules` VALUES (2659, '2025-07-17', 1, 27, 1, '2025-02-17 16:25:40', NULL, NULL, NULL, 45, 45, 21, 15, 9);
INSERT INTO `schedules` VALUES (2660, '2025-07-20', 1, 27, 1, '2025-02-17 16:25:40', NULL, NULL, NULL, 46, 46, 21, 15, 9);
INSERT INTO `schedules` VALUES (2661, '2025-07-24', 1, 27, 1, '2025-02-17 16:25:40', NULL, NULL, NULL, 47, 47, 21, 15, 9);
INSERT INTO `schedules` VALUES (2662, '2025-07-27', 1, 27, 1, '2025-02-17 16:25:40', NULL, NULL, NULL, 48, 48, 21, 15, 9);
INSERT INTO `schedules` VALUES (2663, '2025-07-31', 1, 27, 1, '2025-02-17 16:25:40', NULL, NULL, NULL, 49, 49, 21, 15, 9);
INSERT INTO `schedules` VALUES (2664, '2025-08-03', 1, 27, 1, '2025-02-17 16:25:40', NULL, NULL, NULL, 50, 50, 21, 15, 9);
INSERT INTO `schedules` VALUES (2665, '2025-08-07', 1, 27, 1, '2025-02-17 16:25:40', NULL, NULL, NULL, 51, 51, 21, 15, 9);
INSERT INTO `schedules` VALUES (2666, '2025-08-10', 1, 27, 1, '2025-02-17 16:25:40', NULL, NULL, NULL, 52, 52, 21, 15, 9);
INSERT INTO `schedules` VALUES (2667, '2025-08-14', 1, 27, 1, '2025-02-17 16:25:40', NULL, NULL, NULL, 53, 53, 21, 15, 9);
INSERT INTO `schedules` VALUES (2668, '2025-08-17', 1, 27, 1, '2025-02-17 16:25:40', NULL, NULL, NULL, 54, 54, 21, 15, 9);
INSERT INTO `schedules` VALUES (2669, '2025-08-21', 1, 27, 1, '2025-02-17 16:25:40', NULL, NULL, NULL, 55, 55, 21, 15, 9);
INSERT INTO `schedules` VALUES (2670, '2025-08-24', 1, 27, 1, '2025-02-17 16:25:40', NULL, NULL, NULL, 56, 56, 21, 15, 9);
INSERT INTO `schedules` VALUES (2671, '2025-08-28', 1, 27, 1, '2025-02-17 16:25:40', NULL, NULL, NULL, 57, 57, 21, 15, 9);
INSERT INTO `schedules` VALUES (2672, '2025-08-31', 1, 27, 1, '2025-02-17 16:25:40', NULL, NULL, NULL, 58, 58, 21, 15, 9);
INSERT INTO `schedules` VALUES (2673, '2025-09-04', 1, 27, 1, '2025-02-17 16:25:40', NULL, NULL, NULL, 59, 59, 21, 15, 9);
INSERT INTO `schedules` VALUES (2674, '2025-09-07', 1, 27, 1, '2025-02-17 16:25:40', NULL, NULL, NULL, 60, 60, 21, 15, 9);
INSERT INTO `schedules` VALUES (2675, '2025-09-11', 1, 27, 1, '2025-02-17 16:25:40', NULL, NULL, NULL, 61, 61, 21, 15, 9);
INSERT INTO `schedules` VALUES (2676, '2025-09-14', 1, 27, 1, '2025-02-17 16:25:40', NULL, NULL, NULL, 62, 62, 21, 15, 9);
INSERT INTO `schedules` VALUES (2677, '2025-09-18', 1, 27, 1, '2025-02-17 16:25:40', NULL, NULL, NULL, 63, 63, 21, 15, 9);
INSERT INTO `schedules` VALUES (2678, '2025-09-21', 1, 27, 1, '2025-02-17 16:25:40', NULL, NULL, NULL, 64, 64, 21, 15, 9);
INSERT INTO `schedules` VALUES (2679, '2025-09-25', 1, 27, 1, '2025-02-17 16:25:40', NULL, NULL, NULL, 65, 65, 21, 15, 9);
INSERT INTO `schedules` VALUES (2680, '2025-09-28', 1, 27, 1, '2025-02-17 16:25:40', NULL, NULL, NULL, 66, 66, 21, 15, 9);
INSERT INTO `schedules` VALUES (2681, '2025-10-02', 1, 27, 1, '2025-02-17 16:25:40', NULL, NULL, NULL, 67, 67, 21, 15, 9);
INSERT INTO `schedules` VALUES (2682, '2025-10-05', 1, 27, 1, '2025-02-17 16:25:40', NULL, NULL, NULL, 68, 68, 21, 15, 9);
INSERT INTO `schedules` VALUES (2683, '2025-10-09', 1, 27, 1, '2025-02-17 16:25:40', NULL, NULL, NULL, 69, 69, 21, 15, 9);
INSERT INTO `schedules` VALUES (2684, '2025-10-12', 1, 27, 1, '2025-02-17 16:25:40', NULL, NULL, NULL, 70, 70, 21, 15, 9);
INSERT INTO `schedules` VALUES (2685, '2025-10-16', 1, 27, 1, '2025-02-17 16:25:40', NULL, NULL, NULL, 71, 71, 21, 15, 9);
INSERT INTO `schedules` VALUES (2686, '2025-10-19', 1, 27, 1, '2025-02-17 16:25:40', NULL, NULL, NULL, 72, 72, 21, 15, 9);
INSERT INTO `schedules` VALUES (2687, '2025-10-23', 1, 27, 1, '2025-02-17 16:25:40', NULL, NULL, NULL, 73, 73, 21, 15, 9);
INSERT INTO `schedules` VALUES (2688, '2025-10-26', 1, 27, 1, '2025-02-17 16:25:40', NULL, NULL, NULL, 74, 74, 21, 15, 9);
INSERT INTO `schedules` VALUES (2689, '2025-10-30', 1, 27, 1, '2025-02-17 16:25:40', NULL, NULL, NULL, 75, 75, 21, 15, 9);
INSERT INTO `schedules` VALUES (2690, '2025-11-02', 1, 27, 1, '2025-02-17 16:25:40', NULL, NULL, NULL, 76, 76, 21, 15, 9);
INSERT INTO `schedules` VALUES (2691, '2025-11-06', 1, 27, 1, '2025-02-17 16:25:40', NULL, NULL, NULL, 77, 77, 21, 15, 9);
INSERT INTO `schedules` VALUES (2692, '2025-11-09', 1, 27, 1, '2025-02-17 16:25:40', NULL, NULL, NULL, 78, 78, 21, 15, 9);
INSERT INTO `schedules` VALUES (2693, '2025-11-13', 1, 27, 1, '2025-02-17 16:25:40', NULL, NULL, NULL, 79, 79, 21, 15, 9);
INSERT INTO `schedules` VALUES (2694, '2025-11-16', 1, 27, 1, '2025-02-17 16:25:40', NULL, NULL, NULL, 80, 80, 21, 15, 9);
INSERT INTO `schedules` VALUES (2695, '2025-11-20', 1, 27, 1, '2025-02-17 16:25:40', NULL, NULL, NULL, 81, 81, 21, 15, 9);
INSERT INTO `schedules` VALUES (2696, '2025-11-23', 1, 27, 1, '2025-02-17 16:25:40', NULL, NULL, NULL, 82, 82, 21, 15, 9);
INSERT INTO `schedules` VALUES (2697, '2025-11-27', 1, 27, 1, '2025-02-17 16:25:40', NULL, NULL, NULL, 83, 83, 21, 15, 9);
INSERT INTO `schedules` VALUES (2698, '2025-11-30', 1, 27, 1, '2025-02-17 16:25:40', NULL, NULL, NULL, 84, 84, 21, 15, 9);
INSERT INTO `schedules` VALUES (2699, '2025-12-04', 1, 27, 1, '2025-02-17 16:25:40', NULL, NULL, NULL, 85, 85, 21, 15, 9);
INSERT INTO `schedules` VALUES (2700, '2025-12-07', 1, 27, 1, '2025-02-17 16:25:40', NULL, NULL, NULL, 86, 86, 21, 15, 9);
INSERT INTO `schedules` VALUES (2701, '2025-12-11', 1, 27, 1, '2025-02-17 16:25:40', NULL, NULL, NULL, 87, 87, 21, 15, 9);
INSERT INTO `schedules` VALUES (2702, '2025-12-14', 1, 27, 1, '2025-02-17 16:25:40', NULL, NULL, NULL, 88, 88, 21, 15, 9);
INSERT INTO `schedules` VALUES (2703, '2025-12-18', 1, 27, 1, '2025-02-17 16:25:40', NULL, NULL, NULL, 89, 89, 21, 15, 9);
INSERT INTO `schedules` VALUES (2704, '2025-12-21', 1, 27, 1, '2025-02-17 16:25:40', NULL, NULL, NULL, 90, 90, 21, 15, 9);
INSERT INTO `schedules` VALUES (2705, '2025-12-25', 1, 27, 1, '2025-02-17 16:25:40', NULL, NULL, NULL, 91, 91, 21, 15, 9);
INSERT INTO `schedules` VALUES (2706, '2025-12-28', 1, 27, 1, '2025-02-17 16:25:40', NULL, NULL, NULL, 92, 92, 21, 15, 9);
INSERT INTO `schedules` VALUES (2707, '2026-01-01', 1, 27, 1, '2025-02-17 16:25:40', NULL, NULL, NULL, 93, 93, 21, 15, 9);
INSERT INTO `schedules` VALUES (2708, '2026-01-04', 1, 27, 1, '2025-02-17 16:25:40', NULL, NULL, NULL, 94, 94, 21, 15, 9);
INSERT INTO `schedules` VALUES (2709, '2026-01-08', 1, 27, 1, '2025-02-17 16:25:40', NULL, NULL, NULL, 95, 95, 21, 15, 9);
INSERT INTO `schedules` VALUES (2710, '2026-01-11', 1, 27, 1, '2025-02-17 16:25:40', NULL, NULL, NULL, 96, 96, 21, 15, 9);
INSERT INTO `schedules` VALUES (2711, '2025-02-13', 1, 28, 1, '2025-02-17 16:26:33', NULL, NULL, NULL, 1, 1, 22, 15, 9);
INSERT INTO `schedules` VALUES (2712, '2025-02-16', 1, 28, 1, '2025-02-17 16:26:33', NULL, NULL, NULL, 2, 2, 22, 15, 9);
INSERT INTO `schedules` VALUES (2713, '2025-02-20', 1, 28, 1, '2025-02-17 16:26:33', NULL, NULL, NULL, 3, 3, 22, 15, 9);
INSERT INTO `schedules` VALUES (2714, '2025-02-23', 1, 28, 1, '2025-02-17 16:26:33', NULL, NULL, NULL, 4, 4, 22, 15, 9);
INSERT INTO `schedules` VALUES (2715, '2025-02-27', 1, 28, 1, '2025-02-17 16:26:33', NULL, NULL, NULL, 5, 5, 22, 15, 9);
INSERT INTO `schedules` VALUES (2716, '2025-03-02', 1, 28, 1, '2025-02-17 16:26:33', NULL, NULL, NULL, 6, 6, 22, 15, 9);
INSERT INTO `schedules` VALUES (2717, '2025-03-06', 1, 28, 1, '2025-02-17 16:26:33', NULL, NULL, NULL, 7, 7, 22, 15, 9);
INSERT INTO `schedules` VALUES (2718, '2025-03-09', 1, 28, 1, '2025-02-17 16:26:33', NULL, NULL, NULL, 8, 8, 22, 15, 9);
INSERT INTO `schedules` VALUES (2719, '2025-03-13', 1, 28, 1, '2025-02-17 16:26:33', NULL, NULL, NULL, 9, 9, 22, 15, 9);
INSERT INTO `schedules` VALUES (2720, '2025-03-16', 1, 28, 1, '2025-02-17 16:26:33', NULL, NULL, NULL, 10, 10, 22, 15, 9);
INSERT INTO `schedules` VALUES (2721, '2025-03-20', 1, 28, 1, '2025-02-17 16:26:33', NULL, NULL, NULL, 11, 11, 22, 15, 9);
INSERT INTO `schedules` VALUES (2722, '2025-03-23', 1, 28, 1, '2025-02-17 16:26:33', NULL, NULL, NULL, 12, 12, 22, 15, 9);
INSERT INTO `schedules` VALUES (2723, '2025-03-27', 1, 28, 1, '2025-02-17 16:26:33', NULL, NULL, NULL, 13, 13, 22, 15, 9);
INSERT INTO `schedules` VALUES (2724, '2025-03-30', 1, 28, 1, '2025-02-17 16:26:33', NULL, NULL, NULL, 14, 14, 22, 15, 9);
INSERT INTO `schedules` VALUES (2725, '2025-04-03', 1, 28, 1, '2025-02-17 16:26:33', NULL, NULL, NULL, 15, 15, 22, 15, 9);
INSERT INTO `schedules` VALUES (2726, '2025-04-06', 1, 28, 1, '2025-02-17 16:26:33', NULL, NULL, NULL, 16, 16, 22, 15, 9);
INSERT INTO `schedules` VALUES (2727, '2025-04-10', 1, 28, 1, '2025-02-17 16:26:33', NULL, NULL, NULL, 17, 17, 22, 15, 9);
INSERT INTO `schedules` VALUES (2728, '2025-04-13', 1, 28, 1, '2025-02-17 16:26:33', NULL, NULL, NULL, 18, 18, 22, 15, 9);
INSERT INTO `schedules` VALUES (2729, '2025-04-17', 1, 28, 1, '2025-02-17 16:26:33', NULL, NULL, NULL, 19, 19, 22, 15, 9);
INSERT INTO `schedules` VALUES (2730, '2025-04-20', 1, 28, 1, '2025-02-17 16:26:33', NULL, NULL, NULL, 20, 20, 22, 15, 9);
INSERT INTO `schedules` VALUES (2731, '2025-04-24', 1, 28, 1, '2025-02-17 16:26:33', NULL, NULL, NULL, 21, 21, 22, 15, 9);
INSERT INTO `schedules` VALUES (2732, '2025-04-27', 1, 28, 1, '2025-02-17 16:26:33', NULL, NULL, NULL, 22, 22, 22, 15, 9);
INSERT INTO `schedules` VALUES (2733, '2025-05-01', 1, 28, 1, '2025-02-17 16:26:33', NULL, NULL, NULL, 23, 23, 22, 15, 9);
INSERT INTO `schedules` VALUES (2734, '2025-05-04', 1, 28, 1, '2025-02-17 16:26:33', NULL, NULL, NULL, 24, 24, 22, 15, 9);
INSERT INTO `schedules` VALUES (2735, '2025-05-08', 1, 28, 1, '2025-02-17 16:26:33', NULL, NULL, NULL, 25, 25, 22, 15, 9);
INSERT INTO `schedules` VALUES (2736, '2025-05-11', 1, 28, 1, '2025-02-17 16:26:33', NULL, NULL, NULL, 26, 26, 22, 15, 9);
INSERT INTO `schedules` VALUES (2737, '2025-05-15', 1, 28, 1, '2025-02-17 16:26:33', NULL, NULL, NULL, 27, 27, 22, 15, 9);
INSERT INTO `schedules` VALUES (2738, '2025-05-18', 1, 28, 1, '2025-02-17 16:26:33', NULL, NULL, NULL, 28, 28, 22, 15, 9);
INSERT INTO `schedules` VALUES (2739, '2025-05-22', 1, 28, 1, '2025-02-17 16:26:33', NULL, NULL, NULL, 29, 29, 22, 15, 9);
INSERT INTO `schedules` VALUES (2740, '2025-05-25', 1, 28, 1, '2025-02-17 16:26:33', NULL, NULL, NULL, 30, 30, 22, 15, 9);
INSERT INTO `schedules` VALUES (2741, '2025-05-29', 1, 28, 1, '2025-02-17 16:26:33', NULL, NULL, NULL, 31, 31, 22, 15, 9);
INSERT INTO `schedules` VALUES (2742, '2025-06-01', 1, 28, 1, '2025-02-17 16:26:33', NULL, NULL, NULL, 32, 32, 22, 15, 9);
INSERT INTO `schedules` VALUES (2743, '2025-06-05', 1, 28, 1, '2025-02-17 16:26:33', NULL, NULL, NULL, 33, 33, 22, 15, 9);
INSERT INTO `schedules` VALUES (2744, '2025-06-08', 1, 28, 1, '2025-02-17 16:26:33', NULL, NULL, NULL, 34, 34, 22, 15, 9);
INSERT INTO `schedules` VALUES (2745, '2025-06-12', 1, 28, 1, '2025-02-17 16:26:33', NULL, NULL, NULL, 35, 35, 22, 15, 9);
INSERT INTO `schedules` VALUES (2746, '2025-06-15', 1, 28, 1, '2025-02-17 16:26:33', NULL, NULL, NULL, 36, 36, 22, 15, 9);
INSERT INTO `schedules` VALUES (2747, '2025-06-19', 1, 28, 1, '2025-02-17 16:26:33', NULL, NULL, NULL, 37, 37, 22, 15, 9);
INSERT INTO `schedules` VALUES (2748, '2025-06-22', 1, 28, 1, '2025-02-17 16:26:33', NULL, NULL, NULL, 38, 38, 22, 15, 9);
INSERT INTO `schedules` VALUES (2749, '2025-06-26', 1, 28, 1, '2025-02-17 16:26:33', NULL, NULL, NULL, 39, 39, 22, 15, 9);
INSERT INTO `schedules` VALUES (2750, '2025-06-29', 1, 28, 1, '2025-02-17 16:26:33', NULL, NULL, NULL, 40, 40, 22, 15, 9);
INSERT INTO `schedules` VALUES (2751, '2025-07-03', 1, 28, 1, '2025-02-17 16:26:33', NULL, NULL, NULL, 41, 41, 22, 15, 9);
INSERT INTO `schedules` VALUES (2752, '2025-07-06', 1, 28, 1, '2025-02-17 16:26:33', NULL, NULL, NULL, 42, 42, 22, 15, 9);
INSERT INTO `schedules` VALUES (2753, '2025-07-10', 1, 28, 1, '2025-02-17 16:26:33', NULL, NULL, NULL, 43, 43, 22, 15, 9);
INSERT INTO `schedules` VALUES (2754, '2025-07-13', 1, 28, 1, '2025-02-17 16:26:33', NULL, NULL, NULL, 44, 44, 22, 15, 9);
INSERT INTO `schedules` VALUES (2755, '2025-07-17', 1, 28, 1, '2025-02-17 16:26:33', NULL, NULL, NULL, 45, 45, 22, 15, 9);
INSERT INTO `schedules` VALUES (2756, '2025-07-20', 1, 28, 1, '2025-02-17 16:26:33', NULL, NULL, NULL, 46, 46, 22, 15, 9);
INSERT INTO `schedules` VALUES (2757, '2025-07-24', 1, 28, 1, '2025-02-17 16:26:33', NULL, NULL, NULL, 47, 47, 22, 15, 9);
INSERT INTO `schedules` VALUES (2758, '2025-07-27', 1, 28, 1, '2025-02-17 16:26:33', NULL, NULL, NULL, 48, 48, 22, 15, 9);
INSERT INTO `schedules` VALUES (2759, '2025-07-31', 1, 28, 1, '2025-02-17 16:26:33', NULL, NULL, NULL, 49, 49, 22, 15, 9);
INSERT INTO `schedules` VALUES (2760, '2025-08-03', 1, 28, 1, '2025-02-17 16:26:33', NULL, NULL, NULL, 50, 50, 22, 15, 9);
INSERT INTO `schedules` VALUES (2761, '2025-08-07', 1, 28, 1, '2025-02-17 16:26:33', NULL, NULL, NULL, 51, 51, 22, 15, 9);
INSERT INTO `schedules` VALUES (2762, '2025-08-10', 1, 28, 1, '2025-02-17 16:26:33', NULL, NULL, NULL, 52, 52, 22, 15, 9);
INSERT INTO `schedules` VALUES (2763, '2025-08-14', 1, 28, 1, '2025-02-17 16:26:33', NULL, NULL, NULL, 53, 53, 22, 15, 9);
INSERT INTO `schedules` VALUES (2764, '2025-08-17', 1, 28, 1, '2025-02-17 16:26:33', NULL, NULL, NULL, 54, 54, 22, 15, 9);
INSERT INTO `schedules` VALUES (2765, '2025-08-21', 1, 28, 1, '2025-02-17 16:26:33', NULL, NULL, NULL, 55, 55, 22, 15, 9);
INSERT INTO `schedules` VALUES (2766, '2025-08-24', 1, 28, 1, '2025-02-17 16:26:33', NULL, NULL, NULL, 56, 56, 22, 15, 9);
INSERT INTO `schedules` VALUES (2767, '2025-08-28', 1, 28, 1, '2025-02-17 16:26:33', NULL, NULL, NULL, 57, 57, 22, 15, 9);
INSERT INTO `schedules` VALUES (2768, '2025-08-31', 1, 28, 1, '2025-02-17 16:26:33', NULL, NULL, NULL, 58, 58, 22, 15, 9);
INSERT INTO `schedules` VALUES (2769, '2025-09-04', 1, 28, 1, '2025-02-17 16:26:33', NULL, NULL, NULL, 59, 59, 22, 15, 9);
INSERT INTO `schedules` VALUES (2770, '2025-09-07', 1, 28, 1, '2025-02-17 16:26:33', NULL, NULL, NULL, 60, 60, 22, 15, 9);
INSERT INTO `schedules` VALUES (2771, '2025-09-11', 1, 28, 1, '2025-02-17 16:26:33', NULL, NULL, NULL, 61, 61, 22, 15, 9);
INSERT INTO `schedules` VALUES (2772, '2025-09-14', 1, 28, 1, '2025-02-17 16:26:33', NULL, NULL, NULL, 62, 62, 22, 15, 9);
INSERT INTO `schedules` VALUES (2773, '2025-09-18', 1, 28, 1, '2025-02-17 16:26:33', NULL, NULL, NULL, 63, 63, 22, 15, 9);
INSERT INTO `schedules` VALUES (2774, '2025-09-21', 1, 28, 1, '2025-02-17 16:26:33', NULL, NULL, NULL, 64, 64, 22, 15, 9);
INSERT INTO `schedules` VALUES (2775, '2025-09-25', 1, 28, 1, '2025-02-17 16:26:33', NULL, NULL, NULL, 65, 65, 22, 15, 9);
INSERT INTO `schedules` VALUES (2776, '2025-09-28', 1, 28, 1, '2025-02-17 16:26:33', NULL, NULL, NULL, 66, 66, 22, 15, 9);
INSERT INTO `schedules` VALUES (2777, '2025-10-02', 1, 28, 1, '2025-02-17 16:26:33', NULL, NULL, NULL, 67, 67, 22, 15, 9);
INSERT INTO `schedules` VALUES (2778, '2025-10-05', 1, 28, 1, '2025-02-17 16:26:33', NULL, NULL, NULL, 68, 68, 22, 15, 9);
INSERT INTO `schedules` VALUES (2779, '2025-10-09', 1, 28, 1, '2025-02-17 16:26:33', NULL, NULL, NULL, 69, 69, 22, 15, 9);
INSERT INTO `schedules` VALUES (2780, '2025-10-12', 1, 28, 1, '2025-02-17 16:26:33', NULL, NULL, NULL, 70, 70, 22, 15, 9);
INSERT INTO `schedules` VALUES (2781, '2025-10-16', 1, 28, 1, '2025-02-17 16:26:33', NULL, NULL, NULL, 71, 71, 22, 15, 9);
INSERT INTO `schedules` VALUES (2782, '2025-10-19', 1, 28, 1, '2025-02-17 16:26:33', NULL, NULL, NULL, 72, 72, 22, 15, 9);
INSERT INTO `schedules` VALUES (2783, '2025-10-23', 1, 28, 1, '2025-02-17 16:26:33', NULL, NULL, NULL, 73, 73, 22, 15, 9);
INSERT INTO `schedules` VALUES (2784, '2025-10-26', 1, 28, 1, '2025-02-17 16:26:33', NULL, NULL, NULL, 74, 74, 22, 15, 9);
INSERT INTO `schedules` VALUES (2785, '2025-10-30', 1, 28, 1, '2025-02-17 16:26:33', NULL, NULL, NULL, 75, 75, 22, 15, 9);
INSERT INTO `schedules` VALUES (2786, '2025-11-02', 1, 28, 1, '2025-02-17 16:26:33', NULL, NULL, NULL, 76, 76, 22, 15, 9);
INSERT INTO `schedules` VALUES (2787, '2025-11-06', 1, 28, 1, '2025-02-17 16:26:33', NULL, NULL, NULL, 77, 77, 22, 15, 9);
INSERT INTO `schedules` VALUES (2788, '2025-11-09', 1, 28, 1, '2025-02-17 16:26:33', NULL, NULL, NULL, 78, 78, 22, 15, 9);
INSERT INTO `schedules` VALUES (2789, '2025-11-13', 1, 28, 1, '2025-02-17 16:26:33', NULL, NULL, NULL, 79, 79, 22, 15, 9);
INSERT INTO `schedules` VALUES (2790, '2025-11-16', 1, 28, 1, '2025-02-17 16:26:33', NULL, NULL, NULL, 80, 80, 22, 15, 9);
INSERT INTO `schedules` VALUES (2791, '2025-11-20', 1, 28, 1, '2025-02-17 16:26:33', NULL, NULL, NULL, 81, 81, 22, 15, 9);
INSERT INTO `schedules` VALUES (2792, '2025-11-23', 1, 28, 1, '2025-02-17 16:26:33', NULL, NULL, NULL, 82, 82, 22, 15, 9);
INSERT INTO `schedules` VALUES (2793, '2025-11-27', 1, 28, 1, '2025-02-17 16:26:33', NULL, NULL, NULL, 83, 83, 22, 15, 9);
INSERT INTO `schedules` VALUES (2794, '2025-11-30', 1, 28, 1, '2025-02-17 16:26:33', NULL, NULL, NULL, 84, 84, 22, 15, 9);
INSERT INTO `schedules` VALUES (2795, '2025-12-04', 1, 28, 1, '2025-02-17 16:26:33', NULL, NULL, NULL, 85, 85, 22, 15, 9);
INSERT INTO `schedules` VALUES (2796, '2025-12-07', 1, 28, 1, '2025-02-17 16:26:33', NULL, NULL, NULL, 86, 86, 22, 15, 9);
INSERT INTO `schedules` VALUES (2797, '2025-12-11', 1, 28, 1, '2025-02-17 16:26:33', NULL, NULL, NULL, 87, 87, 22, 15, 9);
INSERT INTO `schedules` VALUES (2798, '2025-12-14', 1, 28, 1, '2025-02-17 16:26:33', NULL, NULL, NULL, 88, 88, 22, 15, 9);
INSERT INTO `schedules` VALUES (2799, '2025-12-18', 1, 28, 1, '2025-02-17 16:26:33', NULL, NULL, NULL, 89, 89, 22, 15, 9);
INSERT INTO `schedules` VALUES (2800, '2025-12-21', 1, 28, 1, '2025-02-17 16:26:33', NULL, NULL, NULL, 90, 90, 22, 15, 9);
INSERT INTO `schedules` VALUES (2801, '2025-12-25', 1, 28, 1, '2025-02-17 16:26:33', NULL, NULL, NULL, 91, 91, 22, 15, 9);
INSERT INTO `schedules` VALUES (2802, '2025-12-28', 1, 28, 1, '2025-02-17 16:26:33', NULL, NULL, NULL, 92, 92, 22, 15, 9);
INSERT INTO `schedules` VALUES (2803, '2026-01-01', 1, 28, 1, '2025-02-17 16:26:33', NULL, NULL, NULL, 93, 93, 22, 15, 9);
INSERT INTO `schedules` VALUES (2804, '2026-01-04', 1, 28, 1, '2025-02-17 16:26:34', NULL, NULL, NULL, 94, 94, 22, 15, 9);
INSERT INTO `schedules` VALUES (2805, '2026-01-08', 1, 28, 1, '2025-02-17 16:26:34', NULL, NULL, NULL, 95, 95, 22, 15, 9);
INSERT INTO `schedules` VALUES (2806, '2026-01-11', 1, 28, 1, '2025-02-17 16:26:34', NULL, NULL, NULL, 96, 96, 22, 15, 9);
INSERT INTO `schedules` VALUES (2807, '2025-02-13', 1, 29, 1, '2025-02-17 16:34:27', NULL, NULL, NULL, 1, 1, 21, 15, 9);
INSERT INTO `schedules` VALUES (2808, '2025-02-16', 1, 29, 1, '2025-02-17 16:34:27', NULL, NULL, NULL, 2, 2, 21, 15, 9);
INSERT INTO `schedules` VALUES (2809, '2025-02-20', 1, 29, 1, '2025-02-17 16:34:27', NULL, NULL, NULL, 3, 3, 21, 15, 9);
INSERT INTO `schedules` VALUES (2810, '2025-02-23', 1, 29, 1, '2025-02-17 16:34:27', NULL, NULL, NULL, 4, 4, 21, 15, 9);
INSERT INTO `schedules` VALUES (2811, '2025-02-27', 1, 29, 1, '2025-02-17 16:34:27', NULL, NULL, NULL, 5, 5, 21, 15, 9);
INSERT INTO `schedules` VALUES (2812, '2025-03-02', 1, 29, 1, '2025-02-17 16:34:27', NULL, NULL, NULL, 6, 6, 21, 15, 9);
INSERT INTO `schedules` VALUES (2813, '2025-03-06', 1, 29, 1, '2025-02-17 16:34:27', NULL, NULL, NULL, 7, 7, 21, 15, 9);
INSERT INTO `schedules` VALUES (2814, '2025-03-09', 1, 29, 1, '2025-02-17 16:34:27', NULL, NULL, NULL, 8, 8, 21, 15, 9);
INSERT INTO `schedules` VALUES (2815, '2025-03-13', 1, 29, 1, '2025-02-17 16:34:27', NULL, NULL, NULL, 9, 9, 21, 15, 9);
INSERT INTO `schedules` VALUES (2816, '2025-03-16', 1, 29, 1, '2025-02-17 16:34:27', NULL, NULL, NULL, 10, 10, 21, 15, 9);
INSERT INTO `schedules` VALUES (2817, '2025-03-20', 1, 29, 1, '2025-02-17 16:34:27', NULL, NULL, NULL, 11, 11, 21, 15, 9);
INSERT INTO `schedules` VALUES (2818, '2025-03-23', 1, 29, 1, '2025-02-17 16:34:28', NULL, NULL, NULL, 12, 12, 21, 15, 9);
INSERT INTO `schedules` VALUES (2819, '2025-03-27', 1, 29, 1, '2025-02-17 16:34:28', NULL, NULL, NULL, 13, 13, 21, 15, 9);
INSERT INTO `schedules` VALUES (2820, '2025-03-30', 1, 29, 1, '2025-02-17 16:34:28', NULL, NULL, NULL, 14, 14, 21, 15, 9);
INSERT INTO `schedules` VALUES (2821, '2025-04-03', 1, 29, 1, '2025-02-17 16:34:28', NULL, NULL, NULL, 15, 15, 21, 15, 9);
INSERT INTO `schedules` VALUES (2822, '2025-04-06', 1, 29, 1, '2025-02-17 16:34:28', NULL, NULL, NULL, 16, 16, 21, 15, 9);
INSERT INTO `schedules` VALUES (2823, '2025-04-10', 1, 29, 1, '2025-02-17 16:34:28', NULL, NULL, NULL, 17, 17, 21, 15, 9);
INSERT INTO `schedules` VALUES (2824, '2025-04-13', 1, 29, 1, '2025-02-17 16:34:28', NULL, NULL, NULL, 18, 18, 21, 15, 9);
INSERT INTO `schedules` VALUES (2825, '2025-04-17', 1, 29, 1, '2025-02-17 16:34:28', NULL, NULL, NULL, 19, 19, 21, 15, 9);
INSERT INTO `schedules` VALUES (2826, '2025-04-20', 1, 29, 1, '2025-02-17 16:34:28', NULL, NULL, NULL, 20, 20, 21, 15, 9);
INSERT INTO `schedules` VALUES (2827, '2025-04-24', 1, 29, 1, '2025-02-17 16:34:28', NULL, NULL, NULL, 21, 21, 21, 15, 9);
INSERT INTO `schedules` VALUES (2828, '2025-04-27', 1, 29, 1, '2025-02-17 16:34:28', NULL, NULL, NULL, 22, 22, 21, 15, 9);
INSERT INTO `schedules` VALUES (2829, '2025-05-01', 1, 29, 1, '2025-02-17 16:34:28', NULL, NULL, NULL, 23, 23, 21, 15, 9);
INSERT INTO `schedules` VALUES (2830, '2025-05-04', 1, 29, 1, '2025-02-17 16:34:28', NULL, NULL, NULL, 24, 24, 21, 15, 9);
INSERT INTO `schedules` VALUES (2831, '2025-05-08', 1, 29, 1, '2025-02-17 16:34:28', NULL, NULL, NULL, 25, 25, 21, 15, 9);
INSERT INTO `schedules` VALUES (2832, '2025-05-11', 1, 29, 1, '2025-02-17 16:34:28', NULL, NULL, NULL, 26, 26, 21, 15, 9);
INSERT INTO `schedules` VALUES (2833, '2025-05-15', 1, 29, 1, '2025-02-17 16:34:28', NULL, NULL, NULL, 27, 27, 21, 15, 9);
INSERT INTO `schedules` VALUES (2834, '2025-05-18', 1, 29, 1, '2025-02-17 16:34:28', NULL, NULL, NULL, 28, 28, 21, 15, 9);
INSERT INTO `schedules` VALUES (2835, '2025-05-22', 1, 29, 1, '2025-02-17 16:34:28', NULL, NULL, NULL, 29, 29, 21, 15, 9);
INSERT INTO `schedules` VALUES (2836, '2025-05-25', 1, 29, 1, '2025-02-17 16:34:28', NULL, NULL, NULL, 30, 30, 21, 15, 9);
INSERT INTO `schedules` VALUES (2837, '2025-05-29', 1, 29, 1, '2025-02-17 16:34:28', NULL, NULL, NULL, 31, 31, 21, 15, 9);
INSERT INTO `schedules` VALUES (2838, '2025-06-01', 1, 29, 1, '2025-02-17 16:34:28', NULL, NULL, NULL, 32, 32, 21, 15, 9);
INSERT INTO `schedules` VALUES (2839, '2025-06-05', 1, 29, 1, '2025-02-17 16:34:28', NULL, NULL, NULL, 33, 33, 21, 15, 9);
INSERT INTO `schedules` VALUES (2840, '2025-06-08', 1, 29, 1, '2025-02-17 16:34:28', NULL, NULL, NULL, 34, 34, 21, 15, 9);
INSERT INTO `schedules` VALUES (2841, '2025-06-12', 1, 29, 1, '2025-02-17 16:34:28', NULL, NULL, NULL, 35, 35, 21, 15, 9);
INSERT INTO `schedules` VALUES (2842, '2025-06-15', 1, 29, 1, '2025-02-17 16:34:28', NULL, NULL, NULL, 36, 36, 21, 15, 9);
INSERT INTO `schedules` VALUES (2843, '2025-06-19', 1, 29, 1, '2025-02-17 16:34:28', NULL, NULL, NULL, 37, 37, 21, 15, 9);
INSERT INTO `schedules` VALUES (2844, '2025-06-22', 1, 29, 1, '2025-02-17 16:34:28', NULL, NULL, NULL, 38, 38, 21, 15, 9);
INSERT INTO `schedules` VALUES (2845, '2025-06-26', 1, 29, 1, '2025-02-17 16:34:28', NULL, NULL, NULL, 39, 39, 21, 15, 9);
INSERT INTO `schedules` VALUES (2846, '2025-06-29', 1, 29, 1, '2025-02-17 16:34:28', NULL, NULL, NULL, 40, 40, 21, 15, 9);
INSERT INTO `schedules` VALUES (2847, '2025-07-03', 1, 29, 1, '2025-02-17 16:34:28', NULL, NULL, NULL, 41, 41, 21, 15, 9);
INSERT INTO `schedules` VALUES (2848, '2025-07-06', 1, 29, 1, '2025-02-17 16:34:28', NULL, NULL, NULL, 42, 42, 21, 15, 9);
INSERT INTO `schedules` VALUES (2849, '2025-07-10', 1, 29, 1, '2025-02-17 16:34:28', NULL, NULL, NULL, 43, 43, 21, 15, 9);
INSERT INTO `schedules` VALUES (2850, '2025-07-13', 1, 29, 1, '2025-02-17 16:34:28', NULL, NULL, NULL, 44, 44, 21, 15, 9);
INSERT INTO `schedules` VALUES (2851, '2025-07-17', 1, 29, 1, '2025-02-17 16:34:28', NULL, NULL, NULL, 45, 45, 21, 15, 9);
INSERT INTO `schedules` VALUES (2852, '2025-07-20', 1, 29, 1, '2025-02-17 16:34:28', NULL, NULL, NULL, 46, 46, 21, 15, 9);
INSERT INTO `schedules` VALUES (2853, '2025-07-24', 1, 29, 1, '2025-02-17 16:34:28', NULL, NULL, NULL, 47, 47, 21, 15, 9);
INSERT INTO `schedules` VALUES (2854, '2025-07-27', 1, 29, 1, '2025-02-17 16:34:28', NULL, NULL, NULL, 48, 48, 21, 15, 9);
INSERT INTO `schedules` VALUES (2855, '2025-07-31', 1, 29, 1, '2025-02-17 16:34:28', NULL, NULL, NULL, 49, 49, 21, 15, 9);
INSERT INTO `schedules` VALUES (2856, '2025-08-03', 1, 29, 1, '2025-02-17 16:34:28', NULL, NULL, NULL, 50, 50, 21, 15, 9);
INSERT INTO `schedules` VALUES (2857, '2025-08-07', 1, 29, 1, '2025-02-17 16:34:28', NULL, NULL, NULL, 51, 51, 21, 15, 9);
INSERT INTO `schedules` VALUES (2858, '2025-08-10', 1, 29, 1, '2025-02-17 16:34:28', NULL, NULL, NULL, 52, 52, 21, 15, 9);
INSERT INTO `schedules` VALUES (2859, '2025-08-14', 1, 29, 1, '2025-02-17 16:34:28', NULL, NULL, NULL, 53, 53, 21, 15, 9);
INSERT INTO `schedules` VALUES (2860, '2025-08-17', 1, 29, 1, '2025-02-17 16:34:28', NULL, NULL, NULL, 54, 54, 21, 15, 9);
INSERT INTO `schedules` VALUES (2861, '2025-08-21', 1, 29, 1, '2025-02-17 16:34:28', NULL, NULL, NULL, 55, 55, 21, 15, 9);
INSERT INTO `schedules` VALUES (2862, '2025-08-24', 1, 29, 1, '2025-02-17 16:34:28', NULL, NULL, NULL, 56, 56, 21, 15, 9);
INSERT INTO `schedules` VALUES (2863, '2025-08-28', 1, 29, 1, '2025-02-17 16:34:28', NULL, NULL, NULL, 57, 57, 21, 15, 9);
INSERT INTO `schedules` VALUES (2864, '2025-08-31', 1, 29, 1, '2025-02-17 16:34:28', NULL, NULL, NULL, 58, 58, 21, 15, 9);
INSERT INTO `schedules` VALUES (2865, '2025-09-04', 1, 29, 1, '2025-02-17 16:34:28', NULL, NULL, NULL, 59, 59, 21, 15, 9);
INSERT INTO `schedules` VALUES (2866, '2025-09-07', 1, 29, 1, '2025-02-17 16:34:28', NULL, NULL, NULL, 60, 60, 21, 15, 9);
INSERT INTO `schedules` VALUES (2867, '2025-09-11', 1, 29, 1, '2025-02-17 16:34:28', NULL, NULL, NULL, 61, 61, 21, 15, 9);
INSERT INTO `schedules` VALUES (2868, '2025-09-14', 1, 29, 1, '2025-02-17 16:34:28', NULL, NULL, NULL, 62, 62, 21, 15, 9);
INSERT INTO `schedules` VALUES (2869, '2025-09-18', 1, 29, 1, '2025-02-17 16:34:28', NULL, NULL, NULL, 63, 63, 21, 15, 9);
INSERT INTO `schedules` VALUES (2870, '2025-09-21', 1, 29, 1, '2025-02-17 16:34:28', NULL, NULL, NULL, 64, 64, 21, 15, 9);
INSERT INTO `schedules` VALUES (2871, '2025-09-25', 1, 29, 1, '2025-02-17 16:34:28', NULL, NULL, NULL, 65, 65, 21, 15, 9);
INSERT INTO `schedules` VALUES (2872, '2025-09-28', 1, 29, 1, '2025-02-17 16:34:28', NULL, NULL, NULL, 66, 66, 21, 15, 9);
INSERT INTO `schedules` VALUES (2873, '2025-10-02', 1, 29, 1, '2025-02-17 16:34:28', NULL, NULL, NULL, 67, 67, 21, 15, 9);
INSERT INTO `schedules` VALUES (2874, '2025-10-05', 1, 29, 1, '2025-02-17 16:34:28', NULL, NULL, NULL, 68, 68, 21, 15, 9);
INSERT INTO `schedules` VALUES (2875, '2025-10-09', 1, 29, 1, '2025-02-17 16:34:28', NULL, NULL, NULL, 69, 69, 21, 15, 9);
INSERT INTO `schedules` VALUES (2876, '2025-10-12', 1, 29, 1, '2025-02-17 16:34:28', NULL, NULL, NULL, 70, 70, 21, 15, 9);
INSERT INTO `schedules` VALUES (2877, '2025-10-16', 1, 29, 1, '2025-02-17 16:34:28', NULL, NULL, NULL, 71, 71, 21, 15, 9);
INSERT INTO `schedules` VALUES (2878, '2025-10-19', 1, 29, 1, '2025-02-17 16:34:28', NULL, NULL, NULL, 72, 72, 21, 15, 9);
INSERT INTO `schedules` VALUES (2879, '2025-10-23', 1, 29, 1, '2025-02-17 16:34:28', NULL, NULL, NULL, 73, 73, 21, 15, 9);
INSERT INTO `schedules` VALUES (2880, '2025-10-26', 1, 29, 1, '2025-02-17 16:34:28', NULL, NULL, NULL, 74, 74, 21, 15, 9);
INSERT INTO `schedules` VALUES (2881, '2025-10-30', 1, 29, 1, '2025-02-17 16:34:28', NULL, NULL, NULL, 75, 75, 21, 15, 9);
INSERT INTO `schedules` VALUES (2882, '2025-11-02', 1, 29, 1, '2025-02-17 16:34:28', NULL, NULL, NULL, 76, 76, 21, 15, 9);
INSERT INTO `schedules` VALUES (2883, '2025-11-06', 1, 29, 1, '2025-02-17 16:34:28', NULL, NULL, NULL, 77, 77, 21, 15, 9);
INSERT INTO `schedules` VALUES (2884, '2025-11-09', 1, 29, 1, '2025-02-17 16:34:28', NULL, NULL, NULL, 78, 78, 21, 15, 9);
INSERT INTO `schedules` VALUES (2885, '2025-11-13', 1, 29, 1, '2025-02-17 16:34:28', NULL, NULL, NULL, 79, 79, 21, 15, 9);
INSERT INTO `schedules` VALUES (2886, '2025-11-16', 1, 29, 1, '2025-02-17 16:34:28', NULL, NULL, NULL, 80, 80, 21, 15, 9);
INSERT INTO `schedules` VALUES (2887, '2025-11-20', 1, 29, 1, '2025-02-17 16:34:28', NULL, NULL, NULL, 81, 81, 21, 15, 9);
INSERT INTO `schedules` VALUES (2888, '2025-11-23', 1, 29, 1, '2025-02-17 16:34:28', NULL, NULL, NULL, 82, 82, 21, 15, 9);
INSERT INTO `schedules` VALUES (2889, '2025-11-27', 1, 29, 1, '2025-02-17 16:34:28', NULL, NULL, NULL, 83, 83, 21, 15, 9);
INSERT INTO `schedules` VALUES (2890, '2025-11-30', 1, 29, 1, '2025-02-17 16:34:28', NULL, NULL, NULL, 84, 84, 21, 15, 9);
INSERT INTO `schedules` VALUES (2891, '2025-12-04', 1, 29, 1, '2025-02-17 16:34:28', NULL, NULL, NULL, 85, 85, 21, 15, 9);
INSERT INTO `schedules` VALUES (2892, '2025-12-07', 1, 29, 1, '2025-02-17 16:34:28', NULL, NULL, NULL, 86, 86, 21, 15, 9);
INSERT INTO `schedules` VALUES (2893, '2025-12-11', 1, 29, 1, '2025-02-17 16:34:28', NULL, NULL, NULL, 87, 87, 21, 15, 9);
INSERT INTO `schedules` VALUES (2894, '2025-12-14', 1, 29, 1, '2025-02-17 16:34:28', NULL, NULL, NULL, 88, 88, 21, 15, 9);
INSERT INTO `schedules` VALUES (2895, '2025-12-18', 1, 29, 1, '2025-02-17 16:34:28', NULL, NULL, NULL, 89, 89, 21, 15, 9);
INSERT INTO `schedules` VALUES (2896, '2025-12-21', 1, 29, 1, '2025-02-17 16:34:28', NULL, NULL, NULL, 90, 90, 21, 15, 9);
INSERT INTO `schedules` VALUES (2897, '2025-12-25', 1, 29, 1, '2025-02-17 16:34:28', NULL, NULL, NULL, 91, 91, 21, 15, 9);
INSERT INTO `schedules` VALUES (2898, '2025-12-28', 1, 29, 1, '2025-02-17 16:34:28', NULL, NULL, NULL, 92, 92, 21, 15, 9);
INSERT INTO `schedules` VALUES (2899, '2026-01-01', 1, 29, 1, '2025-02-17 16:34:28', NULL, NULL, NULL, 93, 93, 21, 15, 9);
INSERT INTO `schedules` VALUES (2900, '2026-01-04', 1, 29, 1, '2025-02-17 16:34:28', NULL, NULL, NULL, 94, 94, 21, 15, 9);
INSERT INTO `schedules` VALUES (2901, '2026-01-08', 1, 29, 1, '2025-02-17 16:34:28', NULL, NULL, NULL, 95, 95, 21, 15, 9);
INSERT INTO `schedules` VALUES (2902, '2026-01-11', 1, 29, 1, '2025-02-17 16:34:28', NULL, NULL, NULL, 96, 96, 21, 15, 9);
INSERT INTO `schedules` VALUES (2903, '2025-02-15', 1, 30, 1, '2025-02-17 16:37:25', NULL, NULL, NULL, 1, 1, 22, 15, 9);
INSERT INTO `schedules` VALUES (2904, '2025-02-16', 1, 30, 1, '2025-02-17 16:37:25', NULL, NULL, NULL, 2, 2, 22, 15, 9);
INSERT INTO `schedules` VALUES (2905, '2025-02-22', 1, 30, 1, '2025-02-17 16:37:25', NULL, NULL, NULL, 3, 3, 22, 15, 9);
INSERT INTO `schedules` VALUES (2906, '2025-02-23', 1, 30, 1, '2025-02-17 16:37:25', NULL, NULL, NULL, 4, 4, 22, 15, 9);
INSERT INTO `schedules` VALUES (2907, '2025-03-01', 1, 30, 1, '2025-02-17 16:37:25', NULL, NULL, NULL, 5, 5, 22, 15, 9);
INSERT INTO `schedules` VALUES (2908, '2025-03-02', 1, 30, 1, '2025-02-17 16:37:25', NULL, NULL, NULL, 6, 6, 22, 15, 9);
INSERT INTO `schedules` VALUES (2909, '2025-03-08', 1, 30, 1, '2025-02-17 16:37:25', NULL, NULL, NULL, 7, 7, 22, 15, 9);
INSERT INTO `schedules` VALUES (2910, '2025-03-09', 1, 30, 1, '2025-02-17 16:37:25', NULL, NULL, NULL, 8, 8, 22, 15, 9);
INSERT INTO `schedules` VALUES (2911, '2025-03-15', 1, 30, 1, '2025-02-17 16:37:25', NULL, NULL, NULL, 9, 9, 22, 15, 9);
INSERT INTO `schedules` VALUES (2912, '2025-03-16', 1, 30, 1, '2025-02-17 16:37:25', NULL, NULL, NULL, 10, 10, 22, 15, 9);
INSERT INTO `schedules` VALUES (2913, '2025-03-22', 1, 30, 1, '2025-02-17 16:37:25', NULL, NULL, NULL, 11, 11, 22, 15, 9);
INSERT INTO `schedules` VALUES (2914, '2025-03-23', 1, 30, 1, '2025-02-17 16:37:25', NULL, NULL, NULL, 12, 12, 22, 15, 9);
INSERT INTO `schedules` VALUES (2915, '2025-03-29', 1, 30, 1, '2025-02-17 16:37:25', NULL, NULL, NULL, 13, 13, 22, 15, 9);
INSERT INTO `schedules` VALUES (2916, '2025-03-30', 1, 30, 1, '2025-02-17 16:37:25', NULL, NULL, NULL, 14, 14, 22, 15, 9);
INSERT INTO `schedules` VALUES (2917, '2025-04-05', 1, 30, 1, '2025-02-17 16:37:25', NULL, NULL, NULL, 15, 15, 22, 15, 9);
INSERT INTO `schedules` VALUES (2918, '2025-04-06', 1, 30, 1, '2025-02-17 16:37:25', NULL, NULL, NULL, 16, 16, 22, 15, 9);
INSERT INTO `schedules` VALUES (2919, '2025-04-12', 1, 30, 1, '2025-02-17 16:37:25', NULL, NULL, NULL, 17, 17, 22, 15, 9);
INSERT INTO `schedules` VALUES (2920, '2025-04-13', 1, 30, 1, '2025-02-17 16:37:25', NULL, NULL, NULL, 18, 18, 22, 15, 9);
INSERT INTO `schedules` VALUES (2921, '2025-04-19', 1, 30, 1, '2025-02-17 16:37:25', NULL, NULL, NULL, 19, 19, 22, 15, 9);
INSERT INTO `schedules` VALUES (2922, '2025-04-20', 1, 30, 1, '2025-02-17 16:37:26', NULL, NULL, NULL, 20, 20, 22, 15, 9);
INSERT INTO `schedules` VALUES (2923, '2025-04-26', 1, 30, 1, '2025-02-17 16:37:26', NULL, NULL, NULL, 21, 21, 22, 15, 9);
INSERT INTO `schedules` VALUES (2924, '2025-04-27', 1, 30, 1, '2025-02-17 16:37:26', NULL, NULL, NULL, 22, 22, 22, 15, 9);
INSERT INTO `schedules` VALUES (2925, '2025-05-03', 1, 30, 1, '2025-02-17 16:37:26', NULL, NULL, NULL, 23, 23, 22, 15, 9);
INSERT INTO `schedules` VALUES (2926, '2025-05-04', 1, 30, 1, '2025-02-17 16:37:26', NULL, NULL, NULL, 24, 24, 22, 15, 9);
INSERT INTO `schedules` VALUES (2927, '2025-05-10', 1, 30, 1, '2025-02-17 16:37:26', NULL, NULL, NULL, 25, 25, 22, 15, 9);
INSERT INTO `schedules` VALUES (2928, '2025-05-11', 1, 30, 1, '2025-02-17 16:37:26', NULL, NULL, NULL, 26, 26, 22, 15, 9);
INSERT INTO `schedules` VALUES (2929, '2025-05-17', 1, 30, 1, '2025-02-17 16:37:26', NULL, NULL, NULL, 27, 27, 22, 15, 9);
INSERT INTO `schedules` VALUES (2930, '2025-05-18', 1, 30, 1, '2025-02-17 16:37:26', NULL, NULL, NULL, 28, 28, 22, 15, 9);
INSERT INTO `schedules` VALUES (2931, '2025-05-24', 1, 30, 1, '2025-02-17 16:37:26', NULL, NULL, NULL, 29, 29, 22, 15, 9);
INSERT INTO `schedules` VALUES (2932, '2025-05-25', 1, 30, 1, '2025-02-17 16:37:26', NULL, NULL, NULL, 30, 30, 22, 15, 9);
INSERT INTO `schedules` VALUES (2933, '2025-05-31', 1, 30, 1, '2025-02-17 16:37:26', NULL, NULL, NULL, 31, 31, 22, 15, 9);
INSERT INTO `schedules` VALUES (2934, '2025-06-01', 1, 30, 1, '2025-02-17 16:37:26', NULL, NULL, NULL, 32, 32, 22, 15, 9);
INSERT INTO `schedules` VALUES (2935, '2025-06-07', 1, 30, 1, '2025-02-17 16:37:26', NULL, NULL, NULL, 33, 33, 22, 15, 9);
INSERT INTO `schedules` VALUES (2936, '2025-06-08', 1, 30, 1, '2025-02-17 16:37:26', NULL, NULL, NULL, 34, 34, 22, 15, 9);
INSERT INTO `schedules` VALUES (2937, '2025-06-14', 1, 30, 1, '2025-02-17 16:37:26', NULL, NULL, NULL, 35, 35, 22, 15, 9);
INSERT INTO `schedules` VALUES (2938, '2025-06-15', 1, 30, 1, '2025-02-17 16:37:26', NULL, NULL, NULL, 36, 36, 22, 15, 9);
INSERT INTO `schedules` VALUES (2939, '2025-06-21', 1, 30, 1, '2025-02-17 16:37:26', NULL, NULL, NULL, 37, 37, 22, 15, 9);
INSERT INTO `schedules` VALUES (2940, '2025-06-22', 1, 30, 1, '2025-02-17 16:37:26', NULL, NULL, NULL, 38, 38, 22, 15, 9);
INSERT INTO `schedules` VALUES (2941, '2025-06-28', 1, 30, 1, '2025-02-17 16:37:26', NULL, NULL, NULL, 39, 39, 22, 15, 9);
INSERT INTO `schedules` VALUES (2942, '2025-06-29', 1, 30, 1, '2025-02-17 16:37:26', NULL, NULL, NULL, 40, 40, 22, 15, 9);
INSERT INTO `schedules` VALUES (2943, '2025-07-05', 1, 30, 1, '2025-02-17 16:37:26', NULL, NULL, NULL, 41, 41, 22, 15, 9);
INSERT INTO `schedules` VALUES (2944, '2025-07-06', 1, 30, 1, '2025-02-17 16:37:26', NULL, NULL, NULL, 42, 42, 22, 15, 9);
INSERT INTO `schedules` VALUES (2945, '2025-07-12', 1, 30, 1, '2025-02-17 16:37:26', NULL, NULL, NULL, 43, 43, 22, 15, 9);
INSERT INTO `schedules` VALUES (2946, '2025-07-13', 1, 30, 1, '2025-02-17 16:37:26', NULL, NULL, NULL, 44, 44, 22, 15, 9);
INSERT INTO `schedules` VALUES (2947, '2025-07-19', 1, 30, 1, '2025-02-17 16:37:26', NULL, NULL, NULL, 45, 45, 22, 15, 9);
INSERT INTO `schedules` VALUES (2948, '2025-07-20', 1, 30, 1, '2025-02-17 16:37:26', NULL, NULL, NULL, 46, 46, 22, 15, 9);
INSERT INTO `schedules` VALUES (2949, '2025-07-26', 1, 30, 1, '2025-02-17 16:37:26', NULL, NULL, NULL, 47, 47, 22, 15, 9);
INSERT INTO `schedules` VALUES (2950, '2025-07-27', 1, 30, 1, '2025-02-17 16:37:26', NULL, NULL, NULL, 48, 48, 22, 15, 9);
INSERT INTO `schedules` VALUES (2951, '2025-08-02', 1, 30, 1, '2025-02-17 16:37:26', NULL, NULL, NULL, 49, 49, 22, 15, 9);
INSERT INTO `schedules` VALUES (2952, '2025-08-03', 1, 30, 1, '2025-02-17 16:37:26', NULL, NULL, NULL, 50, 50, 22, 15, 9);
INSERT INTO `schedules` VALUES (2953, '2025-08-09', 1, 30, 1, '2025-02-17 16:37:26', NULL, NULL, NULL, 51, 51, 22, 15, 9);
INSERT INTO `schedules` VALUES (2954, '2025-08-10', 1, 30, 1, '2025-02-17 16:37:26', NULL, NULL, NULL, 52, 52, 22, 15, 9);
INSERT INTO `schedules` VALUES (2955, '2025-08-16', 1, 30, 1, '2025-02-17 16:37:26', NULL, NULL, NULL, 53, 53, 22, 15, 9);
INSERT INTO `schedules` VALUES (2956, '2025-08-17', 1, 30, 1, '2025-02-17 16:37:26', NULL, NULL, NULL, 54, 54, 22, 15, 9);
INSERT INTO `schedules` VALUES (2957, '2025-08-23', 1, 30, 1, '2025-02-17 16:37:26', NULL, NULL, NULL, 55, 55, 22, 15, 9);
INSERT INTO `schedules` VALUES (2958, '2025-08-24', 1, 30, 1, '2025-02-17 16:37:26', NULL, NULL, NULL, 56, 56, 22, 15, 9);
INSERT INTO `schedules` VALUES (2959, '2025-08-30', 1, 30, 1, '2025-02-17 16:37:26', NULL, NULL, NULL, 57, 57, 22, 15, 9);
INSERT INTO `schedules` VALUES (2960, '2025-08-31', 1, 30, 1, '2025-02-17 16:37:26', NULL, NULL, NULL, 58, 58, 22, 15, 9);
INSERT INTO `schedules` VALUES (2961, '2025-09-06', 1, 30, 1, '2025-02-17 16:37:26', NULL, NULL, NULL, 59, 59, 22, 15, 9);
INSERT INTO `schedules` VALUES (2962, '2025-09-07', 1, 30, 1, '2025-02-17 16:37:26', NULL, NULL, NULL, 60, 60, 22, 15, 9);
INSERT INTO `schedules` VALUES (2963, '2025-09-13', 1, 30, 1, '2025-02-17 16:37:26', NULL, NULL, NULL, 61, 61, 22, 15, 9);
INSERT INTO `schedules` VALUES (2964, '2025-09-14', 1, 30, 1, '2025-02-17 16:37:26', NULL, NULL, NULL, 62, 62, 22, 15, 9);
INSERT INTO `schedules` VALUES (2965, '2025-09-20', 1, 30, 1, '2025-02-17 16:37:26', NULL, NULL, NULL, 63, 63, 22, 15, 9);
INSERT INTO `schedules` VALUES (2966, '2025-09-21', 1, 30, 1, '2025-02-17 16:37:26', NULL, NULL, NULL, 64, 64, 22, 15, 9);
INSERT INTO `schedules` VALUES (2967, '2025-09-27', 1, 30, 1, '2025-02-17 16:37:26', NULL, NULL, NULL, 65, 65, 22, 15, 9);
INSERT INTO `schedules` VALUES (2968, '2025-09-28', 1, 30, 1, '2025-02-17 16:37:26', NULL, NULL, NULL, 66, 66, 22, 15, 9);
INSERT INTO `schedules` VALUES (2969, '2025-10-04', 1, 30, 1, '2025-02-17 16:37:26', NULL, NULL, NULL, 67, 67, 22, 15, 9);
INSERT INTO `schedules` VALUES (2970, '2025-10-05', 1, 30, 1, '2025-02-17 16:37:26', NULL, NULL, NULL, 68, 68, 22, 15, 9);
INSERT INTO `schedules` VALUES (2971, '2025-10-11', 1, 30, 1, '2025-02-17 16:37:26', NULL, NULL, NULL, 69, 69, 22, 15, 9);
INSERT INTO `schedules` VALUES (2972, '2025-10-12', 1, 30, 1, '2025-02-17 16:37:26', NULL, NULL, NULL, 70, 70, 22, 15, 9);
INSERT INTO `schedules` VALUES (2973, '2025-10-18', 1, 30, 1, '2025-02-17 16:37:26', NULL, NULL, NULL, 71, 71, 22, 15, 9);
INSERT INTO `schedules` VALUES (2974, '2025-10-19', 1, 30, 1, '2025-02-17 16:37:26', NULL, NULL, NULL, 72, 72, 22, 15, 9);
INSERT INTO `schedules` VALUES (2975, '2025-10-25', 1, 30, 1, '2025-02-17 16:37:26', NULL, NULL, NULL, 73, 73, 22, 15, 9);
INSERT INTO `schedules` VALUES (2976, '2025-10-26', 1, 30, 1, '2025-02-17 16:37:26', NULL, NULL, NULL, 74, 74, 22, 15, 9);
INSERT INTO `schedules` VALUES (2977, '2025-11-01', 1, 30, 1, '2025-02-17 16:37:26', NULL, NULL, NULL, 75, 75, 22, 15, 9);
INSERT INTO `schedules` VALUES (2978, '2025-11-02', 1, 30, 1, '2025-02-17 16:37:26', NULL, NULL, NULL, 76, 76, 22, 15, 9);
INSERT INTO `schedules` VALUES (2979, '2025-11-08', 1, 30, 1, '2025-02-17 16:37:26', NULL, NULL, NULL, 77, 77, 22, 15, 9);
INSERT INTO `schedules` VALUES (2980, '2025-11-09', 1, 30, 1, '2025-02-17 16:37:26', NULL, NULL, NULL, 78, 78, 22, 15, 9);
INSERT INTO `schedules` VALUES (2981, '2025-11-15', 1, 30, 1, '2025-02-17 16:37:26', NULL, NULL, NULL, 79, 79, 22, 15, 9);
INSERT INTO `schedules` VALUES (2982, '2025-11-16', 1, 30, 1, '2025-02-17 16:37:26', NULL, NULL, NULL, 80, 80, 22, 15, 9);
INSERT INTO `schedules` VALUES (2983, '2025-11-22', 1, 30, 1, '2025-02-17 16:37:26', NULL, NULL, NULL, 81, 81, 22, 15, 9);
INSERT INTO `schedules` VALUES (2984, '2025-11-23', 1, 30, 1, '2025-02-17 16:37:26', NULL, NULL, NULL, 82, 82, 22, 15, 9);
INSERT INTO `schedules` VALUES (2985, '2025-11-29', 1, 30, 1, '2025-02-17 16:37:26', NULL, NULL, NULL, 83, 83, 22, 15, 9);
INSERT INTO `schedules` VALUES (2986, '2025-11-30', 1, 30, 1, '2025-02-17 16:37:26', NULL, NULL, NULL, 84, 84, 22, 15, 9);
INSERT INTO `schedules` VALUES (2987, '2025-12-06', 1, 30, 1, '2025-02-17 16:37:26', NULL, NULL, NULL, 85, 85, 22, 15, 9);
INSERT INTO `schedules` VALUES (2988, '2025-12-07', 1, 30, 1, '2025-02-17 16:37:26', NULL, NULL, NULL, 86, 86, 22, 15, 9);
INSERT INTO `schedules` VALUES (2989, '2025-12-13', 1, 30, 1, '2025-02-17 16:37:26', NULL, NULL, NULL, 87, 87, 22, 15, 9);
INSERT INTO `schedules` VALUES (2990, '2025-12-14', 1, 30, 1, '2025-02-17 16:37:26', NULL, NULL, NULL, 88, 88, 22, 15, 9);
INSERT INTO `schedules` VALUES (2991, '2025-12-20', 1, 30, 1, '2025-02-17 16:37:26', NULL, NULL, NULL, 89, 89, 22, 15, 9);
INSERT INTO `schedules` VALUES (2992, '2025-12-21', 1, 30, 1, '2025-02-17 16:37:26', NULL, NULL, NULL, 90, 90, 22, 15, 9);
INSERT INTO `schedules` VALUES (2993, '2025-12-27', 1, 30, 1, '2025-02-17 16:37:26', NULL, NULL, NULL, 91, 91, 22, 15, 9);
INSERT INTO `schedules` VALUES (2994, '2025-12-28', 1, 30, 1, '2025-02-17 16:37:26', NULL, NULL, NULL, 92, 92, 22, 15, 9);
INSERT INTO `schedules` VALUES (2995, '2026-01-03', 1, 30, 1, '2025-02-17 16:37:26', NULL, NULL, NULL, 93, 93, 22, 15, 9);
INSERT INTO `schedules` VALUES (2996, '2026-01-04', 1, 30, 1, '2025-02-17 16:37:26', NULL, NULL, NULL, 94, 94, 22, 15, 9);
INSERT INTO `schedules` VALUES (2997, '2026-01-10', 1, 30, 1, '2025-02-17 16:37:26', NULL, NULL, NULL, 95, 95, 22, 15, 9);
INSERT INTO `schedules` VALUES (2998, '2026-01-11', 1, 30, 1, '2025-02-17 16:37:26', NULL, NULL, NULL, 96, 96, 22, 15, 9);
INSERT INTO `schedules` VALUES (2999, '2025-02-15', 1, 31, 1, '2025-02-17 16:38:02', NULL, NULL, NULL, 1, 1, 21, 15, 9);
INSERT INTO `schedules` VALUES (3000, '2025-02-16', 1, 31, 1, '2025-02-17 16:38:02', NULL, NULL, NULL, 2, 2, 21, 15, 9);
INSERT INTO `schedules` VALUES (3001, '2025-02-22', 1, 31, 1, '2025-02-17 16:38:02', NULL, NULL, NULL, 3, 3, 21, 15, 9);
INSERT INTO `schedules` VALUES (3002, '2025-02-23', 1, 31, 1, '2025-02-17 16:38:02', NULL, NULL, NULL, 4, 4, 21, 15, 9);
INSERT INTO `schedules` VALUES (3003, '2025-03-01', 1, 31, 1, '2025-02-17 16:38:02', NULL, NULL, NULL, 5, 5, 21, 15, 9);
INSERT INTO `schedules` VALUES (3004, '2025-03-02', 1, 31, 1, '2025-02-17 16:38:02', NULL, NULL, NULL, 6, 6, 21, 15, 9);
INSERT INTO `schedules` VALUES (3005, '2025-03-08', 1, 31, 1, '2025-02-17 16:38:02', NULL, NULL, NULL, 7, 7, 21, 15, 9);
INSERT INTO `schedules` VALUES (3006, '2025-03-09', 1, 31, 1, '2025-02-17 16:38:02', NULL, NULL, NULL, 8, 8, 21, 15, 9);
INSERT INTO `schedules` VALUES (3007, '2025-03-15', 1, 31, 1, '2025-02-17 16:38:02', NULL, NULL, NULL, 9, 9, 21, 15, 9);
INSERT INTO `schedules` VALUES (3008, '2025-03-16', 1, 31, 1, '2025-02-17 16:38:02', NULL, NULL, NULL, 10, 10, 21, 15, 9);
INSERT INTO `schedules` VALUES (3009, '2025-03-22', 1, 31, 1, '2025-02-17 16:38:02', NULL, NULL, NULL, 11, 11, 21, 15, 9);
INSERT INTO `schedules` VALUES (3010, '2025-03-23', 1, 31, 1, '2025-02-17 16:38:02', NULL, NULL, NULL, 12, 12, 21, 15, 9);
INSERT INTO `schedules` VALUES (3011, '2025-03-29', 1, 31, 1, '2025-02-17 16:38:02', NULL, NULL, NULL, 13, 13, 21, 15, 9);
INSERT INTO `schedules` VALUES (3012, '2025-03-30', 1, 31, 1, '2025-02-17 16:38:02', NULL, NULL, NULL, 14, 14, 21, 15, 9);
INSERT INTO `schedules` VALUES (3013, '2025-04-05', 1, 31, 1, '2025-02-17 16:38:02', NULL, NULL, NULL, 15, 15, 21, 15, 9);
INSERT INTO `schedules` VALUES (3014, '2025-04-06', 1, 31, 1, '2025-02-17 16:38:02', NULL, NULL, NULL, 16, 16, 21, 15, 9);
INSERT INTO `schedules` VALUES (3015, '2025-04-12', 1, 31, 1, '2025-02-17 16:38:02', NULL, NULL, NULL, 17, 17, 21, 15, 9);
INSERT INTO `schedules` VALUES (3016, '2025-04-13', 1, 31, 1, '2025-02-17 16:38:02', NULL, NULL, NULL, 18, 18, 21, 15, 9);
INSERT INTO `schedules` VALUES (3017, '2025-04-19', 1, 31, 1, '2025-02-17 16:38:02', NULL, NULL, NULL, 19, 19, 21, 15, 9);
INSERT INTO `schedules` VALUES (3018, '2025-04-20', 1, 31, 1, '2025-02-17 16:38:02', NULL, NULL, NULL, 20, 20, 21, 15, 9);
INSERT INTO `schedules` VALUES (3019, '2025-04-26', 1, 31, 1, '2025-02-17 16:38:02', NULL, NULL, NULL, 21, 21, 21, 15, 9);
INSERT INTO `schedules` VALUES (3020, '2025-04-27', 1, 31, 1, '2025-02-17 16:38:02', NULL, NULL, NULL, 22, 22, 21, 15, 9);
INSERT INTO `schedules` VALUES (3021, '2025-05-03', 1, 31, 1, '2025-02-17 16:38:02', NULL, NULL, NULL, 23, 23, 21, 15, 9);
INSERT INTO `schedules` VALUES (3022, '2025-05-04', 1, 31, 1, '2025-02-17 16:38:02', NULL, NULL, NULL, 24, 24, 21, 15, 9);
INSERT INTO `schedules` VALUES (3023, '2025-05-10', 1, 31, 1, '2025-02-17 16:38:02', NULL, NULL, NULL, 25, 25, 21, 15, 9);
INSERT INTO `schedules` VALUES (3024, '2025-05-11', 1, 31, 1, '2025-02-17 16:38:02', NULL, NULL, NULL, 26, 26, 21, 15, 9);
INSERT INTO `schedules` VALUES (3025, '2025-05-17', 1, 31, 1, '2025-02-17 16:38:02', NULL, NULL, NULL, 27, 27, 21, 15, 9);
INSERT INTO `schedules` VALUES (3026, '2025-05-18', 1, 31, 1, '2025-02-17 16:38:02', NULL, NULL, NULL, 28, 28, 21, 15, 9);
INSERT INTO `schedules` VALUES (3027, '2025-05-24', 1, 31, 1, '2025-02-17 16:38:02', NULL, NULL, NULL, 29, 29, 21, 15, 9);
INSERT INTO `schedules` VALUES (3028, '2025-05-25', 1, 31, 1, '2025-02-17 16:38:02', NULL, NULL, NULL, 30, 30, 21, 15, 9);
INSERT INTO `schedules` VALUES (3029, '2025-05-31', 1, 31, 1, '2025-02-17 16:38:02', NULL, NULL, NULL, 31, 31, 21, 15, 9);
INSERT INTO `schedules` VALUES (3030, '2025-06-01', 1, 31, 1, '2025-02-17 16:38:02', NULL, NULL, NULL, 32, 32, 21, 15, 9);
INSERT INTO `schedules` VALUES (3031, '2025-06-07', 1, 31, 1, '2025-02-17 16:38:02', NULL, NULL, NULL, 33, 33, 21, 15, 9);
INSERT INTO `schedules` VALUES (3032, '2025-06-08', 1, 31, 1, '2025-02-17 16:38:02', NULL, NULL, NULL, 34, 34, 21, 15, 9);
INSERT INTO `schedules` VALUES (3033, '2025-06-14', 1, 31, 1, '2025-02-17 16:38:02', NULL, NULL, NULL, 35, 35, 21, 15, 9);
INSERT INTO `schedules` VALUES (3034, '2025-06-15', 1, 31, 1, '2025-02-17 16:38:02', NULL, NULL, NULL, 36, 36, 21, 15, 9);
INSERT INTO `schedules` VALUES (3035, '2025-06-21', 1, 31, 1, '2025-02-17 16:38:02', NULL, NULL, NULL, 37, 37, 21, 15, 9);
INSERT INTO `schedules` VALUES (3036, '2025-06-22', 1, 31, 1, '2025-02-17 16:38:02', NULL, NULL, NULL, 38, 38, 21, 15, 9);
INSERT INTO `schedules` VALUES (3037, '2025-06-28', 1, 31, 1, '2025-02-17 16:38:02', NULL, NULL, NULL, 39, 39, 21, 15, 9);
INSERT INTO `schedules` VALUES (3038, '2025-06-29', 1, 31, 1, '2025-02-17 16:38:02', NULL, NULL, NULL, 40, 40, 21, 15, 9);
INSERT INTO `schedules` VALUES (3039, '2025-07-05', 1, 31, 1, '2025-02-17 16:38:02', NULL, NULL, NULL, 41, 41, 21, 15, 9);
INSERT INTO `schedules` VALUES (3040, '2025-07-06', 1, 31, 1, '2025-02-17 16:38:02', NULL, NULL, NULL, 42, 42, 21, 15, 9);
INSERT INTO `schedules` VALUES (3041, '2025-07-12', 1, 31, 1, '2025-02-17 16:38:02', NULL, NULL, NULL, 43, 43, 21, 15, 9);
INSERT INTO `schedules` VALUES (3042, '2025-07-13', 1, 31, 1, '2025-02-17 16:38:02', NULL, NULL, NULL, 44, 44, 21, 15, 9);
INSERT INTO `schedules` VALUES (3043, '2025-07-19', 1, 31, 1, '2025-02-17 16:38:02', NULL, NULL, NULL, 45, 45, 21, 15, 9);
INSERT INTO `schedules` VALUES (3044, '2025-07-20', 1, 31, 1, '2025-02-17 16:38:02', NULL, NULL, NULL, 46, 46, 21, 15, 9);
INSERT INTO `schedules` VALUES (3045, '2025-07-26', 1, 31, 1, '2025-02-17 16:38:02', NULL, NULL, NULL, 47, 47, 21, 15, 9);
INSERT INTO `schedules` VALUES (3046, '2025-07-27', 1, 31, 1, '2025-02-17 16:38:02', NULL, NULL, NULL, 48, 48, 21, 15, 9);
INSERT INTO `schedules` VALUES (3047, '2025-08-02', 1, 31, 1, '2025-02-17 16:38:02', NULL, NULL, NULL, 49, 49, 21, 15, 9);
INSERT INTO `schedules` VALUES (3048, '2025-08-03', 1, 31, 1, '2025-02-17 16:38:02', NULL, NULL, NULL, 50, 50, 21, 15, 9);
INSERT INTO `schedules` VALUES (3049, '2025-08-09', 1, 31, 1, '2025-02-17 16:38:02', NULL, NULL, NULL, 51, 51, 21, 15, 9);
INSERT INTO `schedules` VALUES (3050, '2025-08-10', 1, 31, 1, '2025-02-17 16:38:02', NULL, NULL, NULL, 52, 52, 21, 15, 9);
INSERT INTO `schedules` VALUES (3051, '2025-08-16', 1, 31, 1, '2025-02-17 16:38:02', NULL, NULL, NULL, 53, 53, 21, 15, 9);
INSERT INTO `schedules` VALUES (3052, '2025-08-17', 1, 31, 1, '2025-02-17 16:38:02', NULL, NULL, NULL, 54, 54, 21, 15, 9);
INSERT INTO `schedules` VALUES (3053, '2025-08-23', 1, 31, 1, '2025-02-17 16:38:02', NULL, NULL, NULL, 55, 55, 21, 15, 9);
INSERT INTO `schedules` VALUES (3054, '2025-08-24', 1, 31, 1, '2025-02-17 16:38:02', NULL, NULL, NULL, 56, 56, 21, 15, 9);
INSERT INTO `schedules` VALUES (3055, '2025-08-30', 1, 31, 1, '2025-02-17 16:38:02', NULL, NULL, NULL, 57, 57, 21, 15, 9);
INSERT INTO `schedules` VALUES (3056, '2025-08-31', 1, 31, 1, '2025-02-17 16:38:02', NULL, NULL, NULL, 58, 58, 21, 15, 9);
INSERT INTO `schedules` VALUES (3057, '2025-09-06', 1, 31, 1, '2025-02-17 16:38:02', NULL, NULL, NULL, 59, 59, 21, 15, 9);
INSERT INTO `schedules` VALUES (3058, '2025-09-07', 1, 31, 1, '2025-02-17 16:38:02', NULL, NULL, NULL, 60, 60, 21, 15, 9);
INSERT INTO `schedules` VALUES (3059, '2025-09-13', 1, 31, 1, '2025-02-17 16:38:02', NULL, NULL, NULL, 61, 61, 21, 15, 9);
INSERT INTO `schedules` VALUES (3060, '2025-09-14', 1, 31, 1, '2025-02-17 16:38:02', NULL, NULL, NULL, 62, 62, 21, 15, 9);
INSERT INTO `schedules` VALUES (3061, '2025-09-20', 1, 31, 1, '2025-02-17 16:38:02', NULL, NULL, NULL, 63, 63, 21, 15, 9);
INSERT INTO `schedules` VALUES (3062, '2025-09-21', 1, 31, 1, '2025-02-17 16:38:02', NULL, NULL, NULL, 64, 64, 21, 15, 9);
INSERT INTO `schedules` VALUES (3063, '2025-09-27', 1, 31, 1, '2025-02-17 16:38:02', NULL, NULL, NULL, 65, 65, 21, 15, 9);
INSERT INTO `schedules` VALUES (3064, '2025-09-28', 1, 31, 1, '2025-02-17 16:38:02', NULL, NULL, NULL, 66, 66, 21, 15, 9);
INSERT INTO `schedules` VALUES (3065, '2025-10-04', 1, 31, 1, '2025-02-17 16:38:02', NULL, NULL, NULL, 67, 67, 21, 15, 9);
INSERT INTO `schedules` VALUES (3066, '2025-10-05', 1, 31, 1, '2025-02-17 16:38:02', NULL, NULL, NULL, 68, 68, 21, 15, 9);
INSERT INTO `schedules` VALUES (3067, '2025-10-11', 1, 31, 1, '2025-02-17 16:38:02', NULL, NULL, NULL, 69, 69, 21, 15, 9);
INSERT INTO `schedules` VALUES (3068, '2025-10-12', 1, 31, 1, '2025-02-17 16:38:02', NULL, NULL, NULL, 70, 70, 21, 15, 9);
INSERT INTO `schedules` VALUES (3069, '2025-10-18', 1, 31, 1, '2025-02-17 16:38:02', NULL, NULL, NULL, 71, 71, 21, 15, 9);
INSERT INTO `schedules` VALUES (3070, '2025-10-19', 1, 31, 1, '2025-02-17 16:38:02', NULL, NULL, NULL, 72, 72, 21, 15, 9);
INSERT INTO `schedules` VALUES (3071, '2025-10-25', 1, 31, 1, '2025-02-17 16:38:02', NULL, NULL, NULL, 73, 73, 21, 15, 9);
INSERT INTO `schedules` VALUES (3072, '2025-10-26', 1, 31, 1, '2025-02-17 16:38:02', NULL, NULL, NULL, 74, 74, 21, 15, 9);
INSERT INTO `schedules` VALUES (3073, '2025-11-01', 1, 31, 1, '2025-02-17 16:38:02', NULL, NULL, NULL, 75, 75, 21, 15, 9);
INSERT INTO `schedules` VALUES (3074, '2025-11-02', 1, 31, 1, '2025-02-17 16:38:02', NULL, NULL, NULL, 76, 76, 21, 15, 9);
INSERT INTO `schedules` VALUES (3075, '2025-11-08', 1, 31, 1, '2025-02-17 16:38:02', NULL, NULL, NULL, 77, 77, 21, 15, 9);
INSERT INTO `schedules` VALUES (3076, '2025-11-09', 1, 31, 1, '2025-02-17 16:38:02', NULL, NULL, NULL, 78, 78, 21, 15, 9);
INSERT INTO `schedules` VALUES (3077, '2025-11-15', 1, 31, 1, '2025-02-17 16:38:02', NULL, NULL, NULL, 79, 79, 21, 15, 9);
INSERT INTO `schedules` VALUES (3078, '2025-11-16', 1, 31, 1, '2025-02-17 16:38:02', NULL, NULL, NULL, 80, 80, 21, 15, 9);
INSERT INTO `schedules` VALUES (3079, '2025-11-22', 1, 31, 1, '2025-02-17 16:38:02', NULL, NULL, NULL, 81, 81, 21, 15, 9);
INSERT INTO `schedules` VALUES (3080, '2025-11-23', 1, 31, 1, '2025-02-17 16:38:02', NULL, NULL, NULL, 82, 82, 21, 15, 9);
INSERT INTO `schedules` VALUES (3081, '2025-11-29', 1, 31, 1, '2025-02-17 16:38:02', NULL, NULL, NULL, 83, 83, 21, 15, 9);
INSERT INTO `schedules` VALUES (3082, '2025-11-30', 1, 31, 1, '2025-02-17 16:38:02', NULL, NULL, NULL, 84, 84, 21, 15, 9);
INSERT INTO `schedules` VALUES (3083, '2025-12-06', 1, 31, 1, '2025-02-17 16:38:02', NULL, NULL, NULL, 85, 85, 21, 15, 9);
INSERT INTO `schedules` VALUES (3084, '2025-12-07', 1, 31, 1, '2025-02-17 16:38:02', NULL, NULL, NULL, 86, 86, 21, 15, 9);
INSERT INTO `schedules` VALUES (3085, '2025-12-13', 1, 31, 1, '2025-02-17 16:38:02', NULL, NULL, NULL, 87, 87, 21, 15, 9);
INSERT INTO `schedules` VALUES (3086, '2025-12-14', 1, 31, 1, '2025-02-17 16:38:02', NULL, NULL, NULL, 88, 88, 21, 15, 9);
INSERT INTO `schedules` VALUES (3087, '2025-12-20', 1, 31, 1, '2025-02-17 16:38:02', NULL, NULL, NULL, 89, 89, 21, 15, 9);
INSERT INTO `schedules` VALUES (3088, '2025-12-21', 1, 31, 1, '2025-02-17 16:38:02', NULL, NULL, NULL, 90, 90, 21, 15, 9);
INSERT INTO `schedules` VALUES (3089, '2025-12-27', 1, 31, 1, '2025-02-17 16:38:02', NULL, NULL, NULL, 91, 91, 21, 15, 9);
INSERT INTO `schedules` VALUES (3090, '2025-12-28', 1, 31, 1, '2025-02-17 16:38:02', NULL, NULL, NULL, 92, 92, 21, 15, 9);
INSERT INTO `schedules` VALUES (3091, '2026-01-03', 1, 31, 1, '2025-02-17 16:38:02', NULL, NULL, NULL, 93, 93, 21, 15, 9);
INSERT INTO `schedules` VALUES (3092, '2026-01-04', 1, 31, 1, '2025-02-17 16:38:02', NULL, NULL, NULL, 94, 94, 21, 15, 9);
INSERT INTO `schedules` VALUES (3093, '2026-01-10', 1, 31, 1, '2025-02-17 16:38:02', NULL, NULL, NULL, 95, 95, 21, 15, 9);
INSERT INTO `schedules` VALUES (3094, '2026-01-11', 1, 31, 1, '2025-02-17 16:38:02', NULL, NULL, NULL, 96, 96, 21, 15, 9);
INSERT INTO `schedules` VALUES (3095, '2025-02-15', 1, 32, 1, '2025-02-17 16:38:29', NULL, NULL, NULL, 1, 1, 22, 15, 9);
INSERT INTO `schedules` VALUES (3096, '2025-02-16', 1, 32, 1, '2025-02-17 16:38:29', NULL, NULL, NULL, 2, 2, 22, 15, 9);
INSERT INTO `schedules` VALUES (3097, '2025-02-22', 1, 32, 1, '2025-02-17 16:38:29', NULL, NULL, NULL, 3, 3, 22, 15, 9);
INSERT INTO `schedules` VALUES (3098, '2025-02-23', 1, 32, 1, '2025-02-17 16:38:29', NULL, NULL, NULL, 4, 4, 22, 15, 9);
INSERT INTO `schedules` VALUES (3099, '2025-03-01', 1, 32, 1, '2025-02-17 16:38:29', NULL, NULL, NULL, 5, 5, 22, 15, 9);
INSERT INTO `schedules` VALUES (3100, '2025-03-02', 1, 32, 1, '2025-02-17 16:38:29', NULL, NULL, NULL, 6, 6, 22, 15, 9);
INSERT INTO `schedules` VALUES (3101, '2025-03-08', 1, 32, 1, '2025-02-17 16:38:29', NULL, NULL, NULL, 7, 7, 22, 15, 9);
INSERT INTO `schedules` VALUES (3102, '2025-03-09', 1, 32, 1, '2025-02-17 16:38:29', NULL, NULL, NULL, 8, 8, 22, 15, 9);
INSERT INTO `schedules` VALUES (3103, '2025-03-15', 1, 32, 1, '2025-02-17 16:38:29', NULL, NULL, NULL, 9, 9, 22, 15, 9);
INSERT INTO `schedules` VALUES (3104, '2025-03-16', 1, 32, 1, '2025-02-17 16:38:29', NULL, NULL, NULL, 10, 10, 22, 15, 9);
INSERT INTO `schedules` VALUES (3105, '2025-03-22', 1, 32, 1, '2025-02-17 16:38:29', NULL, NULL, NULL, 11, 11, 22, 15, 9);
INSERT INTO `schedules` VALUES (3106, '2025-03-23', 1, 32, 1, '2025-02-17 16:38:29', NULL, NULL, NULL, 12, 12, 22, 15, 9);
INSERT INTO `schedules` VALUES (3107, '2025-03-29', 1, 32, 1, '2025-02-17 16:38:29', NULL, NULL, NULL, 13, 13, 22, 15, 9);
INSERT INTO `schedules` VALUES (3108, '2025-03-30', 1, 32, 1, '2025-02-17 16:38:29', NULL, NULL, NULL, 14, 14, 22, 15, 9);
INSERT INTO `schedules` VALUES (3109, '2025-04-05', 1, 32, 1, '2025-02-17 16:38:29', NULL, NULL, NULL, 15, 15, 22, 15, 9);
INSERT INTO `schedules` VALUES (3110, '2025-04-06', 1, 32, 1, '2025-02-17 16:38:29', NULL, NULL, NULL, 16, 16, 22, 15, 9);
INSERT INTO `schedules` VALUES (3111, '2025-04-12', 1, 32, 1, '2025-02-17 16:38:29', NULL, NULL, NULL, 17, 17, 22, 15, 9);
INSERT INTO `schedules` VALUES (3112, '2025-04-13', 1, 32, 1, '2025-02-17 16:38:29', NULL, NULL, NULL, 18, 18, 22, 15, 9);
INSERT INTO `schedules` VALUES (3113, '2025-04-19', 1, 32, 1, '2025-02-17 16:38:29', NULL, NULL, NULL, 19, 19, 22, 15, 9);
INSERT INTO `schedules` VALUES (3114, '2025-04-20', 1, 32, 1, '2025-02-17 16:38:29', NULL, NULL, NULL, 20, 20, 22, 15, 9);
INSERT INTO `schedules` VALUES (3115, '2025-04-26', 1, 32, 1, '2025-02-17 16:38:29', NULL, NULL, NULL, 21, 21, 22, 15, 9);
INSERT INTO `schedules` VALUES (3116, '2025-04-27', 1, 32, 1, '2025-02-17 16:38:29', NULL, NULL, NULL, 22, 22, 22, 15, 9);
INSERT INTO `schedules` VALUES (3117, '2025-05-03', 1, 32, 1, '2025-02-17 16:38:29', NULL, NULL, NULL, 23, 23, 22, 15, 9);
INSERT INTO `schedules` VALUES (3118, '2025-05-04', 1, 32, 1, '2025-02-17 16:38:29', NULL, NULL, NULL, 24, 24, 22, 15, 9);
INSERT INTO `schedules` VALUES (3119, '2025-05-10', 1, 32, 1, '2025-02-17 16:38:29', NULL, NULL, NULL, 25, 25, 22, 15, 9);
INSERT INTO `schedules` VALUES (3120, '2025-05-11', 1, 32, 1, '2025-02-17 16:38:29', NULL, NULL, NULL, 26, 26, 22, 15, 9);
INSERT INTO `schedules` VALUES (3121, '2025-05-17', 1, 32, 1, '2025-02-17 16:38:29', NULL, NULL, NULL, 27, 27, 22, 15, 9);
INSERT INTO `schedules` VALUES (3122, '2025-05-18', 1, 32, 1, '2025-02-17 16:38:29', NULL, NULL, NULL, 28, 28, 22, 15, 9);
INSERT INTO `schedules` VALUES (3123, '2025-05-24', 1, 32, 1, '2025-02-17 16:38:29', NULL, NULL, NULL, 29, 29, 22, 15, 9);
INSERT INTO `schedules` VALUES (3124, '2025-05-25', 1, 32, 1, '2025-02-17 16:38:29', NULL, NULL, NULL, 30, 30, 22, 15, 9);
INSERT INTO `schedules` VALUES (3125, '2025-05-31', 1, 32, 1, '2025-02-17 16:38:29', NULL, NULL, NULL, 31, 31, 22, 15, 9);
INSERT INTO `schedules` VALUES (3126, '2025-06-01', 1, 32, 1, '2025-02-17 16:38:29', NULL, NULL, NULL, 32, 32, 22, 15, 9);
INSERT INTO `schedules` VALUES (3127, '2025-06-07', 1, 32, 1, '2025-02-17 16:38:29', NULL, NULL, NULL, 33, 33, 22, 15, 9);
INSERT INTO `schedules` VALUES (3128, '2025-06-08', 1, 32, 1, '2025-02-17 16:38:29', NULL, NULL, NULL, 34, 34, 22, 15, 9);
INSERT INTO `schedules` VALUES (3129, '2025-06-14', 1, 32, 1, '2025-02-17 16:38:29', NULL, NULL, NULL, 35, 35, 22, 15, 9);
INSERT INTO `schedules` VALUES (3130, '2025-06-15', 1, 32, 1, '2025-02-17 16:38:29', NULL, NULL, NULL, 36, 36, 22, 15, 9);
INSERT INTO `schedules` VALUES (3131, '2025-06-21', 1, 32, 1, '2025-02-17 16:38:29', NULL, NULL, NULL, 37, 37, 22, 15, 9);
INSERT INTO `schedules` VALUES (3132, '2025-06-22', 1, 32, 1, '2025-02-17 16:38:29', NULL, NULL, NULL, 38, 38, 22, 15, 9);
INSERT INTO `schedules` VALUES (3133, '2025-06-28', 1, 32, 1, '2025-02-17 16:38:29', NULL, NULL, NULL, 39, 39, 22, 15, 9);
INSERT INTO `schedules` VALUES (3134, '2025-06-29', 1, 32, 1, '2025-02-17 16:38:29', NULL, NULL, NULL, 40, 40, 22, 15, 9);
INSERT INTO `schedules` VALUES (3135, '2025-07-05', 1, 32, 1, '2025-02-17 16:38:29', NULL, NULL, NULL, 41, 41, 22, 15, 9);
INSERT INTO `schedules` VALUES (3136, '2025-07-06', 1, 32, 1, '2025-02-17 16:38:29', NULL, NULL, NULL, 42, 42, 22, 15, 9);
INSERT INTO `schedules` VALUES (3137, '2025-07-12', 1, 32, 1, '2025-02-17 16:38:29', NULL, NULL, NULL, 43, 43, 22, 15, 9);
INSERT INTO `schedules` VALUES (3138, '2025-07-13', 1, 32, 1, '2025-02-17 16:38:29', NULL, NULL, NULL, 44, 44, 22, 15, 9);
INSERT INTO `schedules` VALUES (3139, '2025-07-19', 1, 32, 1, '2025-02-17 16:38:29', NULL, NULL, NULL, 45, 45, 22, 15, 9);
INSERT INTO `schedules` VALUES (3140, '2025-07-20', 1, 32, 1, '2025-02-17 16:38:29', NULL, NULL, NULL, 46, 46, 22, 15, 9);
INSERT INTO `schedules` VALUES (3141, '2025-07-26', 1, 32, 1, '2025-02-17 16:38:29', NULL, NULL, NULL, 47, 47, 22, 15, 9);
INSERT INTO `schedules` VALUES (3142, '2025-07-27', 1, 32, 1, '2025-02-17 16:38:29', NULL, NULL, NULL, 48, 48, 22, 15, 9);
INSERT INTO `schedules` VALUES (3143, '2025-08-02', 1, 32, 1, '2025-02-17 16:38:29', NULL, NULL, NULL, 49, 49, 22, 15, 9);
INSERT INTO `schedules` VALUES (3144, '2025-08-03', 1, 32, 1, '2025-02-17 16:38:29', NULL, NULL, NULL, 50, 50, 22, 15, 9);
INSERT INTO `schedules` VALUES (3145, '2025-08-09', 1, 32, 1, '2025-02-17 16:38:29', NULL, NULL, NULL, 51, 51, 22, 15, 9);
INSERT INTO `schedules` VALUES (3146, '2025-08-10', 1, 32, 1, '2025-02-17 16:38:29', NULL, NULL, NULL, 52, 52, 22, 15, 9);
INSERT INTO `schedules` VALUES (3147, '2025-08-16', 1, 32, 1, '2025-02-17 16:38:29', NULL, NULL, NULL, 53, 53, 22, 15, 9);
INSERT INTO `schedules` VALUES (3148, '2025-08-17', 1, 32, 1, '2025-02-17 16:38:29', NULL, NULL, NULL, 54, 54, 22, 15, 9);
INSERT INTO `schedules` VALUES (3149, '2025-08-23', 1, 32, 1, '2025-02-17 16:38:29', NULL, NULL, NULL, 55, 55, 22, 15, 9);
INSERT INTO `schedules` VALUES (3150, '2025-08-24', 1, 32, 1, '2025-02-17 16:38:29', NULL, NULL, NULL, 56, 56, 22, 15, 9);
INSERT INTO `schedules` VALUES (3151, '2025-08-30', 1, 32, 1, '2025-02-17 16:38:29', NULL, NULL, NULL, 57, 57, 22, 15, 9);
INSERT INTO `schedules` VALUES (3152, '2025-08-31', 1, 32, 1, '2025-02-17 16:38:29', NULL, NULL, NULL, 58, 58, 22, 15, 9);
INSERT INTO `schedules` VALUES (3153, '2025-09-06', 1, 32, 1, '2025-02-17 16:38:29', NULL, NULL, NULL, 59, 59, 22, 15, 9);
INSERT INTO `schedules` VALUES (3154, '2025-09-07', 1, 32, 1, '2025-02-17 16:38:29', NULL, NULL, NULL, 60, 60, 22, 15, 9);
INSERT INTO `schedules` VALUES (3155, '2025-09-13', 1, 32, 1, '2025-02-17 16:38:29', NULL, NULL, NULL, 61, 61, 22, 15, 9);
INSERT INTO `schedules` VALUES (3156, '2025-09-14', 1, 32, 1, '2025-02-17 16:38:29', NULL, NULL, NULL, 62, 62, 22, 15, 9);
INSERT INTO `schedules` VALUES (3157, '2025-09-20', 1, 32, 1, '2025-02-17 16:38:29', NULL, NULL, NULL, 63, 63, 22, 15, 9);
INSERT INTO `schedules` VALUES (3158, '2025-09-21', 1, 32, 1, '2025-02-17 16:38:29', NULL, NULL, NULL, 64, 64, 22, 15, 9);
INSERT INTO `schedules` VALUES (3159, '2025-09-27', 1, 32, 1, '2025-02-17 16:38:29', NULL, NULL, NULL, 65, 65, 22, 15, 9);
INSERT INTO `schedules` VALUES (3160, '2025-09-28', 1, 32, 1, '2025-02-17 16:38:29', NULL, NULL, NULL, 66, 66, 22, 15, 9);
INSERT INTO `schedules` VALUES (3161, '2025-10-04', 1, 32, 1, '2025-02-17 16:38:29', NULL, NULL, NULL, 67, 67, 22, 15, 9);
INSERT INTO `schedules` VALUES (3162, '2025-10-05', 1, 32, 1, '2025-02-17 16:38:29', NULL, NULL, NULL, 68, 68, 22, 15, 9);
INSERT INTO `schedules` VALUES (3163, '2025-10-11', 1, 32, 1, '2025-02-17 16:38:29', NULL, NULL, NULL, 69, 69, 22, 15, 9);
INSERT INTO `schedules` VALUES (3164, '2025-10-12', 1, 32, 1, '2025-02-17 16:38:29', NULL, NULL, NULL, 70, 70, 22, 15, 9);
INSERT INTO `schedules` VALUES (3165, '2025-10-18', 1, 32, 1, '2025-02-17 16:38:29', NULL, NULL, NULL, 71, 71, 22, 15, 9);
INSERT INTO `schedules` VALUES (3166, '2025-10-19', 1, 32, 1, '2025-02-17 16:38:29', NULL, NULL, NULL, 72, 72, 22, 15, 9);
INSERT INTO `schedules` VALUES (3167, '2025-10-25', 1, 32, 1, '2025-02-17 16:38:29', NULL, NULL, NULL, 73, 73, 22, 15, 9);
INSERT INTO `schedules` VALUES (3168, '2025-10-26', 1, 32, 1, '2025-02-17 16:38:29', NULL, NULL, NULL, 74, 74, 22, 15, 9);
INSERT INTO `schedules` VALUES (3169, '2025-11-01', 1, 32, 1, '2025-02-17 16:38:29', NULL, NULL, NULL, 75, 75, 22, 15, 9);
INSERT INTO `schedules` VALUES (3170, '2025-11-02', 1, 32, 1, '2025-02-17 16:38:29', NULL, NULL, NULL, 76, 76, 22, 15, 9);
INSERT INTO `schedules` VALUES (3171, '2025-11-08', 1, 32, 1, '2025-02-17 16:38:29', NULL, NULL, NULL, 77, 77, 22, 15, 9);
INSERT INTO `schedules` VALUES (3172, '2025-11-09', 1, 32, 1, '2025-02-17 16:38:29', NULL, NULL, NULL, 78, 78, 22, 15, 9);
INSERT INTO `schedules` VALUES (3173, '2025-11-15', 1, 32, 1, '2025-02-17 16:38:29', NULL, NULL, NULL, 79, 79, 22, 15, 9);
INSERT INTO `schedules` VALUES (3174, '2025-11-16', 1, 32, 1, '2025-02-17 16:38:29', NULL, NULL, NULL, 80, 80, 22, 15, 9);
INSERT INTO `schedules` VALUES (3175, '2025-11-22', 1, 32, 1, '2025-02-17 16:38:29', NULL, NULL, NULL, 81, 81, 22, 15, 9);
INSERT INTO `schedules` VALUES (3176, '2025-11-23', 1, 32, 1, '2025-02-17 16:38:29', NULL, NULL, NULL, 82, 82, 22, 15, 9);
INSERT INTO `schedules` VALUES (3177, '2025-11-29', 1, 32, 1, '2025-02-17 16:38:29', NULL, NULL, NULL, 83, 83, 22, 15, 9);
INSERT INTO `schedules` VALUES (3178, '2025-11-30', 1, 32, 1, '2025-02-17 16:38:29', NULL, NULL, NULL, 84, 84, 22, 15, 9);
INSERT INTO `schedules` VALUES (3179, '2025-12-06', 1, 32, 1, '2025-02-17 16:38:29', NULL, NULL, NULL, 85, 85, 22, 15, 9);
INSERT INTO `schedules` VALUES (3180, '2025-12-07', 1, 32, 1, '2025-02-17 16:38:29', NULL, NULL, NULL, 86, 86, 22, 15, 9);
INSERT INTO `schedules` VALUES (3181, '2025-12-13', 1, 32, 1, '2025-02-17 16:38:29', NULL, NULL, NULL, 87, 87, 22, 15, 9);
INSERT INTO `schedules` VALUES (3182, '2025-12-14', 1, 32, 1, '2025-02-17 16:38:29', NULL, NULL, NULL, 88, 88, 22, 15, 9);
INSERT INTO `schedules` VALUES (3183, '2025-12-20', 1, 32, 1, '2025-02-17 16:38:29', NULL, NULL, NULL, 89, 89, 22, 15, 9);
INSERT INTO `schedules` VALUES (3184, '2025-12-21', 1, 32, 1, '2025-02-17 16:38:29', NULL, NULL, NULL, 90, 90, 22, 15, 9);
INSERT INTO `schedules` VALUES (3185, '2025-12-27', 1, 32, 1, '2025-02-17 16:38:29', NULL, NULL, NULL, 91, 91, 22, 15, 9);
INSERT INTO `schedules` VALUES (3186, '2025-12-28', 1, 32, 1, '2025-02-17 16:38:29', NULL, NULL, NULL, 92, 92, 22, 15, 9);
INSERT INTO `schedules` VALUES (3187, '2026-01-03', 1, 32, 1, '2025-02-17 16:38:29', NULL, NULL, NULL, 93, 93, 22, 15, 9);
INSERT INTO `schedules` VALUES (3188, '2026-01-04', 1, 32, 1, '2025-02-17 16:38:29', NULL, NULL, NULL, 94, 94, 22, 15, 9);
INSERT INTO `schedules` VALUES (3189, '2026-01-10', 1, 32, 1, '2025-02-17 16:38:29', NULL, NULL, NULL, 95, 95, 22, 15, 9);
INSERT INTO `schedules` VALUES (3190, '2026-01-11', 1, 32, 1, '2025-02-17 16:38:29', NULL, NULL, NULL, 96, 96, 22, 15, 9);
INSERT INTO `schedules` VALUES (3191, '2025-02-15', 1, 33, 1, '2025-02-17 16:38:58', NULL, NULL, NULL, 1, 1, 22, 15, 9);
INSERT INTO `schedules` VALUES (3192, '2025-02-16', 1, 33, 1, '2025-02-17 16:38:58', NULL, NULL, NULL, 2, 2, 22, 15, 9);
INSERT INTO `schedules` VALUES (3193, '2025-02-22', 1, 33, 1, '2025-02-17 16:38:58', NULL, NULL, NULL, 3, 3, 22, 15, 9);
INSERT INTO `schedules` VALUES (3194, '2025-02-23', 1, 33, 1, '2025-02-17 16:38:58', NULL, NULL, NULL, 4, 4, 22, 15, 9);
INSERT INTO `schedules` VALUES (3195, '2025-03-01', 1, 33, 1, '2025-02-17 16:38:58', NULL, NULL, NULL, 5, 5, 22, 15, 9);
INSERT INTO `schedules` VALUES (3196, '2025-03-02', 1, 33, 1, '2025-02-17 16:38:58', NULL, NULL, NULL, 6, 6, 22, 15, 9);
INSERT INTO `schedules` VALUES (3197, '2025-03-08', 1, 33, 1, '2025-02-17 16:38:58', NULL, NULL, NULL, 7, 7, 22, 15, 9);
INSERT INTO `schedules` VALUES (3198, '2025-03-09', 1, 33, 1, '2025-02-17 16:38:58', NULL, NULL, NULL, 8, 8, 22, 15, 9);
INSERT INTO `schedules` VALUES (3199, '2025-03-15', 1, 33, 1, '2025-02-17 16:38:58', NULL, NULL, NULL, 9, 9, 22, 15, 9);
INSERT INTO `schedules` VALUES (3200, '2025-03-16', 1, 33, 1, '2025-02-17 16:38:58', NULL, NULL, NULL, 10, 10, 22, 15, 9);
INSERT INTO `schedules` VALUES (3201, '2025-03-22', 1, 33, 1, '2025-02-17 16:38:58', NULL, NULL, NULL, 11, 11, 22, 15, 9);
INSERT INTO `schedules` VALUES (3202, '2025-03-23', 1, 33, 1, '2025-02-17 16:38:58', NULL, NULL, NULL, 12, 12, 22, 15, 9);
INSERT INTO `schedules` VALUES (3203, '2025-03-29', 1, 33, 1, '2025-02-17 16:38:58', NULL, NULL, NULL, 13, 13, 22, 15, 9);
INSERT INTO `schedules` VALUES (3204, '2025-03-30', 1, 33, 1, '2025-02-17 16:38:58', NULL, NULL, NULL, 14, 14, 22, 15, 9);
INSERT INTO `schedules` VALUES (3205, '2025-04-05', 1, 33, 1, '2025-02-17 16:38:58', NULL, NULL, NULL, 15, 15, 22, 15, 9);
INSERT INTO `schedules` VALUES (3206, '2025-04-06', 1, 33, 1, '2025-02-17 16:38:58', NULL, NULL, NULL, 16, 16, 22, 15, 9);
INSERT INTO `schedules` VALUES (3207, '2025-04-12', 1, 33, 1, '2025-02-17 16:38:58', NULL, NULL, NULL, 17, 17, 22, 15, 9);
INSERT INTO `schedules` VALUES (3208, '2025-04-13', 1, 33, 1, '2025-02-17 16:38:58', NULL, NULL, NULL, 18, 18, 22, 15, 9);
INSERT INTO `schedules` VALUES (3209, '2025-04-19', 1, 33, 1, '2025-02-17 16:38:58', NULL, NULL, NULL, 19, 19, 22, 15, 9);
INSERT INTO `schedules` VALUES (3210, '2025-04-20', 1, 33, 1, '2025-02-17 16:38:58', NULL, NULL, NULL, 20, 20, 22, 15, 9);
INSERT INTO `schedules` VALUES (3211, '2025-04-26', 1, 33, 1, '2025-02-17 16:38:58', NULL, NULL, NULL, 21, 21, 22, 15, 9);
INSERT INTO `schedules` VALUES (3212, '2025-04-27', 1, 33, 1, '2025-02-17 16:38:58', NULL, NULL, NULL, 22, 22, 22, 15, 9);
INSERT INTO `schedules` VALUES (3213, '2025-05-03', 1, 33, 1, '2025-02-17 16:38:58', NULL, NULL, NULL, 23, 23, 22, 15, 9);
INSERT INTO `schedules` VALUES (3214, '2025-05-04', 1, 33, 1, '2025-02-17 16:38:58', NULL, NULL, NULL, 24, 24, 22, 15, 9);
INSERT INTO `schedules` VALUES (3215, '2025-05-10', 1, 33, 1, '2025-02-17 16:38:58', NULL, NULL, NULL, 25, 25, 22, 15, 9);
INSERT INTO `schedules` VALUES (3216, '2025-05-11', 1, 33, 1, '2025-02-17 16:38:58', NULL, NULL, NULL, 26, 26, 22, 15, 9);
INSERT INTO `schedules` VALUES (3217, '2025-05-17', 1, 33, 1, '2025-02-17 16:38:58', NULL, NULL, NULL, 27, 27, 22, 15, 9);
INSERT INTO `schedules` VALUES (3218, '2025-05-18', 1, 33, 1, '2025-02-17 16:38:58', NULL, NULL, NULL, 28, 28, 22, 15, 9);
INSERT INTO `schedules` VALUES (3219, '2025-05-24', 1, 33, 1, '2025-02-17 16:38:58', NULL, NULL, NULL, 29, 29, 22, 15, 9);
INSERT INTO `schedules` VALUES (3220, '2025-05-25', 1, 33, 1, '2025-02-17 16:38:58', NULL, NULL, NULL, 30, 30, 22, 15, 9);
INSERT INTO `schedules` VALUES (3221, '2025-05-31', 1, 33, 1, '2025-02-17 16:38:58', NULL, NULL, NULL, 31, 31, 22, 15, 9);
INSERT INTO `schedules` VALUES (3222, '2025-06-01', 1, 33, 1, '2025-02-17 16:38:58', NULL, NULL, NULL, 32, 32, 22, 15, 9);
INSERT INTO `schedules` VALUES (3223, '2025-06-07', 1, 33, 1, '2025-02-17 16:38:58', NULL, NULL, NULL, 33, 33, 22, 15, 9);
INSERT INTO `schedules` VALUES (3224, '2025-06-08', 1, 33, 1, '2025-02-17 16:38:58', NULL, NULL, NULL, 34, 34, 22, 15, 9);
INSERT INTO `schedules` VALUES (3225, '2025-06-14', 1, 33, 1, '2025-02-17 16:38:58', NULL, NULL, NULL, 35, 35, 22, 15, 9);
INSERT INTO `schedules` VALUES (3226, '2025-06-15', 1, 33, 1, '2025-02-17 16:38:58', NULL, NULL, NULL, 36, 36, 22, 15, 9);
INSERT INTO `schedules` VALUES (3227, '2025-06-21', 1, 33, 1, '2025-02-17 16:38:58', NULL, NULL, NULL, 37, 37, 22, 15, 9);
INSERT INTO `schedules` VALUES (3228, '2025-06-22', 1, 33, 1, '2025-02-17 16:38:58', NULL, NULL, NULL, 38, 38, 22, 15, 9);
INSERT INTO `schedules` VALUES (3229, '2025-06-28', 1, 33, 1, '2025-02-17 16:38:58', NULL, NULL, NULL, 39, 39, 22, 15, 9);
INSERT INTO `schedules` VALUES (3230, '2025-06-29', 1, 33, 1, '2025-02-17 16:38:58', NULL, NULL, NULL, 40, 40, 22, 15, 9);
INSERT INTO `schedules` VALUES (3231, '2025-07-05', 1, 33, 1, '2025-02-17 16:38:58', NULL, NULL, NULL, 41, 41, 22, 15, 9);
INSERT INTO `schedules` VALUES (3232, '2025-07-06', 1, 33, 1, '2025-02-17 16:38:58', NULL, NULL, NULL, 42, 42, 22, 15, 9);
INSERT INTO `schedules` VALUES (3233, '2025-07-12', 1, 33, 1, '2025-02-17 16:38:58', NULL, NULL, NULL, 43, 43, 22, 15, 9);
INSERT INTO `schedules` VALUES (3234, '2025-07-13', 1, 33, 1, '2025-02-17 16:38:58', NULL, NULL, NULL, 44, 44, 22, 15, 9);
INSERT INTO `schedules` VALUES (3235, '2025-07-19', 1, 33, 1, '2025-02-17 16:38:58', NULL, NULL, NULL, 45, 45, 22, 15, 9);
INSERT INTO `schedules` VALUES (3236, '2025-07-20', 1, 33, 1, '2025-02-17 16:38:59', NULL, NULL, NULL, 46, 46, 22, 15, 9);
INSERT INTO `schedules` VALUES (3237, '2025-07-26', 1, 33, 1, '2025-02-17 16:38:59', NULL, NULL, NULL, 47, 47, 22, 15, 9);
INSERT INTO `schedules` VALUES (3238, '2025-07-27', 1, 33, 1, '2025-02-17 16:38:59', NULL, NULL, NULL, 48, 48, 22, 15, 9);
INSERT INTO `schedules` VALUES (3239, '2025-08-02', 1, 33, 1, '2025-02-17 16:38:59', NULL, NULL, NULL, 49, 49, 22, 15, 9);
INSERT INTO `schedules` VALUES (3240, '2025-08-03', 1, 33, 1, '2025-02-17 16:38:59', NULL, NULL, NULL, 50, 50, 22, 15, 9);
INSERT INTO `schedules` VALUES (3241, '2025-08-09', 1, 33, 1, '2025-02-17 16:38:59', NULL, NULL, NULL, 51, 51, 22, 15, 9);
INSERT INTO `schedules` VALUES (3242, '2025-08-10', 1, 33, 1, '2025-02-17 16:38:59', NULL, NULL, NULL, 52, 52, 22, 15, 9);
INSERT INTO `schedules` VALUES (3243, '2025-08-16', 1, 33, 1, '2025-02-17 16:38:59', NULL, NULL, NULL, 53, 53, 22, 15, 9);
INSERT INTO `schedules` VALUES (3244, '2025-08-17', 1, 33, 1, '2025-02-17 16:38:59', NULL, NULL, NULL, 54, 54, 22, 15, 9);
INSERT INTO `schedules` VALUES (3245, '2025-08-23', 1, 33, 1, '2025-02-17 16:38:59', NULL, NULL, NULL, 55, 55, 22, 15, 9);
INSERT INTO `schedules` VALUES (3246, '2025-08-24', 1, 33, 1, '2025-02-17 16:38:59', NULL, NULL, NULL, 56, 56, 22, 15, 9);
INSERT INTO `schedules` VALUES (3247, '2025-08-30', 1, 33, 1, '2025-02-17 16:38:59', NULL, NULL, NULL, 57, 57, 22, 15, 9);
INSERT INTO `schedules` VALUES (3248, '2025-08-31', 1, 33, 1, '2025-02-17 16:38:59', NULL, NULL, NULL, 58, 58, 22, 15, 9);
INSERT INTO `schedules` VALUES (3249, '2025-09-06', 1, 33, 1, '2025-02-17 16:38:59', NULL, NULL, NULL, 59, 59, 22, 15, 9);
INSERT INTO `schedules` VALUES (3250, '2025-09-07', 1, 33, 1, '2025-02-17 16:38:59', NULL, NULL, NULL, 60, 60, 22, 15, 9);
INSERT INTO `schedules` VALUES (3251, '2025-09-13', 1, 33, 1, '2025-02-17 16:38:59', NULL, NULL, NULL, 61, 61, 22, 15, 9);
INSERT INTO `schedules` VALUES (3252, '2025-09-14', 1, 33, 1, '2025-02-17 16:38:59', NULL, NULL, NULL, 62, 62, 22, 15, 9);
INSERT INTO `schedules` VALUES (3253, '2025-09-20', 1, 33, 1, '2025-02-17 16:38:59', NULL, NULL, NULL, 63, 63, 22, 15, 9);
INSERT INTO `schedules` VALUES (3254, '2025-09-21', 1, 33, 1, '2025-02-17 16:38:59', NULL, NULL, NULL, 64, 64, 22, 15, 9);
INSERT INTO `schedules` VALUES (3255, '2025-09-27', 1, 33, 1, '2025-02-17 16:38:59', NULL, NULL, NULL, 65, 65, 22, 15, 9);
INSERT INTO `schedules` VALUES (3256, '2025-09-28', 1, 33, 1, '2025-02-17 16:38:59', NULL, NULL, NULL, 66, 66, 22, 15, 9);
INSERT INTO `schedules` VALUES (3257, '2025-10-04', 1, 33, 1, '2025-02-17 16:38:59', NULL, NULL, NULL, 67, 67, 22, 15, 9);
INSERT INTO `schedules` VALUES (3258, '2025-10-05', 1, 33, 1, '2025-02-17 16:38:59', NULL, NULL, NULL, 68, 68, 22, 15, 9);
INSERT INTO `schedules` VALUES (3259, '2025-10-11', 1, 33, 1, '2025-02-17 16:38:59', NULL, NULL, NULL, 69, 69, 22, 15, 9);
INSERT INTO `schedules` VALUES (3260, '2025-10-12', 1, 33, 1, '2025-02-17 16:38:59', NULL, NULL, NULL, 70, 70, 22, 15, 9);
INSERT INTO `schedules` VALUES (3261, '2025-10-18', 1, 33, 1, '2025-02-17 16:38:59', NULL, NULL, NULL, 71, 71, 22, 15, 9);
INSERT INTO `schedules` VALUES (3262, '2025-10-19', 1, 33, 1, '2025-02-17 16:38:59', NULL, NULL, NULL, 72, 72, 22, 15, 9);
INSERT INTO `schedules` VALUES (3263, '2025-10-25', 1, 33, 1, '2025-02-17 16:38:59', NULL, NULL, NULL, 73, 73, 22, 15, 9);
INSERT INTO `schedules` VALUES (3264, '2025-10-26', 1, 33, 1, '2025-02-17 16:38:59', NULL, NULL, NULL, 74, 74, 22, 15, 9);
INSERT INTO `schedules` VALUES (3265, '2025-11-01', 1, 33, 1, '2025-02-17 16:38:59', NULL, NULL, NULL, 75, 75, 22, 15, 9);
INSERT INTO `schedules` VALUES (3266, '2025-11-02', 1, 33, 1, '2025-02-17 16:38:59', NULL, NULL, NULL, 76, 76, 22, 15, 9);
INSERT INTO `schedules` VALUES (3267, '2025-11-08', 1, 33, 1, '2025-02-17 16:38:59', NULL, NULL, NULL, 77, 77, 22, 15, 9);
INSERT INTO `schedules` VALUES (3268, '2025-11-09', 1, 33, 1, '2025-02-17 16:38:59', NULL, NULL, NULL, 78, 78, 22, 15, 9);
INSERT INTO `schedules` VALUES (3269, '2025-11-15', 1, 33, 1, '2025-02-17 16:38:59', NULL, NULL, NULL, 79, 79, 22, 15, 9);
INSERT INTO `schedules` VALUES (3270, '2025-11-16', 1, 33, 1, '2025-02-17 16:38:59', NULL, NULL, NULL, 80, 80, 22, 15, 9);
INSERT INTO `schedules` VALUES (3271, '2025-11-22', 1, 33, 1, '2025-02-17 16:38:59', NULL, NULL, NULL, 81, 81, 22, 15, 9);
INSERT INTO `schedules` VALUES (3272, '2025-11-23', 1, 33, 1, '2025-02-17 16:38:59', NULL, NULL, NULL, 82, 82, 22, 15, 9);
INSERT INTO `schedules` VALUES (3273, '2025-11-29', 1, 33, 1, '2025-02-17 16:38:59', NULL, NULL, NULL, 83, 83, 22, 15, 9);
INSERT INTO `schedules` VALUES (3274, '2025-11-30', 1, 33, 1, '2025-02-17 16:38:59', NULL, NULL, NULL, 84, 84, 22, 15, 9);
INSERT INTO `schedules` VALUES (3275, '2025-12-06', 1, 33, 1, '2025-02-17 16:38:59', NULL, NULL, NULL, 85, 85, 22, 15, 9);
INSERT INTO `schedules` VALUES (3276, '2025-12-07', 1, 33, 1, '2025-02-17 16:38:59', NULL, NULL, NULL, 86, 86, 22, 15, 9);
INSERT INTO `schedules` VALUES (3277, '2025-12-13', 1, 33, 1, '2025-02-17 16:38:59', NULL, NULL, NULL, 87, 87, 22, 15, 9);
INSERT INTO `schedules` VALUES (3278, '2025-12-14', 1, 33, 1, '2025-02-17 16:38:59', NULL, NULL, NULL, 88, 88, 22, 15, 9);
INSERT INTO `schedules` VALUES (3279, '2025-12-20', 1, 33, 1, '2025-02-17 16:38:59', NULL, NULL, NULL, 89, 89, 22, 15, 9);
INSERT INTO `schedules` VALUES (3280, '2025-12-21', 1, 33, 1, '2025-02-17 16:38:59', NULL, NULL, NULL, 90, 90, 22, 15, 9);
INSERT INTO `schedules` VALUES (3281, '2025-12-27', 1, 33, 1, '2025-02-17 16:38:59', NULL, NULL, NULL, 91, 91, 22, 15, 9);
INSERT INTO `schedules` VALUES (3282, '2025-12-28', 1, 33, 1, '2025-02-17 16:38:59', NULL, NULL, NULL, 92, 92, 22, 15, 9);
INSERT INTO `schedules` VALUES (3283, '2026-01-03', 1, 33, 1, '2025-02-17 16:38:59', NULL, NULL, NULL, 93, 93, 22, 15, 9);
INSERT INTO `schedules` VALUES (3284, '2026-01-04', 1, 33, 1, '2025-02-17 16:38:59', NULL, NULL, NULL, 94, 94, 22, 15, 9);
INSERT INTO `schedules` VALUES (3285, '2026-01-10', 1, 33, 1, '2025-02-17 16:38:59', NULL, NULL, NULL, 95, 95, 22, 15, 9);
INSERT INTO `schedules` VALUES (3286, '2026-01-11', 1, 33, 1, '2025-02-17 16:38:59', NULL, NULL, NULL, 96, 96, 22, 15, 9);
INSERT INTO `schedules` VALUES (3287, '2025-02-15', 1, 34, 1, '2025-02-17 16:39:25', NULL, NULL, NULL, 1, 1, 23, 15, 9);
INSERT INTO `schedules` VALUES (3288, '2025-02-16', 1, 34, 1, '2025-02-17 16:39:25', NULL, NULL, NULL, 2, 2, 23, 15, 9);
INSERT INTO `schedules` VALUES (3289, '2025-02-22', 1, 34, 1, '2025-02-17 16:39:25', NULL, NULL, NULL, 3, 3, 23, 15, 9);
INSERT INTO `schedules` VALUES (3290, '2025-02-23', 1, 34, 1, '2025-02-17 16:39:25', NULL, NULL, NULL, 4, 4, 23, 15, 9);
INSERT INTO `schedules` VALUES (3291, '2025-03-01', 1, 34, 1, '2025-02-17 16:39:25', NULL, NULL, NULL, 5, 5, 23, 15, 9);
INSERT INTO `schedules` VALUES (3292, '2025-03-02', 1, 34, 1, '2025-02-17 16:39:25', NULL, NULL, NULL, 6, 6, 23, 15, 9);
INSERT INTO `schedules` VALUES (3293, '2025-03-08', 1, 34, 1, '2025-02-17 16:39:25', NULL, NULL, NULL, 7, 7, 23, 15, 9);
INSERT INTO `schedules` VALUES (3294, '2025-03-09', 1, 34, 1, '2025-02-17 16:39:25', NULL, NULL, NULL, 8, 8, 23, 15, 9);
INSERT INTO `schedules` VALUES (3295, '2025-03-15', 1, 34, 1, '2025-02-17 16:39:25', NULL, NULL, NULL, 9, 9, 23, 15, 9);
INSERT INTO `schedules` VALUES (3296, '2025-03-16', 1, 34, 1, '2025-02-17 16:39:25', NULL, NULL, NULL, 10, 10, 23, 15, 9);
INSERT INTO `schedules` VALUES (3297, '2025-03-22', 1, 34, 1, '2025-02-17 16:39:25', NULL, NULL, NULL, 11, 11, 23, 15, 9);
INSERT INTO `schedules` VALUES (3298, '2025-03-23', 1, 34, 1, '2025-02-17 16:39:25', NULL, NULL, NULL, 12, 12, 23, 15, 9);
INSERT INTO `schedules` VALUES (3299, '2025-03-29', 1, 34, 1, '2025-02-17 16:39:25', NULL, NULL, NULL, 13, 13, 23, 15, 9);
INSERT INTO `schedules` VALUES (3300, '2025-03-30', 1, 34, 1, '2025-02-17 16:39:25', NULL, NULL, NULL, 14, 14, 23, 15, 9);
INSERT INTO `schedules` VALUES (3301, '2025-04-05', 1, 34, 1, '2025-02-17 16:39:25', NULL, NULL, NULL, 15, 15, 23, 15, 9);
INSERT INTO `schedules` VALUES (3302, '2025-04-06', 1, 34, 1, '2025-02-17 16:39:25', NULL, NULL, NULL, 16, 16, 23, 15, 9);
INSERT INTO `schedules` VALUES (3303, '2025-04-12', 1, 34, 1, '2025-02-17 16:39:25', NULL, NULL, NULL, 17, 17, 23, 15, 9);
INSERT INTO `schedules` VALUES (3304, '2025-04-13', 1, 34, 1, '2025-02-17 16:39:25', NULL, NULL, NULL, 18, 18, 23, 15, 9);
INSERT INTO `schedules` VALUES (3305, '2025-04-19', 1, 34, 1, '2025-02-17 16:39:25', NULL, NULL, NULL, 19, 19, 23, 15, 9);
INSERT INTO `schedules` VALUES (3306, '2025-04-20', 1, 34, 1, '2025-02-17 16:39:25', NULL, NULL, NULL, 20, 20, 23, 15, 9);
INSERT INTO `schedules` VALUES (3307, '2025-04-26', 1, 34, 1, '2025-02-17 16:39:25', NULL, NULL, NULL, 21, 21, 23, 15, 9);
INSERT INTO `schedules` VALUES (3308, '2025-04-27', 1, 34, 1, '2025-02-17 16:39:25', NULL, NULL, NULL, 22, 22, 23, 15, 9);
INSERT INTO `schedules` VALUES (3309, '2025-05-03', 1, 34, 1, '2025-02-17 16:39:25', NULL, NULL, NULL, 23, 23, 23, 15, 9);
INSERT INTO `schedules` VALUES (3310, '2025-05-04', 1, 34, 1, '2025-02-17 16:39:25', NULL, NULL, NULL, 24, 24, 23, 15, 9);
INSERT INTO `schedules` VALUES (3311, '2025-05-10', 1, 34, 1, '2025-02-17 16:39:25', NULL, NULL, NULL, 25, 25, 23, 15, 9);
INSERT INTO `schedules` VALUES (3312, '2025-05-11', 1, 34, 1, '2025-02-17 16:39:25', NULL, NULL, NULL, 26, 26, 23, 15, 9);
INSERT INTO `schedules` VALUES (3313, '2025-05-17', 1, 34, 1, '2025-02-17 16:39:25', NULL, NULL, NULL, 27, 27, 23, 15, 9);
INSERT INTO `schedules` VALUES (3314, '2025-05-18', 1, 34, 1, '2025-02-17 16:39:25', NULL, NULL, NULL, 28, 28, 23, 15, 9);
INSERT INTO `schedules` VALUES (3315, '2025-05-24', 1, 34, 1, '2025-02-17 16:39:25', NULL, NULL, NULL, 29, 29, 23, 15, 9);
INSERT INTO `schedules` VALUES (3316, '2025-05-25', 1, 34, 1, '2025-02-17 16:39:25', NULL, NULL, NULL, 30, 30, 23, 15, 9);
INSERT INTO `schedules` VALUES (3317, '2025-05-31', 1, 34, 1, '2025-02-17 16:39:25', NULL, NULL, NULL, 31, 31, 23, 15, 9);
INSERT INTO `schedules` VALUES (3318, '2025-06-01', 1, 34, 1, '2025-02-17 16:39:25', NULL, NULL, NULL, 32, 32, 23, 15, 9);
INSERT INTO `schedules` VALUES (3319, '2025-06-07', 1, 34, 1, '2025-02-17 16:39:25', NULL, NULL, NULL, 33, 33, 23, 15, 9);
INSERT INTO `schedules` VALUES (3320, '2025-06-08', 1, 34, 1, '2025-02-17 16:39:25', NULL, NULL, NULL, 34, 34, 23, 15, 9);
INSERT INTO `schedules` VALUES (3321, '2025-06-14', 1, 34, 1, '2025-02-17 16:39:25', NULL, NULL, NULL, 35, 35, 23, 15, 9);
INSERT INTO `schedules` VALUES (3322, '2025-06-15', 1, 34, 1, '2025-02-17 16:39:25', NULL, NULL, NULL, 36, 36, 23, 15, 9);
INSERT INTO `schedules` VALUES (3323, '2025-06-21', 1, 34, 1, '2025-02-17 16:39:25', NULL, NULL, NULL, 37, 37, 23, 15, 9);
INSERT INTO `schedules` VALUES (3324, '2025-06-22', 1, 34, 1, '2025-02-17 16:39:25', NULL, NULL, NULL, 38, 38, 23, 15, 9);
INSERT INTO `schedules` VALUES (3325, '2025-06-28', 1, 34, 1, '2025-02-17 16:39:25', NULL, NULL, NULL, 39, 39, 23, 15, 9);
INSERT INTO `schedules` VALUES (3326, '2025-06-29', 1, 34, 1, '2025-02-17 16:39:25', NULL, NULL, NULL, 40, 40, 23, 15, 9);
INSERT INTO `schedules` VALUES (3327, '2025-07-05', 1, 34, 1, '2025-02-17 16:39:25', NULL, NULL, NULL, 41, 41, 23, 15, 9);
INSERT INTO `schedules` VALUES (3328, '2025-07-06', 1, 34, 1, '2025-02-17 16:39:25', NULL, NULL, NULL, 42, 42, 23, 15, 9);
INSERT INTO `schedules` VALUES (3329, '2025-07-12', 1, 34, 1, '2025-02-17 16:39:25', NULL, NULL, NULL, 43, 43, 23, 15, 9);
INSERT INTO `schedules` VALUES (3330, '2025-07-13', 1, 34, 1, '2025-02-17 16:39:25', NULL, NULL, NULL, 44, 44, 23, 15, 9);
INSERT INTO `schedules` VALUES (3331, '2025-07-19', 1, 34, 1, '2025-02-17 16:39:25', NULL, NULL, NULL, 45, 45, 23, 15, 9);
INSERT INTO `schedules` VALUES (3332, '2025-07-20', 1, 34, 1, '2025-02-17 16:39:25', NULL, NULL, NULL, 46, 46, 23, 15, 9);
INSERT INTO `schedules` VALUES (3333, '2025-07-26', 1, 34, 1, '2025-02-17 16:39:25', NULL, NULL, NULL, 47, 47, 23, 15, 9);
INSERT INTO `schedules` VALUES (3334, '2025-07-27', 1, 34, 1, '2025-02-17 16:39:25', NULL, NULL, NULL, 48, 48, 23, 15, 9);
INSERT INTO `schedules` VALUES (3335, '2025-08-02', 1, 34, 1, '2025-02-17 16:39:25', NULL, NULL, NULL, 49, 49, 23, 15, 9);
INSERT INTO `schedules` VALUES (3336, '2025-08-03', 1, 34, 1, '2025-02-17 16:39:25', NULL, NULL, NULL, 50, 50, 23, 15, 9);
INSERT INTO `schedules` VALUES (3337, '2025-08-09', 1, 34, 1, '2025-02-17 16:39:25', NULL, NULL, NULL, 51, 51, 23, 15, 9);
INSERT INTO `schedules` VALUES (3338, '2025-08-10', 1, 34, 1, '2025-02-17 16:39:25', NULL, NULL, NULL, 52, 52, 23, 15, 9);
INSERT INTO `schedules` VALUES (3339, '2025-08-16', 1, 34, 1, '2025-02-17 16:39:25', NULL, NULL, NULL, 53, 53, 23, 15, 9);
INSERT INTO `schedules` VALUES (3340, '2025-08-17', 1, 34, 1, '2025-02-17 16:39:25', NULL, NULL, NULL, 54, 54, 23, 15, 9);
INSERT INTO `schedules` VALUES (3341, '2025-08-23', 1, 34, 1, '2025-02-17 16:39:25', NULL, NULL, NULL, 55, 55, 23, 15, 9);
INSERT INTO `schedules` VALUES (3342, '2025-08-24', 1, 34, 1, '2025-02-17 16:39:25', NULL, NULL, NULL, 56, 56, 23, 15, 9);
INSERT INTO `schedules` VALUES (3343, '2025-08-30', 1, 34, 1, '2025-02-17 16:39:25', NULL, NULL, NULL, 57, 57, 23, 15, 9);
INSERT INTO `schedules` VALUES (3344, '2025-08-31', 1, 34, 1, '2025-02-17 16:39:26', NULL, NULL, NULL, 58, 58, 23, 15, 9);
INSERT INTO `schedules` VALUES (3345, '2025-09-06', 1, 34, 1, '2025-02-17 16:39:26', NULL, NULL, NULL, 59, 59, 23, 15, 9);
INSERT INTO `schedules` VALUES (3346, '2025-09-07', 1, 34, 1, '2025-02-17 16:39:26', NULL, NULL, NULL, 60, 60, 23, 15, 9);
INSERT INTO `schedules` VALUES (3347, '2025-09-13', 1, 34, 1, '2025-02-17 16:39:26', NULL, NULL, NULL, 61, 61, 23, 15, 9);
INSERT INTO `schedules` VALUES (3348, '2025-09-14', 1, 34, 1, '2025-02-17 16:39:26', NULL, NULL, NULL, 62, 62, 23, 15, 9);
INSERT INTO `schedules` VALUES (3349, '2025-09-20', 1, 34, 1, '2025-02-17 16:39:26', NULL, NULL, NULL, 63, 63, 23, 15, 9);
INSERT INTO `schedules` VALUES (3350, '2025-09-21', 1, 34, 1, '2025-02-17 16:39:26', NULL, NULL, NULL, 64, 64, 23, 15, 9);
INSERT INTO `schedules` VALUES (3351, '2025-09-27', 1, 34, 1, '2025-02-17 16:39:26', NULL, NULL, NULL, 65, 65, 23, 15, 9);
INSERT INTO `schedules` VALUES (3352, '2025-09-28', 1, 34, 1, '2025-02-17 16:39:26', NULL, NULL, NULL, 66, 66, 23, 15, 9);
INSERT INTO `schedules` VALUES (3353, '2025-10-04', 1, 34, 1, '2025-02-17 16:39:26', NULL, NULL, NULL, 67, 67, 23, 15, 9);
INSERT INTO `schedules` VALUES (3354, '2025-10-05', 1, 34, 1, '2025-02-17 16:39:26', NULL, NULL, NULL, 68, 68, 23, 15, 9);
INSERT INTO `schedules` VALUES (3355, '2025-10-11', 1, 34, 1, '2025-02-17 16:39:26', NULL, NULL, NULL, 69, 69, 23, 15, 9);
INSERT INTO `schedules` VALUES (3356, '2025-10-12', 1, 34, 1, '2025-02-17 16:39:26', NULL, NULL, NULL, 70, 70, 23, 15, 9);
INSERT INTO `schedules` VALUES (3357, '2025-10-18', 1, 34, 1, '2025-02-17 16:39:26', NULL, NULL, NULL, 71, 71, 23, 15, 9);
INSERT INTO `schedules` VALUES (3358, '2025-10-19', 1, 34, 1, '2025-02-17 16:39:26', NULL, NULL, NULL, 72, 72, 23, 15, 9);
INSERT INTO `schedules` VALUES (3359, '2025-10-25', 1, 34, 1, '2025-02-17 16:39:26', NULL, NULL, NULL, 73, 73, 23, 15, 9);
INSERT INTO `schedules` VALUES (3360, '2025-10-26', 1, 34, 1, '2025-02-17 16:39:26', NULL, NULL, NULL, 74, 74, 23, 15, 9);
INSERT INTO `schedules` VALUES (3361, '2025-11-01', 1, 34, 1, '2025-02-17 16:39:26', NULL, NULL, NULL, 75, 75, 23, 15, 9);
INSERT INTO `schedules` VALUES (3362, '2025-11-02', 1, 34, 1, '2025-02-17 16:39:26', NULL, NULL, NULL, 76, 76, 23, 15, 9);
INSERT INTO `schedules` VALUES (3363, '2025-11-08', 1, 34, 1, '2025-02-17 16:39:26', NULL, NULL, NULL, 77, 77, 23, 15, 9);
INSERT INTO `schedules` VALUES (3364, '2025-11-09', 1, 34, 1, '2025-02-17 16:39:26', NULL, NULL, NULL, 78, 78, 23, 15, 9);
INSERT INTO `schedules` VALUES (3365, '2025-11-15', 1, 34, 1, '2025-02-17 16:39:26', NULL, NULL, NULL, 79, 79, 23, 15, 9);
INSERT INTO `schedules` VALUES (3366, '2025-11-16', 1, 34, 1, '2025-02-17 16:39:26', NULL, NULL, NULL, 80, 80, 23, 15, 9);
INSERT INTO `schedules` VALUES (3367, '2025-11-22', 1, 34, 1, '2025-02-17 16:39:26', NULL, NULL, NULL, 81, 81, 23, 15, 9);
INSERT INTO `schedules` VALUES (3368, '2025-11-23', 1, 34, 1, '2025-02-17 16:39:26', NULL, NULL, NULL, 82, 82, 23, 15, 9);
INSERT INTO `schedules` VALUES (3369, '2025-11-29', 1, 34, 1, '2025-02-17 16:39:26', NULL, NULL, NULL, 83, 83, 23, 15, 9);
INSERT INTO `schedules` VALUES (3370, '2025-11-30', 1, 34, 1, '2025-02-17 16:39:26', NULL, NULL, NULL, 84, 84, 23, 15, 9);
INSERT INTO `schedules` VALUES (3371, '2025-12-06', 1, 34, 1, '2025-02-17 16:39:26', NULL, NULL, NULL, 85, 85, 23, 15, 9);
INSERT INTO `schedules` VALUES (3372, '2025-12-07', 1, 34, 1, '2025-02-17 16:39:26', NULL, NULL, NULL, 86, 86, 23, 15, 9);
INSERT INTO `schedules` VALUES (3373, '2025-12-13', 1, 34, 1, '2025-02-17 16:39:26', NULL, NULL, NULL, 87, 87, 23, 15, 9);
INSERT INTO `schedules` VALUES (3374, '2025-12-14', 1, 34, 1, '2025-02-17 16:39:26', NULL, NULL, NULL, 88, 88, 23, 15, 9);
INSERT INTO `schedules` VALUES (3375, '2025-12-20', 1, 34, 1, '2025-02-17 16:39:26', NULL, NULL, NULL, 89, 89, 23, 15, 9);
INSERT INTO `schedules` VALUES (3376, '2025-12-21', 1, 34, 1, '2025-02-17 16:39:26', NULL, NULL, NULL, 90, 90, 23, 15, 9);
INSERT INTO `schedules` VALUES (3377, '2025-12-27', 1, 34, 1, '2025-02-17 16:39:26', NULL, NULL, NULL, 91, 91, 23, 15, 9);
INSERT INTO `schedules` VALUES (3378, '2025-12-28', 1, 34, 1, '2025-02-17 16:39:26', NULL, NULL, NULL, 92, 92, 23, 15, 9);
INSERT INTO `schedules` VALUES (3379, '2026-01-03', 1, 34, 1, '2025-02-17 16:39:26', NULL, NULL, NULL, 93, 93, 23, 15, 9);
INSERT INTO `schedules` VALUES (3380, '2026-01-04', 1, 34, 1, '2025-02-17 16:39:26', NULL, NULL, NULL, 94, 94, 23, 15, 9);
INSERT INTO `schedules` VALUES (3381, '2026-01-10', 1, 34, 1, '2025-02-17 16:39:26', NULL, NULL, NULL, 95, 95, 23, 15, 9);
INSERT INTO `schedules` VALUES (3382, '2026-01-11', 1, 34, 1, '2025-02-17 16:39:26', NULL, NULL, NULL, 96, 96, 23, 15, 9);
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
) ENGINE=InnoDB AUTO_INCREMENT=167 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of sessions
-- ----------------------------
BEGIN;
INSERT INTO `sessions` VALUES (119, 15, 7, 8, 9, 7, 1, '2024-12-08 20:32:28', 1, NULL, NULL, 5);
INSERT INTO `sessions` VALUES (120, 15, 7, 8, 9, 8, 1, '2024-12-08 20:32:28', 1, NULL, NULL, 5);
INSERT INTO `sessions` VALUES (121, 16, 7, 9, 9, 3, 1, '2024-12-08 20:33:15', 1, NULL, NULL, 6);
INSERT INTO `sessions` VALUES (122, 16, 7, 9, 9, 6, 1, '2024-12-08 20:33:15', 1, NULL, NULL, 6);
INSERT INTO `sessions` VALUES (125, 14, 7, 5, 10, 5, 1, '2025-01-07 23:03:40', 1, NULL, NULL, 4);
INSERT INTO `sessions` VALUES (126, 14, 7, 5, 10, 8, 1, '2025-01-07 23:03:40', 1, NULL, NULL, 4);
INSERT INTO `sessions` VALUES (127, 17, 8, 5, 3, 3, 1, '2025-02-17 14:44:10', 1, NULL, NULL, 4);
INSERT INTO `sessions` VALUES (128, 17, 8, 5, 3, 5, 1, '2025-02-17 14:44:10', 1, NULL, NULL, 4);
INSERT INTO `sessions` VALUES (133, 19, 14, 11, 21, 3, 1, '2025-02-17 16:14:44', 15, NULL, NULL, 9);
INSERT INTO `sessions` VALUES (134, 19, 14, 11, 21, 6, 1, '2025-02-17 16:14:44', 15, NULL, NULL, 9);
INSERT INTO `sessions` VALUES (135, 18, 14, 12, 21, 3, 1, '2025-02-17 16:15:02', 15, NULL, NULL, 9);
INSERT INTO `sessions` VALUES (136, 18, 14, 12, 21, 6, 1, '2025-02-17 16:15:02', 15, NULL, NULL, 9);
INSERT INTO `sessions` VALUES (137, 20, 14, 17, 22, 3, 1, '2025-02-17 16:15:43', 15, NULL, NULL, 9);
INSERT INTO `sessions` VALUES (138, 20, 14, 17, 22, 6, 1, '2025-02-17 16:15:43', 15, NULL, NULL, 9);
INSERT INTO `sessions` VALUES (139, 21, 15, 17, 22, 3, 1, '2025-02-17 16:16:28', 15, NULL, NULL, 9);
INSERT INTO `sessions` VALUES (140, 21, 15, 17, 22, 6, 1, '2025-02-17 16:16:28', 15, NULL, NULL, 9);
INSERT INTO `sessions` VALUES (141, 22, 15, 11, 21, 3, 1, '2025-02-17 16:17:55', 15, NULL, NULL, 9);
INSERT INTO `sessions` VALUES (142, 22, 15, 11, 21, 6, 1, '2025-02-17 16:17:55', 15, NULL, NULL, 9);
INSERT INTO `sessions` VALUES (143, 23, 16, 11, 21, 4, 1, '2025-02-17 16:18:45', 15, NULL, NULL, 9);
INSERT INTO `sessions` VALUES (144, 23, 16, 11, 21, 7, 1, '2025-02-17 16:18:45', 15, NULL, NULL, 9);
INSERT INTO `sessions` VALUES (145, 24, 16, 17, 22, 4, 1, '2025-02-17 16:19:24', 15, NULL, NULL, 9);
INSERT INTO `sessions` VALUES (146, 24, 16, 17, 22, 7, 1, '2025-02-17 16:19:24', 15, NULL, NULL, 9);
INSERT INTO `sessions` VALUES (147, 25, 17, 11, 21, 4, 1, '2025-02-17 16:19:59', 15, NULL, NULL, 9);
INSERT INTO `sessions` VALUES (148, 25, 17, 11, 21, 7, 1, '2025-02-17 16:19:59', 15, NULL, NULL, 9);
INSERT INTO `sessions` VALUES (149, 26, 17, 17, 22, 4, 1, '2025-02-17 16:20:31', 15, NULL, NULL, 9);
INSERT INTO `sessions` VALUES (150, 26, 17, 17, 22, 7, 1, '2025-02-17 16:20:31', 15, NULL, NULL, 9);
INSERT INTO `sessions` VALUES (151, 27, 18, 11, 21, 5, 1, '2025-02-17 16:25:40', 15, NULL, NULL, 9);
INSERT INTO `sessions` VALUES (152, 27, 18, 11, 21, 8, 1, '2025-02-17 16:25:40', 15, NULL, NULL, 9);
INSERT INTO `sessions` VALUES (153, 28, 18, 16, 22, 5, 1, '2025-02-17 16:26:33', 15, NULL, NULL, 9);
INSERT INTO `sessions` VALUES (154, 28, 18, 16, 22, 8, 1, '2025-02-17 16:26:33', 15, NULL, NULL, 9);
INSERT INTO `sessions` VALUES (155, 29, 19, 11, 21, 5, 1, '2025-02-17 16:34:27', 15, NULL, NULL, 9);
INSERT INTO `sessions` VALUES (156, 29, 19, 11, 21, 8, 1, '2025-02-17 16:34:27', 15, NULL, NULL, 9);
INSERT INTO `sessions` VALUES (157, 30, 21, 17, 22, 7, 1, '2025-02-17 16:37:25', 15, NULL, NULL, 9);
INSERT INTO `sessions` VALUES (158, 30, 21, 17, 22, 8, 1, '2025-02-17 16:37:25', 15, NULL, NULL, 9);
INSERT INTO `sessions` VALUES (159, 31, 21, 11, 21, 7, 1, '2025-02-17 16:38:02', 15, NULL, NULL, 9);
INSERT INTO `sessions` VALUES (160, 31, 21, 11, 21, 8, 1, '2025-02-17 16:38:02', 15, NULL, NULL, 9);
INSERT INTO `sessions` VALUES (161, 32, 22, 16, 22, 7, 1, '2025-02-17 16:38:29', 15, NULL, NULL, 9);
INSERT INTO `sessions` VALUES (162, 32, 22, 16, 22, 8, 1, '2025-02-17 16:38:29', 15, NULL, NULL, 9);
INSERT INTO `sessions` VALUES (163, 33, 23, 16, 22, 7, 1, '2025-02-17 16:38:58', 15, NULL, NULL, 9);
INSERT INTO `sessions` VALUES (164, 33, 23, 16, 22, 8, 1, '2025-02-17 16:38:58', 15, NULL, NULL, 9);
INSERT INTO `sessions` VALUES (165, 34, 23, 12, 23, 7, 1, '2025-02-17 16:39:25', 15, NULL, NULL, 9);
INSERT INTO `sessions` VALUES (166, 34, 23, 12, 23, 8, 1, '2025-02-17 16:39:25', 15, NULL, NULL, 9);
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
INSERT INTO `shifts` VALUES (14, 'Ca 5 T3 - 6', '17:30:00', '19:00:00', 1, NULL, 15, NULL, '2025-02-17 15:36:53', NULL);
INSERT INTO `shifts` VALUES (15, 'Ca 6 T3 - 6', '19:15:00', '20:45:00', 1, NULL, 15, NULL, '2025-02-17 15:37:39', NULL);
INSERT INTO `shifts` VALUES (16, 'Ca 5 T4 - 7', '17:30:00', '19:00:00', 1, NULL, 15, NULL, '2025-02-17 15:39:06', NULL);
INSERT INTO `shifts` VALUES (17, 'Ca 6 T4 - 7', '19:15:00', '20:45:00', 1, NULL, 15, NULL, '2025-02-17 15:39:26', NULL);
INSERT INTO `shifts` VALUES (18, 'Ca 5 T5 - CN', '17:30:00', '19:00:00', 1, NULL, 15, NULL, '2025-02-17 15:39:54', NULL);
INSERT INTO `shifts` VALUES (19, 'Ca 6 T5 - CN', '19:15:00', '20:45:00', 1, NULL, 15, NULL, '2025-02-17 15:40:10', NULL);
INSERT INTO `shifts` VALUES (20, 'Ca 1 T7 - CN', '08:00:00', '09:30:00', 1, NULL, 15, NULL, '2025-02-17 15:40:33', NULL);
INSERT INTO `shifts` VALUES (21, 'Ca 2 T7 - CN', '09:45:00', '11:15:00', 1, NULL, 15, NULL, '2025-02-17 15:40:59', NULL);
INSERT INTO `shifts` VALUES (22, 'Ca 3 T7 - CN', '14:00:00', '15:30:00', 1, NULL, 15, NULL, '2025-02-17 15:41:15', NULL);
INSERT INTO `shifts` VALUES (23, 'Ca 4 T7 - CN', '15:45:00', '17:15:00', 1, NULL, 15, NULL, '2025-02-17 15:41:29', NULL);
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
) ENGINE=InnoDB AUTO_INCREMENT=468 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of source_detail
-- ----------------------------
BEGIN;
INSERT INTO `source_detail` VALUES (461, 'Chiến dịch 1', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `source_detail` VALUES (462, 'chiến dịch 2', 1, '2024-12-17 23:40:17', 1, NULL, NULL, NULL, 1, 4);
INSERT INTO `source_detail` VALUES (465, 'VCBank', 1, '2024-12-18 00:56:06', 1, NULL, NULL, NULL, 2, 3);
INSERT INTO `source_detail` VALUES (466, 'chính sách đối tác VCB', 1, '2024-12-18 10:40:54', 1, NULL, NULL, NULL, 1, 5);
INSERT INTO `source_detail` VALUES (467, 'Chính sánh giới thiệu khách hàng T12/2024', 1, '2024-12-21 19:43:14', 1, '2024-12-21 19:47:23', 1, NULL, 3, 8);
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
INSERT INTO `sources` VALUES (1, 'Marketing', NULL, 1, NULL, NULL);
INSERT INTO `sources` VALUES (2, 'B2B', NULL, 1, NULL, NULL);
INSERT INTO `sources` VALUES (3, 'C2C', NULL, 1, NULL, NULL);
INSERT INTO `sources` VALUES (4, 'Trung tâm', NULL, 1, NULL, NULL);
INSERT INTO `sources` VALUES (5, 'Khác', NULL, 1, NULL, NULL);
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
) ENGINE=InnoDB AUTO_INCREMENT=70 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- ----------------------------
-- Records of student_logs
-- ----------------------------
BEGIN;
INSERT INTO `student_logs` VALUES (28, 6, 'Chuyển lên danh sách học sinh chính thức', '2024-11-30 21:03:15', 1, 1);
INSERT INTO `student_logs` VALUES (29, 7, 'Chuyển lên danh sách học sinh chính thức', '2024-11-30 21:20:21', 1, 1);
INSERT INTO `student_logs` VALUES (30, 8, 'Chuyển lên danh sách học sinh chính thức', '2024-12-09 12:27:46', 13, 1);
INSERT INTO `student_logs` VALUES (31, 9, 'Chuyển lên danh sách học sinh chính thức', '2024-12-11 09:35:15', 13, 1);
INSERT INTO `student_logs` VALUES (32, 9, 'Cập nhật thông tin học sinh: ', '2024-12-11 09:35:22', 13, 1);
INSERT INTO `student_logs` VALUES (33, 7, 'Thêm mới hợp đồng nhập học - C000007', '2024-12-20 12:15:42', 1, 1);
INSERT INTO `student_logs` VALUES (34, 10, 'Chuyển lên danh sách học sinh chính thức', '2024-12-21 20:44:17', 1, 1);
INSERT INTO `student_logs` VALUES (35, 10, 'Thêm mới hợp đồng nhập học - C000008', '2024-12-21 20:45:36', 1, 1);
INSERT INTO `student_logs` VALUES (36, 10, 'Thu đủ phí cho hợp đồng - C000008', '2024-12-21 20:46:45', 1, 1);
INSERT INTO `student_logs` VALUES (37, 7, 'Thu đủ phí cho hợp đồng - C000007', '2025-01-07 22:53:57', 1, 1);
INSERT INTO `student_logs` VALUES (38, 6, 'Thêm mới hợp đồng nhập học - C000009', '2025-01-07 22:56:44', 1, 1);
INSERT INTO `student_logs` VALUES (39, 6, 'Thu đủ phí cho hợp đồng - C000009', '2025-01-07 22:57:11', 1, 1);
INSERT INTO `student_logs` VALUES (40, 10, 'Xếp vào lớp ***KD1C/TS 5/ Room 2/ Thomas', '2025-01-07 23:04:09', 1, 1);
INSERT INTO `student_logs` VALUES (41, 7, 'Xếp vào lớp ***KD1C/TS 5/ Room 2/ Thomas', '2025-01-07 23:04:09', 1, 1);
INSERT INTO `student_logs` VALUES (42, 6, 'Xếp vào lớp ***KD1C/TS 5/ Room 2/ Thomas', '2025-01-07 23:04:09', 1, 1);
INSERT INTO `student_logs` VALUES (43, 11, 'Chuyển lên danh sách học sinh chính thức', '2025-02-17 15:24:29', 15, 1);
INSERT INTO `student_logs` VALUES (44, 11, 'Thêm mới hợp đồng nhập học - C000010', '2025-02-17 15:25:10', 15, 1);
INSERT INTO `student_logs` VALUES (45, 11, 'Thu đủ phí cho hợp đồng - C000010', '2025-02-17 15:25:36', 15, 1);
INSERT INTO `student_logs` VALUES (46, 11, 'Xếp vào lớp Seedbed 1 TF5 R2', '2025-02-18 10:31:52', 15, 1);
INSERT INTO `student_logs` VALUES (47, 12, 'Chuyển lên danh sách học sinh chính thức', '2025-02-18 10:36:51', 15, 1);
INSERT INTO `student_logs` VALUES (48, 13, 'Chuyển lên danh sách học sinh chính thức', '2025-02-18 10:37:02', 15, 1);
INSERT INTO `student_logs` VALUES (49, 14, 'Chuyển lên danh sách học sinh chính thức', '2025-02-18 10:37:12', 15, 1);
INSERT INTO `student_logs` VALUES (50, 15, 'Chuyển lên danh sách học sinh chính thức', '2025-02-18 10:37:22', 15, 1);
INSERT INTO `student_logs` VALUES (51, 12, 'Thêm mới hợp đồng nhập học - C000011', '2025-02-18 10:37:57', 15, 1);
INSERT INTO `student_logs` VALUES (52, 15, 'Thêm mới hợp đồng nhập học - C000012', '2025-02-18 10:38:26', 15, 1);
INSERT INTO `student_logs` VALUES (53, 14, 'Thêm mới hợp đồng nhập học - C000013', '2025-02-18 10:38:43', 15, 1);
INSERT INTO `student_logs` VALUES (54, 13, 'Thêm mới hợp đồng nhập học - C000014', '2025-02-18 10:39:00', 15, 1);
INSERT INTO `student_logs` VALUES (55, 13, 'Thu đủ phí cho hợp đồng - C000014', '2025-02-18 10:39:18', 1, 1);
INSERT INTO `student_logs` VALUES (56, 14, 'Thu đủ phí cho hợp đồng - C000013', '2025-02-18 10:39:25', 1, 1);
INSERT INTO `student_logs` VALUES (57, 15, 'Thu đủ phí cho hợp đồng - C000012', '2025-02-18 10:39:35', 1, 1);
INSERT INTO `student_logs` VALUES (58, 12, 'Thu đủ phí cho hợp đồng - C000011', '2025-02-18 10:39:43', 1, 1);
INSERT INTO `student_logs` VALUES (59, 15, 'Xếp vào lớp Seedbed 1 TF5 R2', '2025-02-18 10:40:08', 15, 1);
INSERT INTO `student_logs` VALUES (60, 14, 'Xếp vào lớp Seedbed 1 TF5 R2', '2025-02-18 10:40:08', 15, 1);
INSERT INTO `student_logs` VALUES (61, 13, 'Xếp vào lớp Seedbed 1 TF5 R2', '2025-02-18 10:40:08', 15, 1);
INSERT INTO `student_logs` VALUES (62, 12, 'Xếp vào lớp Seedbed 1 TF5 R2', '2025-02-18 10:40:08', 15, 1);
INSERT INTO `student_logs` VALUES (63, 16, 'Chuyển lên danh sách học sinh chính thức', '2025-02-18 16:49:18', 15, 1);
INSERT INTO `student_logs` VALUES (64, 15, 'Thêm mới hợp đồng nhập học - C000015', '2025-02-18 16:57:23', 15, 1);
INSERT INTO `student_logs` VALUES (65, 17, 'Chuyển lên danh sách học sinh chính thức', '2025-02-18 17:16:16', 15, 1);
INSERT INTO `student_logs` VALUES (66, 17, 'Thêm mới hợp đồng nhập học - C000016', '2025-02-18 17:20:15', 15, 1);
INSERT INTO `student_logs` VALUES (67, 17, 'Đặt cọc 1,000,000đ cho hợp đồng - C000016', '2025-02-18 17:21:14', 15, 1);
INSERT INTO `student_logs` VALUES (68, 15, 'Đặt cọc 1,000,000đ cho hợp đồng - C000015', '2025-02-19 00:09:29', 1, 1);
INSERT INTO `student_logs` VALUES (69, 15, 'Thu đủ phí cho hợp đồng - C000015', '2025-02-19 00:09:58', 1, 1);
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
  `c2c_mobile` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `index_student` (`lms_code`) USING BTREE,
  KEY `province_id` (`province_id`) USING BTREE,
  KEY `district_id` (`district_id`) USING BTREE,
  KEY `gud_mobile1` (`gud_mobile1`) USING BTREE,
  KEY `gud_mobile2` (`gud_mobile2`) USING BTREE,
  KEY `branch_id` (`branch_id`) USING BTREE,
  KEY `status` (`status`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of students
-- ----------------------------
BEGIN;
INSERT INTO `students` VALUES (11, 'LMS000011', 'Nguyễn Minh Khôi', 'Nguyễn', 'Minh', 'Khôi', NULL, 'M', 0, '2024-03-27', '0966948868', 'Nguyễn Văn Đạt', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-02-17 15:24:29', 15, '2025-02-17 08:24:29', NULL, NULL, NULL, NULL, 9, NULL, NULL, NULL, NULL, NULL, 'M', NULL, NULL, NULL, 1, NULL, 4, '/images/common/avatar-boy.svg', NULL);
INSERT INTO `students` VALUES (12, 'LMS000012', 'HS test 4', 'HS', 'test', '4', NULL, 'F', 0, '2025-02-18', '0965412548', 'PH test 4', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-02-18 10:36:51', 15, '2025-02-18 03:36:51', NULL, NULL, NULL, NULL, 9, NULL, NULL, NULL, NULL, NULL, 'M', NULL, NULL, NULL, 1, NULL, 4, '/images/common/avatar-girl.svg', NULL);
INSERT INTO `students` VALUES (13, 'LMS000013', 'HS test 3', 'HS', 'test', '3', NULL, 'F', 0, '2025-02-05', '0965412364', 'PH test 3', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-02-18 10:37:02', 15, '2025-02-18 03:37:02', NULL, NULL, NULL, NULL, 9, NULL, NULL, NULL, NULL, NULL, 'M', NULL, NULL, NULL, 1, NULL, 4, '/images/common/avatar-girl.svg', NULL);
INSERT INTO `students` VALUES (14, 'LMS000014', 'HS test 2', 'HS', 'test', '2', NULL, 'M', 0, '2025-02-18', '0965412236', 'PH test 2', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-02-18 10:37:12', 15, '2025-02-18 03:37:12', NULL, NULL, NULL, NULL, 9, NULL, NULL, NULL, NULL, NULL, 'M', NULL, NULL, NULL, 1, NULL, 4, '/images/common/avatar-boy.svg', NULL);
INSERT INTO `students` VALUES (15, 'LMS000015', 'HS test 1', 'HS', 'test', '1', NULL, 'M', 0, '2024-12-31', '0965487421', 'PH 1', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-02-18 10:37:22', 15, '2025-02-18 03:37:22', NULL, NULL, NULL, NULL, 9, NULL, NULL, NULL, NULL, NULL, 'M', NULL, NULL, NULL, 1, NULL, 4, '/images/common/avatar-boy.svg', NULL);
INSERT INTO `students` VALUES (16, 'LMS000016', 'Nguyễn Văn B', 'Nguyễn', 'Văn', 'B', NULL, 'M', 0, '2025-02-18', '0965412548', 'PH test 4', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-02-18 16:49:18', 15, '2025-02-18 09:49:18', NULL, NULL, NULL, NULL, 9, NULL, NULL, NULL, NULL, NULL, 'M', NULL, NULL, NULL, 1, NULL, 4, '/images/common/avatar-boy.svg', NULL);
INSERT INTO `students` VALUES (17, 'LMS000017', 'Phùng Ngọc Hoa', 'Phùng', 'Ngọc', 'Hoa', NULL, 'F', 0, '2025-02-18', '0862882290', 'Chị Bổ', NULL, NULL, NULL, NULL, NULL, 35, 347, NULL, NULL, NULL, '2025-02-18 17:16:15', 15, '2025-02-18 10:16:15', NULL, 'Lớp Cocoon 1', NULL, NULL, 9, NULL, NULL, NULL, NULL, NULL, 'F', NULL, NULL, NULL, 1, NULL, 4, '/images/common/avatar-girl.svg', NULL);
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
) ENGINE=InnoDB AUTO_INCREMENT=253 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- ----------------------------
-- Records of subject_has_class
-- ----------------------------
BEGIN;
INSERT INTO `subject_has_class` VALUES (229, 1, 15, 32, 1, '2024-12-08 20:32:28');
INSERT INTO `subject_has_class` VALUES (230, 1, 16, 32, 1, '2024-12-08 20:33:15');
INSERT INTO `subject_has_class` VALUES (232, 1, 14, 32, 1, '2025-01-07 23:03:40');
INSERT INTO `subject_has_class` VALUES (233, 1, 17, 96, 1, '2025-02-17 14:44:10');
INSERT INTO `subject_has_class` VALUES (236, 1, 19, 96, 1, '2025-02-17 16:14:44');
INSERT INTO `subject_has_class` VALUES (237, 1, 18, 96, 1, '2025-02-17 16:15:02');
INSERT INTO `subject_has_class` VALUES (238, 1, 20, 96, 1, '2025-02-17 16:15:43');
INSERT INTO `subject_has_class` VALUES (239, 1, 21, 96, 1, '2025-02-17 16:16:28');
INSERT INTO `subject_has_class` VALUES (240, 1, 22, 96, 1, '2025-02-17 16:17:55');
INSERT INTO `subject_has_class` VALUES (241, 1, 23, 96, 1, '2025-02-17 16:18:45');
INSERT INTO `subject_has_class` VALUES (242, 1, 24, 96, 1, '2025-02-17 16:19:24');
INSERT INTO `subject_has_class` VALUES (243, 1, 25, 96, 1, '2025-02-17 16:19:59');
INSERT INTO `subject_has_class` VALUES (244, 1, 26, 96, 1, '2025-02-17 16:20:31');
INSERT INTO `subject_has_class` VALUES (245, 1, 27, 96, 1, '2025-02-17 16:25:40');
INSERT INTO `subject_has_class` VALUES (246, 1, 28, 96, 1, '2025-02-17 16:26:33');
INSERT INTO `subject_has_class` VALUES (247, 1, 29, 96, 1, '2025-02-17 16:34:27');
INSERT INTO `subject_has_class` VALUES (248, 1, 30, 96, 1, '2025-02-17 16:37:25');
INSERT INTO `subject_has_class` VALUES (249, 1, 31, 96, 1, '2025-02-17 16:38:02');
INSERT INTO `subject_has_class` VALUES (250, 1, 32, 96, 1, '2025-02-17 16:38:29');
INSERT INTO `subject_has_class` VALUES (251, 1, 33, 96, 1, '2025-02-17 16:38:58');
INSERT INTO `subject_has_class` VALUES (252, 1, 34, 96, 1, '2025-02-17 16:39:25');
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
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of term_student_user
-- ----------------------------
BEGIN;
INSERT INTO `term_student_user` VALUES (6, 6, 1, NULL, 6, 6, 2, 10, '2024-11-30 21:03:15', '2025-01-07 23:04:09', 1, 4, 1, 1);
INSERT INTO `term_student_user` VALUES (7, 7, 1, NULL, 6, 6, 2, 10, '2024-11-30 21:20:21', '2025-01-07 23:04:09', 1, 4, 1, 1);
INSERT INTO `term_student_user` VALUES (8, 8, 13, NULL, NULL, NULL, 2, NULL, '2024-12-09 12:27:46', NULL, 1, 6, 13, NULL);
INSERT INTO `term_student_user` VALUES (9, 9, 13, NULL, NULL, NULL, 2, NULL, '2024-12-11 09:35:15', NULL, 1, 6, 13, NULL);
INSERT INTO `term_student_user` VALUES (10, 10, 5, NULL, 6, 6, 2, 10, '2024-12-21 20:44:17', '2025-01-07 23:04:09', 1, 4, 1, 1);
INSERT INTO `term_student_user` VALUES (11, 11, 17, NULL, 15, 15, 16, 21, '2025-02-17 15:24:29', '2025-02-18 10:31:52', 1, 9, 15, 15);
INSERT INTO `term_student_user` VALUES (12, 12, 17, NULL, 15, 15, 16, 21, '2025-02-18 10:36:51', '2025-02-18 10:40:08', 1, 9, 15, 15);
INSERT INTO `term_student_user` VALUES (13, 13, 17, NULL, 15, 15, 16, 21, '2025-02-18 10:37:02', '2025-02-18 10:40:08', 1, 9, 15, 15);
INSERT INTO `term_student_user` VALUES (14, 14, 17, NULL, 15, 15, 16, 21, '2025-02-18 10:37:12', '2025-02-18 10:40:08', 1, 9, 15, 15);
INSERT INTO `term_student_user` VALUES (15, 15, 17, NULL, 15, 15, 16, 21, '2025-02-18 10:37:22', '2025-02-18 10:40:08', 1, 9, 15, 15);
INSERT INTO `term_student_user` VALUES (16, 16, 17, NULL, NULL, NULL, 16, NULL, '2025-02-18 16:49:18', NULL, 1, 9, 15, NULL);
INSERT INTO `term_student_user` VALUES (17, 17, 17, NULL, NULL, NULL, 16, NULL, '2025-02-18 17:16:15', NULL, 1, 9, 15, NULL);
COMMIT;

-- ----------------------------
-- Table structure for tmp_payments
-- ----------------------------
DROP TABLE IF EXISTS `tmp_payments`;
CREATE TABLE `tmp_payments` (
  `id` int NOT NULL AUTO_INCREMENT,
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
  PRIMARY KEY (`id`),
  KEY `contract_id` (`contract_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of tmp_payments
-- ----------------------------
BEGIN;
INSERT INTO `tmp_payments` VALUES (1, 15, 100000, 1800000, 100000, NULL, '2025-02-18', 0, 'ghi chú', '{\"id\":\"1\",\"contract_id\":15,\"note\":\"ghi ch\\u00fa\",\"charge_date\":\"2025-02-18\",\"amount\":100000,\"method\":0}', '2025-02-18 22:49:33', 1, '2025-02-18 23:41:58', 1, '2025-02-19 00:05:21', 1, 2, NULL);
INSERT INTO `tmp_payments` VALUES (3, 15, 1000000, 900000, 1000000, NULL, '2025-02-19', 1, 'ghi chú', '{\"contract_id\":15,\"note\":\"ghi ch\\u00fa\",\"charge_date\":\"2025-02-19\",\"amount\":1000000,\"method\":1}', '2025-02-19 00:07:56', 1, NULL, NULL, '2025-02-19 00:09:30', 1, 1, NULL);
INSERT INTO `tmp_payments` VALUES (4, 15, 900000, 0, 1900000, NULL, '2025-02-19', 1, 'ghi chú', '{\"id\":\"4\",\"contract_id\":15,\"note\":\"ghi ch\\u00fa\",\"charge_date\":\"2025-02-19\",\"amount\":900000,\"method\":1}', '2025-02-19 00:08:27', 1, '2025-02-19 00:09:49', 1, '2025-02-19 00:09:59', 1, 1, NULL);
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
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of tuition_fee
-- ----------------------------
BEGIN;
INSERT INTO `tuition_fee` VALUES (10, 'Khóa học Logiclab 1 tháng', NULL, 14, NULL, 8, 1900000, NULL, NULL, 1900000, '2025-02-17 15:12:46', 1, '2025-02-18 16:53:26', 1, '2025-02-17', '2026-01-31', NULL, NULL, 1, 0, '9', NULL, 1, 1);
INSERT INTO `tuition_fee` VALUES (11, 'Khóa học Logiclab 3 tháng', NULL, 14, NULL, 24, 5700000, NULL, NULL, 5700000, '2025-02-17 15:13:28', 1, '2025-02-18 16:53:18', 1, '2025-02-17', '2026-01-31', NULL, NULL, 1, 0, '9', NULL, 3, 1);
INSERT INTO `tuition_fee` VALUES (12, 'Khóa học Logiclab 6 tháng', NULL, 14, NULL, 48, 10830000, NULL, NULL, 10830000, '2025-02-17 15:15:41', 1, '2025-02-18 16:53:07', 1, '2025-02-17', '2026-01-31', NULL, NULL, 1, 0, '9', NULL, 6, 1);
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
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb3;

-- ----------------------------
-- Records of tuition_fee_relation
-- ----------------------------
BEGIN;
INSERT INTO `tuition_fee_relation` VALUES (6, 10, 10, 1, 'Khóa học Apax 1 tháng', 'Khóa học Apax 1 tháng');
INSERT INTO `tuition_fee_relation` VALUES (7, 11, 11, 1, 'Khóa học Apax 3 tháng', 'Khóa học Apax 3 tháng');
INSERT INTO `tuition_fee_relation` VALUES (8, 12, 12, 1, 'Khóa học Apax 6 tháng', 'Khóa học Apax 6 tháng');
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
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of users
-- ----------------------------
BEGIN;
INSERT INTO `users` VALUES (1, 'Lương Thành Công', '0389941902', 'admin@admin.com', '2020-08-27 02:04:43', '$2y$10$upeEWEPRUHvriPrThSy8hOMzfqxFGRFPrBP0UgkBwC8C54RBz/6C6', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vbG9jYWwtZXJwLmNvbmduZ2hlZ2lhb2R1Yy5jb20vYXBpL2F1dGgvbG9naW4iLCJpYXQiOjE3Mzk4OTI0MzcsImV4cCI6MTc0MDQ5NzIzNywibmJmIjoxNzM5ODkyNDM3LCJqdGkiOiJDSzBlOXIyeHNiUzFtWTJVIiwic3ViIjoiMSIsInBydiI6Ijg3ZTBhZjFlZjlmZDE1ODEyZmRlYzk3MTUzYTE0ZTBiMDQ3NTQ2YWEifQ.cQf0CcUGfz2Z8Mm3r3rApGDTsrjt2Y6ksYsmNqlo1_c', 'admin,user', 1, 'EQt3UDeo4F', '2020-08-27 02:04:43', '2025-02-17 15:01:31', NULL, 1, NULL, 9, 'Trung Tâm Hà Nam', 'admin', NULL, NULL, 0, 'Quản trị hệ thống', NULL, '');
INSERT INTO `users` VALUES (15, 'My NTH', '0968743254', 'Mynth.hn@logiclab.vn', NULL, '$2y$10$SXVFEv93GqPh1NVfPojK1uyEwu5jJ.RiWmCSHZw7iReM.tOccln4.', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwczovL2hhbmFtLmxvZ2ljbGFiLnZuL2FwaS9hdXRoL2xvZ2luIiwiaWF0IjoxNzM5ODczNzIxLCJleHAiOjE3NDA0Nzg1MjEsIm5iZiI6MTczOTg3MzcyMSwianRpIjoiNVRsR2RGV256UUVXMFBiQyIsInN1YiI6IjE1IiwicHJ2IjoiODdlMGFmMWVmOWZkMTU4MTJmZGVjOTcxNTNhMTRlMGIwNDc1NDZhYSJ9.liyPnJGqt4vIOtFlUhAb6oZGpiNkinn0GrM_5cTYC7s', '', 1, NULL, '2025-02-17 14:46:29', '2025-02-18 10:46:48', 1, 1, NULL, 9, 'Trung Tâm Hà Nam', 'LOG01', NULL, NULL, 0, 'Trưởng nhóm CSKH', NULL, NULL);
INSERT INTO `users` VALUES (16, 'Phạm Văn Nam', '0968554869', 'nampv@logiclab.vn', NULL, '$2y$10$cKg15EETqEPMIHb1Bgzmue22cnSm3B.7lx/J6dt.M9XJBaSSW7Yau', NULL, '', 1, NULL, '2025-02-17 15:02:09', NULL, 1, NULL, NULL, 9, 'Trung Tâm Hà Nam', 'LOG02', NULL, NULL, 0, 'Giám đốc trung tâm', NULL, NULL);
INSERT INTO `users` VALUES (17, 'TTT Hằng', '0965874521', 'Hangttt.hn@logiclab.vn', NULL, '$2y$10$xhZMd4U0NO4ryn6MzVSutuA5sosAOn7W4xn0oGK/vgHyDnNa8Gz.a', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwczovL2hhbmFtLmxvZ2ljbGFiLnZuL2FwaS9hdXRoL2xvZ2luIiwiaWF0IjoxNzM5ODcwOTY0LCJleHAiOjE3NDA0NzU3NjQsIm5iZiI6MTczOTg3MDk2NCwianRpIjoiYWJRYURLeGNibzZrcXU1ayIsInN1YiI6IjE3IiwicHJ2IjoiODdlMGFmMWVmOWZkMTU4MTJmZGVjOTcxNTNhMTRlMGIwNDc1NDZhYSJ9.WrJ0kP8CDRgGaVDMP9OyiRDu2d9ZWjDlzlWrpaxy_gM', '', 1, NULL, '2025-02-17 15:06:11', '2025-02-18 16:18:15', 1, 1, NULL, 9, 'Trung Tâm Hà Nam', 'LGL000111', NULL, NULL, 0, 'Trưởng nhóm Kinh doanh', NULL, NULL);
INSERT INTO `users` VALUES (18, 'Nguyệt NT', '0965872136', 'Nguyetnt.hn@logiclab.vn', NULL, '$2y$10$Dr/x9qZFwzu8WEaBm1Z8PO5KJHaSE3SiBjMFcLvixApZykZNXRh5m', NULL, '', 1, NULL, '2025-02-17 15:07:12', '2025-02-18 16:14:25', 1, 1, NULL, 9, 'Trung Tâm Hà Nam', 'LOG04', NULL, NULL, 0, 'Nhân viên kinh doanh', NULL, NULL);
INSERT INTO `users` VALUES (19, 'TN Giáo Viên', '0965847123', 'tngv.hn@logiclab.vn', NULL, '$2y$10$dM.d48sI27XxlMWWPTHxe.VevQxkKbxacZmBMZqV5u9.rakSN23UK', NULL, '', 1, NULL, '2025-02-17 15:09:03', NULL, 1, NULL, NULL, 9, 'Trung Tâm Hà Nam', 'LOG5', NULL, NULL, 0, 'Trưởng nhóm giáo viên', NULL, NULL);
INSERT INTO `users` VALUES (20, 'Giáo Viên', '0965412311', 'gv.hn@logiclab.vn', NULL, '$2y$10$oc8W09ZxY3qcMDwhULQL3eAu85/A1KIWVn07Nlasm7Pwp.ttE25q6', NULL, '', 1, NULL, '2025-02-17 15:09:31', '2025-02-17 15:09:36', 1, 1, NULL, 9, 'Trung Tâm Hà Nam', 'LOG06', NULL, NULL, 0, 'Giáo viên', NULL, NULL);
INSERT INTO `users` VALUES (21, 'Graham James Walker', '0965412548', 'walker.hn@logiclab.vn', NULL, '$2y$10$z0Na0SIjG.PR411OvDuCz.AcxH88SN3bSI44J7qaGP2MIO3ZnlJ/2', NULL, '', 1, NULL, '2025-02-17 15:48:58', '2025-02-17 16:08:37', 1, 1, NULL, 9, 'Trung Tâm Hà Nam', 'LOG07', NULL, NULL, 0, 'Giáo viên', NULL, NULL);
INSERT INTO `users` VALUES (22, 'Mhlelembana Wandile', '0965412236', 'wandile.hn@logiclab.vn', NULL, '$2y$10$aKjsi1pypdwEc199oqKoJ.bgqu7ky2rFZgw1jr0YFtRz/VIDGKnhG', NULL, '', 1, NULL, '2025-02-17 15:49:30', NULL, 1, NULL, NULL, 9, 'Trung Tâm Hà Nam', 'LOG08', NULL, NULL, 0, 'Giáo viên', NULL, NULL);
INSERT INTO `users` VALUES (23, 'Sandani Moyeme Jamel', '0965411254', 'jammel.hn@logiclab.vn', NULL, '$2y$10$0T.rIlfJwevSJ9tnH44BkOdJwy5XPNLB9GE9JU7nL6pOxpLiMLcMe', NULL, '', 1, NULL, '2025-02-17 16:09:01', NULL, 1, NULL, NULL, 9, 'Trung Tâm Hà Nam', 'LOG09', NULL, NULL, 0, 'Giáo viên', NULL, NULL);
COMMIT;

SET FOREIGN_KEY_CHECKS = 1;
