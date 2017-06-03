<?php
class FabricinfoModel extends BaseModel
{
	var $table  = 'fabric_info';
    var $prikey = 'fabric_id';
    var $alias  = 'fai';
    var $_name  = 'fabric_info';
    var $_prefix = "cf_";






    var $_relation  = array(

        // 一个返修记录
        'has_fabric_price' => array(
            'model' => 'fabricpricecf',
            'type' => HAS_ONE,
            'foreign_key' => 'fabric_id',
            'refer_key'       => 'fabric_id',
            'dependent' => true
        ),
        // 一个返修记录
        'has_fabricrelattr' => array(
            'model' => 'fabricrelattr',
            'type' => HAS_ONE,
            'foreign_key' => 'fabric_id',
            'refer_key'       => 'fabric_id',
            'dependent' => true
        ),



	);

}
