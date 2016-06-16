/*
Navicat MySQL Data Transfer

Source Server         : Combell_newserver
Source Server Version : 50623
Source Host           : 178.208.48.50:3306
Source Database       : dcms_groupdc_be

Target Server Type    : MYSQL
Target Server Version : 50623
File Encoding         : 65001

Date: 2016-06-16 11:37:33
*/

SET FOREIGN_KEY_CHECKS=0;
-- ----------------------------
-- Table structure for `pages_language`
-- ----------------------------
DROP TABLE IF EXISTS `pages_language`;
CREATE TABLE `pages_language` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `language_id` int(11) unsigned NOT NULL DEFAULT '1',
  `parent_id` int(11) unsigned DEFAULT NULL,
  `lft` int(11) DEFAULT NULL,
  `rgt` int(11) DEFAULT NULL,
  `depth` int(11) DEFAULT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `body` text COLLATE utf8_unicode_ci,
  `url_path` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `url_slug` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `admin` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NULL DEFAULT '0000-00-00 00:00:00',
  `_oldpageid` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `i_Parent` (`parent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of pages_language
-- ----------------------------
