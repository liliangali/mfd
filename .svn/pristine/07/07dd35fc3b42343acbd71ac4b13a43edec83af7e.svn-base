<?php

define('CHECK_PM_INTEVAL', 600); // 检查新消息的时间间隔（单位：秒）

/* 短消息 message */
class MessageTemplateModel extends BaseModel
{
    var $table  = 'messagetemplate';
    var $prikey = 'mt_id';
    var $_name  = 'messagetemplate';
    var $alias='mt';

    /* 与其它模型之间的关系 */
    var $_relation = array(
    		'belongs_to_icategory' => array(
    				'model'             => 'icategory',
    				'type'              => BELONGS_TO,
    				'foreign_key'       => 'cate_id',
    				'reverse'           => 'has_messages',
    		),
    );
     /* 添加编辑时自动验证 */
//     var $_autov = array(
//     /*    'from_id' => array(
//             'required'  => true,
//             'type'      => 'int',
//             'filter'    => 'trim',
//         ),*/
//         'to_id' => array(
//             'required'  => true,
//             'type'      => 'int',
//             'filter'    => 'trim',
//         ),
//         'content' => array(
//             'required'  => true,
//             'filter'    => 'trim',
//         ),
//     );
    /*
     * 判断分类类型是否唯一
     */
    function unique($type, $mt_id = 0)
    {
    	$conditions = "mt_type = '$type'";
    	$mt_id && $conditions .= " AND mt_id <> '" . $mt_id . "'";
    	return count($this->find(array('conditions' => $conditions))) == 0;
    }
}

?>