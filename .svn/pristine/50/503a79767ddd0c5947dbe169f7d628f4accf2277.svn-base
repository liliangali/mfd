<?php

/**
 * 面料和属性中间表数据模型
 *
 * @author liang.li <1184820705@qq.com>
 * @version $Id: fabricattr.model.php 4291 2015-05-30 02:41:45Z gaofei $
 * @copyright Copyright 2014 mfd.com
 * @package fabricattr.model.php
 */
class FabricattrModel extends BaseModel
{
    var $table  = 'fabric_attr';
    var $prikey = 'fabric_attr_id';
    var $alias  = 'fabricattr';
    var $_name  = 'fabricattr';

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
    );
}

?>