/*
SQLyog v10.2 
MySQL - 5.5.40 : Database - mfd
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
/*Table structure for table `cf_shipping` */

DROP TABLE IF EXISTS `cf_shipping`;

CREATE TABLE `cf_shipping` (
  `shipping_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `shipping_name` varchar(100) NOT NULL DEFAULT '' COMMENT '快递名称',
  `shipping_desc` varchar(255) DEFAULT NULL,
  `first_weight` int(10) NOT NULL,
  `step_weight` int(10) NOT NULL,
  `enabled` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '1启用  0禁用',
  `sort_order` tinyint(3) unsigned NOT NULL DEFAULT '255',
  `code` varchar(50) NOT NULL COMMENT '快递编号',
  `first_money` decimal(10,2) NOT NULL COMMENT '首重运费',
  `step_money` decimal(10,2) NOT NULL COMMENT '增重运费',
  `is_default` tinyint(4) NOT NULL DEFAULT '0' COMMENT '1默认 0不是默认',
  PRIMARY KEY (`shipping_id`)
) ENGINE=MyISAM AUTO_INCREMENT=38 DEFAULT CHARSET=utf8;

/*Data for the table `cf_shipping` */

LOCK TABLES `cf_shipping` WRITE;

insert  into `cf_shipping`(`shipping_id`,`shipping_name`,`shipping_desc`,`first_weight`,`step_weight`,`enabled`,`sort_order`,`code`,`first_money`,`step_money`,`is_default`) values (34,'发放asd1',NULL,101,221,1,1,'发放asd1','96.01','10.01',0);
insert  into `cf_shipping`(`shipping_id`,`shipping_name`,`shipping_desc`,`first_weight`,`step_weight`,`enabled`,`sort_order`,`code`,`first_money`,`step_money`,`is_default`) values (36,'342',NULL,32,23,1,255,'11222','23.00','3.00',1);

UNLOCK TABLES;

/*Table structure for table `cf_shipping_area` */

DROP TABLE IF EXISTS `cf_shipping_area`;

CREATE TABLE `cf_shipping_area` (
  `area_id` int(10) NOT NULL AUTO_INCREMENT COMMENT '配送区域ID',
  `first_price` decimal(10,2) NOT NULL COMMENT '首重费用',
  `step_price` decimal(10,2) NOT NULL COMMENT '续重费用',
  `region_id` int(11) NOT NULL COMMENT '地区id',
  `shipping_id` int(10) NOT NULL COMMENT '配送方式ID',
  `first_weight` int(11) NOT NULL COMMENT '首重',
  `step_weight` int(11) NOT NULL DEFAULT '0' COMMENT '增重',
  `fr` varchar(2250) DEFAULT NULL COMMENT '唯一标示',
  PRIMARY KEY (`area_id`)
) ENGINE=MyISAM AUTO_INCREMENT=278 DEFAULT CHARSET=utf8;

/*Data for the table `cf_shipping_area` */

LOCK TABLES `cf_shipping_area` WRITE;

insert  into `cf_shipping_area`(`area_id`,`first_price`,`step_price`,`region_id`,`shipping_id`,`first_weight`,`step_weight`,`fr`) values (258,'45.00','54.00',59,36,54,54,'54,45.00,54,54.00');
insert  into `cf_shipping_area`(`area_id`,`first_price`,`step_price`,`region_id`,`shipping_id`,`first_weight`,`step_weight`,`fr`) values (259,'45.00','54.00',76,36,54,54,'54,45.00,54,54.00');
insert  into `cf_shipping_area`(`area_id`,`first_price`,`step_price`,`region_id`,`shipping_id`,`first_weight`,`step_weight`,`fr`) values (197,'44.01','44.01',64,34,10,5,'10,44.01,5,44.01');
insert  into `cf_shipping_area`(`area_id`,`first_price`,`step_price`,`region_id`,`shipping_id`,`first_weight`,`step_weight`,`fr`) values (196,'3.01','5.01',107,34,1,4,'1,3.01,4,5.01');
insert  into `cf_shipping_area`(`area_id`,`first_price`,`step_price`,`region_id`,`shipping_id`,`first_weight`,`step_weight`,`fr`) values (195,'3.01','5.01',111,34,1,4,'1,3.01,4,5.01');

UNLOCK TABLES;

ALTER TABLE  `cf_goods` CHANGE  `marketable`  `marketable` TINYINT NOT NULL DEFAULT  '1' COMMENT  '上架';
ALTER TABLE `cf_goods_gallery` ADD `sort` INT(10) NOT NULL COMMENT '图片排序' AFTER `img_original`;



/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
