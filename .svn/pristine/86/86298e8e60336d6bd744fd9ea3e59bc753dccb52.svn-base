ALTER TABLE `cf_order_promotion_rules`
MODIFY COLUMN `member_lv_id`  varchar(100) NULL DEFAULT 1 COMMENT '会员级别' AFTER `endtime`;

ALTER TABLE `cf_order_promotion_rules`
ADD COLUMN `ex_rule`  varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '被排它的 规则id' AFTER `if_ex`;



ALTER TABLE `cf_goods_promotion_rules`
MODIFY COLUMN `member_lv_id`  varchar(100) NULL DEFAULT 1 COMMENT '会员级别' AFTER `endtime`,
ADD COLUMN `ex_rule`  varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '被排它的 规则id ' AFTER `if_ex`;



ALTER TABLE `cf_satnav`
ADD COLUMN `parent_id`  int(10) NULL DEFAULT 0 COMMENT '分类父id' AFTER `add_time`;

