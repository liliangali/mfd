/*
Navicat MySQL Data Transfer

Source Server         : www
Source Server Version : 50628
Source Host           : localhost:3306
Source Database       : project_mfdplatform

Target Server Type    : MYSQL
Target Server Version : 50628
File Encoding         : 65001

Date: 2017-04-10 21:18:39
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for cf_comment_img
-- ----------------------------
DROP TABLE IF EXISTS `cf_comment_img`;
CREATE TABLE `cf_comment_img` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `image` varchar(255) DEFAULT NULL COMMENT '评价大图',
  `s_image` varchar(255) DEFAULT NULL COMMENT '评价小图',
  `comment_id` int(11) DEFAULT '0' COMMENT '评价id',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
