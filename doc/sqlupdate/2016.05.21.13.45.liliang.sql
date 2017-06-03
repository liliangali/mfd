

CREATE TABLE IF NOT EXISTS `cf_goods_type_spec` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `spec_id` int(11) NOT NULL COMMENT '规格ID',
  `type_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

ALTER TABLE  `cf_spec_values` CHANGE  `spec_image`  `spec_image` VARCHAR( 2250 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT  '规格图片';
ALTER TABLE  `cf_goods` ADD  `is_pc` TINYINT NOT NULL DEFAULT  '0' COMMENT  '发往PC平台';
ALTER TABLE  `cf_goods` ADD  `is_wap` TINYINT NOT NULL DEFAULT  '0' COMMENT  '发往wap平台';
ALTER TABLE  `cf_goods` ADD  `is_app` TINYINT NOT NULL DEFAULT  '0' COMMENT  '发往app平台';
ALTER TABLE  `cf_goods` ADD  `is_debit` TINYINT NOT NULL DEFAULT  '0' COMMENT  '是否使用优惠券';
ALTER TABLE  `cf_goods_attribute` ADD  `attr_input_type` TINYINT NOT NULL DEFAULT  '0' COMMENT  '0手工录  1选择项 ';




 