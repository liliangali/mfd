<?php
class CardTicketModel extends BaseModel
{
    var $table  = 'card_ticket';
    var $prikey = 'card_ticket_id';
    var $_name  = 'cardticket';
    
    
    var $_relation  = array(
    		// 一个商品属性只能属于一个商品
    		'belongs_to_ticket' => array(
    				'model'         => 'ticket',
    				'type'          => BELONGS_TO,
    				'foreign_key'   => 'ticket_id',
    				'reverse'       => 'has_cardticket',
    		),
    		 
    
    );
    
}

?>