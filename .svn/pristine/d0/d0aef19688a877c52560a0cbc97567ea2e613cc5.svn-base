<?php

/**
 * 产品和属性中间表数据模型
 *
 * @author liang.li <1184820705@qq.com>
 * @version $Id: goodsattr.model.php 4291 2015-05-30 02:41:45Z gaofei $
 * @copyright Copyright 2014 mfd.com
 * @package goodsattr.model.php
 */
class GoodsattrModel extends BaseModel
{
    var $table  = 'goods_attr';
    var $prikey = 'id';
    var $alias  = 'goodsattr';
    var $_name  = 'goodsattr';

    var $_relation  = array(
        // 一个商品属性只能属于一个商品
        'belongs_to_goods' => array(
            'model'         => 'goods',
            'type'          => BELONGS_TO,
            'foreign_key'   => 'goods_id',
            'reverse'       => 'has_goodsattr',
        ),
    	
    	'belongs_to_attr' => array(
    			'model'         => 'attribute',
    			'type'          => BELONGS_TO,
    			'foreign_key'   => 'attr_id',
    			'reverse'       => 'has_goodsattr',
    	),
    		
    		'belongs_attribute' => array(
    				'model'         => 'goodsattribute',
    				'type'          => HAS_ONE,
    				'foreign_key'   => 'attr_id',
    				'refer_key'    =>'attr_id',
    		),
    		
    		
    );
}

?>