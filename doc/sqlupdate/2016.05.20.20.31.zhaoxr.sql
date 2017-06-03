ALTER TABLE `cf_acategory`
MODIFY COLUMN `code`  varchar(60) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '唯一标记(唯一)' AFTER `sort_order`;