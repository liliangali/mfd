SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for cf_search_record
-- ----------------------------
DROP TABLE IF EXISTS `cf_search_record`;
CREATE TABLE `cf_search_record` (
  `sid` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '搜索id',
  `value` varchar(100) NOT NULL COMMENT '搜索内容',
  `user_id` int(10) unsigned NOT NULL COMMENT '最近搜索的用户id',
  `num` int(10) unsigned NOT NULL DEFAULT '1' COMMENT '搜索次数',
  `new_time` int(11) unsigned NOT NULL COMMENT '最近添加时间',
  `sort_order` int(11) unsigned DEFAULT NULL COMMENT '排序',
  PRIMARY KEY (`sid`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='用户搜索记录表';
