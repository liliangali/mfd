<?php

/**
 *礼包券
 * @author liang.li <1184820705@qq.com>
 * @version $Id: ticket.model.php 4291 2015-05-30 02:41:45Z gaofei $
 * @copyright Copyright 2014 mfd.com
 * @package ticket.model.php
 */
class TicketModel extends BaseModel
{
    var $table  = 'ticket';
    var $prikey = 'id';
    var $_name  = 'ticket';
    var $_obj_search='name|名称';
    var $_obj_fields='id|ID,name|名称,type_name|分类名称';
    
    
    /* 表单自动验证 */
    var $_autov = array(
        
    );

    /* 关系列表 */
    var $_relation  = array(
    		
    		'has_cardticket' => array(
    				'model'         => 'cardticket',
    				'type'          => HAS_MANY,
    				'foreign_key'   => 'ticket_id',
    				'dependent'     => true
    		),
    );
    
    /**
     * 判断名称是否唯一
     * @param string $addName
     * @param int $addId
     * @return boolean
     */
    function unique($type_name, $type_id = 0)
    {
    	$conditions = "type_name = '" . $type_name . "' AND type_id != ".$type_id."";
    	return count($this->find(array('conditions' => $conditions))) == 0;
    }
   
    
    
    
    
    
}

?>