-- ----------------------------
-- Table structure for cf_shuffling
-- ----------------------------
DROP TABLE IF EXISTS `cf_shuffling`;
CREATE TABLE `cf_shuffling` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`name`  varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '名称' ,
`title`  varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '轮播图title（鼠标悬浮显示，默认与名称相同）' ,
`site_id`  tinyint(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '发布平台(1pc2app3wap)' ,
`groups`  tinyint(3) UNSIGNED NOT NULL COMMENT '分组(发往平台)' ,
`link_url`  varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '链接url（去除http://部分）' ,
`is_blank`  tinyint(1) UNSIGNED NULL DEFAULT 0 COMMENT '是否新窗口打开(pc端1是0否)' ,
`img`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '图片路径' ,
`sort_order`  smallint(5) UNSIGNED NULL DEFAULT NULL COMMENT '排序（值越大越靠前，为空则按添加顺序自动排列）' ,
`status`  tinyint(1) NOT NULL COMMENT '状态（1启用0停用）' ,
`add_time`  int(11) UNSIGNED NOT NULL COMMENT '添加时间' ,
`edit_time`  int(11) UNSIGNED NULL DEFAULT NULL COMMENT '修改时间' ,
PRIMARY KEY (`id`)
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
COMMENT='轮播图管理'
AUTO_INCREMENT=1

;
-- ----------------------------
-- Table structure for cf_shuffling_group
-- ----------------------------
DROP TABLE IF EXISTS `cf_shuffling_group`;
CREATE TABLE `cf_shuffling_group` (
`id`  int(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
`name`  varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '分组名称' ,
`site_id`  tinyint(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '发布平台（1pc2app3wap）' ,
`code`  varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '轮播图分组标记（唯一）' ,
`add_time`  int(11) NOT NULL COMMENT '添加时间' ,
PRIMARY KEY (`id`)
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
COMMENT='轮播图分组管理'
AUTO_INCREMENT=1

;
