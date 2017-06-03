-- phpMyAdmin SQL Dump
-- version phpStudy 2014
-- http://www.phpmyadmin.net
--
-- 主机: localhost
-- 生成日期: 2016 广05 朿19 旿11:29
-- 服务器版本: 5.5.40
-- PHP 版本: 5.6.1

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- 数据库: `mfd`
--

-- --------------------------------------------------------

--
-- 表的结构 `diy_custom_attr`
-- 
DROP TABLE IF EXISTS `cf_goods_attr`;
CREATE TABLE IF NOT EXISTS `cf_goods_attr` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `attr_id` int(10) NOT NULL COMMENT '属性id',
  `attr_value` varchar(200) NOT NULL COMMENT '属性值',
  `goods_id` int(10) NOT NULL COMMENT '商品id',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='基础数据-样衣关联属性表' AUTO_INCREMENT=469 ;

-- --------------------------------------------------------

--
-- 表的结构 `diy_custom_attribute`
--
DROP TABLE IF EXISTS `cf_goods_attribute`;
CREATE TABLE IF NOT EXISTS `cf_goods_attribute` (
  `attr_id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `attr_name` varchar(200) NOT NULL COMMENT '属性名称',
  `type_id` int(10) NOT NULL COMMENT '类型id',
  `attr_values` varchar(200) NOT NULL COMMENT '属性值逗号分隔[1,2,3]',
  `sort_order` int(10) NOT NULL COMMENT '排序',
  `alias` varchar(200) NOT NULL COMMENT '别名',
  PRIMARY KEY (`attr_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='基础数据-样衣属性表' AUTO_INCREMENT=9 ;
ALTER TABLE  `cf_products` CHANGE  `is_default`  `is_default` TINYINT NOT NULL DEFAULT  '0' COMMENT  '0不是默认规格 1是默认规格';
ALTER TABLE  `cf_gcategory` ADD  `type_id` INT NOT NULL DEFAULT  '0' COMMENT  '类型id';
ALTER TABLE  `cf_products` ADD  `freez` MEDIUMINT( 8 ) NOT NULL DEFAULT  '0' COMMENT  '冻结库存';
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
