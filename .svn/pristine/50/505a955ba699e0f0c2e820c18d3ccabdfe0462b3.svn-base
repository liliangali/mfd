/*
SQLyog v10.2 
MySQL - 5.5.40 : Database - ecoss
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
/*Table structure for table `cf_goods` */
DROP TABLE IF EXISTS `cf_goods`;


DROP TABLE IF EXISTS `cf_goods`;

CREATE TABLE `cf_goods` (
  `goods_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '商品ID',
  `bn` varchar(200) DEFAULT NULL COMMENT '商品编号',
  `name` varchar(200) NOT NULL DEFAULT '' COMMENT '商品名称',
  `price` decimal(20,3) NOT NULL DEFAULT '0.000' COMMENT '销售价',
  `type_id` mediumint(8) unsigned DEFAULT NULL COMMENT '类型',
  `cat_id` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '分类',
  `brand_id` mediumint(8) unsigned DEFAULT NULL COMMENT '品牌',
  `marketable` enum('true','false') NOT NULL DEFAULT 'true' COMMENT '上架',
  `store` mediumint(8) unsigned DEFAULT '0' COMMENT '库存',
  /*`notify_num` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '缺货登记',*/
  `uptime` int(10) unsigned DEFAULT NULL COMMENT '上架时间',
  `downtime` int(10) unsigned DEFAULT NULL COMMENT '下架时间',
  `last_modify` int(10) unsigned DEFAULT NULL COMMENT '更新时间',
  `p_order` mediumint(8) unsigned NOT NULL DEFAULT '30' COMMENT '排序',
 /* `d_order` mediumint(8) unsigned NOT NULL DEFAULT '30' COMMENT '动态排序',*/
  `score` mediumint(8) unsigned DEFAULT NULL COMMENT '积分',
 /* `cost` decimal(20,3) NOT NULL DEFAULT '0.000' COMMENT '成本价',*/
  `mktprice` decimal(20,3) DEFAULT NULL COMMENT '市场价',
  `weight` decimal(20,3) DEFAULT NULL COMMENT '重量',
  `unit` varchar(20) DEFAULT NULL COMMENT '单位',
  `brief` varchar(255) DEFAULT NULL COMMENT '商品简介',
  /*`goods_type` enum('normal','bind','gift') NOT NULL DEFAULT 'normal' COMMENT '销售类型',*/
  /*`image_default_id` varchar(32) DEFAULT NULL COMMENT '默认图片',*/
 /* `udfimg` enum('true','false') DEFAULT 'false' COMMENT '是否用户自定义图',*/
  `thumbnail_pic` varchar(32) DEFAULT NULL COMMENT '缩略图',
 /* `small_pic` varchar(255) DEFAULT NULL COMMENT '小图',*/
  `big_pic` varchar(255) DEFAULT NULL COMMENT '大图',
  `intro` longtext COMMENT '详细介绍',
  /*`store_place` varchar(255) DEFAULT NULL COMMENT '库位',
  `min_buy` mediumint(8) unsigned DEFAULT NULL COMMENT '起定量',
  `package_scale` decimal(20,2) DEFAULT NULL COMMENT '打包比例',
  `package_unit` varchar(20) DEFAULT NULL COMMENT '打包单位',
  `package_use` enum('0','1') DEFAULT NULL COMMENT '是否开启打包',
  `score_setting` enum('percent','number') DEFAULT 'number',
  `store_prompt` mediumint(8) unsigned DEFAULT NULL COMMENT '库存提示规则',
  `nostore_sell` enum('0','1') DEFAULT '0' COMMENT '是否开启无库存销售',
  `goods_setting` longtext COMMENT '商品设置',
  `spec_desc` longtext COMMENT '货品规格序列化',
  `params` longtext COMMENT '商品规格序列化',*/
  `disabled` enum('true','false') NOT NULL DEFAULT 'false',
  `rank_count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'google page rank count',
  `comments_count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '评论次数',
 /* `view_w_count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '周浏览次数',*/
  `view_count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '浏览次数',
 /* `count_stat` longtext COMMENT '统计数据序列化',*/
  `buy_count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '购买次数',
 /* `buy_w_count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '购买次数',*/
  PRIMARY KEY (`goods_id`),
  UNIQUE KEY `uni_bn` (`bn`),
  KEY `ind_frontend` (`disabled`,`marketable`),
  KEY `idx_marketable` (`marketable`)
) ENGINE=InnoDB AUTO_INCREMENT=100 DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS `cf_goods_type`;

CREATE TABLE `cf_goods_type` (
  `type_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '类型序号',
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '类型名称',
  /*`floatstore` enum('0','1') NOT NULL DEFAULT '0' COMMENT '小数型库存',*/
  `alias` longtext COMMENT '类型别名(|分隔,前后|)',
  /*`is_physical` enum('0','1') NOT NULL DEFAULT '1' COMMENT '实体商品',
  `schema_id` varchar(30) NOT NULL DEFAULT 'custom' COMMENT '供应商序号',
  `setting` longtext COMMENT '类型设置',
  `price` longtext COMMENT '设置价格区间，用于列表页搜索使用',
  `minfo` longtext COMMENT '用户购买时所需输入信息的字段定义序列化数组方式 array(字段名,字段含义,类型(input,select,radio))',
  `params` longtext COMMENT '参数表结构(序列化) array(参数组名=>array(参数名1=>别名1|别名2,参数名2=>别名1|别名2))',
  `tab` longtext COMMENT '商品详情页的自定义tab设置',
  `dly_func` enum('0','1') NOT NULL DEFAULT '0' COMMENT '是否包含发货函数',
  `ret_func` enum('0','1') NOT NULL DEFAULT '0' COMMENT '是否包含退货函数',
  `reship` enum('disabled','func','normal','mixed') NOT NULL DEFAULT 'normal' COMMENT '退货方式 disabled:不允许退货 func:函数式',*/
  `disabled` enum('true','false') DEFAULT 'false',
 /* `is_def` enum('true','false') NOT NULL DEFAULT 'false' COMMENT '是否系统默认',
  `lastmodify` int(10) unsigned DEFAULT NULL COMMENT '上次修改时间',*/
  PRIMARY KEY (`type_id`),
  KEY `ind_disabled` (`disabled`) 
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

/*Table structure for table `cf_products` */

DROP TABLE IF EXISTS `cf_products`;

CREATE TABLE `cf_products` (
  `product_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '货品ID',
  `goods_id` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '商品ID',
  /*`barcode` varchar(128) DEFAULT NULL COMMENT '条码',*/
  `title` varchar(255) DEFAULT NULL COMMENT '标题',
  `bn` varchar(30) DEFAULT NULL COMMENT '货号',
  `price` decimal(20,3) NOT NULL DEFAULT '0.000' COMMENT '销售价格',
  `cost` decimal(20,3) NOT NULL DEFAULT '0.000' COMMENT '成本价',
  `mktprice` decimal(20,3) DEFAULT NULL COMMENT '市场价',
  `name` varchar(200) NOT NULL DEFAULT '' COMMENT '货品名称',
  `weight` decimal(20,3) DEFAULT NULL COMMENT '单位重量',
  `unit` varchar(20) DEFAULT NULL COMMENT '单位',
  `store` mediumint(8) unsigned DEFAULT '0' COMMENT '库存',
  /*`store_place` varchar(255) DEFAULT NULL COMMENT '库位',*/
  /*`freez` mediumint(8) unsigned DEFAULT NULL COMMENT '冻结库存',*/
  /*`goods_type` enum('normal','bind','gift') NOT NULL DEFAULT 'normal' COMMENT '销售类型',*/
  `spec_info` longtext COMMENT '物品描述',
  `spec_desc` longtext COMMENT '规格值,序列化',
  `is_default` enum('true','false') NOT NULL DEFAULT 'false',
/*  `qrcode_image_id` varchar(32) DEFAULT NULL COMMENT '二维码图片ID',*/
  `uptime` int(10) unsigned DEFAULT NULL COMMENT '录入时间',
  `last_modify` int(10) unsigned DEFAULT NULL COMMENT '最后修改时间',
  `disabled` enum('true','false') DEFAULT 'false',
  /*`marketable` enum('true','false') NOT NULL DEFAULT 'true' COMMENT '上架',*/
  PRIMARY KEY (`product_id`),
  UNIQUE KEY `ind_bn` (`bn`),
  KEY `ind_goods_id` (`goods_id`),
  KEY `ind_disabled` (`disabled`),
 /* KEY `ind_barcode` (`barcode`),*/
  /*KEY `idx_goods_type` (`goods_type`),*/
  KEY `idx_store` (`store`)
) ENGINE=InnoDB AUTO_INCREMENT=560 DEFAULT CHARSET=utf8;

/*Table structure for table `cf_spec_values` */

DROP TABLE IF EXISTS `cf_spec_values`;

CREATE TABLE `cf_spec_values` (
  `spec_value_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '规格值ID',
  `spec_id` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '规格ID',
  `spec_value` varchar(100) NOT NULL DEFAULT '' COMMENT '规格值',
  `alias` varchar(255) DEFAULT '' COMMENT '规格别名',
  `spec_image` char(32) DEFAULT '' COMMENT '规格图片',
  `p_order` mediumint(8) unsigned NOT NULL DEFAULT '50' COMMENT '排序',
  PRIMARY KEY (`spec_value_id`)
) ENGINE=InnoDB AUTO_INCREMENT=63 DEFAULT CHARSET=utf8;

/*Table structure for table `cf_specification` */

DROP TABLE IF EXISTS `cf_specification`;

CREATE TABLE `cf_specification` (
  `spec_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '规格id',
  `spec_name` varchar(50) NOT NULL DEFAULT '' COMMENT '规格名称',
  /*`is_money` varchar(50) NOT NULL DEFAULT 1 COMMENT '是否销售属性(1是 0否)',*/
  `spec_show_type` enum('select','flat') NOT NULL DEFAULT 'flat' COMMENT '显示方式',
  `spec_type` enum('text','image') NOT NULL DEFAULT 'text' COMMENT '类型',
  `spec_memo` varchar(50) NOT NULL DEFAULT '' COMMENT '规格备注',
  `p_order` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `disabled` enum('true','false') NOT NULL DEFAULT 'false',
  `alias` varchar(255) DEFAULT '' COMMENT '规格别名',
  PRIMARY KEY (`spec_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
