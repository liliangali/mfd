/*
Navicat MySQL Data Transfer

Source Server         : www
Source Server Version : 50617
Source Host           : localhost:3306
Source Database       : mfdplatform

Target Server Type    : MYSQL
Target Server Version : 50617
File Encoding         : 65001

Date: 2016-05-21 08:40:13
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for cf_goods_promotion_rules

CREATE TABLE `cf_goods_promotion_rules` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主健id',
  `name` varchar(100) NOT NULL COMMENT '规则名称',
  `introduce` varchar(255) DEFAULT NULL COMMENT '简介',
  `is_open` tinyint(3) DEFAULT '1' COMMENT '是否启用（1是0否）',
  `level` tinyint(3) DEFAULT NULL COMMENT '优先级',
  `site_id` varchar(50) DEFAULT '1' COMMENT 'pc段：1  app：2 wap 3',
  `if_ex` tinyint(3) DEFAULT '0' COMMENT '是否排他 否 0 是 1',
  `starttime` int(11) DEFAULT NULL COMMENT '开始时间',
  `endtime` int(11) DEFAULT NULL COMMENT '结束时间',
  `member_lv_id` mediumint(8) unsigned DEFAULT '1' COMMENT '会员级别',
  `favorable` int(10) unsigned DEFAULT NULL COMMENT '优惠条件(1:商品类型2指定商品3商品分类4所有商品)',
  `yhcase` int(10) unsigned DEFAULT NULL COMMENT '优惠方案',
  `yhcase_value` varchar(100) DEFAULT NULL COMMENT '优惠方案的值',
  `add_time` int(11) DEFAULT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;


CREATE TABLE `cf_goods_promotion_link` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `rules_id` int(10) NOT NULL COMMENT '促销活动id',
  `favorable_id` int(10) NOT NULL COMMENT '促销条件id',
  `favorable_value` varchar(255) DEFAULT NULL COMMENT '活动条件的值',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



CREATE TABLE `cf_order_promotion_rules` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主健id',
  `name` varchar(100) NOT NULL COMMENT '规则名称',
  `introduce` varchar(255) DEFAULT NULL COMMENT '简介',
  `is_open` tinyint(3) DEFAULT '1' COMMENT '是否启用（1是0否）',
  `level` tinyint(3) DEFAULT NULL COMMENT '优先级',
  `site_id` varchar(50) DEFAULT '1' COMMENT 'pc段：1  app：2 wap 3',
  `if_ex` tinyint(3) DEFAULT '0' COMMENT '是否排他 否 0 是 1',
  `starttime` int(11) DEFAULT NULL COMMENT '开始时间',
  `endtime` int(11) DEFAULT NULL COMMENT '结束时间',
  `member_lv_id` mediumint(8) unsigned DEFAULT '1' COMMENT '会员级别',
  `favorable` int(10) unsigned DEFAULT NULL COMMENT '1当订单商品总价满X时，对所有商品优惠:2当订单商品数量满X时，给予优惠3对所有订单给予优惠',
  `favorable_value` varchar(100) DEFAULT NULL COMMENT '优惠条件的值',
  `yhcase` int(10) unsigned DEFAULT NULL COMMENT '优惠方案',
  `yhcase_value` varchar(100) DEFAULT NULL COMMENT '优惠方案的值',
  `add_time` int(11) DEFAULT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8;