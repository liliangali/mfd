<?php

/* 可定制项数据模型 */
class ItemsModel extends BaseModel
{
    var $table  = 'items';
    var $prikey = 'id';
    var $alias  = 'items';
    var $_name  = 'items';
    var $temp; // 临时变量
    
    /* 关系列表 */
    var $_relation  = array(
    
        'has_group' => array(
            'model'             => 'items',
            'type'              => BELONGS_TO,
            'foreign_key' => 'item_id',
        ),
    );
}

?>