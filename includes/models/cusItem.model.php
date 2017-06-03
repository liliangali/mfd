<?php
class CusItemModel extends BaseModel
{
	var $table  = 'custom_item';
    var $prikey = 'id';
    var $alias  = 'im';
    var $_name  = 'item';
    var $_prefix = "diy_";
    
    var $_relation = array(
        'belongs_to_dict' => array(
        				'model'             => 'dict1',
        				'type'              => HAS_ONE,
        				'foreign_key'       => 'id',
                        "refer_key"         => "item_id"
        ),
    );
}
