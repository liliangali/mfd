CREATE TABLE IF NOT EXISTS `cf_pet` (
  `pet_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '宠物id',
  `user_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户id',
  `name` varchar(60) NOT NULL COMMENT '宠物名称',
  `gender` tinyint(3) NOT NULL DEFAULT '0' COMMENT '性别',
  `region_id` varchar(100) DEFAULT NULL COMMENT '属性id（逗号连接的字符串）',
  `region_name` varchar(255) DEFAULT NULL COMMENT '3级属性名称',
  `summary` varchar(255) NOT NULL COMMENT '简介',
  `birthday` date DEFAULT NULL COMMENT '出生日期',
  `url` varchar(255) DEFAULT NULL COMMENT '头像地址',
  PRIMARY KEY (`pet_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='宠物管理';
