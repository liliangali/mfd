<?php

/**
 * 产品组件中间表数据模型
 *
 * @author liang.li <1184820705@qq.com>
 * @version $Id: goodpart.model.php 4291 2015-05-30 02:41:45Z gaofei $
 * @copyright Copyright 2014 mfd.com
 * @package ad.model.php
 */
/**
 * 产品组件中间表数据模型
 * @see GoodsPartModel
 * @version 1.0.0 (2014-11-18)
 * @author liang.li <1184820705@qq.com>
 * @package goodspart.model.php
 */
class GoodsPartModel extends BaseModel
{
    var $table  = 'goods_custom_part';
    var $prikey = 'id';
    var $_name  = 'goodspart';

    var $_relation = array(

    );
}

?>