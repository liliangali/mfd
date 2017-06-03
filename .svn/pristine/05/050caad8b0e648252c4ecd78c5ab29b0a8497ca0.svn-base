<?php
class FabricrelattrModel extends BaseModel
{
	var $table  = 'fabric_rel_attr';
    var $prikey = 'id';
    var $alias  = 'fa';
    var $_name  = 'fabric_rel_attr';
    var $_prefix = "cf_";


    var $_relation  = array(


        'has_fabric_info' => array(
            'model' => 'fabricinfo',
            'type' => HAS_ONE,
            'foreign_key' => 'fabric_id',
            'refer_key'       => 'fabric_id',
            'dependent' => true
        ),



    );
}
