/*
 Navicat Premium Data Transfer

 Source Server         : docker - localhost
 Source Server Type    : MySQL
 Source Server Version : 50729
 Source Host           : localhost:33060
 Source Schema         : erp_fastrackids

 Target Server Type    : MySQL
 Target Server Version : 50729
 File Encoding         : 65001

 Date: 13/05/2024 14:09:33
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for assessments
-- ----------------------------
DROP TABLE IF EXISTS `assessments`;
CREATE TABLE `assessments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `branch_id` int(11) DEFAULT NULL,
  `student_id` int(11) DEFAULT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `created_at` datetime DEFAULT NULL,
  `creator_id` int(11) DEFAULT NULL,
  `updator_id` int(11) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `meta_data` text COLLATE utf8_unicode_ci,
  `status` tinyint(2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `student_id` (`student_id`) USING BTREE,
  KEY `branch_id` (`branch_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of assessments
-- ----------------------------
BEGIN;
INSERT INTO `assessments` VALUES (2, 1, 101752, 'demo nhận xét', 'Trên mỗi phiếu sinh hoạt hè có đầy đủ thông tin như: họ tên học sinh, lớp, học lực, hạnh kiểm cuối năm, năng khiếu cá nhân, địa chỉ; họ tên cha, họ tên mẹ và kèm theo đó là chữ ký của giáo viên chủ nhiệm, đóng dấu của nhà trường; phía sau mỗi phiếu là phần nhận xét kết quả hoạt động của tiểu ban hoạt động hè và xác', '2024-05-11 14:20:51', 1, 1, '2024-05-11 14:44:03', '{\"assessment\":{\"branch_id\":1,\"student_id\":101752,\"title\":\"demo nh\\u1eadn x\\u00e9t\",\"description\":\"n\\u1ed9i dung nh\\u1eadn x\\u00e9t\"},\"student_info\":{\"name\":\"h\\u1ecdc sinh\",\"lms_code\":\"LMS101752\",\"gud_name1\":\"demo23\",\"gud_mobile1\":\"0389946902\",\"gud_email1\":\"demo@gmail.com\",\"address\":\"ha n\\u1ed9i\",\"student_id\":101752,\"label\":\"h\\u1ecdc sinh - LMS101752\",\"product_name\":\"UCREA\",\"program_name\":\"U2019\",\"class_name\":\"demo\",\"tuition_fee_name\":\"UCREA - 6 th\\u00e1ng\",\"product_id\":1,\"class_id\":1,\"contract_id\":44068,\"enrolment_start_date\":\"2024-04-25\",\"enrolment_last_date\":\"2024-10-17\"}}', 1);
COMMIT;

-- ----------------------------
-- Table structure for branch_has_user
-- ----------------------------
DROP TABLE IF EXISTS `branch_has_user`;
CREATE TABLE `branch_has_user` (
  `branch_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`branch_id`,`user_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of branch_has_user
-- ----------------------------
BEGIN;
INSERT INTO `branch_has_user` VALUES (1, 1);
INSERT INTO `branch_has_user` VALUES (1, 2);
INSERT INTO `branch_has_user` VALUES (1, 3);
INSERT INTO `branch_has_user` VALUES (1, 4);
INSERT INTO `branch_has_user` VALUES (1, 5);
INSERT INTO `branch_has_user` VALUES (1, 6);
INSERT INTO `branch_has_user` VALUES (1, 7);
INSERT INTO `branch_has_user` VALUES (1, 8);
INSERT INTO `branch_has_user` VALUES (1, 3289);
INSERT INTO `branch_has_user` VALUES (1, 3294);
INSERT INTO `branch_has_user` VALUES (1, 3296);
INSERT INTO `branch_has_user` VALUES (2, 1);
INSERT INTO `branch_has_user` VALUES (2, 2);
INSERT INTO `branch_has_user` VALUES (2, 3);
INSERT INTO `branch_has_user` VALUES (2, 4);
INSERT INTO `branch_has_user` VALUES (2, 5);
INSERT INTO `branch_has_user` VALUES (2, 6);
INSERT INTO `branch_has_user` VALUES (2, 7);
INSERT INTO `branch_has_user` VALUES (2, 8);
INSERT INTO `branch_has_user` VALUES (2, 3289);
INSERT INTO `branch_has_user` VALUES (2, 3294);
INSERT INTO `branch_has_user` VALUES (2, 3296);
INSERT INTO `branch_has_user` VALUES (3, 1);
INSERT INTO `branch_has_user` VALUES (3, 2);
INSERT INTO `branch_has_user` VALUES (3, 3);
INSERT INTO `branch_has_user` VALUES (3, 4);
INSERT INTO `branch_has_user` VALUES (3, 5);
INSERT INTO `branch_has_user` VALUES (3, 6);
INSERT INTO `branch_has_user` VALUES (3, 7);
INSERT INTO `branch_has_user` VALUES (3, 8);
INSERT INTO `branch_has_user` VALUES (3, 3289);
INSERT INTO `branch_has_user` VALUES (3, 3294);
INSERT INTO `branch_has_user` VALUES (3, 3296);
INSERT INTO `branch_has_user` VALUES (4, 3296);
INSERT INTO `branch_has_user` VALUES (5, 3296);
INSERT INTO `branch_has_user` VALUES (10, 3294);
COMMIT;

-- ----------------------------
-- Table structure for branch_transfer
-- ----------------------------
DROP TABLE IF EXISTS `branch_transfer`;
CREATE TABLE `branch_transfer` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) DEFAULT NULL,
  `list_contract_id` int(11) DEFAULT NULL,
  `from_class_id` int(11) DEFAULT NULL,
  `from_branch_id` int(11) DEFAULT NULL,
  `from_product_id` int(11) DEFAULT NULL,
  `from_program_id` int(11) DEFAULT NULL,
  `to_class_id` int(11) DEFAULT NULL,
  `to_branch_id` int(11) DEFAULT NULL,
  `to_product_id` int(11) DEFAULT NULL,
  `to_program_id` int(11) DEFAULT NULL,
  `meta_data` text COLLATE utf8mb4_unicode_ci,
  `creator_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updator_id` int(11) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `attached_file` text COLLATE utf8mb4_unicode_ci,
  `status` tinyint(4) DEFAULT NULL COMMENT '0 xóa, 1 chờ duyệt di, 2 từ chối duyệt đi, 3 chờ duyệt đến, 4 từ chối duyệt đến, 5 đã duyệt đến, 6 đã xử lý',
  `note` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `transfer_date` date DEFAULT NULL,
  `amount_transferred` int(11) DEFAULT NULL,
  `session_transferred` int(11) DEFAULT NULL,
  `session_exchange` int(11) DEFAULT NULL,
  `amount_exchange` int(11) DEFAULT NULL,
  `from_approver_id` int(11) DEFAULT NULL,
  `from_approved_at` datetime DEFAULT NULL,
  `from_approve_comment` text COLLATE utf8mb4_unicode_ci,
  `to_approver_id` int(11) DEFAULT NULL,
  `to_approved_at` datetime DEFAULT NULL,
  `to_approve_comment` text COLLATE utf8mb4_unicode_ci,
  `final_approver_id` int(11) DEFAULT NULL,
  `final_approved_at` datetime DEFAULT NULL,
  `final_approve_comment` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `student_id` (`student_id`) USING BTREE,
  KEY `from_class_id` (`from_class_id`) USING BTREE,
  KEY `to_class_id` (`to_class_id`) USING BTREE,
  KEY `creator_id` (`creator_id`) USING BTREE,
  KEY `to_branch_id` (`to_branch_id`) USING BTREE,
  KEY `from_branch_id` (`from_branch_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of branch_transfer
-- ----------------------------
BEGIN;
INSERT INTO `branch_transfer` VALUES (1, 101750, 44066, NULL, 1, NULL, NULL, NULL, 2, NULL, NULL, '{\"branch_transfer\":{\"branch_id\":1,\"student_id\":101750,\"to_branch_id\":2,\"note\":\"ghi ch\\u00fa\",\"transfer_date\":\"2024-05-17\"},\"student_info\":{\"name\":\"h\\u1ecdc sinh\",\"lms_code\":\"LMS101750\",\"gud_name1\":\"demo23\",\"gud_mobile1\":\"0389946902\",\"gud_email1\":null,\"address\":null,\"student_id\":101750,\"label\":\"h\\u1ecdc sinh - LMS101750\",\"cm_name\":\"L\\u01b0\\u01a1ng Th\\u00e0nh C\\u00f4ng - C00001\",\"ec_name\":\"L\\u01b0\\u01a1ng Th\\u00e0nh C\\u00f4ng - C00001\"},\"from_contracts\":[{\"contract_id\":44066,\"total_charged\":1000000,\"summary_sessions\":0,\"real_sessions\":24,\"branch_id\":1,\"product_id\":1,\"bonus_sessions\":4,\"done_sessions\":0,\"left_sessions\":0,\"class_id\":null,\"code\":\"C044066\",\"enrolment_start_date\":null,\"enrolment_last_date\":null,\"class_day\":null,\"class_name\":null,\"product_name\":\"UCREA\",\"tuition_fee_name\":\"UCREA - 6 th\\u00e1ng\"},{\"contract_id\":44068,\"total_charged\":12000000,\"summary_sessions\":28,\"real_sessions\":24,\"branch_id\":1,\"product_id\":1,\"bonus_sessions\":4,\"done_sessions\":0,\"left_sessions\":0,\"class_id\":1,\"code\":\"LMS044068\",\"enrolment_start_date\":\"2024-04-25\",\"enrolment_last_date\":\"2024-10-17\",\"class_day\":\"2,3,6\",\"class_name\":\"demo\",\"product_name\":\"UCREA\",\"tuition_fee_name\":\"UCREA - 6 th\\u00e1ng\"}],\"to_contracts\":[{\"contract_id\":44066,\"total_charged\":1000000,\"summary_sessions\":0,\"real_sessions\":24,\"branch_id\":1,\"product_id\":1,\"bonus_sessions\":4,\"done_sessions\":0,\"left_sessions\":0,\"class_id\":null,\"code\":\"C044066\",\"enrolment_start_date\":null,\"enrolment_last_date\":null,\"class_day\":null,\"class_name\":null,\"product_name\":\"UCREA\",\"tuition_fee_name\":\"UCREA - 6 th\\u00e1ng\"},{\"contract_id\":44068,\"total_charged\":12000000,\"summary_sessions\":28,\"real_sessions\":24,\"branch_id\":1,\"product_id\":1,\"bonus_sessions\":4,\"done_sessions\":6,\"left_sessions\":0,\"class_id\":1,\"code\":\"LMS044068\",\"enrolment_start_date\":\"2024-04-25\",\"enrolment_last_date\":\"2024-10-17\",\"class_day\":\"2,3,6\",\"class_name\":\"demo\",\"product_name\":\"UCREA\",\"tuition_fee_name\":\"UCREA - 6 th\\u00e1ng\"}]}', 1, '2024-04-26 06:49:24', 1, '2024-04-26 08:34:53', NULL, 4, 'ghi chú', '2024-05-17', NULL, NULL, NULL, NULL, 1, '2024-04-26 08:25:20', 'ghi chú  chuyển di', 1, '2024-04-26 08:34:53', 'không đồng ý', NULL, NULL, NULL);
INSERT INTO `branch_transfer` VALUES (2, 101750, 44066, NULL, 1, NULL, NULL, NULL, 2, NULL, NULL, '{\"branch_transfer\":{\"branch_id\":1,\"student_id\":101750,\"to_branch_id\":2,\"note\":null,\"transfer_date\":\"2024-04-26\"},\"student_info\":{\"name\":\"h\\u1ecdc sinh\",\"lms_code\":\"LMS101750\",\"gud_name1\":\"demo23\",\"gud_mobile1\":\"0389946902\",\"gud_email1\":null,\"address\":null,\"student_id\":101750,\"label\":\"h\\u1ecdc sinh - LMS101750\",\"cm_name\":\"L\\u01b0\\u01a1ng Th\\u00e0nh C\\u00f4ng - C00001\",\"ec_name\":\"L\\u01b0\\u01a1ng Th\\u00e0nh C\\u00f4ng - C00001\"},\"from_contracts\":[{\"contract_id\":44066,\"total_charged\":1000000,\"summary_sessions\":0,\"real_sessions\":24,\"branch_id\":1,\"product_id\":1,\"bonus_sessions\":4,\"done_sessions\":0,\"left_sessions\":0,\"class_id\":null,\"code\":\"C044066\",\"enrolment_start_date\":null,\"enrolment_last_date\":null,\"class_day\":null,\"class_name\":null,\"product_name\":\"UCREA\",\"tuition_fee_name\":\"UCREA - 6 th\\u00e1ng\"},{\"contract_id\":44068,\"total_charged\":12000000,\"summary_sessions\":28,\"real_sessions\":24,\"branch_id\":1,\"product_id\":1,\"bonus_sessions\":4,\"done_sessions\":0,\"left_sessions\":0,\"class_id\":1,\"code\":\"LMS044068\",\"enrolment_start_date\":\"2024-04-25\",\"enrolment_last_date\":\"2024-10-17\",\"class_day\":\"2,3,6\",\"class_name\":\"demo\",\"product_name\":\"UCREA\",\"tuition_fee_name\":\"UCREA - 6 th\\u00e1ng\"}],\"to_contracts\":[{\"contract_id\":44066,\"total_charged\":1000000,\"summary_sessions\":0,\"real_sessions\":24,\"branch_id\":1,\"product_id\":1,\"bonus_sessions\":4,\"done_sessions\":0,\"left_sessions\":0,\"class_id\":null,\"code\":\"C044066\",\"enrolment_start_date\":null,\"enrolment_last_date\":null,\"class_day\":null,\"class_name\":null,\"product_name\":\"UCREA\",\"tuition_fee_name\":\"UCREA - 6 th\\u00e1ng\"},{\"contract_id\":44068,\"total_charged\":12000000,\"summary_sessions\":28,\"real_sessions\":24,\"branch_id\":1,\"product_id\":1,\"bonus_sessions\":4,\"done_sessions\":0,\"left_sessions\":0,\"class_id\":1,\"code\":\"LMS044068\",\"enrolment_start_date\":\"2024-04-25\",\"enrolment_last_date\":\"2024-10-17\",\"class_day\":\"2,3,6\",\"class_name\":\"demo\",\"product_name\":\"UCREA\",\"tuition_fee_name\":\"UCREA - 6 th\\u00e1ng\"}]}', 1, '2024-04-26 09:28:50', NULL, NULL, NULL, 1, NULL, '2024-04-26', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
COMMIT;

-- ----------------------------
-- Table structure for branches
-- ----------------------------
DROP TABLE IF EXISTS `branches`;
CREATE TABLE `branches` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'branch id lms',
  `accounting_id` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hrm_id` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `area_id` int(3) DEFAULT NULL COMMENT 'Mã phân khu vùng miền: 1 - Hà Nội, 2 - TPHCM, 3 - Các Tỉnh Khác',
  `zone_id` int(11) DEFAULT NULL,
  `region_id` int(11) DEFAULT NULL,
  `opened_date` date DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL,
  `creator_id` int(11) DEFAULT NULL,
  `updator_id` int(11) DEFAULT NULL,
  `status` tinyint(1) DEFAULT '1',
  `branch_code` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_last_date` datetime DEFAULT NULL,
  `hotline` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_lms` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `placeID` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `index_branch` (`id`) USING BTREE,
  KEY `zone_id` (`zone_id`) USING BTREE,
  KEY `region_id` (`region_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of branches
-- ----------------------------
BEGIN;
INSERT INTO `branches` VALUES (1, 'Trung tâm Giảng Võ', 'TT01', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2024-05-12 13:54:26', NULL, 1, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `branches` VALUES (2, 'Trung tâm Mỹ Đình', 'TT02', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2024-05-12 13:55:41', NULL, 1, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `branches` VALUES (3, 'Trung tâm Cầu Giấy', 'TT03', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2024-05-12 13:56:28', NULL, 1, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL);
COMMIT;

-- ----------------------------
-- Table structure for class_transfer
-- ----------------------------
DROP TABLE IF EXISTS `class_transfer`;
CREATE TABLE `class_transfer` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) DEFAULT NULL,
  `contract_id` int(11) DEFAULT NULL,
  `from_class_id` int(11) DEFAULT NULL,
  `from_branch_id` int(11) DEFAULT NULL,
  `from_product_id` int(11) DEFAULT NULL,
  `from_program_id` int(11) DEFAULT NULL,
  `to_class_id` int(11) DEFAULT NULL,
  `to_branch_id` int(11) DEFAULT NULL,
  `to_product_id` int(11) DEFAULT NULL,
  `to_program_id` int(11) DEFAULT NULL,
  `meta_data` text COLLATE utf8mb4_unicode_ci,
  `creator_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updator_id` int(11) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `attached_file` text COLLATE utf8mb4_unicode_ci,
  `status` tinyint(4) DEFAULT NULL,
  `note` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `transfer_date` date DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `student_id` (`student_id`) USING BTREE,
  KEY `from_class_id` (`from_class_id`) USING BTREE,
  KEY `to_class_id` (`to_class_id`) USING BTREE,
  KEY `creator_id` (`creator_id`) USING BTREE,
  KEY `to_branch_id` (`to_branch_id`) USING BTREE,
  KEY `from_branch_id` (`from_branch_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=45425 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of class_transfer
-- ----------------------------
BEGIN;
INSERT INTO `class_transfer` VALUES (45423, 101750, 44068, 3, 1, 1, 434, 1, 1, 1, 434, '{\"class_transfer\":{\"branch_id\":1,\"student_id\":101750,\"contract_id\":44068,\"product_id\":1,\"class_id\":1,\"note\":\"ghi ch\\u00fa\",\"transfer_date\":\"2024-04-25\",\"left_session\":28},\"student_info\":{\"name\":\"h\\u1ecdc sinh\",\"lms_code\":\"LMS101750\",\"gud_name1\":\"demo23\",\"gud_mobile1\":\"0389946902\",\"gud_email1\":null,\"address\":null,\"student_id\":101750,\"label\":\"h\\u1ecdc sinh - LMS101750\",\"product_name\":\"UCREA\",\"program_name\":\"U2019\",\"class_name\":\"demo 1\",\"tuition_fee_name\":\"UCREA - 6 th\\u00e1ng\",\"total_charged\":12000000,\"summary_sessions\":28,\"bonus_sessions\":4,\"real_sessions\":24,\"product_id\":1,\"class_id\":3,\"contract_id\":44068,\"enrolment_start_date\":\"2024-04-25\",\"enrolment_last_date\":\"2024-10-17\",\"done_sessions\":0,\"left_sessions\":0}}', 1, '2024-04-24 16:39:32', NULL, '2024-04-24 16:39:32', NULL, 2, 'ghi chú', '2024-04-25');
INSERT INTO `class_transfer` VALUES (45424, 101750, 44068, 3, 1, 1, 434, 1, 1, 1, 434, '{\"class_transfer\":{\"branch_id\":1,\"student_id\":101750,\"contract_id\":44068,\"product_id\":1,\"class_id\":1,\"note\":\"ghi ch\\u00fa\",\"transfer_date\":\"2024-04-25\",\"left_session\":28},\"student_info\":{\"name\":\"h\\u1ecdc sinh\",\"lms_code\":\"LMS101750\",\"gud_name1\":\"demo23\",\"gud_mobile1\":\"0389946902\",\"gud_email1\":null,\"address\":null,\"student_id\":101750,\"label\":\"h\\u1ecdc sinh - LMS101750\",\"product_name\":\"UCREA\",\"program_name\":\"U2019\",\"class_name\":\"demo 1\",\"tuition_fee_name\":\"UCREA - 6 th\\u00e1ng\",\"total_charged\":12000000,\"summary_sessions\":28,\"bonus_sessions\":4,\"real_sessions\":24,\"product_id\":1,\"class_id\":3,\"contract_id\":44068,\"enrolment_start_date\":\"2024-04-25\",\"enrolment_last_date\":\"2024-10-17\",\"done_sessions\":0,\"left_sessions\":0}}', 1, '2024-04-24 16:40:20', NULL, '2024-04-24 16:40:20', NULL, 2, 'ghi chú', '2024-04-25');
COMMIT;

-- ----------------------------
-- Table structure for classes
-- ----------------------------
DROP TABLE IF EXISTS `classes`;
CREATE TABLE `classes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `teacher_id` int(11) DEFAULT NULL COMMENT 'teacher user id',
  `cls_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'tên lớp trên lms',
  `product_id` int(11) DEFAULT NULL,
  `program_id` int(11) DEFAULT NULL COMMENT 'program id crm (cấp chia nhỏ nhất) VD: Seed 1, Seed 2, Seedbed 1, Seedbed 2',
  `cm_id` int(11) DEFAULT NULL,
  `cls_startdate` date DEFAULT NULL COMMENT 'ngày bắt đầu lớp trên lms',
  `cls_enddate` date DEFAULT NULL COMMENT 'ngày kết thúc lớp trên lms',
  `level_id` int(11) DEFAULT NULL COMMENT 'program level id lms',
  `class_day` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'dạng json',
  `max_students` int(11) DEFAULT '16',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `creator_id` int(11) DEFAULT NULL,
  `updator_id` int(11) DEFAULT NULL,
  `branch_id` int(11) DEFAULT NULL,
  `status` tinyint(1) DEFAULT NULL COMMENT 'Trạng thái sử dụng của lớp:\r\n0 - Lớp đang available mọi chức năng\r\n1 - Lớp không được phép sử dụng để thêm học sinh',
  `type` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `program_id` (`program_id`) USING BTREE,
  KEY `product_id` (`product_id`) USING BTREE,
  KEY `branch_id` (`branch_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of classes
-- ----------------------------
BEGIN;
INSERT INTO `classes` VALUES (1, 3, 'Lớp học thử', 3, 4, 4, '2024-05-01', '2024-06-17', NULL, '2,3,4,5,6,7,8', 16, '2024-05-12 22:01:20', '2024-05-12 22:28:42', 1, 1, 1, 1, 0);
INSERT INTO `classes` VALUES (2, 3, 'Lớp chính thức 1', 3, 4, 4, '2024-05-01', '2024-10-21', NULL, '3,8', 16, '2024-05-12 22:20:52', '2024-05-12 22:31:55', 1, 1, 1, 1, 1);
INSERT INTO `classes` VALUES (3, 3, 'Lớp chính thức 2', 3, 4, 4, '2024-05-01', '2024-10-20', NULL, '5,7', 16, '2024-05-12 22:22:14', '2024-05-12 22:32:00', 1, 1, 1, 1, 1);
INSERT INTO `classes` VALUES (4, 6, 'Lớp chính thức 1', 1, 1, 4, '2024-05-01', '2024-10-22', NULL, '2,7', 16, '2024-05-12 22:25:24', '2024-05-12 22:31:46', 1, 1, 1, 1, 1);
COMMIT;

-- ----------------------------
-- Table structure for contracts
-- ----------------------------
DROP TABLE IF EXISTS `contracts`;
CREATE TABLE `contracts` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID hợp đồng',
  `type` tinyint(2) DEFAULT '0',
  `code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Mã hợp đồng trên phần mềm kế toán',
  `student_id` int(11) DEFAULT NULL COMMENT 'Mã ID của học sinh',
  `reference_contract_id` int(11) DEFAULT NULL COMMENT 'Mã ID hợp đồng có liên quan',
  `transfer_to_contract_id` int(11) DEFAULT NULL,
  `branch_id` int(11) DEFAULT NULL COMMENT 'Mã ID trung tâm',
  `ceo_branch_id` int(11) DEFAULT NULL,
  `ec_id` int(11) DEFAULT NULL COMMENT 'Mã User ID của EC trung tâm tại thời điểm hợp đồng có hiệu lực',
  `ec_leader_id` int(11) DEFAULT NULL COMMENT 'Mã User ID của EC Leader trung tâm tại thời điểm hợp đồng có hiệu lực',
  `cm_id` int(11) DEFAULT NULL COMMENT 'Mã User ID của CM trung tâm tại thời điểm hợp đồng có hiệu lực',
  `cm_leader_id` int(11) DEFAULT NULL COMMENT 'Mã User ID của OM (CM Leader) trung tâm tại thời điểm hợp đồng có hiệu lực',
  `product_id` int(11) DEFAULT NULL COMMENT 'Mã ID sản phẩm',
  `program_id` int(11) DEFAULT NULL COMMENT 'Mã ID chương trình học',
  `tuition_fee_id` int(11) DEFAULT NULL COMMENT 'Mã ID gói phí',
  `tuition_fee_amount` int(11) DEFAULT NULL,
  `tuition_fee_receivable` int(11) DEFAULT NULL COMMENT 'Số tiền sau giảm trừ trong giá gốc theo thông tin gói phí',
  `tuition_fee_session` int(11) DEFAULT NULL,
  `must_charge` int(11) DEFAULT NULL COMMENT 'Tổng số tiền còn lại phải đóng sau khi đã trừ hết các khoản khuyến mại và chiết khấu',
  `total_charged` int(11) DEFAULT NULL COMMENT 'Tổng số tiền đã đóng',
  `debt_amount` int(11) DEFAULT NULL COMMENT 'Số tiền công nợ của hợp đồng',
  `total_discount` int(11) DEFAULT NULL,
  `discount_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Mã chiết khấu',
  `discount_code_id` int(11) DEFAULT NULL,
  `discount_code_amount` int(11) DEFAULT '0' COMMENT 'Tổng số tiền giảm trừ bao gồm các hình thức chiết khấu, voucher và hình thức khác',
  `discount_code_percent` int(11) DEFAULT NULL,
  `coupon_code` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Mã giảm giá',
  `coupon_amount` int(11) DEFAULT NULL,
  `coupon_session` int(11) DEFAULT NULL,
  `total_sessions` int(11) DEFAULT NULL COMMENT 'Tổng số buổi học đã mua theo gói phí nhập học và lựa chọn có bỏ qua học thử hay không (bỏ qua được cộng 3 buổi chỉ cho lần đầu tiên)',
  `real_sessions` int(11) DEFAULT NULL COMMENT 'Số buổi học thực tế được tính từ số tiền thực đóng',
  `bonus_sessions` int(11) DEFAULT '0',
  `summary_sessions` int(11) DEFAULT NULL,
  `reserved_sessions` int(11) DEFAULT '0' COMMENT 'Số buổi đã bảo lưu',
  `reservable_sessions` int(11) DEFAULT '0' COMMENT 'Số buổi được bảo lưu bình thường',
  `done_sessions` int(11) DEFAULT '0',
  `left_sessions` int(11) DEFAULT '0',
  `description` text COLLATE utf8mb4_unicode_ci COMMENT 'Thông tin chi tiết giảm trừ',
  `bill_info` varchar(1000) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Thông tin mô tả giảm trừ',
  `start_date` date DEFAULT NULL COMMENT 'Ngày bắt đầu nhập học',
  `end_date` date DEFAULT NULL COMMENT 'Ngày dự tính kết thúc học',
  `class_id` int(11) DEFAULT NULL COMMENT 'Mã ID lớp học được xếp vào học',
  `enrolment_start_date` date DEFAULT NULL COMMENT 'Ngày bắt đầu vào học',
  `enrolment_last_date` date DEFAULT NULL COMMENT 'Ngày kết thúc học theo số tiền thực đóng',
  `status` tinyint(1) DEFAULT '0' COMMENT '0 - Đã xóa, \r\n1 - Đã active nhưng chưa đóng phí, \r\n2 - Đã active và đặt cọc nhưng chưa thu đủ phí hoặc đang chờ nhận chuyển phí, \r\n3 - Đã active và đã thu đủ phí nhưng chưa được xếp lớp, \r\n4 - Đang bảo lưu không giữ chỗ hoặc pending, \r\n5 - Đang được nhận học bổng hoặc VIP hoặc chuyển phí \r\n6 - Đã được xếp lớp và đang đi học, \r\n7 - Đã bị withdraw, \r\n8 - Đã bỏ cọc',
  `created_at` datetime DEFAULT NULL COMMENT 'Thời gian khởi tạo bản ghi, thành lập hợp đồng',
  `creator_id` int(11) DEFAULT NULL COMMENT 'Mã User ID người lập hợp đồng',
  `updated_at` datetime DEFAULT NULL COMMENT 'Thời gian cập nhật thông tin thay đổi nội dung hợp đồng',
  `updator_id` int(11) DEFAULT NULL COMMENT 'Người thực hiện cập nhật thông tin thay đổi nội dung hợp đồng',
  `note` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Các ghi chú, lưu ý',
  `action` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Ghi chú thao tác cập nhật',
  `count_recharge` tinyint(2) DEFAULT '0',
  `type_withdraw` tinyint(2) DEFAULT NULL COMMENT '1 withdraw hết phí,2 withdraw do chuyển phí',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `ec_id` (`ec_id`) USING BTREE,
  KEY `tuition_fee_id` (`tuition_fee_id`) USING BTREE,
  KEY `creator_id` (`creator_id`) USING BTREE,
  KEY `cm_id` (`cm_id`) USING BTREE,
  KEY `student_id` (`student_id`) USING BTREE,
  KEY `branch_id` (`branch_id`) USING BTREE,
  KEY `ceo_branch_id` (`ceo_branch_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of contracts
-- ----------------------------
BEGIN;
INSERT INTO `contracts` VALUES (2, 0, 'C000002', 1, NULL, NULL, 1, 2, 5, NULL, 4, 4, 3, 4, 3, 0, 0, 2, 0, 0, 0, 0, NULL, NULL, NULL, NULL, '', 0, 0, 2, 2, 2, 2, 0, 0, 0, 0, NULL, NULL, '2024-05-01', NULL, 1, '2024-05-12', NULL, 6, '2024-05-12 21:55:13', 1, '2024-05-12 22:26:10', 1, NULL, NULL, 0, NULL);
COMMIT;

-- ----------------------------
-- Table structure for coupon_logs
-- ----------------------------
DROP TABLE IF EXISTS `coupon_logs`;
CREATE TABLE `coupon_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `coupon_id` int(11) DEFAULT NULL,
  `contract_id` int(11) DEFAULT NULL,
  `type` tinyint(2) DEFAULT '0',
  `created_at` datetime DEFAULT NULL,
  `creator_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1002 DEFAULT CHARSET=latin1;

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
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Mã ID phiếu ưu đãi',
  `code` varchar(255) DEFAULT NULL COMMENT 'Mã xác nhận phiếu ưu đãi, vd: 105100100000123',
  `alias` varchar(255) DEFAULT NULL COMMENT 'Mã định danh của phiếu, vd: 00000123',
  `type` tinyint(1) DEFAULT '1' COMMENT 'Loại phiếu ưu đãi với: \r\n 0 - Mặc định là unlimited tức không có giới hạn lượng sử dụng và không có mã định danh riêng biệt.\r\n 1 - Là có mã định danh riêng biệt cho từng coupon và số lần được sử dụng cho mỗi coupon là 1.\r\n 2 - Là không có mã định danh riêng biệt nhưng có giới hạn về số lần được sử dụng.',
  `quota` int(11) DEFAULT '1' COMMENT 'Số lần được sử dụng tối đa với coupon',
  `used` int(11) DEFAULT '0' COMMENT 'Số lần đã được sử dụng với coupon',
  `partner_id` int(11) DEFAULT NULL COMMENT 'Mã đối tác sở hữu',
  `student_id` int(11) DEFAULT NULL COMMENT 'Mã ID học sinh sử dụng phiếu',
  `branch_id` int(11) DEFAULT '0' COMMENT 'Mã trung tâm tiếp nhận phiếu',
  `coupon_amount` int(11) DEFAULT '0' COMMENT 'Giá trị của phiếu ưu đãi',
  `coupon_session` int(11) DEFAULT '0',
  `bonus_amount` int(11) DEFAULT NULL COMMENT 'Số tiền tương ứng với số buổi học bổng',
  `start_date` date DEFAULT NULL COMMENT 'Ngày bắt đầu có hiệu lực',
  `end_date` date DEFAULT NULL COMMENT 'Ngày hết hiệu lực',
  `checked_date` date DEFAULT NULL COMMENT 'Ngày sử dụng',
  `note` text COMMENT 'Ghi chú về phiếu ưu đãi',
  `status` tinyint(1) DEFAULT NULL COMMENT 'Trạng thái của phiếu với 0 là deactive, 1 là đang active và 2 là đã sử dụng',
  `created_at` datetime DEFAULT NULL COMMENT 'Thời điểm khởi tạo bản ghi',
  `creator_id` int(11) DEFAULT NULL COMMENT 'Mã ID của user khởi tạo bản ghi',
  `updated_at` datetime DEFAULT NULL,
  `updator_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `code` (`code`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=287 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of coupons
-- ----------------------------
BEGIN;
INSERT INTO `coupons` VALUES (234, 'VC0001', NULL, 1, 1, 0, NULL, NULL, 0, 0, 4, 1900000, '2024-05-01', '2025-05-01', NULL, NULL, 1, NULL, NULL, NULL, NULL);
INSERT INTO `coupons` VALUES (235, 'VC0002', NULL, 1, 1, 0, NULL, NULL, 0, 0, 4, 1900000, '2024-05-01', '2025-05-01', NULL, NULL, 1, NULL, NULL, NULL, NULL);
INSERT INTO `coupons` VALUES (250, 'VC0003', NULL, 1, 1, 0, NULL, NULL, 0, 0, 4, 1900000, '2024-05-01', '2025-05-01', NULL, NULL, 1, NULL, NULL, NULL, NULL);
INSERT INTO `coupons` VALUES (251, 'VC0004', NULL, 1, 1, 0, NULL, NULL, 0, 0, 4, 1900000, '2024-05-01', '2025-05-01', NULL, NULL, 1, NULL, NULL, NULL, NULL);
INSERT INTO `coupons` VALUES (257, 'VC0005', NULL, 1, 1, 0, NULL, NULL, 0, 0, 4, 1900000, '2024-05-01', '2025-05-01', NULL, NULL, 1, NULL, NULL, NULL, NULL);
INSERT INTO `coupons` VALUES (271, 'VC1001', NULL, 1, 1, 0, NULL, NULL, 0, 1000000, 0, 1900000, '2024-05-01', '2025-05-01', NULL, NULL, 1, NULL, NULL, NULL, NULL);
INSERT INTO `coupons` VALUES (272, 'VC1002', NULL, 1, 1, 0, NULL, NULL, 0, 1000000, 0, 1900000, '2024-05-01', '2025-05-01', NULL, NULL, 1, NULL, NULL, NULL, NULL);
INSERT INTO `coupons` VALUES (278, 'VC1003', NULL, 1, 1, 0, NULL, NULL, 0, 1000000, 0, 1900000, '2024-05-01', '2025-05-01', NULL, NULL, 1, NULL, NULL, NULL, NULL);
INSERT INTO `coupons` VALUES (286, 'VC1004', NULL, 1, 1, 0, NULL, NULL, 0, 1000000, 0, 1900000, '2024-05-01', '2025-05-01', NULL, NULL, 1, NULL, NULL, NULL, NULL);
COMMIT;

-- ----------------------------
-- Table structure for crm_contact_methods
-- ----------------------------
DROP TABLE IF EXISTS `crm_contact_methods`;
CREATE TABLE `crm_contact_methods` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint(2) NOT NULL COMMENT '0 - inactive, 1 - active',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of crm_contact_methods
-- ----------------------------
BEGIN;
INSERT INTO `crm_contact_methods` VALUES (1, 'Điện thoại ', 1, NULL, NULL);
INSERT INTO `crm_contact_methods` VALUES (2, 'Email', 1, NULL, NULL);
INSERT INTO `crm_contact_methods` VALUES (3, 'Gặp mặt trực tiếp', 1, NULL, NULL);
INSERT INTO `crm_contact_methods` VALUES (4, 'Sms', 1, NULL, NULL);
INSERT INTO `crm_contact_methods` VALUES (5, 'Zalo', 1, NULL, NULL);
COMMIT;

-- ----------------------------
-- Table structure for crm_customer_care
-- ----------------------------
DROP TABLE IF EXISTS `crm_customer_care`;
CREATE TABLE `crm_customer_care` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) DEFAULT NULL,
  `note` text COLLATE utf8_unicode_ci,
  `created_at` datetime DEFAULT NULL,
  `creator_id` int(11) DEFAULT NULL,
  `method_id` int(11) DEFAULT NULL,
  `care_date` datetime DEFAULT NULL,
  `data_id` int(11) DEFAULT NULL,
  `data_state` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `branch_id` int(11) DEFAULT NULL,
  `status` tinyint(2) DEFAULT '1',
  `attached_file` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `next_care_date` datetime DEFAULT NULL,
  `call_status` int(11) DEFAULT NULL,
  `call_status_sub` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `parent_id` (`parent_id`) USING BTREE,
  KEY `branch_id` (`branch_id`) USING BTREE,
  KEY `data_id` (`data_id`) USING BTREE,
  KEY `creator_id` (`creator_id`) USING BTREE,
  KEY `care_date` (`care_date`) USING BTREE,
  KEY `method_id` (`method_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of crm_customer_care
-- ----------------------------
BEGIN;
INSERT INTO `crm_customer_care` VALUES (1, 1, 'Khách hàng đồng ý  tư vấn', '2024-05-12 20:35:30', 1, 1, '2024-05-12 20:35:30', NULL, NULL, NULL, 3, 1, '', NULL, 7, 71);
INSERT INTO `crm_customer_care` VALUES (2, 1, 'Không có con', '2024-05-12 20:39:17', 1, 1, '2024-05-12 20:39:17', NULL, NULL, NULL, 3, 1, '', NULL, 6, 61);
INSERT INTO `crm_customer_care` VALUES (3, 1, 'Nhan tin sms tu van khach hang', '2024-05-12 20:40:02', 1, 4, '2024-05-12 20:40:02', NULL, NULL, '0389941902', 3, 1, NULL, NULL, NULL, NULL);
COMMIT;

-- ----------------------------
-- Table structure for crm_import_parents
-- ----------------------------
DROP TABLE IF EXISTS `crm_import_parents`;
CREATE TABLE `crm_import_parents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `import_id` int(11) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `gud_mobile1` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `gud_mobile2` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `address` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `note` text COLLATE utf8_unicode_ci,
  `status` tinyint(2) DEFAULT '0' COMMENT '0: là chưa xử lý, 1: ok, 2: dữ liệu  đầu vào lỗi, 3: trùng lặp dữ liệu file excel, 4: trùng lặp dữ liệu khách hàng đang chăm sóc, 6: đã import thành công',
  `created_at` datetime DEFAULT NULL,
  `creator_id` int(11) DEFAULT NULL,
  `error_message` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `student_name_1` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `student_name_2` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `student_birthday_1` date DEFAULT NULL,
  `student_birthday_2` date DEFAULT NULL,
  `owner_hrm` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `owner_id` int(11) DEFAULT NULL,
  `checkin_at` datetime DEFAULT NULL,
  `checkin_branch_accounting_id` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `is_lock` tinyint(2) DEFAULT '1',
  `parent_id` int(11) DEFAULT NULL,
  `province_id` int(11) DEFAULT NULL,
  `province_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `district_id` int(11) DEFAULT NULL,
  `district_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `import_id` (`import_id`) USING BTREE,
  KEY `gud_mobile1` (`gud_mobile1`) USING BTREE,
  KEY `gud_mobile2` (`gud_mobile2`) USING BTREE,
  KEY `parent_id` (`parent_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of crm_import_parents
-- ----------------------------
BEGIN;
INSERT INTO `crm_import_parents` VALUES (1, 10121, 'Nguyễn Thị Huế', 'demo@gmail.com', '0954112541', '0954112542', 'hà nội', 'ghi chú', 3, '2024-05-12 20:25:09', 1, 'Trùng lặp dữ liệu trong file import', 'Nguyễn Văn A', 'Nguyễn Văn B', '2015-07-08', '2016-08-16', 'C00001', 1, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `crm_import_parents` VALUES (2, 10121, 'Nguyễn Văn A', '', '0954112542', '', '', '', 6, '2024-05-12 20:25:09', 1, '', '', '', NULL, NULL, '', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `crm_import_parents` VALUES (3, 10121, 'Nguyễn Văn B', '', '0954112543', '', '', '', 6, '2024-05-12 20:25:09', 1, '', '', '', NULL, NULL, '', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `crm_import_parents` VALUES (4, 10121, 'Nguyễn Văn C', '', '0954112544', '', '', '', 6, '2024-05-12 20:25:09', 1, '', '', '', NULL, NULL, '', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `crm_import_parents` VALUES (5, 10121, 'Nguyễn Văn D', '', '0954112545', '', '', '', 6, '2024-05-12 20:25:09', 1, '', '', '', NULL, NULL, '', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `crm_import_parents` VALUES (6, 10121, 'Nguyễn Văn E', '', '0954112546', '', '', '', 6, '2024-05-12 20:25:09', 1, '', '', '', NULL, NULL, '', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `crm_import_parents` VALUES (7, 10121, 'Nguyễn Văn F', '', '0954112547', '', '', '', 6, '2024-05-12 20:25:09', 1, '', '', '', NULL, NULL, '', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `crm_import_parents` VALUES (8, 10122, 'Nguyễn Văn A', '', '0954112542', '', '', '', 4, '2024-05-12 20:33:14', 1, 'SĐT đang thuộc quyền quản lý của nhân viên CSKH - C00005 Trung tâm Giảng Võ, Trung tâm Mỹ Đình, Trung tâm Cầu Giấy', '', '', NULL, NULL, '', NULL, NULL, NULL, 1, 2, NULL, NULL, NULL, NULL);
INSERT INTO `crm_import_parents` VALUES (9, 10122, 'Nguyễn Văn B', '', '0954112543', '', '', '', 4, '2024-05-12 20:33:14', 1, 'SĐT đang thuộc quyền quản lý của nhân viên CSKH 1 - C00008 Trung tâm Giảng Võ, Trung tâm Mỹ Đình, Trung tâm Cầu Giấy', '', '', NULL, NULL, '', NULL, NULL, NULL, 1, 3, NULL, NULL, NULL, NULL);
INSERT INTO `crm_import_parents` VALUES (10, 10122, 'Nguyễn Văn G', '', '0954112548', '', '', '', 1, '2024-05-12 20:33:14', 1, '', '', '', NULL, NULL, '', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `crm_import_parents` VALUES (11, 10122, 'Nguyễn Văn H', '', '0954112549', '', '', '', 1, '2024-05-12 20:33:14', 1, '', '', '', NULL, NULL, '', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `crm_import_parents` VALUES (12, 10122, 'Nguyễn Văn I', '', '0954112550', '', '', '', 1, '2024-05-12 20:33:14', 1, '', '', '', NULL, NULL, '', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `crm_import_parents` VALUES (13, 10122, 'Nguyễn Văn J', '', '0954112551', '', '', '', 1, '2024-05-12 20:33:14', 1, '', '', '', NULL, NULL, '', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL);
COMMIT;

-- ----------------------------
-- Table structure for crm_imports
-- ----------------------------
DROP TABLE IF EXISTS `crm_imports`;
CREATE TABLE `crm_imports` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `file_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `file_link` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` tinyint(2) DEFAULT '0',
  `created_at` datetime DEFAULT NULL,
  `creator_id` int(11) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updator_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=10123 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of crm_imports
-- ----------------------------
BEGIN;
INSERT INTO `crm_imports` VALUES (10121, '64fd060b606a447e52773769bd9d6d70.xlsx', 'static/upload/64fd060b606a447e52773769bd9d6d70.xlsx', 1, '2024-05-12 20:25:09', 1, NULL, NULL);
INSERT INTO `crm_imports` VALUES (10122, 'bed49617f05b69448cdeb5259b9520a3.xlsx', 'static/upload/bed49617f05b69448cdeb5259b9520a3.xlsx', 0, '2024-05-12 20:33:14', 1, NULL, NULL);
COMMIT;

-- ----------------------------
-- Table structure for crm_parent_logs
-- ----------------------------
DROP TABLE IF EXISTS `crm_parent_logs`;
CREATE TABLE `crm_parent_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) DEFAULT NULL,
  `content` text COLLATE utf8_unicode_ci,
  `created_at` datetime DEFAULT NULL,
  `creator_id` int(11) DEFAULT NULL,
  `status` tinyint(2) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `parent_id` (`parent_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of crm_parent_logs
-- ----------------------------
BEGIN;
INSERT INTO `crm_parent_logs` VALUES (1, 1, 'Khởi tạo khách hàng thủ công', '2024-05-12 19:55:38', 1, 1);
INSERT INTO `crm_parent_logs` VALUES (2, 1, 'Thay đổi người phụ trách: từ `Lương Thành Công (C00001)` thành `CSKH (C00005)`', '2024-05-12 20:08:19', 1, 1);
INSERT INTO `crm_parent_logs` VALUES (3, 1, 'Thay đổi level khách hàng từ `` thành `C2`', '2024-05-12 20:34:08', 1, 1);
INSERT INTO `crm_parent_logs` VALUES (4, 1, 'Cập nhật lịch chăm sóc tiếp theo: 2024-05-18 09:34:00', '2024-05-12 20:34:19', 1, 1);
INSERT INTO `crm_parent_logs` VALUES (5, 1, 'Thêm mới học sinh: Lương Bảo Hân (ID: 1)', '2024-05-12 20:42:16', 1, 1);
INSERT INTO `crm_parent_logs` VALUES (6, 1, 'Thêm mới học sinh: Lương Bảo Long (ID: 2)', '2024-05-12 20:43:31', 1, 1);
COMMIT;

-- ----------------------------
-- Table structure for crm_parent_overwrite
-- ----------------------------
DROP TABLE IF EXISTS `crm_parent_overwrite`;
CREATE TABLE `crm_parent_overwrite` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) DEFAULT NULL,
  `last_branch_id` int(11) DEFAULT NULL,
  `last_owner_id` int(11) DEFAULT NULL,
  `branch_id` int(11) DEFAULT NULL,
  `owner_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `creator_id` int(11) DEFAULT NULL,
  `last_care_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `parent_id` (`parent_id`) USING BTREE,
  KEY `owner_id` (`owner_id`) USING BTREE,
  KEY `last_owner_id` (`last_owner_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

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
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `mobile_1` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `mobile_2` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `address` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `province_id` int(11) DEFAULT NULL,
  `district_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `creator_id` int(11) DEFAULT NULL,
  `gender` char(2) COLLATE utf8_unicode_ci DEFAULT NULL,
  `birthday` date DEFAULT NULL,
  `job_id` int(11) DEFAULT NULL,
  `source_detail_id` int(11) DEFAULT NULL,
  `source_id` int(11) DEFAULT NULL,
  `note` text COLLATE utf8_unicode_ci,
  `status` tinyint(2) DEFAULT '0',
  `last_care_date` datetime DEFAULT NULL,
  `last_assign_date` datetime DEFAULT NULL,
  `owner_id` int(11) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updator_id` int(11) DEFAULT NULL,
  `is_lock` tinyint(2) DEFAULT '1',
  `care_date` datetime DEFAULT NULL,
  `next_care_date` datetime DEFAULT NULL,
  `c2c_mobile` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `tmp_branch_id` int(11) DEFAULT NULL,
  `is_update` tinyint(2) DEFAULT '0',
  `old_status` tinyint(2) DEFAULT NULL,
  `last_ticket_date` datetime DEFAULT NULL,
  `level` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `mobile_1` (`mobile_1`) USING BTREE,
  KEY `mobile_2` (`mobile_2`) USING BTREE,
  KEY `owner_id` (`owner_id`) USING BTREE,
  KEY `source_id` (`source_id`) USING BTREE,
  KEY `tmp_branch_id` (`tmp_branch_id`) USING BTREE,
  KEY `creator_id` (`creator_id`) USING BTREE,
  KEY `last_care_date` (`last_care_date`) USING BTREE,
  KEY `last_assign_date` (`last_assign_date`) USING BTREE,
  KEY `status` (`status`) USING BTREE,
  KEY `source_detail_id` (`source_detail_id`) USING BTREE,
  KEY `level` (`level`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of crm_parents
-- ----------------------------
BEGIN;
INSERT INTO `crm_parents` VALUES (1, 'Lương Thành Công', 'thanhcong1710@gmail.com', '0389941902', NULL, NULL, 1, 1, '2024-05-12 19:55:38', 1, 'M', '1990-05-09', 7, 232, 26, NULL, 71, NULL, '2024-05-12 20:08:19', 5, '2024-05-12 20:35:30', 1, 1, NULL, '2024-05-18 09:34:00', NULL, NULL, 0, NULL, '2024-05-12 20:34:54', 'C2');
INSERT INTO `crm_parents` VALUES (2, 'Nguyễn Văn A', '', '0954112542', '', '', NULL, NULL, '2024-05-12 20:26:22', 1, NULL, NULL, NULL, 55, 26, '', 0, NULL, '2024-05-12 20:26:22', 5, NULL, NULL, 1, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL);
INSERT INTO `crm_parents` VALUES (3, 'Nguyễn Văn B', '', '0954112543', '', '', NULL, NULL, '2024-05-12 20:26:22', 1, NULL, NULL, NULL, 55, 26, '', 0, NULL, '2024-05-12 20:26:22', 8, NULL, NULL, 1, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL);
INSERT INTO `crm_parents` VALUES (4, 'Nguyễn Văn C', '', '0954112544', '', '', NULL, NULL, '2024-05-12 20:26:22', 1, NULL, NULL, NULL, 55, 26, '', 0, NULL, '2024-05-12 20:26:22', 5, NULL, NULL, 1, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL);
INSERT INTO `crm_parents` VALUES (5, 'Nguyễn Văn D', '', '0954112545', '', '', NULL, NULL, '2024-05-12 20:26:22', 1, NULL, NULL, NULL, 55, 26, '', 0, NULL, '2024-05-12 20:26:22', 8, NULL, NULL, 1, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL);
INSERT INTO `crm_parents` VALUES (6, 'Nguyễn Văn E', '', '0954112546', '', '', NULL, NULL, '2024-05-12 20:26:22', 1, NULL, NULL, NULL, 55, 26, '', 0, NULL, '2024-05-12 20:26:22', 5, NULL, NULL, 1, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL);
INSERT INTO `crm_parents` VALUES (7, 'Nguyễn Văn F', '', '0954112547', '', '', NULL, NULL, '2024-05-12 20:26:22', 1, NULL, NULL, NULL, 55, 26, '', 0, NULL, '2024-05-12 20:26:22', 8, NULL, NULL, 1, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL);
COMMIT;

-- ----------------------------
-- Table structure for crm_students
-- ----------------------------
DROP TABLE IF EXISTS `crm_students`;
CREATE TABLE `crm_students` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `gender` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `school_level` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `school` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `birthday` date DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `creator_id` int(11) DEFAULT NULL,
  `status` tinyint(2) DEFAULT '0' COMMENT '0: mới tạo, 1: tạo checkin, 2: đã đến checkin, 3: lên chính thức',
  `note` text COLLATE utf8_unicode_ci,
  `gud_mobile_1` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updator_id` int(11) DEFAULT NULL,
  `checkin_owner_id` int(11) DEFAULT NULL,
  `checkin_branch_id` int(11) DEFAULT NULL,
  `checkin_at` datetime DEFAULT NULL,
  `checkined_at` datetime DEFAULT NULL,
  `checkined_by` int(11) DEFAULT NULL,
  `checkined_note` text COLLATE utf8_unicode_ci,
  `lms_id` int(11) DEFAULT NULL,
  `checkin_branch_accounting_id` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `type_product` tinyint(2) DEFAULT '0',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `parent_id` (`parent_id`) USING BTREE,
  KEY `checkin_branch_id` (`checkin_branch_id`) USING BTREE,
  KEY `lms_id` (`lms_id`) USING BTREE,
  KEY `checkin_owner_id` (`checkin_owner_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of crm_students
-- ----------------------------
BEGIN;
INSERT INTO `crm_students` VALUES (1, 1, 'Lương Bảo Hân', 'F', NULL, NULL, '2018-09-08', '2024-05-12 20:42:16', 1, 2, 'Ghi chú học sinh Lương Bảo Hân', NULL, '2024-05-12 20:45:23', 1, 5, 1, '2024-05-07 10:00:00', '2024-05-09 09:00:00', 1, 'Đã đến checkin', NULL, NULL, 3);
INSERT INTO `crm_students` VALUES (2, 1, 'Lương Bảo Long', 'M', NULL, NULL, '2021-05-27', '2024-05-12 20:43:31', 1, 3, 'Ghi chú học sinh Lương Bảo Long', NULL, '2024-05-12 20:44:32', 1, 5, 1, '2024-05-10 09:00:00', '2024-05-10 10:00:00', 1, 'Đã đến checkin và hướng dẫn test đầu vào', 1, NULL, 3);
COMMIT;

-- ----------------------------
-- Table structure for crm_tickets
-- ----------------------------
DROP TABLE IF EXISTS `crm_tickets`;
CREATE TABLE `crm_tickets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) DEFAULT NULL,
  `type` tinyint(2) DEFAULT NULL COMMENT '1: yêu cầu dịch vụ, 2 hỗ trợ, 3 khiếu lại, 4 báo lỗi',
  `description` tinytext COLLATE utf8_unicode_ci,
  `note` tinytext COLLATE utf8_unicode_ci,
  `created_at` datetime DEFAULT NULL,
  `creator_id` int(11) DEFAULT NULL,
  `status` tinyint(2) DEFAULT NULL COMMENT '1- Mới , 2 đã tiếp nhận, 3 chờ xử lý, 4 hoàn thành, 5 hủy',
  `updator_id` int(11) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `parent_id` (`parent_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of crm_tickets
-- ----------------------------
BEGIN;
INSERT INTO `crm_tickets` VALUES (3, 1103095, 1, 'demo', NULL, '2024-05-09 00:30:08', 1, 2, 1, '2024-05-09 00:51:36');
INSERT INTO `crm_tickets` VALUES (4, 1103094, 1, 'Việc tạo ra các trạng thái ticket, giúp người dùng dễ dàng nhận biết được công việc của mình đang ở mức độ nào.', 'giúp người dùng dễ dàng nhận biết được công việc của mình đang ở mức độ nào.', '2024-05-09 00:52:40', 1, 4, 1, '2024-05-12 13:32:57');
INSERT INTO `crm_tickets` VALUES (5, 1, 1, 'Đăng ký khóa học FasTracKids - Thủ lĩnh nhí', NULL, '2024-05-12 20:34:54', 1, 1, NULL, NULL);
COMMIT;

-- ----------------------------
-- Table structure for discount_codes
-- ----------------------------
DROP TABLE IF EXISTS `discount_codes`;
CREATE TABLE `discount_codes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Mã định danh cho mã chiết khấu giảm giá',
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Tên mã chiết khấu',
  `percent` float(11,2) NOT NULL COMMENT 'Phần trăm giảm giá của mã chiết khấu',
  `price` float DEFAULT NULL COMMENT 'Giá gốc',
  `discount` double DEFAULT NULL COMMENT 'Số tiền chiết khấu',
  `bonus_sessions` int(11) DEFAULT NULL COMMENT 'Số buổi học bổng đi kèm mã triết khấu',
  `start_date` date NOT NULL COMMENT 'Ngày bắt đầu có hiệu lực',
  `end_date` date NOT NULL COMMENT 'Ngày hết hạn hiệu lực',
  `created_at` datetime DEFAULT NULL,
  `creator_id` int(11) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updator_id` int(11) DEFAULT NULL,
  `status` tinyint(2) DEFAULT NULL COMMENT '0 = không hoạt động, 1 = hoạt động',
  `fee_ids` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `code` (`code`) USING BTREE,
  UNIQUE KEY `code_2` (`code`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of discount_codes
-- ----------------------------
BEGIN;
INSERT INTO `discount_codes` VALUES (1, 'CK0015', 'Chiết khấu 15%  FasTracKids - 12 tháng', 15.00, 26000000, 3900000, 0, '2024-05-01', '2025-05-01', '2024-05-12 14:36:09', 1, NULL, NULL, 1, '5');
INSERT INTO `discount_codes` VALUES (2, 'CK0010', 'Chiết khấu 10% FasTracKids - 12 tháng', 10.00, 26000000, 2600000, 0, '2024-05-01', '2025-05-01', '2024-05-12 14:37:35', 1, NULL, NULL, 1, '5');
COMMIT;

-- ----------------------------
-- Table structure for districts
-- ----------------------------
DROP TABLE IF EXISTS `districts`;
CREATE TABLE `districts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `province_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `accounting_id` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
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
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lock` tinyint(2) DEFAULT '0',
  `status` tinyint(2) DEFAULT '0',
  `email_from` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `email_to` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `email_subject` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `email_body` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `email_cc` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `email_attack` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `created_at` datetime DEFAULT NULL,
  `add_queue_at` datetime DEFAULT NULL,
  `send_at` datetime DEFAULT NULL,
  `send_code` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of email_queues
-- ----------------------------
BEGIN;
INSERT INTO `email_queues` VALUES (2, 0, 1, '[]', '[\"thanhcong1710@gmail.com\"]', 'Test gửi mail', '<h2>Test</h2><br>afdjhgb  ', '[]', '[]', '2024-05-03 15:16:29', '2024-05-03 15:18:26', '2024-05-03 15:18:31', 'SUCCESS');
COMMIT;

-- ----------------------------
-- Table structure for jobs
-- ----------------------------
DROP TABLE IF EXISTS `jobs`;
CREATE TABLE `jobs` (
  `id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `status` tinyint(2) DEFAULT '0',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID hợp đồng',
  `contract_id` int(11) DEFAULT NULL,
  `type` tinyint(2) DEFAULT '0',
  `code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Mã hợp đồng trên phần mềm kế toán',
  `student_id` int(11) DEFAULT NULL COMMENT 'Mã ID của học sinh',
  `reference_contract_id` int(11) DEFAULT NULL COMMENT 'Mã ID hợp đồng có liên quan',
  `transfer_to_contract_id` int(11) DEFAULT NULL,
  `branch_id` int(11) DEFAULT NULL COMMENT 'Mã ID trung tâm',
  `ceo_branch_id` int(11) DEFAULT NULL,
  `ec_id` int(11) DEFAULT NULL COMMENT 'Mã User ID của EC trung tâm tại thời điểm hợp đồng có hiệu lực',
  `ec_leader_id` int(11) DEFAULT NULL COMMENT 'Mã User ID của EC Leader trung tâm tại thời điểm hợp đồng có hiệu lực',
  `cm_id` int(11) DEFAULT NULL COMMENT 'Mã User ID của CM trung tâm tại thời điểm hợp đồng có hiệu lực',
  `cm_leader_id` int(11) DEFAULT NULL COMMENT 'Mã User ID của OM (CM Leader) trung tâm tại thời điểm hợp đồng có hiệu lực',
  `product_id` int(11) DEFAULT NULL COMMENT 'Mã ID sản phẩm',
  `program_id` int(11) DEFAULT NULL COMMENT 'Mã ID chương trình học',
  `tuition_fee_id` int(11) DEFAULT NULL COMMENT 'Mã ID gói phí',
  `tuition_fee_amount` int(11) DEFAULT NULL,
  `tuition_fee_receivable` int(11) DEFAULT NULL COMMENT 'Số tiền sau giảm trừ trong giá gốc theo thông tin gói phí',
  `tuition_fee_session` int(11) DEFAULT NULL,
  `must_charge` int(11) DEFAULT NULL COMMENT 'Tổng số tiền còn lại phải đóng sau khi đã trừ hết các khoản khuyến mại và chiết khấu',
  `total_charged` int(11) DEFAULT NULL COMMENT 'Tổng số tiền đã đóng',
  `debt_amount` int(11) DEFAULT NULL COMMENT 'Số tiền công nợ của hợp đồng',
  `total_discount` int(11) DEFAULT NULL,
  `discount_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Mã chiết khấu',
  `discount_code_id` int(11) DEFAULT NULL,
  `discount_code_amount` int(11) DEFAULT '0' COMMENT 'Tổng số tiền giảm trừ bao gồm các hình thức chiết khấu, voucher và hình thức khác',
  `discount_code_percent` int(11) DEFAULT NULL,
  `coupon_code` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Mã giảm giá',
  `coupon_amount` int(11) DEFAULT NULL,
  `coupon_session` int(11) DEFAULT NULL,
  `total_sessions` int(11) DEFAULT NULL COMMENT 'Tổng số buổi học đã mua theo gói phí nhập học và lựa chọn có bỏ qua học thử hay không (bỏ qua được cộng 3 buổi chỉ cho lần đầu tiên)',
  `real_sessions` int(11) DEFAULT NULL COMMENT 'Số buổi học thực tế được tính từ số tiền thực đóng',
  `bonus_sessions` int(11) DEFAULT '0',
  `summary_sessions` int(11) DEFAULT NULL,
  `reserved_sessions` int(11) DEFAULT '0' COMMENT 'Số buổi đã bảo lưu',
  `reservable_sessions` int(11) DEFAULT '0' COMMENT 'Số buổi được bảo lưu bình thường',
  `done_sessions` int(11) DEFAULT '0',
  `left_sessions` int(11) DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci COMMENT 'Thông tin chi tiết giảm trừ',
  `bill_info` varchar(1000) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Thông tin mô tả giảm trừ',
  `start_date` date DEFAULT NULL COMMENT 'Ngày bắt đầu nhập học',
  `end_date` date DEFAULT NULL COMMENT 'Ngày dự tính kết thúc học',
  `class_id` int(11) DEFAULT NULL COMMENT 'Mã ID lớp học được xếp vào học',
  `enrolment_start_date` date DEFAULT NULL COMMENT 'Ngày bắt đầu vào học',
  `enrolment_last_date` date DEFAULT NULL COMMENT 'Ngày kết thúc học theo số tiền thực đóng',
  `status` tinyint(1) DEFAULT '0' COMMENT '0 - Đã xóa, \r\n1 - Đã active nhưng chưa đóng phí, \r\n2 - Đã active và đặt cọc nhưng chưa thu đủ phí hoặc đang chờ nhận chuyển phí, \r\n3 - Đã active và đã thu đủ phí nhưng chưa được xếp lớp, \r\n4 - Đang bảo lưu không giữ chỗ hoặc pending, \r\n5 - Đang được nhận học bổng hoặc VIP, \r\n6 - Đã được xếp lớp và đang đi học, \r\n7 - Đã bị withdraw, \r\n8 - Đã bỏ cọc',
  `created_at` datetime DEFAULT NULL COMMENT 'Thời gian khởi tạo bản ghi, thành lập hợp đồng',
  `creator_id` int(11) DEFAULT NULL COMMENT 'Mã User ID người lập hợp đồng',
  `updated_at` datetime DEFAULT NULL COMMENT 'Thời gian cập nhật thông tin thay đổi nội dung hợp đồng',
  `updator_id` int(11) DEFAULT NULL COMMENT 'Người thực hiện cập nhật thông tin thay đổi nội dung hợp đồng',
  `note` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Các ghi chú, lưu ý',
  `action` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Ghi chú thao tác cập nhật',
  `count_recharge` tinyint(2) DEFAULT NULL,
  `type_withdraw` tinyint(2) DEFAULT NULL COMMENT '1 withdraw hết phí,2 withdraw do chuyển phí',
  `log_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `ec_id` (`ec_id`) USING BTREE,
  KEY `tuition_fee_id` (`tuition_fee_id`) USING BTREE,
  KEY `creator_id` (`creator_id`) USING BTREE,
  KEY `cm_id` (`cm_id`) USING BTREE,
  KEY `student_id` (`student_id`) USING BTREE,
  KEY `branch_id` (`branch_id`) USING BTREE,
  KEY `ceo_branch_id` (`ceo_branch_id`) USING BTREE,
  KEY `contract_id` (`contract_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=44093 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of log_contracts
-- ----------------------------
BEGIN;
INSERT INTO `log_contracts` VALUES (44067, 44067, 1, 'LMS044067', 101750, NULL, NULL, 1, NULL, NULL, NULL, 1, NULL, 1, NULL, 262, 12000000, 12000000, 24, 12000000, 0, 12000000, NULL, NULL, 1070, 0, NULL, 'CMSHBD05', 0, 4, 28, 24, 4, 0, 0, 0, 0, NULL, NULL, NULL, '2024-04-19', NULL, NULL, NULL, NULL, 0, '2024-04-19 10:40:32', 1, NULL, NULL, 'ghi chú', NULL, NULL, NULL, NULL);
INSERT INTO `log_contracts` VALUES (44068, 44068, 1, 'LMS044068', 101750, NULL, NULL, 1, NULL, NULL, NULL, 1, NULL, 1, NULL, 262, 12000000, 12000000, 24, 12000000, 0, 12000000, NULL, NULL, 1070, 0, NULL, 'CMSHBD05', 0, 4, 28, 24, 4, 0, 0, 0, 0, NULL, NULL, NULL, '2024-04-19', NULL, NULL, NULL, NULL, 1, '2024-04-19 10:41:43', 1, NULL, NULL, 'ghi chú', NULL, NULL, NULL, NULL);
INSERT INTO `log_contracts` VALUES (44069, 44067, 1, 'LMS044067', 101750, NULL, NULL, 1, NULL, NULL, NULL, 1, NULL, 1, NULL, 262, 12000000, 12000000, 24, 12000000, 0, 12000000, NULL, NULL, 1070, 0, NULL, 'CMSHBD05', 0, 4, 28, 24, 4, 0, 0, 0, 0, NULL, NULL, NULL, '2024-04-19', NULL, NULL, NULL, NULL, 0, '2024-04-19 10:40:32', 1, NULL, NULL, 'ghi chú', NULL, NULL, NULL, NULL);
INSERT INTO `log_contracts` VALUES (44070, 44066, 1, 'C044066', 101750, NULL, NULL, 1, 5, 1, 2, 3, 4, 1, NULL, 262, 12000000, 12000000, 24, 12000000, 0, 12000000, NULL, NULL, 1070, 0, 30, '', 0, 0, 28, 24, 0, 0, 0, 0, 0, NULL, NULL, NULL, '2024-04-19', NULL, NULL, NULL, NULL, 1, '2024-04-19 10:39:17', 1, '2024-04-19 16:49:32', 1, 'ghi chú demo', NULL, NULL, NULL, NULL);
INSERT INTO `log_contracts` VALUES (44071, 44066, 1, 'C044066', 101750, NULL, NULL, 1, 5, 1, 2, 3, 4, 1, NULL, 262, 12000000, 12000000, 24, 12000000, 0, 12000000, NULL, NULL, 1070, 0, 30, 'CMSHBD02', 0, 4, 28, 24, 4, 0, 0, 0, 0, NULL, NULL, NULL, '2024-04-19', NULL, NULL, NULL, NULL, 1, '2024-04-19 10:39:17', 1, '2024-04-19 16:51:11', 1, 'ghi chú demo', NULL, NULL, NULL, NULL);
INSERT INTO `log_contracts` VALUES (44072, 44066, 1, 'C044066', 101750, NULL, NULL, 1, 5, 1, 2, 3, 4, 1, NULL, 262, 12000000, 12000000, 24, 12000000, 0, 12000000, NULL, NULL, 1070, 0, 30, 'CMSHBD02', 0, 4, 28, 24, 4, 0, 0, 0, 0, NULL, NULL, NULL, '2024-04-19', NULL, NULL, NULL, NULL, 1, '2024-04-19 10:39:17', 1, '2024-04-19 16:52:42', 1, 'ghi chú demo', NULL, NULL, NULL, NULL);
INSERT INTO `log_contracts` VALUES (44073, 44066, 1, 'C044066', 101750, NULL, NULL, 1, 5, 1, 2, 3, 4, 1, NULL, 262, 12000000, 12000000, 24, 12000000, 0, 12000000, NULL, NULL, 1070, 0, 30, 'CMSHBD03', 0, 4, 28, 24, 4, 0, 0, 0, 0, NULL, NULL, NULL, '2024-04-19', NULL, NULL, NULL, NULL, 1, '2024-04-19 10:39:17', 1, '2024-04-19 16:53:31', 1, 'ghi chú demo', NULL, NULL, NULL, NULL);
INSERT INTO `log_contracts` VALUES (44074, 44066, 1, 'C044066', 101750, NULL, NULL, 1, 5, 1, 2, 3, 4, 1, NULL, 262, 12000000, 12000000, 24, 12000000, 0, 12000000, NULL, NULL, 1070, 0, 30, 'CMSHBD03', 0, 4, 28, 24, 4, 0, 0, 0, 0, NULL, NULL, NULL, '2024-04-19', NULL, NULL, NULL, NULL, 1, '2024-04-19 10:39:17', 1, '2024-04-19 16:55:59', 1, 'ghi chú demo', NULL, NULL, NULL, NULL);
INSERT INTO `log_contracts` VALUES (44075, 44066, 1, 'C044066', 101750, NULL, NULL, 1, 5, 1, 2, 3, 4, 1, NULL, 262, 12000000, 12000000, 24, 12000000, 0, 12000000, NULL, NULL, 1070, 0, 30, 'CMSHBD03', 0, 4, 28, 24, 4, 0, 0, 0, 0, NULL, NULL, NULL, '2024-04-19', NULL, NULL, NULL, NULL, 1, '2024-04-19 10:39:17', 1, '2024-04-19 16:57:20', 1, 'ghi chú demo', NULL, NULL, NULL, NULL);
INSERT INTO `log_contracts` VALUES (44076, 44066, 1, 'C044066', 101750, NULL, NULL, 1, 5, 1, 2, 3, 4, 1, NULL, 262, 12000000, 12000000, 24, 12000000, 0, 12000000, NULL, NULL, 1070, 0, 30, 'CMSHBD03', 0, 4, 28, 24, 4, 0, 0, 0, 0, NULL, NULL, NULL, '2024-04-19', NULL, NULL, NULL, NULL, 1, '2024-04-19 10:39:17', 1, '2024-04-19 16:57:52', 1, 'ghi chú demo', NULL, NULL, NULL, NULL);
INSERT INTO `log_contracts` VALUES (44077, 44068, 1, 'LMS044068', 101750, NULL, NULL, 1, NULL, 1, 1, 1, NULL, 1, NULL, 262, 12000000, 12000000, 24, 12000000, 1000, 11999000, NULL, NULL, 1070, 0, NULL, 'CMSHBD05', 0, 4, 28, 24, 4, 0, 0, 0, 0, NULL, NULL, NULL, '2024-04-19', NULL, NULL, NULL, NULL, 2, '2024-04-19 10:41:43', 1, '2024-04-20 05:21:17', 1, 'ghi chú', NULL, NULL, NULL, NULL);
INSERT INTO `log_contracts` VALUES (44078, 44068, 1, 'LMS044068', 101750, NULL, NULL, 1, NULL, 1, 1, 1, NULL, 1, NULL, 262, 12000000, 12000000, 24, 12000000, 2001000, 9999000, NULL, NULL, 1070, 0, NULL, 'CMSHBD05', 0, 4, 28, 24, 4, 0, 0, 0, 0, NULL, NULL, NULL, '2024-04-19', NULL, NULL, NULL, NULL, 2, '2024-04-19 10:41:43', 1, '2024-04-20 05:21:55', 1, 'ghi chú', NULL, NULL, NULL, NULL);
INSERT INTO `log_contracts` VALUES (44079, 44068, 1, 'LMS044068', 101750, NULL, NULL, 1, NULL, 1, 1, 1, NULL, 1, NULL, 262, 12000000, 12000000, 24, 12000000, 4001000, 7999000, NULL, NULL, 1070, 0, NULL, 'CMSHBD05', 0, 4, 28, 24, 4, 0, 0, 0, 0, NULL, NULL, NULL, '2024-04-19', NULL, NULL, NULL, NULL, 2, '2024-04-19 10:41:43', 1, '2024-04-20 09:02:48', 1, 'ghi chú', NULL, NULL, NULL, NULL);
INSERT INTO `log_contracts` VALUES (44080, 44068, 1, 'LMS044068', 101750, NULL, NULL, 1, NULL, 1, 1, 1, NULL, 1, NULL, 262, 12000000, 12000000, 24, 12000000, 12000000, 0, NULL, NULL, 1070, 0, NULL, 'CMSHBD05', 0, 4, 28, 24, 4, 28, 0, 1, 0, NULL, NULL, NULL, '2024-04-19', NULL, NULL, NULL, NULL, 3, '2024-04-19 10:41:43', 1, '2024-04-20 09:04:45', 1, 'ghi chú', NULL, NULL, NULL, NULL);
INSERT INTO `log_contracts` VALUES (44081, 44066, 1, 'C044066', 101750, NULL, NULL, 1, 5, 1, 2, 3, 4, 1, NULL, 262, 12000000, 12000000, 24, 12000000, 1000000, 11000000, NULL, NULL, 1070, 0, 30, 'CMSHBD03', 0, 4, 28, 24, 4, 0, 0, 0, 0, NULL, NULL, NULL, '2024-04-19', NULL, NULL, NULL, NULL, 2, '2024-04-19 10:39:17', 1, '2024-04-20 09:09:15', 1, 'ghi chú demo', NULL, NULL, NULL, NULL);
INSERT INTO `log_contracts` VALUES (44082, 44068, 1, 'LMS044068', 101750, NULL, NULL, 1, NULL, 1, 1, 1, 1, 1, 434, 262, 12000000, 12000000, 24, 12000000, 12000000, 0, NULL, NULL, 1070, 0, NULL, 'CMSHBD05', 0, 4, 28, 24, 4, 28, 0, 1, 0, NULL, NULL, NULL, '2024-04-19', NULL, 3, '2024-04-25', NULL, 6, '2024-04-19 10:41:43', 1, '2024-04-23 08:00:54', 1, 'ghi chú', NULL, 0, NULL, NULL);
INSERT INTO `log_contracts` VALUES (44083, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 0, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `log_contracts` VALUES (44084, 44068, 1, 'LMS044068', 101750, NULL, NULL, 1, NULL, 1, 1, 1, 1, 1, 434, 262, 12000000, 12000000, 24, 12000000, 12000000, 0, NULL, NULL, 1070, 0, NULL, 'CMSHBD05', 0, 4, 28, 24, 4, 28, 0, 1, 0, NULL, NULL, NULL, '2024-04-19', NULL, 3, '2024-04-25', '2024-10-17', 6, '2024-04-19 10:41:43', 1, '2024-04-23 08:00:54', 1, 'ghi chú', NULL, 0, NULL, NULL);
INSERT INTO `log_contracts` VALUES (44085, 44068, 1, 'LMS044068', 101750, NULL, NULL, 1, NULL, 1, 1, 1, 1, 1, 434, 262, 12000000, 12000000, 24, 12000000, 12000000, 0, NULL, NULL, 1070, 0, NULL, 'CMSHBD05', 0, 4, 28, 24, 4, 28, 2, 1, 0, NULL, NULL, NULL, '2024-04-19', NULL, 3, '2024-04-25', '2024-10-17', 6, '2024-04-19 10:41:43', 1, '2024-04-24 07:34:14', 1, 'ghi chú', NULL, 0, NULL, NULL);
INSERT INTO `log_contracts` VALUES (44086, 44068, 1, 'LMS044068', 101750, NULL, NULL, 1, NULL, 1, 1, 1, 1, 1, 434, 262, 12000000, 12000000, 24, 12000000, 12000000, 0, NULL, NULL, 1070, 0, NULL, 'CMSHBD05', 0, 4, 28, 24, 4, 28, 2, 1, 0, NULL, NULL, NULL, '2024-04-19', NULL, 1, '2024-04-25', '2024-10-17', 6, '2024-04-19 10:41:43', 1, '2024-04-24 16:40:20', 1, 'ghi chú', NULL, 0, NULL, NULL);
INSERT INTO `log_contracts` VALUES (44087, 44068, 1, 'LMS044068', 101750, NULL, NULL, 1, NULL, 1, 1, 1, 1, 1, 434, 262, 12000000, 12000000, 24, 12000000, 12000000, 0, NULL, NULL, 1070, 0, NULL, 'CMSHBD05', 0, 4, 28, 24, 4, 28, 2, 1, 0, 28, NULL, NULL, '2024-04-19', NULL, 1, '2024-04-25', '2024-10-17', 6, '2024-04-19 10:41:43', 1, '2024-04-24 16:40:20', 1, 'ghi chú', NULL, 0, NULL, NULL);
INSERT INTO `log_contracts` VALUES (44088, 44068, 1, 'LMS044068', 101750, NULL, NULL, 1, NULL, 1, 1, 1, 1, 1, 434, 262, 12000000, 12000000, 24, 12000000, 12000000, 0, NULL, NULL, 1070, 0, NULL, 'CMSHBD05', 0, 4, 28, 24, 4, 28, 2, 1, 0, 28, NULL, NULL, '2024-04-19', NULL, 1, '2024-04-25', '2024-10-17', 6, '2024-04-19 10:41:43', 1, '2024-04-24 16:40:20', 1, 'ghi chú 32', NULL, 0, NULL, '2024-05-01 13:55:09');
INSERT INTO `log_contracts` VALUES (44089, 44068, 1, 'LMS044068', 101750, NULL, NULL, 1, NULL, 1, 1, 1, 1, 1, 434, 262, 12000000, 12000000, 24, 12000000, 12000000, 0, NULL, NULL, 1070, 0, NULL, 'CMSHBD05', 0, 4, 28, 24, 4, 28, 2, 1, 0, 28, NULL, NULL, '2024-04-19', NULL, 1, '2024-04-25', '2024-10-17', 6, '2024-04-19 10:41:43', 1, '2024-04-24 16:40:20', 1, 'ghi chú', NULL, 0, NULL, '2024-05-01 20:56:27');
INSERT INTO `log_contracts` VALUES (44090, 1, 0, 'C000001', 1, NULL, NULL, 1, 2, 5, NULL, NULL, NULL, 3, NULL, 2, 0, 0, 1, 0, 0, 0, 0, NULL, NULL, NULL, NULL, '', 0, 0, 1, 1, 0, 0, 0, 0, 0, 0, NULL, NULL, '2024-05-05', NULL, NULL, NULL, NULL, 1, '2024-05-12 21:03:41', 1, NULL, NULL, NULL, NULL, 0, NULL, '2024-05-12 21:03:41');
INSERT INTO `log_contracts` VALUES (44091, 2, 0, 'C000002', 1, NULL, NULL, 1, 2, 5, NULL, NULL, NULL, 3, NULL, 3, 0, 0, 2, 0, 0, 0, 0, NULL, NULL, NULL, NULL, '', 0, 0, 2, 2, 2, 2, 0, 0, 0, 0, NULL, NULL, '2024-05-01', NULL, NULL, NULL, NULL, 3, '2024-05-12 21:55:13', 1, NULL, NULL, NULL, NULL, 0, NULL, '2024-05-12 21:55:13');
INSERT INTO `log_contracts` VALUES (44092, 2, 0, 'C000002', 1, NULL, NULL, 1, 2, 5, NULL, 4, 4, 3, 4, 3, 0, 0, 2, 0, 0, 0, 0, NULL, NULL, NULL, NULL, '', 0, 0, 2, 2, 2, 2, 0, 0, 0, 0, NULL, NULL, '2024-05-01', NULL, 1, '2024-05-12', NULL, 6, '2024-05-12 21:55:13', 1, '2024-05-12 22:26:10', 1, NULL, NULL, 0, NULL, '2024-05-12 22:26:11');
COMMIT;

-- ----------------------------
-- Table structure for log_jobs
-- ----------------------------
DROP TABLE IF EXISTS `log_jobs`;
CREATE TABLE `log_jobs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `action` varchar(255) DEFAULT NULL,
  `data` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1342 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of log_jobs
-- ----------------------------
BEGIN;
INSERT INTO `log_jobs` VALUES (1331, 'ProcessDataScheduleHasStudent', '2024-05-01', '2024-05-01 15:47:40');
INSERT INTO `log_jobs` VALUES (1332, 'ProcessDataScheduleHasStudent', '2024-05-02', '2024-05-01 15:48:50');
INSERT INTO `log_jobs` VALUES (1333, 'ProcessDataScheduleHasStudent', '2024-05-02', '2024-05-01 15:51:59');
INSERT INTO `log_jobs` VALUES (1334, 'ProcessDataScheduleHasStudent', '2024-05-02', '2024-05-01 19:39:25');
INSERT INTO `log_jobs` VALUES (1335, 'ProcessDataScheduleHasStudent', '2024-05-02', '2024-05-01 19:40:00');
INSERT INTO `log_jobs` VALUES (1336, 'ProcessDataScheduleHasStudent', '2024-05-02', '2024-05-01 19:42:06');
INSERT INTO `log_jobs` VALUES (1337, 'ProcessDataScheduleHasStudent', '2024-05-02', '2024-05-01 19:43:14');
INSERT INTO `log_jobs` VALUES (1338, 'ProcessDataScheduleHasStudent', '2024-05-02', '2024-05-01 19:43:53');
INSERT INTO `log_jobs` VALUES (1339, 'AddScheduleHasStudent', '2024-05-02', '2024-05-01 20:53:28');
INSERT INTO `log_jobs` VALUES (1340, 'AddScheduleHasStudent', '2024-05-02', '2024-05-01 20:54:49');
INSERT INTO `log_jobs` VALUES (1341, 'AddScheduleHasStudent', '2024-05-02', '2024-05-01 20:56:27');
COMMIT;

-- ----------------------------
-- Table structure for make_up_sessions
-- ----------------------------
DROP TABLE IF EXISTS `make_up_sessions`;
CREATE TABLE `make_up_sessions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) DEFAULT NULL,
  `schedule_student_id` int(11) DEFAULT NULL,
  `from_class_id` int(11) DEFAULT NULL,
  `from_class_date` date DEFAULT NULL,
  `to_class_id` int(11) DEFAULT NULL,
  `to_class_date` date DEFAULT NULL,
  `status` tinyint(2) DEFAULT NULL COMMENT '0 :xóa, 1: đăng ký học bù, 2 đã học bù, 3 không đến học bù',
  `created_at` datetime DEFAULT NULL,
  `creator_id` int(11) DEFAULT NULL,
  `updator_id` int(11) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of make_up_sessions
-- ----------------------------
BEGIN;
INSERT INTO `make_up_sessions` VALUES (1, 101751, 5, 3, '0000-00-00', 3, '2024-05-12', 3, '2024-05-10 17:35:22', 1, 1, '2024-05-10 18:29:13');
INSERT INTO `make_up_sessions` VALUES (2, 101751, 5, 1, '2024-05-07', 3, '2024-05-12', 2, '2024-05-10 22:01:30', 1, 1, '2024-05-10 22:01:48');
COMMIT;

-- ----------------------------
-- Table structure for payments
-- ----------------------------
DROP TABLE IF EXISTS `payments`;
CREATE TABLE `payments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `contract_id` int(11) DEFAULT NULL,
  `accounting_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `method` tinyint(2) DEFAULT '0' COMMENT '0 - tiền mặt, 1 - chuyển khoản 2- Thanh toán thẻ tín dụng',
  `payload` tinyint(2) DEFAULT '0' COMMENT '0 - 1 lần, 1 - nhiều lần',
  `must_charge` int(11) DEFAULT NULL,
  `amount` int(11) DEFAULT NULL COMMENT 'số tiền thu',
  `total` int(11) DEFAULT NULL,
  `debt` int(11) DEFAULT NULL COMMENT 'công nợ',
  `charge_date` date DEFAULT NULL COMMENT 'ngày đóng phí',
  `created_at` datetime DEFAULT NULL,
  `creator_id` int(11) DEFAULT NULL,
  `hash_key` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `count` int(11) NOT NULL,
  `type` tinyint(1) DEFAULT NULL COMMENT '0 - bỏ cọc, 1 - lần đầu, 2 - tái phí',
  `student_id` int(11) DEFAULT NULL,
  `note` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `contract_id` (`contract_id`) USING BTREE,
  KEY `creator_id` (`creator_id`) USING BTREE,
  KEY `student_id` (`student_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=37092 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of payments
-- ----------------------------
BEGIN;
INSERT INTO `payments` VALUES (37086, 44068, NULL, NULL, 0, 12000000, 1, 1, 11999999, '2024-04-20', '2024-04-20 05:17:59', 1, NULL, 0, NULL, 101750, 'ghi chú');
INSERT INTO `payments` VALUES (37087, 44068, NULL, 1, 0, 12000000, 999, 1000, 11999000, '2024-04-20', '2024-04-20 05:21:17', 1, NULL, 0, NULL, 101750, 'ghi chú');
INSERT INTO `payments` VALUES (37088, 44068, NULL, 1, 0, 12000000, 2000000, 2001000, 9999000, '2024-04-20', '2024-04-20 05:21:55', 1, NULL, 0, NULL, 101750, NULL);
INSERT INTO `payments` VALUES (37089, 44068, NULL, 1, 0, 12000000, 2000000, 4001000, 7999000, '2024-04-20', '2024-04-20 09:02:48', 1, NULL, 0, NULL, 101750, 'ghi chú');
INSERT INTO `payments` VALUES (37090, 44068, NULL, 1, 0, 12000000, 7999000, 12000000, 0, '2024-04-20', '2024-04-20 09:04:45', 1, NULL, 0, NULL, 101750, 'ghi chú');
INSERT INTO `payments` VALUES (37091, 44066, NULL, 1, 0, 12000000, 1000000, 1000000, 11000000, '2024-04-17', '2024-04-20 09:09:15', 1, NULL, 0, NULL, 101750, NULL);
COMMIT;

-- ----------------------------
-- Table structure for permission_groups
-- ----------------------------
DROP TABLE IF EXISTS `permission_groups`;
CREATE TABLE `permission_groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` tinyint(2) DEFAULT NULL,
  `display_order` tinyint(2) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

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
  `role_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL,
  PRIMARY KEY (`role_id`,`permission_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

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
COMMIT;

-- ----------------------------
-- Table structure for permissions
-- ----------------------------
DROP TABLE IF EXISTS `permissions`;
CREATE TABLE `permissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `group_id` int(11) DEFAULT NULL,
  `display_order` tinyint(2) DEFAULT NULL,
  `status` tinyint(2) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

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
COMMIT;

-- ----------------------------
-- Table structure for products
-- ----------------------------
DROP TABLE IF EXISTS `products`;
CREATE TABLE `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `accounting_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'product id lsm',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `creator_id` int(11) DEFAULT NULL,
  `updator_id` int(11) DEFAULT NULL,
  `status` tinyint(2) DEFAULT '1',
  `max_student` int(11) DEFAULT NULL COMMENT 'số học sinh tối đa',
  `min_number_of_days_in_week` tinyint(2) DEFAULT NULL COMMENT 'Số ngày tối thiểu được học trong một tuần',
  `max_number_of_days_in_week` tinyint(2) DEFAULT NULL COMMENT 'Số ngày tối đa được học trong một tuần',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `proc_id` (`code`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of products
-- ----------------------------
BEGIN;
INSERT INTO `products` VALUES (1, NULL, 'KH01', 'FasTrack Tots', NULL, '2024-05-12 13:57:46', NULL, 1, NULL, 1, NULL, NULL, NULL);
INSERT INTO `products` VALUES (2, NULL, 'KH02', 'FasTrack-Fundamentals', NULL, '2024-05-12 13:58:20', NULL, 1, NULL, 1, NULL, NULL, NULL);
INSERT INTO `products` VALUES (3, NULL, 'KH03', 'FasTracKids', NULL, '2024-05-12 13:58:48', NULL, 1, NULL, 1, NULL, NULL, NULL);
INSERT INTO `products` VALUES (4, NULL, 'KH04', 'MathKids', NULL, '2024-05-12 13:59:10', NULL, 1, NULL, 1, NULL, NULL, NULL);
INSERT INTO `products` VALUES (5, NULL, 'KH05', 'FSC - Thông minh Tài chính', NULL, '2024-05-12 14:00:52', NULL, 1, NULL, 1, NULL, NULL, NULL);
COMMIT;

-- ----------------------------
-- Table structure for programs
-- ----------------------------
DROP TABLE IF EXISTS `programs`;
CREATE TABLE `programs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `accounting_id` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Mã gói học tương ứng bên phần mềm kế toán',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `code` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci COMMENT 'Ghi chú tương đương với trường tên gói học bên phần mềm kế toán',
  `created_at` datetime DEFAULT NULL,
  `creator_id` int(11) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updator_id` int(11) DEFAULT NULL,
  `status` tinyint(2) DEFAULT '1',
  `parent_id` int(11) DEFAULT '0',
  `product_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `product_id` (`product_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of programs
-- ----------------------------
BEGIN;
INSERT INTO `programs` VALUES (1, NULL, 'FasTrack Tots', 'CT01', NULL, '2024-05-12 14:02:13', 1, NULL, NULL, 1, 0, 1);
INSERT INTO `programs` VALUES (2, NULL, 'FasTrack-Fundamentals - Mẫu giáo', 'CT02', NULL, '2024-05-12 14:02:39', 1, NULL, NULL, 1, 0, 2);
INSERT INTO `programs` VALUES (3, NULL, 'FasTrack-Fundamentals - Tiểu học', 'CT03', NULL, '2024-05-12 14:03:11', 1, NULL, NULL, 1, 0, 2);
INSERT INTO `programs` VALUES (4, NULL, 'FasTracKids - Thủ lĩnh nhí', 'CT04', NULL, '2024-05-12 14:03:35', 1, NULL, NULL, 1, 0, 3);
INSERT INTO `programs` VALUES (5, NULL, 'MathKids', 'CT05', NULL, '2024-05-12 14:03:55', 1, NULL, NULL, 1, 0, 4);
INSERT INTO `programs` VALUES (6, NULL, 'FSC - Thông minh Tài chính', 'CT06', NULL, '2024-05-12 14:04:10', 1, NULL, NULL, 1, 0, 5);
COMMIT;

-- ----------------------------
-- Table structure for provinces
-- ----------------------------
DROP TABLE IF EXISTS `provinces`;
CREATE TABLE `provinces` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `accounting_id` varchar(2) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `caresoft_id` int(11) DEFAULT NULL,
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
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `zone_id` int(11) DEFAULT NULL,
  `products` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'ID của các products được áp dụng, vd: [1,2,3]',
  `branch_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `creator_id` int(11) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updator_id` int(11) DEFAULT NULL,
  `status` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=131 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of public_holiday
-- ----------------------------
BEGIN;
INSERT INTO `public_holiday` VALUES (1, 'CMS Holiday', '2018-09-01', '2018-09-01', 1, '1,2,3', NULL, NULL, NULL, NULL, NULL, 1);
INSERT INTO `public_holiday` VALUES (2, 'National day', '2018-09-02', '2018-09-02', 1, '1,2,3', NULL, NULL, NULL, NULL, NULL, 1);
INSERT INTO `public_holiday` VALUES (3, 'CMS Holiday', '2018-12-30', '2018-12-31', 1, '1,2,3', NULL, NULL, NULL, NULL, NULL, 1);
INSERT INTO `public_holiday` VALUES (4, 'Public holidays', '2019-01-01', '2019-01-01', 1, '1,2,3', NULL, NULL, NULL, NULL, NULL, 1);
INSERT INTO `public_holiday` VALUES (5, 'CMS Tet holidays', '2019-01-30', '2019-02-03', 1, '1,2,3', NULL, NULL, NULL, NULL, NULL, 1);
INSERT INTO `public_holiday` VALUES (6, 'Tet holidays', '2019-02-04', '2019-02-08', 1, '1,2,3', NULL, NULL, NULL, NULL, NULL, 1);
INSERT INTO `public_holiday` VALUES (7, 'CMS Tet holidays', '2019-02-09', '2019-02-11', 1, '1,2,3', NULL, NULL, NULL, NULL, NULL, 1);
INSERT INTO `public_holiday` VALUES (8, 'King Hung\'s day', '2019-04-14', '2019-04-14', 1, '1,2,3', NULL, NULL, NULL, NULL, NULL, 1);
INSERT INTO `public_holiday` VALUES (9, 'CMS  holiday', '2019-04-15', '2019-04-15', 1, '1,2,3', NULL, NULL, NULL, NULL, NULL, 1);
INSERT INTO `public_holiday` VALUES (10, 'Liberation day', '2019-04-30', '2019-04-30', 1, '1,2,3', NULL, NULL, NULL, NULL, NULL, 1);
INSERT INTO `public_holiday` VALUES (11, 'Labor day', '2019-05-01', '2019-05-04', 1, '1,2,3', NULL, NULL, NULL, NULL, NULL, 1);
INSERT INTO `public_holiday` VALUES (12, 'CMS holiday', '2019-09-01', '2019-09-01', 1, '1,2,3', NULL, NULL, NULL, NULL, NULL, 1);
INSERT INTO `public_holiday` VALUES (13, 'National day', '2019-09-02', '2019-09-07', 1, '1,2,3', NULL, NULL, NULL, NULL, NULL, 1);
INSERT INTO `public_holiday` VALUES (14, 'CMS holiday', '2019-12-31', '2019-12-31', 1, '1,2,3', NULL, NULL, NULL, NULL, NULL, 1);
INSERT INTO `public_holiday` VALUES (15, 'Public holidays', '2020-01-01', '2020-01-01', 1, '1,2,3', NULL, NULL, NULL, NULL, NULL, 1);
INSERT INTO `public_holiday` VALUES (16, 'Public holidays', '2020-01-24', '2020-01-29', 1, '1,2,3', NULL, NULL, NULL, '2019-01-28 12:10:09', NULL, 1);
INSERT INTO `public_holiday` VALUES (17, 'CMS holidays', '2020-01-30', '2020-01-31', 1, '1,2,3', NULL, NULL, NULL, '2019-01-28 12:10:41', NULL, 1);
INSERT INTO `public_holiday` VALUES (22, 'Public holidays', '2019-01-01', '2019-01-01', 2, '1,2,3', NULL, NULL, NULL, '2019-01-28 13:04:00', NULL, 1);
INSERT INTO `public_holiday` VALUES (23, 'CMS Tet holidays', '2019-01-30', '2019-02-03', 2, '1,2,3', NULL, NULL, NULL, NULL, NULL, 1);
INSERT INTO `public_holiday` VALUES (24, 'King Hung\'s day', '2019-04-14', '2019-04-14', 2, '1,2,3', NULL, NULL, NULL, '2019-01-28 12:15:30', NULL, 1);
INSERT INTO `public_holiday` VALUES (25, 'CMS holiday', '2019-04-15', '2019-04-15', 2, '1,2,3', NULL, NULL, NULL, NULL, NULL, 1);
INSERT INTO `public_holiday` VALUES (26, 'Liberation day', '2019-04-30', '2019-04-30', 2, '1,2,3', NULL, NULL, NULL, NULL, NULL, 1);
INSERT INTO `public_holiday` VALUES (27, 'Labor day', '2019-05-01', '2019-05-04', 2, '1,2,3', NULL, NULL, NULL, NULL, NULL, 1);
INSERT INTO `public_holiday` VALUES (28, 'CMS holiday', '2019-09-01', '2019-09-01', 2, '1,2,3', NULL, NULL, NULL, NULL, NULL, 1);
INSERT INTO `public_holiday` VALUES (29, 'National day', '2019-09-02', '2019-09-07', 2, '1,2,3', NULL, NULL, NULL, NULL, NULL, 1);
INSERT INTO `public_holiday` VALUES (30, 'CMS holiday', '2019-12-31', '2019-12-31', 2, '1,2,3', NULL, NULL, NULL, NULL, NULL, 1);
INSERT INTO `public_holiday` VALUES (31, 'Tet holidays', '2019-02-04', '2019-02-08', 2, '1,2,3', NULL, '2019-01-28 13:06:02', 1, NULL, NULL, 1);
INSERT INTO `public_holiday` VALUES (32, 'CMS Tet holidays', '2019-02-09', '2019-02-11', 2, '1,2,3', NULL, '2019-01-28 13:07:07', 1, NULL, NULL, 1);
INSERT INTO `public_holiday` VALUES (33, 'Public holidays', '2020-01-01', '2020-01-01', 2, '1,2,3', NULL, '2019-01-28 13:09:13', 1, NULL, NULL, 1);
INSERT INTO `public_holiday` VALUES (34, 'Public holidays', '2020-01-24', '2020-01-29', 2, '1,2,3', NULL, '2019-01-28 13:10:19', 1, NULL, NULL, 1);
INSERT INTO `public_holiday` VALUES (35, 'CMS holidays', '2020-01-30', '2020-01-31', 2, '1,2,3', NULL, '2019-01-28 13:11:07', 1, NULL, NULL, 1);
INSERT INTO `public_holiday` VALUES (40, 'National day', '2018-09-02', '2018-09-02', 2, '1,2,3', NULL, '2019-01-28 13:16:37', 1, NULL, NULL, 1);
INSERT INTO `public_holiday` VALUES (41, 'CMS holiday', '2018-12-30', '2018-12-31', 2, '1,2,3', NULL, '2019-01-28 13:17:26', 1, NULL, NULL, 1);
INSERT INTO `public_holiday` VALUES (42, 'CMS holiday', '2018-09-01', '2018-09-01', 2, '1,2,3', NULL, '2019-01-28 13:19:53', 1, NULL, NULL, 1);
INSERT INTO `public_holiday` VALUES (43, 'Nghỉ dịch covid 19', '2020-02-01', '2020-05-09', 1, '1,4,2,3', NULL, '2020-05-11 13:45:15', 1, '2020-05-11 16:42:13', NULL, 1);
INSERT INTO `public_holiday` VALUES (44, 'Nghỉ dịch covid 19', '2020-02-01', '2020-05-09', 2, '4,1,2,3', NULL, '2020-05-11 13:45:40', 1, '2020-05-11 16:20:56', NULL, 1);
INSERT INTO `public_holiday` VALUES (45, 'Quốc khánh', '2020-08-29', '2020-09-02', 1, '1,2,3', NULL, '2020-08-05 23:42:14', 1, NULL, NULL, 1);
INSERT INTO `public_holiday` VALUES (46, 'Quốc khánh', '2020-08-29', '2020-09-02', 2, '1,2,3', NULL, '2020-08-05 23:42:45', 1, NULL, NULL, 1);
INSERT INTO `public_holiday` VALUES (47, 'Nghỉ tết dương lịch', '2020-12-31', '2021-01-03', 1, '1,3,2', NULL, '2020-08-05 23:50:54', 1, NULL, NULL, 1);
INSERT INTO `public_holiday` VALUES (48, 'Nghỉ tết dương lịch', '2020-12-31', '2021-01-03', 2, '1,2,3', NULL, '2020-08-05 23:51:17', 1, NULL, NULL, 1);
INSERT INTO `public_holiday` VALUES (49, 'Nghỉ tết nguyên đán', '2021-02-01', '2021-02-28', 1, '2,1,3', NULL, '2020-08-05 23:52:07', 1, '2021-02-17 15:05:32', NULL, 1);
INSERT INTO `public_holiday` VALUES (50, 'Nghỉ tết nguyên đán', '2021-02-01', '2021-02-28', 2, '1,2,3', NULL, '2020-08-05 23:52:33', 1, '2021-02-17 15:05:10', NULL, 1);
INSERT INTO `public_holiday` VALUES (51, 'Lễ Giỗ Tổ Hùng Vương', '2021-04-21', '2021-04-21', 1, '1,3,2', NULL, '2020-12-28 11:19:55', 1, NULL, NULL, 1);
INSERT INTO `public_holiday` VALUES (52, 'Lễ Giỗ Tổ Hùng Vương', '2021-04-21', '2021-04-21', 2, '1,3,2', NULL, '2020-12-28 11:21:34', 1, NULL, NULL, 1);
INSERT INTO `public_holiday` VALUES (53, 'Nghỉ lễ 30/4 - 01/05', '2021-04-30', '2021-08-25', 1, '1,3,2', NULL, '2020-12-28 11:26:16', 1, NULL, NULL, 1);
INSERT INTO `public_holiday` VALUES (54, 'Nghỉ lễ 30/4 - 01/05', '2021-04-30', '2021-05-04', 2, '1,3,2', NULL, '2020-12-28 11:26:34', 1, NULL, NULL, 1);
INSERT INTO `public_holiday` VALUES (55, 'Nghỉ lễ Quốc Khánh 02/09', '2021-09-02', '2021-09-05', 1, '1,3,2', NULL, '2020-12-28 11:28:09', 1, '2021-10-13 21:35:01', NULL, 1);
INSERT INTO `public_holiday` VALUES (56, 'Nghỉ lễ Quốc Khánh 02/09', '2021-09-02', '2021-09-05', 2, '1,3,2', NULL, '2020-12-28 11:28:27', 1, NULL, NULL, 1);
INSERT INTO `public_holiday` VALUES (57, 'CMS Holiday', '2018-09-01', '2018-09-01', 3, '1,2,3', NULL, NULL, NULL, NULL, NULL, 1);
INSERT INTO `public_holiday` VALUES (58, 'National day', '2018-09-02', '2018-09-02', 3, '1,2,3', NULL, NULL, NULL, NULL, NULL, 1);
INSERT INTO `public_holiday` VALUES (59, 'CMS Holiday', '2018-12-30', '2018-12-31', 3, '1,2,3', NULL, NULL, NULL, NULL, NULL, 1);
INSERT INTO `public_holiday` VALUES (60, 'Public holidays', '2019-01-01', '2019-01-01', 3, '1,2,3', NULL, NULL, NULL, NULL, NULL, 1);
INSERT INTO `public_holiday` VALUES (61, 'CMS Tet holidays', '2019-01-30', '2019-02-03', 3, '1,2,3', NULL, NULL, NULL, NULL, NULL, 1);
INSERT INTO `public_holiday` VALUES (62, 'Tet holidays', '2019-02-04', '2019-02-08', 3, '1,2,3', NULL, NULL, NULL, NULL, NULL, 1);
INSERT INTO `public_holiday` VALUES (63, 'CMS Tet holidays', '2019-02-09', '2019-02-11', 3, '1,2,3', NULL, NULL, NULL, NULL, NULL, 1);
INSERT INTO `public_holiday` VALUES (64, 'King Hung\'s day', '2019-04-14', '2019-04-14', 3, '1,2,3', NULL, NULL, NULL, NULL, NULL, 1);
INSERT INTO `public_holiday` VALUES (65, 'CMS  holiday', '2019-04-15', '2019-04-15', 3, '1,2,3', NULL, NULL, NULL, NULL, NULL, 1);
INSERT INTO `public_holiday` VALUES (66, 'Liberation day', '2019-04-30', '2019-04-30', 3, '1,2,3', NULL, NULL, NULL, NULL, NULL, 1);
INSERT INTO `public_holiday` VALUES (67, 'Labor day', '2019-05-01', '2019-05-04', 3, '1,2,3', NULL, NULL, NULL, NULL, NULL, 1);
INSERT INTO `public_holiday` VALUES (68, 'CMS holiday', '2019-09-01', '2019-09-01', 3, '1,2,3', NULL, NULL, NULL, NULL, NULL, 1);
INSERT INTO `public_holiday` VALUES (69, 'National day', '2019-09-02', '2019-09-07', 3, '1,2,3', NULL, NULL, NULL, NULL, NULL, 1);
INSERT INTO `public_holiday` VALUES (70, 'CMS holiday', '2019-12-31', '2019-12-31', 3, '1,2,3', NULL, NULL, NULL, NULL, NULL, 1);
INSERT INTO `public_holiday` VALUES (71, 'Public holidays', '2020-01-01', '2020-01-01', 3, '1,2,3', NULL, NULL, NULL, NULL, NULL, 1);
INSERT INTO `public_holiday` VALUES (72, 'Public holidays', '2020-01-24', '2020-01-29', 3, '1,2,3', NULL, NULL, NULL, '2019-01-28 12:10:09', NULL, 1);
INSERT INTO `public_holiday` VALUES (73, 'CMS holidays', '2020-01-30', '2020-01-31', 3, '1,2,3', NULL, NULL, NULL, '2019-01-28 12:10:41', NULL, 1);
INSERT INTO `public_holiday` VALUES (74, 'Nghỉ dịch covid 19', '2020-02-01', '2020-05-09', 3, '1,4,2,3', NULL, '2020-05-11 13:45:15', 1, '2020-05-11 16:42:13', NULL, 1);
INSERT INTO `public_holiday` VALUES (75, 'Quốc khánh', '2020-08-29', '2020-09-02', 3, '1,2,3', NULL, '2020-08-05 23:42:14', 1, NULL, NULL, 1);
INSERT INTO `public_holiday` VALUES (76, 'Nghỉ tết dương lịch', '2020-12-31', '2021-01-03', 3, '1,3,2', NULL, '2020-08-05 23:50:54', 1, NULL, NULL, 1);
INSERT INTO `public_holiday` VALUES (77, 'Nghỉ tết nguyên đán', '2021-02-01', '2021-02-28', 3, '2,1,3', NULL, '2020-08-05 23:52:07', 1, '2021-02-17 15:05:32', NULL, 1);
INSERT INTO `public_holiday` VALUES (78, 'Lễ Giỗ Tổ Hùng Vương', '2021-04-21', '2021-04-21', 3, '1,3,2', NULL, '2020-12-28 11:19:55', 1, NULL, NULL, 1);
INSERT INTO `public_holiday` VALUES (79, 'Nghỉ lễ 30/4 - 01/05', '2021-04-30', '2021-05-04', 3, '1,3,2', NULL, '2020-12-28 11:26:16', 1, NULL, NULL, 1);
INSERT INTO `public_holiday` VALUES (80, 'Nghỉ lễ Quốc Khánh 02/09', '2021-09-02', '2021-09-05', 3, '1,3,2', NULL, '2020-12-28 11:28:09', 1, NULL, NULL, 1);
INSERT INTO `public_holiday` VALUES (81, 'Nghỉ dịch Hạ Long', '2021-01-28', '2021-01-31', 3, '1,3,2', NULL, '2021-05-06 18:10:54', 1, NULL, NULL, 1);
INSERT INTO `public_holiday` VALUES (82, 'Nghỉ dịch covid Hạ Long', '2021-05-07', '2021-06-15', 3, '1,3,2', NULL, '2021-05-07 16:07:14', 1, NULL, NULL, 1);
INSERT INTO `public_holiday` VALUES (83, 'Nghỉ dịch covid HCM 2021', '2021-05-10', '2021-08-25', 2, '1,3,2', NULL, '2021-05-07 16:07:46', 1, NULL, NULL, 1);
INSERT INTO `public_holiday` VALUES (84, 'Dịch', '2021-12-01', '2021-12-31', 1, '1,3,2', NULL, '2021-12-21 11:43:46', 1, NULL, NULL, 1);
INSERT INTO `public_holiday` VALUES (85, 'Dịch', '2021-11-01', '2021-12-31', 2, '1,3,2', NULL, '2021-12-21 12:04:37', 1, NULL, NULL, 1);
INSERT INTO `public_holiday` VALUES (86, 'Nghỉ tết dương 2022', '2022-01-01', '2022-03-16', 1, '1,3,2', NULL, '2021-12-31 18:17:25', 1, NULL, NULL, 1);
INSERT INTO `public_holiday` VALUES (87, 'Nghỉ tết dương 2022', '2022-01-01', '2022-02-28', 2, '1,2', NULL, '2021-12-31 18:17:58', 1, NULL, NULL, 1);
INSERT INTO `public_holiday` VALUES (88, 'Nghỉ tết dương 2022', '2022-01-01', '2022-01-03', 3, '1,3,2', NULL, '2021-12-31 18:18:19', 1, NULL, NULL, 1);
INSERT INTO `public_holiday` VALUES (89, 'Nghỉ tết nguyên đán', '2022-01-29', '2022-02-07', 3, '1,2,3', NULL, '2022-02-05 19:02:05', 1, NULL, NULL, 1);
INSERT INTO `public_holiday` VALUES (90, 'Nghỉ lễ 30-4 và 1-5', '2022-04-30', '2022-05-03', 1, '3,1,2', NULL, '2022-04-29 15:22:17', 1, NULL, NULL, 1);
INSERT INTO `public_holiday` VALUES (91, 'Nghỉ lễ 30-4 và 1-5', '2022-04-30', '2022-05-03', 2, '1,3,2', NULL, '2022-04-29 15:22:41', 1, NULL, NULL, 1);
INSERT INTO `public_holiday` VALUES (92, 'Nghỉ lễ 30-4 và 1-5', '2022-04-30', '2022-05-03', 3, '1,3,2', NULL, '2022-04-29 15:22:56', 1, NULL, NULL, 1);
INSERT INTO `public_holiday` VALUES (93, 'Nghỉ lễ Quốc Khánh 2/9/2022', '2022-09-02', '2022-09-08', 1, '3,1,2', NULL, '2022-11-17 14:16:32', 1, NULL, NULL, 1);
INSERT INTO `public_holiday` VALUES (94, 'Nghỉ Lễ Quốc Khánh 02/09/2022', '2022-09-02', '2022-09-08', 2, '1,3,2', NULL, '2022-11-17 14:17:15', 1, NULL, NULL, 1);
INSERT INTO `public_holiday` VALUES (95, 'Nghỉ Lễ Quốc Khánh 02/09/2022', '2022-09-02', '2022-09-08', 3, '1,3,2', NULL, '2022-11-17 14:17:36', 1, NULL, NULL, 1);
INSERT INTO `public_holiday` VALUES (96, 'Nghỉ tết dương 2023', '2022-12-31', '2023-01-02', 1, '1,3,2', NULL, '2023-01-03 11:21:11', 1, NULL, NULL, 1);
INSERT INTO `public_holiday` VALUES (97, 'Nghỉ tết Nguyên Đán 2023', '2023-01-20', '2023-01-29', 1, '1,3,2', NULL, '2023-01-03 11:21:52', 1, NULL, NULL, 1);
INSERT INTO `public_holiday` VALUES (98, 'Nghỉ giỗ Tổ 2023', '2023-04-29', '2023-05-03', 1, '1,3,2', NULL, '2023-01-03 11:22:24', 1, NULL, NULL, 1);
INSERT INTO `public_holiday` VALUES (99, 'Nghỉ Quốc Khánh 2023', '2023-09-01', '2023-09-04', 1, '1,3,2', NULL, '2023-01-03 11:23:09', 1, NULL, NULL, 1);
INSERT INTO `public_holiday` VALUES (100, 'Nghỉ tết dương 2023', '2022-12-31', '2023-01-02', 2, '1,3,2', NULL, '2023-01-03 11:21:11', 1, NULL, NULL, 1);
INSERT INTO `public_holiday` VALUES (101, 'Nghỉ tết dương 2023', '2022-12-31', '2023-01-02', 3, '1,3,2', NULL, '2023-01-03 11:21:11', 1, NULL, NULL, 1);
INSERT INTO `public_holiday` VALUES (102, 'Nghỉ tết Nguyên Đán 2023', '2023-01-20', '2023-01-29', 2, '1,3,2', NULL, '2023-01-03 11:21:52', 1, NULL, NULL, 1);
INSERT INTO `public_holiday` VALUES (103, 'Nghỉ tết Nguyên Đán 2023', '2023-01-20', '2023-01-29', 3, '1,3,2', NULL, '2023-01-03 11:21:52', 1, NULL, NULL, 1);
INSERT INTO `public_holiday` VALUES (104, 'Nghỉ giỗ Tổ 2023', '2023-04-29', '2023-05-03', 2, '1,3,2', NULL, '2023-01-03 11:22:24', 1, NULL, NULL, 1);
INSERT INTO `public_holiday` VALUES (105, 'Nghỉ giỗ Tổ 2023', '2023-04-29', '2023-05-03', 3, '1,3,2', NULL, '2023-01-03 11:22:24', 1, NULL, NULL, 1);
INSERT INTO `public_holiday` VALUES (106, 'Nghỉ Quốc Khánh 2023', '2023-09-01', '2023-09-04', 2, '1,3,2', NULL, '2023-01-03 11:23:09', 1, NULL, NULL, 1);
INSERT INTO `public_holiday` VALUES (107, 'Nghỉ Quốc Khánh 2023', '2023-09-01', '2023-09-04', 3, '1,3,2', NULL, '2023-01-03 11:23:09', 1, NULL, NULL, 1);
INSERT INTO `public_holiday` VALUES (108, 'Nghỉ hè 2023', '2023-05-31', '2023-06-30', 1, '1,3,5,4,2', NULL, '2023-06-29 14:42:07', 1, NULL, NULL, 1);
INSERT INTO `public_holiday` VALUES (109, 'Nghỉ hè 2023', '2023-05-31', '2023-06-30', 2, '1,3,5,2,4', NULL, '2023-06-29 14:42:29', 1, NULL, NULL, 1);
INSERT INTO `public_holiday` VALUES (110, 'Nghỉ hè 2023', '2023-05-31', '2023-06-30', 3, '1,3,5,2,4', NULL, '2023-06-29 14:43:00', 1, NULL, NULL, 1);
INSERT INTO `public_holiday` VALUES (111, 'Nghỉ hè 2023', '2023-07-01', '2023-07-23', 2, '1,3,2', NULL, '2023-07-12 16:27:24', 1, NULL, NULL, 1);
INSERT INTO `public_holiday` VALUES (112, 'Nghỉ Quốc Khánh 2023', '2023-09-01', '2023-09-04', 1, '1,2,3', NULL, '2023-10-09 22:43:40', 1, NULL, NULL, 1);
INSERT INTO `public_holiday` VALUES (113, 'Nghỉ Tết dương lịch 2024', '2023-12-31', '2024-01-04', 1, '1,4,5,2,3', NULL, '2023-12-29 14:57:05', 1, NULL, NULL, 1);
INSERT INTO `public_holiday` VALUES (114, 'Nghỉ Tết Dương Lịch 2024', '2023-12-31', '2024-01-04', 2, '1,4,2,5,3', NULL, '2023-12-29 14:58:56', 1, NULL, NULL, 1);
INSERT INTO `public_holiday` VALUES (115, 'Nghỉ Tết Dương Lịch 2024', '2023-12-31', '2024-01-04', 3, '1,4,2,5,3', NULL, '2023-12-29 14:59:19', 1, NULL, NULL, 1);
INSERT INTO `public_holiday` VALUES (116, 'Tết nguyên đán 2024', '2024-02-03', '2024-02-16', 1, '1,4,2,5,3', NULL, '2023-12-29 15:00:16', 1, NULL, NULL, 1);
INSERT INTO `public_holiday` VALUES (117, 'Tết nguyên đán 2024', '2024-02-03', '2024-02-16', 2, '1,4,2,5,3', NULL, '2023-12-29 15:00:46', 1, NULL, NULL, 1);
INSERT INTO `public_holiday` VALUES (118, 'Tết nguyên đán 2024', '2024-02-03', '2024-02-16', 3, '1,4,5,2,3', NULL, '2023-12-29 15:01:10', 1, NULL, NULL, 1);
INSERT INTO `public_holiday` VALUES (119, 'Giổ Tổ Hùng Vương 2024', '2024-04-18', '2024-04-18', 1, '1,4,2,5,3', NULL, '2023-12-29 15:01:56', 1, NULL, NULL, 1);
INSERT INTO `public_holiday` VALUES (120, 'Giổ Tổ Hùng Vương 2024', '2024-04-18', '2024-04-18', 2, '1,4,2,5,3', NULL, '2023-12-29 15:02:14', 1, NULL, NULL, 1);
INSERT INTO `public_holiday` VALUES (121, 'Giổ Tổ Hùng Vương 2024', '2024-04-18', '2024-04-18', 3, '1,4,2,5,3', NULL, '2023-12-29 15:02:34', 1, NULL, NULL, 1);
INSERT INTO `public_holiday` VALUES (122, 'Nghỉ lễ 30/4/2024', '2024-04-29', '2024-05-01', 1, '1,4,2,5,3', NULL, '2023-12-29 15:03:42', 1, NULL, NULL, 1);
INSERT INTO `public_holiday` VALUES (123, 'Nghỉ lễ 30/4/2024', '2024-04-29', '2024-05-01', 2, '1,4,2,5,3', NULL, '2023-12-29 15:04:04', 1, NULL, NULL, 1);
INSERT INTO `public_holiday` VALUES (124, 'Nghỉ lễ 30/4/2024', '2024-04-29', '2024-05-01', 3, '1,4,5,2,3', NULL, '2023-12-29 15:04:22', 1, NULL, NULL, 1);
INSERT INTO `public_holiday` VALUES (125, 'Nghỉ lễ 30/4/2024', '2024-05-03', '2024-05-05', 1, '1,4,2,5,3', NULL, '2023-12-29 15:04:45', 1, NULL, NULL, 1);
INSERT INTO `public_holiday` VALUES (126, 'Nghỉ lễ 30/4/2024', '2024-05-03', '2024-05-05', 2, '1,4,2,5,3', NULL, '2023-12-29 15:05:02', 1, NULL, NULL, 1);
INSERT INTO `public_holiday` VALUES (127, 'Nghỉ lễ 30/4/2024', '2024-05-03', '2024-05-05', 3, '1,4,2,5,3', NULL, '2023-12-29 15:05:26', 1, NULL, NULL, 1);
INSERT INTO `public_holiday` VALUES (128, 'Quốc khánh 2024', '2024-08-31', '2024-09-06', 1, '1,4,5,2,3', '1,3,4,5', '2023-12-29 15:05:55', 1, NULL, NULL, 1);
INSERT INTO `public_holiday` VALUES (129, 'Quốc khánh 2024', '2024-08-31', '2024-09-06', 2, '1,4,2,5,3', NULL, '2023-12-29 15:06:26', 1, NULL, NULL, 1);
INSERT INTO `public_holiday` VALUES (130, 'Quốc khánh 2024', '2024-08-31', '2024-09-06', 3, '1,4,2,5,3', '1,2', '2023-12-29 15:06:44', 1, NULL, NULL, 1);
COMMIT;

-- ----------------------------
-- Table structure for reserves
-- ----------------------------
DROP TABLE IF EXISTS `reserves`;
CREATE TABLE `reserves` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) DEFAULT NULL,
  `type` tinyint(2) DEFAULT '0' COMMENT '0 - bình thường, 1 - đặc biệt',
  `start_date` date DEFAULT NULL,
  `session` int(11) DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `status` tinyint(4) DEFAULT '0' COMMENT '0 - đã xóa, 1 - chờ duyệt, 2 - đã duyệt, 3 - từ chối',
  `creator_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `approver_id` int(11) DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `comment` text COLLATE utf8mb4_unicode_ci,
  `meta_data` text COLLATE utf8mb4_unicode_ci,
  `is_reserved` tinyint(2) DEFAULT '0' COMMENT '0 - không giữ chỗ, 1 - giữ chỗ',
  `attached_file` varchar(2550) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contract_id` int(11) DEFAULT NULL,
  `branch_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `program_id` int(11) DEFAULT NULL,
  `class_id` int(11) DEFAULT NULL,
  `note` text COLLATE utf8mb4_unicode_ci,
  `updated_at` datetime DEFAULT NULL,
  `updator_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `student_id` (`student_id`) USING BTREE,
  KEY `creator_id` (`creator_id`) USING BTREE,
  KEY `approver_id` (`approver_id`) USING BTREE,
  KEY `contract_id` (`contract_id`) USING BTREE,
  KEY `branch_id` (`branch_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=57559 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of reserves
-- ----------------------------
BEGIN;
INSERT INTO `reserves` VALUES (57556, 101750, 2, '2024-04-26', 1, '2024-04-27', 2, 1, '2024-04-24 04:24:35', NULL, NULL, NULL, '{\"reserve\":{\"branch_id\":1,\"student_id\":101750,\"contract_id\":44068,\"note\":\"ghi ch\\u00fa\",\"start_date\":\"2024-05-16\",\"end_date\":\"2024-05-18\",\"session\":\"2\",\"is_reserved\":1},\"student_info\":{\"name\":\"h\\u1ecdc sinh\",\"lms_code\":\"LMS101750\",\"gud_name1\":\"demo23\",\"gud_mobile1\":\"0389946902\",\"gud_email1\":null,\"address\":null,\"student_id\":101750,\"label\":\"h\\u1ecdc sinh - LMS101750\",\"product_name\":\"UCREA\",\"program_name\":\"U2019\",\"class_name\":\"demo 1\",\"tuition_fee_name\":\"UCREA - 6 th\\u00e1ng\",\"total_charged\":12000000,\"summary_sessions\":28,\"bonus_sessions\":4,\"real_sessions\":24,\"reservable_sessions\":1,\"reserved_sessions\":0,\"product_id\":1,\"class_id\":3,\"contract_id\":44068,\"enrolment_start_date\":\"2024-04-25\",\"enrolment_last_date\":\"2024-10-17\"}}', 1, NULL, 44068, 1, 1, 434, 3, 'ghi chú bảo lưu', NULL, NULL);
INSERT INTO `reserves` VALUES (57557, 101751, 1, '2024-04-30', 2, '2024-05-07', 2, 1, '2024-04-24 04:30:22', NULL, NULL, NULL, '{\"reserve\":{\"branch_id\":1,\"student_id\":101750,\"contract_id\":44068,\"note\":\"ghi ch\\u00fa\",\"start_date\":\"2024-05-16\",\"end_date\":\"2024-05-18\",\"session\":\"2\",\"is_reserved\":1},\"student_info\":{\"name\":\"h\\u1ecdc sinh\",\"lms_code\":\"LMS101750\",\"gud_name1\":\"demo23\",\"gud_mobile1\":\"0389946902\",\"gud_email1\":null,\"address\":null,\"student_id\":101750,\"label\":\"h\\u1ecdc sinh - LMS101750\",\"product_name\":\"UCREA\",\"program_name\":\"U2019\",\"class_name\":\"demo 1\",\"tuition_fee_name\":\"UCREA - 6 th\\u00e1ng\",\"total_charged\":12000000,\"summary_sessions\":28,\"bonus_sessions\":4,\"real_sessions\":24,\"reservable_sessions\":1,\"reserved_sessions\":0,\"product_id\":1,\"class_id\":3,\"contract_id\":44068,\"enrolment_start_date\":\"2024-04-25\",\"enrolment_last_date\":\"2024-10-17\"}}', 1, NULL, 44068, 1, 1, 434, 3, 'ghi chú bảo lưu', NULL, NULL);
INSERT INTO `reserves` VALUES (57558, 101750, 1, '2024-05-16', 2, '2024-05-18', 2, 1, '2024-04-24 05:11:58', 1, '2024-04-24 07:34:14', 'rgdfgfdg', '{\"reserve\":{\"branch_id\":1,\"student_id\":101750,\"contract_id\":44068,\"note\":\"ghi ch\\u00fa\",\"start_date\":\"2024-05-16\",\"end_date\":\"2024-05-18\",\"session\":\"2\",\"is_reserved\":1},\"student_info\":{\"name\":\"h\\u1ecdc sinh\",\"lms_code\":\"LMS101750\",\"gud_name1\":\"demo23\",\"gud_mobile1\":\"0389946902\",\"gud_email1\":null,\"address\":null,\"student_id\":101750,\"label\":\"h\\u1ecdc sinh - LMS101750\",\"product_name\":\"UCREA\",\"program_name\":\"U2019\",\"class_name\":\"demo 1\",\"tuition_fee_name\":\"UCREA - 6 th\\u00e1ng\",\"total_charged\":12000000,\"summary_sessions\":28,\"bonus_sessions\":4,\"real_sessions\":24,\"reservable_sessions\":1,\"reserved_sessions\":0,\"product_id\":1,\"class_id\":3,\"contract_id\":44068,\"enrolment_start_date\":\"2024-04-25\",\"enrolment_last_date\":\"2024-10-17\"}}', 1, NULL, 44068, 1, 1, 434, 3, 'ghi chú', '2024-04-24 07:34:14', 1);
COMMIT;

-- ----------------------------
-- Table structure for role_has_user
-- ----------------------------
DROP TABLE IF EXISTS `role_has_user`;
CREATE TABLE `role_has_user` (
  `user_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  PRIMARY KEY (`user_id`,`role_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of role_has_user
-- ----------------------------
BEGIN;
INSERT INTO `role_has_user` VALUES (1, 999999);
INSERT INTO `role_has_user` VALUES (2, 686868);
INSERT INTO `role_has_user` VALUES (3, 36);
INSERT INTO `role_has_user` VALUES (4, 55);
INSERT INTO `role_has_user` VALUES (5, 68);
INSERT INTO `role_has_user` VALUES (6, 36);
INSERT INTO `role_has_user` VALUES (7, 55);
INSERT INTO `role_has_user` VALUES (8, 68);
COMMIT;

-- ----------------------------
-- Table structure for roles
-- ----------------------------
DROP TABLE IF EXISTS `roles`;
CREATE TABLE `roles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `creator_id` int(11) DEFAULT NULL,
  `updator_id` int(11) DEFAULT NULL,
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
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'room id lms',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'room name lms',
  `branch_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `creator_id` int(11) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updator_id` int(11) DEFAULT NULL,
  `status` tinyint(2) DEFAULT '0' COMMENT '0 - inactive, 1 - active',
  `type` tinyint(2) DEFAULT NULL COMMENT '1 - manualy, 0 - auto',
  `product_id` int(11) DEFAULT NULL COMMENT 'Theo hiệu suất chương trình',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `branch_id` (`branch_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of rooms
-- ----------------------------
BEGIN;
INSERT INTO `rooms` VALUES (1, 'PH01', 'Phòng học 1', 1, '2024-05-12 14:07:13', 1, NULL, NULL, 1, NULL, NULL);
INSERT INTO `rooms` VALUES (2, 'PH02', 'Phòng học 2', 1, '2024-05-12 14:07:29', 1, NULL, NULL, 1, NULL, NULL);
INSERT INTO `rooms` VALUES (3, 'PH03', 'Phòng học 3', 1, '2024-05-12 14:07:51', 1, NULL, NULL, 1, NULL, NULL);
INSERT INTO `rooms` VALUES (4, 'PH11', 'Phòng học 1 - MĐ', 2, '2024-05-12 14:08:19', 1, NULL, NULL, 1, NULL, NULL);
COMMIT;

-- ----------------------------
-- Table structure for schedule_has_student
-- ----------------------------
DROP TABLE IF EXISTS `schedule_has_student`;
CREATE TABLE `schedule_has_student` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) DEFAULT NULL,
  `branch_id` int(11) DEFAULT NULL,
  `class_id` int(11) DEFAULT NULL,
  `contract_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `program_id` int(11) DEFAULT NULL,
  `subject_id` int(11) DEFAULT NULL,
  `subject_stt` tinyint(11) DEFAULT NULL,
  `class_date` date DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updator_id` int(11) DEFAULT NULL,
  `note` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` tinyint(2) DEFAULT '0' COMMENT ' 1 - tính phí, 0 ko tính phí',
  `attendance_status` tinyint(2) DEFAULT '0' COMMENT '0 chưa điểm danh, 1 đi học , 2 đăng ký học bù, 3  đã học bù',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `student_id` (`student_id`) USING BTREE,
  KEY `branch_id` (`branch_id`) USING BTREE,
  KEY `class_id` (`class_id`) USING BTREE,
  KEY `contract_id` (`contract_id`) USING BTREE,
  KEY `class_date` (`class_date`) USING BTREE,
  KEY `subject_id` (`subject_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of schedule_has_student
-- ----------------------------
BEGIN;
INSERT INTO `schedule_has_student` VALUES (5, 101751, 1, 1, 44067, 1, 434, 1, 2, '2024-05-07', '2024-05-01 19:43:14', '2024-05-10 22:28:32', 1, NULL, 1, 3);
INSERT INTO `schedule_has_student` VALUES (6, 1, 1, 1, 2, 3, 4, NULL, NULL, '2024-05-12', '2024-05-12 22:26:11', NULL, NULL, NULL, 1, 0);
COMMIT;

-- ----------------------------
-- Table structure for schedules
-- ----------------------------
DROP TABLE IF EXISTS `schedules`;
CREATE TABLE `schedules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `class_date` date NOT NULL,
  `status` tinyint(2) DEFAULT '0' COMMENT '0 - inactive, 1 - active',
  `class_id` int(11) DEFAULT NULL COMMENT 'class id crm',
  `subject_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `class_stt` int(11) DEFAULT NULL,
  `subject_stt` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `class_id` (`class_id`) USING BTREE,
  KEY `class_date` (`class_date`) USING BTREE,
  KEY `subject_id` (`subject_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=907 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of schedules
-- ----------------------------
BEGIN;
INSERT INTO `schedules` VALUES (715, '2024-05-01', 1, 1, 1, '2024-05-12 22:28:42', NULL, 1, 1);
INSERT INTO `schedules` VALUES (716, '2024-05-02', 1, 1, 1, '2024-05-12 22:28:42', NULL, 2, 2);
INSERT INTO `schedules` VALUES (717, '2024-05-03', 1, 1, 1, '2024-05-12 22:28:42', NULL, 3, 3);
INSERT INTO `schedules` VALUES (718, '2024-05-04', 1, 1, 1, '2024-05-12 22:28:42', NULL, 4, 4);
INSERT INTO `schedules` VALUES (719, '2024-05-05', 1, 1, 2, '2024-05-12 22:28:42', NULL, 5, 1);
INSERT INTO `schedules` VALUES (720, '2024-05-06', 1, 1, 2, '2024-05-12 22:28:42', NULL, 6, 2);
INSERT INTO `schedules` VALUES (721, '2024-05-07', 1, 1, 2, '2024-05-12 22:28:42', NULL, 7, 3);
INSERT INTO `schedules` VALUES (722, '2024-05-08', 1, 1, 2, '2024-05-12 22:28:42', NULL, 8, 4);
INSERT INTO `schedules` VALUES (723, '2024-05-09', 1, 1, 3, '2024-05-12 22:28:42', NULL, 9, 1);
INSERT INTO `schedules` VALUES (724, '2024-05-10', 1, 1, 3, '2024-05-12 22:28:42', NULL, 10, 2);
INSERT INTO `schedules` VALUES (725, '2024-05-11', 1, 1, 3, '2024-05-12 22:28:42', NULL, 11, 3);
INSERT INTO `schedules` VALUES (726, '2024-05-12', 1, 1, 3, '2024-05-12 22:28:42', NULL, 12, 4);
INSERT INTO `schedules` VALUES (727, '2024-05-13', 1, 1, 4, '2024-05-12 22:28:42', NULL, 13, 1);
INSERT INTO `schedules` VALUES (728, '2024-05-14', 1, 1, 4, '2024-05-12 22:28:42', NULL, 14, 2);
INSERT INTO `schedules` VALUES (729, '2024-05-15', 1, 1, 4, '2024-05-12 22:28:42', NULL, 15, 3);
INSERT INTO `schedules` VALUES (730, '2024-05-16', 1, 1, 4, '2024-05-12 22:28:42', NULL, 16, 4);
INSERT INTO `schedules` VALUES (731, '2024-05-17', 1, 1, 5, '2024-05-12 22:28:42', NULL, 17, 1);
INSERT INTO `schedules` VALUES (732, '2024-05-18', 1, 1, 5, '2024-05-12 22:28:42', NULL, 18, 2);
INSERT INTO `schedules` VALUES (733, '2024-05-19', 1, 1, 5, '2024-05-12 22:28:42', NULL, 19, 3);
INSERT INTO `schedules` VALUES (734, '2024-05-20', 1, 1, 5, '2024-05-12 22:28:42', NULL, 20, 4);
INSERT INTO `schedules` VALUES (735, '2024-05-21', 1, 1, 6, '2024-05-12 22:28:42', NULL, 21, 1);
INSERT INTO `schedules` VALUES (736, '2024-05-22', 1, 1, 6, '2024-05-12 22:28:42', NULL, 22, 2);
INSERT INTO `schedules` VALUES (737, '2024-05-23', 1, 1, 6, '2024-05-12 22:28:42', NULL, 23, 3);
INSERT INTO `schedules` VALUES (738, '2024-05-24', 1, 1, 6, '2024-05-12 22:28:42', NULL, 24, 4);
INSERT INTO `schedules` VALUES (739, '2024-05-25', 1, 1, 7, '2024-05-12 22:28:42', NULL, 25, 1);
INSERT INTO `schedules` VALUES (740, '2024-05-26', 1, 1, 7, '2024-05-12 22:28:42', NULL, 26, 2);
INSERT INTO `schedules` VALUES (741, '2024-05-27', 1, 1, 7, '2024-05-12 22:28:42', NULL, 27, 3);
INSERT INTO `schedules` VALUES (742, '2024-05-28', 1, 1, 7, '2024-05-12 22:28:42', NULL, 28, 4);
INSERT INTO `schedules` VALUES (743, '2024-05-29', 1, 1, 8, '2024-05-12 22:28:42', NULL, 29, 1);
INSERT INTO `schedules` VALUES (744, '2024-05-30', 1, 1, 8, '2024-05-12 22:28:42', NULL, 30, 2);
INSERT INTO `schedules` VALUES (745, '2024-05-31', 1, 1, 8, '2024-05-12 22:28:42', NULL, 31, 3);
INSERT INTO `schedules` VALUES (746, '2024-06-01', 1, 1, 8, '2024-05-12 22:28:42', NULL, 32, 4);
INSERT INTO `schedules` VALUES (747, '2024-06-02', 1, 1, 9, '2024-05-12 22:28:42', NULL, 33, 1);
INSERT INTO `schedules` VALUES (748, '2024-06-03', 1, 1, 9, '2024-05-12 22:28:42', NULL, 34, 2);
INSERT INTO `schedules` VALUES (749, '2024-06-04', 1, 1, 9, '2024-05-12 22:28:42', NULL, 35, 3);
INSERT INTO `schedules` VALUES (750, '2024-06-05', 1, 1, 9, '2024-05-12 22:28:42', NULL, 36, 4);
INSERT INTO `schedules` VALUES (751, '2024-06-06', 1, 1, 10, '2024-05-12 22:28:42', NULL, 37, 1);
INSERT INTO `schedules` VALUES (752, '2024-06-07', 1, 1, 10, '2024-05-12 22:28:42', NULL, 38, 2);
INSERT INTO `schedules` VALUES (753, '2024-06-08', 1, 1, 10, '2024-05-12 22:28:42', NULL, 39, 3);
INSERT INTO `schedules` VALUES (754, '2024-06-09', 1, 1, 10, '2024-05-12 22:28:42', NULL, 40, 4);
INSERT INTO `schedules` VALUES (755, '2024-06-10', 1, 1, 11, '2024-05-12 22:28:42', NULL, 41, 1);
INSERT INTO `schedules` VALUES (756, '2024-06-11', 1, 1, 11, '2024-05-12 22:28:42', NULL, 42, 2);
INSERT INTO `schedules` VALUES (757, '2024-06-12', 1, 1, 11, '2024-05-12 22:28:42', NULL, 43, 3);
INSERT INTO `schedules` VALUES (758, '2024-06-13', 1, 1, 11, '2024-05-12 22:28:42', NULL, 44, 4);
INSERT INTO `schedules` VALUES (759, '2024-06-14', 1, 1, 12, '2024-05-12 22:28:42', NULL, 45, 1);
INSERT INTO `schedules` VALUES (760, '2024-06-15', 1, 1, 12, '2024-05-12 22:28:42', NULL, 46, 2);
INSERT INTO `schedules` VALUES (761, '2024-06-16', 1, 1, 12, '2024-05-12 22:28:42', NULL, 47, 3);
INSERT INTO `schedules` VALUES (762, '2024-06-17', 1, 1, 12, '2024-05-12 22:28:42', NULL, 48, 4);
INSERT INTO `schedules` VALUES (763, '2024-05-05', 1, 4, 1, '2024-05-12 22:31:46', NULL, 1, 1);
INSERT INTO `schedules` VALUES (764, '2024-05-07', 1, 4, 1, '2024-05-12 22:31:46', NULL, 2, 2);
INSERT INTO `schedules` VALUES (765, '2024-05-12', 1, 4, 1, '2024-05-12 22:31:46', NULL, 3, 3);
INSERT INTO `schedules` VALUES (766, '2024-05-14', 1, 4, 1, '2024-05-12 22:31:46', NULL, 4, 4);
INSERT INTO `schedules` VALUES (767, '2024-05-19', 1, 4, 1, '2024-05-12 22:31:46', NULL, 5, 5);
INSERT INTO `schedules` VALUES (768, '2024-05-21', 1, 4, 1, '2024-05-12 22:31:46', NULL, 6, 6);
INSERT INTO `schedules` VALUES (769, '2024-05-26', 1, 4, 1, '2024-05-12 22:31:46', NULL, 7, 7);
INSERT INTO `schedules` VALUES (770, '2024-05-28', 1, 4, 1, '2024-05-12 22:31:46', NULL, 8, 8);
INSERT INTO `schedules` VALUES (771, '2024-06-02', 1, 4, 2, '2024-05-12 22:31:46', NULL, 9, 1);
INSERT INTO `schedules` VALUES (772, '2024-06-04', 1, 4, 2, '2024-05-12 22:31:46', NULL, 10, 2);
INSERT INTO `schedules` VALUES (773, '2024-06-09', 1, 4, 2, '2024-05-12 22:31:46', NULL, 11, 3);
INSERT INTO `schedules` VALUES (774, '2024-06-11', 1, 4, 2, '2024-05-12 22:31:46', NULL, 12, 4);
INSERT INTO `schedules` VALUES (775, '2024-06-16', 1, 4, 3, '2024-05-12 22:31:46', NULL, 13, 1);
INSERT INTO `schedules` VALUES (776, '2024-06-18', 1, 4, 3, '2024-05-12 22:31:46', NULL, 14, 2);
INSERT INTO `schedules` VALUES (777, '2024-06-23', 1, 4, 3, '2024-05-12 22:31:46', NULL, 15, 3);
INSERT INTO `schedules` VALUES (778, '2024-06-25', 1, 4, 3, '2024-05-12 22:31:46', NULL, 16, 4);
INSERT INTO `schedules` VALUES (779, '2024-06-30', 1, 4, 4, '2024-05-12 22:31:46', NULL, 17, 1);
INSERT INTO `schedules` VALUES (780, '2024-07-02', 1, 4, 4, '2024-05-12 22:31:46', NULL, 18, 2);
INSERT INTO `schedules` VALUES (781, '2024-07-07', 1, 4, 4, '2024-05-12 22:31:46', NULL, 19, 3);
INSERT INTO `schedules` VALUES (782, '2024-07-09', 1, 4, 4, '2024-05-12 22:31:46', NULL, 20, 4);
INSERT INTO `schedules` VALUES (783, '2024-07-14', 1, 4, 5, '2024-05-12 22:31:46', NULL, 21, 1);
INSERT INTO `schedules` VALUES (784, '2024-07-16', 1, 4, 5, '2024-05-12 22:31:46', NULL, 22, 2);
INSERT INTO `schedules` VALUES (785, '2024-07-21', 1, 4, 5, '2024-05-12 22:31:46', NULL, 23, 3);
INSERT INTO `schedules` VALUES (786, '2024-07-23', 1, 4, 5, '2024-05-12 22:31:46', NULL, 24, 4);
INSERT INTO `schedules` VALUES (787, '2024-07-28', 1, 4, 6, '2024-05-12 22:31:46', NULL, 25, 1);
INSERT INTO `schedules` VALUES (788, '2024-07-30', 1, 4, 6, '2024-05-12 22:31:46', NULL, 26, 2);
INSERT INTO `schedules` VALUES (789, '2024-08-04', 1, 4, 6, '2024-05-12 22:31:46', NULL, 27, 3);
INSERT INTO `schedules` VALUES (790, '2024-08-06', 1, 4, 6, '2024-05-12 22:31:46', NULL, 28, 4);
INSERT INTO `schedules` VALUES (791, '2024-08-11', 1, 4, 7, '2024-05-12 22:31:46', NULL, 29, 1);
INSERT INTO `schedules` VALUES (792, '2024-08-13', 1, 4, 7, '2024-05-12 22:31:46', NULL, 30, 2);
INSERT INTO `schedules` VALUES (793, '2024-08-18', 1, 4, 7, '2024-05-12 22:31:46', NULL, 31, 3);
INSERT INTO `schedules` VALUES (794, '2024-08-20', 1, 4, 7, '2024-05-12 22:31:46', NULL, 32, 4);
INSERT INTO `schedules` VALUES (795, '2024-08-25', 1, 4, 8, '2024-05-12 22:31:46', NULL, 33, 1);
INSERT INTO `schedules` VALUES (796, '2024-08-27', 1, 4, 8, '2024-05-12 22:31:46', NULL, 34, 2);
INSERT INTO `schedules` VALUES (797, '2024-09-08', 1, 4, 8, '2024-05-12 22:31:46', NULL, 35, 3);
INSERT INTO `schedules` VALUES (798, '2024-09-10', 1, 4, 8, '2024-05-12 22:31:46', NULL, 36, 4);
INSERT INTO `schedules` VALUES (799, '2024-09-15', 1, 4, 9, '2024-05-12 22:31:46', NULL, 37, 1);
INSERT INTO `schedules` VALUES (800, '2024-09-17', 1, 4, 9, '2024-05-12 22:31:46', NULL, 38, 2);
INSERT INTO `schedules` VALUES (801, '2024-09-22', 1, 4, 9, '2024-05-12 22:31:46', NULL, 39, 3);
INSERT INTO `schedules` VALUES (802, '2024-09-24', 1, 4, 9, '2024-05-12 22:31:46', NULL, 40, 4);
INSERT INTO `schedules` VALUES (803, '2024-09-29', 1, 4, 10, '2024-05-12 22:31:46', NULL, 41, 1);
INSERT INTO `schedules` VALUES (804, '2024-10-01', 1, 4, 10, '2024-05-12 22:31:46', NULL, 42, 2);
INSERT INTO `schedules` VALUES (805, '2024-10-06', 1, 4, 10, '2024-05-12 22:31:46', NULL, 43, 3);
INSERT INTO `schedules` VALUES (806, '2024-10-08', 1, 4, 10, '2024-05-12 22:31:46', NULL, 44, 4);
INSERT INTO `schedules` VALUES (807, '2024-10-13', 1, 4, 11, '2024-05-12 22:31:46', NULL, 45, 1);
INSERT INTO `schedules` VALUES (808, '2024-10-15', 1, 4, 11, '2024-05-12 22:31:46', NULL, 46, 2);
INSERT INTO `schedules` VALUES (809, '2024-10-20', 1, 4, 11, '2024-05-12 22:31:46', NULL, 47, 3);
INSERT INTO `schedules` VALUES (810, '2024-10-22', 1, 4, 11, '2024-05-12 22:31:46', NULL, 48, 4);
INSERT INTO `schedules` VALUES (811, '2024-05-01', 1, 2, 1, '2024-05-12 22:31:55', NULL, 1, 1);
INSERT INTO `schedules` VALUES (812, '2024-05-06', 1, 2, 1, '2024-05-12 22:31:55', NULL, 2, 2);
INSERT INTO `schedules` VALUES (813, '2024-05-08', 1, 2, 1, '2024-05-12 22:31:55', NULL, 3, 3);
INSERT INTO `schedules` VALUES (814, '2024-05-13', 1, 2, 1, '2024-05-12 22:31:55', NULL, 4, 4);
INSERT INTO `schedules` VALUES (815, '2024-05-15', 1, 2, 2, '2024-05-12 22:31:55', NULL, 5, 1);
INSERT INTO `schedules` VALUES (816, '2024-05-20', 1, 2, 2, '2024-05-12 22:31:55', NULL, 6, 2);
INSERT INTO `schedules` VALUES (817, '2024-05-22', 1, 2, 2, '2024-05-12 22:31:55', NULL, 7, 3);
INSERT INTO `schedules` VALUES (818, '2024-05-27', 1, 2, 2, '2024-05-12 22:31:55', NULL, 8, 4);
INSERT INTO `schedules` VALUES (819, '2024-05-29', 1, 2, 3, '2024-05-12 22:31:55', NULL, 9, 1);
INSERT INTO `schedules` VALUES (820, '2024-06-03', 1, 2, 3, '2024-05-12 22:31:55', NULL, 10, 2);
INSERT INTO `schedules` VALUES (821, '2024-06-05', 1, 2, 3, '2024-05-12 22:31:55', NULL, 11, 3);
INSERT INTO `schedules` VALUES (822, '2024-06-10', 1, 2, 3, '2024-05-12 22:31:55', NULL, 12, 4);
INSERT INTO `schedules` VALUES (823, '2024-06-12', 1, 2, 4, '2024-05-12 22:31:55', NULL, 13, 1);
INSERT INTO `schedules` VALUES (824, '2024-06-17', 1, 2, 4, '2024-05-12 22:31:55', NULL, 14, 2);
INSERT INTO `schedules` VALUES (825, '2024-06-19', 1, 2, 4, '2024-05-12 22:31:55', NULL, 15, 3);
INSERT INTO `schedules` VALUES (826, '2024-06-24', 1, 2, 4, '2024-05-12 22:31:55', NULL, 16, 4);
INSERT INTO `schedules` VALUES (827, '2024-06-26', 1, 2, 5, '2024-05-12 22:31:55', NULL, 17, 1);
INSERT INTO `schedules` VALUES (828, '2024-07-01', 1, 2, 5, '2024-05-12 22:31:55', NULL, 18, 2);
INSERT INTO `schedules` VALUES (829, '2024-07-03', 1, 2, 5, '2024-05-12 22:31:55', NULL, 19, 3);
INSERT INTO `schedules` VALUES (830, '2024-07-08', 1, 2, 5, '2024-05-12 22:31:55', NULL, 20, 4);
INSERT INTO `schedules` VALUES (831, '2024-07-10', 1, 2, 6, '2024-05-12 22:31:55', NULL, 21, 1);
INSERT INTO `schedules` VALUES (832, '2024-07-15', 1, 2, 6, '2024-05-12 22:31:55', NULL, 22, 2);
INSERT INTO `schedules` VALUES (833, '2024-07-17', 1, 2, 6, '2024-05-12 22:31:55', NULL, 23, 3);
INSERT INTO `schedules` VALUES (834, '2024-07-22', 1, 2, 6, '2024-05-12 22:31:55', NULL, 24, 4);
INSERT INTO `schedules` VALUES (835, '2024-07-24', 1, 2, 7, '2024-05-12 22:31:55', NULL, 25, 1);
INSERT INTO `schedules` VALUES (836, '2024-07-29', 1, 2, 7, '2024-05-12 22:31:55', NULL, 26, 2);
INSERT INTO `schedules` VALUES (837, '2024-07-31', 1, 2, 7, '2024-05-12 22:31:55', NULL, 27, 3);
INSERT INTO `schedules` VALUES (838, '2024-08-05', 1, 2, 7, '2024-05-12 22:31:55', NULL, 28, 4);
INSERT INTO `schedules` VALUES (839, '2024-08-07', 1, 2, 8, '2024-05-12 22:31:55', NULL, 29, 1);
INSERT INTO `schedules` VALUES (840, '2024-08-12', 1, 2, 8, '2024-05-12 22:31:55', NULL, 30, 2);
INSERT INTO `schedules` VALUES (841, '2024-08-14', 1, 2, 8, '2024-05-12 22:31:55', NULL, 31, 3);
INSERT INTO `schedules` VALUES (842, '2024-08-19', 1, 2, 8, '2024-05-12 22:31:55', NULL, 32, 4);
INSERT INTO `schedules` VALUES (843, '2024-08-21', 1, 2, 9, '2024-05-12 22:31:55', NULL, 33, 1);
INSERT INTO `schedules` VALUES (844, '2024-08-26', 1, 2, 9, '2024-05-12 22:31:55', NULL, 34, 2);
INSERT INTO `schedules` VALUES (845, '2024-08-28', 1, 2, 9, '2024-05-12 22:31:55', NULL, 35, 3);
INSERT INTO `schedules` VALUES (846, '2024-09-09', 1, 2, 9, '2024-05-12 22:31:55', NULL, 36, 4);
INSERT INTO `schedules` VALUES (847, '2024-09-11', 1, 2, 10, '2024-05-12 22:31:55', NULL, 37, 1);
INSERT INTO `schedules` VALUES (848, '2024-09-16', 1, 2, 10, '2024-05-12 22:31:55', NULL, 38, 2);
INSERT INTO `schedules` VALUES (849, '2024-09-18', 1, 2, 10, '2024-05-12 22:31:55', NULL, 39, 3);
INSERT INTO `schedules` VALUES (850, '2024-09-23', 1, 2, 10, '2024-05-12 22:31:55', NULL, 40, 4);
INSERT INTO `schedules` VALUES (851, '2024-09-25', 1, 2, 11, '2024-05-12 22:31:55', NULL, 41, 1);
INSERT INTO `schedules` VALUES (852, '2024-09-30', 1, 2, 11, '2024-05-12 22:31:55', NULL, 42, 2);
INSERT INTO `schedules` VALUES (853, '2024-10-02', 1, 2, 11, '2024-05-12 22:31:55', NULL, 43, 3);
INSERT INTO `schedules` VALUES (854, '2024-10-07', 1, 2, 11, '2024-05-12 22:31:55', NULL, 44, 4);
INSERT INTO `schedules` VALUES (855, '2024-10-09', 1, 2, 12, '2024-05-12 22:31:55', NULL, 45, 1);
INSERT INTO `schedules` VALUES (856, '2024-10-14', 1, 2, 12, '2024-05-12 22:31:55', NULL, 46, 2);
INSERT INTO `schedules` VALUES (857, '2024-10-16', 1, 2, 12, '2024-05-12 22:31:55', NULL, 47, 3);
INSERT INTO `schedules` VALUES (858, '2024-10-21', 1, 2, 12, '2024-05-12 22:31:55', NULL, 48, 4);
INSERT INTO `schedules` VALUES (859, '2024-05-03', 1, 3, 1, '2024-05-12 22:32:00', NULL, 1, 1);
INSERT INTO `schedules` VALUES (860, '2024-05-05', 1, 3, 1, '2024-05-12 22:32:00', NULL, 2, 2);
INSERT INTO `schedules` VALUES (861, '2024-05-10', 1, 3, 1, '2024-05-12 22:32:00', NULL, 3, 3);
INSERT INTO `schedules` VALUES (862, '2024-05-12', 1, 3, 1, '2024-05-12 22:32:00', NULL, 4, 4);
INSERT INTO `schedules` VALUES (863, '2024-05-17', 1, 3, 2, '2024-05-12 22:32:00', NULL, 5, 1);
INSERT INTO `schedules` VALUES (864, '2024-05-19', 1, 3, 2, '2024-05-12 22:32:00', NULL, 6, 2);
INSERT INTO `schedules` VALUES (865, '2024-05-24', 1, 3, 2, '2024-05-12 22:32:00', NULL, 7, 3);
INSERT INTO `schedules` VALUES (866, '2024-05-26', 1, 3, 2, '2024-05-12 22:32:00', NULL, 8, 4);
INSERT INTO `schedules` VALUES (867, '2024-05-31', 1, 3, 3, '2024-05-12 22:32:00', NULL, 9, 1);
INSERT INTO `schedules` VALUES (868, '2024-06-02', 1, 3, 3, '2024-05-12 22:32:00', NULL, 10, 2);
INSERT INTO `schedules` VALUES (869, '2024-06-07', 1, 3, 3, '2024-05-12 22:32:00', NULL, 11, 3);
INSERT INTO `schedules` VALUES (870, '2024-06-09', 1, 3, 3, '2024-05-12 22:32:00', NULL, 12, 4);
INSERT INTO `schedules` VALUES (871, '2024-06-14', 1, 3, 4, '2024-05-12 22:32:00', NULL, 13, 1);
INSERT INTO `schedules` VALUES (872, '2024-06-16', 1, 3, 4, '2024-05-12 22:32:00', NULL, 14, 2);
INSERT INTO `schedules` VALUES (873, '2024-06-21', 1, 3, 4, '2024-05-12 22:32:00', NULL, 15, 3);
INSERT INTO `schedules` VALUES (874, '2024-06-23', 1, 3, 4, '2024-05-12 22:32:00', NULL, 16, 4);
INSERT INTO `schedules` VALUES (875, '2024-06-28', 1, 3, 5, '2024-05-12 22:32:00', NULL, 17, 1);
INSERT INTO `schedules` VALUES (876, '2024-06-30', 1, 3, 5, '2024-05-12 22:32:00', NULL, 18, 2);
INSERT INTO `schedules` VALUES (877, '2024-07-05', 1, 3, 5, '2024-05-12 22:32:00', NULL, 19, 3);
INSERT INTO `schedules` VALUES (878, '2024-07-07', 1, 3, 5, '2024-05-12 22:32:00', NULL, 20, 4);
INSERT INTO `schedules` VALUES (879, '2024-07-12', 1, 3, 6, '2024-05-12 22:32:00', NULL, 21, 1);
INSERT INTO `schedules` VALUES (880, '2024-07-14', 1, 3, 6, '2024-05-12 22:32:00', NULL, 22, 2);
INSERT INTO `schedules` VALUES (881, '2024-07-19', 1, 3, 6, '2024-05-12 22:32:00', NULL, 23, 3);
INSERT INTO `schedules` VALUES (882, '2024-07-21', 1, 3, 6, '2024-05-12 22:32:00', NULL, 24, 4);
INSERT INTO `schedules` VALUES (883, '2024-07-26', 1, 3, 7, '2024-05-12 22:32:00', NULL, 25, 1);
INSERT INTO `schedules` VALUES (884, '2024-07-28', 1, 3, 7, '2024-05-12 22:32:00', NULL, 26, 2);
INSERT INTO `schedules` VALUES (885, '2024-08-02', 1, 3, 7, '2024-05-12 22:32:00', NULL, 27, 3);
INSERT INTO `schedules` VALUES (886, '2024-08-04', 1, 3, 7, '2024-05-12 22:32:00', NULL, 28, 4);
INSERT INTO `schedules` VALUES (887, '2024-08-09', 1, 3, 8, '2024-05-12 22:32:00', NULL, 29, 1);
INSERT INTO `schedules` VALUES (888, '2024-08-11', 1, 3, 8, '2024-05-12 22:32:00', NULL, 30, 2);
INSERT INTO `schedules` VALUES (889, '2024-08-16', 1, 3, 8, '2024-05-12 22:32:00', NULL, 31, 3);
INSERT INTO `schedules` VALUES (890, '2024-08-18', 1, 3, 8, '2024-05-12 22:32:00', NULL, 32, 4);
INSERT INTO `schedules` VALUES (891, '2024-08-23', 1, 3, 9, '2024-05-12 22:32:00', NULL, 33, 1);
INSERT INTO `schedules` VALUES (892, '2024-08-25', 1, 3, 9, '2024-05-12 22:32:00', NULL, 34, 2);
INSERT INTO `schedules` VALUES (893, '2024-08-30', 1, 3, 9, '2024-05-12 22:32:00', NULL, 35, 3);
INSERT INTO `schedules` VALUES (894, '2024-09-08', 1, 3, 9, '2024-05-12 22:32:00', NULL, 36, 4);
INSERT INTO `schedules` VALUES (895, '2024-09-13', 1, 3, 10, '2024-05-12 22:32:00', NULL, 37, 1);
INSERT INTO `schedules` VALUES (896, '2024-09-15', 1, 3, 10, '2024-05-12 22:32:00', NULL, 38, 2);
INSERT INTO `schedules` VALUES (897, '2024-09-20', 1, 3, 10, '2024-05-12 22:32:00', NULL, 39, 3);
INSERT INTO `schedules` VALUES (898, '2024-09-22', 1, 3, 10, '2024-05-12 22:32:00', NULL, 40, 4);
INSERT INTO `schedules` VALUES (899, '2024-09-27', 1, 3, 11, '2024-05-12 22:32:00', NULL, 41, 1);
INSERT INTO `schedules` VALUES (900, '2024-09-29', 1, 3, 11, '2024-05-12 22:32:00', NULL, 42, 2);
INSERT INTO `schedules` VALUES (901, '2024-10-04', 1, 3, 11, '2024-05-12 22:32:00', NULL, 43, 3);
INSERT INTO `schedules` VALUES (902, '2024-10-06', 1, 3, 11, '2024-05-12 22:32:00', NULL, 44, 4);
INSERT INTO `schedules` VALUES (903, '2024-10-11', 1, 3, 12, '2024-05-12 22:32:00', NULL, 45, 1);
INSERT INTO `schedules` VALUES (904, '2024-10-13', 1, 3, 12, '2024-05-12 22:32:00', NULL, 46, 2);
INSERT INTO `schedules` VALUES (905, '2024-10-18', 1, 3, 12, '2024-05-12 22:32:00', NULL, 47, 3);
INSERT INTO `schedules` VALUES (906, '2024-10-20', 1, 3, 12, '2024-05-12 22:32:00', NULL, 48, 4);
COMMIT;

-- ----------------------------
-- Table structure for sessions
-- ----------------------------
DROP TABLE IF EXISTS `sessions`;
CREATE TABLE `sessions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `class_id` int(11) DEFAULT NULL,
  `shift_id` int(11) DEFAULT NULL,
  `room_id` int(11) DEFAULT NULL COMMENT 'room id crm',
  `teacher_id` int(11) DEFAULT NULL COMMENT 'Đây là user_id trong bảng users của giáo viên chứ không phải teacher_id trong bảng teachers',
  `class_day` int(11) DEFAULT NULL COMMENT '0 - Chủ nhật,\r\n1 - Thứ 2, \r\n2 - Thứ 3, \r\n3 - Thứ 4, \r\n4 - Thứ 5, \r\n5 - Thứ 6, \r\n6 - Thứ 7 ',
  `status` tinyint(2) DEFAULT '1' COMMENT '0 - inactive, 1 - active',
  `created_at` datetime DEFAULT NULL,
  `creator_id` int(11) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updator_id` int(11) DEFAULT NULL,
  `branch_id` int(11) DEFAULT '0',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `class_id` (`class_id`) USING BTREE,
  KEY `shift_id` (`shift_id`) USING BTREE,
  KEY `teacher_id` (`teacher_id`) USING BTREE,
  KEY `room_id` (`room_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=79 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of sessions
-- ----------------------------
BEGIN;
INSERT INTO `sessions` VALUES (12, 5, 1, 1, 3289, 2, 1, '2024-05-09 22:12:50', 1, NULL, NULL, 1);
INSERT INTO `sessions` VALUES (13, 5, 1, 1, 3289, 8, 1, '2024-05-09 22:12:50', 1, NULL, NULL, 1);
INSERT INTO `sessions` VALUES (14, 6, 1, 1, 3289, 2, 1, '2024-05-09 22:14:55', 1, NULL, NULL, 1);
INSERT INTO `sessions` VALUES (15, 6, 1, 1, 3289, 7, 1, '2024-05-09 22:14:55', 1, NULL, NULL, 1);
INSERT INTO `sessions` VALUES (66, 1, NULL, 1, 3, 2, 1, '2024-05-12 22:28:41', 1, NULL, NULL, 1);
INSERT INTO `sessions` VALUES (67, 1, NULL, 1, 3, 3, 1, '2024-05-12 22:28:41', 1, NULL, NULL, 1);
INSERT INTO `sessions` VALUES (68, 1, NULL, 1, 3, 4, 1, '2024-05-12 22:28:41', 1, NULL, NULL, 1);
INSERT INTO `sessions` VALUES (69, 1, NULL, 1, 3, 5, 1, '2024-05-12 22:28:42', 1, NULL, NULL, 1);
INSERT INTO `sessions` VALUES (70, 1, NULL, 1, 3, 6, 1, '2024-05-12 22:28:42', 1, NULL, NULL, 1);
INSERT INTO `sessions` VALUES (71, 1, NULL, 1, 3, 7, 1, '2024-05-12 22:28:42', 1, NULL, NULL, 1);
INSERT INTO `sessions` VALUES (72, 1, NULL, 1, 3, 8, 1, '2024-05-12 22:28:42', 1, NULL, NULL, 1);
INSERT INTO `sessions` VALUES (73, 4, 1, 1, 6, 2, 1, '2024-05-12 22:31:46', 1, NULL, NULL, 1);
INSERT INTO `sessions` VALUES (74, 4, 1, 1, 6, 7, 1, '2024-05-12 22:31:46', 1, NULL, NULL, 1);
INSERT INTO `sessions` VALUES (75, 2, NULL, 2, 3, 3, 1, '2024-05-12 22:31:55', 1, NULL, NULL, 1);
INSERT INTO `sessions` VALUES (76, 2, NULL, 2, 3, 8, 1, '2024-05-12 22:31:55', 1, NULL, NULL, 1);
INSERT INTO `sessions` VALUES (77, 3, 1, 2, 3, 5, 1, '2024-05-12 22:32:00', 1, NULL, NULL, 1);
INSERT INTO `sessions` VALUES (78, 3, 1, 2, 3, 7, 1, '2024-05-12 22:32:00', 1, NULL, NULL, 1);
COMMIT;

-- ----------------------------
-- Table structure for shifts
-- ----------------------------
DROP TABLE IF EXISTS `shifts`;
CREATE TABLE `shifts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `status` tinyint(2) DEFAULT '0' COMMENT '0 - inactive, 1- active',
  `zone_id` tinyint(4) DEFAULT NULL,
  `creator_id` int(11) DEFAULT NULL,
  `updator_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of shifts
-- ----------------------------
BEGIN;
INSERT INTO `shifts` VALUES (1, 'Sáng 8:00 - 9:30', '08:00:00', '09:30:00', 1, NULL, 1, NULL, '2024-05-12 14:11:54', NULL);
INSERT INTO `shifts` VALUES (2, 'Sáng 9:30 - 11:00', '09:30:00', '11:00:00', 1, NULL, 1, NULL, '2024-05-12 14:13:08', NULL);
INSERT INTO `shifts` VALUES (3, 'Chiều 14:00 - 15:30', '14:00:00', '15:30:00', 1, NULL, 1, NULL, '2024-05-12 14:14:16', NULL);
INSERT INTO `shifts` VALUES (4, 'Chiều 15:30 - 17:00', '15:30:00', '17:00:00', 1, NULL, 1, NULL, '2024-05-12 14:14:46', NULL);
INSERT INTO `shifts` VALUES (5, 'Tối 17:30 - 19:00', '17:30:00', '19:00:00', 1, NULL, 1, NULL, '2024-05-12 14:15:22', NULL);
INSERT INTO `shifts` VALUES (6, 'Tối 19:00 - 20:30', '19:00:00', '20:30:00', 1, NULL, 1, 1, '2024-05-12 14:16:07', '2024-05-12 14:16:14');
COMMIT;

-- ----------------------------
-- Table structure for source_detail
-- ----------------------------
DROP TABLE IF EXISTS `source_detail`;
CREATE TABLE `source_detail` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(2) DEFAULT '0',
  `created_at` datetime DEFAULT NULL,
  `creator_id` int(11) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updator_id` int(11) DEFAULT NULL,
  `branch_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `branch_id` (`branch_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=461 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of source_detail
-- ----------------------------
BEGIN;
INSERT INTO `source_detail` VALUES (4, '480 Quận 1_Nguồn Ms Ngọc Anh HCM', 1, '2022-02-11 16:39:13', 21, NULL, NULL, NULL);
INSERT INTO `source_detail` VALUES (5, 'Mầm Non Q7_Nguồn Ms Ngọc Anh', 1, '2022-02-14 09:21:37', 21, NULL, NULL, NULL);
INSERT INTO `source_detail` VALUES (6, 'Data Quận 7_Ms Ngọc Anh', 1, '2022-02-14 09:23:27', 21, NULL, NULL, NULL);
INSERT INTO `source_detail` VALUES (7, 'Team Sales Đi trường_Nguồn Ms Ngọc Anh', 1, '2022-02-16 08:41:20', 21, '2022-02-16 08:41:57', 21, NULL);
INSERT INTO `source_detail` VALUES (8, 'Data Phụ Huynh học sinh các quận_NGA', 1, '2022-02-17 08:55:27', 21, NULL, NULL, NULL);
INSERT INTO `source_detail` VALUES (9, 'PTĐT - TNC- Hai Bà Trưng', 1, '2022-02-18 15:31:20', 21, NULL, NULL, NULL);
INSERT INTO `source_detail` VALUES (10, 'KH Vip Hạ Long_Mrs Huyền Anh', 1, '2022-03-14 08:57:24', 21, NULL, NULL, NULL);
INSERT INTO `source_detail` VALUES (11, 'Data Tiếng Anh Hạ Long', 1, '2022-03-17 16:41:15', 21, NULL, NULL, NULL);
INSERT INTO `source_detail` VALUES (12, 'MB Bank', 1, '2022-03-21 09:34:10', 21, NULL, NULL, NULL);
INSERT INTO `source_detail` VALUES (13, 'Data Apax', 1, '2022-03-28 17:16:55', 21, NULL, NULL, NULL);
INSERT INTO `source_detail` VALUES (14, 'Q.Hai Bà Trưng,Hoàn Kiếm_Diễm', 1, '2022-04-02 09:47:49', 39, '2022-04-16 16:56:44', 39, NULL);
INSERT INTO `source_detail` VALUES (15, 'C1 Ngọc Thụy', 1, '2022-04-05 08:51:51', 21, NULL, NULL, NULL);
INSERT INTO `source_detail` VALUES (16, 'Ecall', 1, '2022-04-05 14:10:56', 21, NULL, NULL, NULL);
INSERT INTO `source_detail` VALUES (17, 'DVKH', 1, '2022-04-05 14:14:57', 21, NULL, NULL, NULL);
INSERT INTO `source_detail` VALUES (18, 'nghĩa tân', 1, '2022-04-06 17:06:41', 78, NULL, NULL, NULL);
INSERT INTO `source_detail` VALUES (19, 'TH Dịch Vọng A', 1, '2022-04-07 10:07:23', 145, NULL, NULL, NULL);
INSERT INTO `source_detail` VALUES (20, 'Chung cư An Bình, Phạm Văn Đồng', 1, '2022-04-15 12:08:10', 145, NULL, NULL, NULL);
INSERT INTO `source_detail` VALUES (21, 'TUNG NVC', 1, '2022-04-16 16:49:57', 134, NULL, NULL, NULL);
INSERT INTO `source_detail` VALUES (22, 'B2B Trường_Diễm', 1, '2022-04-16 16:56:20', 39, '2022-04-16 16:56:27', 39, NULL);
INSERT INTO `source_detail` VALUES (23, 'Dũng PVD', 1, '2022-04-20 16:35:21', 78, NULL, NULL, NULL);
INSERT INTO `source_detail` VALUES (24, 'Dũng PVD 20/4', 1, '2022-04-20 20:00:54', 78, NULL, NULL, NULL);
INSERT INTO `source_detail` VALUES (25, 'Apax All_Linh', 1, '2022-04-22 14:09:54', 1, NULL, NULL, NULL);
INSERT INTO `source_detail` VALUES (26, 'Ecall_Linh', 1, '2022-04-22 14:10:15', 1, NULL, NULL, NULL);
INSERT INTO `source_detail` VALUES (27, 'PTĐT_Linh', 1, '2022-04-22 14:10:27', 1, NULL, NULL, NULL);
INSERT INTO `source_detail` VALUES (28, 'Trần Quốc Toản_Linh', 1, '2022-04-22 14:10:47', 1, NULL, NULL, NULL);
INSERT INTO `source_detail` VALUES (29, 'Mầm non A_Linh', 1, '2022-04-22 14:11:00', 1, NULL, NULL, NULL);
INSERT INTO `source_detail` VALUES (30, 'Mầm Non Chị Bình', 1, '2022-04-23 09:47:09', 78, NULL, NULL, NULL);
INSERT INTO `source_detail` VALUES (31, 'TH_Chương Dương_Linh', 1, '2022-04-25 15:33:10', 1, NULL, NULL, NULL);
INSERT INTO `source_detail` VALUES (32, 'Fastrackid_Linh', 1, '2022-04-25 15:34:50', 1, NULL, NULL, NULL);
INSERT INTO `source_detail` VALUES (33, 'MN_Quỳnh Mai_Linh', 1, '2022-04-25 15:51:18', 1, NULL, NULL, NULL);
INSERT INTO `source_detail` VALUES (34, 'MN Hạt mầm nhỏ_Linh', 1, '2022-04-25 16:09:57', 1, NULL, NULL, NULL);
INSERT INTO `source_detail` VALUES (35, 'TH_Đoàn kết_Linh', 1, '2022-04-25 16:21:59', 1, NULL, NULL, NULL);
INSERT INTO `source_detail` VALUES (36, 'Làng quốc tế Thăng Long', 1, '2022-04-25 16:26:43', 145, NULL, NULL, NULL);
INSERT INTO `source_detail` VALUES (37, 'TH Tây Sơn_Linh', 1, '2022-04-25 16:36:20', 1, NULL, NULL, NULL);
INSERT INTO `source_detail` VALUES (38, 'PTĐT - Alfred Nobel', 1, '2022-04-28 09:39:58', 21, NULL, NULL, NULL);
INSERT INTO `source_detail` VALUES (39, 'Data Cầu Giấy_PTĐT', 1, '2022-04-28 09:44:27', 21, NULL, NULL, NULL);
INSERT INTO `source_detail` VALUES (40, 'Data Long Biên_Nguồn PTĐT', 1, '2022-04-28 09:49:16', 21, NULL, NULL, NULL);
INSERT INTO `source_detail` VALUES (41, 'Data HCM_Nguồn PTĐT', 1, '2022-04-28 10:04:37', 21, NULL, NULL, NULL);
INSERT INTO `source_detail` VALUES (42, 'Dung Mỹ Đình', 1, '2022-04-28 14:35:06', 59, NULL, NULL, NULL);
INSERT INTO `source_detail` VALUES (43, 'Data Ba ĐÌnh_PTĐT', 0, '2022-04-29 10:00:37', 21, '2022-04-29 10:01:55', 21, NULL);
INSERT INTO `source_detail` VALUES (44, 'Data cư dân Hạ Long_Nam HL', 1, '2022-05-07 09:52:57', 21, NULL, NULL, NULL);
INSERT INTO `source_detail` VALUES (45, 'PVD 10/5/2022', 1, '2022-05-10 14:53:37', 78, NULL, NULL, NULL);
INSERT INTO `source_detail` VALUES (46, 'Scots English', 1, '2022-05-10 15:53:49', 157, NULL, NULL, NULL);
INSERT INTO `source_detail` VALUES (47, 'Dũng PVD 11/5/2022', 1, '2022-05-11 10:15:56', 78, NULL, NULL, NULL);
INSERT INTO `source_detail` VALUES (48, 'Mầm non Họa Mi_Linh 12/5', 1, '2022-05-12 17:54:24', 40, '2022-05-12 18:24:10', 40, NULL);
INSERT INTO `source_detail` VALUES (49, 'Mầm non Việt Bun_Linh', 1, '2022-05-14 14:04:49', 40, NULL, NULL, NULL);
INSERT INTO `source_detail` VALUES (50, 'Nguồn test', 1, '2022-05-17 10:01:34', 193, NULL, NULL, NULL);
INSERT INTO `source_detail` VALUES (51, 'Mầm Non AMG', 1, '2022-05-17 14:52:55', 183, NULL, NULL, NULL);
INSERT INTO `source_detail` VALUES (52, 'MN Kitten', 1, '2022-05-18 13:55:40', 21, NULL, NULL, NULL);
INSERT INTO `source_detail` VALUES (53, 'Tiểu Học Trần Quốc Toản_ HL', 1, '2022-05-19 09:55:51', 97, NULL, NULL, NULL);
INSERT INTO `source_detail` VALUES (54, 'TH Lê Ngọc Hân_Linh PH', 1, '2022-05-19 16:00:37', 40, NULL, NULL, NULL);
INSERT INTO `source_detail` VALUES (55, 'Mầm non Mickey', 1, '2022-05-19 20:26:21', 21, NULL, NULL, NULL);
INSERT INTO `source_detail` VALUES (56, 'Mầm non Quang Trung HBT_Linh', 1, '2022-05-24 17:41:40', 40, NULL, NULL, NULL);
INSERT INTO `source_detail` VALUES (57, 'MN Global Kids', 1, '2022-05-25 11:44:22', 21, NULL, NULL, NULL);
INSERT INTO `source_detail` VALUES (58, 'MN Small World', 1, '2022-05-25 11:44:34', 21, NULL, NULL, NULL);
INSERT INTO `source_detail` VALUES (59, 'Data Apax Quảng Ninh_Nam HL', 1, '2022-05-26 17:42:51', 21, NULL, NULL, NULL);
INSERT INTO `source_detail` VALUES (60, 'CLB Việt Nhí', 1, '2022-05-30 16:39:11', 162, NULL, NULL, NULL);
INSERT INTO `source_detail` VALUES (61, 'Data mầm non Hà Đông_T5', 1, '2022-05-31 09:14:28', 21, NULL, NULL, NULL);
INSERT INTO `source_detail` VALUES (62, 'Data mobifone_T5', 1, '2022-05-31 09:14:40', 21, NULL, NULL, NULL);
INSERT INTO `source_detail` VALUES (63, 'Data Ecall_Bình', 1, '2022-06-01 10:09:30', 21, NULL, NULL, NULL);
INSERT INTO `source_detail` VALUES (64, 'Mầm non Hồng Tiến, Long Biên', 1, '2022-06-03 15:51:34', 162, NULL, NULL, NULL);
INSERT INTO `source_detail` VALUES (65, 'Mầm non Pink Sunshine', 1, '2022-06-06 13:41:23', 162, NULL, NULL, NULL);
INSERT INTO `source_detail` VALUES (66, 'Mầm non Hạnh Phúc', 1, '2022-06-06 18:04:21', 21, NULL, NULL, NULL);
INSERT INTO `source_detail` VALUES (67, 'Mầm Non Long Biên', 1, '2022-06-07 14:40:31', 21, NULL, NULL, NULL);
INSERT INTO `source_detail` VALUES (68, 'Mầm non Tuổi Hồng', 1, '2022-06-08 13:48:57', 21, NULL, NULL, NULL);
INSERT INTO `source_detail` VALUES (69, 'Mầm Non Baby World', 1, '2022-06-09 15:20:30', 21, NULL, NULL, NULL);
INSERT INTO `source_detail` VALUES (70, 'Mầm non Chị Ong Nâu_HBT Linh', 1, '2022-06-09 19:11:27', 40, NULL, NULL, NULL);
INSERT INTO `source_detail` VALUES (71, 'MN Bách Khoa HBT_Linh', 1, '2022-06-14 17:55:56', 40, NULL, NULL, NULL);
INSERT INTO `source_detail` VALUES (72, 'Mầm Non Teddy', 1, '2022-06-16 09:35:56', 21, NULL, NULL, NULL);
INSERT INTO `source_detail` VALUES (73, 'MN Wonderland', 1, '2022-06-17 09:02:09', 21, NULL, NULL, NULL);
INSERT INTO `source_detail` VALUES (74, 'Phố Huế_Linh', 1, '2022-06-17 10:23:10', 40, NULL, NULL, NULL);
INSERT INTO `source_detail` VALUES (75, 'MỎ VÀNG - TUNG NVC', 1, '2022-06-18 16:12:44', 134, NULL, NULL, NULL);
INSERT INTO `source_detail` VALUES (76, 'Data dân cư các tòa nhà khu Mỹ Đình', 1, '2022-06-22 09:11:10', 157, '2022-06-23 15:19:34', 157, 2);
INSERT INTO `source_detail` VALUES (77, 'MN BEN BEN_NVC', 1, '2022-06-22 17:20:44', 39, NULL, NULL, 5);
INSERT INTO `source_detail` VALUES (78, 'Mầm non BEN BEN TUNG NVC', 1, '2022-06-22 17:29:41', 134, NULL, NULL, 9);
INSERT INTO `source_detail` VALUES (79, 'PH Trường FPT', 1, '2022-06-22 18:05:23', 157, NULL, NULL, 2);
INSERT INTO `source_detail` VALUES (80, 'PHHS Mầm non quận Thanh Xuân', 1, '2022-06-23 14:52:21', 157, NULL, NULL, 2);
INSERT INTO `source_detail` VALUES (81, 'Mầm Non Hoa Hồng và Mễ Trì', 1, '2022-06-24 09:16:27', 157, NULL, NULL, 2);
INSERT INTO `source_detail` VALUES (82, 'data khu vực Hà Đông', 1, '2022-06-24 16:32:28', 157, NULL, NULL, 2);
INSERT INTO `source_detail` VALUES (83, 'PVD_Yên sở', 1, '2022-06-24 17:59:26', 78, NULL, NULL, 7);
INSERT INTO `source_detail` VALUES (84, 'PVD 26/6', 1, '2022-06-26 15:04:18', 78, NULL, NULL, 7);
INSERT INTO `source_detail` VALUES (85, 'Mầm Non Hữu Phước_Sales Hub', 1, '2022-06-29 10:46:43', 21, NULL, NULL, 0);
INSERT INTO `source_detail` VALUES (86, 'Mn Himawari_Sale Hub', 1, '2022-07-01 08:47:13', 21, NULL, NULL, 0);
INSERT INTO `source_detail` VALUES (87, 'Apax GD - Dung MĐ', 1, '2022-07-02 15:45:59', 59, NULL, NULL, 2);
INSERT INTO `source_detail` VALUES (88, 'Vinschool Royal - Dung MĐ', 1, '2022-07-03 15:48:31', 59, NULL, NULL, 2);
INSERT INTO `source_detail` VALUES (89, '6/7 Dũng PVD', 1, '2022-07-06 14:32:51', 78, '2022-07-06 14:33:15', 78, 7);
INSERT INTO `source_detail` VALUES (90, 'Sakura Dung MĐ', 1, '2022-07-07 15:40:32', 59, '2022-07-07 15:44:49', 59, 2);
INSERT INTO `source_detail` VALUES (91, 'Mầm non Bibo house', 1, '2022-07-07 16:55:40', 21, NULL, NULL, 0);
INSERT INTO `source_detail` VALUES (92, 'TH Nguyễn Bá Ngọc', 1, '2022-07-08 09:40:19', 84, '2022-07-08 09:40:25', 84, 6);
INSERT INTO `source_detail` VALUES (93, 'Nguồn Cầu Giấy_Đào Ngọc Anh', 1, '2022-07-11 15:58:41', 1, NULL, NULL, NULL);
INSERT INTO `source_detail` VALUES (94, 'Mn bibo house cs Kiều Mai', 1, '2022-07-13 18:11:56', 21, NULL, NULL, 0);
INSERT INTO `source_detail` VALUES (95, 'C1 - BẾ VĂN ĐÀN - TÙNG NVC', 1, '2022-07-16 09:19:41', 134, NULL, NULL, 9);
INSERT INTO `source_detail` VALUES (96, 'HOÀN KIẾM - TÙNG NVC', 1, '2022-07-16 09:30:31', 134, NULL, NULL, 9);
INSERT INTO `source_detail` VALUES (97, 'BA ĐÌNH - TÙNG NVC', 1, '2022-07-16 10:07:27', 134, NULL, NULL, 9);
INSERT INTO `source_detail` VALUES (98, 'MN HOA HƯỚNG DƯƠNG - CẦU GIẤY - TÙNG NVC', 1, '2022-07-16 12:00:51', 134, NULL, NULL, 9);
INSERT INTO `source_detail` VALUES (99, 'MN SAO MAI-THUPVĐ', 1, '2022-07-16 14:49:03', 220, '2022-07-16 15:53:09', 220, 7);
INSERT INTO `source_detail` VALUES (100, 'TH QUỲNH MAI - HBT - TÙNG NVC', 1, '2022-07-16 14:59:22', 134, NULL, NULL, 9);
INSERT INTO `source_detail` VALUES (101, 'MN HOA SEN -VĂN QUÁN HĐ - TÙNG NVC', 1, '2022-07-16 15:48:08', 134, NULL, NULL, 9);
INSERT INTO `source_detail` VALUES (102, 'MN A', 1, '2022-07-16 16:08:07', 220, NULL, NULL, 7);
INSERT INTO `source_detail` VALUES (103, 'Sakura MĐ', 1, '2022-07-16 16:13:48', 183, NULL, NULL, 2);
INSERT INTO `source_detail` VALUES (104, 'Trần Hưng Đạo', 1, '2022-07-16 16:16:52', 183, NULL, NULL, 2);
INSERT INTO `source_detail` VALUES (105, 'Highland coffee pvđ_PTĐT', 1, '2022-07-18 22:25:54', 21, NULL, NULL, 0);
INSERT INTO `source_detail` VALUES (106, 'TIỂU HỌC TRUNG TỰ - TÙNG NVC', 0, '2022-07-19 11:37:30', 134, '2022-07-19 11:38:02', 134, 9);
INSERT INTO `source_detail` VALUES (107, 'TIỂU HỌC TRUNG YÊN - TÙNG NVC', 1, '2022-07-19 11:38:32', 134, NULL, NULL, 9);
INSERT INTO `source_detail` VALUES (108, 'MN Cá Voi Xanh', 1, '2022-07-20 17:35:17', 21, NULL, NULL, 0);
INSERT INTO `source_detail` VALUES (109, 'MN Cánh Diều Bay_Linh', 1, '2022-07-20 17:41:34', 40, NULL, NULL, 5);
INSERT INTO `source_detail` VALUES (110, 'Tiêm chủng 2013', 1, '2022-07-22 11:02:21', 97, NULL, NULL, 10);
INSERT INTO `source_detail` VALUES (111, 'TH ĐỨC THẮNG - PVĐ', 1, '2022-07-23 15:34:57', 220, NULL, NULL, 7);
INSERT INTO `source_detail` VALUES (112, 'MN XUÂN ĐỈNH-PVĐ', 1, '2022-07-26 10:29:42', 220, NULL, NULL, 7);
INSERT INTO `source_detail` VALUES (113, 'Tiểu học Xuân Đỉnh', 1, '2022-07-26 11:51:23', 21, NULL, NULL, 0);
INSERT INTO `source_detail` VALUES (114, 'Data thanh Xuân', 1, '2022-07-26 16:03:39', 183, '2023-10-06 14:31:57', 417, 2);
INSERT INTO `source_detail` VALUES (115, 'KH làm Master card', 1, '2022-07-27 14:41:17', 21, NULL, NULL, 0);
INSERT INTO `source_detail` VALUES (116, 'PVĐ', 1, '2022-07-27 20:00:32', 220, NULL, NULL, 7);
INSERT INTO `source_detail` VALUES (117, 'MN Tuổi Thơ', 1, '2022-08-03 09:28:58', 21, NULL, NULL, 0);
INSERT INTO `source_detail` VALUES (118, 'MN Học Viện Mặt Trời', 1, '2022-08-03 14:25:10', 30, '2022-08-03 14:56:17', 30, 0);
INSERT INTO `source_detail` VALUES (119, 'MN Cầu Diễn', 1, '2022-08-03 18:44:24', 183, NULL, NULL, 2);
INSERT INTO `source_detail` VALUES (120, 'MN Sao Mai', 1, '2022-08-03 22:29:14', 157, NULL, NULL, 2);
INSERT INTO `source_detail` VALUES (121, 'MN Phương Canh', 1, '2022-08-03 22:29:27', 157, NULL, NULL, 2);
INSERT INTO `source_detail` VALUES (122, 'vinhomes gardenia', 1, '2022-08-05 17:28:54', 183, NULL, NULL, 2);
INSERT INTO `source_detail` VALUES (123, 'th nhân chính-pvd', 1, '2022-08-07 10:23:23', 220, NULL, NULL, 7);
INSERT INTO `source_detail` VALUES (124, 'MN LONG BIÊN 2022 TUNG NVC', 1, '2022-08-07 12:29:51', 134, NULL, NULL, 9);
INSERT INTO `source_detail` VALUES (125, 'Mn Nhân chính', 1, '2022-08-07 15:05:15', 183, NULL, NULL, 2);
INSERT INTO `source_detail` VALUES (126, 'TH Phương Mai_Linh', 1, '2022-08-09 11:22:53', 40, NULL, NULL, 5);
INSERT INTO `source_detail` VALUES (127, 'th yên sở- PVĐ', 1, '2022-08-10 11:29:32', 220, NULL, NULL, 7);
INSERT INTO `source_detail` VALUES (128, 'MN Chim Non Lò Đúc_Linh', 1, '2022-08-10 16:38:39', 40, NULL, NULL, 5);
INSERT INTO `source_detail` VALUES (129, 'Tiểu Học Mễ Trì', 1, '2022-08-11 11:44:13', 183, NULL, NULL, 2);
INSERT INTO `source_detail` VALUES (130, 'Tiểu học Trung Hòa', 1, '2022-08-11 11:44:30', 183, NULL, NULL, 2);
INSERT INTO `source_detail` VALUES (131, 'MN Sao Mai', 0, '2022-08-11 11:52:06', 183, '2022-08-11 15:05:15', 183, 2);
INSERT INTO `source_detail` VALUES (132, 'TP.HUB', 1, '2022-08-12 15:01:12', 21, NULL, NULL, 0);
INSERT INTO `source_detail` VALUES (133, 'TH NGUYỄN KHẢ TRẠC-PVĐ', 1, '2022-08-13 15:26:46', 220, NULL, NULL, 7);
INSERT INTO `source_detail` VALUES (134, 'TH ĐÔNG NGẠC-PVĐ', 1, '2022-08-14 17:59:18', 220, NULL, NULL, 7);
INSERT INTO `source_detail` VALUES (135, 'MN XUÂN ĐỈNH A', 1, '2022-08-14 18:15:09', 220, NULL, NULL, 7);
INSERT INTO `source_detail` VALUES (136, 'MN XUÂN ĐỈNH A-PVĐ', 1, '2022-08-14 19:17:44', 220, NULL, NULL, 7);
INSERT INTO `source_detail` VALUES (137, 'MN XUÂN ĐỈNH CS1-PVĐ', 1, '2022-08-16 10:25:45', 220, '2022-08-16 10:25:57', 220, 7);
INSERT INTO `source_detail` VALUES (138, 'Data Thanh Tâm', 1, '2022-08-19 10:24:30', 97, NULL, NULL, 10);
INSERT INTO `source_detail` VALUES (139, 'Hanoi Academy - TPHUB', 1, '2022-08-20 10:39:31', 275, '2022-08-20 10:40:46', 275, 0);
INSERT INTO `source_detail` VALUES (140, 'GĐ HUB', 1, '2022-08-22 11:26:54', 21, NULL, NULL, 0);
INSERT INTO `source_detail` VALUES (141, 'DANH SÁCH 700 KHÁCH HÀNG THẺ SEOUL GARDEN', 1, '2022-08-22 12:04:59', 21, NULL, NULL, 0);
INSERT INTO `source_detail` VALUES (142, 'DANH SÁCH 521 KHÁCH HÀNG CĂN HỘ THE MANOR HCM', 1, '2022-08-22 12:05:23', 21, NULL, NULL, 0);
INSERT INTO `source_detail` VALUES (143, 'TopCV - TPHUB', 1, '2022-08-23 10:44:56', 275, '2022-08-23 10:45:50', 275, 0);
INSERT INTO `source_detail` VALUES (144, 'Mầm non Sao Sáng Hàng Bài_Linh', 1, '2022-08-23 14:08:35', 40, NULL, NULL, 5);
INSERT INTO `source_detail` VALUES (145, 'Mầm Non Ngôi sao xanh', 1, '2022-08-26 14:00:42', 275, NULL, NULL, 0);
INSERT INTO `source_detail` VALUES (146, 'CƯ DÂN TIMES CITY', 1, '2022-08-26 20:55:24', 21, NULL, NULL, 0);
INSERT INTO `source_detail` VALUES (147, 'MN KV HOÀNG MAI - TÙNG NVC', 1, '2022-08-30 18:21:41', 134, NULL, NULL, 9);
INSERT INTO `source_detail` VALUES (148, 'Data  SK 1', 1, '2022-09-06 15:10:21', 21, NULL, NULL, 0);
INSERT INTO `source_detail` VALUES (149, 'Data NVC _ Nguồn Ms Trang', 1, '2022-09-06 17:28:22', 21, NULL, NULL, 0);
INSERT INTO `source_detail` VALUES (150, 'mầm non hoa hồng - 110 quán thánh', 1, '2022-09-06 17:37:17', 21, NULL, NULL, 0);
INSERT INTO `source_detail` VALUES (151, 'Data Xuân Diệu', 1, '2022-09-08 10:32:33', 21, NULL, NULL, 0);
INSERT INTO `source_detail` VALUES (152, 'Tiểu học Việt Hưng - GĐPTKD', 1, '2022-09-08 10:44:13', 1, NULL, NULL, NULL);
INSERT INTO `source_detail` VALUES (153, 'Kossmo', 1, '2022-09-08 11:12:54', 21, NULL, NULL, 0);
INSERT INTO `source_detail` VALUES (154, 'Data NKT _Ms Trang', 1, '2022-09-08 16:53:35', 21, NULL, NULL, 0);
INSERT INTO `source_detail` VALUES (155, '_Nguồn Ms.Trang', 1, '2022-09-08 19:58:16', 21, NULL, NULL, 0);
INSERT INTO `source_detail` VALUES (156, 'Nha khoa_Nguồn Ms.Trang', 1, '2022-09-08 19:58:27', 21, NULL, NULL, 0);
INSERT INTO `source_detail` VALUES (157, 'Data TT - MĐ', 1, '2022-09-08 20:11:17', 21, NULL, NULL, 0);
INSERT INTO `source_detail` VALUES (158, 'MN Lê Đại Hành_Linh', 1, '2022-09-09 10:58:11', 40, NULL, NULL, 5);
INSERT INTO `source_detail` VALUES (159, 'TH Everest_Linh PH', 1, '2022-09-09 15:49:55', 40, NULL, NULL, 5);
INSERT INTO `source_detail` VALUES (160, 'TH Phú Lương_Linh PH', 1, '2022-09-09 15:50:31', 40, NULL, NULL, 5);
INSERT INTO `source_detail` VALUES (161, 'PTĐT - MN TXB', 1, '2022-09-09 18:58:48', 65, '2022-09-09 18:58:59', 65, 4);
INSERT INTO `source_detail` VALUES (162, 'PTĐT - Mn X20', 1, '2022-09-11 14:35:40', 65, NULL, NULL, 4);
INSERT INTO `source_detail` VALUES (163, 'Tiểu Học Thăng Long-Linh', 1, '2022-09-13 17:51:57', 84, '2023-09-11 07:42:00', 418, 6);
INSERT INTO `source_detail` VALUES (164, 'MN Ngôi nhà trẻ thơ_Linh PH', 1, '2022-09-13 21:28:17', 40, NULL, NULL, 5);
INSERT INTO `source_detail` VALUES (165, 'DT LỌC', 1, '2022-09-14 00:16:17', 21, NULL, NULL, 0);
INSERT INTO `source_detail` VALUES (166, 'MN Hoa Anh Đào_Sales Hub', 1, '2022-09-14 00:34:24', 21, NULL, NULL, 0);
INSERT INTO `source_detail` VALUES (167, 'DATA NVC (1)', 1, '2022-09-14 23:38:52', 21, NULL, NULL, 0);
INSERT INTO `source_detail` VALUES (168, 'DATA KHU NKT', 1, '2022-09-14 23:53:43', 21, NULL, NULL, 0);
INSERT INTO `source_detail` VALUES (169, 'TH QUANG TRUNG-PVĐ', 1, '2022-09-15 14:51:13', 220, NULL, NULL, 7);
INSERT INTO `source_detail` VALUES (170, 'MN LÝ THƯỜNG KIỆT-PVĐ', 1, '2022-09-16 11:10:48', 220, NULL, NULL, 7);
INSERT INTO `source_detail` VALUES (171, 'Mẫu Giáo Số 9 Ba Đình_PTĐT', 1, '2022-09-16 16:27:33', 30, NULL, NULL, 0);
INSERT INTO `source_detail` VALUES (172, 'TH PHÚC DIỄN-PVĐ', 1, '2022-09-16 16:30:00', 220, NULL, NULL, 7);
INSERT INTO `source_detail` VALUES (173, 'TH TÂN QUY Q7- PVĐ', 1, '2022-09-18 10:00:11', 220, NULL, NULL, 7);
INSERT INTO `source_detail` VALUES (174, 'TH TÂN THUẬN ĐÔNG Q7-PVĐ', 1, '2022-09-18 12:08:36', 220, NULL, NULL, 7);
INSERT INTO `source_detail` VALUES (175, 'TH TÂN ĐỊNH-PVĐ', 1, '2022-09-18 16:17:50', 220, NULL, NULL, 7);
INSERT INTO `source_detail` VALUES (176, 'TH DỊCH VỌNG B-PVĐ', 1, '2022-09-18 17:40:56', 220, '2022-09-18 17:41:05', 220, 7);
INSERT INTO `source_detail` VALUES (177, 'MN Nắng Mai HK_Linh PH', 1, '2022-09-20 17:44:10', 40, NULL, NULL, 5);
INSERT INTO `source_detail` VALUES (178, 'th đặng thùy trâm-PVĐ', 1, '2022-09-20 18:46:26', 220, NULL, NULL, 7);
INSERT INTO `source_detail` VALUES (179, 'TH Nhân Chính', 1, '2022-09-21 16:45:22', 65, NULL, NULL, 4);
INSERT INTO `source_detail` VALUES (180, 'TH Nhân Chính', 1, '2022-09-22 08:59:54', 65, NULL, NULL, 4);
INSERT INTO `source_detail` VALUES (181, 'TH Tam Khương_Linh PH', 1, '2022-09-22 10:23:51', 40, NULL, NULL, 5);
INSERT INTO `source_detail` VALUES (182, 'Tiểu Học Ái Mộ A_ Long Biên_PTĐT', 1, '2022-09-22 11:58:20', 30, NULL, NULL, 0);
INSERT INTO `source_detail` VALUES (183, 'MN BẠCH ĐẰNG -PVĐ', 1, '2022-09-22 17:20:16', 220, NULL, NULL, 7);
INSERT INTO `source_detail` VALUES (184, 'Tiểu Học Đoàn Kết_Long Biên_PTĐT', 1, '2022-09-23 09:30:18', 30, NULL, NULL, 0);
INSERT INTO `source_detail` VALUES (185, 'TH LOng Biên_PTĐT', 1, '2022-09-24 09:30:26', 21, NULL, NULL, 0);
INSERT INTO `source_detail` VALUES (186, 'KV BTL-PVĐ', 1, '2022-09-24 10:52:42', 220, NULL, NULL, 7);
INSERT INTO `source_detail` VALUES (187, 'TH NGHĨA TÂN-PVĐ', 1, '2022-09-24 17:13:51', 220, NULL, NULL, 7);
INSERT INTO `source_detail` VALUES (188, 'TH NHẬT TÂN- PVĐ', 1, '2022-09-24 18:01:48', 220, NULL, NULL, 7);
INSERT INTO `source_detail` VALUES (189, 'MN Trung Tự_Linh PH', 1, '2022-09-25 15:54:50', 40, NULL, NULL, 5);
INSERT INTO `source_detail` VALUES (190, 'Apax HQV', 1, '2022-09-27 08:48:22', 21, NULL, NULL, 0);
INSERT INTO `source_detail` VALUES (191, 'ĐẶT LỊCH_06.19', 1, '2022-09-27 08:56:41', 21, NULL, NULL, 0);
INSERT INTO `source_detail` VALUES (192, 'Bắt trùng', 1, '2022-09-27 09:07:40', 21, NULL, NULL, 0);
INSERT INTO `source_detail` VALUES (193, 'Data Online', 1, '2022-09-27 09:15:13', 21, NULL, NULL, 0);
INSERT INTO `source_detail` VALUES (194, 'Team A - August Demo', 1, '2022-09-27 09:26:07', 21, NULL, NULL, 0);
INSERT INTO `source_detail` VALUES (195, 'CHI XINH-PVĐ', 1, '2022-09-27 10:17:46', 220, NULL, NULL, 7);
INSERT INTO `source_detail` VALUES (196, 'Data Tiếng Anh Hạ Long', 1, '2022-09-27 11:08:45', 21, NULL, NULL, 0);
INSERT INTO `source_detail` VALUES (197, 'MN Bibo', 1, '2022-09-27 14:12:26', 21, NULL, NULL, 0);
INSERT INTO `source_detail` VALUES (198, 'DSJH', 1, '2022-09-27 14:28:05', 21, NULL, NULL, 0);
INSERT INTO `source_detail` VALUES (199, 'C1 Lomonoxop', 1, '2022-09-28 10:53:30', 21, NULL, NULL, 0);
INSERT INTO `source_detail` VALUES (200, 'MN Ánh Sao - Thanh Xuân', 1, '2022-09-30 10:43:38', 162, NULL, NULL, 0);
INSERT INTO `source_detail` VALUES (201, 'TH Bà Triệu_ Linh PH', 1, '2022-10-04 17:35:44', 40, NULL, NULL, 5);
INSERT INTO `source_detail` VALUES (202, 'lucky', 1, '2022-10-05 10:29:16', 220, '2022-10-11 16:46:24', 220, 7);
INSERT INTO `source_detail` VALUES (203, 'Data trường', 1, '2022-10-07 09:18:23', 21, NULL, NULL, 0);
INSERT INTO `source_detail` VALUES (204, 'MN Doremon_PTĐT', 1, '2022-10-07 10:07:48', 21, '2022-10-07 10:07:59', 21, 0);
INSERT INTO `source_detail` VALUES (205, 'CLB Patin LB', 1, '2022-10-07 11:44:58', 144, NULL, NULL, 9);
INSERT INTO `source_detail` VALUES (206, 'FB Soroban', 1, '2022-10-07 11:56:46', 144, NULL, NULL, 9);
INSERT INTO `source_detail` VALUES (207, 'MN Sơn Ca', 1, '2022-10-07 14:38:53', 21, NULL, NULL, 0);
INSERT INTO `source_detail` VALUES (208, 'MN MiMi House_PTDT', 1, '2022-10-08 10:33:50', 30, NULL, NULL, 0);
INSERT INTO `source_detail` VALUES (209, 'LỢI PVĐ', 1, '2022-10-09 11:23:05', 220, NULL, NULL, 7);
INSERT INTO `source_detail` VALUES (210, 'QUỲNH 18 TUỔI PVĐ', 1, '2022-10-09 11:23:40', 220, NULL, NULL, 7);
INSERT INTO `source_detail` VALUES (211, 'MN Sakura Mỹ Đình', 1, '2022-10-12 11:35:35', 144, NULL, NULL, 9);
INSERT INTO `source_detail` VALUES (212, 'Mn Maple Bear Bắc Ninhh', 1, '2022-10-13 15:46:34', 144, NULL, NULL, 9);
INSERT INTO `source_detail` VALUES (213, 'MN Chim Non', 0, '2022-10-14 10:43:39', 30, '2022-10-14 11:20:36', 21, 0);
INSERT INTO `source_detail` VALUES (214, 'MN Chim Non_PTĐT', 1, '2022-10-14 10:53:53', 30, NULL, NULL, 0);
INSERT INTO `source_detail` VALUES (215, 'Data Nga', 1, '2022-10-14 13:48:34', 21, NULL, NULL, 0);
INSERT INTO `source_detail` VALUES (216, 'vinschool PVĐ', 1, '2022-10-14 14:25:50', 220, '2023-05-18 10:40:52', 50, 7);
INSERT INTO `source_detail` VALUES (217, 'C1 Trần Quốc Toản', 1, '2022-10-14 15:46:37', 21, NULL, NULL, 0);
INSERT INTO `source_detail` VALUES (218, 'MN Họa Mi Hàng Giấy_Linh PH', 1, '2022-10-14 17:32:57', 40, NULL, NULL, 5);
INSERT INTO `source_detail` VALUES (219, 'Review Các trường mầm non và tiểu học Hà Nội', 1, '2022-10-14 20:01:01', 144, NULL, NULL, 9);
INSERT INTO `source_detail` VALUES (220, 'MG Đống Mác _ Linh PH', 1, '2022-10-18 17:21:58', 40, NULL, NULL, 5);
INSERT INTO `source_detail` VALUES (221, 'Mua vé Tiny World', 1, '2022-10-20 18:45:13', 144, NULL, NULL, 9);
INSERT INTO `source_detail` VALUES (222, 'PTDT - MN Bình Minh', 1, '2022-10-21 15:49:29', 65, NULL, NULL, 4);
INSERT INTO `source_detail` VALUES (223, 'MN Jerry House', 1, '2022-10-22 08:41:42', 21, NULL, NULL, 0);
INSERT INTO `source_detail` VALUES (224, 'MN Happy Kid', 1, '2022-10-22 09:09:41', 21, NULL, NULL, 0);
INSERT INTO `source_detail` VALUES (225, 'MN Midori', 1, '2022-10-22 09:38:56', 21, NULL, NULL, 0);
INSERT INTO `source_detail` VALUES (226, 'MN HMN', 1, '2022-10-22 09:53:11', 21, NULL, NULL, 0);
INSERT INTO `source_detail` VALUES (227, 'Gà Trống Vàng', 1, '2022-10-22 10:05:08', 21, NULL, NULL, 0);
INSERT INTO `source_detail` VALUES (228, 'Nhung Times', 1, '2022-10-27 11:22:09', 64, NULL, NULL, 4);
INSERT INTO `source_detail` VALUES (229, 'TH TRUNG YÊN- PVĐ THU', 1, '2022-10-27 15:39:30', 220, NULL, NULL, 7);
INSERT INTO `source_detail` VALUES (230, 'PTĐT - Mn Hoa Trà My', 1, '2022-10-27 15:58:19', 65, '2022-10-27 15:58:33', 65, 4);
INSERT INTO `source_detail` VALUES (231, 'Nhung 31/10/22', 1, '2022-10-31 10:21:19', 64, NULL, NULL, 4);
INSERT INTO `source_detail` VALUES (232, 'MN Cầu Diễn', 1, '2022-11-01 17:48:20', 157, NULL, NULL, 2);
INSERT INTO `source_detail` VALUES (233, 'MG Bùi Thị Xuân_Linh PH', 1, '2022-11-03 17:23:05', 40, NULL, NULL, 5);
INSERT INTO `source_detail` VALUES (234, 'Nhung care 2021', 1, '2022-11-04 15:16:04', 64, NULL, NULL, 4);
INSERT INTO `source_detail` VALUES (235, 'MN Mẫu giáo số 2', 1, '2022-11-04 15:19:06', 1, NULL, NULL, NULL);
INSERT INTO `source_detail` VALUES (236, 'MN Mẹ Yêu Con', 1, '2022-11-04 15:34:49', 1, NULL, NULL, NULL);
INSERT INTO `source_detail` VALUES (237, 'Mẫu Giáo Số 2', 1, '2022-11-05 09:12:17', 21, NULL, NULL, 0);
INSERT INTO `source_detail` VALUES (238, 'MN Vòng Tay mẹ', 1, '2022-11-05 09:17:24', 21, NULL, NULL, 0);
INSERT INTO `source_detail` VALUES (239, 'TH - VŨ XUÂN THIỀU LB - TÙNG NVC', 1, '2022-11-05 14:55:45', 134, NULL, NULL, 9);
INSERT INTO `source_detail` VALUES (240, 'TH - GIA QUẤT LB - TÙNG NVC', 1, '2022-11-05 15:54:09', 134, NULL, NULL, 9);
INSERT INTO `source_detail` VALUES (241, 'TH - BỒ ĐỀ LB - TÙNG NVC', 1, '2022-11-05 16:08:54', 134, NULL, NULL, 9);
INSERT INTO `source_detail` VALUES (242, 'TH - VIỆT HƯNG LB - TÙNG NVC', 1, '2022-11-05 16:19:19', 134, NULL, NULL, 9);
INSERT INTO `source_detail` VALUES (243, 'Salehub MB DaoNgocANh', 1, '2022-11-07 14:48:55', 53, NULL, NULL, 0);
INSERT INTO `source_detail` VALUES (244, 'MG Bạch Mai_Linh PH', 1, '2022-11-08 17:24:30', 40, NULL, NULL, 5);
INSERT INTO `source_detail` VALUES (245, 'PTĐT - Mầm non Hoa Thủy Tinh LB', 1, '2022-11-08 17:38:50', 134, NULL, NULL, 9);
INSERT INTO `source_detail` VALUES (246, 'PTĐT-Mầm non Ánh Sao', 1, '2022-11-10 08:48:25', 1, NULL, NULL, NULL);
INSERT INTO `source_detail` VALUES (247, 'PTĐT-MN Đầm Trấu', 1, '2022-11-10 08:48:39', 1, NULL, NULL, NULL);
INSERT INTO `source_detail` VALUES (248, 'PTĐT-MN Lạc Trung', 1, '2022-11-10 08:48:51', 1, NULL, NULL, NULL);
INSERT INTO `source_detail` VALUES (249, 'MN GOLDEN WALL', 1, '2022-11-10 18:29:15', 220, NULL, NULL, 7);
INSERT INTO `source_detail` VALUES (250, 'MN Học Viện Mầm non Đa Trí Tuệ AMI_PTĐT', 1, '2022-11-11 09:42:15', 30, NULL, NULL, 0);
INSERT INTO `source_detail` VALUES (251, 'Amon Kindercare', 1, '2022-11-18 11:40:40', 144, NULL, NULL, 9);
INSERT INTO `source_detail` VALUES (252, 'Apollo MĐ', 1, '2022-11-18 15:48:24', 59, '2023-10-06 14:29:35', 417, 2);
INSERT INTO `source_detail` VALUES (253, 'FB các trung tâm kỹ năng HN', 1, '2022-11-18 18:18:07', 144, NULL, NULL, 9);
INSERT INTO `source_detail` VALUES (254, 'Hạ Long_TH Quang Trung', 1, '2022-11-19 09:46:03', 51, NULL, NULL, 4);
INSERT INTO `source_detail` VALUES (255, 'Nhung care 2020', 1, '2022-11-19 18:29:10', 64, NULL, NULL, 4);
INSERT INTO `source_detail` VALUES (256, 'MN PHÚ DIỄN A', 1, '2022-11-22 17:42:45', 220, '2022-11-22 17:43:00', 220, 7);
INSERT INTO `source_detail` VALUES (257, 'Data Mầm non 23.11', 1, '2022-11-23 10:09:10', 1, NULL, NULL, 9);
INSERT INTO `source_detail` VALUES (258, 'Tiểu học Phú Thượng_Hub', 1, '2022-11-23 17:05:53', 21, NULL, NULL, 0);
INSERT INTO `source_detail` VALUES (259, 'Thành đoàn Hà Nội', 1, '2022-11-24 10:41:33', 1, NULL, NULL, 9);
INSERT INTO `source_detail` VALUES (260, 'Nhung Care 2022', 1, '2022-11-24 11:56:53', 64, NULL, NULL, 4);
INSERT INTO `source_detail` VALUES (261, 'data up 25.11', 1, '2022-11-25 10:30:43', 1, NULL, NULL, 9);
INSERT INTO `source_detail` VALUES (262, 'Tiểu Học Phú Thượng- DaoNgocAnh', 1, '2022-11-25 11:56:41', 53, NULL, NULL, 7);
INSERT INTO `source_detail` VALUES (263, 'thành đoàn 25.11', 1, '2022-11-25 14:24:22', 1, NULL, NULL, 9);
INSERT INTO `source_detail` VALUES (264, 'Loan Care 2022', 1, '2022-11-25 19:05:23', 64, NULL, NULL, 4);
INSERT INTO `source_detail` VALUES (265, 'Loan care 2021', 1, '2022-11-25 19:05:32', 64, NULL, NULL, 4);
INSERT INTO `source_detail` VALUES (266, 'Loan care 2020', 1, '2022-11-25 19:06:42', 64, NULL, NULL, 4);
INSERT INTO `source_detail` VALUES (267, 'Thu MĐ', 1, '2022-11-27 14:35:19', 183, '2023-10-06 14:29:27', 417, 2);
INSERT INTO `source_detail` VALUES (268, 'data trường - Linh', 1, '2022-11-27 17:28:45', 1, NULL, NULL, 9);
INSERT INTO `source_detail` VALUES (269, 'TH Ngôi Sao', 1, '2022-11-28 14:44:18', 64, NULL, NULL, 4);
INSERT INTO `source_detail` VALUES (270, 'HA - TH Lê Văn Tám', 1, '2022-11-28 15:12:47', 64, NULL, NULL, 4);
INSERT INTO `source_detail` VALUES (271, 'PTDT - MN Hoa Sen _ Hồng Hapu', 1, '2022-11-29 11:06:20', 65, NULL, NULL, 4);
INSERT INTO `source_detail` VALUES (272, 'Trường 02.12', 1, '2022-12-02 09:18:40', 1, NULL, NULL, 9);
INSERT INTO `source_detail` VALUES (273, 'Tiểu học An Dương_Hub', 1, '2022-12-05 17:20:53', 30, NULL, NULL, 0);
INSERT INTO `source_detail` VALUES (274, 'TH An Dương_Hub', 0, '2022-12-05 18:32:48', 21, '2022-12-05 21:02:22', 30, 0);
INSERT INTO `source_detail` VALUES (275, 'Data up 7.12', 1, '2022-12-07 10:15:14', 1, NULL, NULL, 9);
INSERT INTO `source_detail` VALUES (276, 'c1 long biên 8.12', 1, '2022-12-08 17:26:35', 1, NULL, NULL, 9);
INSERT INTO `source_detail` VALUES (277, 'chu văn an', 1, '2022-12-08 18:08:08', 1, NULL, NULL, 9);
INSERT INTO `source_detail` VALUES (278, 'Mầm non Hà My và COzy housse', 1, '2022-12-09 16:27:58', 1, NULL, NULL, 9);
INSERT INTO `source_detail` VALUES (279, 'C1 Tam Khương_Linh Talent', 1, '2022-12-09 16:33:02', 21, NULL, NULL, 0);
INSERT INTO `source_detail` VALUES (280, 'C1 Quan Hoa Khối 1_Linh Talent', 1, '2022-12-09 16:33:24', 21, NULL, NULL, 0);
INSERT INTO `source_detail` VALUES (281, 'Tiểu học 9.12', 1, '2022-12-09 17:35:34', 1, NULL, NULL, 9);
INSERT INTO `source_detail` VALUES (282, 'TH Ai Mo B va Thượng Thanh_Linh Talent', 1, '2022-12-12 11:35:19', 21, NULL, NULL, 0);
INSERT INTO `source_detail` VALUES (283, 'MỸ ĐÌNH I _Linh Talent', 1, '2022-12-12 12:00:22', 21, NULL, NULL, 0);
INSERT INTO `source_detail` VALUES (284, 'Data-PHHS Các Trường Tiểu Học Q Long Biên_Linh Talent', 1, '2022-12-12 12:49:06', 21, NULL, NULL, 0);
INSERT INTO `source_detail` VALUES (285, 'C1 Tam Khương_Linh Talent', 1, '2022-12-12 12:50:15', 21, NULL, NULL, 0);
INSERT INTO `source_detail` VALUES (286, 'C1 Quan Hoa_Linh Talent', 1, '2022-12-12 12:51:58', 21, NULL, NULL, 0);
INSERT INTO `source_detail` VALUES (287, 'C1 Quan Hoa(Khối 1)_Linh Talent', 1, '2022-12-12 12:53:16', 21, NULL, NULL, 0);
INSERT INTO `source_detail` VALUES (288, 'C1 Quan Hoa Khối 1_Linh Talent', 1, '2022-12-12 13:09:31', 21, NULL, NULL, 0);
INSERT INTO `source_detail` VALUES (289, 'C1 Phương Canh(1692)_Linh Talent', 1, '2022-12-12 13:10:36', 21, NULL, NULL, 0);
INSERT INTO `source_detail` VALUES (290, 'C1 Long Biên_Linh Talent', 1, '2022-12-12 13:11:45', 21, NULL, NULL, 0);
INSERT INTO `source_detail` VALUES (291, 'C1 Lomonoxop_Linh Talent', 1, '2022-12-12 13:12:41', 21, NULL, NULL, 0);
INSERT INTO `source_detail` VALUES (292, 'C1 Lomonoxop(Khối 1)_Linh Talent', 1, '2022-12-12 13:13:36', 21, NULL, NULL, 0);
INSERT INTO `source_detail` VALUES (293, 'C1 Gia Thụy_Linh Talent', 1, '2022-12-12 13:14:49', 21, NULL, NULL, 0);
INSERT INTO `source_detail` VALUES (294, 'C1 Đức Giang_Linh Talent', 1, '2022-12-12 13:16:12', 21, NULL, NULL, 0);
INSERT INTO `source_detail` VALUES (295, 'C1 Dịch Vọng B Khối 1_Linh Talent', 1, '2022-12-12 13:16:58', 21, NULL, NULL, 0);
INSERT INTO `source_detail` VALUES (296, 'C1 Canh Phương Khối 1_Linh Talent', 1, '2022-12-12 13:17:47', 21, NULL, NULL, 0);
INSERT INTO `source_detail` VALUES (297, 'C1 Bồ Đề_Linh Talent', 1, '2022-12-12 13:18:48', 21, NULL, NULL, 0);
INSERT INTO `source_detail` VALUES (298, '8000KHconhoctienganh_Linh Talent', 1, '2022-12-12 13:19:46', 21, NULL, NULL, 0);
INSERT INTO `source_detail` VALUES (299, 'MỸ ĐÌNH I_Linh Talent', 1, '2022-12-12 20:19:13', 21, NULL, NULL, 0);
INSERT INTO `source_detail` VALUES (300, 'Thành Đoàn hà nội 14/12', 1, '2022-12-14 11:12:11', 277, NULL, NULL, 9);
INSERT INTO `source_detail` VALUES (301, 'Tiểu học Ngọc thuỵ', 1, '2022-12-22 14:34:42', 277, NULL, NULL, 9);
INSERT INTO `source_detail` VALUES (302, 'Tiểu học Bồ Đề', 1, '2022-12-22 14:45:36', 277, NULL, NULL, 9);
INSERT INTO `source_detail` VALUES (303, 'C1 Minh Khai', 1, '2022-12-22 14:46:00', 277, NULL, NULL, 9);
INSERT INTO `source_detail` VALUES (304, 'DGS CHI PVĐ', 1, '2022-12-22 18:00:41', 134, NULL, NULL, 9);
INSERT INTO `source_detail` VALUES (305, 'Ngọc times', 1, '2022-12-23 10:59:55', 64, NULL, NULL, 4);
INSERT INTO `source_detail` VALUES (306, 'Data lạnh', 1, '2022-12-26 10:37:25', 64, NULL, NULL, 4);
INSERT INTO `source_detail` VALUES (307, 'Data 29/12', 1, '2022-12-29 15:56:23', 277, NULL, NULL, 9);
INSERT INTO `source_detail` VALUES (308, 'Confirm 2022', 1, '2022-12-30 10:12:06', 64, NULL, NULL, 4);
INSERT INTO `source_detail` VALUES (309, 'TH Nam Trung Yên', 1, '2022-12-31 11:29:20', 64, NULL, NULL, 4);
INSERT INTO `source_detail` VALUES (310, 'Ames English', 1, '2022-12-31 11:29:32', 64, NULL, NULL, 4);
INSERT INTO `source_detail` VALUES (311, 'data 5/1', 1, '2023-01-05 09:24:02', 277, NULL, NULL, 9);
INSERT INTO `source_detail` VALUES (312, 'HVT - Tân Mai', 1, '2023-01-05 14:11:38', 64, NULL, NULL, 4);
INSERT INTO `source_detail` VALUES (313, 'Zung MD', 1, '2023-01-06 10:13:15', 59, '2023-10-06 14:29:56', 417, 2);
INSERT INTO `source_detail` VALUES (314, 'TH Văn Chương - Đống Đa', 1, '2023-01-11 17:07:00', 144, NULL, NULL, 9);
INSERT INTO `source_detail` VALUES (315, 'MN Hoa Sen - Hà Đông', 1, '2023-01-11 17:28:19', 144, NULL, NULL, 9);
INSERT INTO `source_detail` VALUES (316, 'DataTT_THTân Định', 1, '2023-01-12 10:26:43', 51, NULL, NULL, 4);
INSERT INTO `source_detail` VALUES (317, 'TH Mai Động', 1, '2023-01-12 10:40:28', 64, NULL, NULL, 4);
INSERT INTO `source_detail` VALUES (318, 'Tiểu học An Hòa - Cầu Giấy', 1, '2023-01-12 11:31:47', 144, NULL, NULL, 9);
INSERT INTO `source_detail` VALUES (319, 'Tiểu Học Nhật Tân - Thụy Khuê', 1, '2023-01-12 11:32:06', 144, NULL, NULL, 9);
INSERT INTO `source_detail` VALUES (320, 'data 1.2', 1, '2023-02-01 10:42:40', 277, NULL, NULL, 9);
INSERT INTO `source_detail` VALUES (321, 'Loan care 2023', 1, '2023-02-05 09:50:22', 64, NULL, NULL, 4);
INSERT INTO `source_detail` VALUES (322, 'Thiếu niên siêu đăng', 1, '2023-02-08 14:21:32', 144, NULL, NULL, 9);
INSERT INTO `source_detail` VALUES (323, 'Scots Enghish NVC', 1, '2023-02-21 19:22:15', 144, NULL, NULL, 9);
INSERT INTO `source_detail` VALUES (324, 'MN Happy kid_Linh PH', 1, '2023-02-21 20:10:07', 40, NULL, NULL, 5);
INSERT INTO `source_detail` VALUES (325, 'MN Rosemont American International school_Linh PH', 1, '2023-02-22 18:06:48', 40, NULL, NULL, 5);
INSERT INTO `source_detail` VALUES (326, '25.2', 1, '2023-02-25 11:37:04', 277, NULL, NULL, 9);
INSERT INTO `source_detail` VALUES (327, 'Data du lịch Ms Trang NVC', 1, '2023-03-09 16:00:34', 144, NULL, NULL, 9);
INSERT INTO `source_detail` VALUES (328, '10.3', 1, '2023-03-10 11:47:34', 277, NULL, NULL, 5);
INSERT INTO `source_detail` VALUES (329, 'Ecopark', 1, '2023-03-17 17:11:22', 144, NULL, NULL, 9);
INSERT INTO `source_detail` VALUES (330, 'Thành Đoàn_Linh Trần', 1, '2023-03-19 09:35:55', 277, NULL, NULL, 5);
INSERT INTO `source_detail` VALUES (331, 'FB Tiền tiểu học', 1, '2023-03-25 17:14:34', 144, NULL, NULL, 9);
INSERT INTO `source_detail` VALUES (332, 'PVĐ-AB-2023', 1, '2023-04-02 11:45:50', 285, NULL, NULL, 7);
INSERT INTO `source_detail` VALUES (333, 'TH-THANH', 1, '2023-04-04 15:23:40', 285, '2023-04-04 15:24:09', 285, 7);
INSERT INTO `source_detail` VALUES (334, 'Tiểu học Nghĩa Tân_Hub', 1, '2023-04-07 14:07:27', 21, NULL, NULL, 0);
INSERT INTO `source_detail` VALUES (335, 'Data Long Biên Ms Trang', 1, '2023-04-08 14:36:18', 144, NULL, NULL, 9);
INSERT INTO `source_detail` VALUES (336, 'C1_Dịch vọng B', 1, '2023-04-10 14:08:54', 21, NULL, NULL, 0);
INSERT INTO `source_detail` VALUES (337, 'Apax Hoàng Quốc Việt_1', 1, '2023-04-10 15:47:41', 21, NULL, NULL, 0);
INSERT INTO `source_detail` VALUES (338, 'Apax Hoàng Quốc Việt_2', 1, '2023-04-10 15:47:54', 21, NULL, NULL, 0);
INSERT INTO `source_detail` VALUES (339, 'Apollo Mỹ Đình', 1, '2023-04-10 15:48:05', 21, NULL, NULL, 0);
INSERT INTO `source_detail` VALUES (340, 'Apollo Phạm Tuấn Tài', 1, '2023-04-10 15:48:13', 21, NULL, NULL, 0);
INSERT INTO `source_detail` VALUES (341, 'C1 Mai Dịch (1,2,3)', 1, '2023-04-10 15:48:28', 21, NULL, NULL, 0);
INSERT INTO `source_detail` VALUES (342, 'C1 Phương Canh (Khối 1,2,3)', 1, '2023-04-10 15:48:46', 21, NULL, NULL, 0);
INSERT INTO `source_detail` VALUES (343, 'C1_Dịch Vọng B', 1, '2023-04-10 15:49:00', 21, NULL, NULL, 0);
INSERT INTO `source_detail` VALUES (344, 'Cleverlearn Mỹ Đình', 1, '2023-04-10 15:49:12', 21, NULL, NULL, 0);
INSERT INTO `source_detail` VALUES (345, 'Danh Sách Data Vinhome', 1, '2023-04-10 15:49:21', 21, NULL, NULL, 0);
INSERT INTO `source_detail` VALUES (346, 'Data 219 Trung Kính', 1, '2023-04-10 15:49:33', 21, NULL, NULL, 0);
INSERT INTO `source_detail` VALUES (347, 'Data dân cư các tòa nhà khu Mỹ Đình', 1, '2023-04-10 15:49:50', 21, NULL, NULL, 0);
INSERT INTO `source_detail` VALUES (348, 'Data Hapulico', 1, '2023-04-10 15:49:58', 21, NULL, NULL, 0);
INSERT INTO `source_detail` VALUES (349, 'Data Tiểu Học An Hòa', 1, '2023-04-10 15:50:10', 21, NULL, NULL, 0);
INSERT INTO `source_detail` VALUES (350, 'Data Tiểu Học Quan Hoa', 1, '2023-04-10 15:50:20', 21, NULL, NULL, 0);
INSERT INTO `source_detail` VALUES (351, 'MN Mapple Bear', 1, '2023-04-10 15:50:33', 21, NULL, NULL, 0);
INSERT INTO `source_detail` VALUES (352, 'MN Sakura Dịch Vọng', 1, '2023-04-10 15:50:44', 21, NULL, NULL, 0);
INSERT INTO `source_detail` VALUES (353, 'MN Vườn Ươm Hạnh Phúc', 1, '2023-04-10 15:50:55', 21, NULL, NULL, 0);
INSERT INTO `source_detail` VALUES (354, 'MN Yên Hòa', 1, '2023-04-10 15:51:05', 21, NULL, NULL, 0);
INSERT INTO `source_detail` VALUES (355, 'TH Nguyễn Khả Trạc', 1, '2023-04-10 15:51:17', 21, NULL, NULL, 0);
INSERT INTO `source_detail` VALUES (356, 'Tiểu Học Lý Thái Tổ', 1, '2023-04-10 15:51:27', 21, NULL, NULL, 0);
INSERT INTO `source_detail` VALUES (357, 'Tiểu Học Nam Trung Yên', 1, '2023-04-10 15:51:38', 21, NULL, NULL, 0);
INSERT INTO `source_detail` VALUES (358, 'Tiểu Học Phan Đình Giót', 1, '2023-04-10 15:51:50', 21, NULL, NULL, 0);
INSERT INTO `source_detail` VALUES (359, 'Tiểu Học Trung Hòa', 1, '2023-04-10 15:51:58', 21, NULL, NULL, 0);
INSERT INTO `source_detail` VALUES (360, 'Tiểu Học Yên Hòa', 1, '2023-04-10 15:52:07', 21, NULL, NULL, 0);
INSERT INTO `source_detail` VALUES (361, 'C1_Dịch Vọng B', 1, '2023-04-10 15:54:53', 21, NULL, NULL, 0);
INSERT INTO `source_detail` VALUES (362, 'MN Nguyễn Khả Trạc', 1, '2023-04-10 15:59:58', 21, NULL, NULL, 0);
INSERT INTO `source_detail` VALUES (363, 'T4-2023', 1, '2023-04-13 10:08:59', 285, NULL, NULL, 7);
INSERT INTO `source_detail` VALUES (364, 'Data Apax TK và XD_ Linh Trần', 1, '2023-04-13 11:38:20', 277, NULL, NULL, 6);
INSERT INTO `source_detail` VALUES (365, 'MN gần Tây Hồ - Linh Trần', 1, '2023-04-14 11:51:59', 277, '2023-07-25 14:02:12', 418, 6);
INSERT INTO `source_detail` VALUES (366, 'Tiểu học _ Linh Trần', 1, '2023-04-14 12:03:28', 277, NULL, NULL, 6);
INSERT INTO `source_detail` VALUES (367, 'Data apax MĐ_PVĐ', 1, '2023-04-14 17:06:01', 50, NULL, NULL, 7);
INSERT INTO `source_detail` VALUES (368, 'Times 2023', 1, '2023-04-16 15:39:26', 64, NULL, NULL, 4);
INSERT INTO `source_detail` VALUES (369, 'Thống nhất_data Hoa', 1, '2023-04-20 13:42:32', 21, NULL, NULL, 0);
INSERT INTO `source_detail` VALUES (370, 'Tiểu học Hồ Tùng Mậu_PVĐ', 1, '2023-04-20 14:28:03', 50, NULL, NULL, 7);
INSERT INTO `source_detail` VALUES (371, 'Data cổ nhuế 2A 2B_PVĐ', 1, '2023-04-20 16:59:26', 50, NULL, NULL, 7);
INSERT INTO `source_detail` VALUES (372, 'Friend FB NVC', 1, '2023-04-27 11:06:43', 144, NULL, NULL, 9);
INSERT INTO `source_detail` VALUES (373, 'Trung tâm tiếng anh The Giralle LB', 1, '2023-05-19 15:22:52', 144, NULL, NULL, 9);
INSERT INTO `source_detail` VALUES (374, 'Nhung care 2023', 1, '2023-06-13 15:40:16', 64, '2023-06-13 15:40:30', 64, 4);
INSERT INTO `source_detail` VALUES (375, 'Hội thảo TCT CMS', 1, '2023-06-17 15:05:18', 40, NULL, NULL, 5);
INSERT INTO `source_detail` VALUES (376, 'Up mới 21/6/23', 1, '2023-06-21 15:05:17', 64, NULL, NULL, 4);
INSERT INTO `source_detail` VALUES (377, 'Tam Trinh_HN', 1, '2023-06-23 11:05:13', 39, NULL, NULL, 5);
INSERT INTO `source_detail` VALUES (378, 'DATA A NAM NVC', 1, '2023-06-27 09:59:02', 144, NULL, NULL, 9);
INSERT INTO `source_detail` VALUES (379, 'KH SCB HOÀN KIẾM_LINH PH', 1, '2023-07-13 14:17:04', 40, NULL, NULL, 5);
INSERT INTO `source_detail` VALUES (380, 'Nhiều tiền các khu vực_Linh PH', 1, '2023-07-13 14:34:37', 40, NULL, NULL, 5);
INSERT INTO `source_detail` VALUES (381, 'data Thúy', 1, '2023-07-15 12:33:07', 21, NULL, NULL, 1);
INSERT INTO `source_detail` VALUES (382, 'Jolo English_Linh Phố Huế', 1, '2023-07-18 18:46:06', 40, NULL, NULL, 5);
INSERT INTO `source_detail` VALUES (383, 'TH Vĩnh tuy', 1, '2023-07-21 15:18:46', 64, NULL, NULL, 4);
INSERT INTO `source_detail` VALUES (384, 'Kinh doanh_tháng 7.2023', 1, '2023-07-25 09:30:53', 51, NULL, NULL, 4);
INSERT INTO `source_detail` VALUES (385, 'Kinh Doanh T7.2023', 1, '2023-07-25 13:54:07', 418, NULL, NULL, 6);
INSERT INTO `source_detail` VALUES (386, 'DT', 0, '2023-07-25 14:01:39', 418, '2023-07-25 14:16:09', 418, 6);
INSERT INTO `source_detail` VALUES (387, 'DT', 0, '2023-07-25 14:02:33', 418, '2023-07-25 14:16:32', 418, 6);
INSERT INTO `source_detail` VALUES (388, 'DT', 0, '2023-07-25 14:02:53', 418, '2023-07-25 14:15:20', 418, 6);
INSERT INTO `source_detail` VALUES (389, 'DT', 0, '2023-07-25 14:04:20', 418, '2023-07-25 14:15:35', 418, 6);
INSERT INTO `source_detail` VALUES (390, 'Nguồn VIP MN all _ Linh Phố Huế', 1, '2023-07-25 14:27:07', 40, NULL, NULL, 5);
INSERT INTO `source_detail` VALUES (391, 'Kinh doanh_7.2023 Phố Huế', 1, '2023-07-25 15:18:35', 40, NULL, NULL, 5);
INSERT INTO `source_detail` VALUES (392, 'Kinh doanh_7.2023', 1, '2023-07-25 15:38:51', 21, NULL, NULL, 1);
INSERT INTO `source_detail` VALUES (393, 'Trường TH & MN', 1, '2023-07-25 16:27:33', 39, NULL, NULL, 4);
INSERT INTO `source_detail` VALUES (394, 'Nga_hub', 1, '2023-07-27 09:34:56', 21, NULL, NULL, 1);
INSERT INTO `source_detail` VALUES (395, 'Kinh doanh_7.2023', 1, '2023-07-29 10:56:26', 446, '2023-07-29 10:57:08', 431, 7);
INSERT INTO `source_detail` VALUES (396, 'Nhung care 2023', 1, '2023-08-03 11:02:31', 64, NULL, NULL, 1);
INSERT INTO `source_detail` VALUES (397, 'Loan care 2020', 1, '2023-08-03 11:24:09', 64, NULL, NULL, 1);
INSERT INTO `source_detail` VALUES (398, 'MN Lạc Trung _Linh PH', 1, '2023-08-03 18:43:41', 40, NULL, NULL, 5);
INSERT INTO `source_detail` VALUES (399, 'Thanh xuân_Nga', 1, '2023-08-04 15:59:32', 21, NULL, NULL, 1);
INSERT INTO `source_detail` VALUES (400, 'MN Hoa Phượng_ Linh Phố Huế', 1, '2023-08-04 20:59:16', 40, NULL, NULL, 5);
INSERT INTO `source_detail` VALUES (401, 'Vin_Hồng', 1, '2023-08-06 15:14:26', 21, NULL, NULL, 1);
INSERT INTO `source_detail` VALUES (402, 'Tiểu học_MB bank', 1, '2023-08-09 10:16:48', 21, NULL, NULL, 1);
INSERT INTO `source_detail` VALUES (403, 'Nhung update 17/8', 1, '2023-08-17 16:00:52', 64, NULL, NULL, 1);
INSERT INTO `source_detail` VALUES (404, 'Nhung care 2022', 1, '2023-08-18 15:58:32', 64, NULL, NULL, 1);
INSERT INTO `source_detail` VALUES (405, 'Nhung care 2021', 1, '2023-08-18 16:02:22', 64, NULL, NULL, 1);
INSERT INTO `source_detail` VALUES (406, 'MN Thanh Lương_Linh Phố Huế', 1, '2023-08-20 14:52:00', 40, NULL, NULL, 5);
INSERT INTO `source_detail` VALUES (407, 'MN Trung Tự_Linh Phố Huế', 1, '2023-08-22 16:25:26', 40, NULL, NULL, 5);
INSERT INTO `source_detail` VALUES (408, 'LH_5/6/7', 1, '2023-08-23 11:16:55', 21, '2023-09-15 14:18:56', 64, 1);
INSERT INTO `source_detail` VALUES (409, 'HL-ĐHA-Ghi đè', 1, '2023-08-28 08:50:56', 1, NULL, NULL, NULL);
INSERT INTO `source_detail` VALUES (410, 'Lê Thị Việt Kiều', 1, '2023-09-06 11:22:50', 412, NULL, NULL, 13);
INSERT INTO `source_detail` VALUES (411, 'CH Kiều _ Lớp 3', 1, '2023-09-10 11:44:52', 412, NULL, NULL, 13);
INSERT INTO `source_detail` VALUES (412, 'TH Đặng Trần Côn', 1, '2023-09-16 10:54:04', 64, NULL, NULL, 1);
INSERT INTO `source_detail` VALUES (413, 'TH Phan Đình Giót', 1, '2023-09-16 10:54:16', 64, NULL, NULL, 1);
INSERT INTO `source_detail` VALUES (414, 'TH TXT', 1, '2023-09-21 16:40:40', 65, NULL, NULL, 1);
INSERT INTO `source_detail` VALUES (415, 'TH TXB', 1, '2023-09-21 16:41:01', 65, NULL, NULL, 1);
INSERT INTO `source_detail` VALUES (416, 'BC_Linh PH', 1, '2023-10-13 20:05:14', 40, NULL, NULL, 5);
INSERT INTO `source_detail` VALUES (417, 'Thanh Huyền PVĐ', 1, '2023-10-19 10:45:06', 507, NULL, NULL, 7);
INSERT INTO `source_detail` VALUES (418, 'T Huyền PVĐ Ninh Hiệp', 1, '2023-10-19 10:45:35', 507, NULL, NULL, 7);
INSERT INTO `source_detail` VALUES (419, 'Thanh Huyền PVĐ SteamE Hào Nam', 1, '2023-10-19 11:33:16', 507, NULL, NULL, 7);
INSERT INTO `source_detail` VALUES (420, 'MN Tuổi Thơ_Ngõ Gạch_Linh PH', 1, '2023-10-25 17:42:55', 40, NULL, NULL, 5);
INSERT INTO `source_detail` VALUES (421, 'Huyền Anh', 1, '2023-10-26 09:31:09', 51, NULL, NULL, 1);
INSERT INTO `source_detail` VALUES (422, 'Direct_Linh PH', 1, '2023-10-26 17:49:46', 40, NULL, NULL, 5);
INSERT INTO `source_detail` VALUES (423, 'Phụ Huynh Apax HCM _ IT', 1, '2023-10-27 10:13:57', 503, NULL, NULL, NULL);
INSERT INTO `source_detail` VALUES (424, 'TH Cổ Nhuế 2A- Huyền', 1, '2023-10-28 09:45:04', 507, NULL, NULL, 7);
INSERT INTO `source_detail` VALUES (425, 'Thanh Huyền PVĐ TH Hoàng Diệu', 1, '2023-10-28 10:27:27', 507, NULL, NULL, 7);
INSERT INTO `source_detail` VALUES (426, 'GDKD_11.2023', 1, '2023-11-03 09:57:42', 51, NULL, NULL, 1);
INSERT INTO `source_detail` VALUES (427, 'Nhiều nguồn', 1, '2023-11-15 20:21:35', 40, NULL, NULL, 5);
INSERT INTO `source_detail` VALUES (428, 'Chứng khoán - Dung PVĐ', 1, '2023-11-16 19:28:12', 507, NULL, NULL, 7);
INSERT INTO `source_detail` VALUES (429, 'Thoa PVĐ', 1, '2023-11-18 17:45:50', 507, NULL, NULL, 7);
INSERT INTO `source_detail` VALUES (430, 'MN Hoa Hồng _Ngõ Quỳnh', 1, '2023-11-22 17:24:18', 40, NULL, NULL, 5);
INSERT INTO `source_detail` VALUES (431, 'Thanh Huyền PVĐ - Apax HQV', 1, '2023-11-22 18:04:14', 507, NULL, NULL, 7);
INSERT INTO `source_detail` VALUES (432, 'Thanh Huyền PVĐ - An Bình city', 1, '2023-11-26 11:34:33', 507, NULL, NULL, 7);
INSERT INTO `source_detail` VALUES (433, 'Thanh Huyền PVĐ- Trường học new', 1, '2023-11-29 14:31:06', 507, NULL, NULL, 7);
INSERT INTO `source_detail` VALUES (434, 'test', 0, '2023-12-07 14:36:33', 541, '2023-12-07 14:36:53', 541, NULL);
INSERT INTO `source_detail` VALUES (435, 'HO_CTV Hoàng Thu Trang', 1, '2023-12-12 09:21:36', 503, NULL, NULL, NULL);
INSERT INTO `source_detail` VALUES (436, 'Mn Sasuke', 1, '2023-12-16 17:47:41', 65, NULL, NULL, 1);
INSERT INTO `source_detail` VALUES (437, 'Nhà sách Tân Việt', 1, '2023-12-19 14:12:45', 65, NULL, NULL, 1);
INSERT INTO `source_detail` VALUES (438, 'KH Vinmec_Hong', 1, '2023-12-20 13:58:56', 65, NULL, NULL, 1);
INSERT INTO `source_detail` VALUES (439, 'MN Sakura kids NĐC_Linh PH', 1, '2023-12-20 16:17:02', 40, '2023-12-21 09:18:49', 541, 5);
INSERT INTO `source_detail` VALUES (440, 'Hapulico_DT Scots', 1, '2023-12-24 10:32:34', 503, '2024-01-02 17:18:03', 503, 1);
INSERT INTO `source_detail` VALUES (442, 'HLONG_Hoptac_MN Sakura', 1, '2024-01-02 17:16:20', 503, '2024-01-10 11:00:48', 503, 10);
INSERT INTO `source_detail` VALUES (443, 'HLONG_Private_Data BigBen', 1, '2024-01-02 17:16:52', 503, '2024-01-10 11:00:39', 503, 10);
INSERT INTO `source_detail` VALUES (444, 'HLONG_Private_Data Steame', 1, '2024-01-02 17:17:18', 503, '2024-01-10 11:00:23', 503, 10);
INSERT INTO `source_detail` VALUES (445, 'TKHUE_Hoptac_Steame_XuanDieu', 1, '2024-01-04 11:11:49', 503, '2024-01-10 11:00:13', 503, 6);
INSERT INTO `source_detail` VALUES (446, 'TKHUE_Hoptac_Steame_Kosmo', 1, '2024-01-04 11:12:36', 503, '2024-01-10 11:00:05', 503, 6);
INSERT INTO `source_detail` VALUES (447, 'TKHUE_Hoptac_MauGiaoSo2_PDC', 1, '2024-01-04 11:14:32', 503, '2024-01-10 10:59:50', 503, 6);
INSERT INTO `source_detail` VALUES (448, 'PHUE_Hoptac_MN Lê Đại Hành', 0, '2024-01-10 10:58:10', 503, '2024-01-10 13:06:09', 503, 5);
INSERT INTO `source_detail` VALUES (449, 'KLIEN_HopTac_TH Quang Trung', 1, '2024-01-10 10:58:40', 503, NULL, NULL, 8);
INSERT INTO `source_detail` VALUES (450, 'PVDONG_HopTac_MN Cổ Nhuế 2A', 1, '2024-01-10 10:59:11', 503, NULL, NULL, 7);
INSERT INTO `source_detail` VALUES (451, 'KLIEN_HopTac_TH Phương Liên', 1, '2024-01-10 10:59:36', 503, NULL, NULL, 8);
INSERT INTO `source_detail` VALUES (452, 'PHUE_HopTac_MN Bách Khoa', 1, '2024-01-10 11:01:05', 503, NULL, NULL, 5);
INSERT INTO `source_detail` VALUES (453, 'PHUE_HopTac_MN 8.3', 1, '2024-01-10 11:01:24', 503, NULL, NULL, 5);
INSERT INTO `source_detail` VALUES (454, 'TKHUE_HopTac_MN Mini House', 1, '2024-01-10 11:01:46', 503, NULL, NULL, 6);
INSERT INTO `source_detail` VALUES (455, 'HAPU_HopTac_MN Thanh Xuân Nam', 1, '2024-01-10 11:02:09', 503, '2024-01-10 11:02:18', 503, 1);
INSERT INTO `source_detail` VALUES (456, 'TIME_HopTac_TH Nguyễn Đình Chiểu', 1, '2024-01-10 11:02:43', 503, NULL, NULL, 4);
INSERT INTO `source_detail` VALUES (457, 'MDINH_HopTac_MN Mỹ Đình 1', 1, '2024-01-10 11:03:04', 503, '2024-04-08 17:03:10', 541, 2);
INSERT INTO `source_detail` VALUES (458, 'NVCU_HopTac_MN Chim Én', 1, '2024-01-10 11:03:21', 503, NULL, NULL, 9);
INSERT INTO `source_detail` VALUES (459, 'KLIEN_HopTac_MN Trung Tự', 1, '2024-02-01 14:22:53', 503, NULL, NULL, 8);
INSERT INTO `source_detail` VALUES (460, 'PHUE_HopTac_Mẫu giáo Bách Khoa', 1, '2024-02-22 18:48:59', 503, '2024-02-22 18:50:34', 503, 5);
COMMIT;

-- ----------------------------
-- Table structure for sources
-- ----------------------------
DROP TABLE IF EXISTS `sources`;
CREATE TABLE `sources` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(2) NOT NULL DEFAULT '0' COMMENT '0 - inactive, 1 - active',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=47 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of sources
-- ----------------------------
BEGIN;
INSERT INTO `sources` VALUES (24, 'Coldbase', 'Data trung tâm mua về', 1, NULL, '2018-03-22 04:40:07');
INSERT INTO `sources` VALUES (25, 'Walkin', 'KH trực tiếp đến trung tâm', 1, NULL, '2018-03-22 04:40:07');
INSERT INTO `sources` VALUES (26, 'Marketing', 'Marketing', 1, NULL, '2018-03-22 04:40:07');
INSERT INTO `sources` VALUES (27, 'Sale Hub', 'Sale Hub MB', 1, NULL, '2018-03-22 04:40:07');
INSERT INTO `sources` VALUES (28, 'CTV', 'CTV', 1, NULL, '2018-03-22 04:40:07');
INSERT INTO `sources` VALUES (29, 'Giới thiệu', 'Giới thiệu', 1, NULL, '2018-03-22 04:40:07');
INSERT INTO `sources` VALUES (30, 'Khác', 'Khác', 1, NULL, '2018-03-22 04:40:07');
COMMIT;

-- ----------------------------
-- Table structure for student_logs
-- ----------------------------
DROP TABLE IF EXISTS `student_logs`;
CREATE TABLE `student_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) DEFAULT NULL,
  `content` text COLLATE utf8_unicode_ci,
  `created_at` datetime DEFAULT NULL,
  `creator_id` int(11) DEFAULT NULL,
  `status` tinyint(2) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `student_id` (`student_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of student_logs
-- ----------------------------
BEGIN;
INSERT INTO `student_logs` VALUES (1, 1, 'Chuyển lên danh sách học sinh chính thức', '2024-05-12 20:58:19', 1, 1);
INSERT INTO `student_logs` VALUES (2, 1, 'Thêm mới hợp đồng nhập học - C000001', '2024-05-12 21:03:41', 1, 1);
INSERT INTO `student_logs` VALUES (3, 1, 'Thêm mới hợp đồng nhập học - C000002', '2024-05-12 21:55:13', 1, 1);
INSERT INTO `student_logs` VALUES (4, 1, 'Xếp vào lớp FasTracKids lớp học thử', '2024-05-12 22:26:11', 1, 1);
COMMIT;

-- ----------------------------
-- Table structure for student_waitting_process
-- ----------------------------
DROP TABLE IF EXISTS `student_waitting_process`;
CREATE TABLE `student_waitting_process` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) DEFAULT NULL,
  `data_id` int(11) DEFAULT NULL,
  `type` tinyint(2) DEFAULT NULL COMMENT '1 bảo lưu, 2 chuyển lớp, 3 chuyển trung  tâm, 4 chuyển phí',
  `status` tinyint(2) DEFAULT NULL COMMENT '0 hủy, 1 chờ xử lý, 2 đã xử lý',
  `created_at` datetime DEFAULT NULL,
  `processed_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updator_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `student_id` (`student_id`) USING BTREE,
  KEY `data_id` (`data_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

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
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Mã ID của học sinh',
  `lms_code` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Mã CRM: ''CRM''+id',
  `name` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Họ tên học sinh viết in hoa',
  `firstname` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `midname` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lastname` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nick` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Nick name trong lớp của học sinh',
  `gender` char(1) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Giới tính học sinh: M - nam, F - nữ',
  `type` tinyint(2) DEFAULT '0' COMMENT '0 - Bình thường, 1 - VIP',
  `date_of_birth` date DEFAULT NULL COMMENT 'định dạng: YYYY-mm-dd',
  `gud_mobile1` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Số di động của phụ huynh 1 (bố)',
  `gud_name1` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Tên phụ huynh 1 (bố)',
  `gud_email1` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Email phụ huynh 1 (bố)',
  `gud_mobile2` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Số di động của phụ huynh 2 (mẹ)',
  `gud_name2` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Tên phụ huynh 2 (mẹ)',
  `gud_email2` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Email phụ huynh 2 (mẹ)',
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Địa chỉ thường trú',
  `province_id` int(11) DEFAULT NULL COMMENT 'ID Tỉnh / Thành phố',
  `district_id` int(11) DEFAULT NULL COMMENT 'ID Quận / Huyện',
  `school` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Trường học',
  `school_level` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `school_grade` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Mã School Grade (xem API 16 LMS)',
  `created_at` datetime DEFAULT NULL COMMENT 'định dạng: YYYY-mm-dd H:m:i',
  `creator_id` int(11) DEFAULT NULL COMMENT 'User ID người tạo học sinh',
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP COMMENT 'định dạng: YYYY-mm-dd H:m:i',
  `updator_id` int(11) DEFAULT NULL COMMENT 'User ID người cập nhật thông tin hồ sơ học sinh',
  `note` text COLLATE utf8mb4_unicode_ci COMMENT 'Các ghi chú khác',
  `avatar` text COLLATE utf8mb4_unicode_ci COMMENT 'Nguồn thu thập dữ liệu học sinh',
  `branch_id` int(11) DEFAULT NULL COMMENT 'Mã trung tâm',
  `attached_file` varchar(255) CHARACTER SET utf8mb4 DEFAULT NULL COMMENT 'Tệp tin bổ sung đính kèm',
  `gud_card1` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Số CCCD của phụ huynh 1 (bố)',
  `gud_card2` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Số CCCD của phụ huynh 2 (mẹ)',
  `gud_birth_day1` date DEFAULT NULL COMMENT 'Ngày sinh nhật của phụ huynh 1 (bố)',
  `gud_birth_day2` date DEFAULT NULL COMMENT 'Ngày sinh nhật của phụ huynh 1 (mẹ)',
  `gud_gender1` char(2) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gud_gender2` char(2) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gud_job1` int(11) DEFAULT NULL,
  `gud_job2` int(11) DEFAULT NULL,
  `status` tinyint(1) unsigned zerofill DEFAULT '1',
  `source_detail_id` int(11) DEFAULT NULL,
  `source_id` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `index_student` (`lms_code`) USING BTREE,
  KEY `province_id` (`province_id`) USING BTREE,
  KEY `district_id` (`district_id`) USING BTREE,
  KEY `gud_mobile1` (`gud_mobile1`) USING BTREE,
  KEY `gud_mobile2` (`gud_mobile2`) USING BTREE,
  KEY `branch_id` (`branch_id`) USING BTREE,
  KEY `status` (`status`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of students
-- ----------------------------
BEGIN;
INSERT INTO `students` VALUES (1, 'LMS000001', 'Lương Bảo Long', 'Lương', 'Bảo', 'Long', NULL, 'M', 0, '2021-05-27', '0389941902', 'Lương Thành Công', 'thanhcong1710@gmail.com', NULL, NULL, NULL, NULL, 1, 1, NULL, NULL, NULL, '2024-05-12 20:58:19', 1, '2024-05-12 13:58:19', NULL, NULL, NULL, 1, NULL, NULL, NULL, '1990-05-09', NULL, 'M', NULL, 7, NULL, 1, 232, 26);
COMMIT;

-- ----------------------------
-- Table structure for subject_has_class
-- ----------------------------
DROP TABLE IF EXISTS `subject_has_class`;
CREATE TABLE `subject_has_class` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `subject_id` int(11) DEFAULT NULL,
  `class_id` int(11) DEFAULT NULL,
  `session` int(11) DEFAULT NULL,
  `stt` tinyint(2) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `subject_id` (`subject_id`) USING BTREE,
  KEY `class_id` (`class_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=203 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of subject_has_class
-- ----------------------------
BEGIN;
INSERT INTO `subject_has_class` VALUES (2, 1, 5, 4, 0, '2024-05-09 22:12:50');
INSERT INTO `subject_has_class` VALUES (3, 2, 5, 4, 0, '2024-05-09 22:12:50');
INSERT INTO `subject_has_class` VALUES (4, 3, 5, 4, 0, '2024-05-09 22:12:50');
INSERT INTO `subject_has_class` VALUES (5, 4, 5, 4, 0, '2024-05-09 22:12:50');
INSERT INTO `subject_has_class` VALUES (6, 1, 6, 4, 1, '2024-05-09 22:14:55');
INSERT INTO `subject_has_class` VALUES (7, 2, 6, 4, 2, '2024-05-09 22:14:55');
INSERT INTO `subject_has_class` VALUES (8, 3, 6, 4, 3, '2024-05-09 22:14:55');
INSERT INTO `subject_has_class` VALUES (9, 4, 6, 4, 4, '2024-05-09 22:14:55');
INSERT INTO `subject_has_class` VALUES (154, 1, 1, 4, 1, '2024-05-12 22:28:42');
INSERT INTO `subject_has_class` VALUES (155, 2, 1, 4, 2, '2024-05-12 22:28:42');
INSERT INTO `subject_has_class` VALUES (156, 3, 1, 4, 3, '2024-05-12 22:28:42');
INSERT INTO `subject_has_class` VALUES (157, 4, 1, 4, 4, '2024-05-12 22:28:42');
INSERT INTO `subject_has_class` VALUES (158, 5, 1, 4, 5, '2024-05-12 22:28:42');
INSERT INTO `subject_has_class` VALUES (159, 6, 1, 4, 6, '2024-05-12 22:28:42');
INSERT INTO `subject_has_class` VALUES (160, 7, 1, 4, 7, '2024-05-12 22:28:42');
INSERT INTO `subject_has_class` VALUES (161, 8, 1, 4, 8, '2024-05-12 22:28:42');
INSERT INTO `subject_has_class` VALUES (162, 9, 1, 4, 9, '2024-05-12 22:28:42');
INSERT INTO `subject_has_class` VALUES (163, 10, 1, 4, 10, '2024-05-12 22:28:42');
INSERT INTO `subject_has_class` VALUES (164, 11, 1, 4, 11, '2024-05-12 22:28:42');
INSERT INTO `subject_has_class` VALUES (165, 12, 1, 4, 12, '2024-05-12 22:28:42');
INSERT INTO `subject_has_class` VALUES (166, 1, 4, 4, 0, '2024-05-12 22:31:46');
INSERT INTO `subject_has_class` VALUES (167, 1, 4, 4, 1, '2024-05-12 22:31:46');
INSERT INTO `subject_has_class` VALUES (168, 2, 4, 4, 2, '2024-05-12 22:31:46');
INSERT INTO `subject_has_class` VALUES (169, 3, 4, 4, 3, '2024-05-12 22:31:46');
INSERT INTO `subject_has_class` VALUES (170, 4, 4, 4, 4, '2024-05-12 22:31:46');
INSERT INTO `subject_has_class` VALUES (171, 5, 4, 4, 5, '2024-05-12 22:31:46');
INSERT INTO `subject_has_class` VALUES (172, 6, 4, 4, 6, '2024-05-12 22:31:46');
INSERT INTO `subject_has_class` VALUES (173, 7, 4, 4, 7, '2024-05-12 22:31:46');
INSERT INTO `subject_has_class` VALUES (174, 8, 4, 4, 8, '2024-05-12 22:31:46');
INSERT INTO `subject_has_class` VALUES (175, 9, 4, 4, 9, '2024-05-12 22:31:46');
INSERT INTO `subject_has_class` VALUES (176, 10, 4, 4, 10, '2024-05-12 22:31:46');
INSERT INTO `subject_has_class` VALUES (177, 11, 4, 4, 11, '2024-05-12 22:31:46');
INSERT INTO `subject_has_class` VALUES (178, 12, 4, 4, 12, '2024-05-12 22:31:46');
INSERT INTO `subject_has_class` VALUES (179, 1, 2, 4, 1, '2024-05-12 22:31:55');
INSERT INTO `subject_has_class` VALUES (180, 2, 2, 4, 2, '2024-05-12 22:31:55');
INSERT INTO `subject_has_class` VALUES (181, 3, 2, 4, 3, '2024-05-12 22:31:55');
INSERT INTO `subject_has_class` VALUES (182, 4, 2, 4, 4, '2024-05-12 22:31:55');
INSERT INTO `subject_has_class` VALUES (183, 5, 2, 4, 5, '2024-05-12 22:31:55');
INSERT INTO `subject_has_class` VALUES (184, 6, 2, 4, 6, '2024-05-12 22:31:55');
INSERT INTO `subject_has_class` VALUES (185, 7, 2, 4, 7, '2024-05-12 22:31:55');
INSERT INTO `subject_has_class` VALUES (186, 8, 2, 4, 8, '2024-05-12 22:31:55');
INSERT INTO `subject_has_class` VALUES (187, 9, 2, 4, 9, '2024-05-12 22:31:55');
INSERT INTO `subject_has_class` VALUES (188, 10, 2, 4, 10, '2024-05-12 22:31:55');
INSERT INTO `subject_has_class` VALUES (189, 11, 2, 4, 11, '2024-05-12 22:31:55');
INSERT INTO `subject_has_class` VALUES (190, 12, 2, 4, 12, '2024-05-12 22:31:55');
INSERT INTO `subject_has_class` VALUES (191, 1, 3, 4, 1, '2024-05-12 22:32:00');
INSERT INTO `subject_has_class` VALUES (192, 2, 3, 4, 2, '2024-05-12 22:32:00');
INSERT INTO `subject_has_class` VALUES (193, 3, 3, 4, 3, '2024-05-12 22:32:00');
INSERT INTO `subject_has_class` VALUES (194, 4, 3, 4, 4, '2024-05-12 22:32:00');
INSERT INTO `subject_has_class` VALUES (195, 5, 3, 4, 5, '2024-05-12 22:32:00');
INSERT INTO `subject_has_class` VALUES (196, 6, 3, 4, 6, '2024-05-12 22:32:00');
INSERT INTO `subject_has_class` VALUES (197, 7, 3, 4, 7, '2024-05-12 22:32:00');
INSERT INTO `subject_has_class` VALUES (198, 8, 3, 4, 8, '2024-05-12 22:32:00');
INSERT INTO `subject_has_class` VALUES (199, 9, 3, 4, 9, '2024-05-12 22:32:00');
INSERT INTO `subject_has_class` VALUES (200, 10, 3, 4, 10, '2024-05-12 22:32:00');
INSERT INTO `subject_has_class` VALUES (201, 11, 3, 4, 11, '2024-05-12 22:32:00');
INSERT INTO `subject_has_class` VALUES (202, 12, 3, 4, 12, '2024-05-12 22:32:00');
COMMIT;

-- ----------------------------
-- Table structure for subjects
-- ----------------------------
DROP TABLE IF EXISTS `subjects`;
CREATE TABLE `subjects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` tinytext COLLATE utf8_unicode_ci,
  `status` tinyint(2) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `creator_id` int(11) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updator_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of subjects
-- ----------------------------
BEGIN;
INSERT INTO `subjects` VALUES (1, 'M1', 'Sinh vật học', 'Thông qua những thí nghiệm khoa học, trẻ sẽ có những trải nghiệm đầy cảm xúc về môi trường tự nhiên xung quanh trẻ cùng với con tàu FasTracKids và những chuyến du lịch ảo. Là một hành trình khám phá', 1, '2024-05-09 17:34:25', 1, '2024-05-09 17:35:04', 1);
INSERT INTO `subjects` VALUES (2, 'M2', 'Công nghệ', 'Trẻ được tham gia trải nghiệm các hoạt động khám phá công nghệ giao thông vận tải, công nghệ điện, công nghệ y tế và công nghệ máy tính từ đó giúp trẻ hiểu được giá trị của công nghệ và bi', 1, '2024-05-09 17:35:42', 1, NULL, NULL);
INSERT INTO `subjects` VALUES (3, 'M3', 'Mục tiêu và các bài học từ cuộc sống', 'Trẻ sẽ được học cách hợp tác với nhau để cùng khám phá ra cách làm thế nào để xác định mục tiêu và cùng nỗ lực để đạt được mục tiêu trong cuộc sống. Thông qua các trò chơi đóng vai theo ch', 1, '2024-05-09 17:35:59', 1, NULL, NULL);
INSERT INTO `subjects` VALUES (4, 'M4', 'Toán học', 'Trẻ hình thành được biểu tượng và phát triển các kỹ năng toán học như số lượng, không gian, thời gian, hình học một cách tự nhiên thông qua các trò chơi và các hoạt động tạo hình. Trong môn học n', 1, '2024-05-09 17:36:18', 1, NULL, NULL);
INSERT INTO `subjects` VALUES (5, 'M5', 'Khoa học tự nhiên', 'Trẻ được tham gia vào các thí nghiệm khoa học để hiểu nguyên nhân của các hiện tượng như từ tính, sự nổi lên của vật, hay sự chuyển hoá của các phân tử; từ đó phát triển óc quan sát, khả năng', 1, '2024-05-09 17:36:32', 1, NULL, NULL);
INSERT INTO `subjects` VALUES (6, 'M6', 'Thông tin liên lạc', 'Trẻ được khám phá các cách khác nhau mà con người giao tiếp và trao đổi thông tin trong suốt quá trình lịch sử. Bên cạnh đó trò chơi đóng vai và hoạt động ghi hình tự chọn cũng tạo cơ hội cho trẻ ', 1, '2024-05-09 17:36:50', 1, NULL, NULL);
INSERT INTO `subjects` VALUES (7, 'M7', 'Sáng tạo', 'Con người ở các quốc gia, dân tộc khác nháu trong các thời kỳ lịch sử khác nhau đã sử dụng sự sáng tạo và trí tưởng tượng của mình trong các lĩnh vực khác nhau của cuộc sống. Trong môn học này, t', 1, '2024-05-09 17:37:11', 1, NULL, NULL);
INSERT INTO `subjects` VALUES (8, 'M8', 'Khoa học trái đất', 'Khả năng kì diệu cùng những bí ẩn của Trái Đất như sức mạnh thiên nhiên, khí hậu, địa lý sẽ được trẻ khám phá một cách trực quan và sinh động. Những trò chơi trên bảng thông minh sẽ đưa trẻ ', 1, '2024-05-09 17:37:38', 1, NULL, NULL);
INSERT INTO `subjects` VALUES (9, 'M9', 'Thiên văn học', 'Trẻ khám phá khả năng tư duy trừu tượng và định hướng không gian của mình khi thám hiểm vũ trụ, mặt trời, mặt trăng & các hành tinh. Các em được tham gia vào những chuyến du hành không gian để hiểu ', 1, '2024-05-09 17:37:54', 1, NULL, NULL);
INSERT INTO `subjects` VALUES (10, 'M10', 'Văn học sáng tác', 'Trẻ hiểu và biết cách diễn đạt các trạng thái cảm xúc khác nhau trước các vấn đề của cuộc sống. Qua các hoạt động sáng tác, khả năng sáng tạo, trí tưởng tượng phong phú của trẻ được chắp ', 1, '2024-05-09 17:38:11', 1, NULL, NULL);
INSERT INTO `subjects` VALUES (11, 'M11', 'Diễn thuyết, Kịch nghệ và Nghệ Thuật', 'Thông qua các hoạt động khám phá nghệ thuật biểu diễn, trẻ sẽ đánh giá được giá trị to lớn của nghệ thuật & văn hóa. Từ đó trẻ biết cách làm thế nào để có thể thể hiện khả năng diễn đ', 1, '2024-05-09 17:38:29', 1, NULL, NULL);
INSERT INTO `subjects` VALUES (12, 'M12', 'Kinh tế học', 'Khái niệm kinh doanh sẽ trở nên gần gũi hơn với trẻ khi trẻ được tìm hiểu về quy trình sản xuất sản phẩm ở khắp mọi nơi trên thế giới. Trẻ sẽ được tham gia vào các hoạt động khảo sát ảo v', 1, '2024-05-09 17:38:44', 1, NULL, NULL);
COMMIT;

-- ----------------------------
-- Table structure for term_student_user
-- ----------------------------
DROP TABLE IF EXISTS `term_student_user`;
CREATE TABLE `term_student_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) NOT NULL,
  `ec_id` int(11) DEFAULT NULL,
  `ec_leader_id` int(11) DEFAULT NULL,
  `cm_id` int(11) DEFAULT NULL,
  `cm_leader_id` int(11) DEFAULT NULL,
  `ceo_branch_id` int(11) DEFAULT NULL,
  `teacher_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `status` tinyint(1) DEFAULT '0',
  `branch_id` int(11) DEFAULT NULL,
  `creator_id` int(11) DEFAULT NULL,
  `updator_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `index_term_student_user` (`id`,`student_id`,`ec_id`,`cm_id`) USING BTREE,
  KEY `ec_id` (`ec_id`) USING BTREE,
  KEY `cm_id` (`cm_id`) USING BTREE,
  KEY `student_id` (`student_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of term_student_user
-- ----------------------------
BEGIN;
INSERT INTO `term_student_user` VALUES (1, 1, 5, NULL, 4, 4, 2, 3, '2024-05-12 20:58:19', '2024-05-12 22:26:11', 1, 1, 1, 1);
COMMIT;

-- ----------------------------
-- Table structure for tuition_fee
-- ----------------------------
DROP TABLE IF EXISTS `tuition_fee`;
CREATE TABLE `tuition_fee` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `accounting_id` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `zone_id` int(11) DEFAULT NULL,
  `session` int(11) DEFAULT NULL,
  `price` int(11) DEFAULT NULL,
  `discount` int(11) DEFAULT NULL,
  `tax` int(11) DEFAULT NULL COMMENT 'Thuế giá trị gia tăng',
  `receivable` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `creator_id` int(11) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updator_id` int(11) DEFAULT NULL,
  `available_date` date DEFAULT NULL,
  `expired_date` date DEFAULT NULL,
  `hash_key` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `changed_fields` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(1) unsigned zerofill DEFAULT '1',
  `type` tinyint(2) DEFAULT '0' COMMENT '0 - 1 lần, 1 - nhiều lần',
  `branch_id` text COLLATE utf8mb4_unicode_ci,
  `meta_data` text COLLATE utf8mb4_unicode_ci,
  `number_of_months` tinyint(4) DEFAULT NULL COMMENT 'số tháng đào tạo của gói phí',
  `type_contract` tinyint(2) DEFAULT '1' COMMENT '1- chính thức, 0 - học thử',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `product_id` (`product_id`) USING BTREE,
  KEY `zone_id` (`zone_id`) USING BTREE,
  KEY `creator_id` (`creator_id`) USING BTREE,
  KEY `editor_id` (`updator_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of tuition_fee
-- ----------------------------
BEGIN;
INSERT INTO `tuition_fee` VALUES (1, 'FasTrack Tots học thử 1 buổi', NULL, 1, NULL, 1, 0, NULL, NULL, 0, '2024-05-12 14:22:19', 1, '2024-05-12 14:28:53', 1, '2024-05-01', '2025-05-01', NULL, NULL, 1, 0, '1,2,3', NULL, 1, 0);
INSERT INTO `tuition_fee` VALUES (2, 'FasTracKids học thử 1 buổi', NULL, 3, NULL, 1, 0, NULL, NULL, 0, '2024-05-12 14:24:37', 1, NULL, NULL, '2024-05-01', '2025-05-01', NULL, NULL, 1, 0, '1,2,3', NULL, 1, 0);
INSERT INTO `tuition_fee` VALUES (3, 'FasTracKids học thử 2 buổi', NULL, 3, NULL, 2, 0, NULL, NULL, 0, '2024-05-12 14:25:21', 1, NULL, NULL, '2024-05-01', '2025-05-01', NULL, NULL, 1, 0, '1,2,3', NULL, 1, 0);
INSERT INTO `tuition_fee` VALUES (4, 'FasTracKids - 6 tháng', NULL, 3, NULL, 48, 15000000, NULL, NULL, 15000000, '2024-05-12 14:27:25', 1, '2024-05-12 14:32:15', 1, '2024-05-01', '2025-05-01', NULL, NULL, 1, 0, '1,2,3', NULL, 6, 1);
INSERT INTO `tuition_fee` VALUES (5, 'FasTracKids - 12 tháng', NULL, 3, NULL, 96, 26000000, NULL, NULL, 26000000, '2024-05-12 14:28:07', 1, '2024-05-12 14:32:08', 1, '2024-05-01', '2025-05-01', NULL, NULL, 1, 0, '1,2,3', NULL, 12, 1);
INSERT INTO `tuition_fee` VALUES (6, 'FasTrack Tots - 12 tháng', NULL, 1, NULL, 96, 28000000, NULL, NULL, 28000000, '2024-05-12 14:32:01', 1, NULL, NULL, '2024-05-01', '2025-05-01', NULL, NULL, 1, 0, '1,2,3', NULL, 12, 1);
COMMIT;

-- ----------------------------
-- Table structure for tuition_fee_relation
-- ----------------------------
DROP TABLE IF EXISTS `tuition_fee_relation`;
CREATE TABLE `tuition_fee_relation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tuition_fee_id` int(11) DEFAULT NULL,
  `exchange_tuition_fee_id` int(11) DEFAULT NULL,
  `status` tinyint(2) DEFAULT '0' COMMENT '0 - inactive, 1 - active',
  `tuition_fee_name` varchar(255) DEFAULT NULL,
  `exchange_tuition_fee_name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `tuition_id` (`tuition_fee_id`) USING BTREE,
  KEY `exchange_tuition_id` (`exchange_tuition_fee_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tuition_fee_relation
-- ----------------------------
BEGIN;
INSERT INTO `tuition_fee_relation` VALUES (1, 6, 5, 1, 'FasTrack Tots - 12 tháng', 'FasTracKids - 12 tháng');
INSERT INTO `tuition_fee_relation` VALUES (2, 5, 6, 1, 'FasTracKids - 12 tháng', 'FasTrack Tots - 12 tháng');
INSERT INTO `tuition_fee_relation` VALUES (3, 4, 6, 1, 'FasTracKids - 6 tháng', 'FasTrack Tots - 12 tháng');
COMMIT;

-- ----------------------------
-- Table structure for tuition_transfer
-- ----------------------------
DROP TABLE IF EXISTS `tuition_transfer`;
CREATE TABLE `tuition_transfer` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Mã ID của bản ghi chuyển phí',
  `from_student_id` int(11) DEFAULT NULL COMMENT 'Mã ID học sinh chuyển phí',
  `to_student_id` int(11) DEFAULT NULL COMMENT 'Mã ID học sinh nhận phí',
  `note` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Ghi chú lý do chuyển phí.',
  `transfer_date` date DEFAULT NULL COMMENT 'Ngày thực hiện chuyển phí',
  `status` tinyint(4) DEFAULT NULL COMMENT 'Trạng thái bản ghi chuyển phí với: \r\n0 - Đã bị hủy\r\n1 - Chờ duyệt\r\n2 - Giám đốc đã từ chối \n3 - Kế toán HO đã từ chối\r\n4 - Giám đốc duyệt \n5 - Kế toán HO đã duyệt  \r\n6 -  Đã được duyệt thành công',
  `creator_id` int(11) DEFAULT NULL COMMENT 'Mã ID user khởi tạo bản ghi',
  `created_at` datetime DEFAULT NULL COMMENT 'Thời điểm khởi tạo bản ghi',
  `transferred_amount` int(11) DEFAULT NULL COMMENT 'Số tiền được chuyển',
  `received_amount` int(11) DEFAULT NULL COMMENT 'Số tiền nhận được',
  `meta_data` text COLLATE utf8mb4_unicode_ci COMMENT 'Dữ liệu các contract chuyển',
  `from_branch_id` int(11) DEFAULT NULL COMMENT 'Trung tâm chuyển phí',
  `to_branch_id` int(11) DEFAULT NULL COMMENT 'Trung tâm nhận phí',
  `from_product_id` int(11) DEFAULT NULL COMMENT 'Sản phẩm chuyển phí',
  `to_product_id` int(11) DEFAULT NULL COMMENT 'Sản phẩm nhận phí',
  `updator_id` int(11) DEFAULT NULL COMMENT 'Mã ID user sửa bản ghi',
  `ceo_approver_id` int(11) DEFAULT NULL COMMENT 'Mã ID user giám đốc phê duyệt chuyển phí',
  `ceo_approved_at` datetime DEFAULT NULL COMMENT 'Thời điểm giám đốc phê duyệt chuyển phí',
  `ceo_comment` text COLLATE utf8mb4_unicode_ci COMMENT 'Bình luận của giám đốc duyệt',
  `accounting_approver_id` int(11) DEFAULT NULL COMMENT 'Mã ID user kế toán phê duyệt chuyển phí',
  `accounting_approved_at` datetime DEFAULT NULL COMMENT 'Thời điểm kế toán phê duyệt chuyển phí',
  `accounting_comment` text COLLATE utf8mb4_unicode_ci COMMENT 'Bình luận của kế toán duyệt',
  `updated_at` datetime DEFAULT NULL COMMENT 'Thời điểm chỉnh sửa bản ghi',
  `attached_file` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Đường dẫn tệp tin đính kèm',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `to_student_id` (`to_student_id`) USING BTREE,
  KEY `creator_id` (`creator_id`) USING BTREE,
  KEY `from_student_id` (`from_student_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=7065 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of tuition_transfer
-- ----------------------------
BEGIN;
INSERT INTO `tuition_transfer` VALUES (7064, 101750, 101751, 'chuyển phí demo', '2024-05-09', 5, 1, '2024-04-30 15:41:54', 10000000, 10000000, '{\"tuition_transfer\":{\"from_branch_id\":1,\"from_student_id\":101750,\"to_branch_id\":1,\"to_student_id\":101751,\"to_product_id\":2,\"note\":\"chuy\\u1ec3n ph\\u00ed demo\",\"transfer_date\":\"2024-05-09\",\"received_amount\":10000000,\"transferred_amount\":10000000},\"from_student_info\":{\"name\":\"h\\u1ecdc sinh\",\"lms_code\":\"LMS101750\",\"gud_name1\":\"demo23\",\"gud_mobile1\":\"0389946902\",\"gud_email1\":null,\"address\":null,\"student_id\":101750,\"label\":\"h\\u1ecdc sinh - LMS101750\",\"cm_name\":\"L\\u01b0\\u01a1ng Th\\u00e0nh C\\u00f4ng - C00001\",\"ec_name\":\"L\\u01b0\\u01a1ng Th\\u00e0nh C\\u00f4ng - C00001\"},\"from_contracts\":[{\"contract_id\":44066,\"total_charged\":1000000,\"summary_sessions\":0,\"real_sessions\":24,\"branch_id\":1,\"product_id\":1,\"bonus_sessions\":4,\"done_sessions\":0,\"left_sessions\":0,\"class_id\":null,\"code\":\"C044066\",\"enrolment_start_date\":null,\"enrolment_last_date\":null,\"tuition_fee_id\":262,\"class_day\":null,\"class_name\":null,\"product_name\":\"UCREA\",\"tuition_fee_name\":\"UCREA - 6 th\\u00e1ng\"},{\"contract_id\":44068,\"total_charged\":12000000,\"summary_sessions\":28,\"real_sessions\":24,\"branch_id\":1,\"product_id\":1,\"bonus_sessions\":4,\"done_sessions\":4,\"left_sessions\":24,\"class_id\":1,\"code\":\"LMS044068\",\"enrolment_start_date\":\"2024-04-25\",\"enrolment_last_date\":\"2024-10-17\",\"tuition_fee_id\":262,\"class_day\":\"2,3,6\",\"class_name\":\"demo\",\"product_name\":\"UCREA\",\"tuition_fee_name\":\"UCREA - 6 th\\u00e1ng\"}],\"to_student_info\":{\"name\":\"h\\u1ecdc sinh\",\"lms_code\":\"LMS101751\",\"gud_name1\":\"demo23\",\"gud_mobile1\":\"0389946902\",\"gud_email1\":\"demo@gmail.com\",\"address\":\"h\\u00e0 n\\u1ed9i c\\u1ea7u di\\u1ec5n\",\"student_id\":101751,\"label\":\"h\\u1ecdc sinh - LMS101751\",\"cm_name\":null,\"ec_name\":null},\"to_contracts\":[{\"contract_id\":44067,\"total_charged\":0,\"summary_sessions\":0,\"real_sessions\":24,\"branch_id\":1,\"product_id\":1,\"bonus_sessions\":4,\"done_sessions\":0,\"left_sessions\":0,\"class_id\":3,\"code\":\"LMS044067\",\"enrolment_start_date\":\"2024-04-26\",\"enrolment_last_date\":\"2024-07-19\",\"tuition_fee_id\":262,\"class_day\":\"2,4,6,7\",\"class_name\":\"demo 1\",\"product_name\":\"UCREA\",\"tuition_fee_name\":\"UCREA - 6 th\\u00e1ng\"}],\"transferred_contracts\":[{\"contract_id\":44066,\"total_charged\":1000000,\"summary_sessions\":0,\"real_sessions\":24,\"branch_id\":1,\"product_id\":1,\"bonus_sessions\":4,\"done_sessions\":0,\"left_sessions\":0,\"class_id\":null,\"code\":\"C044066\",\"enrolment_start_date\":null,\"enrolment_last_date\":null,\"tuition_fee_id\":262,\"class_day\":null,\"class_name\":null,\"product_name\":\"UCREA\",\"tuition_fee_name\":\"UCREA - 6 th\\u00e1ng\",\"left_real_sessions\":24,\"left_amount\":1000000},{\"contract_id\":44068,\"total_charged\":12000000,\"summary_sessions\":28,\"real_sessions\":24,\"branch_id\":1,\"product_id\":1,\"bonus_sessions\":4,\"done_sessions\":6,\"left_sessions\":22,\"class_id\":1,\"code\":\"LMS044068\",\"enrolment_start_date\":\"2024-04-25\",\"enrolment_last_date\":\"2024-10-17\",\"tuition_fee_id\":262,\"class_day\":\"2,3,6\",\"class_name\":\"demo\",\"product_name\":\"UCREA\",\"tuition_fee_name\":\"UCREA - 6 th\\u00e1ng\",\"left_real_sessions\":18,\"left_amount\":9000000}],\"received_contracts\":[{\"tuition_fee_id\":268,\"tuition_fee_name\":\"BRIGHT IG - 6 th\\u00e1ng\",\"total_charged\":1000000,\"real_sessions\":2,\"bonus_sessions\":0,\"product_name\":\"BRIGHT IG\"},{\"tuition_fee_id\":268,\"tuition_fee_name\":\"BRIGHT IG - 6 th\\u00e1ng\",\"total_charged\":9000000,\"real_sessions\":18,\"bonus_sessions\":0,\"product_name\":\"BRIGHT IG\"}]}', 1, 1, NULL, 2, 1, 1, '2024-04-30 16:28:35', 'ghi chú đồng ý', 1, '2024-04-30 16:29:54', 'ghi chú', '2024-04-30 16:29:54', NULL);
COMMIT;

-- ----------------------------
-- Table structure for users
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `api_token` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `menuroles` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint(2) NOT NULL DEFAULT '0',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `creator_id` int(11) DEFAULT NULL,
  `updator_id` int(11) DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `branch_id` int(11) DEFAULT NULL,
  `branch_name` tinytext COLLATE utf8mb4_unicode_ci,
  `hrm_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `manager_hrm_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `manager_id` int(11) DEFAULT '0',
  `change_password` tinyint(2) DEFAULT '0',
  `role_name` tinytext COLLATE utf8mb4_unicode_ci,
  `sip_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `manager_id` (`manager_id`) USING BTREE,
  KEY `hrm_id` (`hrm_id`) USING BTREE,
  KEY `manager_hrm_id` (`manager_hrm_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of users
-- ----------------------------
BEGIN;
INSERT INTO `users` VALUES (1, 'Lương Thành Công', '0389941902', 'admin@admin.com', '2020-08-27 02:04:43', '$2y$10$upeEWEPRUHvriPrThSy8hOMzfqxFGRFPrBP0UgkBwC8C54RBz/6C6', NULL, 'admin,user', 1, 'EQt3UDeo4F', '2020-08-27 02:04:43', '2024-05-11 21:40:30', NULL, 1, NULL, 3, 'Trung tâm - HAPU, Trung tâm - Mỹ Đình, Trung tâm - Trung Kính', 'C00001', NULL, NULL, 0, 'Quản trị hệ thống', NULL);
INSERT INTO `users` VALUES (2, 'Giám đốc trung tâm', '0388888888', 'gdtt@gmail.com', NULL, '$2y$10$Oj6RZMdDQ43LadutsxpXZet.dVDDYm7rd7q1Aj8541XMwvgJdSDPS', NULL, '', 1, NULL, '2024-05-12 14:43:31', NULL, 1, NULL, NULL, 3, 'Trung tâm Giảng Võ, Trung tâm Mỹ Đình, Trung tâm Cầu Giấy', 'C00002', NULL, NULL, 0, 'Giám đốc trung tâm', NULL);
INSERT INTO `users` VALUES (3, 'Giáo viên', '0388888888', 'giaovien@gmail.com', NULL, '$2y$10$Q33qboMMmLejTZRJ28GsMOv5f.ZGOnfd6BJhDnQztAlTDNHMVVYPa', NULL, '', 1, NULL, '2024-05-12 14:44:40', NULL, 1, NULL, NULL, 3, 'Trung tâm Giảng Võ, Trung tâm Mỹ Đình, Trung tâm Cầu Giấy', 'C00003', NULL, NULL, 0, 'Giáo viên', NULL);
INSERT INTO `users` VALUES (4, 'Vận hành', '0388888888', 'vanhanh@gmail.com', NULL, '$2y$10$AuQRTmWhgKyfvUau7zOsb.nv7u6/jls8o2uJNt0RpoQhXwhElqENK', NULL, '', 1, NULL, '2024-05-12 14:45:39', NULL, 1, NULL, NULL, 3, 'Trung tâm Giảng Võ, Trung tâm Mỹ Đình, Trung tâm Cầu Giấy', 'C00004', NULL, NULL, 0, 'Vận hành', NULL);
INSERT INTO `users` VALUES (5, 'CSKH', '0388888888', 'cskh@gmail.com', NULL, '$2y$10$fniRs3v1mKsaJFNgkffhb.FDotIpYgnBjqd/XIXxEbD.ss/4yRmo2', NULL, '', 1, NULL, '2024-05-12 14:46:47', NULL, 1, NULL, NULL, 3, 'Trung tâm Giảng Võ, Trung tâm Mỹ Đình, Trung tâm Cầu Giấy', 'C00005', NULL, NULL, 0, 'CSKH', NULL);
INSERT INTO `users` VALUES (6, 'Giáo viên 1', '0388888888', 'giaovien1@gmail.com', NULL, '$2y$10$PZ21PfVuQ6FGt/D93XyHhetjnmLMdBJ5LA9703PpOk.fIC1kwJeya', NULL, '', 1, NULL, '2024-05-12 19:46:09', NULL, 1, NULL, NULL, 3, 'Trung tâm Giảng Võ, Trung tâm Mỹ Đình, Trung tâm Cầu Giấy', 'C00006', NULL, NULL, 0, 'Giáo viên', NULL);
INSERT INTO `users` VALUES (7, 'Vận hành 1', '0388888888', 'vanhanh1@gmail.com', NULL, '$2y$10$2y/yvZK36FbSozhMe8gkUOKSx8e84bJZsRwxAKzjTf.z3ol4urnHO', NULL, '', 1, NULL, '2024-05-12 19:46:53', NULL, 1, NULL, NULL, 3, 'Trung tâm Giảng Võ, Trung tâm Mỹ Đình, Trung tâm Cầu Giấy', 'C00007', NULL, NULL, 0, 'Vận hành', NULL);
INSERT INTO `users` VALUES (8, 'CSKH 1', '0388888888', 'cskh1@gmail.com', NULL, '$2y$10$iUiGUMExBuJnMS4CglN5tub4My34DS7EATVXYJ.zFPS7FlzETHO.G', NULL, '', 1, NULL, '2024-05-12 19:47:32', NULL, 1, NULL, NULL, 3, 'Trung tâm Giảng Võ, Trung tâm Mỹ Đình, Trung tâm Cầu Giấy', 'C00008', NULL, NULL, 0, 'CSKH', NULL);
COMMIT;

SET FOREIGN_KEY_CHECKS = 1;
