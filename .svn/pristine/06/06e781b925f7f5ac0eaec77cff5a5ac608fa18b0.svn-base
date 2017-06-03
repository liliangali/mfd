<?php

class FabsModel extends BaseModel{
    
    var $table  = 'fabric';
    var $prikey = 'ID';
    var $alias  = 'f';
    var $_name  = 'fabric';
    var $_prefix = "diy_";
    var $_typeid = array('8001'=>'西服、西裤、马夹','8030'=>'衬衣','8050'=>'大衣');
    var $temp; // 临时变量
    
    var $_relation = array(
        'belongs_to_price' => array(
            'model'             => 'fabricprice',
            'type'              => HAS_ONE,
            'foreign_key'       => "FABRICCODE",
            "refer_key"         => "CODE"
        ),
    	'has_gallery_id'=>array(
    			'model'=>'fabricgallery',
    			'type'=>HAS_ONE,
    			'foreign_key'=>"fabric_id",
    			'refer_key'=>"ID",
    	),
    	'has_gallery_code'=>array(
    			'model'=>'fabricgallery',
    			'type'=>HAS_ONE,
    			'foreign_key'=>'CODE',
    			'refer_key'=>'CODE',
    	),
    );
}

?>
