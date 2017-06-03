/*
Navicat MySQL Data Transfer

Source Server         : www
Source Server Version : 50628
Source Host           : localhost:3306
Source Database       : project_mfdplatform

Target Server Type    : MYSQL
Target Server Version : 50628
File Encoding         : 65001

Date: 2017-05-10 09:52:33
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for cf_new_promotion
-- ----------------------------
DROP TABLE IF EXISTS `cf_new_promotion`;
CREATE TABLE `cf_new_promotion` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主健id',
  `name` varchar(100) NOT NULL COMMENT '规则名称',
  `introduce` varchar(255) DEFAULT NULL COMMENT '简介',
  `is_open` tinyint(3) DEFAULT '1' COMMENT '是否启用（1是0否）',
  `level` tinyint(3) DEFAULT NULL COMMENT '优先级',
  `starttime` int(11) DEFAULT NULL COMMENT '开始时间',
  `endtime` int(11) DEFAULT NULL COMMENT '结束时间',
  `member_lv_id` varchar(100) DEFAULT '1' COMMENT '会员级别',
  `favorable` int(10) unsigned DEFAULT NULL COMMENT '优惠条件(1:商品类型2指定商品3商品分类4所有商品)',
  `yhcase` int(10) unsigned DEFAULT NULL COMMENT '优惠方案',
  `yhcase_value` varchar(100) DEFAULT NULL COMMENT '优惠方案的值',
  `add_time` int(11) DEFAULT NULL COMMENT '添加时间',
  `yhcase_value2` int(11) NOT NULL DEFAULT '0' COMMENT '优惠方案值2',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
