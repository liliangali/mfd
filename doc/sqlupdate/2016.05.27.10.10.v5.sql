

--
-- 表的结构 `cf_delivery`
--

DROP TABLE IF EXISTS `cf_delivery`;
CREATE TABLE IF NOT EXISTS `cf_delivery` (
  `delivery_id` bigint(20) unsigned NOT NULL COMMENT '配送流水号',
  `tid` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '订单号',
  `user_id` int(10) unsigned DEFAULT NULL COMMENT '订货会员ID',
  `post_fee` decimal(20,3) NOT NULL DEFAULT '0.000' COMMENT '配送费用',
  `is_protect` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否保价',
  `dlytmpl_id` int(10) unsigned DEFAULT NULL COMMENT '配送方式(货到付款、EMS...)',
  `logi_id` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '物流公司ID',
  `logi_name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '物流公司名称',
  `corp_code` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '物流公司代码',
  `logi_no` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '物流单号',
  `area_ids` varchar(50) COLLATE utf8_unicode_ci NOT NULL COMMENT '地区id字符串',
  `area_names` varchar(100) COLLATE utf8_unicode_ci NOT NULL COMMENT '地区名称字符串',
  `receiver_name` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '收货人姓名',
  `receiver_state` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '收货人所在省',
  `receiver_city` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '收货人所在市',
  `receiver_district` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '收货人所在地区',
  `receiver_address` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '收货人详细地址',
  `receiver_zip` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '收货人邮编',
  `receiver_mobile` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '收货人手机号',
  `receiver_phone` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '收货人电话',
  `t_begin` int(10) unsigned DEFAULT NULL COMMENT '单据生成时间',
  `t_send` int(10) unsigned DEFAULT NULL COMMENT '单据结束时间',
  `t_confirm` int(10) unsigned DEFAULT NULL COMMENT '单据确认时间',
  `op_name` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '操作者',
  `status` varchar(8) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'ready' COMMENT '状态',
  `memo` longtext COLLATE utf8_unicode_ci COMMENT '备注',
  `disabled` tinyint(1) DEFAULT '0' COMMENT '无效'
) ENGINE=InnoDB AUTO_INCREMENT=1201601302103400001 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- 转存表中的数据 `cf_delivery`
--

INSERT INTO `cf_delivery` (`delivery_id`, `tid`, `user_id`, `post_fee`, `is_protect`, `dlytmpl_id`, `logi_id`, `logi_name`, `corp_code`, `logi_no`, `area_ids`, `area_names`, `receiver_name`, `receiver_state`, `receiver_city`, `receiver_district`, `receiver_address`, `receiver_zip`, `receiver_mobile`, `receiver_phone`, `t_begin`, `t_send`, `t_confirm`, `op_name`, `status`, `memo`, `disabled`) VALUES
(120160126215730000, '1601261603150002', 2, '10.500', 0, 5, '4', '韵达快递', 'YD', '4545465654545', '', '', 'buyer01', '上海市', NULL, NULL, '桂林路396号', NULL, '13600980921', NULL, 1453797054, 1453797054, 1453797054, NULL, 'succ', NULL, 0),
(120160126302740000, '1601261617500002', 2, '0.000', 0, 0, '1', '申通快递', 'STO', '162523299268562', '', '', 'buyer', '北京市', NULL, NULL, '万寿路长宁苑56号', NULL, '15800893873', NULL, 1453796301, 1453796301, 1453796301, NULL, 'succ', NULL, 0),
(120160126450540000, '1601261420370002', 2, '18.000', 0, 2, '2', '顺丰速递', 'SF', '965226445298268', '', '', 'buyer01', '上海市', NULL, NULL, '桂林路396号', NULL, '13600980921', NULL, 1453789363, 1453789363, 1453789363, NULL, 'succ', NULL, 0),
(120160126466340000, '1601261605080002', 2, '0.000', 0, 3, '3', '圆通快递', 'YTO', '153564264569898', '', '', 'buyer01', '上海市', NULL, NULL, '桂林路396号', NULL, '13600980921', NULL, 1453797044, 1453797044, 1453797044, NULL, 'succ', NULL, 0),
(120160126595940000, '1601261433090002', 2, '12.000', 0, 8, '2', '顺丰速递', 'SF', '12941551546541020356', '', '', 'buyer01', '上海市', NULL, NULL, '桂林路396号', NULL, '13600980921', NULL, 1453790152, 1453790152, 1453790152, NULL, 'succ', NULL, 0),
(120160130165260000, '1601282004100001', 1, '0.000', 0, 7, '1', '申通快递', 'STO', '863463', '', '', 'test01', '上海市', NULL, NULL, '桂林路', NULL, '13687299999', NULL, 1454150739, 1454150739, 1454150739, NULL, 'succ', NULL, 0),
(120160130306840000, '1601281958080001', 1, '0.000', 0, 7, '1', '申通快递', 'STO', '2352525', '', '', 'test01', '上海市', NULL, NULL, '桂林路', NULL, '13687299999', NULL, 1454150774, 1454150774, 1454150774, NULL, 'succ', NULL, 0),
(120160130435720000, '1601301844190001', 1, '0.000', 0, 7, '1', '申通快递', 'STO', '1523525', '', '', 'test01', '上海市', NULL, NULL, '桂林路', NULL, '13687299999', NULL, 1454150714, 1454150714, 1454150714, NULL, 'succ', NULL, 0),
(120160130497880000, '1601261607320002', 2, '0.000', 0, 0, '1', '申通快递', 'STO', '2351253125', '', '', 'buyer01', '上海市', NULL, NULL, '桂林路396号', NULL, '13600980921', NULL, 1454150811, 1454150811, 1454150811, NULL, 'succ', NULL, 0),
(120160130596500000, '1601282002290001', 1, '0.000', 0, 7, '1', '申通快递', 'STO', '8763425', '', '', 'test01', '上海市', NULL, NULL, '桂林路', NULL, '13687299999', NULL, 1454150749, 1454150749, 1454150749, NULL, 'succ', NULL, 0),
(120160130748740000, '1601281959360001', 1, '0.000', 0, 7, '1', '申通快递', 'STO', '12623424', '', '', 'test01', '上海市', NULL, NULL, '桂林路', NULL, '13687299999', NULL, 1454150759, 1454150759, 1454150759, NULL, 'succ', NULL, 0),
(120160130922320000, '1601282003130001', 1, '33.000', 0, 8, '2', '顺丰速递', 'SF', '1167888', '', '', 'test01', '上海市', NULL, NULL, '桂林路', NULL, '13687299999', NULL, 1454150744, 1454150744, 1454150744, NULL, 'succ', NULL, 0),
(1201601261070400000, '1601261441460002', 2, '12.000', 0, 8, '2', '顺丰速递', 'SF', '54966265359026535', '', '', 'buyer01', '上海市', NULL, NULL, '桂林路396号', NULL, '13600980921', NULL, 1453790537, 1453790537, 1453790537, NULL, 'succ', NULL, 0),
(1201601261786800000, '1601261432140002', 2, '0.000', 0, 7, '1', '申通快递', 'STO', '541892041159328', '', '', 'buyer01', '上海市', NULL, NULL, '桂林路396号', NULL, '13600980921', NULL, 1453790167, 1453790167, 1453790167, NULL, 'succ', NULL, 0),
(1201601261871700000, '1601261431290002', 2, '0.000', 0, 7, '1', '申通快递', 'STO', '1546503656234', '', '', 'buyer', '北京市', NULL, NULL, '万寿路长宁苑56号', NULL, '15800893873', NULL, 1453796349, 1453796349, 1453796349, NULL, 'succ', NULL, 0),
(1201601262016900000, '1601261614320002', 2, '0.000', 0, 7, '1', '申通快递', 'STO', '1146662356336622', '', '', 'buyer', '北京市', NULL, NULL, '万寿路长宁苑56号', NULL, '15800893873', NULL, 1453796328, 1453796328, 1453796328, NULL, 'succ', NULL, 0),
(1201601301374300000, '1601281959270001', 1, '0.000', 0, 7, '1', '申通快递', 'STO', '98765423', '', '', 'test01', '上海市', NULL, NULL, '桂林路', NULL, '13687299999', NULL, 1454150754, 1454150754, 1454150754, NULL, 'succ', NULL, 0),
(1201601301553800000, '1601291845470001', 1, '0.000', 0, 7, '1', '申通快递', 'STO', '563452', '', '', 'test01', '上海市', NULL, NULL, '桂林路', NULL, '13687299999', NULL, 1454150722, 1454150722, 1454150722, NULL, 'succ', NULL, 0),
(1201601301681300000, '1601281957010001', 1, '0.000', 0, 7, '1', '申通快递', 'STO', '34734723', '', '', 'test01', '上海市', NULL, NULL, '桂林路', NULL, '13687299999', NULL, 1454150783, 1454150783, 1454150783, NULL, 'succ', NULL, 0),
(1201601301759700000, '1601281958090001', 1, '0.000', 0, 7, '1', '申通快递', 'STO', '8525333', '', '', 'test01', '上海市', NULL, NULL, '桂林路', NULL, '13687299999', NULL, 1454150767, 1454150767, 1454150767, NULL, 'succ', NULL, 0),
(1201601301787300000, '1601291844420001', 1, '0.000', 0, 7, '1', '申通快递', 'STO', '214141414', '', '', 'test01', '上海市', NULL, NULL, '桂林路', NULL, '13687299999', NULL, 1454150730, 1454150730, 1454150730, NULL, 'succ', NULL, 0),
(1201601301873900000, '1601281135410002', 2, '0.000', 0, 7, '1', '申通快递', 'STO', '125125', '', '', 'buyer', '北京市', NULL, NULL, '万寿路长宁苑56号', NULL, '15800893873', NULL, 1454150787, 1454150787, 1454150787, NULL, 'succ', NULL, 0),
(1201601301936900000, '1601301844130001', 1, '0.000', 0, 7, '1', '申通快递', 'STO', '12414141', '', '', 'test01', '上海市', NULL, NULL, '桂林路', NULL, '13687299999', NULL, 1454150717, 1454150717, 1454150717, NULL, 'succ', NULL, 0),
(1201601301947400000, '1601291845420001', 1, '0.000', 0, 7, '1', '申通快递', 'STO', '437543734', '', '', 'test01', '上海市', NULL, NULL, '桂林路', NULL, '13687299999', NULL, 1454150726, 1454150726, 1454150726, NULL, 'succ', NULL, 0),
(1201601301982900000, '1601291844360001', 1, '0.000', 0, 7, '1', '申通快递', 'STO', '12312311', '', '', 'test01', '上海市', NULL, NULL, '桂林路', NULL, '13687299999', NULL, 1454150735, 1454150735, 1454150735, NULL, 'succ', NULL, 0),
(1201601302103400000, '1601301848190001', 1, '18.000', 0, 8, '2', '顺丰速递', 'SF', '6353251', '', '', 'test01', '上海市', NULL, NULL, '桂林路', NULL, '13687299999', NULL, 1454150963, 1454150963, 1454150963, NULL, 'succ', NULL, 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cf_delivery`
--
ALTER TABLE `cf_delivery`
  ADD PRIMARY KEY (`delivery_id`),
  ADD KEY `ind_disabled` (`disabled`),
  ADD KEY `ind_logi_no` (`logi_no`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cf_delivery`
--
ALTER TABLE `cf_delivery`
  MODIFY `delivery_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '配送流水号',AUTO_INCREMENT=1201601302103400001;