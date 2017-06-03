ALTER TABLE `cf_detail_comments`
MODIFY COLUMN `comment_id`  int(10) UNSIGNED NOT NULL COMMENT '被评价的商品id,fdiy没有商品id记为0' AFTER `nickname`;