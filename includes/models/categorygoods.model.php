<?php
/**
 * Cotte licence
 *
 * @copyright  Copyright (c) 2007-2016 cotte.cn Inc. (http://www.cotte.cn)
 * @license  http://license.cotte.cn/ cotte License
 */
/* 扩展分类model */
class CategorygoodsModel extends BaseModel
{
    var $table  = 'category_goods';
    var $prikey = 'id';
    var $_name  = 'categorygoods';

    var $_relation = array(
    		/* // 一篇文章只能属于一个店铺
    		'belongs_to_user' => array(
    				'model'             => 'member',
    				'type'              => BELONGS_TO,
    				'foreign_key'       => 'user_id',
    				'reverse'           => 'has_apply',
    		), */

    );
}

?>