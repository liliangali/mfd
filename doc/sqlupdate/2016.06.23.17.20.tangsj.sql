ALTER TABLE `uc_members`
MODIFY COLUMN `username`  varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' AFTER `uid`;



ALTER TABLE `uc_members`
ADD COLUMN `type`  int(10) NULL DEFAULT 0 COMMENT '类型 1：qq  2： 微信  3：微博 4 手机号 ' AFTER `secques`;



ALTER TABLE `cf_member`
ADD COLUMN `type`  int(10) UNSIGNED NULL DEFAULT 0 COMMENT '类型 1：qq  2： 微信  3：微博 4 手机号 ' AFTER `openid`;
