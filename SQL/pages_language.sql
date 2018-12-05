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
  `thumbnail` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `url_path` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `url_slug` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `admin` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT null,
  `updated_at` timestamp NULL DEFAULT null,
  PRIMARY KEY (`id`),
  KEY `i_Parent` (`parent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- ----------------------------
-- View structure for `vwpages_language`
-- ----------------------------
DROP VIEW IF EXISTS `vwpages_language`;
CREATE VIEW `vwpages_language` AS select `pages_language`.`id` AS `id`,`pages_language`.`language_id` AS `language_id`,`pages_language`.`parent_id` AS `parent_id`,`pages_language`.`lft` AS `lft`,`pages_language`.`rgt` AS `rgt`,`pages_language`.`depth` AS `depth`,`pages_language`.`title` AS `title`,`pages_language`.`body` AS `body`,`pages_language`.`url_path` AS `url_path`,`pages_language`.`url_slug` AS `url_slug`,`pages_language`.`admin` AS `admin`,`pages_language`.`created_at` AS `created_at`,`pages_language`.`updated_at` AS `updated_at`,(select `parent`.`title` from (`pages_language` `node` join `pages_language` `parent`) where ((`node`.`lft` between `parent`.`lft` and `parent`.`rgt`) and (`node`.`id` = `pages_language`.`id`) and (`parent`.`language_id` = `pages_language`.`language_id`) and (`parent`.`depth` = 1))) AS `division`,(select `parent`.`url_slug` from (`pages_language` `node` join `pages_language` `parent`) where ((`node`.`lft` between `parent`.`lft` and `parent`.`rgt`) and (`node`.`id` = `pages_language`.`id`) and (`parent`.`language_id` = `pages_language`.`language_id`) and (`parent`.`depth` = 1)) order by `node`.`lft`) AS `division_url`,(select `parent`.`title` from (`pages_language` `node` join `pages_language` `parent`) where ((`node`.`lft` between `parent`.`lft` and `parent`.`rgt`) and (`node`.`id` = `pages_language`.`id`) and (`parent`.`language_id` = `pages_language`.`language_id`) and (`parent`.`depth` = 2)) group by `parent`.`language_id` order by `node`.`lft`) AS `sector`,(select `parent`.`url_slug` from (`pages_language` `node` join `pages_language` `parent`) where ((`node`.`lft` between `parent`.`lft` and `parent`.`rgt`) and (`node`.`id` = `pages_language`.`id`) and (`parent`.`language_id` = `pages_language`.`language_id`) and (`parent`.`depth` = 2)) group by `parent`.`language_id` order by `node`.`lft`) AS `sector_url`,(select group_concat(`parent`.`url_slug` separator '/') AS `title` from (`pages_language` `node` join `pages_language` `parent`) where ((`node`.`lft` between `parent`.`lft` and `parent`.`rgt`) and (`node`.`id` = `pages_language`.`id`) and (`parent`.`language_id` = `pages_language`.`language_id`) and (`parent`.`depth` in (1,2))) group by `parent`.`language_id` order by `node`.`lft`) AS `divisionsector_url`,(select `parent`.`title` from (`pages_language` `node` join `pages_language` `parent`) where ((`node`.`lft` between `parent`.`lft` and `parent`.`rgt`) and (`node`.`id` = `pages_language`.`id`) and (`parent`.`language_id` = `pages_language`.`language_id`) and (`parent`.`depth` = 3)) order by `node`.`lft`) AS `page`,(select `parent`.`url_slug` from (`pages_language` `node` join `pages_language` `parent`) where ((`node`.`lft` between `parent`.`lft` and `parent`.`rgt`) and (`node`.`id` = `pages_language`.`id`) and (`parent`.`language_id` = `pages_language`.`language_id`) and (`parent`.`depth` = 3)) order by `node`.`lft`) AS `page_url`,(select group_concat(`parent`.`url_slug` separator '/') from (`pages_language` `node` join `pages_language` `parent`) where ((`node`.`lft` between `parent`.`lft` and `parent`.`rgt`) and (`node`.`id` = `pages_language`.`id`) and (`parent`.`language_id` = `pages_language`.`language_id`) and (`parent`.`depth` > 0)) group by `parent`.`language_id` order by `node`.`lft`) AS `full_url` from `pages_language`;


-- ----------------------------
-- Records of pages_language
-- ----------------------------
