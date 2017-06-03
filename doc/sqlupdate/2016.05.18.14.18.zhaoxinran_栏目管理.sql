/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50709
Source Host           : localhost:3306
Source Database       : project_mfdplatform

Target Server Type    : MYSQL
Target Server Version : 50709
File Encoding         : 65001

Date: 2016-05-18 14:47:03
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for cf_motif
-- ----------------------------
DROP TABLE IF EXISTS `cf_motif`;
CREATE TABLE `cf_motif` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`title`  varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '栏目标题' ,
`title_switch`  tinyint(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '标题开关（1启用 0禁用）' ,
`subhead`  varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '栏目副标题' ,
`subhead_switch`  tinyint(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '副标题开关（1启用 0禁用）' ,
`subhead_link`  varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '副标题链接（url参数）' ,
`code`  varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '栏目标记' ,
`site_id`  tinyint(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '发布平台（1pc2app3wap）' ,
`location_id`  smallint(5) UNSIGNED NOT NULL COMMENT '位置归属(发往平台)' ,
`sort_order`  smallint(5) UNSIGNED NULL DEFAULT NULL COMMENT '排序（值越大越靠前，为空则按添加顺序自动排列）' ,
`is_show`  tinyint(1) NOT NULL COMMENT '状态（1启用0停用）' ,
`add_time`  int(11) UNSIGNED NOT NULL COMMENT '添加时间' ,
`edit_time`  int(11) UNSIGNED NULL DEFAULT NULL COMMENT '修改时间' ,
`is_delete`  tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '非物理删除，只是调取不出来，可以恢复（1删除 0不删）' ,
PRIMARY KEY (`id`)
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
COMMENT='栏目管理'
AUTO_INCREMENT=1

;

-- ----------------------------
-- Table structure for cf_motif_group
-- ----------------------------
DROP TABLE IF EXISTS `cf_motif_group`;
CREATE TABLE `cf_motif_group` (
`id`  int(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
`name`  varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '分组名称' ,
`code`  varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '位置标记（唯一）' ,
`site_id`  tinyint(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '发布平台(1pc2app3wap)' ,
`add_time`  int(11) NOT NULL COMMENT '添加时间' ,
PRIMARY KEY (`id`)
)
ENGINE=MyISAM
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
COMMENT='栏目位置管理'
AUTO_INCREMENT=1

;

-- ----------------------------
-- Table structure for cf_motif_rel_content
-- ----------------------------
DROP TABLE IF EXISTS `cf_motif_rel_content`;
CREATE TABLE `cf_motif_rel_content` (
`id`  int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '栏目内容id' ,
`parent_id`  int(11) UNSIGNED NOT NULL COMMENT '内容关联栏目id（多对1）' ,
`img`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '栏目内容图片' ,
`link_url`  varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '#' COMMENT '内容链接(自带http://+域名)' ,
`sort_order`  smallint(5) UNSIGNED NULL DEFAULT NULL COMMENT '排序（越大越靠前）' ,
`is_show`  tinyint(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '是否显示（1是0否）' ,
PRIMARY KEY (`id`)
)
ENGINE=MyISAM
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
COMMENT='栏目关联内容表'
AUTO_INCREMENT=1

;


