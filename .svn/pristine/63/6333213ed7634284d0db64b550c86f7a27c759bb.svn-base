<?php
class GoodstypespecModel extends BaseModel
{
    var $table  = 'goods_type_spec';
    var $prikey = 'id';
    var $_name  = 'goodstypespec';
    var $_prefix = "cf_";
    
   
    
    var $_relation  = array(
    		'has_specification' => array(
    				'model'         => 'specification',
    				'type'          => HAS_ONE,
    				'foreign_key'   => 'spec_id',
    				'refer_key'     => 'spec_id',
    				'dependent'     => true
    		),
    );
}


?>