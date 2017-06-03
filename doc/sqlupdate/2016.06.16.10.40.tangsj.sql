/*
Navicat MySQL Data Transfer

Source Server         : www
Source Server Version : 50617
Source Host           : localhost:3306
Source Database       : project_mfdplatform

Target Server Type    : MYSQL
Target Server Version : 50617
File Encoding         : 65001

Date: 2016-06-16 10:38:31
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for uc_members_connect
-- ----------------------------
DROP TABLE IF EXISTS `uc_members_connect`;
CREATE TABLE `uc_members_connect` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `access_token` varchar(255) DEFAULT NULL COMMENT '标识码',
  `openid` varchar(100) DEFAULT NULL COMMENT '标识名称',
  `uid` mediumint(8) NOT NULL COMMENT '用户id',
  `type` int(10) NOT NULL COMMENT '类型 1：qq  2： 微信  3：微博',
  `come_from` varchar(33) NOT NULL COMMENT '来源 pc  app  dz',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
