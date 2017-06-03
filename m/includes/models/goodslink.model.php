<?php

/**
 * 产品关联表模型
 *
 * @author liang.li <1184820705@qq.com>
 * @version $Id: goodslink.model.php 4291 2015-05-30 02:41:45Z gaofei $
 * @copyright Copyright 2014 mfd.com
 * @package goodslink.model.php
 */
class GoodsLinkModel extends BaseModel
{
    var $table  = 'goods_link';
    var $prikey = 'id';
    var $_name  = 'goodslink';

    /* 关系列表 */
    var $_relation  = array(
    		// 一个中间表数据能属于一个商品
    		'belongs_to_goods' => array(
    				'model'         => 'goods',
    				'type'          => BELONGS_TO,
    				'foreign_key'   => 'link_goods_id',
    				'reverse'       => 'has_goods_link',
    		),
    );
    
    
    
    
}

?>