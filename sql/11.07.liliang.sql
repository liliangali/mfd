ALTER TABLE `cf_cart` ADD `body_condition` INT NOT NULL DEFAULT '0' COMMENT '体况id' ;
ALTER TABLE `cf_cart` ADD `run_time` INT NOT NULL DEFAULT '0' COMMENT '运动量id' ;
ALTER TABLE `cf_cart` CHANGE `weight` `weight` DECIMAL(10,2) NOT NULL DEFAULT '0.00' COMMENT '体重';
ALTER TABLE `cf_cart` ADD `time_id` INT NOT NULL DEFAULT '0' COMMENT '时间id' ;


ALTER TABLE `cf_order_goods` ADD `body_condition` INT NOT NULL DEFAULT '0' COMMENT '体况id' ;
ALTER TABLE `cf_order_goods` ADD `run_time` INT NOT NULL DEFAULT '0' COMMENT '运动量id' ;
ALTER TABLE `cf_order_goods` CHANGE `weight` `weight` DECIMAL(10,2) NOT NULL DEFAULT '0.00' COMMENT '体重';
ALTER TABLE `cf_order_goods` ADD `time_id` INT NOT NULL DEFAULT '0' COMMENT '时间id' ;

