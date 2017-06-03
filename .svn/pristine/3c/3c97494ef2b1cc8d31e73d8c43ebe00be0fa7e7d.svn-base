--
-- 表的结构 `cf_fbcategory`
--

DROP TABLE IF EXISTS `cf_fbcategory`;
CREATE TABLE IF NOT EXISTS `cf_fbcategory` (
  `cate_id` int(10) unsigned NOT NULL,
  `cate_name` varchar(100) NOT NULL DEFAULT '' COMMENT '分类名称',
  `parent_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '分类父id',
  `sort_order` tinyint(3) unsigned NOT NULL DEFAULT '255' COMMENT '排序',
  `uprice` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '单价',
  `fprice` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '固定价',
  `is_def` tinyint(1) NOT NULL DEFAULT '0' COMMENT '默认',
  `if_show` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否显示',
  `source_img` varchar(255) DEFAULT NULL COMMENT '大图',
  `small_img` varchar(200) NOT NULL COMMENT '缩略图',
  `ve` varchar(50) NOT NULL COMMENT '属性值',
  `content` text COMMENT '品牌描述'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='定制商品属性树形表';

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cf_fbcategory`
--
ALTER TABLE `cf_fbcategory`
  ADD PRIMARY KEY (`cate_id`),
  ADD KEY `store_id` (`parent_id`) USING BTREE,
  ADD KEY `parent_id` (`parent_id`,`is_def`,`if_show`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cf_fbcategory`
--
ALTER TABLE `cf_fbcategory`
  MODIFY `cate_id` int(10) unsigned NOT NULL AUTO_INCREMENT;












--
-- 表的结构 `cf_fdiy_management`
--

DROP TABLE IF EXISTS `cf_fdiy_management`;
CREATE TABLE IF NOT EXISTS `cf_fdiy_management` (
  `cate_id` int(10) unsigned NOT NULL,
  `cate_name` varchar(100) NOT NULL DEFAULT '',
  `parent_id` int(10) unsigned NOT NULL DEFAULT '0',
  `sort_order` tinyint(3) unsigned NOT NULL DEFAULT '255',
  `code` varchar(10) DEFAULT NULL,
  `did` varchar(200) NOT NULL COMMENT '属性id',
  `small_img` varchar(200) NOT NULL,
  `if_show` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='通告文章分类表';

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cf_fdiy_management`
--
ALTER TABLE `cf_fdiy_management`
  ADD PRIMARY KEY (`cate_id`),
  ADD KEY `did` (`did`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cf_fdiy_management`
--
ALTER TABLE `cf_fdiy_management`
  MODIFY `cate_id` int(10) unsigned NOT NULL AUTO_INCREMENT;
