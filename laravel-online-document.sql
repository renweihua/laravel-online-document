/*
 Navicat Premium Data Transfer

 Source Server         : localhost
 Source Server Type    : MySQL
 Source Server Version : 50740
 Source Host           : localhost:3306
 Source Schema         : laravel-online-document

 Target Server Type    : MySQL
 Target Server Version : 50740
 File Encoding         : 65001

 Date: 24/04/2023 20:04:51
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for doc_apis
-- ----------------------------
DROP TABLE IF EXISTS `doc_apis`;
CREATE TABLE `doc_apis` (
  `api_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '会员Id',
  `project_id` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '项目Id',
  `group_id` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '分组Id',
  `api_url` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'URL',
  `api_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'API名称',
  `api_description` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'API描述',
  `http_protocol` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '协议',
  `object_name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '对象名',
  `function_name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '方法名',
  `develop_language` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '开发语言',
  `http_method` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'Http请求方式',
  `http_header` json DEFAULT NULL COMMENT '请求头部信息',
  `http_params` json DEFAULT NULL COMMENT '请求参数',
  `http_return_type` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'http请求返回值',
  `response_sample` json NOT NULL COMMENT 'http响应数据样例',
  `response_params` json DEFAULT NULL COMMENT 'http请求响应参数',
  `created_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updated_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`api_id`) USING BTREE,
  KEY `doc_apis_api_name_index` (`api_name`),
  KEY `doc_apis_user_id_index` (`user_id`),
  KEY `doc_apis_project_id_group_id_index` (`project_id`,`group_id`),
  KEY `doc_apis_is_delete_index` (`is_delete`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='API接口表';

-- ----------------------------
-- Records of doc_apis
-- ----------------------------
BEGIN;
INSERT INTO `doc_apis` (`api_id`, `user_id`, `project_id`, `group_id`, `api_url`, `api_name`, `api_description`, `http_protocol`, `object_name`, `function_name`, `develop_language`, `http_method`, `http_header`, `http_params`, `http_return_type`, `response_sample`, `response_params`, `created_time`, `updated_time`, `is_delete`) VALUES (1, 1, 1, 1, '/auth/login', '登录', '登录接口的基本描述·~~~~2222', 'HTTP', '对象名称??', '方法名称??', 'PHP', 'POST', '{}', '[{\"desc\": \"账户\", \"name\": \"name\", \"type\": \"string\", \"isAdd\": true, \"handle\": true, \"example\": \"\", \"required\": true}, {\"desc\": \"密码\", \"name\": \"password\", \"type\": \"string\", \"isAdd\": true, \"handle\": true, \"example\": \"\", \"required\": true}, {\"desc\": \"平台\", \"name\": \"platform\", \"type\": \"string\", \"isAdd\": true, \"handle\": true, \"example\": \"\", \"required\": false}]', '', '{\"success\": {\"msg\": \"success\", \"data\": {\"auth_type\": \"user\", \"access_token\": \"M9nIWfm6GyVDfTkHtBHoI7vxRl69amQMm8t8B8URlsmvpt+xklwwHl4kFlDQPxM/\", \"expires_time\": 1656512968}, \"status\": 200, \"execution_time\": 0.2484140396118164}}', '[{\"type\": \"string\", \"isAdd\": false, \"handle\": true, \"required\": true, \"fieldName\": \"msg\", \"objectName\": \"\", \"description\": \"\"}, {\"type\": \"string\", \"isAdd\": false, \"handle\": true, \"required\": true, \"fieldName\": \"data\", \"objectName\": \"\", \"description\": \"\"}, {\"type\": \"string\", \"isAdd\": false, \"handle\": true, \"required\": true, \"fieldName\": \">auth_type\", \"objectName\": \"\", \"description\": \"\"}, {\"type\": \"string\", \"isAdd\": false, \"handle\": true, \"required\": true, \"fieldName\": \">access_token\", \"objectName\": \"\", \"description\": \"\"}, {\"type\": \"string\", \"isAdd\": false, \"handle\": true, \"required\": true, \"fieldName\": \">expires_time\", \"objectName\": \"\", \"description\": \"\"}]', 1655695888, 1682237386, 0);
INSERT INTO `doc_apis` (`api_id`, `user_id`, `project_id`, `group_id`, `api_url`, `api_name`, `api_description`, `http_protocol`, `object_name`, `function_name`, `develop_language`, `http_method`, `http_header`, `http_params`, `http_return_type`, `response_sample`, `response_params`, `created_time`, `updated_time`, `is_delete`) VALUES (2, 1, 1, 5, '1212', '2112', '', 'HTTP', '', '', '', 'GET', '[]', '[]', '', '{\"failed\": [], \"success\": []}', '[]', 1682315661, 1682315661, 0);
COMMIT;

-- ----------------------------
-- Table structure for doc_docs
-- ----------------------------
DROP TABLE IF EXISTS `doc_docs`;
CREATE TABLE `doc_docs` (
  `doc_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '会员Id',
  `project_id` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '项目Id',
  `group_id` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '分组Id',
  `doc_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '文档名称',
  `content_html` longtext COLLATE utf8mb4_unicode_ci COMMENT '内容',
  `content_markdown` longtext COLLATE utf8mb4_unicode_ci COMMENT 'md内容',
  `sort` int(10) unsigned NOT NULL DEFAULT '100' COMMENT '排序：升序',
  `created_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updated_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `view_count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '浏览量',
  PRIMARY KEY (`doc_id`) USING BTREE,
  KEY `doc_docs_doc_name_index` (`doc_name`),
  KEY `doc_docs_user_id_index` (`user_id`),
  KEY `doc_docs_project_id_group_id_index` (`project_id`,`group_id`),
  KEY `doc_docs_sort_index` (`sort`),
  KEY `doc_docs_is_delete_index` (`is_delete`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='文档表';

-- ----------------------------
-- Records of doc_docs
-- ----------------------------
BEGIN;
INSERT INTO `doc_docs` (`doc_id`, `user_id`, `project_id`, `group_id`, `doc_name`, `content_html`, `content_markdown`, `sort`, `created_time`, `updated_time`, `is_delete`, `view_count`) VALUES (1, 1, 1, 0, '1', NULL, NULL, 100, 1682062323, 1682062323, 0, 0);
INSERT INTO `doc_docs` (`doc_id`, `user_id`, `project_id`, `group_id`, `doc_name`, `content_html`, `content_markdown`, `sort`, `created_time`, `updated_time`, `is_delete`, `view_count`) VALUES (2, 1, 1, 0, '111', NULL, NULL, 100, 1682062343, 1682062343, 0, 0);
INSERT INTO `doc_docs` (`doc_id`, `user_id`, `project_id`, `group_id`, `doc_name`, `content_html`, `content_markdown`, `sort`, `created_time`, `updated_time`, `is_delete`, `view_count`) VALUES (3, 1, 1, 0, '接口文档', NULL, '** 数据源说明 **\n\n|字段       |类型         |空   |默认   |注释         |\n|:----      |:-------    |:-- |---   |------      |\n|uid        |int         |否   |      |            |\n|username   |string      |否   |      |   用户名    |\n|password   |string      |否   |      |   密码      |\n|name       |string      |是   |      |   昵称      |\n|created_at | timestamp  |否   | null |   注册时间  |\n\n**备注**\n\n- 无\n\n\n\n**简要描述：**\n\n- 描述\n\n**请求URL：**\n- ` BASE_URL/api/xx `\n\n**请求方式：**\n- POST\n\n**参数：**\n\n| 参数名   |必选   | 类型   | 说明   | 默认   |\n|:----    |:---  |:----- |-----   | ---  |\n|id       |是    |int    | ID     |       |\n|password |是    |string | 密码    |      |\n|name     |否    |string | 姓名    | 空字符 |\n\n **返回示例**\n\n```\n{\n    \"code\": 0,\n    \"message\": \"OK\",\n    \"data\": {\n      \"id\": \"1\",\n      \"username\": \"yybawang\",\n      \"name\": \"测试\",\n      \"created_at\": \"2020-11-11 12:09:21\",\n      \"updated_at\": \"2020-11-11 12:09:21\",\n    }\n}\n```\n\n **返回参数说明**\n\n|参数名 | 类型 | 说明   |\n|:----- |:-----  |-----  |\n| code    | int    | 全局错误码，0 成功，其他值都为失败  |\n| message | string | 成功/失败提示信息  |\n| data    | array/object | 成功时返回的数据，根据每个接口不同，如果是失败，则只有默认值-->空数组  |\n\n **备注**\n\n- 无', 100, 1682063884, 1682063884, 0, 0);
INSERT INTO `doc_docs` (`doc_id`, `user_id`, `project_id`, `group_id`, `doc_name`, `content_html`, `content_markdown`, `sort`, `created_time`, `updated_time`, `is_delete`, `view_count`) VALUES (4, 1, 1, 0, '21212', NULL, '** 数据源说明 **\n\n|字段       |类型         |空   |默认   |注释         |\n|:----      |:-------    |:-- |---   |------      |\n|uid        |int         |否   |      |            |\n|username   |string      |否   |      |   用户名    |\n|password   |string      |否   |      |   密码      |\n|name       |string      |是   |      |   昵称      |\n|created_at | timestamp  |否   | null |   注册时间  |\n\n**备注**\n\n- 无\n\n\n\n**简要描述：**\n\n- 描述\n\n**请求URL：**\n- ` BASE_URL/api/xx `\n\n**请求方式：**\n- POST\n\n**参数：**\n\n| 参数名   |必选   | 类型   | 说明   | 默认   |\n|:----    |:---  |:----- |-----   | ---  |\n|id       |是    |int    | ID     |       |\n|password |是    |string | 密码    |      |\n|name     |否    |string | 姓名    | 空字符 |\n\n **返回示例**\n\n```\n{\n    \"code\": 0,\n    \"message\": \"OK\",\n    \"data\": {\n      \"id\": \"1\",\n      \"username\": \"yybawang\",\n      \"name\": \"测试\",\n      \"created_at\": \"2020-11-11 12:09:21\",\n      \"updated_at\": \"2020-11-11 12:09:21\",\n    }\n}\n```\n\n **返回参数说明**\n\n|参数名 | 类型 | 说明   |\n|:----- |:-----  |-----  |\n| code    | int    | 全局错误码，0 成功，其他值都为失败  |\n| message | string | 成功/失败提示信息  |\n| data    | array/object | 成功时返回的数据，根据每个接口不同，如果是失败，则只有默认值-->空数组  |\n\n **备注**\n\n- 无', 100, 1682335187, 1682335981, 0, 0);
COMMIT;

-- ----------------------------
-- Table structure for doc_groups
-- ----------------------------
DROP TABLE IF EXISTS `doc_groups`;
CREATE TABLE `doc_groups` (
  `group_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `project_id` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '项目Id',
  `group_type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '分组类型：0.文档',
  `group_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '分组名称',
  `sort` int(10) unsigned NOT NULL DEFAULT '100' COMMENT '排序：升序',
  `created_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updated_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `parent_id` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '项目Id',
  `user_id` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '项目Id',
  PRIMARY KEY (`group_id`) USING BTREE,
  KEY `doc_groups_group_name_index` (`group_name`),
  KEY `doc_groups_project_id_group_type_index` (`project_id`,`group_type`),
  KEY `doc_groups_sort_index` (`sort`),
  KEY `doc_groups_is_delete_index` (`is_delete`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of doc_groups
-- ----------------------------
BEGIN;
INSERT INTO `doc_groups` (`group_id`, `project_id`, `group_type`, `group_name`, `sort`, `created_time`, `updated_time`, `is_delete`, `parent_id`, `user_id`) VALUES (1, 1, 0, 'Auth', 100, 1682076997, 1682076997, 0, 0, 1);
INSERT INTO `doc_groups` (`group_id`, `project_id`, `group_type`, `group_name`, `sort`, `created_time`, `updated_time`, `is_delete`, `parent_id`, `user_id`) VALUES (2, 1, 1, '文档分组', 100, 1682077180, 1682077180, 0, 0, 1);
INSERT INTO `doc_groups` (`group_id`, `project_id`, `group_type`, `group_name`, `sort`, `created_time`, `updated_time`, `is_delete`, `parent_id`, `user_id`) VALUES (3, 1, 0, '项目', 100, 1682078679, 1682078679, 0, 0, 1);
INSERT INTO `doc_groups` (`group_id`, `project_id`, `group_type`, `group_name`, `sort`, `created_time`, `updated_time`, `is_delete`, `parent_id`, `user_id`) VALUES (4, 1, 0, 'API接口', 100, 1682078727, 1682078727, 0, 0, 1);
INSERT INTO `doc_groups` (`group_id`, `project_id`, `group_type`, `group_name`, `sort`, `created_time`, `updated_time`, `is_delete`, `parent_id`, `user_id`) VALUES (5, 1, 0, '文档', 100, 1682078733, 1682078733, 0, 0, 1);
COMMIT;

-- ----------------------------
-- Table structure for doc_migrations
-- ----------------------------
DROP TABLE IF EXISTS `doc_migrations`;
CREATE TABLE `doc_migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of doc_migrations
-- ----------------------------
BEGIN;
INSERT INTO `doc_migrations` (`id`, `migration`, `batch`) VALUES (1, '2014_10_12_000000_create_users_table', 1);
INSERT INTO `doc_migrations` (`id`, `migration`, `batch`) VALUES (2, '2014_10_12_100000_create_password_resets_table', 1);
INSERT INTO `doc_migrations` (`id`, `migration`, `batch`) VALUES (3, '2019_08_19_000000_create_failed_jobs_table', 1);
INSERT INTO `doc_migrations` (`id`, `migration`, `batch`) VALUES (4, '2019_12_14_000001_create_personal_access_tokens_table', 1);
INSERT INTO `doc_migrations` (`id`, `migration`, `batch`) VALUES (5, '2023_04_20_175507_create_projects_table', 1);
INSERT INTO `doc_migrations` (`id`, `migration`, `batch`) VALUES (6, '2023_04_21_140534_create_docs_table', 2);
INSERT INTO `doc_migrations` (`id`, `migration`, `batch`) VALUES (7, '2023_04_21_144256_create_groups_table', 3);
INSERT INTO `doc_migrations` (`id`, `migration`, `batch`) VALUES (8, '2023_04_21_162821_create_apis_table', 4);
COMMIT;

-- ----------------------------
-- Table structure for doc_projects
-- ----------------------------
DROP TABLE IF EXISTS `doc_projects`;
CREATE TABLE `doc_projects` (
  `project_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '会员Id',
  `project_type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '项目类型：0.PC',
  `project_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '项目名称',
  `project_description` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '项目描述',
  `project_version` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '项目版本',
  `created_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updated_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `is_public` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '公开度：0.私密；1.完全公开；2.密码访问',
  PRIMARY KEY (`project_id`) USING BTREE,
  KEY `cnpscy_projects_user_id_index` (`user_id`),
  KEY `cnpscy_projects_project_name_index` (`project_name`),
  KEY `cnpscy_projects_is_delete_index` (`is_delete`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='项目表';

-- ----------------------------
-- Records of doc_projects
-- ----------------------------
BEGIN;
INSERT INTO `doc_projects` (`project_id`, `user_id`, `project_type`, `project_name`, `project_description`, `project_version`, `created_time`, `updated_time`, `is_delete`, `is_public`) VALUES (1, 1, 0, '小丑路人·社区01', '社区的接口文档-2112', 'v1.0.001', 1681986475, 1682334316, 0, 1);
COMMIT;

SET FOREIGN_KEY_CHECKS = 1;
