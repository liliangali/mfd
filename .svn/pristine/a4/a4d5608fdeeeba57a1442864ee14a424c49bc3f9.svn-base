

CREATE TABLE `cf_goods_promotion_relation` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `d_id` int(10) NOT NULL COMMENT '商品促销规则id',
  `c_id` int(10) NOT NULL COMMENT '商品id',
  `lorder` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='促销规则关联商品表';








ALTER TABLE `cf_goods_promotion_link`
ADD COLUMN `type`  tinyint(3) NULL DEFAULT 0 COMMENT '0: 普通商品 1:diy商品' AFTER `favorable_value`;
