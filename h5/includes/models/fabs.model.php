<?php

class FabsModel extends BaseModel{
    
    var $table  = 'fabric';
    var $prikey = 'ID';
    var $alias  = 'f';
    var $_name  = 'fabric';
    var $_prefix = "diy_";
    var $temp; // 临时变量
    
    var $_relation = array(
        'belongs_to_price' => array(
            'model'             => 'fabprice',
            'type'              => HAS_ONE,
            'foreign_key'       => "FABRICCODE",
            "refer_key"         => "CODE"
        ),
    );
}

?>
