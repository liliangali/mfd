-- ----------------------------
-- Table structure for cf_pet_type
-- ----------------------------
DROP TABLE IF EXISTS `cf_pet_type`;
CREATE TABLE `cf_pet_type` (
  `type_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '种类ID',
  `type_name` varchar(100) NOT NULL DEFAULT '' COMMENT '种类名称',
  `parent_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '父类id',
  `sort_order` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  PRIMARY KEY (`type_id`),
  KEY `parent_id` (`parent_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='宠物种类管理';
