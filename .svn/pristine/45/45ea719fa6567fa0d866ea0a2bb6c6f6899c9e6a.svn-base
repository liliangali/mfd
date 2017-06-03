-- ----------------------------
-- Table structure for cf_shuffling
-- ----------------------------
CREATE TABLE `cf_satnav` (
  `satnav_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '导航名称',
  `title` varchar(100) NOT NULL DEFAULT '' COMMENT '标题',
  `cate_id` int(10) NOT NULL DEFAULT '0' COMMENT '分类',
  `link` varchar(255) DEFAULT NULL COMMENT '链接',
  `lcon` tinyint(3) unsigned DEFAULT '0' COMMENT '图标(0 无图标 1 最新 2 最热)',
  `sort_order` tinyint(3) unsigned NOT NULL DEFAULT '255',
  `alone` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '是否独立页面显示(0 否 1 是)',
  `if_show` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '是否显示',
  `add_time` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`satnav_id`),
  KEY `cate_id` (`cate_id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=171 DEFAULT CHARSET=utf8 COMMENT='主导航表';



-- ----------------------------
-- Table structure for cf_satnavcat
-- ----------------------------
CREATE TABLE `cf_satnavcat` (
  `cate_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `cate_name` varchar(100) NOT NULL DEFAULT '' COMMENT '分类名称',
  `parent_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '分类父id',
  `sort_order` tinyint(3) unsigned NOT NULL DEFAULT '255' COMMENT '排序',
  `if_show` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否显示',
  PRIMARY KEY (`cate_id`),
  KEY `store_id` (`parent_id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=utf8 COMMENT='导航分类表';
