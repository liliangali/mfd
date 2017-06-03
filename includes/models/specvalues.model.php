<?php
/**
 * Cotte licence
 *
 * @copyright  Copyright (c) 2007-2016 cotte.cn Inc. (http://www.cotte.cn)
 * @license  http://license.cotte.cn/ cotte License
 *  ---------------------------------------------------------------------
 * @author shaozz<shaozizhen94@163.com>
 * 2016-5-20
 */

/* 商品规格 goodsspec */
class SpecvaluesModel extends BaseModel
{
    var $table  = 'spec_values';
    var $prikey = 'spec_value_id';
    var $_name  = 'specvalues';
    var $_prefix = "cf_";
    
    
    var $_relation  = array(
        // 一个商品规格只能属于一个商品
        'belongs_to_goods' => array(
            'model'         => 'specification',
            'type'          => BELONGS_TO,
            'foreign_key'   => 'spec_id',
            'reverse'       => 'has_goodsspec',
        ),
    );
}

?>