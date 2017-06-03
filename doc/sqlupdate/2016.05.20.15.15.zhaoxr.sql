ALTER TABLE `cf_motif_rel_content`
ADD COLUMN `is_blank`  tinyint(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '是否新窗口打开（1是0否）' AFTER `link_url`;
