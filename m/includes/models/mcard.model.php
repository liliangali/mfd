<?php
class McardModel extends BaseModel
{
    var $table  = 'mcards';
    var $prikey = 'id';
    var $_name  = 'mcard';
    var $_relation = array(
        'has_member' => array(
            'model'       => 'member',       //模型的名称
            'type'        => HAS_ONE,       //关系类型
            'foreign_key' => 'user_id',    //外键名
            'refer_key'   => 'user_id',
            'dependent'   => true           //依赖
        ),
    );
}

?>