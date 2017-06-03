<?php

/**
 *礼包券和商品的中间表
 * @author liang.li <1184820705@qq.com>
 * @version $Id: ticketgoods.model.php 4291 2015-05-30 02:41:45Z gaofei $
 * @copyright Copyright 2014 mfd.com
 * @package ticketgoods.model.php
 */
class TicketGoodsModel extends BaseModel
{
    var $table  = 'ticket_goods';
    var $prikey = 'id';
    var $_name  = 'ticketgoods';
    
    
    /* 表单自动验证 */
    var $_autov = array(
        
    );

    /* 关系列表 */
    var $_relation  = array(
    		'belongs_to_goods' => array(
    				'model'         => 'goods',
    				'type'          => BELONGS_TO,
    				'foreign_key'   => 'goods_id',
    				'reverse'       => 'has_goodsticket',
    		),
    );
    
   
    
    
    
    
    
}

?>