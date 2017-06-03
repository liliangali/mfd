<?php

class GoodsgroupModel extends BaseModel
{
    var $table  = 'goods_group';
    var $prikey = 'group_id';
    var $_name  = 'gp';

    /* 表单自动验证 */
    var $_autov = array(
        'goods_id'   => array(
            'required'  => true,
        ),
        'item_id' => array(
            'required'  => true,
        ),
    );
    
    /* 关系列表 */
    var $_relation  = array(
    
        'belongs_items' => array(
        				'model'             => 'items',
        				'type'              => BELONGS_TO,
        				'foreign_key'       => 'item_id',
        				'reverse'           => 'has_group',
        ),
    );
}

?>