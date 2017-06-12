
######新一轮开始
ALTER TABLE `cf_member` CHANGE `single` `single` SMALLINT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '渠道会员折扣百分比';
ALTER TABLE `cf_member` CHANGE `is_service` `is_service` TINYINT(11) NOT NULL DEFAULT '0' COMMENT '0渠道未审核 1审核成功 2审核失败';
ALTER TABLE `cf_member` CHANGE `member_type` `member_type` TINYINT(4) NOT NULL DEFAULT '0' COMMENT '0普通会员 1个人渠道人员 2公司渠道人员';
ALTER TABLE `cf_member` CHANGE `memo` `memo` VARCHAR(2250) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '渠道绑定的微信账号';
ALTER TABLE `cf_member` CHANGE `erweimaUrl` `erweimaUrl` VARCHAR(1250) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '渠道会员用来自我绑定的二维码url';
DROP TABLE IF EXISTS `cf_carticles`;
CREATE TABLE `cf_carticles` (
  `id` int(11) NOT NULL,
  `title` varchar(2250) NOT NULL COMMENT '文章标题',
  `content` text NOT NULL COMMENT '文章内容',
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '0:不显示   1:显示'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
ALTER TABLE `cf_carticles`
  ADD PRIMARY KEY (`id`);
ALTER TABLE `cf_carticles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;


DROP TABLE IF EXISTS `cf_channel_infos`;
CREATE TABLE `cf_channel_infos` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(11) NOT NULL COMMENT '会员表的主键ID',
  `jigou` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '组织机构代码',
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '商铺名称',
  `region` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '商铺地址主键',
  `region_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '商铺地址名称',
  `address` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '商铺详细地址',
  `image_url` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '商铺营业执照',
  `cstatus` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0' COMMENT '0:为审核失败,1:审核成功',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
ALTER TABLE `cf_channel_infos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `channel_infos_user_id_unique` (`user_id`);
ALTER TABLE `cf_channel_infos`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;


DROP TABLE IF EXISTS `cf_ch_discounts`;
CREATE TABLE `cf_ch_discounts` (
  `id` int(11) NOT NULL,
  `type` varchar(1250) NOT NULL COMMENT '类型',
  `dval` varchar(225) NOT NULL DEFAULT '' COMMENT '折扣',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `cf_ch_discounts`
  ADD PRIMARY KEY (`id`);
ALTER TABLE `cf_ch_discounts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;


DROP TABLE IF EXISTS `cf_member_banks`;
CREATE TABLE `cf_member_banks` (
  `id` int(11) NOT NULL COMMENT '自动增长',
  `user_id` int(11) NOT NULL,
  `card_name` varchar(255) NOT NULL COMMENT '开户姓名',
  `bank_card` varchar(255) NOT NULL COMMENT '卡号',
  `bank_address` varchar(255) NOT NULL COMMENT '开户行地址',
  `status` int(11) NOT NULL DEFAULT '0' COMMENT '是否启用：1：启用 0：禁用',
  `bank` varchar(255) NOT NULL COMMENT '银行名称',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='绑定银行卡' ROW_FORMAT=COMPACT;

ALTER TABLE `cf_member_banks`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `cf_member_banks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自动增长';


  DROP TABLE IF EXISTS `cf_order_cash_log`;
CREATE TABLE `cf_order_cash_log` (
  `id` int(11) NOT NULL COMMENT '主键',
  `order_id` varchar(125) NOT NULL COMMENT '订单编号',
  `user_id` int(11) NOT NULL COMMENT '返现人的user_id',
  `share` varchar(32) NOT NULL DEFAULT '0' COMMENT '本次返现比例',
  `cash_money` decimal(10,2) NOT NULL COMMENT '本次返现金额',
  `add_time` int(11) NOT NULL COMMENT '记录时间',
  `order_money` decimal(10,2) NOT NULL COMMENT '本次订单金额'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
ALTER TABLE `cf_order_cash_log`
  ADD PRIMARY KEY (`id`);
ALTER TABLE `cf_order_cash_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键';


  CREATE TABLE `cf_laravel_sms` (
  `id` int(10) UNSIGNED NOT NULL,
  `to` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `temp_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `data` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `content` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `voice_code` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `fail_times` mediumint(9) NOT NULL DEFAULT '0',
  `last_fail_time` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `sent_time` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `result_info` text COLLATE utf8_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
ALTER TABLE `cf_laravel_sms`
  ADD PRIMARY KEY (`id`);
ALTER TABLE `cf_laravel_sms`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;


DROP TABLE IF EXISTS `cf_cash`;
CREATE TABLE `cf_cash` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL COMMENT '提现的会员id',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0提交申请 1成功 2失败  3已打款',
  `bank_address` varchar(255) NOT NULL COMMENT '开户行及地址',
  `bank_name` varchar(255) NOT NULL DEFAULT '' COMMENT '银行所在地',
  `bank_id` varchar(225) NOT NULL COMMENT '银行名称',
  `bank_card` varchar(255) NOT NULL COMMENT '银行卡号',
  `cash_money` decimal(10,2) NOT NULL COMMENT '提现金额',
  `msg` varchar(1250) NOT NULL COMMENT '如果客服审核失败  这里填写失败原因',
  `audit_time` int(11) NOT NULL COMMENT '客服审核时间',
  `admin_id` int(11) DEFAULT NULL COMMENT '审核人 id ',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `card_name` varchar(255) DEFAULT NULL COMMENT '提现人姓名'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='酷特 提现 记录表' ROW_FORMAT=COMPACT;
ALTER TABLE `cf_cash`
  ADD PRIMARY KEY (`id`);
ALTER TABLE `cf_cash`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;


cf_carticles
cf_channel_info
cf_ch_discount
cf_member_banks
cf_order_cash_log


海报图  发多分





paynotifys-notify-201705235305
############新一轮结束

##################渠道返还金额
h5/includes/models/chdiscounts.model.php
includes/models/chdiscounts.model.php
h5/includes/ordertypes/news.otype.php
includes/libraries/shopCommon.php

###########dingdo文件
dingo/app/Api/V1/Controllers/Admin
dingo/app/Api/V1/Controllers/Admin/Auth
dingo/app/Api/V1/Controllers/Admin/Auth/routes.php
dingo/app/Api/V1/Controllers/Admin/Auth/AuthController.php
dingo/app/Api/V1/Controllers/Admin/Order
dingo/app/Api/V1/Controllers/Admin/Order/routes.php
dingo/app/Api/V1/Controllers/Admin/Order/OrderController.php
dingo/app/Api/V1/Controllers/Admin/User
dingo/app/Api/V1/Controllers/Admin/User/routes.php
dingo/app/Api/V1/Controllers/Admin/User/UserController.php
dingo/app/Api/V1/Controllers/Admin/BaseController.php
dingo/app/Api/V1/Controllers/Admin/routes.php
dingo/app/Api/V1/Controllers/Article
dingo/app/Api/V1/Controllers/Article/routes.php
dingo/app/Api/V1/Controllers/Article/ArticleController.php
dingo/app/Api/V1/Controllers/Auth/routes.php
dingo/app/Api/V1/Controllers/Auth/AuthController.php
dingo/app/Api/V1/Controllers/Order/routes.php
dingo/app/Api/V1/Controllers/Order/OrderController.php
dingo/app/Api/V1/Controllers/Role/routes.php
dingo/app/Api/V1/Controllers/Role/RoleController.php
dingo/app/Api/V1/Controllers/User/routes.php
dingo/app/Api/V1/Controllers/User/UserController.php
dingo/app/Api/V1/Controllers/BaseController.php
dingo/app/Api/V1/Transformers/OrderTransformer.php
dingo/app/Api/V1/Transformers/RegionTransformer.php
dingo/app/Api/V1/Transformers/UserTransformer.php
dingo/app/Api/V1/routes.php
dingo/app/Console/Commands/CrmCommand.php
dingo/app/Console/Commands/CrmUser.php
dingo/app/Console/Commands/MakeImg.php
dingo/app/Console/Kernel.php
dingo/app/Exceptions/Handler.php
dingo/app/Http/Controllers/WxController.php
dingo/app/Http/Middleware/AdminMiddleware.php
dingo/app/Http/Kernel.php
dingo/app/Models/Carticle.php
dingo/app/Models/Cash.php
dingo/app/Models/ChannelInfo.php
dingo/app/Models/ChDiscount.php
dingo/app/Models/Good.php
dingo/app/Models/LaravelSms.php
dingo/app/Models/MemberBank.php
dingo/app/Models/Order.php
dingo/app/Models/OrderCashLog.php
dingo/app/Models/Region.php
dingo/app/Models/Voucher.php
dingo/app/Serializer/CustomSerializer.php
dingo/app/Helper.php
dingo/app/User.php
dingo/config/app.php
dingo/config/auth.php
dingo/config/cors.php
dingo/config/laravel-sms.php
dingo/config/phpsms.php
dingo/config/sentry.php
dingo/composer.json

































#########立即购买
h5/app/cart.app.php
h5/view/sc-utf-8/mall/default/cart/debit/index.html
h5/view/sc-utf-8/mall/default/cart/checkout.html
h5/includes/models/cart.model.php
h5/view/sc-utf-8/mall/default/product/content.html
vendors/Cyteam/Shop/Type/CustomBase.php
includes/models/cart.model.php
h5/diy/js/diy2.js
vendors/Cyteam/Shop/Cart/Carts.php
h5/view/sc-utf-8/mall/default/fdiy/index4.html
h5/view/sc-utf-8/mall/default/fdiy/index5.html
h5/app/cart.app.php
h5/includes/ordertypes/news.otype.php
h5/app/order.app.php
h5/includes/models/cartc.model.php
h5/includes/models/cart.model.php
includes/models/cart.model.php
vendors/Cyteam/Shop/Type/CustomBase.php
vendors/Cyteam/Shop/Type/FdiyBase.php
h5/includes/models/cartc.model.php
includes/models/cartc.model.php

############暂不提交，



CREATE TABLE `cf_cartc` (
  `rec_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `ident` varchar(32) NOT NULL DEFAULT '',
  `goods_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `gift` varchar(50) DEFAULT NULL COMMENT '绑定的赠品ID',
  `price` double(10,2) NOT NULL DEFAULT '0.00',
  `type` char(50) NOT NULL COMMENT '类型',
  `cloth` char(50) NOT NULL COMMENT '品类',
  `dis_ident` varchar(32) DEFAULT '' COMMENT '套装标识',
  `suit_id` varchar(20) DEFAULT '0' COMMENT '套装id',
  `fabric` varchar(20) NOT NULL COMMENT '面料',
  `f_id` int(11) NOT NULL DEFAULT '0' COMMENT '大于0：全球首发 0：非全球首发',
  `lining` varchar(20) NOT NULL COMMENT '里料',
  `button` varchar(200) NOT NULL COMMENT '扣子',
  `style` varchar(200) NOT NULL COMMENT '款式',
  `embs` varchar(200) NOT NULL COMMENT '刺绣',
  `syline` varchar(200) NOT NULL COMMENT '锁眼',
  `params` text NOT NULL COMMENT '参数',
  `items` text NOT NULL COMMENT '其他项',
  `figure` text NOT NULL COMMENT '量体数据',
  `quantity` int(10) UNSIGNED NOT NULL DEFAULT '1' COMMENT '数量',
  `size` varchar(20) NOT NULL DEFAULT 'diy' COMMENT '尺码',
  `first` varchar(20) NOT NULL COMMENT '是否为首推 为何值?',
  `goods_name` varchar(255) NOT NULL DEFAULT '',
  `image` varchar(255) DEFAULT NULL,
  `source_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '来源id',
  `is_diy` tinyint(3) NOT NULL DEFAULT '0' COMMENT '0,不需要定制，1,需要定制',
  `height` smallint(3) NOT NULL DEFAULT '0' COMMENT '身高',
  `weight` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '体重',
  `time` int(11) NOT NULL DEFAULT '0',
  `session_id` varchar(32) DEFAULT NULL,
  `source_from` varchar(36) NOT NULL DEFAULT 'wap' COMMENT '购物车来源',
  `f_cloth` varchar(36) DEFAULT NULL COMMENT '如果是0001 代表的是套装 如果不是0001 则代表单品的品类',
  `is_change` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0没有换面料 1面料已经更换',
  `dog_name` varchar(2250) NOT NULL COMMENT '狗名称',
  `dog_date` varchar(2250) NOT NULL COMMENT '狗生日',
  `dog_desc` text NOT NULL COMMENT '主人寄语',
  `body_condition` int(11) NOT NULL DEFAULT '0' COMMENT '体况id',
  `run_time` int(11) NOT NULL DEFAULT '0' COMMENT '运动量id',
  `time_id` int(11) NOT NULL DEFAULT '0' COMMENT '时间id',
  `dog_nums` int(11) NOT NULL DEFAULT '0' COMMENT '哺乳期狗仔数量',
  `is_try` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0:不试吃 1:试吃',
  `is_check` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0未选择 1已选择'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cf_cartc`
--
ALTER TABLE `cf_cartc`
  ADD PRIMARY KEY (`rec_id`),
  ADD KEY `session_id` (`ident`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `source_id` (`source_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cf_cart`
--
ALTER TABLE `cf_cartc`
  MODIFY `rec_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;