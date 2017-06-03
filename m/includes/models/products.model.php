<?php
/**
 * 货品数据模型
 *
 * @author Ruesin <ruesin@163.com>
 * @version $Id: products.model.php 4291 2015-05-30 02:41:45Z gaofei $
 * @copyright Copyright 2014 mfd.com
 * @package products.model.php
 */
/**
 * 货品数据模型
 * @see ProductsModel
 * @version 1.0.0 (2014-11-18)
 * @author Ruesin <ruesin@163.com>
 * @package products.model.php
 */
class ProductsModel extends BaseModel
{
    var $table  = 'products';
    var $prikey = 'product_id';
    var $alias  = 'product';
    var $_name  = 'products';
    var $temp; // 临时变量
    var $_relation = array();
    var $_autov = array();


}
